<x-layouts.public :title="'Official Receipt: ' . ($order->latestPayment?->receipt_number ?? $order->order_number)">
    <section class="py-8 sm:py-14 bg-lb-off-white min-h-[750px]">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">

            <!-- Action Toolbar (hidden on print) -->
            <div class="flex items-center justify-between gap-4 mb-6 print:hidden">
                <a
                    href="{{ route('receipts.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-bold text-neutral-600 hover:text-lb-orange transition"
                >
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Payment History</span>
                </a>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        onclick="window.print();"
                        class="px-4 py-2 rounded-xl bg-neutral-900 hover:bg-lb-black text-white text-xs font-bold transition shadow-sm flex items-center gap-2 cursor-pointer"
                    >
                        <i class="fas fa-print"></i>
                        <span>Print Receipt</span>
                    </button>
                    @if ($order->isCompleted())
                        <a
                            href="{{ route('learning.index', $order->course) }}"
                            class="px-4 py-2 rounded-xl bg-lb-orange hover:bg-lb-orange-hover text-white text-xs font-bold transition shadow-sm flex items-center gap-2"
                        >
                            <i class="fas fa-play-circle"></i>
                            <span>Go to Course</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- RECEIPT DOCUMENT CARD -->
            <div class="bg-white rounded-3xl border border-neutral-200/90 shadow-lb-md p-8 sm:p-12 space-y-8 text-neutral-900 print:shadow-none print:border-none print:p-0">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 border-b border-neutral-200 pb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-9 h-9 rounded-xl bg-lb-orange text-white flex items-center justify-center font-black text-lg">LB</span>
                            <span class="text-2xl font-black text-lb-black tracking-tight">Learn<span class="text-lb-orange">Buddy</span></span>
                        </div>
                        <p class="text-xs text-neutral-500 leading-relaxed">
                            LearnBuddy Education (Pvt) Ltd<br>
                            Colombo, Sri Lanka<br>
                            support@learnbuddy.lk &bull; +94 11 200 0000
                        </p>
                    </div>

                    <div class="sm:text-right space-y-1">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $order->isCompleted() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $order->isCompleted() ? 'PAID & CONFIRMED' : strtoupper($order->status) }}
                        </span>
                        <div class="text-xs text-neutral-500 pt-1">
                            <div>Receipt #: <strong class="font-mono text-neutral-900">{{ $order->latestPayment?->receipt_number ?? 'PENDING' }}</strong></div>
                            <div>Order #: <strong class="font-mono text-neutral-900">{{ $order->order_number }}</strong></div>
                            <div>Date: <strong class="text-neutral-900">{{ ($order->latestPayment?->paid_at ?? $order->created_at)->setTimezone('Asia/Colombo')->format('d M Y, h:i A') }}</strong></div>
                        </div>
                    </div>
                </div>

                <!-- Payer & Billing Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs border-b border-neutral-200 pb-6">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Billed To (Student)</span>
                        <div class="font-bold text-sm text-neutral-900">{{ $order->billing_name ?: $order->user->name }}</div>
                        <div class="text-neutral-600 mt-0.5">{{ $order->billing_email ?: $order->user->email }}</div>
                        <div class="text-neutral-600">{{ $order->billing_phone ?: $order->user->phone }}</div>
                        @if ($order->billing_address)
                            <div class="text-neutral-600">{{ $order->billing_address }}</div>
                        @endif
                    </div>

                    <div class="sm:text-right">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Payment Method & Gateway</span>
                        <div class="font-bold text-neutral-900">
                            {{ $order->latestPayment?->payment_method ?? 'PayHere Online Gateway' }}
                        </div>
                        <div class="text-neutral-600 mt-0.5">
                            Provider: <span class="uppercase font-semibold">{{ $order->latestPayment?->payment_gateway ?? 'PayHere' }}</span>
                        </div>
                        @if ($order->latestPayment?->transaction_reference)
                            <div class="text-neutral-500 font-mono text-[11px]">
                                Ref: {{ $order->latestPayment->transaction_reference }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-neutral-200 text-neutral-500 uppercase text-[10px] tracking-wider">
                                <th class="pb-3 font-bold">Course Item / Description</th>
                                <th class="pb-3 font-bold">Delivery Mode</th>
                                <th class="pb-3 font-bold text-center">Qty</th>
                                <th class="pb-3 font-bold text-right">Amount (LKR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            <tr>
                                <td class="py-4 pr-3">
                                    <div class="font-bold text-sm text-neutral-900">{{ $order->course->title }}</div>
                                    <div class="text-xs text-neutral-500 mt-0.5">
                                        {{ $order->deliveryOption->title }}
                                        @if ($order->batch)
                                            &bull; Batch: {{ $order->batch->batch_code }} ({{ $order->batch->location?->city_town }})
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $order->deliveryOption->delivery_mode === 'physical' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $order->deliveryOption->delivery_mode }}
                                    </span>
                                </td>
                                <td class="py-4 text-center text-neutral-700">1</td>
                                <td class="py-4 text-right font-bold text-neutral-900 whitespace-nowrap">
                                    {{ $order->formattedAmount() }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="border-t border-neutral-200 pt-4 flex flex-col sm:items-end">
                    <div class="w-full sm:w-72 space-y-2 text-xs">
                        <div class="flex justify-between text-neutral-600">
                            <span>Subtotal:</span>
                            <span class="font-medium text-neutral-900">{{ $order->formattedAmount() }}</span>
                        </div>
                        <div class="flex justify-between text-neutral-600">
                            <span>Gateway Processing Fees:</span>
                            <span class="font-medium text-emerald-600">LKR 0.00</span>
                        </div>
                        <div class="flex justify-between text-neutral-600">
                            <span>Taxes / VAT:</span>
                            <span class="font-medium text-neutral-900">Included</span>
                        </div>
                        <div class="flex justify-between items-baseline border-t border-neutral-300 pt-2 font-black text-sm text-neutral-900">
                            <span>Total Paid (LKR):</span>
                            <span class="text-lg text-lb-orange">{{ $order->formattedAmount() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer / Legal Notes -->
                <div class="border-t border-neutral-100 pt-6 text-[11px] text-neutral-500 space-y-1">
                    <p class="font-semibold text-neutral-700">Security & Authentication Notice:</p>
                    <p>
                        This is an official computer-generated receipt issued by LearnBuddy Sri Lanka. Payments are processed through Central Bank of Sri Lanka (CBSL) authorized IPG operators under PSD Direction No. 1 of 2018.
                    </p>
                    <p class="pt-1 text-neutral-400">
                        Thank you for learning with LearnBuddy! For questions regarding this receipt, please contact support@learnbuddy.lk quoting your Order Reference {{ $order->order_number }}.
                    </p>
                </div>
            </div>

        </div>
    </section>
</x-layouts.public>
