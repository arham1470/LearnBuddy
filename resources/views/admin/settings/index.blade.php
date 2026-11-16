@extends('layouts.admin')

@section('title', 'Platform Settings')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Analytics &amp; System Settings</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Configure site identity, payment gateway credentials, and audit security logs.</p>
        </div>
    </div>

    <!-- System Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="lb-card p-5">
            <div class="text-[0.8125rem] font-semibold text-[#6B6B6B]">Platform Version</div>
            <div class="text-xl font-extrabold text-[#0A0A0A] mt-1">LearnBuddy v2.0 Enterprise</div>
            <div class="text-xs text-emerald-600 font-semibold mt-2 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> System Up to Date
            </div>
        </div>
        <div class="lb-card p-5">
            <div class="text-[0.8125rem] font-semibold text-[#6B6B6B]">Payment Gateways</div>
            <div class="text-xl font-extrabold text-[#0A0A0A] mt-1">Stripe &amp; PayHere</div>
            <div class="text-xs text-[#FF5F00] font-semibold mt-2">Active in LKR / USD</div>
        </div>
        <div class="lb-card p-5">
            <div class="text-[0.8125rem] font-semibold text-[#6B6B6B]">Video Streaming Provider</div>
            <div class="text-xl font-extrabold text-[#0A0A0A] mt-1">Mux Video &amp; HLS</div>
            <div class="text-xs text-emerald-600 font-semibold mt-2 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> Webhooks Connected
            </div>
        </div>
    </div>

    <!-- Recent Audit Logs -->
    <div class="lb-card overflow-hidden">
        <div class="p-5 border-b border-[#E5E5E5] flex items-center justify-between">
            <h3 class="text-base font-bold text-[#0A0A0A]">Recent Security &amp; Audit Logs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Event</th>
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">IP Address</th>
                        <th class="py-3 px-5 text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse($recentLogs as $log)
                    <tr class="hover:bg-[#FFFDFB]">
                        <td class="py-3.5 px-5 font-bold text-[#0A0A0A]">{{ $log->action ?? 'System Event' }}</td>
                        <td class="py-3.5 px-4 text-[#6B6B6B]">{{ $log->user->name ?? 'System' }}</td>
                        <td class="py-3.5 px-4 text-xs font-mono text-[#6B6B6B]">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        <td class="py-3.5 px-5 text-right text-xs text-[#6B6B6B]">{{ $log->created_at ? $log->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-[#6B6B6B]">No recent audit logs recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
