@extends('layouts.admin')

@section('title', 'Manage Courses')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Academic &amp; Courses</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Manage course curriculums, pricing, instructors, and publishing status.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories.index') }}"
               class="px-4 py-2.5 rounded-xl border border-[#E5E5E5] bg-white hover:bg-[#F7F7F5] text-sm font-semibold text-[#0A0A0A] transition-colors">
                Categories
            </a>
            <a href="{{ route('admin.courses.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-sm font-bold shadow-md shadow-orange-500/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>New Course</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="lb-card p-4">
        <form method="GET" action="{{ route('admin.courses.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-6 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search courses by title or subtitle..."
                       class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none">
            </div>
            <div class="sm:col-span-4">
                <select name="status" class="w-full px-3 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="review" {{ request('status') === 'review' ? 'selected' : '' }}>Review</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#1A1A1A] hover:bg-black text-white text-sm font-semibold transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Courses Table -->
    <div class="lb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Course</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Level</th>
                        <th class="py-3 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse($courses as $course)
                    <tr class="hover:bg-[#FFFDFB] transition-colors">
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 border border-orange-200 flex items-center justify-center text-[#FF5F00] font-bold shrink-0">
                                    <svg class="w-5 h-5 text-[#FF5F00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-[#0A0A0A] text-sm truncate max-w-xs">{{ $course->title }}</div>
                                    <div class="text-xs text-[#6B6B6B] truncate max-w-xs">{{ $course->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-[#6B6B6B]">
                            {{ $course->category->name ?? 'Uncategorized' }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if($course->status === 'published')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#DEF7EC] text-[#03543F]">Published</span>
                            @elseif($course->status === 'draft')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#F3F4F6] text-[#4B5563]">Draft</span>
                            @elseif($course->status === 'review')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#FEF3C7] text-[#92400E]">Review</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ ucfirst($course->status) }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-xs font-semibold text-[#6B6B6B]">
                            {{ ucfirst($course->difficulty_level ?? 'Beginner') }}
                        </td>
                        <td class="py-3.5 px-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.courses.edit', $course) }}"
                                   class="px-3 py-1.5 rounded-lg border border-[#E5E5E5] hover:border-orange-300 hover:text-[#FF5F00] text-xs font-bold transition-colors">
                                    Edit
                                </a>
                                <a href="{{ route('courses.show', $course->slug) }}" target="_blank"
                                   class="px-3 py-1.5 rounded-lg bg-[#F7F7F5] hover:bg-[#FFF0E6] text-[#6B6B6B] hover:text-[#FF5F00] text-xs font-bold transition-colors">
                                    Preview
                                </a>
                                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                      data-confirm="Are you sure you want to delete this course? All associated curriculum modules, lessons, and delivery options will be archived."
                                      data-confirm-title="Delete Course"
                                      data-confirm-item="{{ $course->title }}"
                                      data-confirm-btn="Delete Course"
                                      data-confirm-variant="danger"
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Delete Course"
                                            class="p-1.5 rounded-lg border border-transparent hover:border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-[#6B6B6B]">
                            <div class="w-12 h-12 rounded-2xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div class="font-bold text-[#0A0A0A]">No courses found</div>
                            <div class="text-xs text-[#6B6B6B] mt-1">Get started by creating your first course.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($courses->hasPages())
        <div class="p-4 border-t border-[#E5E5E5]">
            {{ $courses->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
