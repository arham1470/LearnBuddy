<x-layouts.public title="Contact Support · LearnBuddy" description="Get in touch with the LearnBuddy support team. We're here to assist with account access, billing, courses, and technical inquiries.">
    <!-- HERO HEADER -->
    <section class="bg-lb-peach pt-8 sm:pt-12 pb-12 sm:pb-16 rounded-b-[32px] sm:rounded-b-[48px] border-b border-orange-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center space-y-4">
            <nav class="flex items-center justify-center gap-2 text-xs text-neutral-500 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-lb-orange transition">Home</a>
                <span class="text-neutral-300">/</span>
                <span class="font-semibold text-neutral-800">Contact Support</span>
            </nav>

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-lb-orange text-xs font-bold tracking-wide uppercase shadow-2xs">
                <i class="fas fa-headset text-xs" aria-hidden="true"></i> LearnBuddy Help Desk
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-lb-black tracking-tight">
                How Can We <span class="text-lb-orange">Help You?</span>
            </h1>

            <p class="text-sm sm:text-base text-neutral-600 max-w-xl mx-auto leading-relaxed">
                Have a question regarding your account, course enrollment, classroom batches, or payments? Fill out the form below and our Colombo team will reply directly to your inbox.
            </p>
        </div>
    </section>

    <!-- CONTENT BODY -->
    <section class="py-10 sm:py-16 bg-neutral-50/60 min-h-[600px]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">

            <!-- Fast Contact Information Pills -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                <div class="p-4 rounded-2xl bg-white border border-neutral-200/80 shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-lb-orange flex items-center justify-center shrink-0 text-base">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider">Email Us</p>
                        <p class="text-xs sm:text-sm font-bold text-neutral-900 truncate">support@learnbuddy.lk</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-neutral-200/80 shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-lb-orange flex items-center justify-center shrink-0 text-base">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider">Call Center</p>
                        <p class="text-xs sm:text-sm font-bold text-neutral-900 truncate">+94 11 234 5678</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-neutral-200/80 shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-lb-orange flex items-center justify-center shrink-0 text-base">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider">Campus Office</p>
                        <p class="text-xs sm:text-sm font-bold text-neutral-900 truncate">Colombo, Sri Lanka</p>
                    </div>
                </div>
            </div>

            <!-- Support Ticket Form Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-10">
                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-8 p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3.5 shadow-xs" role="alert">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-exclamation-circle text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold">Please correct the following:</h3>
                            <ul class="mt-1 list-disc list-inside text-xs space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('support.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Your Full Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                                    <i class="fas fa-user text-xs"></i>
                                </span>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', auth()->user()?->name) }}"
                                    required
                                    placeholder="e.g. Kasun Perera"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('name') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/40 @enderror text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-lb-orange transition"
                                >
                            </div>
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                                    <i class="fas fa-envelope text-xs"></i>
                                </span>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', auth()->user()?->email) }}"
                                    required
                                    placeholder="kasun@example.com"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('email') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/40 @enderror text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-lb-orange transition"
                                >
                            </div>
                            @error('email')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Phone Number -->
                        <div>
                            <label for="phone" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Phone Number <span class="text-neutral-400 font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                                    <i class="fas fa-phone text-xs"></i>
                                </span>
                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone', auth()->user()?->phone) }}"
                                    placeholder="+94 77 123 4567"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('phone') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/40 @enderror text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-lb-orange transition font-mono"
                                >
                            </div>
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Inquiry Category -->
                        <div>
                            <label for="category" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Issue Category <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                                    <i class="fas fa-tag text-xs"></i>
                                </span>
                                <select
                                    id="category"
                                    name="category"
                                    required
                                    class="w-full pl-10 pr-8 py-2.5 rounded-xl border @error('category') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/40 @enderror text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-lb-orange transition appearance-none cursor-pointer"
                                >
                                    @foreach (\App\Models\SupportMessage::CATEGORIES as $key => $label)
                                        <option value="{{ $key }}" {{ old('category', $defaultCategory) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-neutral-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </span>
                            </div>
                            @error('category')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                            Subject / Summary <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            value="{{ old('subject', $defaultSubject) }}"
                            required
                            placeholder="e.g. Account reactivation request / Payment verification"
                            class="w-full px-4 py-2.5 rounded-xl border @error('subject') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/40 @enderror text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-lb-orange transition"
                        >
                        @error('subject')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message Body -->
                    <div>
                        <label for="message" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                            Detailed Message <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                            placeholder="Please provide details about your issue, order number, batch code, or any other relevant information..."
                            class="w-full p-4 rounded-2xl border @error('message') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/40 @enderror text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-lb-orange transition leading-relaxed"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2 text-xs text-neutral-500">
                            <i class="fas fa-clock text-lb-orange" aria-hidden="true"></i>
                            <span>Typical response time: 2–4 business hours</span>
                        </div>

                        <button
                            type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] text-white text-sm font-bold transition shadow-sm cursor-pointer"
                        >
                            <span>Send Message</span>
                            <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Support Success Background-Blurred Popup Modal -->
        @if (session('support_success'))
            <div
                x-data="{ showSuccessModal: true }"
                x-show="showSuccessModal"
                x-cloak
                @keydown.escape.window="showSuccessModal = false"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-900/60 backdrop-blur-md transition-opacity duration-300"
                role="dialog"
                aria-modal="true"
                aria-labelledby="support-success-modal-title"
            >
                <div
                    @click.outside="showSuccessModal = false"
                    x-show="showSuccessModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="bg-white rounded-3xl border border-neutral-200/80 shadow-2xl p-6 sm:p-8 max-w-md w-full text-center relative overflow-hidden transform space-y-5"
                >
                    <!-- Close Button -->
                    <button
                        type="button"
                        @click="showSuccessModal = false"
                        class="absolute top-4 right-4 w-8 h-8 rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-500 hover:text-neutral-800 flex items-center justify-center transition cursor-pointer"
                        aria-label="Close dialog"
                    >
                        <i class="fas fa-times text-xs"></i>
                    </button>

                    <!-- Icon -->
                    <div class="relative mx-auto w-16 h-16">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-500 flex items-center justify-center text-2xl shadow-xs">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] ring-2 ring-white shadow-xs">
                            <i class="fas fa-check" aria-hidden="true"></i>
                        </span>
                    </div>

                    <!-- Texts -->
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 text-xs font-semibold uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Ticket Submitted
                        </div>
                        <h2 id="support-success-modal-title" class="text-xl sm:text-2xl font-extrabold text-neutral-900 tracking-tight">
                            Message Sent Successfully!
                        </h2>
                        <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed max-w-sm mx-auto">
                            {{ session('support_success') }}
                        </p>
                    </div>

                    <!-- SLA Information Box -->
                    <div class="p-3.5 rounded-2xl bg-neutral-50 border border-neutral-200/80 flex items-center justify-between gap-3 text-xs text-neutral-600">
                        <div class="flex items-center gap-2 font-medium">
                            <i class="fas fa-clock text-lb-orange text-sm" aria-hidden="true"></i>
                            <span>Expected Response Time</span>
                        </div>
                        <span class="font-bold text-xs px-2.5 py-1 rounded-full bg-neutral-900 text-white shadow-xs">
                            2–4 Hours
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="pt-1 flex flex-col sm:flex-row gap-2.5">
                        <a
                            href="{{ route('home') }}"
                            class="flex-1 inline-flex items-center justify-center gap-2 py-3 px-5 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm text-sm cursor-pointer"
                        >
                            <span>Return to Home</span>
                            <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        </a>
                        <button
                            type="button"
                            @click="showSuccessModal = false"
                            class="flex-1 inline-flex items-center justify-center py-3 px-5 rounded-full font-semibold text-neutral-700 bg-neutral-100 hover:bg-neutral-200 active:scale-[0.99] transition text-sm cursor-pointer"
                        >
                            Dismiss
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </section>
</x-layouts.public>
