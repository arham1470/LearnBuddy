@extends('layouts.admin')

@section('title', 'Users & Security')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">User &amp; Security</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Manage platform student accounts, instructor credentials, and access roles.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('management.users.approvals') }}"
               class="px-4 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-sm font-bold shadow-md shadow-orange-500/25 transition-all">
                Pending Approvals
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="lb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">User</th>
                        <th class="py-3 px-4">Phone</th>
                        <th class="py-3 px-4">Roles</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-5 text-right">Registered</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse($users as $user)
                    <tr class="hover:bg-[#FFFDFB] transition-colors">
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#1A1A1A] text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-[#0A0A0A]">{{ $user->name }}</div>
                                    <div class="text-xs text-[#6B6B6B]">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-[#6B6B6B]">{{ $user->phone ?? 'N/A' }}</td>
                        <td class="py-3.5 px-4">
                            @foreach($user->roles as $role)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#FFF0E6] text-[#FF5F00] border border-orange-200/50 mr-1">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="py-3.5 px-4">
                            @if($user->is_active)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#DEF7EC] text-[#03543F]">Active</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-right text-xs text-[#6B6B6B] font-medium">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-[#6B6B6B]">
                            <div class="text-3xl mb-2">👤</div>
                            <div class="font-bold text-[#0A0A0A]">No users found</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-[#E5E5E5]">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
