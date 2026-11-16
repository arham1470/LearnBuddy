@php
    $createCourseUrl = class_exists(\App\Filament\Resources\CourseResource::class)
        ? \App\Filament\Resources\CourseResource::getUrl('create')
        : url('/admin/courses/create');
@endphp

<div class="lb-sidebar-promo-ctn" x-show="$store.sidebar.isOpen" x-cloak>
    <div class="lb-sidebar-promo">
        <div class="lb-promo-header">
            <div class="lb-promo-icon-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: block;">
                    <path fill-rule="evenodd" d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 0 1 .75.75c0 5.056-2.383 9.555-6.084 12.436A6.75 6.75 0 0 1 9.75 22.5a.75.75 0 0 1-.75-.75v-4.131A15.838 15.838 0 0 1 6.382 15H2.25a.75.75 0 0 1-.75-.75 6.75 6.75 0 0 1 7.815-6.666ZM15 6.75a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z" clip-rule="evenodd" />
                    <path d="M5.26 17.242a.75.75 0 1 0-.897-1.203 5.243 5.243 0 0 0-2.05 5.022.75.75 0 0 0 .625.627 5.243 5.243 0 0 0 5.022-2.051.75.75 0 1 0-1.202-.897 3.744 3.744 0 0 1-1.498 1.498Z" />
                </svg>
            </div>
            <span class="lb-promo-tag">Fast Launch</span>
        </div>

        <h4 class="lb-sidebar-promo-title">Create &amp; Manage Courses</h4>
        <p class="lb-sidebar-promo-subtitle">Build modules, lessons, and launch your next intake seamlessly.</p>

        <a href="{{ $createCourseUrl }}" class="lb-sidebar-promo-btn">
            <svg class="lb-promo-btn-icon" width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: block;">
                <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
            </svg>
            <span>New Course</span>
        </a>
    </div>
</div>

<style>
    .lb-sidebar-promo-ctn {
        padding: 0.5rem 0.625rem 1rem;
        box-sizing: border-box;
    }

    /* Completely hide promo container when sidebar is collapsed */
    .fi-sidebar:not(.fi-sidebar-open) .lb-sidebar-promo-ctn,
    aside.fi-sidebar:not(.fi-sidebar-open) .lb-sidebar-promo-ctn,
    div.fi-sidebar:not(.fi-sidebar-open) .lb-sidebar-promo-ctn,
    #fi-main-sidebar:not(.fi-sidebar-open) .lb-sidebar-promo-ctn,
    [data-sidebar-collapsed="true"] .lb-sidebar-promo-ctn,
    .fi-sidebar-collapsed .lb-sidebar-promo-ctn {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden !important;
        pointer-events: none !important;
    }

    .lb-sidebar-promo {
        padding: 1.125rem;
        background: linear-gradient(180deg, #242424 0%, #1E1E1E 100%);
        border: 1px solid rgba(255, 95, 0, 0.25);
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        position: relative;
        overflow: hidden;
        transition: border-color 0.2s ease, transform 0.2s ease;
    }

    .lb-sidebar-promo:hover {
        border-color: rgba(255, 95, 0, 0.5);
        transform: translateY(-1px);
    }

    /* Orange glowing top border */
    .lb-sidebar-promo::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #FF5F00 50%, transparent);
    }

    .lb-promo-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.625rem;
    }

    .lb-promo-icon-box {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: rgba(255, 95, 0, 0.15);
        border: 1px solid rgba(255, 95, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FF5F00;
    }

    .lb-promo-icon-box svg {
        width: 16px;
        height: 16px;
    }

    .lb-promo-tag {
        font-size: 0.625rem; /* 10px */
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #FF5F00;
        background: rgba(255, 95, 0, 0.12);
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        border: 1px solid rgba(255, 95, 0, 0.25);
    }

    .lb-sidebar-promo-title {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #FFFFFF;
        margin: 0 0 0.25rem 0;
        line-height: 1.3;
    }

    .lb-sidebar-promo-subtitle {
        font-size: 0.6875rem;
        color: rgba(255, 255, 255, 0.55);
        margin: 0 0 0.875rem 0;
        line-height: 1.4;
    }

    .lb-sidebar-promo-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        width: 100%;
        padding: 0.5rem 0.875rem;
        background-color: #FF5F00;
        color: #FFFFFF !important;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        border-radius: 10px;
        text-decoration: none !important;
        border: none;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(255, 95, 0, 0.4);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-sizing: border-box;
    }

    .lb-sidebar-promo-btn:hover {
        background-color: #e55400;
        box-shadow: 0 4px 14px rgba(255, 95, 0, 0.55);
        transform: translateY(-1px);
        color: #FFFFFF !important;
    }

    .lb-sidebar-promo-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(255, 95, 0, 0.3);
    }

    .lb-promo-btn-icon {
        width: 14px;
        height: 14px;
    }

    /* Auto-hide promotional card when sidebar is collapsed on desktop */
    .fi-sidebar-nav-groups[data-sidebar-open="false"] .lb-sidebar-promo-ctn,
    [data-sidebar-collapsed="true"] .lb-sidebar-promo-ctn,
    .fi-sidebar.fi-sidebar-collapsible-on-desktop:not([data-open]) .lb-sidebar-promo-ctn {
        display: none !important;
    }
</style>
