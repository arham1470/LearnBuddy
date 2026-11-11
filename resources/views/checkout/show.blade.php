<x-layouts.public :title="'Checkout: ' . $course->title . ' · LearnBuddy'">
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
                        <i class="fas fa-shield-alt mr-1"></i> CBSL Approved Payment Gateway (PayHere)
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
        submitting: false
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

            <form action="{{ route('checkout.process', $course) }}" method="POST" @submit="submitting = true">
                @csrf

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
                                    <span>Supported Sri Lankan Payment Methods</span>
                                    <span class="text-lb-orange">PayHere</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap text-neutral-600 text-xs">
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-blue-700">VISA</span>
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-amber-700">MasterCard</span>
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-blue-900">AMEX</span>
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-red-600">eZ Cash</span>
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-yellow-600">mCash</span>
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-purple-700">Genie</span>
                                    <span class="px-2 py-1 bg-white rounded-md border border-neutral-200 font-bold text-[10px] text-emerald-700">Sampath Vishwa</span>
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
                                type="submit"
                                :disabled="submitting"
                                class="w-full py-3.5 px-6 rounded-2xl bg-lb-orange hover:bg-lb-orange-hover text-white font-extrabold text-sm transition shadow-lb-sm flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                            >
                                <span x-show="!submitting">
                                    <i class="fas fa-lock mr-1.5"></i> Pay Securely via PayHere
                                </span>
                                <span x-show="submitting" x-cloak>
                                    <i class="fas fa-spinner fa-spin mr-1.5"></i> Preparing Secure Gateway...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</x-layouts.public>
