@php
    $user = auth()->user();
    $roleName = $user?->isAdmin() ? 'Super Admin' : ($user?->isInstructor() ? 'Instructor' : 'Staff');
@endphp

<div class="flex items-center gap-3 shrink-0">
    <!-- View Storefront Action Button (Matches Website Pill Style) -->
    <a
        href="{{ url('/') }}"
        target="_blank"
        rel="noopener noreferrer"
        class="lb-topbar-btn whitespace-nowrap flex-nowrap shrink-0 group"
        title="Open student-facing storefront in new tab"
    >
        <span>View Storefront</span>
        <svg class="w-3.5 h-3.5 text-neutral-400 group-hover:text-[#FF5F00] transition-colors shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
    </a>

    <!-- Role Badge Pill (LearnBuddy Orange Accent) -->
    <div class="hidden sm:inline-flex lb-topbar-badge lb-topbar-badge-primary whitespace-nowrap shrink-0">
        <span class="w-1.5 h-1.5 rounded-full bg-[#FF5F00] shrink-0"></span>
        <span>{{ $roleName }}</span>
    </div>

    <!-- Quick Sign-Out Icon Button (Matches Website Header) -->
    <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="inline-flex shrink-0 m-0 p-0">
        @csrf
        <button
            type="submit"
            class="lb-quick-logout-btn shrink-0"
            title="Sign out of LearnBuddy"
            aria-label="Sign out"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </button>
    </form>
</div>
