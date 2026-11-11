<x-layouts.public title="Create Batch - {{ $course->title }} - LearnBuddy" description="Configure physical campus intake, seat limit, dates and Sri Lankan venue.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Course Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.batches.index', $course) }}" class="hover:text-lb-orange">Batches</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Create New Batch</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold uppercase tracking-wide mb-2">
                    <i class="fas fa-plus-circle text-xs" aria-hidden="true"></i> New Campus Intake
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                    Create Physical Course Batch
                </h1>
                <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                    Set up a physical classroom cohort for <strong>{{ $course->title }}</strong> with designated seat capacity and Sri Lankan training venue.
                </p>
            </div>

            @if ($deliveryOptions->isEmpty())
                <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 text-amber-800 text-xs space-y-2">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <i class="fas fa-exclamation-triangle text-amber-600"></i>
                        <span>No Physical Delivery Options Configured</span>
                    </div>
                    <p>
                        This course does not currently have any active <strong>Physical</strong> or <strong>Hybrid</strong> delivery options configured.
                        Please edit the course details to configure a physical delivery pricing tier before accepting campus enrollments.
                    </p>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <form method="POST" action="{{ route('management.courses.batches.store', $course) }}" class="space-y-6">
                    @csrf

                    <!-- Course Delivery Option Selection -->
                    <div>
                        <label for="course_delivery_option_id" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                            Course Delivery Option <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="course_delivery_option_id"
                            name="course_delivery_option_id"
                            class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            required
                        >
                            <option value="">-- Select Physical / Hybrid Delivery Tier --</option>
                            @foreach ($deliveryOptions as $opt)
                                <option value="{{ $opt->id }}" {{ old('course_delivery_option_id') == $opt->id ? 'selected' : '' }}>
                                    {{ $opt->title }} ({{ ucfirst($opt->delivery_mode) }} &bull; LKR {{ number_format($opt->price, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_delivery_option_id')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campus Location Selection -->
                    <div>
                        <label for="location_id" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                            Sri Lankan Campus / Training Venue <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="location_id"
                            name="location_id"
                            class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            required
                        >
                            <option value="">-- Select Physical Training Centre --</option>
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} &bull; {{ $loc->city }}, {{ $loc->district }} ({{ $loc->province }} Province) &bull; Room Cap: {{ $loc->room_capacity ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('location_id')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Batch Code & Name -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="batch_code" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Batch Code / Identifier <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="batch_code"
                                name="batch_code"
                                value="{{ old('batch_code') }}"
                                placeholder="e.g. FSWD-2026-COL-01"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 font-mono focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                required
                            >
                            <p class="text-[10px] text-neutral-400 mt-1">Letters, numbers, dashes, and underscores only.</p>
                            @error('batch_code')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="batch_name" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Batch Name / Display Title <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="batch_name"
                                name="batch_name"
                                value="{{ old('batch_name') }}"
                                placeholder="e.g. Weekend Colombo Intake 01"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                required
                            >
                            @error('batch_name')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Max Seats & Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="max_seats" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Maximum Seat Capacity <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="max_seats"
                                name="max_seats"
                                value="{{ old('max_seats', 30) }}"
                                min="1"
                                max="500"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                required
                            >
                            <p class="text-[10px] text-neutral-400 mt-1">Strict limit enforced against concurrent overbooking.</p>
                            @error('max_seats')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Batch Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                required
                            >
                                <option value="upcoming" {{ old('status', 'upcoming') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="open_for_enrollment" {{ old('status') === 'open_for_enrollment' ? 'selected' : '' }}>Open for Enrollment</option>
                                <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Registration Window (Asia/Colombo) -->
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/80 space-y-3">
                        <div class="text-xs font-bold text-neutral-700 uppercase tracking-wider">
                            Registration Window (Optional)
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="registration_start_date" class="block text-[11px] font-semibold text-neutral-600 mb-1">
                                    Registration Open Date
                                </label>
                                <input
                                    type="date"
                                    id="registration_start_date"
                                    name="registration_start_date"
                                    value="{{ old('registration_start_date') }}"
                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                >
                            </div>

                            <div>
                                <label for="registration_end_date" class="block text-[11px] font-semibold text-neutral-600 mb-1">
                                    Registration Close Date
                                </label>
                                <input
                                    type="date"
                                    id="registration_end_date"
                                    name="registration_end_date"
                                    value="{{ old('registration_end_date') }}"
                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Batch Course Dates (Asia/Colombo) -->
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/80 space-y-3">
                        <div class="text-xs font-bold text-neutral-700 uppercase tracking-wider">
                            Course Intake Schedule (Asia/Colombo) <span class="text-rose-500">*</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-[11px] font-semibold text-neutral-600 mb-1">
                                    Batch Start Date <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    value="{{ old('start_date') }}"
                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                    required
                                >
                                @error('start_date')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-[11px] font-semibold text-neutral-600 mb-1">
                                    Batch Expected End Date <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    value="{{ old('end_date') }}"
                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                    required
                                >
                                @error('end_date')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Action Controls -->
                    <div class="pt-4 flex items-center justify-between border-t border-neutral-100">
                        <a
                            href="{{ route('management.courses.batches.index', $course) }}"
                            class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 transition"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition cursor-pointer"
                        >
                            Create Batch &amp; Configure Timetable
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>
