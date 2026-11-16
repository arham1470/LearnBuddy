@extends('layouts.admin')

@section('title', 'Campus Locations')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#6B6B6B] mb-1">
                <a href="{{ route('admin.batches.index') }}" class="hover:text-[#FF5F00] transition-colors">Campus &amp; Batches</a>
                <svg class="w-3 h-3 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#0A0A0A] font-semibold">Campuses</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Physical Training Centres</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Configure physical learning centres, classroom capacities, and regional facilities across Sri Lanka.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.batches.index') }}"
               class="px-4 py-2.5 rounded-xl border border-[#E5E5E5] bg-white hover:bg-[#F7F7F5] text-sm font-semibold text-[#0A0A0A] transition-colors">
                Batches &amp; Schedules
            </a>
            <a href="{{ route('admin.locations.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-sm font-bold shadow-md shadow-orange-500/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Add Campus</span>
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('status') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filters & Search Bar -->
    <div class="lb-card p-4">
        <form method="GET" action="{{ route('admin.locations.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative w-full sm:max-w-md">
                <svg class="w-4 h-4 text-[#6B6B6B] absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search campus name, code, city or province..."
                       class="w-full pl-10 pr-4 py-2 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-5 py-2 rounded-xl bg-[#1A1A1A] hover:bg-black text-white text-sm font-semibold transition-colors">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.locations.index') }}" class="px-4 py-2 rounded-xl border border-[#E5E5E5] text-[#6B6B6B] hover:text-[#0A0A0A] text-sm font-medium transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Campus Locations Table -->
    <div class="lb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Campus Name &amp; Code</th>
                        <th class="py-3 px-4">Province &amp; District</th>
                        <th class="py-3 px-4">Address</th>
                        <th class="py-3 px-4 text-center">Room Capacity</th>
                        <th class="py-3 px-4 text-center">Batches</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse ($locations as $loc)
                        <tr class="hover:bg-[#F7F7F5]/60 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#0A0A0A] text-sm">{{ $loc->name }}</p>
                                        <p class="text-xs font-mono text-[#6B6B6B]">{{ $loc->code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-[#0A0A0A]">
                                <span class="font-semibold">{{ $loc->province }}</span>
                                <span class="text-[#6B6B6B] block">{{ $loc->district }} · {{ $loc->city }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-[#6B6B6B] truncate max-w-xs">
                                {{ $loc->address_line_1 }}
                            </td>
                            <td class="py-3.5 px-4 text-center text-xs font-bold text-[#0A0A0A]">
                                {{ $loc->max_room_capacity }} seats
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#F7F7F5] text-[#0A0A0A] border border-[#E5E5E5]">
                                    {{ $loc->batches_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($loc->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.locations.edit', $loc) }}"
                                       class="px-3 py-1.5 rounded-lg border border-[#E5E5E5] hover:border-orange-300 hover:text-[#FF5F00] text-xs font-bold transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.locations.destroy', $loc) }}" onsubmit="return confirm('Are you sure you want to delete this campus location?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-transparent hover:border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#6B6B6B]">
                                <div class="w-12 h-12 rounded-2xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div class="font-bold text-[#0A0A0A]">No campus locations found</div>
                                <div class="text-xs text-[#6B6B6B] mt-1">Add physical classroom centres to enable in-person and hybrid courses.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($locations->hasPages())
            <div class="p-4 border-t border-[#E5E5E5] bg-[#F7F7F5]/50">
                {{ $locations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
