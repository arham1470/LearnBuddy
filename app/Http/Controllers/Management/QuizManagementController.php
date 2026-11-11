<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizQuestionRequest;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class QuizManagementController extends Controller
{
    /**
     * Display a listing of course quizzes for instructors/admins.
     */
    public function index(Course $course): View
    {
        Gate::authorize('create', [Quiz::class, $course]);

        $quizzes = $course->quizzes()
            ->withCount(['questions', 'attempts'])
            ->with(['section', 'lesson'])
            ->latest()
            ->get();

        return view('management.quizzes.index', compact('course', 'quizzes'));
    }

    /**
     * Show form to create a new quiz.
     */
    public function create(Course $course): View
    {
        Gate::authorize('create', [Quiz::class, $course]);

        $course->load(['sections.lessons']);

        return view('management.quizzes.create', compact('course'));
    }

    /**
     * Store a newly created quiz.
     */
    public function store(StoreQuizRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('create', [Quiz::class, $course]);

        $data = $request->validated();
        $data['course_id'] = $course->id;
        $data['created_by_user_id'] = auth()->id();
        $data['is_published'] = $request->boolean('is_published', true);
        $data['show_correct_answers_after_attempt'] = $request->boolean('show_correct_answers_after_attempt', true);

        $quiz = Quiz::create($data);

        return redirect()->route('management.courses.quizzes.questions', [$course, $quiz])
            ->with('status', 'Quiz created! Now add your questions below.');
    }

    /**
     * Show form to edit an existing quiz.
     */
    public function edit(Course $course, Quiz $quiz): View
    {
        if ((int) $quiz->course_id !== (int) $course->id) {
            abort(404, 'Quiz not found for this course.');
        }

        Gate::authorize('update', $quiz);

        $course->load(['sections.lessons']);

        return view('management.quizzes.edit', compact('course', 'quiz'));
    }

    /**
     * Update an existing quiz.
     */
    public function update(UpdateQuizRequest $request, Course $course, Quiz $quiz): RedirectResponse
    {
        if ((int) $quiz->course_id !== (int) $course->id) {
            abort(404, 'Quiz not found for this course.');
        }

        Gate::authorize('update', $quiz);

        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published', true);
        $data['show_correct_answers_after_attempt'] = $request->boolean('show_correct_answers_after_attempt', true);

        $quiz->update($data);

        return redirect()->route('management.courses.quizzes.index', $course)
            ->with('status', 'Quiz updated successfully.');
    }

    /**
     * Delete a quiz.
     */
    public function destroy(Course $course, Quiz $quiz): RedirectResponse
    {
        if ((int) $quiz->course_id !== (int) $course->id) {
            abort(404, 'Quiz not found for this course.');
        }

        Gate::authorize('delete', $quiz);

        $quiz->delete();

        return redirect()->route('management.courses.quizzes.index', $course)
            ->with('status', 'Quiz deleted successfully.');
    }

    /**
     * Manage questions and options for a quiz.
     */
    public function questions(Course $course, Quiz $quiz): View
    {
        if ((int) $quiz->course_id !== (int) $course->id) {
            abort(404, 'Quiz not found for this course.');
        }

        Gate::authorize('update', $quiz);

        $questions = $quiz->questions()->with('options')->get();

        return view('management.quizzes.questions', compact('course', 'quiz', 'questions'));
    }

    /**
     * Store a new question with its options for a quiz.
     */
    public function storeQuestion(StoreQuizQuestionRequest $request, Course $course, Quiz $quiz): RedirectResponse
    {
        if ((int) $quiz->course_id !== (int) $course->id) {
            abort(404, 'Quiz not found for this course.');
        }

        Gate::authorize('update', $quiz);

        DB::transaction(function () use ($request, $quiz) {
            $question = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_text' => $request->input('question_text'),
                'question_type' => $request->input('question_type'),
                'marks' => $request->input('marks'),
                'explanation' => $request->input('explanation'),
                'sort_order' => $request->input('sort_order', $quiz->questions()->count() + 1),
            ]);

            $options = $request->input('options', []);
            $correctIndex = (int) $request->input('correct_option_index');

            foreach ($options as $index => $optionData) {
                if (empty(trim($optionData['text'] ?? ''))) {
                    continue;
                }

                QuizQuestionOption::create([
                    'quiz_question_id' => $question->id,
                    'option_text' => trim($optionData['text']),
                    'is_correct' => ($index === $correctIndex),
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return back()->with('status', 'Question added successfully.');
    }

    /**
     * Delete a question from a quiz.
     */
    public function destroyQuestion(Course $course, Quiz $quiz, QuizQuestion $question): RedirectResponse
    {
        if ((int) $quiz->course_id !== (int) $course->id || (int) $question->quiz_id !== (int) $quiz->id) {
            abort(404, 'Question not found for this quiz.');
        }

        Gate::authorize('update', $quiz);

        $question->delete();

        return back()->with('status', 'Question deleted successfully.');
    }

    /**
     * View all student attempts for a quiz.
     */
    public function attempts(Course $course, Quiz $quiz): View
    {
        if ((int) $quiz->course_id !== (int) $course->id) {
            abort(404, 'Quiz not found for this course.');
        }

        Gate::authorize('update', $quiz);

        $attempts = $quiz->attempts()
            ->with('user')
            ->latest('started_at')
            ->get();

        return view('management.quizzes.attempts', compact('course', 'quiz', 'attempts'));
    }
}
