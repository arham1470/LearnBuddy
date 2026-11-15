<x-layouts.public title="User Approvals - LearnBuddy Administration" description="Review and approve newly registered student accounts.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-lb-orange">Home</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">User Approvals</span>
            </div>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold tracking-wide uppercase mb-2">
                        <i class="fas fa-shield-alt text-xs" aria-hidden="true"></i> Administration Security
                    </div>
                    <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Student Registration Approvals</h1>
                    <p class="text-sm text-neutral-600 mt-1">
                        Review newly registered students whose emails have been verified with 6-digit OTP codes and approve access.
                    </p>
                </div>
            </div>

            <!-- Status Flash Alerts -->
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- KPI Stats Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-5 rounded-3xl bg-white border border-neutral-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Awaiting Approval</p>
                        <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['pending_count']) }}</p>
                        <p class="text-[11px] text-neutral-400 mt-0.5">Verified OTP, awaiting review</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-user-clock" aria-hidden="true"></i>
                    </div>
                </div>

                <div class="p-5 rounded-3xl bg-white border border-neutral-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Approved Today</p>
                        <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['approved_today']) }}</p>
                        <p class="text-[11px] text-neutral-400 mt-0.5">Activated during {{ today()->format('M d') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-user-check" aria-hidden="true"></i>
                    </div>
                </div>

                <div class="p-5 rounded-3xl bg-white border border-neutral-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Total Approved Users</p>
                        <p class="text-2xl font-black text-lb-black mt-1">{{ number_format($stats['total_approved']) }}</p>
                        <p class="text-[11px] text-neutral-400 mt-0.5">Active platform accounts</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-neutral-100 text-neutral-700 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Toolbar -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-4 sm:p-5">
                <form method="GET" action="{{ route('management.users.approvals') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                            <i class="fas fa-search text-xs" aria-hidden="true"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search pending users by name, email, or phone..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                        >
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            class="py-2.5 px-5 rounded-xl bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-semibold transition cursor-pointer"
                        >
                            Search
                        </button>
                        @if (request('search'))
                            <a
                                href="{{ route('management.users.approvals') }}"
                                class="py-2.5 px-4 rounded-xl border border-neutral-200 hover:bg-neutral-100 text-neutral-600 text-xs font-semibold transition"
                            >
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Approvals Table Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                @if ($users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-neutral-50/80 border-b border-neutral-200/80 text-[11px] font-bold text-neutral-600 uppercase tracking-wider">
                                    <th scope="col" class="py-3.5 px-5">User</th>
                                    <th scope="col" class="py-3.5 px-5">Contact Details</th>
                                    <th scope="col" class="py-3.5 px-5">Location</th>
                                    <th scope="col" class="py-3.5 px-5">OTP Verified</th>
                                    <th scope="col" class="py-3.5 px-5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                @foreach ($users as $pendingUser)
                                    <tr class="hover:bg-neutral-50/60 transition">
                                        <!-- User Profile -->
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-lb-orange/10 text-lb-orange flex items-center justify-center font-bold text-sm shrink-0 border border-orange-200/60">
                                                    {{ strtoupper(substr($pendingUser->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-neutral-900 text-sm">{{ $pendingUser->name }}</p>
                                                    <div class="flex items-center gap-1.5 mt-0.5">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-100 text-lb-orange">
                                                            Pending Approval
                                                        </span>
                                                        @if ($pendingUser->roles->isNotEmpty())
                                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-medium bg-neutral-100 text-neutral-600">
                                                                {{ $pendingUser->roles->pluck('display_name')->first() ?? 'Student' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Contact -->
                                        <td class="py-4 px-5">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1.5 text-neutral-800 font-medium">
                                                    <i class="fas fa-envelope text-neutral-400 text-[11px]" aria-hidden="true"></i>
                                                    <span>{{ $pendingUser->email }}</span>
                                                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded-full" title="Email OTP verified">
                                                        <i class="fas fa-check-circle text-[9px]" aria-hidden="true"></i> Verified
                                                    </span>
                                                </div>
                                                @if ($pendingUser->phone)
                                                    <div class="flex items-center gap-1.5 text-neutral-500 text-[11px]">
                                                        <i class="fas fa-phone text-neutral-400 text-[10px]" aria-hidden="true"></i>
                                                        <span>{{ $pendingUser->formattedPhone() ?? $pendingUser->phone }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Location -->
                                        <td class="py-4 px-5 text-neutral-600">
                                            @if ($pendingUser->city || $pendingUser->district || $pendingUser->province)
                                                <p class="font-medium text-neutral-800">{{ $pendingUser->city ?? 'N/A' }}</p>
                                                <p class="text-[11px] text-neutral-500">{{ implode(', ', array_filter([$pendingUser->district, $pendingUser->province])) }}</p>
                                            @else
                                                <span class="text-neutral-400 italic">Not provided</span>
                                            @endif
                                        </td>

                                        <!-- Verification Time -->
                                        <td class="py-4 px-5 text-neutral-600">
                                            <p class="font-medium text-neutral-800">{{ $pendingUser->email_verified_at?->diffForHumans() ?? 'Pending' }}</p>
                                            <p class="text-[11px] text-neutral-400">{{ $pendingUser->email_verified_at?->format('Y-m-d H:i') ?? '-' }}</p>
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-4 px-5 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <!-- Approve Button (Modern Dialog) -->
                                                <button
                                                    type="button"
                                                    data-item="{{ $pendingUser->name }} ({{ $pendingUser->email }})"
                                                    data-action="{{ route('management.users.approve', $pendingUser) }}"
                                                    onclick="confirmDialog({
                                                        title: 'Approve User Account',
                                                        itemName: this.dataset.item,
                                                        message: 'Approving will activate this student account and immediately dispatch a welcome confirmation email via Mailjet.',
                                                        action: this.dataset.action,
                                                        method: 'POST',
                                                        confirmText: 'Approve Account',
                                                        confirmVariant: 'success',
                                                        icon: 'fas fa-user-check'
                                                    })"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                                                    title="Approve and notify user"
                                                >
                                                    <i class="fas fa-check text-[10px]" aria-hidden="true"></i>
                                                    <span>Approve</span>
                                                </button>

                                                <!-- Reject Button (Modern Dialog) -->
                                                <button
                                                    type="button"
                                                    data-item="{{ $pendingUser->name }} ({{ $pendingUser->email }})"
                                                    data-action="{{ route('management.users.reject', $pendingUser) }}"
                                                    onclick="confirmDialog({
                                                        title: 'Reject User Registration',
                                                        itemName: this.dataset.item,
                                                        message: 'Are you sure you want to reject this registration? The user account will be deactivated and removed from pending reviews.',
                                                        action: this.dataset.action,
                                                        method: 'POST',
                                                        confirmText: 'Reject Registration',
                                                        confirmVariant: 'danger',
                                                        icon: 'fas fa-user-times'
                                                    })"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-neutral-200 hover:border-rose-300 hover:bg-rose-50 text-neutral-600 hover:text-rose-600 font-semibold text-xs transition cursor-pointer"
                                                    title="Reject registration"
                                                >
                                                    <i class="fas fa-times text-[10px]" aria-hidden="true"></i>
                                                    <span>Reject</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($users->hasPages())
                        <div class="p-4 sm:p-5 border-t border-neutral-100 bg-neutral-50/40">
                            {{ $users->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="py-16 px-6 text-center">
                        <div class="w-16 h-16 mx-auto rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4">
                            <i class="fas fa-check-double" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-neutral-900">No Pending Approvals</h3>
                        <p class="text-xs text-neutral-500 mt-1 max-w-sm mx-auto">
                            All registered students with verified email addresses have been reviewed and approved.
                        </p>
                        @if (request('search'))
                            <div class="mt-4">
                                <a
                                    href="{{ route('management.users.approvals') }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition"
                                >
                                    <i class="fas fa-undo text-[10px]" aria-hidden="true"></i>
                                    <span>Clear search filters</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
