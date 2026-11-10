<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicCourseController extends Controller
{
    /**
     * Display the course catalogue with search and multifaceted filtering.
     */
    public function index(Request $request): View
    {
        $query = Course::query()
            ->published()
            ->with([
                'category',
                'instructors',
                'deliveryOptions' => fn ($q) => $q->where('is_active', true),
                'publishedReviews',
            ]);

        // Keyword Search (Title, Subtitle, Description)
        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter (Slug or ID)
        if ($categorySlug = $request->input('category')) {
            $query->whereHas('category', function (Builder $q) use ($categorySlug) {
                $q->where('slug', $categorySlug)->orWhere('id', $categorySlug);
            });
        }

        // Delivery Mode Filter (online, physical, hybrid)
        if ($deliveryMode = $request->input('delivery_mode')) {
            $query->whereHas('deliveryOptions', function (Builder $q) use ($deliveryMode) {
                $q->where('is_active', true)->where('delivery_mode', $deliveryMode);
            });
        }

        // Language Filter
        if ($language = $request->input('language')) {
            $query->where('language', $language);
        }

        // Difficulty Level Filter
        if ($difficulty = $request->input('difficulty')) {
            $query->where('difficulty_level', $difficulty);
        }

        // Price Filter (free / paid)
        if ($priceFilter = $request->input('price')) {
            if ($priceFilter === 'free') {
                $query->whereHas('deliveryOptions', function (Builder $q) {
                    $q->where('is_active', true)
                        ->where(function (Builder $sub) {
                            $sub->where('is_free', true)->orWhere('price', 0);
                        });
                });
            } elseif ($priceFilter === 'paid') {
                $query->whereHas('deliveryOptions', function (Builder $q) {
                    $q->where('is_active', true)
                        ->where('is_free', false)
                        ->where('price', '>', 0);
                });
            }
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->withMin(['deliveryOptions as min_price' => fn ($q) => $q->where('is_active', true)], 'price')
                ->orderBy('min_price', 'asc'),
            'price_desc' => $query->withMax(['deliveryOptions as max_price' => fn ($q) => $q->where('is_active', true)], 'price')
                ->orderBy('max_price', 'desc'),
            'duration_asc' => $query->orderBy('duration_hours', 'asc'),
            'duration_desc' => $query->orderBy('duration_hours', 'desc'),
            default => $query->latest('published_at'),
        };

        $courses = $query->paginate(9)->withQueryString();

        // Metadata for sidebar and filter controls
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['courses as published_courses_count' => fn ($q) => $q->where('status', Course::STATUS_PUBLISHED)])
            ->orderBy('sort_order')
            ->get();

        $availableLanguages = Course::published()
            ->whereNotNull('language')
            ->distinct()
            ->pluck('language');

        $activeFiltersCount = collect([
            $request->input('q'),
            $request->input('category'),
            $request->input('delivery_mode'),
            $request->input('language'),
            $request->input('difficulty'),
            $request->input('price'),
        ])->filter(fn ($v) => ! empty($v))->count();

        return view('courses.index', compact(
            'courses',
            'categories',
            'availableLanguages',
            'activeFiltersCount',
            'sort'
        ));
    }

    /**
     * Display a published course's public details page.
     */
    public function show(Course $course): View
    {
        if (! $course->isPublished()) {
            abort(404, 'The requested course is not publicly available.');
        }

        $course->load([
            'category',
            'instructors',
            'deliveryOptions' => function ($q) {
                $q->where('is_active', true)->with(['batches.location']);
            },
            'sections' => function ($q) {
                $q->where('is_published', true)->orderBy('sort_order')->with([
                    'lessons' => fn ($lq) => $lq->where('is_published', true)->orderBy('sort_order'),
                ]);
            },
            'publishedReviews.user',
        ]);

        $relatedCourses = Course::published()
            ->where('id', '!=', $course->id)
            ->where('category_id', $course->category_id)
            ->with(['category', 'instructors', 'deliveryOptions' => fn ($q) => $q->where('is_active', true), 'publishedReviews'])
            ->take(3)
            ->get();

        // Calculate curriculum aggregates
        $totalLessons = $course->sections->sum(fn ($sec) => $sec->lessons->count());
        $totalDurationMinutes = $course->sections->sum(fn ($sec) => $sec->lessons->sum('duration_minutes'));
        $previewLessons = $course->sections->flatMap->lessons->where('is_preview', true);

        return view('courses.show', compact('course', 'relatedCourses', 'totalLessons', 'totalDurationMinutes', 'previewLessons'));
    }
}
