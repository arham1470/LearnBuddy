@extends('layouts.admin')

@section('title', 'Course Categories')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#6B6B6B] mb-1">
                <a href="{{ route('admin.courses.index') }}" class="hover:text-[#FF5F00] transition-colors">Courses</a>
                <svg class="w-3 h-3 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#0A0A0A] font-semibold">Categories</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Course Categories</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Taxonomy for grouping, organizing, and filtering courses across LearnBuddy.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}"
               class="px-4 py-2.5 rounded-xl border border-[#E5E5E5] bg-white hover:bg-[#F7F7F5] text-sm font-semibold text-[#0A0A0A] transition-colors">
                All Courses
            </a>
            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-sm font-bold shadow-md shadow-orange-500/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>New Category</span>
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('status') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filters & Search Bar -->
    <div class="lb-card p-4">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative w-full sm:max-w-md">
                <svg class="w-4 h-4 text-[#6B6B6B] absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search category name, slug or description..."
                       class="w-full pl-10 pr-4 py-2 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-5 py-2 rounded-xl bg-[#1A1A1A] hover:bg-black text-white text-sm font-semibold transition-colors">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-xl border border-[#E5E5E5] text-[#6B6B6B] hover:text-[#0A0A0A] text-sm font-medium transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="lb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Category Name</th>
                        <th class="py-3 px-4">Slug</th>
                        <th class="py-3 px-4 text-center">Courses</th>
                        <th class="py-3 px-4 text-center">Sort Order</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse ($categories as $cat)
                        <tr class="hover:bg-[#F7F7F5]/60 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#0A0A0A] text-sm">{{ $cat->name }}</p>
                                        @if ($cat->description)
                                            <p class="text-xs text-[#6B6B6B] truncate max-w-xs">{{ $cat->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-[#6B6B6B]">
                                {{ $cat->slug }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#F7F7F5] text-[#0A0A0A] border border-[#E5E5E5]">
                                    {{ $cat->courses_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-xs font-semibold text-[#6B6B6B]">
                                {{ $cat->sort_order }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($cat->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $cat) }}"
                                       class="px-3 py-1.5 rounded-lg border border-[#E5E5E5] hover:border-orange-300 hover:text-[#FF5F00] text-xs font-bold transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Are you sure you want to delete this category?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-transparent hover:border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#6B6B6B]">
                                <div class="w-12 h-12 rounded-2xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                </div>
                                <div class="font-bold text-[#0A0A0A]">No categories found</div>
                                <div class="text-xs text-[#6B6B6B] mt-1">Get started by creating your first course category.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="p-4 border-t border-[#E5E5E5] bg-[#F7F7F5]/50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
