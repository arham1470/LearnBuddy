<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['courses as published_courses_count' => function ($q) {
                $q->where('status', Course::STATUS_PUBLISHED);
            }])
            ->orderBy('sort_order')
            ->get();

        $featuredCourses = Course::query()
            ->published()
            ->with([
                'category',
                'instructors',
                'deliveryOptions' => fn ($q) => $q->where('is_active', true),
                'publishedReviews',
            ])
            ->latest('published_at')
            ->take(6)
            ->get();

        $totalPublishedCourses = Course::published()->count();

        return view('welcome', compact('categories', 'featuredCourses', 'totalPublishedCourses'));
    }
}
