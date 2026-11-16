<style>
/* ============================================================
   LearnBuddy Dashboard – Master Head CSS & Design System
   Injected into HEAD_END for immediate, uncompromised rendering
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

/* ── Outer Shell Flex & Layout Structure ── */
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

/* ── Universal SVG constraints (prevents any SVG from expanding full screen) ── */
svg {
    max-width: 100%;
    vertical-align: middle;
}

.lb-stat-icon-wrap svg {
    width: 22px !important;
    height: 22px !important;
    min-width: 22px !important;
    min-height: 22px !important;
    max-width: 22px !important;
    max-height: 22px !important;
    display: block !important;
}

.lb-topbar-logout-btn svg {
    width: 16px !important;
    height: 16px !important;
    min-width: 16px !important;
    min-height: 16px !important;
    max-width: 16px !important;
    max-height: 16px !important;
    display: block !important;
}

.lb-bell-icon,
.lb-topbar-icon-btn svg {
    width: 18px !important;
    height: 18px !important;
    min-width: 18px !important;
    min-height: 18px !important;
    max-width: 18px !important;
    max-height: 18px !important;
    display: block !important;
}

.lb-storefront-icon {
    width: 14px !important;
    height: 14px !important;
    min-width: 14px !important;
    min-height: 14px !important;
    max-width: 14px !important;
    max-height: 14px !important;
    display: block !important;
}

.lb-search-icon {
    width: 15px !important;
    height: 15px !important;
    min-width: 15px !important;
    min-height: 15px !important;
    max-width: 15px !important;
    max-height: 15px !important;
    display: block !important;
}

.lb-cal-icon {
    width: 16px !important;
    height: 16px !important;
    min-width: 16px !important;
    min-height: 16px !important;
    max-width: 16px !important;
    max-height: 16px !important;
    display: block !important;
}

.fi-topbar .fi-icon-btn svg {
    width: 18px !important;
    height: 18px !important;
    max-width: 18px !important;
    max-height: 18px !important;
    display: block !important;
}

/* ── Main Background Canvas (#F7F7F5) ── */
.fi-main,
.fi-body,
.fi-page {
    background-color: #F7F7F5 !important;
}

/* ── KPI Stat Cards Grid Layout ── */
.lb-stats-overview-wrapper {
    width: 100% !important;
    display: block !important;
    margin-bottom: 0.5rem !important;
    box-sizing: border-box !important;
}

.lb-stats-grid {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 1.25rem !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

@media (min-width: 640px) {
    .lb-stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (min-width: 1024px) {
    .lb-stats-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

/* ── KPI Stat Card Containers ── */
.lb-stat-card {
    background: #FFFFFF !important;
    border: 1px solid #E5E5E5 !important;
    border-radius: 16px !important;
    padding: 1.375rem 1.5rem !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.03) !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    min-height: 148px !important;
    gap: 1rem !important;
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.2s ease !important;
    position: relative !important;
    overflow: hidden !important;
    cursor: default !important;
    box-sizing: border-box !important;
}

.lb-stat-card:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
    border-color: rgba(255, 95, 0, 0.3) !important;
}

/* Active Courses Featured Card: Orange Gradient */
.lb-stat-card.lb-stat-featured {
    background: linear-gradient(135deg, #FF5F00 0%, #FF8A3D 100%) !important;
    border-color: transparent !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 20px rgba(255, 95, 0, 0.35) !important;
}

.lb-stat-card.lb-stat-featured:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 10px 30px rgba(255, 95, 0, 0.45) !important;
}

.lb-stat-top {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

.lb-stat-icon-wrap {
    width: 44px !important;
    height: 44px !important;
    min-width: 44px !important;
    min-height: 44px !important;
    border-radius: 12px !important;
    background: rgba(255, 95, 0, 0.09) !important;
    border: 1px solid rgba(255, 95, 0, 0.18) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    transition: transform 0.2s ease !important;
}

.lb-stat-card:hover .lb-stat-icon-wrap {
    transform: scale(1.05) !important;
}

.lb-stat-featured .lb-stat-icon-wrap {
    background: rgba(255, 255, 255, 0.22) !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
}

.lb-stat-value {
    font-size: 1.875rem !important; /* 30px */
    font-weight: 800 !important;
    color: #0A0A0A !important;
    line-height: 1.15 !important;
    letter-spacing: -0.025em !important;
}

.lb-stat-featured .lb-stat-value {
    color: #FFFFFF !important;
}

.lb-stat-label {
    font-size: 0.8125rem !important;
    font-weight: 700 !important;
    color: #0A0A0A !important;
    line-height: 1.3 !important;
}

.lb-stat-featured .lb-stat-label {
    color: rgba(255, 255, 255, 0.95) !important;
}

.lb-stat-desc {
    font-size: 0.75rem !important;
    color: #6B6B6B !important;
    line-height: 1.35 !important;
}

.lb-stat-featured .lb-stat-desc {
    color: rgba(255, 255, 255, 0.80) !important;
}

/* ── Table & Widget Cards ── */
.fi-ta-ctn,
.fi-wi-chart,
.fi-section {
    background-color: #FFFFFF !important;
    border-radius: 16px !important;
    border: 1px solid #E5E5E5 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.03) !important;
    overflow: hidden !important;
    transition: box-shadow 0.2s ease !important;
}

.fi-ta-ctn:hover,
.fi-wi-chart:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06) !important;
}

