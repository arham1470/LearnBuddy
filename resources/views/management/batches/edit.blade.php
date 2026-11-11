<x-layouts.public title="Edit Batch {{ $batch->batch_code }} - {{ $course->title }} - LearnBuddy" description="Edit physical campus intake, seat limit, status and schedule.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Course Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.batches.index', $course) }}" class="hover:text-lb-orange">Batches</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.batches.show', [$course, $batch]) }}" class="hover:text-lb-orange">{{ $batch->batch_code }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Edit</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold uppercase tracking-wide mb-2">
                    <i class="fas fa-edit text-xs" aria-hidden="true"></i> Edit Batch Configuration
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                    {{ $batch->batch_name }}
                </h1>
                <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                    Update capacity, status, or dates. Any alterations will be recorded in the audit trail.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <form method="POST" action="{{ route('management.courses.batches.update', [$course, $batch]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

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
                            @foreach ($deliveryOptions as $opt)
                                <option value="{{ $opt->id }}" {{ old('course_delivery_option_id', $batch->course_delivery_option_id) == $opt->id ? 'selected' : '' }}>
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
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('location_id', $batch->location_id) == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} &bull; {{ $loc->city }}, {{ $loc->district }} ({{ $loc->province }} Province)
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
                                value="{{ old('batch_code', $batch->batch_code) }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 font-mono focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                required
                            >
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
                                value="{{ old('batch_name', $batch->batch_name) }}"
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
                                value="{{ old('max_seats', $batch->max_seats) }}"
                                min="{{ max(1, $batch->enrolled_seats) }}"
                                max="500"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                required
                            >
                            <p class="text-[10px] text-neutral-400 mt-1">Currently enrolled: {{ $batch->enrolled_seats }} student(s). Minimum is {{ max(1, $batch->enrolled_seats) }}.</p>
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
                                <option value="upcoming" {{ old('status', $batch->status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="open_for_enrollment" {{ old('status', $batch->status) === 'open_for_enrollment' ? 'selected' : '' }}>Open for Enrollment</option>
                                <option value="in_progress" {{ old('status', $batch->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $batch->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $batch->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                                    value="{{ old('registration_start_date', $batch->registration_start_date?->format('Y-m-d')) }}"
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
                                    value="{{ old('registration_end_date', $batch->registration_end_date?->format('Y-m-d')) }}"
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
                                    value="{{ old('start_date', $batch->start_date?->format('Y-m-d')) }}"
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
                                    value="{{ old('end_date', $batch->end_date?->format('Y-m-d')) }}"
                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                    required
                                >
                                @error('end_date')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Audit Trail Change Reason -->
                    <div>
                        <label for="change_reason" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                            Reason for Change (Audit Trail Log)
                        </label>
                        <textarea
                            id="change_reason"
                            name="change_reason"
                            rows="2"
                            placeholder="Optional explanation for students and administrators regarding schedule or venue alterations..."
                            class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                        >{{ old('change_reason') }}</textarea>
                        @error('change_reason')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Controls -->
                    <div class="pt-4 flex items-center justify-between border-t border-neutral-100">
                        <a
                            href="{{ route('management.courses.batches.show', [$course, $batch]) }}"
                            class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 transition"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition cursor-pointer"
                        >
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>
