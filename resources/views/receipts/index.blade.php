<x-layouts.public title="My Payment History & Receipts · LearnBuddy">
    <!-- BANNER -->
    <section class="bg-lb-peach pt-8 sm:pt-12 pb-10 sm:pb-14 rounded-b-[32px] sm:rounded-b-[48px] border-b border-orange-100">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-lb-orange transition">Home</a>
                <span class="text-neutral-300">/</span>
                <a href="{{ route('student.dashboard') }}" class="hover:text-lb-orange transition">Dashboard</a>
                <span class="text-neutral-300">/</span>
                <span class="font-semibold text-neutral-800">Payment History</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-lb-black">
                        Payment History & <span class="text-lb-orange">Receipts</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-600">
                        View all your course enrolment transactions, tax receipts, and payment status records in Sri Lankan Rupees (LKR).
                    </p>
                </div>

                <a
                    href="{{ route('student.dashboard') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-neutral-200 text-neutral-700 hover:text-lb-orange text-xs font-bold transition shadow-sm self-start sm:self-auto"
                >
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="py-10 sm:py-16 bg-lb-off-white min-h-[600px]">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">

            @if ($orders->count() === 0)
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-12 text-center max-w-lg mx-auto space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 text-lb-orange flex items-center justify-center mx-auto text-2xl">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-neutral-900">No payment records found</h3>
                        <p class="text-xs text-neutral-500">
                            You have not initiated any paid course enrolments yet. Browse our catalogue to get started!
                        </p>
                    </div>
                    <a
                        href="{{ route('courses.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange hover:bg-lb-orange-hover text-white text-xs font-bold transition shadow-sm"
                    >
                        <i class="fas fa-search"></i>
                        <span>Explore Courses</span>
                    </a>
                </div>
            @else
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-neutral-50 border-b border-neutral-200/80 text-neutral-500 uppercase text-[10px] tracking-wider">
                                <tr>
                                    <th class="py-3.5 px-6 font-bold">Order #</th>
                                    <th class="py-3.5 px-4 font-bold">Course</th>
                                    <th class="py-3.5 px-4 font-bold">Delivery Mode</th>
                                    <th class="py-3.5 px-4 font-bold">Date & Time</th>
                                    <th class="py-3.5 px-4 font-bold text-right">Amount (LKR)</th>
                                    <th class="py-3.5 px-4 font-bold text-center">Status</th>
                                    <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                @foreach ($orders as $order)
                                    <tr class="hover:bg-neutral-50/70 transition">
                                        <td class="py-4 px-6 font-mono font-bold text-neutral-900 whitespace-nowrap">
                                            {{ $order->order_number }}
                                        </td>
                                        <td class="py-4 px-4 min-w-[200px]">
                                            <div class="font-bold text-neutral-900">{{ $order->course->title }}</div>
                                            <div class="text-[11px] text-neutral-500 mt-0.5">{{ $order->deliveryOption->title }}</div>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $order->deliveryOption->delivery_mode === 'physical' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $order->deliveryOption->delivery_mode }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-neutral-600 whitespace-nowrap">
                                            {{ $order->created_at->setTimezone('Asia/Colombo')->format('d M Y, h:i A') }}
                                        </td>
                                        <td class="py-4 px-4 text-right font-bold text-neutral-900 whitespace-nowrap">
                                            {{ $order->formattedAmount() }}
                                        </td>
                                        <td class="py-4 px-4 text-center whitespace-nowrap">
                                            @if ($order->isCompleted())
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                                    Paid
                                                </span>
                                            @elseif ($order->isPending())
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                                                    Pending
                                                </span>
                                            @elseif ($order->isRefunded())
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800">
                                                    Refunded
                                                </span>
                                            @elseif ($order->isCancelled())
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-neutral-100 text-neutral-600">
                                                    Cancelled
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800">
                                                    Failed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2">
                                                @if ($order->isCompleted())
                                                    <a
                                                        href="{{ route('receipts.show', $order) }}"
                                                        class="px-3 py-1.5 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-800 font-bold text-xs transition flex items-center gap-1.5"
                                                    >
                                                        <i class="fas fa-file-invoice"></i> Receipt
                                                    </a>
                                                @elseif ($order->isPending())
                                                    <a
                                                        href="{{ route('orders.pay', $order) }}"
                                                        class="px-3 py-1.5 rounded-xl bg-lb-orange hover:bg-lb-orange-hover text-white font-bold text-xs transition flex items-center gap-1.5"
                                                    >
                                                        <i class="fas fa-credit-card"></i> Pay Now
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($orders->hasPages())
                        <div class="p-4 border-t border-neutral-100">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </section>
</x-layouts.public>
