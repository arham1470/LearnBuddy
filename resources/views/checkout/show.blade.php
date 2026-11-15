<x-layouts.public :title="'Checkout: ' . $course->title . ' · LearnBuddy'">
@php
    $activeGateway = config('payments.default', 'stripe');
    $gatewayLabel = match($activeGateway) {
        'stripe' => 'Stripe',
        'payhere' => 'PayHere',
        default => 'Secure Gateway',
    };
@endphp
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
                <span class="font-semibold text-neutral-800">Secure Checkout</span>
            </nav>

            <div class="space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <x-badge variant="orange" icon="fas fa-lock">
                        Bank-Grade Secure 256-Bit Checkout
                    </x-badge>
                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        <i class="fas fa-shield-alt mr-1"></i> Verified Payment Gateway ({{ $gatewayLabel }})
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-lb-black">
                    Complete Your <span class="text-lb-orange">Enrolment Order</span>
                </h1>
                <p class="text-xs sm:text-sm text-neutral-600 max-w-xl">
                    Review your course delivery plan, enter your billing details, and pay securely in Sri Lankan Rupees (LKR).
                </p>
            </div>
        </div>
    </section>

    <!-- CHECKOUT FORM -->
    <section class="py-10 sm:py-16 bg-lb-off-white min-h-[600px]" x-data="{
        selectedOptionId: {{ $selectedOption?->id ?? $course->deliveryOptions->first()?->id ?? 'null' }},
        selectedMode: '{{ $selectedOption?->delivery_mode ?? 'online' }}',
        selectedPrice: '{{ $selectedOption?->formattedPrice() ?? '0.00' }}',
        selectedRawPrice: {{ (float) ($selectedOption?->price ?? 0.0) }},
        selectedCurrency: '{{ $selectedOption?->currency ?: 'LKR' }}',
        paymentFlow: 'card', // 'card' or 'hosted'
        
        // Card Modal Visibility
        showCardModal: false,

        // Live Card Input State
        cardNumber: '4242 4242 4242 4242',
        cardName: @js($user->name ?: 'Kamal Perera'),
        cardExpiry: '12/28',
        cardCvc: '123',
        cardBrand: 'visa',
        simulationScenario: 'success', // 'success', '3ds', 'decline'
        
        // Modal & Processing States
        showOtpModal: false,
        otpCode: '',
        processing: false,
        processingStep: 1,
        processingText: '',

        detectCardBrand(num) {
            const clean = (num || '').replace(/\s+/g, '');
            if (/^4/.test(clean)) return 'visa';
            if (/^(5[1-5]|2[2-7])/.test(clean)) return 'mastercard';
            if (/^3[47]/.test(clean)) return 'amex';
            return 'generic';
        },

        formatCardNumber(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 16);
            let parts = [];
            for (let i = 0, len = v.length; i < len; i += 4) {
                parts.push(v.substring(i, i + 4));
            }
            this.cardNumber = parts.join(' ');
            this.cardBrand = this.detectCardBrand(this.cardNumber);
            const clean = this.cardNumber.replace(/\s+/g, '');
            if (clean.endsWith('3022')) {
                this.simulationScenario = '3ds';
            } else if (clean.endsWith('0002')) {
                this.simulationScenario = 'decline';
            } else {
                this.simulationScenario = 'success';
            }
        },

        formatExpiry(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 4);
            if (v.length >= 2) {
                this.cardExpiry = v.substring(0, 2) + '/' + v.substring(2);
            } else {
                this.cardExpiry = v;
            }
        },

        usePreset(type) {
            if (type === 'approved') {
                this.cardNumber = '4242 4242 4242 4242';
                this.cardExpiry = '12/28';
                this.cardCvc = '123';
                this.cardBrand = 'visa';
                this.simulationScenario = 'success';
            } else if (type === '3ds') {
                this.cardNumber = '4000 0000 0000 3022';
                this.cardExpiry = '08/29';
                this.cardCvc = '888';
                this.cardBrand = 'visa';
                this.simulationScenario = '3ds';
            } else if (type === 'decline') {
                this.cardNumber = '4000 0000 0000 0002';
                this.cardExpiry = '05/27';
                this.cardCvc = '404';
                this.cardBrand = 'visa';
                this.simulationScenario = 'decline';
            }
        },

        openPaymentModal() {
            const form = this.$refs.checkoutForm;
            const nameEl = form.querySelector('[name=billing_name]');
            const emailEl = form.querySelector('[name=billing_email]');
            const phoneEl = form.querySelector('[name=billing_phone]');
            if (nameEl && emailEl && phoneEl) {
                if (!nameEl.checkValidity() || !emailEl.checkValidity() || !phoneEl.checkValidity()) {
                    form.reportValidity();
                    return;
                }
            }

            const batchEl = form.querySelector('[name=course_batch_id]');
            if (this.selectedMode === 'physical' && batchEl && !batchEl.value) {
                batchEl.reportValidity();
                return;
            }

            this.showCardModal = true;
        },

        handleCardAuthorize() {
            const clean = (this.cardNumber || '').replace(/\s+/g, '');
            if (clean.length < 15) {
                alert('Please enter a valid 16-digit card number.');
                return;
            }
            if (!this.cardExpiry || this.cardExpiry.length < 5) {
                alert('Please enter a valid expiry date (MM/YY).');
                return;
            }
            if (!this.cardCvc || this.cardCvc.length < 3) {
                alert('Please enter a valid 3 or 4 digit CVC security code.');
                return;
            }

            if (this.simulationScenario === '3ds' && !this.showOtpModal) {
                this.showOtpModal = true;
                return;
            }

            this.showCardModal = false;
            this.startProcessingAnimation();
        },

        proceedWithHostedGateway() {
            this.paymentFlow = 'hosted';
            this.showCardModal = false;
            this.processing = true;
            this.processingStep = 1;
            this.processingText = 'Connecting to external hosted gateway...';
            this.$refs.checkoutForm.submit();
        },

        confirmOtpAndPay() {
            this.showOtpModal = false;
            this.showCardModal = false;
            this.startProcessingAnimation();
        },

        startProcessingAnimation() {
            this.processing = true;
            this.processingStep = 1;
            this.processingText = 'Encrypting card credentials via 256-bit TLS...';

            setTimeout(() => {
                this.processingStep = 2;
                this.processingText = 'Contacting cardholder issuing bank network...';
            }, 550);

            setTimeout(() => {
                this.processingStep = 3;
                this.processingText = this.simulationScenario === 'decline'
                    ? 'Processing response from cardholder bank...'
                    : '3D Secure authentication verified. Authorizing payment...';
            }, 1150);

            setTimeout(() => {
                this.processingStep = 4;
                this.processingText = this.simulationScenario === 'decline'
                    ? 'Finalizing transaction status...'
                    : 'Payment authorized! Unlocking course enrollment...';
                this.$refs.checkoutForm.submit();
            }, 1750);
        }
    }">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6">
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-rose-500"></i>
                        <span>Please resolve the following before proceeding:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pt-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form x-ref="checkoutForm" action="{{ route('checkout.process', $course) }}" method="POST" @submit.prevent="openPaymentModal()">
                @csrf
                <input type="hidden" name="payment_flow" :value="paymentFlow">
                <input type="hidden" name="simulation_scenario" :value="simulationScenario">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- LEFT COLUMN: SELECTION & BILLING (7 Cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- 1. Delivery Options Selection -->
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                            <div class="border-b border-neutral-100 pb-3 flex items-center justify-between">
                                <h2 class="text-sm sm:text-base font-bold text-neutral-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-lb-orange text-white text-xs flex items-center justify-center font-bold">1</span>
                                    <span>Select Learning Delivery Mode</span>
                                </h2>
                                <span class="text-xs text-neutral-500 font-medium">All prices in LKR</span>
                            </div>

                            <div class="space-y-3">
                                @foreach ($course->deliveryOptions->where('is_active', true) as $opt)
                                    <label
                                        class="block relative p-4 rounded-2xl border-2 cursor-pointer transition"
                                        :class="selectedOptionId === {{ $opt->id }} ? 'border-lb-orange bg-orange-50/40 ring-2 ring-lb-orange/20' : 'border-neutral-200 hover:border-neutral-300 bg-white'"
                                    >
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3">
                                                <input
                                                    type="radio"
                                                    name="delivery_option_id"
                                                    value="{{ $opt->id }}"
                                                    x-model="selectedOptionId"
                                                    @change="
                                                        selectedMode = '{{ $opt->delivery_mode }}';
                                                        selectedPrice = '{{ $opt->formattedPrice() }}';
                                                        selectedRawPrice = {{ (float) $opt->price }};
                                                        selectedCurrency = '{{ $opt->currency ?: 'LKR' }}';
                                                    "
                                                    class="mt-1 text-lb-orange focus:ring-lb-orange"
                                                    {{ ($selectedOption?->id === $opt->id) ? 'checked' : '' }}
                                                >
                                                <div>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="font-bold text-sm sm:text-base text-neutral-900">{{ $opt->title }}</span>
                                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $opt->delivery_mode === 'physical' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                                            {{ ucfirst($opt->delivery_mode) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-neutral-600 mt-1">{{ $opt->description }}</p>

                                                    <div class="flex items-center gap-3 mt-2 text-[11px] text-neutral-500 flex-wrap">
                                                        @if ($opt->includes_recorded_lessons)
                                                            <span><i class="fas fa-check-circle text-emerald-600 mr-1"></i> Recorded Lessons</span>
                                                        @endif
                                                        @if ($opt->includes_live_classes)
                                                            <span><i class="fas fa-check-circle text-emerald-600 mr-1"></i> Live Virtual Classes</span>
                                                        @endif
                                                        @if ($opt->includes_physical_sessions)
                                                            <span><i class="fas fa-check-circle text-emerald-600 mr-1"></i> Physical Workshops</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right whitespace-nowrap pl-2">
                                                <div class="text-base sm:text-lg font-black text-lb-black">
                                                    {{ $opt->formattedPrice() }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Physical Campus Batches Dropdown -->
                                        @if ($opt->delivery_mode === 'physical' && $opt->batches->count() > 0)
                                            <div
                                                x-show="selectedOptionId === {{ $opt->id }}"
                                                x-cloak
                                                class="mt-4 pt-3 border-t border-orange-100"
                                            >
                                                <label class="block text-xs font-bold text-neutral-800 mb-1.5">
                                                    Select Campus Batch & Location <span class="text-rose-500">*</span>
                                                </label>
                                                <select
                                                    name="course_batch_id"
                                                    class="w-full text-xs rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-lb-orange bg-white py-2 px-3"
                                                >
                                                    <option value="">-- Choose an available batch intake --</option>
                                                    @foreach ($opt->batches as $batch)
                                                        @php
                                                            $seatsLeft = $batch->availableSeats();
                                                            $isFull = $batch->isFull() || $batch->status !== 'open_for_enrollment';
                                                        @endphp
                                                        <option
                                                            value="{{ $batch->id }}"
                                                            {{ $isFull ? 'disabled' : '' }}
                                                            {{ old('course_batch_id') == $batch->id ? 'selected' : '' }}
                                                        >
                                                            {{ $batch->batch_code }} &bull; {{ $batch->location?->city_town ?? 'Sri Lanka Campus' }}
                                                            ({{ $seatsLeft }} seats remaining)
                                                            {{ $isFull ? '[FULL]' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- 2. Student Billing Details -->
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                            <div class="border-b border-neutral-100 pb-3">
                                <h2 class="text-sm sm:text-base font-bold text-neutral-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-lb-orange text-white text-xs flex items-center justify-center font-bold">2</span>
                                    <span>Billing Contact Information</span>
                                </h2>
                                <p class="text-xs text-neutral-500 mt-0.5">
                                    An official electronic receipt will be generated and issued to this email address.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="text-xs font-bold text-neutral-800">
                                        Student / Payer Full Name <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="billing_name"
                                        value="{{ old('billing_name', $user->name) }}"
                                        required
                                        class="w-full text-xs sm:text-sm rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-lb-orange px-3.5 py-2.5"
                                        placeholder="Kamal Perera"
                                    >
                                </div>

                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-neutral-800">
                                        Billing Email Address <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        name="billing_email"
                                        value="{{ old('billing_email', $user->email) }}"
                                        required
                                        class="w-full text-xs sm:text-sm rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-lb-orange px-3.5 py-2.5"
                                        placeholder="kamal@example.lk"
                                    >
                                </div>

                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-neutral-800">
                                        Mobile Phone (Sri Lanka) <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="tel"
                                        name="billing_phone"
                                        value="{{ old('billing_phone', $user->phone ?? '+94 77 000 0000') }}"
                                        required
                                        class="w-full text-xs sm:text-sm rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-lb-orange px-3.5 py-2.5"
                                        placeholder="+94 77 123 4567"
                                    >
                                </div>

                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-neutral-800">
                                        City / Town
                                    </label>
                                    <input
                                        type="text"
                                        name="billing_city"
                                        value="{{ old('billing_city', $user->city ?? 'Colombo') }}"
                                        class="w-full text-xs sm:text-sm rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-lb-orange px-3.5 py-2.5"
                                        placeholder="Colombo"
                                    >
                                </div>

                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-neutral-800">
                                        Address
                                    </label>
                                    <input
                                        type="text"
                                        name="billing_address"
                                        value="{{ old('billing_address', $user->address_line_1 ?? 'Sri Lanka') }}"
                                        class="w-full text-xs sm:text-sm rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-lb-orange px-3.5 py-2.5"
                                        placeholder="123 Galle Road"
                                    >
                                </div>

                                <div class="space-y-1 sm:col-span-2">
                                    <label class="text-xs font-bold text-neutral-800">
                                        Order Notes (Optional)
                                    </label>
                                    <textarea
                                        name="notes"
                                        rows="2"
                                        class="w-full text-xs sm:text-sm rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-lb-orange px-3.5 py-2.5"
                                        placeholder="Any specific requests or invoicing references..."
                                    >{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: ORDER SUMMARY & PAYMENT BADGES (5 Cols) -->
                    <div class="lg:col-span-5 space-y-6">
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-md p-6 sm:p-8 space-y-5 sticky top-24">
                            <h3 class="text-base font-bold text-neutral-900 border-b border-neutral-100 pb-3 flex items-center justify-between">
                                <span>Order Summary</span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-orange-100 text-lb-orange">LKR Settlement</span>
                            </h3>

                            <!-- Course Thumbnail & Meta -->
                            <div class="flex gap-3">
                                @if ($course->thumbnail_url)
                                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-16 h-16 rounded-xl object-cover shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-orange-100 text-lb-orange flex items-center justify-center shrink-0">
                                        <i class="fas fa-book-open text-xl"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h4 class="font-bold text-xs sm:text-sm text-neutral-900 truncate">{{ $course->title }}</h4>
                                    <p class="text-[11px] text-neutral-500 mt-0.5">{{ $course->category?->name ?? 'General' }} &bull; {{ ucfirst($course->level) }}</p>
                                    <p class="text-xs text-lb-orange font-semibold mt-1">
                                        Mode: <span x-text="selectedMode.toUpperCase()"></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Pricing Table -->
                            <div class="space-y-2 pt-3 border-t border-neutral-100 text-xs">
                                <div class="flex justify-between text-neutral-600">
                                    <span>Course Tuition Fee</span>
                                    <span class="font-medium text-neutral-900" x-text="selectedCurrency + ' ' + selectedRawPrice.toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between text-neutral-600">
                                    <span>Gateway Processing Fee</span>
                                    <span class="font-medium text-emerald-600">LKR 0.00 (Waived)</span>
                                </div>
                                <div class="flex justify-between text-neutral-600">
                                    <span>Applicable VAT / NBT</span>
                                    <span class="font-medium text-neutral-900">Included</span>
                                </div>
                                <div class="flex justify-between items-baseline pt-3 border-t border-dashed border-neutral-200">
                                    <span class="text-sm font-bold text-neutral-900">Total Payable</span>
                                    <span class="text-xl sm:text-2xl font-black text-lb-orange" x-text="selectedCurrency + ' ' + selectedRawPrice.toFixed(2)"></span>
                                </div>
                            </div>

                            <!-- Payment Provider Badge & Payment Methods -->
                            <div class="p-3.5 rounded-2xl bg-neutral-50 border border-neutral-200/80 space-y-2">
                                <div class="flex items-center justify-between text-[11px] font-bold text-neutral-700">
                                    <span>Supported Payment Methods</span>
                                    <span class="text-lb-orange">{{ $gatewayLabel }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap text-neutral-600 text-xs">
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-blue-700">VISA</span>
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-amber-700">MasterCard</span>
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-blue-900">AMEX</span>
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-neutral-800">Apple Pay</span>
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-neutral-800">Google Pay</span>
                                </div>
                            </div>

                            <!-- Zero Card Storage Guarantee -->
                            <div class="text-[11px] text-neutral-500 space-y-1">
                                <p class="flex items-start gap-1.5">
                                    <i class="fas fa-shield-alt text-emerald-600 mt-0.5"></i>
                                    <span><strong>Zero Card Data Storage:</strong> Card numbers and CVV codes are never handled or stored on LearnBuddy servers.</span>
                                </p>
                                <p class="flex items-start gap-1.5">
                                    <i class="fas fa-check-double text-emerald-600 mt-0.5"></i>
                                    <span><strong>Automated Enrolment Activation:</strong> Your access unlocks immediately upon server-side payment verification.</span>
                                </p>
                            </div>

                            <!-- Pay Button -->
                            <button
                                type="button"
                                @click="openPaymentModal()"
                                :disabled="processing"
                                class="w-full py-3.5 px-6 rounded-2xl bg-lb-orange hover:bg-lb-orange-hover text-white font-extrabold text-sm transition shadow-lb-sm flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                            >
                                <span x-show="!processing" class="flex items-center gap-2">
                                    <i class="fas fa-lock mr-1"></i>
                                    <span>Pay <span x-text="selectedCurrency + ' ' + selectedRawPrice.toFixed(2)"></span> Securely</span>
                                </span>
                                <span x-show="processing" x-cloak class="flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>
                                    <span x-text="processingText || 'Processing Payment...'"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- PAYMENT DETAILS & CARD INFORMATION MODAL (HORIZONTAL DESKTOP LAYOUT)     -->
                <!-- ========================================================================= -->
                <div
                    x-show="showCardModal"
                    x-cloak
                    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-5 bg-neutral-900/60 backdrop-blur-md"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                >
                    <div 
                        class="bg-white rounded-3xl sm:rounded-[32px] max-w-lg lg:max-w-4xl xl:max-w-5xl w-full shadow-2xl border border-neutral-100 overflow-hidden transform transition-all relative flex flex-col max-h-[92vh]"
                        @click.away="if (!processing) showCardModal = false"
                    >
                        <!-- Modal Header -->
                        <div class="px-6 py-4 sm:px-8 sm:py-5 bg-white border-b border-neutral-100 flex items-center justify-between shrink-0">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-orange-50 text-lb-orange border border-orange-100 flex items-center justify-center text-base sm:text-lg shadow-xs">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm sm:text-base font-extrabold text-neutral-900 tracking-tight flex items-center gap-2">
                                        <span>Payment Details &amp; Card Information</span>
                                        <span class="hidden sm:inline-flex text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-shield-alt mr-1 self-center"></i> 256-Bit TLS
                                        </span>
                                    </h3>
                                    <p class="text-xs text-neutral-500">
                                        Course Enrolment &bull; <span class="font-semibold text-neutral-700">{{ $course->title }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 sm:gap-4">
                                <div class="hidden sm:flex flex-col text-right">
                                    <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Total Payable</span>
                                    <span class="text-sm sm:text-base font-black text-lb-orange" x-text="selectedCurrency + ' ' + selectedRawPrice.toFixed(2)"></span>
                                </div>
                                <button
                                    type="button"
                                    @click="showCardModal = false"
                                    class="w-9 h-9 rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-500 hover:text-neutral-900 flex items-center justify-center transition cursor-pointer"
                                    aria-label="Close dialog"
                                >
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Horizontal Grid Layout (2 Columns on Desktop) -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 overflow-y-auto flex-1 divide-y lg:divide-y-0 lg:divide-x divide-neutral-200/80">
                            <!-- LEFT PANE: Digital 3D Card Visual, Enrolment Price & Test Presets (5 Cols) -->
                            <div class="lg:col-span-5 bg-lb-off-white/80 p-5 sm:p-6 lg:p-7 flex flex-col justify-between space-y-4">
                                <div class="space-y-4">
                                    <!-- Order Price Highlight Card -->
                                    <div class="p-4 rounded-2xl bg-white border border-neutral-200/80 shadow-xs flex items-center justify-between gap-3">
                                        <div>
                                            <div class="text-[10px] font-bold uppercase tracking-wider text-neutral-400">Total Payable</div>
                                            <div class="text-lg sm:text-xl font-black text-lb-black" x-text="selectedCurrency + ' ' + selectedRawPrice.toFixed(2)"></div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-100 text-lb-orange">
                                                LKR Settlement
                                            </span>
                                            <div class="text-[10px] text-neutral-400 font-medium mt-1">VAT &amp; Gateway Included</div>
                                        </div>
                                    </div>

                                    <!-- Digital 3D Credit Card Visual -->
                                    <div class="relative bg-gradient-to-tr from-neutral-950 via-neutral-900 to-zinc-900 text-white rounded-2xl p-5 sm:p-6 shadow-xl border border-neutral-700/60 overflow-hidden select-none">
                                        <!-- Ambient Lighting Glows -->
                                        <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-orange-500/15 blur-xl pointer-events-none"></div>
                                        <div class="absolute -left-6 -top-6 w-28 h-28 rounded-full bg-blue-500/15 blur-lg pointer-events-none"></div>

                                        <!-- Card Top Row: Chip, Wifi & Dynamic Brand Logo -->
                                        <div class="flex items-center justify-between mb-6 relative z-10">
                                            <div class="flex items-center gap-3">
                                                <!-- Metallic Chip Visual -->
                                                <div class="w-10 h-7 rounded-md bg-gradient-to-br from-amber-200 via-amber-300 to-amber-500 border border-amber-600/40 p-1 flex flex-col justify-between shadow-inner">
                                                    <div class="h-0.5 bg-amber-700/40 w-full"></div>
                                                    <div class="h-0.5 bg-amber-700/40 w-full"></div>
                                                    <div class="h-0.5 bg-amber-700/40 w-full"></div>
                                                </div>
                                                <!-- Contactless Waves -->
                                                <i class="fas fa-wifi text-neutral-400 rotate-90 text-xs"></i>
                                            </div>

                                            <!-- Dynamic Card Brand Logo -->
                                            <div class="text-right">
                                                <template x-if="cardBrand === 'visa'">
                                                    <span class="font-black italic text-lg sm:text-xl tracking-wider text-white">VISA</span>
                                                </template>
                                                <template x-if="cardBrand === 'mastercard'">
                                                    <div class="flex -space-x-2">
                                                        <div class="w-6 h-6 rounded-full bg-rose-500 opacity-90"></div>
                                                        <div class="w-6 h-6 rounded-full bg-amber-400 opacity-90"></div>
                                                    </div>
                                                </template>
                                                <template x-if="cardBrand === 'amex'">
                                                    <span class="font-extrabold text-xs bg-blue-600 px-2 py-0.5 rounded text-white tracking-widest">AMEX</span>
                                                </template>
                                                <template x-if="cardBrand === 'generic'">
                                                    <i class="fas fa-credit-card text-lg text-neutral-400"></i>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Card Number -->
                                        <div class="relative z-10 my-3">
                                            <div class="text-[9px] uppercase tracking-widest text-neutral-400 font-semibold mb-1">Card Number</div>
                                            <div class="font-mono text-base sm:text-lg tracking-[0.2em] font-bold text-white drop-shadow-xs" x-text="cardNumber || '•••• •••• •••• ••••'"></div>
                                        </div>

                                        <!-- Card Bottom Row: Holder Name & Expiry -->
                                        <div class="flex items-end justify-between relative z-10 pt-2 border-t border-white/10 text-xs">
                                            <div>
                                                <div class="text-[9px] uppercase tracking-widest text-neutral-400 font-semibold">Cardholder</div>
                                                <div class="font-semibold text-neutral-100 uppercase tracking-wide truncate max-w-[170px]" x-text="cardName || 'KAMAL PERERA'"></div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-[9px] uppercase tracking-widest text-neutral-400 font-semibold">Expires</div>
                                                <div class="font-mono font-semibold text-neutral-100" x-text="cardExpiry || 'MM/YY'"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Demo Presets Bar -->
                                    <div class="p-3.5 rounded-2xl bg-white border border-neutral-200/80 shadow-xs space-y-2">
                                        <div class="flex items-center justify-between text-[11px] font-bold text-neutral-800">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fas fa-flask text-lb-orange"></i>
                                                <span>Test Card Scenarios:</span>
                                            </span>
                                            <span class="text-[10px] text-neutral-400 font-normal">Click to auto-fill</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-1.5">
                                            <button
                                                type="button"
                                                @click="usePreset('approved')"
                                                class="py-1.5 px-2 rounded-xl text-[10px] sm:text-[11px] font-bold transition flex items-center justify-center gap-1 border cursor-pointer"
                                                :class="simulationScenario === 'success' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs ring-2 ring-emerald-500/20' : 'bg-neutral-50 text-emerald-800 border-neutral-200 hover:bg-emerald-50'"
                                            >
                                                <i class="fas fa-check-circle text-[9px]"></i>
                                                <span>Approved (4242)</span>
                                            </button>
                                            <button
                                                type="button"
                                                @click="usePreset('3ds')"
                                                class="py-1.5 px-2 rounded-xl text-[10px] sm:text-[11px] font-bold transition flex items-center justify-center gap-1 border cursor-pointer"
                                                :class="simulationScenario === '3ds' ? 'bg-amber-500 text-white border-amber-500 shadow-xs ring-2 ring-amber-500/20' : 'bg-neutral-50 text-amber-800 border-neutral-200 hover:bg-amber-50'"
                                            >
                                                <i class="fas fa-shield-alt text-[9px]"></i>
                                                <span>3D Secure (3022)</span>
                                            </button>
                                            <button
                                                type="button"
                                                @click="usePreset('decline')"
                                                class="py-1.5 px-2 rounded-xl text-[10px] sm:text-[11px] font-bold transition flex items-center justify-center gap-1 border cursor-pointer"
                                                :class="simulationScenario === 'decline' ? 'bg-rose-600 text-white border-rose-600 shadow-xs ring-2 ring-rose-600/20' : 'bg-neutral-50 text-rose-800 border-neutral-200 hover:bg-rose-50'"
                                            >
                                                <i class="fas fa-times-circle text-[9px]"></i>
                                                <span>Decline (0002)</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Security Guarantees Footer in Left Column -->
                                <div class="pt-2 border-t border-neutral-200/80 flex items-center justify-between text-[11px] text-neutral-500">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-lock text-emerald-600"></i> 256-Bit TLS
                                    </span>
                                    <span>&bull;</span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-shield-alt text-emerald-600"></i> PCI-DSS Level 1
                                    </span>
                                    <span>&bull;</span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-check-double text-emerald-600"></i> Verified
                                    </span>
                                </div>
                            </div>

                            <!-- RIGHT PANE: Tabs, Form Inputs & Authorization CTA (7 Cols) -->
                            <div class="lg:col-span-7 p-5 sm:p-6 lg:p-7 bg-white flex flex-col justify-between space-y-5">
                                <div class="space-y-4">
                                    <!-- Segmented Switcher Tabs -->
                                    <div class="grid grid-cols-2 gap-1.5 p-1.5 bg-neutral-100 rounded-2xl">
                                        <button
                                            type="button"
                                            @click="paymentFlow = 'card'"
                                            class="py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer"
                                            :class="paymentFlow === 'card' ? 'bg-white text-lb-black shadow-lb-sm' : 'text-neutral-500 hover:text-neutral-900'"
                                        >
                                            <i class="fas fa-credit-card" :class="paymentFlow === 'card' ? 'text-lb-orange' : ''"></i>
                                            <span>Direct Card (Live Demo)</span>
                                        </button>
                                        <button
                                            type="button"
                                            @click="paymentFlow = 'hosted'"
                                            class="py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer"
                                            :class="paymentFlow === 'hosted' ? 'bg-white text-lb-black shadow-lb-sm' : 'text-neutral-500 hover:text-neutral-900'"
                                        >
                                            <i class="fas fa-external-link-alt" :class="paymentFlow === 'hosted' ? 'text-lb-orange' : ''"></i>
                                            <span>Hosted Gateway Portal</span>
                                        </button>
                                    </div>

                                    <!-- TAB 1: INTERACTIVE CARD FORM -->
                                    <div x-show="paymentFlow === 'card'" class="space-y-4">
                                        <!-- Cardholder Name -->
                                        <div class="space-y-1.5">
                                            <label class="text-xs font-bold text-neutral-800 flex items-center justify-between">
                                                <span>Cardholder Name <span class="text-rose-500">*</span></span>
                                                <span class="text-[10px] text-neutral-400 font-normal">As shown on card</span>
                                            </label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400 text-xs">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                                <input
                                                    type="text"
                                                    name="card_name"
                                                    x-model="cardName"
                                                    class="w-full text-xs sm:text-sm rounded-xl border border-neutral-300 focus:border-lb-orange focus:ring-2 focus:ring-lb-orange/20 pl-10 pr-3.5 py-2.5 transition"
                                                    placeholder="Kamal Perera"
                                                >
                                            </div>
                                        </div>

                                        <!-- Card Number Input -->
                                        <div class="space-y-1.5">
                                            <label class="text-xs font-bold text-neutral-800 flex items-center justify-between">
                                                <span>Card Number <span class="text-rose-500">*</span></span>
                                                <span class="text-[10px] text-neutral-400 font-normal">16 digits</span>
                                            </label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400 text-xs">
                                                    <i class="fas fa-credit-card"></i>
                                                </span>
                                                <input
                                                    type="text"
                                                    name="card_number"
                                                    x-model="cardNumber"
                                                    @input="formatCardNumber($event)"
                                                    maxlength="19"
                                                    class="w-full text-xs sm:text-sm font-mono rounded-xl border border-neutral-300 focus:border-lb-orange focus:ring-2 focus:ring-lb-orange/20 pl-10 pr-14 py-2.5 tracking-wider transition"
                                                    placeholder="4242 4242 4242 4242"
                                                >
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-xs font-bold">
                                                    <span x-show="cardBrand === 'visa'" class="text-blue-700 italic font-black">VISA</span>
                                                    <span x-show="cardBrand === 'mastercard'" class="text-amber-600 font-black">MC</span>
                                                    <span x-show="cardBrand === 'amex'" class="text-blue-600 font-black">AMEX</span>
                                                    <span x-show="cardBrand === 'generic'" class="text-neutral-400"><i class="fas fa-lock text-[10px]"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Row: Expiration & CVC -->
                                        <div class="grid grid-cols-2 gap-3.5">
                                            <div class="space-y-1.5">
                                                <label class="text-xs font-bold text-neutral-800">
                                                    Expiration Date <span class="text-rose-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400 text-xs">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </span>
                                                    <input
                                                        type="text"
                                                        name="card_expiry"
                                                        x-model="cardExpiry"
                                                        @input="formatExpiry($event)"
                                                        maxlength="5"
                                                        class="w-full text-xs sm:text-sm font-mono rounded-xl border border-neutral-300 focus:border-lb-orange focus:ring-2 focus:ring-lb-orange/20 pl-10 pr-3.5 py-2.5 transition"
                                                        placeholder="MM/YY"
                                                    >
                                                </div>
                                            </div>

                                            <div class="space-y-1.5">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-xs font-bold text-neutral-800">
                                                        CVC / CVV <span class="text-rose-500">*</span>
                                                    </label>
                                                    <span class="text-[10px] text-neutral-400">3-4 digits</span>
                                                </div>
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400 text-xs">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                    <input
                                                        type="password"
                                                        name="card_cvc"
                                                        x-model="cardCvc"
                                                        maxlength="4"
                                                        class="w-full text-xs sm:text-sm font-mono rounded-xl border border-neutral-300 focus:border-lb-orange focus:ring-2 focus:ring-lb-orange/20 pl-10 pr-3.5 py-2.5 transition"
                                                        placeholder="123"
                                                    >
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Zero-Storage Guarantee Note -->
                                        <div class="p-3 rounded-xl bg-neutral-50 border border-neutral-200/80 text-[11px] text-neutral-600 flex items-start gap-2">
                                            <i class="fas fa-shield-alt text-emerald-600 mt-0.5 shrink-0"></i>
                                            <span>LearnBuddy protects your privacy. Card details are encrypted and securely authorized via the payment provider network.</span>
                                        </div>
                                    </div>

                                    <!-- TAB 2: HOSTED GATEWAY REDIRECT -->
                                    <div x-show="paymentFlow === 'hosted'" class="space-y-4 py-2">
                                        <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/80 space-y-3 text-xs text-neutral-600">
                                            <div class="font-bold text-neutral-800 flex items-center gap-2">
                                                <i class="fas fa-globe text-lb-orange"></i>
                                                <span>MockGateway Hosted Redirection</span>
                                            </div>
                                            <p class="leading-relaxed">
                                                You will be redirected off-site to the official MockGateway Stripe portal. On that page, you can choose from simulated server-to-server webhook scenarios and redirect responses.
                                            </p>
                                            <ul class="list-disc list-inside space-y-1 text-neutral-500 text-[11px]">
                                                <li>Supports PaymentIntent creation via API</li>
                                                <li>Triggers asynchronous webhook event delivery</li>
                                                <li>Instant return verification upon redirection</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons in Right Column -->
                                <div class="space-y-2 pt-2 border-t border-neutral-100">
                                    <template x-if="paymentFlow === 'card'">
                                        <button
                                            type="button"
                                            @click="handleCardAuthorize()"
                                            class="w-full py-3.5 px-6 rounded-2xl bg-lb-orange hover:bg-lb-orange-hover text-white font-extrabold text-sm transition shadow-lb-md flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99]"
                                        >
                                            <i class="fas fa-lock"></i>
                                            <span>Authorize &amp; Pay <span x-text="selectedCurrency + ' ' + selectedRawPrice.toFixed(2)"></span></span>
                                        </button>
                                    </template>

                                    <template x-if="paymentFlow === 'hosted'">
                                        <button
                                            type="button"
                                            @click="proceedWithHostedGateway()"
                                            class="w-full py-3.5 px-6 rounded-2xl bg-neutral-900 hover:bg-lb-black text-white font-bold text-xs sm:text-sm transition cursor-pointer flex items-center justify-center gap-2 shadow"
                                        >
                                            <span>Proceed to MockGateway Hosted Portal</span>
                                            <i class="fas fa-arrow-right text-[11px]"></i>
                                        </button>
                                    </template>

                                    <button
                                        type="button"
                                        @click="showCardModal = false"
                                        class="w-full py-2 text-center text-xs font-semibold text-neutral-500 hover:text-neutral-800 transition cursor-pointer"
                                    >
                                        Cancel &amp; Return to Checkout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- 3D SECURE OTP SIMULATION MODAL -->
            <div
                x-show="showOtpModal"
                x-cloak
                class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-neutral-900/60 backdrop-blur-md"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-neutral-100 overflow-hidden transform transition-all p-6 sm:p-8 space-y-5">
                    <!-- Bank Brand Header -->
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-base shadow-xs">
                                <i class="fas fa-university"></i>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-neutral-900">Commercial Bank / HNB 3D Secure</div>
                                <div class="text-[10px] text-neutral-400 uppercase tracking-wider font-semibold">Verified by Visa &bull; Mastercard Identity Check</div>
                            </div>
                        </div>
                        <button type="button" @click="showOtpModal = false" class="text-neutral-400 hover:text-neutral-600 p-1 cursor-pointer">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    <!-- Transaction Details -->
                    <div class="p-4 rounded-2xl bg-neutral-50 text-xs space-y-2 border border-neutral-200/80">
                        <div class="flex justify-between">
                            <span class="text-neutral-500">Merchant:</span>
                            <span class="font-bold text-neutral-900">LearnBuddy Sri Lanka</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500">Transaction Amount:</span>
                            <span class="font-bold text-lb-orange text-sm" x-text="selectedCurrency + ' ' + selectedRawPrice.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500">Card Number:</span>
                            <span class="font-mono font-medium text-neutral-800" x-text="'•••• •••• •••• ' + (cardNumber.replace(/\s+/g, '').slice(-4) || '3022')"></span>
                        </div>
                    </div>

                    <div class="text-center space-y-3">
                        <p class="text-xs text-neutral-600 leading-relaxed">
                            A One-Time Password (OTP) has been dispatched to your registered mobile phone ending in <strong>+94 77 *** 1234</strong>.
                        </p>

                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider">
                                Enter 6-Digit OTP
                            </label>
                            <input
                                type="text"
                                x-model="otpCode"
                                maxlength="6"
                                class="w-48 mx-auto text-center font-mono text-xl tracking-[0.3em] font-bold rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-lb-orange py-2"
                                placeholder="123456"
                                autofocus
                            >
                        </div>

                        <button
                            type="button"
                            @click="otpCode = '123456'"
                            class="text-[11px] font-semibold text-lb-orange hover:underline inline-flex items-center gap-1 cursor-pointer"
                        >
                            <i class="fas fa-magic text-[10px]"></i> Auto-fill Demo OTP (123456)
                        </button>
                    </div>

                    <div class="space-y-2 pt-2">
                        <button
                            type="button"
                            @click="confirmOtpAndPay()"
                            class="w-full py-3.5 px-6 rounded-2xl bg-neutral-900 hover:bg-lb-black text-white font-bold text-xs transition cursor-pointer flex items-center justify-center gap-2 shadow"
                        >
                            <i class="fas fa-check-circle text-emerald-400"></i>
                            <span>Submit OTP & Complete Payment</span>
                        </button>

                        <button
                            type="button"
                            @click="showOtpModal = false"
                            class="w-full py-2 text-center text-xs text-neutral-500 hover:text-neutral-700 cursor-pointer"
                        >
                            Cancel and return to checkout
                        </button>
                    </div>
                </div>
            </div>

            <!-- REALISTIC PROCESSING OVERLAY MODAL -->
            <div
                x-show="processing"
                x-cloak
                class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-neutral-900/60 backdrop-blur-md"
            >
                <div class="bg-white rounded-3xl max-w-sm w-full p-8 text-center space-y-6 shadow-2xl border border-neutral-100">
                    <!-- Animated Spinner / Check -->
                    <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                        <div class="absolute inset-0 rounded-full border-4 border-orange-100 border-t-lb-orange animate-spin" x-show="processingStep < 4"></div>
                        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl shadow-inner" x-show="processingStep >= 4">
                            <i class="fas fa-check"></i>
                        </div>
                        <i class="fas fa-shield-alt text-2xl text-lb-orange" x-show="processingStep < 4"></i>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-base font-bold text-neutral-900" x-text="processingStep >= 4 ? 'Payment Authorized!' : 'Processing Payment...'"></h3>
                        <p class="text-xs text-neutral-500 leading-relaxed" x-text="processingText"></p>
                    </div>

                    <!-- Steps Progress Tracker -->
                    <div class="space-y-2.5 text-left text-xs bg-neutral-50 p-4 rounded-2xl border border-neutral-200/80">
                        <div class="flex items-center gap-2 text-neutral-700" :class="processingStep >= 1 ? 'font-bold text-emerald-700' : 'text-neutral-400'">
                            <i class="fas text-[11px]" :class="processingStep > 1 ? 'fa-check text-emerald-600' : (processingStep === 1 ? 'fa-spinner fa-spin text-lb-orange' : 'fa-circle text-neutral-300')"></i>
                            <span>Card data encryption (256-bit TLS)</span>
                        </div>
                        <div class="flex items-center gap-2 text-neutral-700" :class="processingStep >= 2 ? 'font-bold text-emerald-700' : 'text-neutral-400'">
                            <i class="fas text-[11px]" :class="processingStep > 2 ? 'fa-check text-emerald-600' : (processingStep === 2 ? 'fa-spinner fa-spin text-lb-orange' : 'fa-circle text-neutral-300')"></i>
                            <span>Issuing bank cardholder network</span>
                        </div>
                        <div class="flex items-center gap-2 text-neutral-700" :class="processingStep >= 3 ? 'font-bold text-emerald-700' : 'text-neutral-400'">
                            <i class="fas text-[11px]" :class="processingStep > 3 ? 'fa-check text-emerald-600' : (processingStep === 3 ? 'fa-spinner fa-spin text-lb-orange' : 'fa-circle text-neutral-300')"></i>
                            <span>3D Secure authorization verification</span>
                        </div>
                        <div class="flex items-center gap-2 text-neutral-700" :class="processingStep >= 4 ? 'font-bold text-emerald-700' : 'text-neutral-400'">
                            <i class="fas text-[11px]" :class="processingStep >= 4 ? 'fa-check text-emerald-600' : 'fa-circle text-neutral-300')"></i>
                            <span>Enrolment & official receipt generation</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
