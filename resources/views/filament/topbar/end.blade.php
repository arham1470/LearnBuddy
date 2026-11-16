@php
    $user = auth()->user();
    $roleName = $user?->isAdmin() ? 'Super Admin' : ($user?->isInstructor() ? 'Instructor' : 'Staff');
@endphp

<div class="lb-topbar-end-ctn" style="display: inline-flex; align-items: center; gap: 0.625rem; flex-shrink: 0;">
    {{-- Notification Icon Button with Indicator Dot --}}
    <div class="lb-notif-dropdown-ctn" x-data="{ open: false }" style="position: relative;">
        <button
            type="button"
            class="lb-topbar-icon-btn"
            @click="open = !open"
            @click.outside="open = false"
            title="Notifications"
            aria-label="View notifications"
            style="width: 2.25rem; height: 2.25rem; min-width: 2.25rem; min-height: 2.25rem; border-radius: 9999px; background-color: #F7F7F5; border: 1px solid #E5E5E5; color: #6B6B6B; cursor: pointer; position: relative; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
        >
            <svg class="lb-bell-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px; min-width: 18px; min-height: 18px; max-width: 18px; max-height: 18px; display: block;">
                <path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 0 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z" clip-rule="evenodd" />
            </svg>
            {{-- Notification pulse indicator --}}
            <span class="lb-notif-badge">
                <span class="lb-notif-ping"></span>
                <span class="lb-notif-dot"></span>
            </span>
        </button>

        {{-- Quick Notification Preview Dropdown --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 transform scale-95 -translate-y-1"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 transform scale-95 -translate-y-1"
            class="lb-notif-dropdown-menu"
            style="display: none;"
        >
            <div class="lb-notif-header">
                <span class="lb-notif-title">Notifications</span>
                <span class="lb-notif-count">2 New</span>
            </div>
            <div class="lb-notif-list">
                <div class="lb-notif-item">
                    <div class="lb-notif-item-icon bg-orange">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: block;">
                            <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
                        </svg>
                    </div>
                    <div class="lb-notif-item-body">
                        <p class="lb-notif-item-text"><strong>New Enrollment</strong> in Master Course</p>
                        <span class="lb-notif-item-time">5 mins ago</span>
                    </div>
                </div>
                <div class="lb-notif-item">
                    <div class="lb-notif-item-icon bg-emerald">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: block;">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="lb-notif-item-body">
                        <p class="lb-notif-item-text"><strong>Payment settled</strong>: LKR 4,500.00</p>
                        <span class="lb-notif-item-time">1 hour ago</span>
                    </div>
                </div>
            </div>
            <a href="/admin" class="lb-notif-footer" @click="open = false">
                View all notifications &rarr;
            </a>
        </div>
    </div>

    {{-- View Storefront Action Button --}}
    <a
        href="{{ url('/') }}"
        target="_blank"
        rel="noopener noreferrer"
        class="lb-topbar-storefront-btn group"
        title="Open student-facing storefront in new tab"
        style="height: 2.25rem; padding: 0 0.875rem; border-radius: 9999px; background: #FFFFFF; border: 1px solid #E5E5E5; color: #0A0A0A; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.375rem; white-space: nowrap; text-decoration: none;"
    >
        <span>View Storefront</span>
        <svg class="lb-storefront-icon" width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: block; color: #6B6B6B;">
            <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Z" clip-rule="evenodd" />
            <path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 0 0 1.06.053L16.5 4.44v2.81a.75.75 0 0 0 1.5 0v-4.5a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0 0 1.5h2.553l-9.056 8.194a.75.75 0 0 0-.053 1.06Z" clip-rule="evenodd" />
        </svg>
    </a>

    {{-- Super Admin Role Pill Badge --}}
    <div class="lb-topbar-role-badge" style="height: 2.25rem; padding: 0 0.875rem; border-radius: 9999px; background: #FFF0E6; border: 1px solid rgba(255, 95, 0, 0.25); color: #FF5F00; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.375rem; white-space: nowrap;">
        <span class="lb-role-pulse-dot" style="width: 6px; height: 6px; min-width: 6px; min-height: 6px; border-radius: 9999px; background-color: #FF5F00; display: inline-block;"></span>
        <span class="lb-role-text">{{ $roleName }}</span>
    </div>

    {{-- Quick Logout Button with explicit SVG constraints --}}
    <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="inline-flex shrink-0 m-0 p-0" style="display: inline-flex; margin: 0; padding: 0;">
        @csrf
        <button
            type="submit"
            class="lb-topbar-logout-btn"
            title="Sign out of LearnBuddy"
            aria-label="Sign out"
            style="width: 2.25rem; height: 2.25rem; min-width: 2.25rem; min-height: 2.25rem; border-radius: 9999px; background-color: #F7F7F5; border: 1px solid #E5E5E5; color: #6B6B6B; cursor: pointer; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
        >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: block;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </button>
    </form>
</div>

<style>
    .lb-topbar-icon-btn:hover {
        background-color: #FFF0E6 !important;
        border-color: rgba(255, 95, 0, 0.3) !important;
        color: #FF5F00 !important;
        transform: translateY(-1px);
    }

    /* Notification Dot & Ping */
    .lb-notif-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        display: flex;
        width: 8px;
        height: 8px;
    }

    .lb-notif-ping {
        position: absolute;
        display: inline-flex;
        height: 100%;
        width: 100%;
        border-radius: 9999px;
        background-color: #FF5F00;
        opacity: 0.75;
        animation: lbPing 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    .lb-notif-dot {
        position: relative;
        display: inline-flex;
        border-radius: 9999px;
        width: 8px;
        height: 8px;
        background-color: #FF5F00;
        border: 1.5px solid #FFFFFF;
    }

    @keyframes lbPing {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }

    /* Notification Dropdown Panel */
    .lb-notif-dropdown-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 290px;
        background: #FFFFFF;
        border: 1px solid #E5E5E5;
        border-radius: 14px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.12);
        padding: 0;
        z-index: 50;
        overflow: hidden;
    }

    .lb-notif-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: #F7F7F5;
        border-bottom: 1px solid #E5E5E5;
    }

    .lb-notif-title {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #0A0A0A;
    }

    .lb-notif-count {
        font-size: 0.6875rem;
        font-weight: 700;
        color: #FF5F00;
        background: #FFF0E6;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        border: 1px solid rgba(255, 95, 0, 0.2);
    }

    .lb-notif-list {
        padding: 0.5rem 0;
        display: flex;
        flex-direction: column;
    }

    .lb-notif-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.625rem 1rem;
        transition: background-color 0.15s ease;
    }

    .lb-notif-item:hover {
        background-color: #F7F7F5;
    }

    .lb-notif-item-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .lb-notif-item-icon.bg-orange {
        background: #FFF0E6;
        color: #FF5F00;
    }

    .lb-notif-item-icon.bg-emerald {
        background: #ECFDF5;
        color: #10B981;
    }

    .lb-notif-item-body {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
        min-width: 0;
    }

    .lb-notif-item-text {
        font-size: 0.75rem;
        color: #0A0A0A;
        line-height: 1.35;
        margin: 0;
    }

    .lb-notif-item-time {
        font-size: 0.6875rem;
        color: #6B6B6B;
    }

    .lb-notif-footer {
        display: block;
        text-align: center;
        padding: 0.625rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #FF5F00;
        background: #F7F7F5;
        border-top: 1px solid #E5E5E5;
        text-decoration: none;
        transition: background-color 0.15s ease;
    }

    .lb-notif-footer:hover {
        background: #FFF0E6;
    }

    .lb-topbar-storefront-btn:hover {
        border-color: #FF5F00 !important;
        color: #FF5F00 !important;
        background: #FFFFFF !important;
        box-shadow: 0 2px 8px rgba(255, 95, 0, 0.12) !important;
        transform: translateY(-1px);
    }

    .lb-topbar-storefront-btn:hover .lb-storefront-icon {
        color: #FF5F00 !important;
    }

    .lb-topbar-logout-btn:hover {
        background-color: #FFF1F2 !important;
        border-color: rgba(225, 29, 72, 0.3) !important;
        color: #E11D48 !important;
        transform: scale(1.05);
    }

    @media (max-width: 639px) {
        .lb-topbar-role-badge {
            display: none !important;
        }

        .lb-topbar-storefront-btn span {
            display: none !important;
        }

        .lb-topbar-storefront-btn {
            padding: 0 !important;
            width: 2.25rem !important;
            justify-content: center !important;
        }
    }
</style>
