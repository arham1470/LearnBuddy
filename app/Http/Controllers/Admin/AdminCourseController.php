<?php

namespace App\Http\Controllers\Admin;

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
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    /**
     * Display a listing of courses in the admin dashboard.
     */
    public function index(Request $request): View
    {
        $query = Course::with(['category', 'instructors'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $courses = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'categories'));
    }

    /**
     * Show form to create a new course draft inside the admin dashboard.
     */
    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $instructors = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'instructor']))->orderBy('name')->get();
        $locations = PhysicalLocation::where('is_active', true)->orderBy('city')->get();

        return view('admin.courses.create', compact('categories', 'instructors', 'locations'));
    }

    /**
     * Store a newly created course draft.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $user = $request->user();

        return DB::transaction(function () use ($request, $user) {
            $thumbnailUrl = null;

            if ($request->hasFile('thumbnail')) {
                $thumbnailUrl = $this->handleThumbnailUpload($request->file('thumbnail'), $request->title);
            }

            $learningOutcomes = array_values(array_filter($request->input('learning_outcomes', [])));
            $prerequisites = array_values(array_filter($request->input('prerequisites', [])));

            $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);
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

            $this->syncInstructors($course, $user, $request->input('instructor_ids', []));
            $this->syncDeliveryOptions($course, $request);

            return redirect()->route('admin.courses.index')
                ->with('status', 'Course draft created successfully!');
        });
    }

    /**
     * Show form to edit an existing course inside the admin dashboard.
     */
    public function edit(Course $course): View
    {
        $course->load(['deliveryOptions', 'instructors']);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $instructors = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'instructor']))->orderBy('name')->get();
        $locations = PhysicalLocation::where('is_active', true)->orderBy('city')->get();

        return view('admin.courses.edit', compact('course', 'categories', 'instructors', 'locations'));
    }

    /**
     * Update an existing course inside the admin dashboard.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $user = $request->user();

        return DB::transaction(function () use ($request, $course, $user) {
            $thumbnailUrl = $course->thumbnail_url;

            if ($request->hasFile('thumbnail')) {
                $thumbnailUrl = $this->handleThumbnailUpload($request->file('thumbnail'), $request->title);
            }

            $learningOutcomes = array_values(array_filter($request->input('learning_outcomes', [])));
            $prerequisites = array_values(array_filter($request->input('prerequisites', [])));

            $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);
            if ($slug !== $course->slug) {
                $originalSlug = $slug;
                $counter = 1;
                while (Course::where('slug', $slug)->where('id', '!=', $course->id)->exists()) {
                    $slug = "{$originalSlug}-{$counter}";
                    $counter++;
                }
            }

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

            $this->syncInstructors($course, $user, $request->input('instructor_ids', []));
            $this->syncDeliveryOptions($course, $request);

            return redirect()->route('admin.courses.index')
                ->with('status', 'Course updated successfully!');
        });
    }

    /**
     * Delete a course.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $title = $course->title;
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('status', "Course '{$title}' has been deleted.");
    }

    /**
     * Handle safe image upload.
     */
    protected function handleThumbnailUpload(UploadedFile $file, string $title): string
    {
        $extension = $file->guessExtension() ?? 'jpg';
        $safeName = Str::slug($title).'-'.Str::uuid().'.'.$extension;
        $path = $file->storeAs('courses/thumbnails', $safeName, 'public');

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
     * Synchronize delivery options.
     */
    protected function syncDeliveryOptions(Course $course, Request $request): void
    {
        $selectedModes = $request->input('delivery_modes', []);

        // 1. Online Self-Paced Option
        if (in_array('online', $selectedModes)) {
            $onlinePrice = (float) $request->input('online_price', 0);
            $existingOnline = $course->deliveryOptions()->where('delivery_mode', 'online')->first();
            $onlineData = [
                'title' => $existingOnline?->title ?? 'Online Self-Paced Learning',
                'description' => $existingOnline?->description ?? 'Lifetime access to on-demand recorded lessons and resources.',
                'price' => $onlinePrice,
                'currency' => 'LKR',
                'is_free' => $onlinePrice === 0.0,
                'is_active' => true,
                'access_period_days' => $request->input('online_access_days'),
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => false,
                'includes_live_classes' => false,
                'includes_certificate' => true,
            ];

            if ($existingOnline) {
                $existingOnline->update($onlineData);
            } else {
                $course->deliveryOptions()->create(array_merge(['delivery_mode' => 'online'], $onlineData));
            }
        } else {
            $course->deliveryOptions()->where('delivery_mode', 'online')->delete();
        }

        // 2. Physical Classroom Option
        if (in_array('physical', $selectedModes)) {
            $physicalPrice = (float) $request->input('physical_price', 0);
            $existingPhysical = $course->deliveryOptions()->where('delivery_mode', 'physical')->first();
            $physicalData = [
                'title' => $existingPhysical?->title ?? 'In-Person Physical Classroom',
                'description' => $existingPhysical?->description ?? 'Hands-on interactive classroom lectures, lab sessions, and local batch peers.',
                'price' => $physicalPrice,
                'currency' => 'LKR',
                'is_free' => $physicalPrice === 0.0,
                'is_active' => true,
                'max_capacity' => $request->input('physical_max_capacity', 30),
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => true,
                'includes_live_classes' => false,
                'includes_certificate' => true,
            ];

            if ($existingPhysical) {
                $existingPhysical->update($physicalData);
                $physicalOption = $existingPhysical;
            } else {
                $physicalOption = $course->deliveryOptions()->create(array_merge(['delivery_mode' => 'physical'], $physicalData));
            }

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
            $existingHybrid = $course->deliveryOptions()->where('delivery_mode', 'hybrid')->first();
            $hybridData = [
                'title' => $existingHybrid?->title ?? 'Hybrid Cohort (Online + Physical Sessions)',
                'description' => $existingHybrid?->description ?? 'Combination of online materials, live webinars, and weekend classroom labs.',
                'price' => $hybridPrice,
                'currency' => 'LKR',
                'is_free' => $hybridPrice === 0.0,
                'is_active' => true,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => true,
                'includes_live_classes' => true,
                'includes_certificate' => true,
            ];

            if ($existingHybrid) {
                $existingHybrid->update($hybridData);
            } else {
                $course->deliveryOptions()->create(array_merge(['delivery_mode' => 'hybrid'], $hybridData));
            }
        } else {
            $course->deliveryOptions()->where('delivery_mode', 'hybrid')->delete();
        }
    }
}
