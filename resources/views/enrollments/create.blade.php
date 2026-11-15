<x-layouts.public :title="'Enroll: ' . $course->title . ' · LearnBuddy'">
    <!-- CHECKOUT HEADER -->
    <section class="bg-lb-peach pt-8 sm:pt-12 pb-10 sm:pb-14 rounded-b-[32px] sm:rounded-b-[48px] border-b border-orange-100">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6">
            <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-lb-orange transition">Home</a>
                <span class="text-neutral-300">/</span>
                <a href="{{ route('courses.index') }}" class="hover:text-lb-orange transition">Courses</a>
                <span class="text-neutral-300">/</span>
                <a href="{{ route('courses.show', $course) }}" class="hover:text-lb-orange transition truncate max-w-xs">{{ $course->title }}</a>
                <span class="text-neutral-300">/</span>
                <span class="font-semibold text-neutral-800">Enrollment Checkout</span>
            </nav>

            <div class="space-y-2">
                <x-badge variant="orange" icon="fas fa-user-graduate">
                    Step 1 of 2 &bull; Enrollment Confirmation
                </x-badge>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-lb-black">
                    Confirm Course <span class="text-lb-orange">Enrollment</span>
                </h1>
                <p class="text-xs sm:text-sm text-neutral-600 max-w-xl">
                    Review your delivery option, campus batch schedule, and student profile before finalizing your seat reservation.
                </p>
            </div>
        </div>
    </section>

    <!-- CHECKOUT FORM -->
    <section class="py-10 sm:py-16 bg-lb-off-white min-h-[600px]" x-data="{
        selectedOptionId: {{ $selectedOption?->id ?? $course->deliveryOptions->first()?->id ?? 'null' }},
        selectedMode: '{{ $selectedOption?->delivery_mode ?? $course->deliveryOptions->first()?->delivery_mode ?? 'online' }}',
        selectedPrice: '{{ $selectedOption?->formattedPrice() ?? $course->deliveryOptions->first()?->formattedPrice() ?? 'Free' }}',
        isFree: {{ ($selectedOption?->is_free || (float) $selectedOption?->price === 0.0) ? 'true' : 'false' }}
    }">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6">
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-rose-500"></i>
                        <span>Please correct the following errors:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pt-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('enrollments.store', $course) }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- LEFT COLUMN: SELECTION & STUDENT INFO (7 Cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- 1. Delivery Options Selection -->
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                            <div class="border-b border-neutral-100 pb-3">
                                <h2 class="text-sm sm:text-base font-bold text-neutral-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-lb-orange text-white text-xs flex items-center justify-center font-bold">1</span>
                                    <span>Choose Delivery Option &amp; Pricing</span>
                                </h2>
                                <p class="text-xs text-neutral-500 mt-1">Select your preferred format for this course.</p>
                            </div>

                            <div class="space-y-3">
                                @foreach ($course->deliveryOptions as $opt)
                                    @php $optIsFree = $opt->is_free || (float)$opt->price === 0.0; @endphp
                                    <label
                                        class="p-4 rounded-2xl border-2 transition cursor-pointer flex items-start justify-between gap-4 block"
                                        :class="selectedOptionId === {{ $opt->id }} ? 'border-lb-orange bg-orange-50/20 shadow-sm' : 'border-neutral-200 bg-neutral-50/40 hover:border-neutral-300'"
                                        @click="
                                            selectedOptionId = {{ $opt->id }};
                                            selectedMode = '{{ $opt->delivery_mode }}';
                                            selectedPrice = '{{ $opt->formattedPrice() }}';
                                            isFree = {{ $optIsFree ? 'true' : 'false' }};
                                        "
                                    >
                                        <div class="flex items-start gap-3">
                                            <input
                                                type="radio"
                                                name="delivery_option_id"
                                                value="{{ $opt->id }}"
                                                class="mt-1 text-lb-orange focus:ring-lb-orange"
                                                x-model="selectedOptionId"
                                                :value="{{ $opt->id }}"
                                                required
                                            >
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-neutral-900">{{ $opt->title }}</span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $opt->delivery_mode === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                                        {{ $opt->delivery_mode }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-neutral-500 leading-relaxed">{{ $opt->description }}</p>

                                                <div class="flex flex-wrap items-center gap-3 text-[11px] text-neutral-500 pt-1">
                                                    @if ($opt->includes_recorded_lessons)
                                                        <span><i class="fas fa-check text-emerald-500 mr-1"></i> Recorded Videos</span>
                                                    @endif
                                                    @if ($opt->includes_physical_sessions)
                                                        <span><i class="fas fa-check text-emerald-500 mr-1"></i> Campus Labs</span>
                                                    @endif
                                                    @if ($opt->includes_certificate)
                                                        <span><i class="fas fa-check text-emerald-500 mr-1"></i> Certificate</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <span class="text-sm font-extrabold text-neutral-900 block">
                                                {{ $opt->formattedPrice() }}
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- 2. Physical Classroom Batches (shown when physical option selected) -->
                        @foreach ($course->deliveryOptions->where('delivery_mode', 'physical') as $pOpt)
                            <div
                                x-show="selectedOptionId === {{ $pOpt->id }}"
                                x-cloak
                                class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4"
                            >
                                <div class="border-b border-neutral-100 pb-3">
                                    <h2 class="text-sm sm:text-base font-bold text-neutral-900 flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-lb-orange text-white text-xs flex items-center justify-center font-bold">2</span>
                                        <span>Select Physical Campus Batch Intake</span>
                                    </h2>
                                    <p class="text-xs text-neutral-500 mt-1">
                                        Choose your campus location and laboratory intake. Sri Lankan schedules adhere to Asia/Colombo local time.
                                    </p>
                                </div>

                                @if ($pOpt->batches->isEmpty())
                                    <div class="p-6 text-center bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-800">
                                        There are currently no open physical batches for this option. Please select an online option or check back for upcoming intakes.
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        @foreach ($pOpt->batches as $batch)
                                            @php $isFull = $batch->isFull(); @endphp
                                            <label
                                                class="p-4 rounded-2xl border-2 transition block {{ $isFull ? 'opacity-60 bg-neutral-100 border-neutral-200 cursor-not-allowed' : 'border-neutral-200 bg-neutral-50/50 hover:border-neutral-300 cursor-pointer' }}"
                                            >
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="flex items-start gap-3">
                                                        <input
                                                            type="radio"
                                                            name="course_batch_id"
                                                            value="{{ $batch->id }}"
                                                            class="mt-1 text-lb-orange focus:ring-lb-orange"
                                                            {{ $isFull ? 'disabled' : '' }}
                                                        >
                                                        <div class="space-y-1">
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-xs font-bold text-neutral-900">{{ $batch->batch_name }}</span>
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $isFull ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                                                    {{ $isFull ? 'Batch Full' : 'Open for Intake' }}
                                                                </span>
                                                            </div>

                                                            @if ($batch->location)
                                                                <p class="text-xs text-neutral-600">
                                                                    <i class="fas fa-map-marker-alt text-lb-orange mr-1"></i>
                                                                    <span>{{ $batch->location->name }}, {{ $batch->location->city }} ({{ $batch->location->district }} District)</span>
                                                                </p>
                                                            @endif

                                                            <div class="flex flex-wrap items-center gap-3 text-[11px] text-neutral-500 pt-1">
                                                                @if ($batch->start_date)
                                                                    <span><i class="far fa-calendar-alt text-neutral-400 mr-1"></i> Starts: {{ $batch->start_date->format('d M Y') }}</span>
                                                                @endif
                                                                <span><i class="fas fa-users text-neutral-400 mr-1"></i> Available Seats: <strong>{{ $batch->availableSeats() }}</strong> of {{ $batch->max_seats }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <span class="text-[10px] font-mono font-bold text-neutral-500 uppercase px-2 py-1 rounded bg-white border border-neutral-200 shrink-0">
                                                        {{ $batch->batch_code }}
                                                    </span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <!-- 3. Student Profile Confirmation -->
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                            <div class="border-b border-neutral-100 pb-3">
                                <h2 class="text-sm sm:text-base font-bold text-neutral-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-lb-orange text-white text-xs flex items-center justify-center font-bold">3</span>
                                    <span>Verified Student Information</span>
                                </h2>
                                <p class="text-xs text-neutral-500 mt-1">This enrollment will be issued to your authenticated account.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div class="p-3 bg-neutral-50 rounded-xl border border-neutral-100">
                                    <span class="text-neutral-400 block mb-0.5">Student Full Name:</span>
                                    <span class="font-bold text-neutral-900">{{ auth()->user()->name }}</span>
                                </div>
                                <div class="p-3 bg-neutral-50 rounded-xl border border-neutral-100">
                                    <span class="text-neutral-400 block mb-0.5">Email Address:</span>
                                    <span class="font-bold text-neutral-900">{{ auth()->user()->email }}</span>
                                </div>
                                <div class="p-3 bg-neutral-50 rounded-xl border border-neutral-100">
                                    <span class="text-neutral-400 block mb-0.5">Phone Number:</span>
                                    <span class="font-bold text-neutral-900">{{ auth()->user()->phone ?: 'Not provided (+94)' }}</span>
                                </div>
                                <div class="p-3 bg-neutral-50 rounded-xl border border-neutral-100">
                                    <span class="text-neutral-400 block mb-0.5">Sri Lanka District:</span>
                                    <span class="font-bold text-neutral-900">{{ auth()->user()->district ? auth()->user()->district . ' District' : 'Western / Not set' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: ORDER SUMMARY & CONFIRMATION (5 Cols) -->
                    <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-md p-6 sm:p-8 space-y-6">
                            <h2 class="text-base font-bold text-neutral-900 border-b border-neutral-100 pb-3">
                                Enrollment Summary
                            </h2>

                            <!-- Course Mini Card -->
                            <div class="flex items-center gap-3 pb-4 border-b border-neutral-100">
                                <div class="w-14 h-14 rounded-xl bg-neutral-100 flex items-center justify-center text-lb-orange shrink-0 overflow-hidden border border-neutral-200">
                                    @if ($course->thumbnail_url)
                                        <img
                                            src="{{ $course->thumbnail_url }}"
                                            alt="{{ $course->title }}"
                                            class="w-full h-full object-cover"
                                            onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';"
                                        >
                                        <div class="w-full h-full items-center justify-center bg-gradient-to-tr from-orange-100 to-neutral-100 hidden">
                                            <i class="fas fa-graduation-cap text-2xl text-lb-orange/40"></i>
                                        </div>
                                    @else
                                        <i class="{{ $course->category?->icon ?? 'fas fa-graduation-cap' }} text-xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider block">Course</span>
                                    <h3 class="text-xs sm:text-sm font-bold text-neutral-900 line-clamp-2 leading-snug">{{ $course->title }}</h3>
                                    <span class="text-[11px] text-lb-orange font-semibold">{{ $course->category?->name }}</span>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="space-y-2.5 text-xs text-neutral-600 border-b border-neutral-100 pb-4">
                                <div class="flex justify-between items-center">
                                    <span>Selected Option Fee:</span>
                                    <span class="font-bold text-neutral-900" x-text="selectedPrice"></span>
                                </div>
                                <div class="flex justify-between items-center text-neutral-500">
                                    <span>Platform &amp; Lab Access:</span>
                                    <span class="font-medium text-emerald-600">Included</span>
                                </div>
                                <div class="flex justify-between items-center text-neutral-500">
                                    <span>Sri Lankan Taxes:</span>
                                    <span>Included</span>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-neutral-900">Total Payable:</span>
                                <span class="text-2xl font-extrabold text-lb-orange" x-text="selectedPrice"></span>
                            </div>

                            <!-- Free vs Paid Notice -->
                            <div class="p-3.5 rounded-2xl text-xs space-y-1.5" :class="isFree ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-orange-50 border border-orange-200 text-orange-900'">
                                <div class="font-bold flex items-center gap-1.5">
                                    <i class="fas" :class="isFree ? 'fa-check-circle text-emerald-600' : 'fa-info-circle text-lb-orange'"></i>
                                    <span x-text="isFree ? 'Instant Free Activation' : 'Seat Reservation (Pending Fee Verification)'"></span>
                                </div>
                                <p class="text-[11px] leading-relaxed" x-text="isFree ? 'This is a free course. Your enrollment will activate immediately in your Student Dashboard.' : 'This course has a tuition fee. Your seat will be reserved and appear as Pending Payment in your Student Dashboard until payment processing is verified in Phase 13.'"></p>
                            </div>

                            <!-- Submit Action -->
                            <button
                                type="submit"
                                class="w-full py-3.5 px-6 rounded-full bg-lb-orange hover:bg-lb-orange-hover text-white text-sm font-bold transition shadow-md hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <i class="fas fa-lock text-xs" aria-hidden="true"></i>
                                <span x-text="isFree ? 'Confirm Free Enrollment' : 'Confirm & Reserve Seat'"></span>
                            </button>

                            <p class="text-[10px] text-center text-neutral-400">
                                Secure server-side validation &bull; Concurrency-protected batch capacity
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</x-layouts.public>
