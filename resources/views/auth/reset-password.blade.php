<x-layouts.public title="Set New Password - LearnBuddy" description="Enter your new password to restore access to your LearnBuddy account.">
    <section class="min-h-[calc(100vh-140px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/50">
        <div class="w-full max-w-md">
            <!-- Brand & Heading -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-lb-orange text-xs font-semibold tracking-wide uppercase mb-3">
                    <i class="fas fa-lock text-xs" aria-hidden="true"></i> Set New Password
                </div>
                <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Create new password</h1>
                <p class="mt-2 text-sm text-neutral-600">
                    Choose a strong password with at least 8 characters including letters and numbers.
                </p>
            </div>

            <!-- Card Container -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                        <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                        <div>
                            <p class="font-semibold">Unable to reset password</p>
                            <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4" novalidate>
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-neutral-800 mb-1.5">
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
                                value="{{ old('email', $email) }}"
                                required
                                autocomplete="email"
                                class="w-full pl-10 pr-4 py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange focus-visible:border-transparent @error('email') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @enderror"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                            New password <span class="text-rose-500">*</span>
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
                                autofocus
                                autocomplete="new-password"
                                placeholder="Minimum 8 characters"
                                class="w-full pl-10 pr-11 py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange focus-visible:border-transparent @error('password') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @enderror"
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
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                            Confirm new password <span class="text-rose-500">*</span>
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
                                placeholder="Re-enter your new password"
                                class="w-full pl-10 pr-11 py-3 rounded-2xl border border-neutral-200 bg-neutral-50/30 text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange focus-visible:border-transparent"
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

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-lb-orange cursor-pointer"
                        >
                            <span>Reset password</span>
                            <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>
