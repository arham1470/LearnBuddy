<x-layouts.public :title="'Checkout Cancelled · Order ' . $order->order_number">
    <section class="py-12 sm:py-20 bg-lb-off-white min-h-[650px] flex items-center justify-center">
        <div class="max-w-md w-full mx-auto px-4 text-center">
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-md p-6 sm:p-10 space-y-6">
                <!-- Cancel Icon -->
                <div class="w-20 h-20 mx-auto rounded-full bg-neutral-100 text-neutral-500 flex items-center justify-center text-3xl">
                    <i class="fas fa-times"></i>
                </div>

                <div class="space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-neutral-100 text-neutral-700">
                        Payment Not Completed
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black text-lb-black">
                        Checkout Was Cancelled
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-600">
                        You cancelled the payment transaction. No charges were made to your account. Your pending order remains saved should you wish to try again.
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
                        <span class="text-neutral-800">Amount:</span>
                        <span class="text-neutral-900">{{ $order->formattedAmount() }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3 pt-2">
                    <a
                        href="{{ route('orders.pay', $order) }}"
                        class="w-full py-3 px-6 rounded-2xl bg-lb-orange hover:bg-lb-orange-hover text-white font-bold text-xs sm:text-sm transition shadow flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <i class="fas fa-redo"></i> Retry Payment via PayHere
                    </a>

                    <a
                        href="{{ route('courses.show', $order->course) }}"
                        class="w-full py-3 px-6 rounded-2xl bg-neutral-100 hover:bg-neutral-200 text-neutral-800 font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2"
                    >
                        Return to Course Details
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
