<x-layouts.public title="Verify Email Code - LearnBuddy" description="Enter the 6-digit verification code sent to your email.">
    <section class="min-h-[calc(100vh-140px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/50">
        <div class="w-full max-w-md">
            <!-- Brand & Heading -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto rounded-3xl bg-orange-100 text-lb-orange flex items-center justify-center text-2xl mb-4 shadow-sm">
                    <i class="fas fa-key" aria-hidden="true"></i>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-lb-orange text-xs font-semibold tracking-wide uppercase mb-3">
                    <i class="fas fa-envelope-open-text text-xs" aria-hidden="true"></i> Email Verification
                </div>
                <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Enter verification code</h1>
                <p class="mt-2 text-sm text-neutral-600">
                    We sent a 6-digit code to <span class="font-semibold text-neutral-900">{{ $maskedEmail }}</span>. Enter the code below to confirm your email.
                </p>
            </div>

            <!-- Card Container -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <!-- Session Status Flash Alert -->
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                        <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                        <div>
                            <p class="font-semibold">Verification failed</p>
                            <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- OTP Input Form with Alpine.js -->
                <form
                    method="POST"
                    action="{{ route('otp.verify.submit') }}"
                    x-data="{
                        digits: ['', '', '', '', '', ''],
                        get otp() {
                            return this.digits.join('');
                        },
                        handleInput(index, event) {
                            const val = event.target.value.replace(/[^0-9]/g, '');
                            this.digits[index] = val ? val.slice(-1) : '';

                            if (this.digits[index] && index < 5) {
                                this.$refs['input' + (index + 1)].focus();
                            }
                        },
                        handleKeyDown(index, event) {
                            if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
                                this.$refs['input' + (index - 1)].focus();
                            }
                        },
                        handlePaste(event) {
                            event.preventDefault();
                            const pasteData = (event.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9]/g, '');
                            if (!pasteData) return;
                            for (let i = 0; i < 6; i++) {
                                this.digits[i] = pasteData[i] || '';
                            }
                            const nextIndex = Math.min(pasteData.length, 5);
                            this.$refs['input' + nextIndex].focus();
                        }
                    }"
                    class="space-y-6"
                >
                    @csrf

                    <!-- Hidden full OTP input submitted with form -->
                    <input type="hidden" name="otp" :value="otp">

                    <div>
                        <label class="block text-center text-xs font-bold text-neutral-700 uppercase tracking-wider mb-4">
                            6-Digit Security Code
                        </label>

                        <!-- 6-digit visual boxes -->
                        <div class="flex items-center justify-center gap-2 sm:gap-3" @paste="handlePaste($event)">
                            <template x-for="(digit, i) in 6" :key="i">
                                <input
                                    :x-ref="'input' + i"
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    maxlength="1"
                                    :value="digits[i]"
                                    @input="handleInput(i, $event)"
                                    @keydown="handleKeyDown(i, $event)"
                                    autofocus="i === 0"
                                    class="w-11 h-14 sm:w-13 sm:h-16 text-center text-xl sm:text-2xl font-black rounded-2xl border-2 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange focus-visible:border-transparent cursor-pointer"
                                    :class="digits[i] ? 'border-lb-orange bg-orange-50/40 text-lb-black' : 'border-neutral-200 bg-neutral-50/40 text-neutral-800'"
                                    autocomplete="off"
                                >
                            </template>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="otp.length !== 6"
                        class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-full font-bold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-lb-orange disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer text-sm"
                    >
                        <i class="fas fa-check-circle text-xs" aria-hidden="true"></i>
                        <span>Confirm & Continue</span>
                    </button>
                </form>

                <!-- Resend & Cooldown Section -->
                <div
                    x-data="{
                        cooldown: 60,
                        timer: null,
                        startTimer() {
                            this.timer = setInterval(() => {
                                if (this.cooldown > 0) {
                                    this.cooldown--;
                                } else {
                                    clearInterval(this.timer);
                                }
                            }, 1000);
                        },
                        init() {
                            this.startTimer();
                        }
                    }"
                    class="mt-6 pt-6 border-t border-neutral-100 text-center"
                >
                    <p class="text-xs text-neutral-500 mb-2">Didn't receive the email code?</p>

                    <form method="POST" action="{{ route('otp.verify.resend') }}">
                        @csrf
                        <button
                            type="submit"
                            :disabled="cooldown > 0"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-lb-orange hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange rounded disabled:text-neutral-400 disabled:no-underline disabled:cursor-not-allowed cursor-pointer"
                        >
                            <i class="fas fa-redo-alt text-[10px]" :class="{ 'fa-spin': false }" aria-hidden="true"></i>
                            <span x-show="cooldown > 0">Resend code in <strong x-text="cooldown"></strong>s</span>
                            <span x-show="cooldown === 0">Resend 6-digit code now</span>
                        </button>
                    </form>
                </div>

                <!-- Back to Login / Change account -->
                <div class="mt-5 text-center">
                    <a
                        href="{{ route('login') }}"
                        class="text-xs text-neutral-500 hover:text-neutral-800 transition inline-flex items-center gap-1.5"
                    >
                        <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                        <span>Back to sign in</span>
                    </a>
                </div>

                <div class="mt-6 pt-4 border-t border-neutral-100 text-center text-xs text-neutral-400">
                    Need help? Contact <a href="mailto:support@learnbuddy.lk" class="text-lb-orange hover:underline font-medium">support@learnbuddy.lk</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
