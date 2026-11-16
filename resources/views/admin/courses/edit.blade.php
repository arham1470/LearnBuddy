@extends('layouts.admin')

@section('title', 'Edit Course: ' . $course->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div>
        <div class="flex items-center gap-2 text-xs text-[#6B6B6B] mb-1">
            <a href="{{ route('admin.courses.index') }}" class="hover:text-[#FF5F00] transition-colors">Courses</a>
            <svg class="w-3 h-3 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#0A0A0A] font-semibold">Edit Course</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Edit: {{ $course->title }}</h1>
                <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Update course metadata, pricing tiers, and delivery options.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('courses.show', $course->slug) }}" target="_blank"
                   class="px-3.5 py-2 rounded-xl bg-white border border-[#E5E5E5] hover:border-orange-300 text-xs font-semibold text-[#0A0A0A] hover:text-[#FF5F00] transition-colors inline-flex items-center gap-1.5 shadow-xs">
                    <span>View Public Page</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Validation Errors Alert -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-bold">Please resolve the following errors:</p>
                <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data" class="space-y-6" novalidate>
        @csrf
        @method('PUT')

        <!-- 1. Core Course Information -->
        <div class="lb-card p-6 sm:p-8 space-y-5">
            <div class="border-b border-[#E5E5E5] pb-3 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#0A0A0A]">1. Core Course Information</h2>
                    <p class="text-xs text-[#6B6B6B]">General course titles, taxonomies, and basic descriptors.</p>
                </div>
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                    Course Title <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" required
                       class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
            </div>

            <!-- Subtitle -->
            <div>
                <label for="subtitle" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                    Course Subtitle / Tagline
                </label>
                <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle', $course->subtitle) }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
            </div>

            <!-- Slug & Category -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="slug" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        URL Slug
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $course->slug) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                </div>
                <div>
                    <label for="category_id" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Course Category <span class="text-rose-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-3 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none transition-colors">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $course->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Difficulty, Language, Duration -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="difficulty_level" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Difficulty Level <span class="text-rose-500">*</span>
                    </label>
                    <select id="difficulty_level" name="difficulty_level" required
                            class="w-full px-3 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none transition-colors">
                        <option value="beginner" {{ old('difficulty_level', $course->difficulty_level) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ old('difficulty_level', $course->difficulty_level) === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ old('difficulty_level', $course->difficulty_level) === 'advanced' ? 'selected' : '' }}>Advanced</option>
                        <option value="all_levels" {{ old('difficulty_level', $course->difficulty_level) === 'all_levels' ? 'selected' : '' }}>All Levels</option>
                    </select>
                </div>
                <div>
                    <label for="language" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Language <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="language" name="language" value="{{ old('language', $course->language ?? 'English') }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none transition-colors">
                </div>
                <div>
                    <label for="duration_hours" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Duration (Hours) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.5" id="duration_hours" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours ?? 40) }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none transition-colors">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                    Comprehensive Description <span class="text-rose-500">*</span>
                </label>
                <textarea id="description" name="description" rows="5" required
                          class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">{{ old('description', $course->description) }}</textarea>
            </div>
        </div>

        <!-- 2. Instructor Assignment -->
        <div class="lb-card p-6 sm:p-8 space-y-4">
            <div class="border-b border-[#E5E5E5] pb-3 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#0A0A0A]">2. Assigned Instructors</h2>
                    <p class="text-xs text-[#6B6B6B]">Select primary instructors who manage student grading and course content.</p>
                </div>
            </div>

            @php
                $assignedInstructorIds = $course->instructors->pluck('id')->toArray();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach ($instructors as $instructor)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-[#E5E5E5] hover:border-orange-300 hover:bg-[#F7F7F5]/60 transition-colors cursor-pointer">
                        <input type="checkbox" name="instructor_ids[]" value="{{ $instructor->id }}"
                               {{ in_array($instructor->id, old('instructor_ids', $assignedInstructorIds)) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-[#FF5F00] focus:ring-[#FF5F00] border-[#E5E5E5]">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[#0A0A0A] truncate">{{ $instructor->name }}</p>
                            <p class="text-[0.7rem] text-[#6B6B6B] truncate">{{ $instructor->email }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- 3. Delivery Modes & Pricing -->
        @php
            $onlineOpt = $course->deliveryOptions->firstWhere('delivery_mode', 'online');
            $physicalOpt = $course->deliveryOptions->firstWhere('delivery_mode', 'physical');
            $hybridOpt = $course->deliveryOptions->firstWhere('delivery_mode', 'hybrid');
        @endphp
        <div class="lb-card p-6 sm:p-8 space-y-5" x-data="{
            online: {{ old('delivery_modes') ? (in_array('online', old('delivery_modes')) ? 'true' : 'false') : ($onlineOpt ? 'true' : 'false') }},
            physical: {{ old('delivery_modes') ? (in_array('physical', old('delivery_modes')) ? 'true' : 'false') : ($physicalOpt ? 'true' : 'false') }},
            hybrid: {{ old('delivery_modes') ? (in_array('hybrid', old('delivery_modes')) ? 'true' : 'false') : ($hybridOpt ? 'true' : 'false') }}
        }">
            <div class="border-b border-[#E5E5E5] pb-3 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#0A0A0A]">3. Delivery Modes &amp; Pricing (LKR)</h2>
                    <p class="text-xs text-[#6B6B6B]">Select at least one delivery option for students to enroll in.</p>
                </div>
            </div>

            <!-- Mode Selection Toggles -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label :class="online ? 'border-[#FF5F00] bg-[#FFF0E6]/30' : 'border-[#E5E5E5] bg-[#F7F7F5]/40'"
                       class="p-4 rounded-xl border flex items-start gap-3 cursor-pointer transition-colors">
                    <input type="checkbox" name="delivery_modes[]" value="online" x-model="online"
                           class="w-4 h-4 mt-0.5 rounded text-[#FF5F00] focus:ring-[#FF5F00] border-[#E5E5E5]">
                    <div>
                        <div class="text-sm font-bold text-[#0A0A0A]">Online Self-Paced</div>
                        <div class="text-[0.72rem] text-[#6B6B6B]">Recorded on-demand video learning</div>
                    </div>
                </label>

                <label :class="physical ? 'border-[#FF5F00] bg-[#FFF0E6]/30' : 'border-[#E5E5E5] bg-[#F7F7F5]/40'"
                       class="p-4 rounded-xl border flex items-start gap-3 cursor-pointer transition-colors">
                    <input type="checkbox" name="delivery_modes[]" value="physical" x-model="physical"
                           class="w-4 h-4 mt-0.5 rounded text-[#FF5F00] focus:ring-[#FF5F00] border-[#E5E5E5]">
                    <div>
                        <div class="text-sm font-bold text-[#0A0A0A]">In-Person Physical</div>
                        <div class="text-[0.72rem] text-[#6B6B6B]">Classroom lectures at physical campus</div>
                    </div>
                </label>

                <label :class="hybrid ? 'border-[#FF5F00] bg-[#FFF0E6]/30' : 'border-[#E5E5E5] bg-[#F7F7F5]/40'"
                       class="p-4 rounded-xl border flex items-start gap-3 cursor-pointer transition-colors">
                    <input type="checkbox" name="delivery_modes[]" value="hybrid" x-model="hybrid"
                           class="w-4 h-4 mt-0.5 rounded text-[#FF5F00] focus:ring-[#FF5F00] border-[#E5E5E5]">
                    <div>
                        <div class="text-sm font-bold text-[#0A0A0A]">Hybrid Cohort</div>
                        <div class="text-[0.72rem] text-[#6B6B6B]">Online content + physical lab sessions</div>
                    </div>
                </label>
            </div>

            <!-- Online Mode Options -->
            <div x-show="online" x-cloak class="p-4 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] space-y-3">
                <div class="text-xs font-bold text-[#0A0A0A] uppercase tracking-wider">Online Mode Settings</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#0A0A0A] mb-1">Price (LKR) · 0 for Free</label>
                        <input type="number" name="online_price" value="{{ old('online_price', $onlineOpt?->price ?? 0) }}" min="0"
                               class="w-full px-3 py-2 rounded-lg bg-white border border-[#E5E5E5] text-sm outline-none focus:border-[#FF5F00]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#0A0A0A] mb-1">Access Period (Days) · Blank for Lifetime</label>
                        <input type="number" name="online_access_days" value="{{ old('online_access_days', $onlineOpt?->access_period_days) }}" min="1"
                               class="w-full px-3 py-2 rounded-lg bg-white border border-[#E5E5E5] text-sm outline-none focus:border-[#FF5F00]">
                    </div>
                </div>
            </div>

            <!-- Physical Mode Options -->
            <div x-show="physical" x-cloak class="p-4 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] space-y-3">
                <div class="text-xs font-bold text-[#0A0A0A] uppercase tracking-wider">Physical Mode Settings</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#0A0A0A] mb-1">Tuition Fee (LKR)</label>
                        <input type="number" name="physical_price" value="{{ old('physical_price', $physicalOpt?->price ?? 25000) }}" min="0"
                               class="w-full px-3 py-2 rounded-lg bg-white border border-[#E5E5E5] text-sm outline-none focus:border-[#FF5F00]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#0A0A0A] mb-1">Max Classroom Seats</label>
                        <input type="number" name="physical_max_capacity" value="{{ old('physical_max_capacity', $physicalOpt?->max_capacity ?? 30) }}" min="1"
                               class="w-full px-3 py-2 rounded-lg bg-white border border-[#E5E5E5] text-sm outline-none focus:border-[#FF5F00]">
                    </div>
                </div>
            </div>

            <!-- Hybrid Mode Options -->
            <div x-show="hybrid" x-cloak class="p-4 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] space-y-3">
                <div class="text-xs font-bold text-[#0A0A0A] uppercase tracking-wider">Hybrid Mode Settings</div>
                <div>
                    <label class="block text-xs font-semibold text-[#0A0A0A] mb-1">Hybrid Tuition Fee (LKR)</label>
                    <input type="number" name="hybrid_price" value="{{ old('hybrid_price', $hybridOpt?->price ?? 35000) }}" min="0"
                           class="w-full max-w-xs px-3 py-2 rounded-lg bg-white border border-[#E5E5E5] text-sm outline-none focus:border-[#FF5F00]">
                </div>
            </div>
        </div>

        <!-- 4. Media & Assets -->
        <div class="lb-card p-6 sm:p-8 space-y-4">
            <div class="border-b border-[#E5E5E5] pb-3 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#0A0A0A]">4. Media &amp; Promo Assets</h2>
                    <p class="text-xs text-[#6B6B6B]">Update course cover banner or preview video.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="thumbnail" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Update Course Thumbnail
                    </label>
                    @if ($course->thumbnail_url)
                        <div class="mb-2">
                            <img src="{{ $course->thumbnail_url }}" alt="Current thumbnail" class="w-32 h-20 object-cover rounded-lg border border-[#E5E5E5]">
                        </div>
                    @endif
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                           class="w-full px-3 py-2 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-xs text-[#6B6B6B] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#1A1A1A] file:text-white hover:file:bg-black cursor-pointer">
                </div>
                <div>
                    <label for="preview_video_url" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Preview Video URL
                    </label>
                    <input type="url" id="preview_video_url" name="preview_video_url" value="{{ old('preview_video_url', $course->preview_video_url) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none transition-colors">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-2 flex items-center justify-between">
            <a href="{{ route('admin.courses.index') }}"
               class="px-5 py-2.5 rounded-xl border border-[#E5E5E5] text-sm font-semibold text-[#6B6B6B] hover:text-[#0A0A0A] hover:bg-white transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-7 py-3 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-sm font-bold shadow-md shadow-orange-500/25 transition-all">
                Update Course
            </button>
        </div>

    </form>

</div>
@endsection
