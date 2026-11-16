@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div>
        <div class="flex items-center gap-2 text-xs text-[#6B6B6B] mb-1">
            <a href="{{ route('admin.courses.index') }}" class="hover:text-[#FF5F00] transition-colors">Courses</a>
            <svg class="w-3 h-3 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('admin.categories.index') }}" class="hover:text-[#FF5F00] transition-colors">Categories</a>
            <svg class="w-3 h-3 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#0A0A0A] font-semibold">New Category</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Create Course Category</h1>
        <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Add a new subject taxonomy for organizing courses.</p>
    </div>

    <!-- Form Card -->
    <div class="lb-card p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                    Category Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Software Engineering, Cloud Computing"
                       class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                @error('name') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                    URL Slug (Optional)
                </label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                       placeholder="e.g. software-engineering (auto-generated if left empty)"
                       class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                @error('slug') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Icon -->
            <div>
                <label for="icon" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                    Icon Identifier / Class
                </label>
                <input type="text" id="icon" name="icon" value="{{ old('icon', 'fas fa-graduation-cap') }}"
                       placeholder="fas fa-code, fas fa-laptop-code"
                       class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                @error('icon') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                    Description
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Brief summary of courses grouped under this topic..."
                          class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Sort Order & Active Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="sort_order" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Display Sort Order
                    </label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', '0') }}" min="0"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                    @error('sort_order') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-[#FF5F00] focus:ring-[#FF5F00] border-[#E5E5E5]">
                        <span class="text-sm font-semibold text-[#0A0A0A]">Active Category (Visible to students)</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-[#E5E5E5] flex items-center justify-between">
                <a href="{{ route('admin.categories.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-[#E5E5E5] text-sm font-semibold text-[#6B6B6B] hover:text-[#0A0A0A] hover:bg-[#F7F7F5] transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-sm font-bold shadow-md shadow-orange-500/25 transition-all">
                    Save Category
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
