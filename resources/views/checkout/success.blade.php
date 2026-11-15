<x-layouts.public :title="'Payment Status · Order ' . $order->order_number">
    <section class="py-12 sm:py-20 bg-lb-off-white min-h-[700px]">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-md p-6 sm:p-10 space-y-6 text-center">

                @if ($order->isCompleted())
                    <!-- VERIFIED SUCCESS STATE -->
                    <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl shadow-sm">
                        <i class="fas fa-check"></i>
                    </div>

                    <div class="space-y-2">
                        <x-badge variant="green" icon="fas fa-shield-check">
                            Payment Verified & Course Enrolment Active
                        </x-badge>
                        <h1 class="text-2xl sm:text-3xl font-black text-lb-black">
                            Welcome! Enrolment Confirmed
                        </h1>
                        <p class="text-xs sm:text-sm text-neutral-600 max-w-md mx-auto">
                            Your payment has been cryptographically verified server-side. Your course content, lessons, and student materials are now fully unlocked.
                        </p>
                    </div>

                    <!-- Details Card -->
                    <div class="p-5 rounded-2xl bg-neutral-50 border border-neutral-200/80 text-left text-xs sm:text-sm space-y-2.5">
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-500">Order Reference:</span>
                            <span class="font-mono font-bold text-neutral-900">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-500">Course Enrolled:</span>
                            <span class="font-bold text-neutral-900">{{ $order->course->title }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-500">Delivery Mode:</span>
                            <span class="font-semibold text-neutral-800 uppercase text-xs">{{ $order->deliveryOption->delivery_mode }}</span>
                        </div>
                        @if ($order->batch)
                            <div class="flex justify-between items-center">
                                <span class="text-neutral-500">Campus Batch:</span>
                                <span class="font-semibold text-neutral-800">{{ $order->batch->batch_code }} &bull; {{ $order->batch->location?->city_town }}</span>
                            </div>
                        @endif
                        @if ($order->latestPayment?->receipt_number)
                            <div class="flex justify-between items-center">
                                <span class="text-neutral-500">Receipt Number:</span>
                                <span class="font-mono font-bold text-emerald-700">{{ $order->latestPayment->receipt_number }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center border-t border-neutral-200/80 pt-2 font-bold">
                            <span class="text-neutral-800">Total Paid (LKR):</span>
                            <span class="text-base text-lb-orange">{{ $order->formattedAmount() }}</span>
                        </div>
                    </div>

                    <!-- Action CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                        <a
                            href="{{ route('learning.index', $order->course) }}"
                            class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-lb-orange hover:bg-lb-orange-hover text-white font-extrabold text-xs sm:text-sm transition shadow flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-play-circle"></i> Start Learning Now
                        </a>

                        <a
                            href="{{ route('receipts.show', $order) }}"
                            class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-neutral-900 hover:bg-lb-black text-white font-bold text-xs sm:text-sm transition shadow flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-file-invoice"></i> View Official Receipt
                        </a>
                    </div>
                @else
                    <!-- PENDING SERVER-SIDE VERIFICATION STATE -->
                    <div class="w-20 h-20 mx-auto rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-3xl shadow-sm animate-pulse">
                        <i class="fas fa-clock"></i>
                    </div>

                    <div class="space-y-2">
                        <x-badge variant="orange" icon="fas fa-sync fa-spin">
                            Verifying Payment with Provider
                        </x-badge>
                        <h1 class="text-2xl sm:text-3xl font-black text-lb-black">
                            Payment Submitted
                        </h1>
                        <p class="text-xs sm:text-sm text-neutral-600 max-w-md mx-auto">
                            Thank you! Your transaction is currently awaiting confirmation from the payment gateway.
                        </p>
                    </div>

                    <!-- Security Notice Banner -->
                    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-left text-xs space-y-1.5">
                        <div class="font-bold flex items-center gap-2 text-blue-800">
                            <i class="fas fa-info-circle"></i> Security Verification Policy:
                        </div>
                        <p class="text-blue-800/90 leading-relaxed">
                            To protect against fraud, <strong>browser redirects are not trusted as proof of payment</strong>. Enrolment access unlocks strictly after an authenticated server-to-server confirmation (IPN) is verified by LearnBuddy.
                        </p>
                        <p class="text-[11px] text-blue-700/80">
                            This verification typically completes in a few seconds. If you just completed payment in PayHere, click the button below to refresh status.
                        </p>
                    </div>

                    <!-- Order Meta Summary -->
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/80 text-left text-xs space-y-2">
                        <div class="flex justify-between">
                            <span class="text-neutral-500">Order Reference:</span>
                            <span class="font-mono font-bold text-neutral-900">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500">Course:</span>
                            <span class="font-semibold text-neutral-900 truncate max-w-[200px]">{{ $order->course->title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500">Amount:</span>
                            <span class="font-bold text-neutral-900">{{ $order->formattedAmount() }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                        <button
                            type="button"
                            onclick="window.location.reload();"
                            class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-lb-orange hover:bg-lb-orange-hover text-white font-bold text-xs sm:text-sm transition shadow flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-sync-alt"></i> Refresh Verification Status
                        </button>

                        <a
                            href="{{ route('student.dashboard') }}"
                            class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-neutral-100 hover:bg-neutral-200 text-neutral-800 font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2"
                        >
                            Go to Dashboard
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </section>
</x-layouts.public>