/* ── Table Heading Area ── */
.fi-ta-header {
    background-color: #FFFFFF !important;
    border-bottom: 1px solid #E5E5E5 !important;
    padding: 1.125rem 1.5rem !important;
}

.fi-ta-header-heading {
    font-size: 1rem !important;
    font-weight: 700 !important;
    color: #0A0A0A !important;
    position: relative !important;
    padding-left: 0.875rem !important;
    display: inline-flex !important;
    align-items: center !important;
}

.fi-ta-header-heading::before {
    content: '' !important;
    position: absolute !important;
    left: 0 !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 3.5px !important;
    height: 1.125rem !important;
    background: #FF5F00 !important;
    border-radius: 9999px !important;
}

/* ── Column Header Cells (th) ── */
.fi-ta-header-cell {
    background-color: #F7F7F5 !important;
    font-size: 0.6875rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.05em !important;
    text-transform: uppercase !important;
    color: #6B6B6B !important;
    padding: 0.75rem 1.25rem !important;
    border-bottom: 1px solid #E5E5E5 !important;
}

/* ── Table Body Rows & Cells ── */
.fi-ta-row {
    transition: background-color 0.15s ease !important;
    border-bottom: 1px solid #F0F0EE !important;
}

.fi-ta-row:hover {
    background-color: #FFF7F0 !important;
}

.fi-ta-row:last-child {
    border-bottom: none !important;
}

.fi-ta-cell {
    padding: 0.875rem 1.25rem !important;
    font-size: 0.875rem !important;
    color: #0A0A0A !important;
    vertical-align: middle !important;
}

/* ── Student Avatar & Initials ── */
.lb-student-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.lb-student-avatar {
    width: 34px;
    height: 34px;
    border-radius: 9999px;
    background-color: #FFF0E6;
    color: #FF5F00;
    font-weight: 700;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid rgba(255, 95, 0, 0.25);
    box-shadow: 0 1px 3px rgba(255, 95, 0, 0.1);
}

.lb-student-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.lb-student-name {
    font-weight: 700;
    font-size: 0.875rem;
    color: #0A0A0A;
    line-height: 1.25;
}

.lb-student-email {
    font-size: 0.6875rem;
    color: #6B6B6B;
    line-height: 1.2;
    margin-top: 0.1rem;
}

/* ── Badges ── */
.fi-badge {
    border-radius: 9999px !important;
    padding: 0.25rem 0.75rem !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.02em !important;
}

/* ── Empty State ── */
.fi-ta-empty-state {
    padding: 3.5rem 1.5rem !important;
    text-align: center !important;
    background: #FFFFFF !important;
}

.fi-ta-empty-state-icon-ctn {
    width: 52px !important;
    height: 52px !important;
    border-radius: 14px !important;
    background-color: #FFF0E6 !important;
    border: 1px solid rgba(255, 95, 0, 0.2) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 auto 1.25rem !important;
}

.fi-ta-empty-state-icon {
    color: #FF5F00 !important;
    width: 1.75rem !important;
    height: 1.75rem !important;
}

.fi-ta-empty-state-heading {
    font-size: 1.0625rem !important;
    font-weight: 700 !important;
    color: #0A0A0A !important;
    margin-bottom: 0.375rem !important;
}

.fi-ta-empty-state-description {
    font-size: 0.8125rem !important;
    color: #6B6B6B !important;
    max-width: 380px !important;
    margin: 0 auto !important;
    line-height: 1.5 !important;
}
</style>
