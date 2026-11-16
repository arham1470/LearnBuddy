@extends('layouts.admin')

@section('title', 'Financial & Orders')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Financial &amp; Orders</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Review student checkout transactions, gateway receipts, and settled income.</p>
        </div>
        <div class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#FF5F00] to-[#FF8A3D] text-white font-bold text-sm shadow-md shadow-orange-500/20">
            Total Revenue: LKR {{ number_format($totalRevenue, 2) }}
        </div>
    </div>

    <!-- Orders Table -->
    <div class="lb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Order #</th>
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-4">Course</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-5 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse($orders as $order)
                    <tr class="hover:bg-[#FFFDFB] transition-colors">
                        <td class="py-3.5 px-5 font-mono font-bold text-[#0A0A0A]">{{ $order->order_number }}</td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-[#0A0A0A]">{{ $order->user->name ?? 'N/A' }}</div>
                            <div class="text-xs text-[#6B6B6B]">{{ $order->user->email ?? '' }}</div>
                        </td>
                        <td class="py-3.5 px-4 text-[#6B6B6B] max-w-xs truncate">{{ $order->course->title ?? 'N/A' }}</td>
                        <td class="py-3.5 px-4 font-bold text-[#0A0A0A]">{{ $order->currency ?? 'LKR' }} {{ number_format($order->total_amount, 2) }}</td>
                        <td class="py-3.5 px-4">
                            @if($order->status === 'completed')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#DEF7EC] text-[#03543F]">Completed</span>
                            @elseif($order->status === 'pending')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#FEF3C7] text-[#92400E]">Pending</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-right text-xs text-[#6B6B6B] font-medium">
                            {{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-[#6B6B6B]">
                            <div class="w-12 h-12 rounded-2xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div class="font-bold text-[#0A0A0A]">No financial transactions found</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="p-4 border-t border-[#E5E5E5]">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
