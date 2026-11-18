@extends('layouts.admin')

@section('title', 'Users & Security')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">User &amp; Security</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Manage platform student accounts, instructor credentials, and access roles.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.approvals.index') }}"
               class="px-4 py-2.5 rounded-xl border border-[#E5E5E5] bg-white hover:bg-[#F7F7F5] text-[#0A0A0A] text-sm font-semibold transition-all flex items-center gap-2 shadow-xs">
                <i class="fas fa-user-check text-xs text-purple-600"></i>
                <span>Pending Approvals</span>
                @if(($stats['pending_approvals'] ?? 0) > 0)
                    <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-bold">
                        {{ $stats['pending_approvals'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.users.create') }}"
               class="px-4 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-sm font-bold shadow-md shadow-orange-500/25 transition-all flex items-center gap-2">
                <i class="fas fa-user-plus text-xs"></i>
                <span>Add New User</span>
            </a>
        </div>
    </div>

    <!-- Status & Error Alerts -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
            <i class="fas fa-check-circle text-emerald-500 text-lg shrink-0 mt-0.5"></i>
            <span class="font-medium">{{ session('status') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
            <i class="fas fa-exclamation-circle text-rose-500 text-lg shrink-0 mt-0.5"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- KPI Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="lb-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Total Users</p>
                <p class="text-2xl font-black text-[#0A0A0A] mt-1">{{ number_format($stats['total_users'] ?? 0) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Registered accounts</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-neutral-100 text-[#0A0A0A] flex items-center justify-center shrink-0">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>

        <div class="lb-card p-5 flex items-center justify-between border-l-4 border-l-[#FF5F00]">
            <div>
                <p class="text-xs font-bold text-[#FF5F00] uppercase tracking-wider">Instructors</p>
                <p class="text-2xl font-black text-[#0A0A0A] mt-1">{{ number_format($stats['instructors_count'] ?? 0) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Verified faculty members</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#FF5F00] flex items-center justify-center shrink-0">
                <i class="fas fa-chalkboard-teacher text-lg"></i>
            </div>
        </div>

        <div class="lb-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Students</p>
                <p class="text-2xl font-black text-[#0A0A0A] mt-1">{{ number_format($stats['students_count'] ?? 0) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Enrolled learners</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fas fa-graduation-cap text-lg"></i>
            </div>
        </div>

        <div class="lb-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Administrators</p>
                <p class="text-2xl font-black text-[#0A0A0A] mt-1">{{ number_format($stats['admins_count'] ?? 0) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Platform managers</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <i class="fas fa-user-shield text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Search & Role Filters Bar -->
    <div class="lb-card p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#6B6B6B]"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name, email, or phone number..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:bg-white outline-none transition-colors">
            </div>

            <select name="role"
                    class="w-full sm:w-48 px-3 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none transition-colors">
                <option value="">All Roles</option>
                <option value="instructor" {{ request('role') === 'instructor' ? 'selected' : '' }}>Instructors</option>
                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Students</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrators</option>
            </select>

            <button type="submit"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#0A0A0A] hover:bg-neutral-800 text-white text-sm font-bold transition shadow-xs">
                Filter
            </button>

            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}"
                   class="text-xs text-[#6B6B6B] hover:text-[#FF5F00] whitespace-nowrap font-semibold">
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="lb-card overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">User</th>
                        <th class="py-3 px-4">Phone</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Registered</th>
                        <th class="py-3 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse($users as $user)
                    <tr class="hover:bg-[#FFFDFB] transition-colors">
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#1A1A1A] text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-[#0A0A0A] flex items-center gap-2">
                                        <span>{{ $user->name }}</span>
                                        @if($user->id === auth()->id())
                                            <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-neutral-200 text-neutral-700 font-bold uppercase">You</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-[#6B6B6B]">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-[#6B6B6B] text-xs font-mono">{{ $user->phone ?? '—' }}</td>
                        <td class="py-3.5 px-4">
                            @foreach($user->roles as $role)
                                @php
                                    $roleName = strtolower($role->name);
                                    $badgeStyle = match($roleName) {
                                        'admin' => 'bg-purple-100 text-purple-700 border-purple-200/60',
                                        'instructor' => 'bg-orange-100 text-orange-700 border-orange-200/60 font-bold',
                                        default => 'bg-neutral-100 text-neutral-700 border-neutral-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $badgeStyle }} mr-1">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-1.5">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#DEF7EC] text-[#03543F]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Inactive
                                    </span>
                                @endif

                                @if($user->is_approved)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200" title="Approved for teaching and full platform access">
                                        Approved
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-xs text-[#6B6B6B] font-medium">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}
                        </td>
                        <td class="py-3.5 px-5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="p-2 rounded-xl text-neutral-500 hover:text-[#FF5F00] hover:bg-orange-50 transition"
                                   title="Edit user details and roles">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>

                                @if($user->id !== auth()->id())
                                    <form id="delete-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                data-item="{{ $user->name }} ({{ $user->email }})"
                                                data-form="delete-user-{{ $user->id }}"
                                                onclick="confirmDialog({
                                                    title: 'Delete User Account?',
                                                    itemName: this.dataset.item,
                                                    message: 'Are you sure you want to permanently delete this user account? All course assignments and user records will be affected.',
                                                    targetForm: document.getElementById(this.dataset.form),
                                                    confirmText: 'Delete User',
                                                    confirmVariant: 'danger',
                                                    icon: 'fas fa-trash-alt'
                                                })"
                                                class="p-2 rounded-xl text-neutral-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                title="Delete user">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-[#6B6B6B]">
                            <div class="w-12 h-12 rounded-2xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-users-slash text-xl"></i>
                            </div>
                            <div class="font-bold text-[#0A0A0A]">No users match your criteria</div>
                            <p class="text-xs text-[#6B6B6B] mt-1">Try adjusting your search terms or role filters.</p>
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
