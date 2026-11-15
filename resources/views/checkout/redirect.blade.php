<x-layouts.public :title="'Connecting to Payment Gateway · Order ' . $order->order_number">
    <section class="py-16 sm:py-24 bg-lb-off-white min-h-[750px] flex items-center justify-center">
        <div class="max-w-md w-full mx-auto px-4 text-center">
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-md p-8 sm:p-10 space-y-6">
                <!-- Gateway Logo / Spinner -->
                <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-4 border-orange-100 border-t-lb-orange animate-spin"></div>
                    <i class="fas fa-shield-alt text-2xl text-lb-orange"></i>
                </div>

                <div class="space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-lb-orange border border-orange-200">
                        @if ($checkoutData['is_sandbox'])
                            <i class="fas fa-flask"></i> Sandbox Test Mode
                        @else
                            <i class="fas fa-lock"></i> Secure 256-Bit SSL
                        @endif
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black text-lb-black">
                        Transferring to {{ ucfirst($checkoutData['gateway'] ?? 'Payment Gateway') }}
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-600">
                        Please do not refresh this page or press back. You will be redirected to the secure payment processor momentarily.
                    </p>
                </div>

                <!-- Order Meta -->
                <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/80 text-left text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Order Reference:</span>
                        <span class="font-mono font-bold text-neutral-900">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Course:</span>
                        <span class="font-semibold text-neutral-900 truncate max-w-[200px]">{{ $order->course->title }}</span>
                    </div>
                    <div class="flex justify-between border-t border-neutral-200/80 pt-2 font-bold">
                        <span class="text-neutral-800">Amount Due:</span>
                        <span class="text-lb-orange text-sm">{{ $order->formattedAmount() }}</span>
                    </div>
                </div>

                <!-- Hidden Auto-Submitting Form -->
                <form id="payment-gateway-form" action="{{ $checkoutData['action_url'] }}" method="{{ $checkoutData['method'] }}">
                    @foreach ($checkoutData['fields'] as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach

                    <noscript>
                        <div class="p-3 mb-3 bg-amber-50 text-amber-900 text-xs rounded-xl border border-amber-200">
                            JavaScript is disabled in your browser. Click the button below to proceed.
                        </div>
                    </noscript>

                    <button
                        type="submit"
                        class="w-full py-3 px-6 rounded-2xl bg-neutral-900 hover:bg-lb-black text-white font-bold text-xs transition shadow cursor-pointer"
                    >
                        Click here if not redirected automatically &rarr;
                    </button>
                </form>

                <p class="text-[11px] text-neutral-400">
                    LearnBuddy never handles or stores credit/debit card numbers or CVV codes.
                </p>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Automatically redirect or submit form
            setTimeout(function () {
                @if (strtoupper($checkoutData['method']) === 'GET' && empty($checkoutData['fields']))
                    window.location.href = @js($checkoutData['action_url']);
                @else
                    var form = document.getElementById('payment-gateway-form');
                    if (form) {
                        form.submit();
                    }
                @endif
            }, 600);
        });
    </script>
</x-layouts.public>
