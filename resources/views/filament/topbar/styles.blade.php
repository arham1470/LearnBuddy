<style>
    /* Modern LearnBuddy Topbar Styling */
    .fi-topbar-ctn {
        position: sticky;
        top: 0;
        z-index: 30;
    }

    /* Subtle Brand Accent Line along the very top of topbar */
    .fi-topbar-ctn::before {
        content: "";
        display: block;
        height: 3px;
        width: 100%;
        background: linear-gradient(90deg, #FF5F00 0%, #FF8A00 35%, #FFA500 70%, #FF5F00 100%);
        background-size: 200% 100%;
        animation: lbGradientShift 8s ease infinite;
    }

    @keyframes lbGradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Frosted Glass Navigation Bar */
    .fi-topbar {
        display: flex !important;
        align-items: center !important;
        backdrop-filter: blur(16px) saturate(180%);
        -webkit-backdrop-filter: blur(16px) saturate(180%);
        background-color: rgba(255, 255, 255, 0.88) !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02) !important;
        height: 4.25rem !important;
        padding-left: 1.25rem !important;
        padding-right: 1.25rem !important;
        transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .dark .fi-topbar {
        background-color: rgba(15, 23, 42, 0.88) !important;
        border-bottom: 1px solid rgba(30, 41, 59, 0.8) !important;
        box-shadow: 0 4px 24px -2px rgba(0, 0, 0, 0.25) !important;
    }

    /* Refined Icon Buttons (Sidebar toggle, etc.) */
    .fi-topbar .fi-icon-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 2.25rem !important;
        width: 2.25rem !important;
        border-radius: 0.75rem !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: 1px solid transparent !important;
    }

    .fi-topbar .fi-icon-btn:hover {
        background-color: rgba(255, 95, 0, 0.08) !important;
        color: #FF5F00 !important;
        border-color: rgba(255, 95, 0, 0.2) !important;
        transform: translateY(-1px);
    }

    .dark .fi-topbar .fi-icon-btn:hover {
        background-color: rgba(255, 95, 0, 0.15) !important;
        color: #FF7A29 !important;
        border-color: rgba(255, 95, 0, 0.3) !important;
    }

    /* Topbar Layout Elements & Spacing */
    .fi-topbar-start {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        height: 100% !important;
    }

    .fi-topbar-end {
        display: flex !important;
        align-items: center !important;
        gap: 0.875rem !important;
        height: 100% !important;
        margin-left: auto !important;
    }

    /* Remove topbar sidebar toggle / hamburger buttons */
    .fi-topbar > button.fi-icon-btn,
    .fi-topbar > .fi-topbar-open-sidebar-btn,
    .fi-topbar > .fi-topbar-close-sidebar-btn,
    .fi-topbar-collapse-sidebar-btn-ctn {
        display: none !important;
    }

    /* Remove sidebar header */
    .fi-sidebar-header-ctn,
    header.fi-sidebar-header {
        display: none !important;
    }

    /* Official Brand Logo Crisp Styling */
    .fi-logo,
    img.fi-logo {
        height: 2.25rem !important;
        width: auto !important;
        max-height: 2.25rem !important;
        object-fit: contain !important;
        image-rendering: -webkit-optimize-contrast !important;
        image-rendering: crisp-edges !important;
        display: block !important;
    }

    /* User Menu Dropdown Container & Trigger */
    .fi-topbar .fi-user-menu {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        width: auto !important;
        height: auto !important;
        max-height: none !important;
        max-width: none !important;
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
        box-shadow: 0 0 0 2px rgba(255, 95, 0, 0.25), 0 2px 6px rgba(0, 0, 0, 0.08) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    }

    .fi-topbar .fi-user-menu-trigger:hover img,
    .fi-topbar .fi-avatar:hover img {
        transform: scale(1.05) !important;
        box-shadow: 0 0 0 2px #FF5F00, 0 4px 12px rgba(255, 95, 0, 0.25) !important;
    }

    /* Dropdown Panel: Clean, Modern Floating Panel matching Website UI */
    .fi-dropdown-panel {
        z-index: 9999 !important;
        border-radius: 1rem !important;
        border: 1px solid rgba(226, 232, 240, 0.95) !important;
        box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.12), 0 6px 14px -2px rgba(15, 23, 42, 0.05) !important;
        padding: 0.5rem !important;
        min-width: 15rem !important;
        max-width: 20rem !important;
        background-color: #FFFFFF !important;
    }

    .dark .fi-dropdown-panel {
        background-color: #0F172A !important;
        border-color: rgba(51, 65, 85, 0.9) !important;
        box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.6) !important;
    }

    .fi-dropdown-list-item {
        border-radius: 0.625rem !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        color: #334155 !important;
        transition: all 0.15s ease !important;
    }

    .dark .fi-dropdown-list-item {
        color: #E2E8F0 !important;
    }

    .fi-dropdown-list-item:hover {
        background-color: #FFF7ED !important;
        color: #FF5F00 !important;
    }

    .dark .fi-dropdown-list-item:hover {
        background-color: rgba(255, 95, 0, 0.15) !important;
        color: #FB923C !important;
    }

    .fi-dropdown-header {
        padding: 0.625rem 0.75rem !important;
        border-bottom: 1px solid rgba(241, 245, 249, 0.9) !important;
        margin-bottom: 0.375rem !important;
    }

    .dark .fi-dropdown-header {
        border-bottom-color: rgba(30, 41, 59, 0.8) !important;
    }

    /* Quick Logout Button (matches website header circular icon button) */
    .lb-quick-logout-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 2.25rem !important;
        height: 2.25rem !important;
        border-radius: 9999px !important;
        background-color: #F8FAFC !important;
        color: #64748B !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        padding: 0 !important;
    }

    .lb-quick-logout-btn:hover {
        background-color: #FFF1F2 !important;
        color: #E11D48 !important;
        border-color: rgba(225, 29, 72, 0.3) !important;
        transform: scale(1.05) !important;
    }

    .dark .lb-quick-logout-btn {
        background-color: #1E293B !important;
        color: #94A3B8 !important;
        border-color: rgba(51, 65, 85, 0.8) !important;
    }

    .dark .lb-quick-logout-btn:hover {
        background-color: rgba(225, 29, 72, 0.15) !important;
        color: #FB7185 !important;
    }

    /* Topbar Pill Badges: Height 2.25rem (36px) */
    .lb-topbar-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 2.25rem !important;
        gap: 0.375rem !important;
        padding-left: 0.875rem !important;
        padding-right: 0.875rem !important;
        border-radius: 9999px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        letter-spacing: -0.01em !important;
        line-height: 1 !important;
        box-sizing: border-box !important;
        transition: all 0.2s ease !important;
    }

    .lb-topbar-badge-primary {
        background: rgba(255, 95, 0, 0.09) !important;
        color: #EA580C !important;
        border: 1px solid rgba(255, 95, 0, 0.22) !important;
    }

    .dark .lb-topbar-badge-primary {
        background: rgba(255, 95, 0, 0.18) !important;
        color: #FB923C !important;
        border-color: rgba(255, 95, 0, 0.35) !important;
    }

    .lb-topbar-badge-neutral {
        background: rgba(241, 245, 249, 0.85) !important;
        color: #475569 !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
    }

    .dark .lb-topbar-badge-neutral {
        background: rgba(30, 41, 59, 0.7) !important;
        color: #94A3B8 !important;
        border-color: rgba(51, 65, 85, 0.8) !important;
    }

    /* View Storefront Button: Height 2.25rem (36px), Single-line */
    .lb-topbar-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 2.25rem !important;
        gap: 0.375rem !important;
        padding-left: 0.875rem !important;
        padding-right: 0.875rem !important;
        border-radius: 9999px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
        color: #334155 !important;
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(203, 213, 225, 0.8) !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        text-decoration: none !important;
        box-sizing: border-box !important;
        line-height: 1 !important;
        transition: all 0.2s ease !important;
    }

    .lb-topbar-btn:hover {
        color: #FF5F00 !important;
        border-color: rgba(255, 95, 0, 0.4) !important;
        background: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(255, 95, 0, 0.12) !important;
        transform: translateY(-1px) !important;
    }

    .dark .lb-topbar-btn {
        background: rgba(30, 41, 59, 0.85) !important;
        color: #E2E8F0 !important;
        border-color: rgba(51, 65, 85, 0.9) !important;
    }

    .dark .lb-topbar-btn:hover {
        color: #FB923C !important;
        border-color: rgba(255, 95, 0, 0.5) !important;
        background: rgba(30, 41, 59, 1) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
    }

    /* Live Pulse Dot */
    .lb-pulse-dot {
        position: relative;
        display: inline-flex;
        width: 7px;
        height: 7px;
        border-radius: 9999px;
        background-color: #10B981;
    }

    .lb-pulse-dot::after {
        content: "";
        position: absolute;
        inset: -2px;
        border-radius: 9999px;
        background-color: rgba(16, 185, 129, 0.4);
        animation: lbPing 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    @keyframes lbPing {
        75%, 100% {
            transform: scale(2.2);
            opacity: 0;
        }
    }
</style>
