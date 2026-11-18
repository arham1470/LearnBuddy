<x-layouts.public title="Create Account - LearnBuddy" description="Join LearnBuddy to start exploring courses, batches, and live classes across Sri Lanka.">
    <section class="min-h-[calc(100vh-140px)] flex items-center justify-center py-6 sm:py-10 px-4 sm:px-6 lg:px-8 bg-neutral-50/50">
        <div class="w-full max-w-3xl lg:max-w-4xl">
            <!-- Brand & Heading -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-lb-orange text-xs font-semibold tracking-wide uppercase mb-2">
                    <i class="fas fa-graduation-cap text-xs" aria-hidden="true"></i> Start Learning Today
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">Create your LearnBuddy account</h1>
                <p class="mt-1.5 text-xs sm:text-sm text-neutral-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-lb-orange hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange rounded">Sign in here</a>
                </p>
            </div>

            <!-- Card Container -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                        <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                        <div>
                            <p class="font-semibold">Please fix the following issues:</p>
                            <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4" novalidate>
                    @csrf

                    <!-- Full Name Field -->
                    <div class="col-span-1">
                        <label for="name" class="block text-xs sm:text-sm font-semibold text-neutral-800 mb-1">
                            Full name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                                <i class="fas fa-user text-sm" aria-hidden="true"></i>
                            </span>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Kasun Perera"
                                class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange focus-visible:border-transparent @error('name') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @enderror"
                            >
                        </div>
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="col-span-1">
                        <label for="email" class="block text-xs sm:text-sm font-semibold text-neutral-800 mb-1">
                            Email address <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                                <i class="fas fa-envelope text-sm" aria-hidden="true"></i>
                            </span>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                placeholder="kasun@example.com"
                                class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange focus-visible:border-transparent @error('email') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @enderror"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sri Lankan Phone Number Field (Optional) -->
                    <div class="col-span-1">
                        <label for="phone" class="block text-xs sm:text-sm font-semibold text-neutral-800 mb-1">
                            Sri Lankan phone number <span class="text-neutral-400 font-normal">(Optional)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                                <i class="fas fa-phone text-sm" aria-hidden="true"></i>
                            </span>
                            <input
                                id="phone"
                                type="tel"
                                name="phone"
                                value="{{ old('phone') }}"
                                autocomplete="tel"
                                placeholder="077 123 4567 or +94 77 123 4567"
                                class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange focus-visible:border-transparent @error('phone') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @enderror"
                            >
                        </div>
                        @error('phone')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Desktop Helper / Info Badge -->
                    <div class="col-span-1 hidden md:flex items-center">
                        <div class="w-full p-3 rounded-2xl bg-orange-50/70 border border-orange-100 flex items-center gap-2.5 text-xs text-neutral-600">
                            <i class="fas fa-shield-alt text-lb-orange text-sm shrink-0" aria-hidden="true"></i>
                            <span>Secure registration: 6-digit email OTP verification ensures a safe and trusted learning community.</span>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="col-span-1">
                        <label for="password" class="block text-xs sm:text-sm font-semibold text-neutral-800 mb-1">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative" x-data="{ showPassword: false }">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                                <i class="fas fa-lock text-sm" aria-hidden="true"></i>
                            </span>
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Minimum 8 characters"
                                class="w-full pl-10 pr-11 py-2.5 sm:py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange focus-visible:border-transparent @error('password') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @enderror"
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-neutral-400 hover:text-neutral-700 focus-visible:outline-none focus-visible:text-lb-orange cursor-pointer transition select-none"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                :title="showPassword ? 'Hide password' : 'Show password'"
                            >
                                <i class="fas text-sm" :class="showPassword ? 'fa-eye-slash text-neutral-600' : 'fa-eye text-neutral-400'" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="col-span-1">
                        <label for="password_confirmation" class="block text-xs sm:text-sm font-semibold text-neutral-800 mb-1">
                            Confirm password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative" x-data="{ showConfirmPassword: false }">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                                <i class="fas fa-check-double text-sm" aria-hidden="true"></i>
                            </span>
                            <input
                                id="password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Re-enter your password"
                                class="w-full pl-10 pr-11 py-2.5 sm:py-3 rounded-2xl border border-neutral-200 bg-neutral-50/30 text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange focus-visible:border-transparent"
                            >
                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-neutral-400 hover:text-neutral-700 focus-visible:outline-none focus-visible:text-lb-orange cursor-pointer transition select-none"
                                :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
                                :title="showConfirmPassword ? 'Hide password' : 'Show password'"
                            >
                                <i class="fas text-sm" :class="showConfirmPassword ? 'fa-eye-slash text-neutral-600' : 'fa-eye text-neutral-400'" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Controls: Terms & Submit Button in horizontal layout on desktop -->
                    <div class="md:col-span-2 pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t border-neutral-100">
                        <!-- Terms of Service Checkbox -->
                        <div class="flex-1">
                            <label class="flex items-start gap-2.5 cursor-pointer text-xs text-neutral-600 select-none">
                                <input
                                    type="checkbox"
                                    name="terms"
                                    id="terms"
                                    value="1"
                                    required
                                    {{ old('terms') ? 'checked' : '' }}
                                    class="w-4 h-4 mt-0.5 rounded border-neutral-300 text-lb-orange focus:ring-lb-orange cursor-pointer"
                                >
                                <span>
                                    I agree to the <a href="#" class="font-semibold text-lb-orange hover:underline">Terms</a> and <a href="#" class="font-semibold text-lb-orange hover:underline">Privacy Policy</a> of LearnBuddy Sri Lanka.
                                </span>
                            </label>
                            @error('terms')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="sm:shrink-0">
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 py-3 px-8 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-lb-orange cursor-pointer text-sm"
                            >
                                <span>Create account</span>
                                <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Market Info -->
                <div class="mt-4 pt-4 border-t border-neutral-100 flex items-center justify-between text-[11px] text-neutral-400">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-coins text-lb-orange" aria-hidden="true"></i>
                        <span>Default currency: LKR (Sri Lankan Rupee)</span>
                    </span>
                    <span class="hidden sm:inline-block">Empowering learners across Sri Lanka</span>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
