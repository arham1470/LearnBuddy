<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\PhysicalLocation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of courses manageable by the authenticated user.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Course::class);

        $user = $request->user();

        $query = Course::forUser($user)
            ->with(['category', 'instructors', 'deliveryOptions'])
            ->latest();

        // Search filter
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Category filter
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Delivery mode filter
        if ($mode = $request->input('delivery_mode')) {
            $query->whereHas('deliveryOptions', function ($q) use ($mode) {
                $q->where('delivery_mode', $mode);
            });
        }

        $courses = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('management.courses.index', compact('courses', 'categories'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        Gate::authorize('create', Course::class);

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $instructors = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'instructor']))->orderBy('name')->get();
        $locations = PhysicalLocation::where('is_active', true)->orderBy('city')->get();

        return view('management.courses.create', compact('categories', 'instructors', 'locations'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $user = $request->user();

        return DB::transaction(function () use ($request, $user) {
            $thumbnailUrl = null;

            if ($request->hasFile('thumbnail')) {
                $thumbnailUrl = $this->handleThumbnailUpload($request->file('thumbnail'), $request->title);
            }

            // Clean learning outcomes & prerequisites
            $learningOutcomes = array_values(array_filter($request->input('learning_outcomes', [])));
            $prerequisites = array_values(array_filter($request->input('prerequisites', [])));

            $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);
            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (Course::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$counter}";
                $counter++;
            }

            $course = Course::create([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'slug' => $slug,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'thumbnail_url' => $thumbnailUrl,
                'preview_video_url' => $request->preview_video_url,
                'difficulty_level' => $request->difficulty_level,
                'language' => $request->language,
                'duration_hours' => $request->duration_hours,
                'learning_outcomes' => $learningOutcomes,
                'prerequisites' => $prerequisites,
                'status' => Course::STATUS_DRAFT,
                'created_by_user_id' => $user->id,
            ]);

            // Assign Instructors
            $this->syncInstructors($course, $user, $request->input('instructor_ids', []));

            // Setup Delivery Options
            $this->syncDeliveryOptions($course, $request);

            return redirect()->route('management.courses.show', $course)
                ->with('status', 'Course draft created successfully! You can now organize curriculum modules or submit for review.');
        });
    }

    /**
     * Display the specified course management overview.
     */
    public function show(Course $course): View
    {
        Gate::authorize('view', $course);

        $course->load([
            'category',
            'instructors',
            'deliveryOptions.batches.location',
            'sections.lessons',
            'creator',
        ]);

        return view('management.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): View
    {
        Gate::authorize('update', $course);

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $instructors = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'instructor']))->orderBy('name')->get();
        $locations = PhysicalLocation::where('is_active', true)->orderBy('city')->get();

        $course->load(['deliveryOptions.batches', 'instructors']);

        return view('management.courses.edit', compact('course', 'categories', 'instructors', 'locations'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        return DB::transaction(function () use ($request, $course) {
            $thumbnailUrl = $course->thumbnail_url;

            if ($request->hasFile('thumbnail')) {
                // Delete previous upload if present on disk
                if ($course->thumbnail_url && Str::startsWith($course->thumbnail_url, '/storage/')) {
                    $oldPath = Str::replaceFirst('/storage/', '', $course->thumbnail_url);
                    Storage::disk('public')->delete($oldPath);
                    @unlink(public_path('storage/'.$oldPath));
                }

                $thumbnailUrl = $this->handleThumbnailUpload($request->file('thumbnail'), $request->title);
            }

            $learningOutcomes = array_values(array_filter($request->input('learning_outcomes', [])));
            $prerequisites = array_values(array_filter($request->input('prerequisites', [])));

            $slug = $request->slug ? Str::slug($request->slug) : $course->slug;

            $course->update([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'slug' => $slug,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'thumbnail_url' => $thumbnailUrl,
                'preview_video_url' => $request->preview_video_url,
                'difficulty_level' => $request->difficulty_level,
                'language' => $request->language,
                'duration_hours' => $request->duration_hours,
                'learning_outcomes' => $learningOutcomes,
                'prerequisites' => $prerequisites,
            ]);

            // Sync instructors if user is admin
            if ($request->user()->isAdmin()) {
                $this->syncInstructors($course, $request->user(), $request->input('instructor_ids', []));
            }

            // Sync delivery options
            $this->syncDeliveryOptions($course, $request);

            return redirect()->route('management.courses.show', $course)
                ->with('status', 'Course updated successfully!');
        });
    }

    /**
     * Transition publication status (Draft -> Review -> Published -> Archived).
     */
    public function transitionStatus(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:draft,review,published,archived'],
        ]);

        $newStatus = $request->status;

        switch ($newStatus) {
            case Course::STATUS_PUBLISHED:
                Gate::authorize('publish', $course);
                $course->update([
                    'status' => Course::STATUS_PUBLISHED,
                    'published_at' => $course->published_at ?? now(),
                ]);
                $message = 'Course successfully published to the public catalogue!';
                break;

            case Course::STATUS_REVIEW:
                Gate::authorize('submitForReview', $course);
                $course->update(['status' => Course::STATUS_REVIEW]);
                $message = 'Course submitted for administrator review!';
                break;

            case Course::STATUS_ARCHIVED:
                Gate::authorize('archive', $course);
                $course->update(['status' => Course::STATUS_ARCHIVED]);
                $message = 'Course has been archived.';
                break;

            case Course::STATUS_DRAFT:
                Gate::authorize('update', $course);
                $course->update(['status' => Course::STATUS_DRAFT]);
                $message = 'Course returned to draft status.';
                break;

            default:
                abort(400, 'Invalid publication status.');
        }

        return redirect()->route('management.courses.show', $course)->with('status', $message);
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        Gate::authorize('delete', $course);

        $course->delete();

        return redirect()->route('management.courses.index')
            ->with('status', 'Course has been safely deleted.');
    }

    /**
     * Handle safe image upload with MIME validation and UUID generation.
     */
    protected function handleThumbnailUpload(UploadedFile $file, string $title): string
    {
        $extension = $file->guessExtension() ?? 'jpg';
        $safeName = Str::slug($title).'-'.Str::uuid().'.'.$extension;
        $path = $file->storeAs('courses/thumbnails', $safeName, 'public');

        // Automatically sync to public/storage if not symlinked (Windows compatibility)
        $publicDestDir = public_path('storage/courses/thumbnails');
        if (! file_exists($publicDestDir)) {
            @mkdir($publicDestDir, 0755, true);
        }
        @copy(storage_path('app/public/'.$path), public_path('storage/'.$path));

        return '/storage/'.$path;
    }

    /**
     * Synchronize course instructors.
     */
    protected function syncInstructors(Course $course, User $currentUser, array $instructorIds): void
    {
        if ($currentUser->isInstructor() && ! $currentUser->isAdmin()) {
            // Instructor creating the course is assigned as primary
            $course->instructors()->syncWithoutDetaching([
                $currentUser->id => [
                    'is_primary' => true,
                    'can_manage_content' => true,
                    'can_grade' => true,
                ],
            ]);

            return;
        }

        // Admin flow
        if (empty($instructorIds)) {
            $instructorIds = [$currentUser->id];
        }

        $syncData = [];
        $isFirst = true;
        foreach ($instructorIds as $id) {
            $syncData[$id] = [
                'is_primary' => $isFirst,
                'can_manage_content' => true,
                'can_grade' => true,
            ];
            $isFirst = false;
        }

        $course->instructors()->sync($syncData);
    }

    /**
     * Synchronize multiple delivery options for a single course record.
     */
    protected function syncDeliveryOptions(Course $course, Request $request): void
    {
        $selectedModes = $request->input('delivery_modes', []);

        // 1. Online Self-Paced Option
        if (in_array('online', $selectedModes)) {
            $onlinePrice = (float) $request->input('online_price', 0);
            $course->deliveryOptions()->updateOrCreate(
                ['delivery_mode' => 'online', 'title' => 'Online Self-Paced Learning'],
                [
                    'description' => 'Lifetime access to on-demand recorded lessons and resources.',
                    'price' => $onlinePrice,
                    'currency' => 'LKR',
                    'is_free' => $onlinePrice === 0.0,
                    'is_active' => true,
                    'access_period_days' => $request->input('online_access_days'),
                    'includes_recorded_lessons' => true,
                    'includes_physical_sessions' => false,
                    'includes_live_classes' => false,
                    'includes_certificate' => true,
                ]
            );
        } else {
            $course->deliveryOptions()->where('delivery_mode', 'online')->delete();
        }

        // 2. Physical Classroom Option
        if (in_array('physical', $selectedModes)) {
            $physicalPrice = (float) $request->input('physical_price', 0);
            $physicalOption = $course->deliveryOptions()->updateOrCreate(
                ['delivery_mode' => 'physical', 'title' => 'In-Person Physical Classroom'],
                [
                    'description' => 'Hands-on interactive classroom lectures, lab sessions, and local batch peers.',
                    'price' => $physicalPrice,
                    'currency' => 'LKR',
                    'is_free' => $physicalPrice === 0.0,
                    'is_active' => true,
                    'max_capacity' => $request->input('physical_max_capacity', 30),
                    'includes_recorded_lessons' => true,
                    'includes_physical_sessions' => true,
                    'includes_live_classes' => false,
                    'includes_certificate' => true,
                ]
            );

            // Create initial batch intake if location specified and no batches exist yet
            if ($request->filled('physical_location_id') && $physicalOption->batches()->doesntExist()) {
                CourseBatch::create([
                    'course_delivery_option_id' => $physicalOption->id,
                    'location_id' => $request->input('physical_location_id'),
                    'batch_code' => 'BAT-'.strtoupper(Str::random(6)),
                    'batch_name' => 'Intake 01 (Upcoming)',
                    'max_seats' => $request->input('physical_max_capacity', 30),
                    'enrolled_seats' => 0,
                    'start_date' => now()->addDays(14),
                    'end_date' => now()->addDays(60),
                    'status' => 'upcoming',
                ]);
            }
        } else {
            $course->deliveryOptions()->where('delivery_mode', 'physical')->delete();
        }

        // 3. Hybrid Delivery Option
        if (in_array('hybrid', $selectedModes)) {
            $hybridPrice = (float) $request->input('hybrid_price', 0);
            $course->deliveryOptions()->updateOrCreate(
                ['delivery_mode' => 'hybrid', 'title' => 'Hybrid Cohort (Online + Physical Sessions)'],
                [
                    'description' => 'Combination of online materials, live webinars, and weekend classroom labs.',
                    'price' => $hybridPrice,
                    'currency' => 'LKR',
                    'is_free' => $hybridPrice === 0.0,
                    'is_active' => true,
                    'includes_recorded_lessons' => true,
                    'includes_physical_sessions' => true,
                    'includes_live_classes' => true,
                    'includes_certificate' => true,
                ]
            );
        } else {
            $course->deliveryOptions()->where('delivery_mode', 'hybrid')->delete();
        }
    }
}
