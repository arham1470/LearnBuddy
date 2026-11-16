@extends('layouts.admin')

@section('title', 'Campus & Batches')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Campus &amp; Batches</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Manage physical classrooms, physical locations, and session schedules.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('management.locations.index') }}"
               class="px-4 py-2.5 rounded-xl border border-[#E5E5E5] bg-white hover:bg-[#F7F7F5] text-sm font-semibold text-[#0A0A0A] transition-colors">
                Manage Campuses
            </a>
        </div>
    </div>

    <!-- Batches Table -->
    <div class="lb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Batch</th>
                        <th class="py-3 px-4">Course</th>
                        <th class="py-3 px-4">Campus Location</th>
                        <th class="py-3 px-4">Capacity</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse($batches as $batch)
                    <tr class="hover:bg-[#FFFDFB] transition-colors">
                        <td class="py-3.5 px-5 font-bold text-[#0A0A0A]">
                            {{ $batch->batch_name }} ({{ $batch->batch_code }})
                        </td>
                        <td class="py-3.5 px-4 text-[#6B6B6B] max-w-xs truncate">
                            {{ $batch->course->title ?? 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4 text-[#0A0A0A]">
                            {{ $batch->physicalLocation->name ?? 'Online / Remote' }}
                        </td>
                        <td class="py-3.5 px-4 text-xs font-semibold text-[#6B6B6B]">
                            {{ $batch->enrollments->count() }} / {{ $batch->capacity ?? 'Unlimited' }} seats
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#DEF7EC] text-[#03543F]">
                                {{ ucfirst($batch->status ?? 'active') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-[#6B6B6B]">
                            <div class="text-3xl mb-2">🏫</div>
                            <div class="font-bold text-[#0A0A0A]">No batches scheduled</div>
                            <div class="text-xs text-[#6B6B6B] mt-1">Configure physical locations and batches to start admitting physical students.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
        <div class="p-4 border-t border-[#E5E5E5]">
            {{ $batches->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
