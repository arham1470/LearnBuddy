<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Notifications\QuizResultNotification;
use App\Services\Notifications\NotificationDispatcherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Display quiz overview, rules and past attempts.
     */
    public function show(Course $course, Quiz $quiz): View
    {
        if ((int) $quiz->course_id !== (int) $course->id) {
            abort(404, 'Quiz not found for this course.');
        }

        Gate::authorize('view', $quiz);

        $user = auth()->user();
        $attempts = $quiz->attempts()
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $activeAttempt = $attempts->firstWhere('status', QuizAttempt::STATUS_IN_PROGRESS);
        $bestAttempt = $quiz->getUserBestAttempt($user);
        $canAttempt = $quiz->canUserAttempt($user);

        return view('quizzes.show', compact('course', 'quiz', 'attempts', 'activeAttempt', 'bestAttempt', 'canAttempt'));
    }

    /**
     * Start a new quiz attempt or resume existing in-progress attempt.
     */
    public function start(Course $course, Quiz $quiz): RedirectResponse
    {
        if ((int) $quiz->course_id !== (int) $course->id) {
            abort(404, 'Quiz not found for this course.');
        }

        $user = auth()->user();

        // Resume existing in-progress attempt if found
        $existing = $quiz->attempts()
            ->where('user_id', $user->id)
            ->where('status', QuizAttempt::STATUS_IN_PROGRESS)
            ->first();

        if ($existing) {
            return redirect()->route('courses.quizzes.take', [$course, $quiz, $existing]);
        }

        Gate::authorize('attempt', $quiz);

        $attemptNumber = $quiz->userAttemptsCount($user) + 1;

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'attempt_number' => $attemptNumber,
            'status' => QuizAttempt::STATUS_IN_PROGRESS,
            'total_marks' => $quiz->totalMarks(),
            'started_at' => now(),
        ]);

        return redirect()->route('courses.quizzes.take', [$course, $quiz, $attempt]);
    }

    /**
     * Show the quiz question form to the student.
     * CRITICAL SECURITY: Does not expose is_correct in options!
     */
    public function take(Course $course, Quiz $quiz, QuizAttempt $attempt): View|RedirectResponse
    {
        if ((int) $quiz->course_id !== (int) $course->id || (int) $attempt->quiz_id !== (int) $quiz->id) {
            abort(404, 'Quiz attempt not found.');
        }

        // IDOR protection: only the owner student can take this attempt
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized attempt access.');
        }

        if ($attempt->isCompleted()) {
            return redirect()->route('courses.quizzes.result', [$course, $quiz, $attempt]);
        }

        // Query questions and options WITHOUT selecting is_correct to prevent leaking answers
        $questions = $quiz->questions()
            ->with(['options' => function ($query) {
                $query->select(['id', 'quiz_question_id', 'option_text', 'sort_order'])
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return view('quizzes.take', compact('course', 'quiz', 'attempt', 'questions'));
    }

    /**
     * Securely submit and grade the quiz server-side.
     */
    public function submit(Request $request, Course $course, Quiz $quiz, QuizAttempt $attempt): RedirectResponse
    {
        if ((int) $quiz->course_id !== (int) $course->id || (int) $attempt->quiz_id !== (int) $quiz->id) {
            abort(404, 'Quiz attempt not found.');
        }

        // IDOR protection
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized attempt access.');
        }

        // Prevent repeated submissions
        if ($attempt->isCompleted()) {
            return redirect()->route('courses.quizzes.result', [$course, $quiz, $attempt]);
        }

        $submittedAnswers = $request->input('answers', []);
        $questions = $quiz->questions()->with('options')->get();

        DB::transaction(function () use ($attempt, $quiz, $questions, $submittedAnswers) {
            $totalScore = 0;

            foreach ($questions as $question) {
                $selectedOptionId = isset($submittedAnswers[$question->id]) ? (int) $submittedAnswers[$question->id] : null;

                $selectedOption = $question->options->firstWhere('id', $selectedOptionId);
                $isCorrect = $selectedOption ? (bool) $selectedOption->is_correct : false;
                $marksAwarded = $isCorrect ? (float) $question->marks : 0.00;

                $totalScore += $marksAwarded;

                QuizAttemptAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $attempt->id,
                        'quiz_question_id' => $question->id,
                    ],
                    [
                        'selected_option_id' => $selectedOptionId,
                        'is_correct' => $isCorrect,
                        'marks_awarded' => $marksAwarded,
                    ]
                );
            }

            $totalMarks = (int) $questions->sum('marks');
            $percentage = $totalMarks > 0 ? round(($totalScore / $totalMarks) * 100, 2) : 0.00;
            $isPassed = ($percentage >= $quiz->passing_percentage);

            $attempt->update([
                'status' => QuizAttempt::STATUS_COMPLETED,
                'score' => $totalScore,
                'total_marks' => $totalMarks,
                'percentage' => $percentage,
                'is_passed' => $isPassed,
                'completed_at' => now(),
            ]);
        });

        $attempt->refresh();
        app(NotificationDispatcherService::class)->send(
            user: auth()->user(),
            notification: new QuizResultNotification($quiz, $attempt),
            deduplicationKey: "quiz_result_{$attempt->id}_{$attempt->score}",
            eventType: 'quiz_result'
        );

        return redirect()->route('courses.quizzes.result', [$course, $quiz, $attempt])
            ->with('status', 'Quiz completed successfully!');
    }

    /**
     * Show quiz attempt result and review.
     */
    public function result(Course $course, Quiz $quiz, QuizAttempt $attempt): View
    {
        if ((int) $quiz->course_id !== (int) $course->id || (int) $attempt->quiz_id !== (int) $quiz->id) {
            abort(404, 'Quiz attempt not found.');
        }

        Gate::authorize('viewAttempt', $attempt);

        $attempt->load(['answers.question.options', 'answers.selectedOption']);

        $user = auth()->user();
        $isStaff = $user->isAdmin() || $course->hasInstructor($user);
        $showAnswers = $isStaff || $quiz->show_correct_answers_after_attempt;

        return view('quizzes.result', compact('course', 'quiz', 'attempt', 'showAnswers'));
    }
}
