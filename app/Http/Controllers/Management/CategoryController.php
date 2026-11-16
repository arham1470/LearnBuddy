<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View|RedirectResponse
    {
        if ($request->user()?->isAdmin()) {
            return redirect()->route('admin.categories.index');
        }

        Gate::authorize('viewAny', Category::class);

        $categories = Category::withCount('courses')->orderBy('sort_order')->orderBy('name')->paginate(15);

        return view('management.categories.index', compact('categories'));
    }

    public function create(\Illuminate\Http\Request $request): View|RedirectResponse
    {
        if ($request->user()?->isAdmin()) {
            return redirect()->route('admin.categories.create');
        }

        Gate::authorize('create', Category::class);

        return view('management.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'icon' => $request->icon ?? 'fas fa-graduation-cap',
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('management.categories.index')
            ->with('status', 'Category created successfully!');
    }

    public function edit(\Illuminate\Http\Request $request, Category $category): View|RedirectResponse
    {
        if ($request->user()?->isAdmin()) {
            return redirect()->route('admin.categories.edit', $category);
        }

        Gate::authorize('update', $category);

        return view('management.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'icon' => $request->icon ?? $category->icon,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? $category->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('management.categories.index')
            ->with('status', 'Category updated successfully!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        if ($category->courses()->exists()) {
            return back()->withErrors(['category' => 'Cannot delete category that contains existing courses.']);
        }

        $category->delete();

        return redirect()->route('management.categories.index')
            ->with('status', 'Category deleted successfully!');
    }
}
