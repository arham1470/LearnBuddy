@props([
    'course',
])

@php
    $instructor = $course->primaryInstructor();
    $rating = $course->averageRating();
    $reviewsCount = $course->reviewsCount();
    $duration = $course->formattedDuration();
    $deliveryModes = $course->deliveryModes();
    $hasPhysical = in_array('physical', $deliveryModes, true);
    $hasOnline = in_array('online', $deliveryModes, true);
    $isHybrid = in_array('hybrid', $deliveryModes, true) || ($hasPhysical && $hasOnline);
@endphp

<x-card variant="white" padding="none" class="group flex flex-col h-full overflow-hidden card-hover-lift border border-neutral-200/80 shadow-lb-sm hover:shadow-lb-md transition-all duration-300">
    <!-- Course Thumbnail Container -->
    <a href="{{ route('courses.show', $course) }}" class="block relative h-44 sm:h-48 bg-gradient-to-tr from-neutral-900 to-neutral-800 overflow-hidden shrink-0" aria-label="{{ $course->title }}">
        @if ($course->thumbnail_url)
            <img
                src="{{ asset('storage/' . $course->thumbnail_url) }}"
                alt="{{ $course->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-orange-50 via-white to-neutral-100 group-hover:scale-105 transition-transform duration-500">
                <i class="{{ $course->category?->icon ?? 'fas fa-graduation-cap' }} text-4xl text-neutral-300 group-hover:text-lb-orange transition-colors duration-300" aria-hidden="true"></i>
            </div>
        @endif

        <!-- Top Badges Header (Relative Layout with Zero Overlap & Strict Boundary Inset) -->
        <div class="absolute top-3 inset-x-3 flex flex-wrap items-center justify-between gap-1.5 z-10 pointer-events-none">
            <!-- Left: Delivery Mode & Language -->
            <div class="flex items-center gap-1.5 flex-wrap min-w-0">
                @if ($isHybrid)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-neutral-900/90 text-white backdrop-blur-xs shadow-xs border border-white/10 shrink-0">
                        <i class="fas fa-layer-group text-lb-orange text-[9px]" aria-hidden="true"></i>
                        <span class="whitespace-nowrap">Online + Physical</span>
                    </span>
                @elseif ($hasPhysical)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500 text-white shadow-xs shrink-0">
                        <i class="fas fa-chalkboard-teacher text-[9px]" aria-hidden="true"></i>
                        <span class="whitespace-nowrap">Physical Batch</span>
                    </span>
                @elseif ($hasOnline)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-lb-orange text-white shadow-xs shrink-0">
                        <i class="fas fa-play-circle text-[9px]" aria-hidden="true"></i>
                        <span class="whitespace-nowrap">Online Self-Paced</span>
                    </span>
                @endif

                @if ($course->language && $course->language !== 'English')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/95 text-neutral-800 shadow-xs border border-neutral-200/50 shrink-0">
                        {{ $course->language }}
                    </span>
                @endif
            </div>

            <!-- Right: Category Pill -->
            @if ($course->category)
                <div class="ml-auto shrink-0 max-w-[50%]">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-white/95 backdrop-blur-xs text-neutral-700 shadow-xs border border-neutral-200/60 truncate max-w-full" title="{{ $course->category->name }}">
                        {{ $course->category->name }}
                    </span>
                </div>
            @endif
        </div>
    </a>

    <!-- Content Area (Expands with flex-1 to enforce uniform card heights across the row) -->
    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between">
        <div>
            <!-- Meta Bar: Rating & Duration & Level -->
            <div class="flex items-center justify-between text-xs text-neutral-500 mb-2.5">
                <div>
                    @if ($rating && $reviewsCount > 0)
                        <span class="inline-flex items-center gap-1 font-semibold text-neutral-800">
                            <i class="fas fa-star text-amber-500 text-xs" aria-hidden="true"></i>
                            <span>{{ number_format($rating, 1) }}</span>
                            <span class="text-neutral-400 font-normal">({{ $reviewsCount }})</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-neutral-400">
                            <i class="far fa-star text-neutral-300" aria-hidden="true"></i>
                            <span>No reviews yet</span>
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2.5">
                    @if ($duration)
                        <span class="inline-flex items-center gap-1 text-[11px]">
                            <i class="fas fa-clock text-neutral-400" aria-hidden="true"></i>
                            <span>{{ $duration }}</span>
                        </span>
                    @endif

                    @if ($course->difficulty_level)
                        <span class="capitalize text-[10px] font-bold px-2 py-0.5 rounded-full bg-neutral-100 text-neutral-600">
                            {{ str_replace('_', ' ', $course->difficulty_level) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Course Title -->
            <h3 class="font-bold text-base text-lb-black line-clamp-2 leading-snug group-hover:text-lb-orange transition-colors mb-2">
                <a href="{{ route('courses.show', $course) }}">
                    {{ $course->title }}
                </a>
            </h3>

            <!-- Short Description -->
            <p class="text-xs text-neutral-500 line-clamp-2 leading-relaxed mb-4">
                {{ $course->subtitle ?: Str::limit($course->description, 100) }}
            </p>
        </div>
    </div>

    <!-- Footer Row: Instructor & Price (Anchored cleanly at the bottom via mt-auto) -->
    <div class="mt-auto px-5 py-3.5 bg-neutral-50/70 border-t border-neutral-100 flex items-center justify-between gap-2.5 shrink-0">
        @if ($instructor)
            <a href="{{ route('instructors.show', $instructor) }}" class="flex items-center gap-2 hover:opacity-80 transition min-w-0 flex-1 truncate">
                <div class="w-7 h-7 rounded-full bg-lb-peach text-lb-orange flex items-center justify-center text-xs font-bold shrink-0 border border-orange-200">
                    {{ strtoupper(substr($instructor->name, 0, 2)) }}
                </div>
                <span class="text-xs font-semibold text-neutral-800 truncate">{{ $instructor->name }}</span>
            </a>
        @else
            <span class="text-xs text-neutral-400 truncate flex-1">LearnBuddy Faculty</span>
        @endif

        <div class="text-right shrink-0">
            <span class="text-xs sm:text-sm font-extrabold text-lb-orange whitespace-nowrap">
                {{ $course->formattedPriceRange() }}
            </span>
        </div>
    </div>
</x-card>
