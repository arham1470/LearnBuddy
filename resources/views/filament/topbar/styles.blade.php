<style>
    /* ============================================================
       LearnBuddy — Topbar Design System
       ============================================================ */

    .fi-topbar-ctn {
        position: sticky !important;
        top: 0 !important;
        z-index: 30 !important;
    }

    /* Subtle Brand Accent Line along the very top of topbar */
    .fi-topbar-ctn::before {
        content: "" !important;
        display: block !important;
        height: 3px !important;
        width: 100% !important;
        background: linear-gradient(90deg, #FF5F00 0%, #FF8A3D 50%, #FF5F00 100%) !important;
        background-size: 200% 100% !important;
        animation: lbGradientShift 8s ease infinite !important;
    }

    @keyframes lbGradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Clean Modern Navigation Bar */
    .fi-topbar {
        display: flex !important;
        align-items: center !important;
        background-color: #FFFFFF !important;
        border-bottom: 1px solid #E5E5E5 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        height: 4.25rem !important; /* 68px */
        min-height: 4.25rem !important;
        padding-left: 1.25rem !important;
        padding-right: 1.25rem !important;
        transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    /* Topbar Layout Sections */
    .fi-topbar-start {
        display: flex !important;
        align-items: center !important;
        gap: 0.875rem !important;
        height: 100% !important;
    }

    .fi-topbar-end {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        height: 100% !important;
        margin-left: auto !important;
    }

    /* ── Sidebar Toggle Buttons (Desktop vs Mobile separation) ── */
    @media (min-width: 1024px) {
        /* On desktop, hide mobile hamburger & close buttons completely */
        .fi-topbar-open-sidebar-btn,
        .fi-topbar-close-sidebar-btn {
            display: none !important;
        }

        .fi-topbar-collapse-sidebar-btn-ctn {
            display: inline-flex !important;
            align-items: center !important;
        }
    }

    @media (max-width: 1023px) {
        /* On mobile, hide desktop collapse buttons */
        .fi-topbar-collapse-sidebar-btn-ctn {
            display: none !important;
        }
    }

    /* Respect Alpine x-show hidden elements */
    .fi-topbar [style*="display: none"],
    .fi-topbar [x-cloak],
    .fi-topbar .fi-hidden,
    .fi-topbar-collapse-sidebar-btn-ctn [style*="display: none"] {
        display: none !important;
    }

    /* Styling for the visible toggle button */
    .fi-topbar .fi-icon-btn {
        width: 2.25rem !important;
        height: 2.25rem !important;
        border-radius: 10px !important;
        background-color: #F7F7F5 !important;
        border: 1px solid #E5E5E5 !important;
        color: #6B6B6B !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        padding: 0 !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .fi-topbar .fi-icon-btn:hover {
        background-color: #FFF0E6 !important;
        color: #FF5F00 !important;
        border-color: rgba(255, 95, 0, 0.3) !important;
        transform: translateY(-1px) !important;
    }

    .fi-topbar .fi-icon-btn svg {
        width: 18px !important;
        height: 18px !important;
        max-width: 18px !important;
        max-height: 18px !important;
        display: block !important;
    }

    /* Hide topbar logo image because sidebar holds official LearnBuddy brand */
    .fi-topbar .fi-logo,
    .fi-topbar img.fi-logo {
        display: none !important;
    }

    /* ── User Avatar Menu ── */
    .fi-topbar .fi-user-menu {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    .fi-topbar .fi-user-menu-trigger {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 2.25rem !important;
        height: 2.25rem !important;
        border-radius: 9999px !important;
        border: none !important;
        background: transparent !important;
        cursor: pointer !important;
        padding: 0 !important;
        outline: none !important;
    }

    .fi-topbar .fi-user-menu-trigger img,
    .fi-topbar .fi-user-avatar img,
    .fi-topbar .fi-avatar img {
        height: 2.25rem !important;
        width: 2.25rem !important;
        border-radius: 9999px !important;
        object-fit: cover !important;
        border: 2px solid #E5E5E5 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
        transition: all 0.2s ease !important;
    }

    .fi-topbar .fi-user-menu-trigger:hover img,
    .fi-topbar .fi-avatar:hover img {
        border-color: #FF5F00 !important;
        box-shadow: 0 0 0 2px rgba(255, 95, 0, 0.25), 0 4px 10px rgba(255, 95, 0, 0.2) !important;
        transform: scale(1.05) !important;
    }

    /* Dropdown Panel */
    .fi-dropdown-panel {
        z-index: 9999 !important;
        border-radius: 14px !important;
        border: 1px solid #E5E5E5 !important;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1) !important;
        padding: 0.5rem !important;
        background-color: #FFFFFF !important;
    }

    .fi-dropdown-list-item {
        border-radius: 8px !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        color: #0A0A0A !important;
        transition: all 0.15s ease !important;
    }

    .fi-dropdown-list-item:hover {
        background-color: #FFF0E6 !important;
        color: #FF5F00 !important;
    }
</style>
