<x-layouts.public title="Log In - LearnBuddy" description="Sign in to your LearnBuddy account to continue your learning journey.">
    @php
        $isPendingApproval = session('account_pending_approval') ||
            ($errors->has('email') && str_contains($errors->first('email'), 'awaiting administrator approval'));
        $displayErrors = array_filter($errors->all(), function ($error) {
            return !str_contains($error, 'awaiting administrator approval');
        });
    @endphp

    <section class="min-h-[calc(100vh-140px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/50">
        <div class="w-full max-w-md">
            <!-- Brand & Heading -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-lb-orange text-xs font-semibold tracking-wide uppercase mb-3">
                    <i class="fas fa-shield-alt text-xs" aria-hidden="true"></i> Secure Portal
                </div>
                <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Welcome Back</h1>
                <p class="mt-2 text-sm text-neutral-600">
                    Don't have an account yet?
                    <a href="{{ route('register') }}" class="font-semibold text-lb-orange hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange rounded">Create an account</a>
                </p>
            </div>

            <!-- Card Container -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <!-- Session Status Flash Alert -->
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5" aria-hidden="true"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Validation Errors Alert (Excludes Pending Approval) -->
                @if (!empty($displayErrors))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                        <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                        <div>
                            <p class="font-semibold">Authentication failed</p>
                            <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                                @foreach ($displayErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5" novalidate>
                    @csrf

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
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="name@example.com"
                                class="w-full pl-10 pr-4 py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange focus-visible:border-transparent @if(!$isPendingApproval && $errors->has('email')) border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @endif"
                            >
                        </div>
                        @if (!$isPendingApproval)
                            @error('email')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-semibold text-neutral-800">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-lb-orange hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange rounded">
                                Forgot password?
                            </a>
                        </div>
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
                                autocomplete="current-password"
                                placeholder="••••••••"
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

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center">
                        <label class="flex items-center gap-2.5 cursor-pointer text-sm text-neutral-700 select-none">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                {{ old('remember') ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-neutral-300 text-lb-orange focus:ring-lb-orange"
                            >
                            <span>Keep me signed in</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-lb-orange cursor-pointer"
                        >
                            <span>Sign in</span>
                            <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>

                <!-- Sri Lanka Context Note -->
                <div class="mt-6 pt-6 border-t border-neutral-100 flex items-center justify-center gap-2 text-xs text-neutral-500">
                    <i class="fas fa-globe-asia text-lb-orange" aria-hidden="true"></i>
                    <span>Default timezone: Sri Lanka (Asia/Colombo)</span>
                </div>
            </div>
        </div>

        <!-- Pending Approval Background-Blurred Modal Popup -->
        @if ($isPendingApproval)
            <div
                x-data="{
                    seconds: 5,
                    timer: null,
                    init() {
                        this.timer = setInterval(() => {
                            if (this.seconds > 1) {
                                this.seconds--;
                            } else {
                                this.seconds = 0;
                                clearInterval(this.timer);
                                window.location.href = '{{ route('home') }}';
                            }
                        }, 1000);
                    }
                }"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-900/60 backdrop-blur-md transition-opacity duration-300"
                role="dialog"
                aria-modal="true"
                aria-labelledby="approval-modal-title"
            >
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-2xl p-6 sm:p-8 max-w-md w-full text-center relative overflow-hidden transform transition-all duration-300 scale-100 space-y-5">
                    <!-- Icon -->
                    <div class="relative mx-auto w-16 h-16">
                        <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-500 flex items-center justify-center text-2xl shadow-xs">
                            <i class="fas fa-user-clock" aria-hidden="true"></i>
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] ring-2 ring-white shadow-xs">
                            <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                        </span>
                    </div>

                    <!-- Texts -->
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/80 text-amber-900 text-xs font-semibold uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Awaiting Approval
                        </div>
                        <h2 id="approval-modal-title" class="text-xl sm:text-2xl font-extrabold text-neutral-900 tracking-tight">
                            Account Pending Approval
                        </h2>
                        <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed max-w-sm mx-auto">
                            Your account has been verified, but is awaiting administrator approval. You will receive an email once approved.
                        </p>
                    </div>

                    <!-- Countdown Status Box -->
                    <div class="p-3.5 rounded-2xl bg-neutral-50 border border-neutral-200/80 flex items-center justify-between gap-3 text-xs text-neutral-600">
                        <div class="flex items-center gap-2 font-medium">
                            <i class="fas fa-circle-notch fa-spin text-lb-orange text-sm" aria-hidden="true"></i>
                            <span>Redirecting to homepage in</span>
                        </div>
                        <span class="font-mono font-extrabold text-xs px-2.5 py-1 rounded-full bg-neutral-900 text-white shadow-xs">
                            <span x-text="seconds">5</span>s
                        </span>
                    </div>

                    <!-- Immediate Redirect Button -->
                    <div class="pt-1">
                        <a
                            href="{{ route('home') }}"
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-lb-orange cursor-pointer"
                        >
                            <span>Go to Home Now</span>
                            <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </section>
</x-layouts.public>
