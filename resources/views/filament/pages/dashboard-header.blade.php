@php
    $hour = (int) date('H');
    $greeting = $hour < 12 ? 'Morning' : ($hour < 17 ? 'Afternoon' : 'Evening');
    $dateFormatted = now()->format('M d, Y');
@endphp

<div class="lb-dash-header" x-data="{
    open: false,
    selectedPeriod: 'Today · {{ $dateFormatted }}',
    periods: [
        'Today · {{ $dateFormatted }}',
        'Last 7 Days',
        'Last 30 Days',
        'This Month ({{ now()->format('M Y') }})',
        'Year to Date ({{ now()->format('Y') }})',
        'All Time'
    ]
}">
    {{-- Left: greeting + title + description --}}
    <div class="lb-dash-header__left">
        <div class="lb-dash-header__greeting-pill">
            <span class="lb-greeting-dot"></span>
            <span class="lb-dash-header__greeting">Good {{ $greeting }}, Super Admin 👋</span>
        </div>
        <h1 class="lb-dash-header__title">Dashboard</h1>
        <p class="lb-dash-header__subtitle">Here's what's happening with your learning platform today.</p>
    </div>

    {{-- Right: date range selector dropdown --}}
    <div class="lb-dash-header__right">
        <div class="lb-date-dropdown-ctn" @click.outside="open = false">
            <button
                type="button"
                class="lb-date-selector-btn"
                @click="open = !open"
                :aria-expanded="open"
                aria-haspopup="true"
                title="Select date range"
            >
                <div class="lb-date-btn-left">
                    <svg class="lb-cal-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: block;">
                        <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd" />
                    </svg>
                    <span class="lb-date-text" x-text="selectedPeriod">Today · {{ $dateFormatted }}</span>
                </div>
                <svg class="lb-chevron-icon" :class="{ 'lb-chevron-rotated': open }" width="16" height="16" viewBox="0 0 20 20" fill="currentColor" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: block;">
                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </button>

            {{-- Dropdown Menu --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 transform scale-95 -translate-y-1"
                x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 transform scale-95 -translate-y-1"
                class="lb-date-dropdown-menu"
                style="display: none;"
            >
                <div class="lb-dropdown-header-label">Time Range</div>
                <template x-for="item in periods" :key="item">
                    <button
                        type="button"
                        class="lb-dropdown-item"
                        :class="{ 'lb-dropdown-item-active': selectedPeriod === item }"
                        @click="selectedPeriod = item; open = false"
                    >
                        <span x-text="item"></span>
                        <svg x-show="selectedPeriod === item" class="lb-check-icon" width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: block;">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    .lb-dash-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        padding-top: 0.5rem;
        padding-bottom: 1.75rem;
        gap: 1.25rem;
        flex-wrap: wrap;
        width: 100%;
        box-sizing: border-box;
    }

    .lb-dash-header__left {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        max-width: 600px;
    }

    .lb-dash-header__greeting-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        align-self: flex-start;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        background: #FFF0E6;
        border: 1px solid rgba(255, 95, 0, 0.22);
    }

    .lb-greeting-dot {
        width: 6px;
        height: 6px;
        border-radius: 9999px;
        background-color: #FF5F00;
    }

    .lb-dash-header__greeting {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #FF5F00;
        letter-spacing: 0.01em;
        margin: 0;
        line-height: 1;
    }

    .lb-dash-header__title {
        font-size: 2rem; /* 32px */
        font-weight: 800;
        color: #0A0A0A;
        line-height: 1.15;
        letter-spacing: -0.03em;
        margin: 0.15rem 0 0;
    }

    .lb-dash-header__subtitle {
        font-size: 0.875rem;
        color: #6B6B6B;
        margin: 0;
        line-height: 1.45;
    }

    .lb-dash-header__right {
        display: flex;
        align-items: center;
        flex-shrink: 0;
        margin-left: auto;
    }

    .lb-date-dropdown-ctn {
        position: relative;
    }

    .lb-date-selector-btn {
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        background: #FFFFFF;
        border: 1px solid #E5E5E5;
        border-radius: 12px;
        padding: 0.5625rem 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        cursor: pointer;
        transition: all 0.2s ease;
        outline: none;
    }

    .lb-date-selector-btn:hover {
        border-color: #FF5F00;
        box-shadow: 0 2px 8px rgba(255, 95, 0, 0.12);
        transform: translateY(-1px);
    }

    .lb-date-btn-left {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .lb-cal-icon {
        width: 16px;
        height: 16px;
        color: #FF5F00;
        flex-shrink: 0;
    }

    .lb-date-text {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #0A0A0A;
        white-space: nowrap;
    }

    .lb-chevron-icon {
        width: 16px;
        height: 16px;
        color: #6B6B6B;
        transition: transform 0.2s ease;
    }

    .lb-chevron-rotated {
        transform: rotate(180deg);
        color: #FF5F00;
    }

    .lb-date-dropdown-menu {
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        z-index: 50;
        min-width: 220px;
        background: #FFFFFF;
        border: 1px solid #E5E5E5;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 0.375rem;
        animation: lbFadeIn 0.15s ease-out;
    }

    .lb-dropdown-header-label {
        font-size: 0.6875rem;
        font-weight: 700;
        color: #6B6B6B;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.5rem 0.75rem 0.25rem;
    }

    .lb-dropdown-item {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: #0A0A0A;
        font-size: 0.8125rem;
        font-weight: 500;
        text-align: left;
        cursor: pointer;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .lb-dropdown-item:hover {
        background-color: #FFF0E6;
        color: #FF5F00;
    }

    .lb-dropdown-item-active {
        background-color: #FFF0E6;
        color: #FF5F00;
        font-weight: 700;
    }

    .lb-check-icon {
        width: 14px;
        height: 14px;
        color: #FF5F00;
    }

    @media (max-width: 639px) {
        .lb-dash-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .lb-dash-header__right {
            margin-left: 0;
            width: 100%;
        }

        .lb-date-dropdown-ctn,
        .lb-date-selector-btn {
            width: 100%;
        }
    }
</style>
