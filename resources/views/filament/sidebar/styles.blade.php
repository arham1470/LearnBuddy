<style>
    /* ============================================================
       LearnBuddy — Outer Admin Shell & Dark Modern Sidebar (#1A1A1A)
       Filament v5 Custom Design System
       ============================================================ */

    /* ── Accessible Skip Link (.fi-skip-link, .fi-sr-only) ── */
    .fi-skip-link,
    .fi-sr-only,
    a.fi-skip-link {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border-width: 0 !important;
    }

    .fi-skip-link:focus,
    .fi-sr-only:focus,
    a.fi-skip-link:focus {
        position: fixed !important;
        top: 1rem !important;
        left: 1rem !important;
        z-index: 9999 !important;
        width: auto !important;
        height: auto !important;
        padding: 0.625rem 1.25rem !important;
        margin: 0 !important;
        overflow: visible !important;
        clip: auto !important;
        white-space: normal !important;
        background-color: #FF5F00 !important;
        color: #FFFFFF !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 0.875rem !important;
        text-decoration: none !important;
        box-shadow: 0 4px 16px rgba(255, 95, 0, 0.4) !important;
        outline: 2px solid #FFFFFF !important;
        outline-offset: 2px !important;
    }

    /* ── Outer Admin Shell: Flex Layout (min-h-screen flex) ── */
    html,
    body,
    .fi-body {
        min-height: 100vh !important;
        background-color: #F7F7F5 !important;
    }

    /* .fi-layout is the master flex container holding the sidebar and the main content side-by-side */
    .fi-layout {
        display: flex !important;
        flex-direction: row !important;
        min-height: 100vh !important;
        width: 100% !important;
        max-width: 100vw !important;
        position: relative !important;
        overflow-x: clip !important;
        align-items: stretch !important;
        box-sizing: border-box !important;
    }

    /* Livewire component wrapper for sidebar inside .fi-layout */
    .fi-layout > div:has(.fi-sidebar),
    .fi-layout > [wire\:id]:has(.fi-sidebar),
    .fi-layout > div:first-child:not(.fi-main-ctn):not(.fi-sidebar-close-overlay) {
        flex-shrink: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        width: auto !important;
    }

    /* ── Main Sidebar Shell (covers both <aside> and <div>) ── */
    aside.fi-sidebar,
    div.fi-sidebar,
    .fi-sidebar,
    #fi-main-sidebar {
        background-color: #1A1A1A !important;
        border-right: 1px solid rgba(255, 255, 255, 0.06) !important;
        min-height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
        flex-shrink: 0 !important; /* shrink-0 */
        box-sizing: border-box !important;
        transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), min-width 0.25s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        overflow-x: hidden !important;
        z-index: 20 !important;
    }

    /* ── Main Content Container (.fi-main-ctn) Sits Beside Sidebar ── */
    .fi-main-ctn {
        flex: 1 1 0% !important;
        min-width: 0 !important;
        width: auto !important;
        display: flex !important;
        flex-direction: column !important;
        min-height: 100vh !important;
        background-color: #F7F7F5 !important;
        box-sizing: border-box !important;
        opacity: 1 !important;
    }

    main.fi-main,
    .fi-main {
        flex: 1 1 auto !important;
        width: 100% !important;
        max-width: 100% !important;
        background-color: #F7F7F5 !important;
        box-sizing: border-box !important;
    }

    /* ── Desktop Sidebar Sizing (min-width: 1024px) ── */
    @media (min-width: 1024px) {
        /* Open State: 280px */
        aside.fi-sidebar.fi-sidebar-open,
        div.fi-sidebar.fi-sidebar-open,
        .fi-sidebar.fi-sidebar-open,
        #fi-main-sidebar.fi-sidebar-open,
        .fi-layout > div:has(.fi-sidebar.fi-sidebar-open),
        .fi-layout > [wire\:id]:has(.fi-sidebar.fi-sidebar-open) {
            width: 280px !important;
            min-width: 280px !important;
            max-width: 280px !important;
            flex-shrink: 0 !important;
        }

        /* Collapsed State: 5rem / 80px (w-20 shrink-0) */
        aside.fi-sidebar:not(.fi-sidebar-open),
        div.fi-sidebar:not(.fi-sidebar-open),
        .fi-sidebar:not(.fi-sidebar-open),
        #fi-main-sidebar:not(.fi-sidebar-open),
        .fi-layout > div:has(.fi-sidebar:not(.fi-sidebar-open)),
        .fi-layout > [wire\:id]:has(.fi-sidebar:not(.fi-sidebar-open)) {
            width: 5rem !important; /* 80px / w-20 */
            min-width: 5rem !important;
            max-width: 5rem !important;
            flex-shrink: 0 !important;
        }

        /* When Collapsed: HIDE ALL LABELS, BADGES, AND PROMO CONTAINER */
        aside.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-label,
        div.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-label,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-label,
        #fi-main-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-label,
        aside.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-label,
        div.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-label,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-label,
        #fi-main-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-label,
        aside.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-collapse-btn,
        div.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-collapse-btn,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-collapse-btn,
        #fi-main-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-collapse-btn,
        aside.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-badge,
        div.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-badge,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-badge,
        #fi-main-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-badge,
        aside.fi-sidebar:not(.fi-sidebar-open) .lb-sidebar-promo-ctn,
        div.fi-sidebar:not(.fi-sidebar-open) .lb-sidebar-promo-ctn,
        .fi-sidebar:not(.fi-sidebar-open) .lb-sidebar-promo-ctn,
        #fi-main-sidebar:not(.fi-sidebar-open) .lb-sidebar-promo-ctn,
        aside.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header-logo-ctn,
        div.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header-logo-ctn,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header-logo-ctn,
        #fi-main-sidebar:not(.fi-sidebar-open) .fi-sidebar-header-logo-ctn {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: hidden !important;
            pointer-events: none !important;
        }

        /* Center and format icons in collapsed state */
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-nav {
            padding: 0.75rem 0.375rem !important;
            overflow-x: hidden !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-button {
            justify-content: center !important;
            width: 44px !important;
            height: 44px !important;
            padding: 0 !important;
            margin: 0.25rem auto !important;
            border-radius: 10px !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-icon,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn .fi-icon {
            margin: 0 !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header {
            justify-content: center !important;
            padding: 0.75rem 0.5rem !important;
        }
    }

    /* ── Mobile Sidebar Drawer (max-width: 1023px) ── */
    @media (max-width: 1023px) {
        aside.fi-sidebar,
        div.fi-sidebar,
        .fi-sidebar,
        #fi-main-sidebar {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            z-index: 50 !important;
            width: 280px !important;
            max-width: 85vw !important;
            height: 100vh !important;
            transform: translateX(-100%) !important;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        aside.fi-sidebar.fi-sidebar-open,
        div.fi-sidebar.fi-sidebar-open,
        .fi-sidebar.fi-sidebar-open,
        #fi-main-sidebar.fi-sidebar-open {
            transform: translateX(0) !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5) !important;
        }

        .fi-main-ctn {
            width: 100% !important;
            max-width: 100% !important;
        }
    }

    /* ── Sidebar Header / Brand Area ── */
    .fi-sidebar-header,
    .fi-sidebar-header-ctn,
    header.fi-sidebar-header {
        background-color: #1A1A1A !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        padding: 1rem !important;
        display: flex !important;
        align-items: center !important;
        min-height: 68px !important;
        height: 68px !important;
        box-sizing: border-box !important;
    }

    /* Logo crisp rendering on dark */
    .fi-sidebar .fi-logo,
    .fi-sidebar img.fi-logo {
        height: 2.25rem !important;
        width: auto !important;
        max-width: 180px !important;
        object-fit: contain !important;
        filter: brightness(1.1) !important;
        display: block !important;
    }

    /* ── Navigation Container ── */
    .fi-sidebar-nav {
        background-color: #1A1A1A !important;
        flex: 1 !important;
        padding: 0.875rem 0.625rem !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    .fi-sidebar-nav-groups {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.25rem !important;
    }

    /* Custom dark scrollbar */
    .fi-sidebar-nav::-webkit-scrollbar {
        width: 4px !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-track {
        background: transparent !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(255, 95, 0, 0.3) !important;
        border-radius: 9999px !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: #FF5F00 !important;
    }

    /* ── Group Labels (Clean, Uppercase, Non-clipped) ── */
    .fi-sidebar-group-label {
        color: rgba(255, 255, 255, 0.45) !important;
        font-size: 0.6875rem !important; /* 11px */
        font-weight: 700 !important;
        letter-spacing: 0.06em !important;
        text-transform: uppercase !important;
        padding: 0.875rem 0.75rem 0.375rem !important;
        display: block !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        line-height: 1.2 !important;
    }

    /* Group toggle buttons / collapse chevron */
    .fi-sidebar-group-btn {
        padding: 0.35rem 0.75rem !important;
        border-radius: 8px !important;
        cursor: pointer !important;
    }

    .fi-sidebar-group-btn:hover {
        background-color: rgba(255, 255, 255, 0.04) !important;
    }

    .fi-sidebar-group-collapse-btn {
        color: rgba(255, 255, 255, 0.45) !important;
        transition: transform 0.2s ease, color 0.2s ease !important;
    }

    .fi-sidebar-group-collapse-btn:hover {
        color: #FFFFFF !important;
    }

    /* ── Navigation Item Buttons ── */
    .fi-sidebar-item-btn,
    .fi-sidebar-item-button {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        width: 100% !important;
        padding: 0.5625rem 0.75rem !important;
        border-radius: 10px !important;
        color: rgba(255, 255, 255, 0.75) !important;
        text-decoration: none !important;
        transition: background-color 0.15s ease, color 0.15s ease !important;
        position: relative !important;
        margin-bottom: 2px !important;
        box-sizing: border-box !important;
    }

    /* Hover State */
    .fi-sidebar-item-btn:hover,
    .fi-sidebar-item-button:hover {
        background-color: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important;
    }

    .fi-sidebar-item-btn:hover .fi-icon,
    .fi-sidebar-item-button:hover .fi-sidebar-item-icon {
        color: #FFFFFF !important;
    }

    /* Nav Item Icon */
    .fi-sidebar-item-icon,
    .fi-sidebar-item-btn .fi-icon {
        color: rgba(255, 255, 255, 0.65) !important;
        width: 1.25rem !important;
        height: 1.25rem !important;
        max-width: 1.25rem !important;
        max-height: 1.25rem !important;
        flex-shrink: 0 !important;
        transition: color 0.15s ease !important;
    }

    /* Nav Item Label */
    .fi-sidebar-item-label {
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        color: inherit !important;
        line-height: 1.3 !important;
    }

    /* ── Active Navigation State: Brand Orange (#FF5F00) ── */
    .fi-sidebar-item.fi-active .fi-sidebar-item-btn,
    .fi-sidebar-item-btn.fi-active,
    .fi-sidebar-item-button.fi-active,
    .fi-sidebar-item-button[aria-current="page"],
    .fi-sidebar-item-btn[aria-current="page"] {
        background-color: #FF5F00 !important;
        color: #FFFFFF !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 14px rgba(255, 95, 0, 0.35) !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-btn:hover,
    .fi-sidebar-item-button.fi-active:hover {
        background-color: #e55400 !important;
    }

    .fi-sidebar-item.fi-active .fi-icon,
    .fi-sidebar-item-btn.fi-active .fi-icon,
    .fi-sidebar-item-button.fi-active .fi-sidebar-item-icon {
        color: #FFFFFF !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-label,
    .fi-sidebar-item-button.fi-active .fi-sidebar-item-label {
        color: #FFFFFF !important;
        font-weight: 600 !important;
    }

    /* ── Sidebar Footer ── */
    .fi-sidebar-footer {
        background-color: #1A1A1A !important;
        border-top: 1px solid rgba(255, 255, 255, 0.06) !important;
        padding: 0.75rem 0.625rem !important;
    }
</style>
