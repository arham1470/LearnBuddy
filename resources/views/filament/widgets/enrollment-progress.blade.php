<div class="lb-progress-card">
    {{-- Header --}}
    <div class="lb-progress-header">
        <div class="lb-progress-title-wrap">
            <div class="lb-progress-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px; min-width: 18px; min-height: 18px; max-width: 18px; max-height: 18px; display: block;">
                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h3 class="lb-progress-title">Enrollment Progress</h3>
                <p class="lb-progress-subtitle">Platform seat fill rate &amp; intake capacity</p>
            </div>
        </div>
        <span class="lb-progress-badge">Live Cohort</span>
    </div>

    {{-- Main metric display --}}
    <div class="lb-progress-metric-wrap">
        <div class="lb-progress-seats">
            <span class="lb-progress-filled">{{ $totalEnrollments }}</span>
            <span class="lb-progress-total">of {{ $totalCapacity }} seats filled</span>
        </div>
        <div class="lb-progress-percent-pill">
            <span class="lb-progress-pulse-dot"></span>
            <span>{{ $percentage }}% completion</span>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="lb-progress-bar-track">
        <div class="lb-progress-bar-fill" style="width: {{ min(max($percentage, 0), 100) }}%;"></div>
    </div>

    {{-- Mini detail metrics --}}
    <div class="lb-progress-footer-stats">
        <div class="lb-progress-stat-col">
            <span class="lb-progress-stat-label">Available Seats</span>
            <span class="lb-progress-stat-val">{{ max($totalCapacity - $totalEnrollments, 0) }} remaining</span>
        </div>
        <div class="lb-progress-stat-col text-right">
            <span class="lb-progress-stat-label">Occupancy Status</span>
            <span class="lb-progress-stat-val text-orange">{{ $percentage >= 80 ? 'Near Capacity' : ($percentage >= 50 ? 'Steady Intake' : 'Open Registration') }}</span>
        </div>
    </div>
</div>

<style>
    .lb-progress-card {
        background: #FFFFFF;
        border: 1px solid #E5E5E5;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.03);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-sizing: border-box;
    }

    .lb-progress-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
    }

    .lb-progress-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .lb-progress-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .lb-progress-icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255, 95, 0, 0.09);
        border: 1px solid rgba(255, 95, 0, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .lb-progress-icon-wrap svg {
        width: 18px;
        height: 18px;
        color: #FF5F00;
    }

    .lb-progress-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #0A0A0A;
        margin: 0;
        line-height: 1.2;
    }

    .lb-progress-subtitle {
        font-size: 0.6875rem;
        color: #6B6B6B;
        margin: 0.15rem 0 0;
        line-height: 1.3;
    }

    .lb-progress-badge {
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        background: #F7F7F5;
        color: #6B6B6B;
        border: 1px solid #E5E5E5;
        white-space: nowrap;
    }

    .lb-progress-metric-wrap {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .lb-progress-seats {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .lb-progress-filled {
        font-size: 2.25rem;
        font-weight: 800;
        color: #0A0A0A;
        line-height: 1;
        letter-spacing: -0.03em;
    }

    .lb-progress-total {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #6B6B6B;
    }

    .lb-progress-percent-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        align-self: flex-start;
        padding: 0.3125rem 0.75rem;
        border-radius: 9999px;
        background: rgba(255, 95, 0, 0.08);
        border: 1px solid rgba(255, 95, 0, 0.22);
        color: #FF5F00;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .lb-progress-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 9999px;
        background-color: #FF5F00;
        display: inline-block;
    }

    .lb-progress-bar-track {
        width: 100%;
        height: 10px;
        background: #F7F7F5;
        border: 1px solid #E5E5E5;
        border-radius: 9999px;
        overflow: hidden;
        padding: 1px;
        box-sizing: border-box;
    }

    .lb-progress-bar-fill {
        height: 100%;
        border-radius: 9999px;
        background: linear-gradient(90deg, #FF5F00 0%, #FF8A3D 100%);
        box-shadow: 0 0 10px rgba(255, 95, 0, 0.4);
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .lb-progress-footer-stats {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 0.875rem;
        border-top: 1px solid #F0F0EE;
        gap: 0.5rem;
    }

    .lb-progress-stat-col {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .lb-progress-stat-label {
        font-size: 0.6875rem;
        font-weight: 500;
        color: #6B6B6B;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .lb-progress-stat-val {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #0A0A0A;
    }

    .lb-progress-stat-val.text-orange {
        color: #FF5F00;
    }
</style>
