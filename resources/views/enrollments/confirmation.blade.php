<x-layouts.public :title="'Enrollment Confirmation · ' . $enrollment->enrollment_code . ' · LearnBuddy'">
    <!-- CONFIRMATION HERO -->
    <section class="bg-lb-peach pt-8 sm:pt-14 pb-12 sm:pb-16 rounded-b-[32px] sm:rounded-b-[48px] border-b border-orange-100">
        <div class="max-w-[800px] mx-auto px-4 sm:px-6 text-center space-y-3">
            @if ($enrollment->isActive())
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-2xl shadow-sm mb-2">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                </div>
                <x-badge variant="orange" icon="fas fa-certificate">
                    Enrollment Verified &bull; Active Access
                </x-badge>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-lb-black">
                    You're Enrolled in <span class="text-lb-orange">{{ $enrollment->course->title }}</span>
                </h1>
                <p class="text-xs sm:text-sm text-neutral-600 max-w-lg mx-auto leading-relaxed">
                    Your learning access is active. You can now access curriculum modules and start learning immediately from your Student Dashboard.
                </p>
            @else
                <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-2xl shadow-sm mb-2">
                    <i class="fas fa-clock" aria-hidden="true"></i>
                </div>
                <x-badge variant="orange" icon="fas fa-receipt">
                    Seat Reserved &bull; Pending Payment
                </x-badge>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-lb-black">
                    Enrollment Reserved: <span class="text-lb-orange">{{ $enrollment->course->title }}</span>
                </h1>
                <p class="text-xs sm:text-sm text-neutral-600 max-w-lg mx-auto leading-relaxed">
                    Your seat has been reserved in this intake. In accordance with Phase 8 specifications, payment gateway integration activates in the upcoming payment phase.
                </p>
            @endif
        </div>
    </section>

    <!-- CONFIRMATION RECEIPT CARD -->
    <section class="py-10 sm:py-16 bg-lb-off-white min-h-[500px]">
        <div class="max-w-[800px] mx-auto px-4 sm:px-6 space-y-6">
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-md overflow-hidden">
                <!-- Receipt Header -->
                <div class="p-6 sm:p-8 bg-neutral-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest block">Official Enrollment Receipt</span>
                        <h2 class="text-xl sm:text-2xl font-extrabold font-mono tracking-wider text-lb-orange mt-0.5">
                            {{ $enrollment->enrollment_code }}
                        </h2>
                    </div>

                    <div class="text-left sm:text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $enrollment->isActive() ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-neutral-900' }}">
                            {{ str_replace('_', ' ', $enrollment->status) }}
                        </span>
                    </div>
                </div>

                <!-- Receipt Body -->
                <div class="p-6 sm:p-8 space-y-6 text-xs sm:text-sm">
                    <!-- Course & Delivery Option Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pb-6 border-b border-neutral-100">
                        <div>
                            <span class="text-neutral-400 block text-xs uppercase tracking-wider mb-1">Enrolled Course:</span>
                            <h3 class="text-sm sm:text-base font-bold text-neutral-900 leading-snug">{{ $enrollment->course->title }}</h3>
                            <p class="text-xs text-lb-orange font-semibold mt-0.5">{{ $enrollment->course->category?->name }}</p>
                        </div>

                        <div>
                            <span class="text-neutral-400 block text-xs uppercase tracking-wider mb-1">Delivery Option:</span>
                            <span class="text-xs sm:text-sm font-bold text-neutral-900 block">{{ $enrollment->deliveryOption?->title }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase mt-1 {{ $enrollment->delivery_mode === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $enrollment->delivery_mode }}
                            </span>
                        </div>
                    </div>

                    <!-- Physical Batch Details (If applicable) -->
                    @if ($enrollment->batch)
                        <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                                    <i class="fas fa-chalkboard-teacher text-amber-700"></i>
                                    <span>Physical Classroom Batch Allocation</span>
                                </span>
                                <span class="text-[10px] font-mono font-bold bg-white px-2 py-0.5 rounded border border-amber-200 text-amber-800">
                                    {{ $enrollment->batch->batch_code }}
                                </span>
                            </div>

                            <p class="text-xs font-semibold text-neutral-800">{{ $enrollment->batch->batch_name }}</p>

                            @if ($enrollment->batch->location)
                                <p class="text-xs text-neutral-600">
                                    <i class="fas fa-map-marker-alt text-lb-orange mr-1"></i>
                                    <span>{{ $enrollment->batch->location->name }}, {{ $enrollment->batch->location->address_line_1 }}, {{ $enrollment->batch->location->city }} ({{ $enrollment->batch->location->district }} District)</span>
                                </p>
                            @endif

                            <div class="flex flex-wrap items-center gap-4 text-[11px] text-neutral-500 pt-1">
                                @if ($enrollment->batch->start_date)
                                    <span>Starts: <strong>{{ $enrollment->batch->start_date->format('d M Y') }}</strong></span>
                                @endif
                                @if ($enrollment->batch->end_date)
                                    <span>Ends: <strong>{{ $enrollment->batch->end_date->format('d M Y') }}</strong></span>
                                @endif
                                <span>Timezone: <strong>Asia/Colombo</strong></span>
                            </div>
                        </div>
                    @endif

                    <!-- Student & Timing Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-6 border-b border-neutral-100 text-xs">
                        <div>
                            <span class="text-neutral-400 block mb-0.5">Enrolled Student:</span>
                            <span class="font-bold text-neutral-900">{{ $enrollment->user->name }}</span>
                            <span class="text-neutral-500 block text-[11px]">{{ $enrollment->user->email }}</span>
                        </div>

                        <div>
                            <span class="text-neutral-400 block mb-0.5">Registration Timestamp:</span>
                            <span class="font-bold text-neutral-900">{{ $enrollment->created_at->setTimezone('Asia/Colombo')->format('d M Y, h:i A') }}</span>
                            <span class="text-neutral-500 block text-[11px]">Asia/Colombo</span>
                        </div>

                        <div>
                            <span class="text-neutral-400 block mb-0.5">Access Duration:</span>
                            <span class="font-bold text-neutral-900">
                                {{ $enrollment->deliveryOption?->access_period_days ? $enrollment->deliveryOption->access_period_days . ' Days' : 'Lifetime Access' }}
                            </span>
                        </div>
                    </div>

                    <!-- Payment Summary Row -->
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <span class="text-xs text-neutral-500 block">Total Tuition Amount:</span>
                            <span class="text-xs text-neutral-400">All local Sri Lankan taxes included</span>
                        </div>

                        <div class="text-right">
                            <span class="text-2xl font-extrabold text-lb-orange">
                                {{ $enrollment->formattedPrice() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Receipt Actions -->
                <div class="p-6 bg-neutral-50 border-t border-neutral-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    @if ($enrollment->status === \App\Models\Enrollment::STATUS_PENDING_PAYMENT)
                        <a
                            href="{{ route('checkout.show', ['course' => $enrollment->course->slug, 'option_id' => $enrollment->course_delivery_option_id]) }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-lb-orange hover:bg-lb-orange-hover text-white text-xs font-bold transition shadow-sm"
                        >
                            <i class="fas fa-credit-card"></i>
                            <span>Complete Payment via PayHere</span>
                        </a>
                    @else
                        <a
                            href="{{ route('student.dashboard') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-lb-orange hover:bg-lb-orange-hover text-white text-xs font-bold transition shadow-sm"
                        >
                            <i class="fas fa-graduation-cap"></i>
                            <span>Go to My Student Dashboard</span>
                        </a>
                    @endif

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a
                            href="{{ route('courses.show', $enrollment->course) }}"
                            class="flex-1 sm:flex-none text-center px-4 py-2 rounded-full bg-white border border-neutral-200 text-xs font-semibold text-neutral-700 hover:bg-neutral-100 transition"
                        >
                            Course Overview
                        </a>

                        <a
                            href="{{ route('courses.index') }}"
                            class="flex-1 sm:flex-none text-center px-4 py-2 rounded-full bg-white border border-neutral-200 text-xs font-semibold text-neutral-700 hover:bg-neutral-100 transition"
                        >
                            Explore Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
