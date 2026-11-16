{{--
    LearnBuddy Dashboard Stats Overview Widget
    6 Premium Cards with Active Courses Featured in Orange Gradient
    Single root node for Livewire v3 compatibility with bulletproof SVG constraints
--}}

@php
    $stats = $stats ?? [];
@endphp

<div class="lb-stats-overview-wrapper" style="width: 100%; display: block; box-sizing: border-box;">
    <div class="lb-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; width: 100%; box-sizing: border-box;">
        @foreach ($stats as $index => $stat)
            @php
                $isFeatured = !empty($stat['featured']);
                $icon       = $stat['icon'] ?? 'academic-cap';
            @endphp

            <div
                class="lb-stat-card {{ $isFeatured ? 'lb-stat-featured' : '' }}"
                style="{{ $isFeatured
                    ? 'background: linear-gradient(135deg, #FF5F00 0%, #FF8A3D 100%) !important; border: 1px solid transparent !important; color: #FFFFFF !important; box-shadow: 0 4px 20px rgba(255, 95, 0, 0.35) !important;'
                    : 'background: #FFFFFF !important; border: 1px solid #E5E5E5 !important; color: #0A0A0A !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.03) !important;'
                }} border-radius: 16px; padding: 1.375rem 1.5rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 148px; gap: 1rem; box-sizing: border-box; position: relative; overflow: hidden;"
            >
                <div class="lb-stat-top" style="display: flex; align-items: center; justify-content: space-between;">
                    <div
                        class="lb-stat-icon-wrap"
                        style="width: 44px; height: 44px; min-width: 44px; min-height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; {{ $isFeatured ? 'background: rgba(255, 255, 255, 0.22) !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;' : 'background: rgba(255, 95, 0, 0.09) !important; border: 1px solid rgba(255, 95, 0, 0.18) !important;' }}"
                    >
                        @if ($icon === 'academic-cap')
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 22px; max-height: 22px; display: block; {{ $isFeatured ? 'color: #FFFFFF !important;' : 'color: #FF5F00 !important;' }}">
                                <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                                <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.71 47.87 47.87 0 0 1-8.105 2.571.75.75 0 0 1-.832-.62 48.5 48.5 0 0 1-.464-3.726A48.45 48.45 0 0 1 13.06 15.473ZM4.277 19.558a48.59 48.59 0 0 1-3.197-.99.75.75 0 0 1-.46-.71 48.558 48.558 0 0 1 .256-4.285 48.465 48.465 0 0 1 7.655 3.267l-.038.218A48.418 48.418 0 0 1 4.277 19.558Z" />
                                <path d="M6.586 4.672A3 3 0 0 0 7.5 10.5h4.5a3 3 0 0 0 .914-5.828 48.456 48.456 0 0 0-5.828 0Z" />
                            </svg>
                        @elseif ($icon === 'user-group')
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 22px; max-height: 22px; display: block; {{ $isFeatured ? 'color: #FFFFFF !important;' : 'color: #FF5F00 !important;' }}">
                                <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd" />
                                <path d="M5.082 14.254a8.287 8.287 0 0 0-1.308 5.135 9.687 9.687 0 0 1-1.764-.44l-.115-.04a.563.563 0 0 1-.373-.487l-.01-.121a3.75 3.75 0 0 1 3.57-4.047ZM20.226 19.389a8.287 8.287 0 0 0-1.308-5.135 3.75 3.75 0 0 1 3.57 4.047l-.01.121a.563.563 0 0 1-.373.486l-.115.04c-.567.2-1.156.349-1.764.441Z" />
                            </svg>
                        @elseif ($icon === 'rectangle-stack')
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 22px; max-height: 22px; display: block; {{ $isFeatured ? 'color: #FFFFFF !important;' : 'color: #FF5F00 !important;' }}">
                                <path d="M5.566 4.657A4.505 4.505 0 0 1 6.75 4.5h10.5c.41 0 .806.055 1.183.157A3 3 0 0 0 15.75 3h-7.5a3 3 0 0 0-2.684 1.657ZM2.25 12a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3v-6ZM5.25 7.5c-.41 0-.806.055-1.184.157A3 3 0 0 1 6.75 6h10.5a3 3 0 0 1 2.683 1.657A4.505 4.505 0 0 0 18.75 7.5H5.25Z" />
                            </svg>
                        @elseif ($icon === 'banknotes')
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 22px; max-height: 22px; display: block; {{ $isFeatured ? 'color: #FFFFFF !important;' : 'color: #FF5F00 !important;' }}">
                                <path d="M12 7.5a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z" />
                                <path fill-rule="evenodd" d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3h17.25c1.035 0 1.875.84 1.875 1.875v9.75c0 1.036-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 0 1 1.5 14.625v-9.75ZM8.25 9.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM18.75 9a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V9.75a.75.75 0 0 0-.75-.75h-.008ZM4.5 9.75A.75.75 0 0 1 5.25 9h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75V9.75Z" clip-rule="evenodd" />
                                <path d="M2.25 18a.75.75 0 0 0 0 1.5c5.4 0 10.63.722 15.6 2.075 1.19.324 2.4-.558 2.4-1.82V18.75a.75.75 0 0 0-.75-.75H2.25Z" />
                            </svg>
                        @elseif ($icon === 'clipboard-document-check')
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 22px; max-height: 22px; display: block; {{ $isFeatured ? 'color: #FFFFFF !important;' : 'color: #FF5F00 !important;' }}">
                                <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0 1 18 9.375v9.375a3 3 0 0 0 3-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 0 0-.673-.05A3 3 0 0 0 15 1.5h-1.5a3 3 0 0 0-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6ZM13.5 3A1.5 1.5 0 0 0 12 4.5h4.5A1.5 1.5 0 0 0 15 3h-1.5Z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V9.375Zm9.586 4.594a.75.75 0 0 0-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 0 0-1.06 1.06l1.5 1.5a.75.75 0 0 0 1.116-.062l3-3.75Z" clip-rule="evenodd" />
                            </svg>
                        @elseif ($icon === 'pencil-square')
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 22px; max-height: 22px; display: block; {{ $isFeatured ? 'color: #FFFFFF !important;' : 'color: #FF5F00 !important;' }}">
                                <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32L19.513 8.2Z" />
                                <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                            </svg>
                        @endif
                    </div>

                    @if ($isFeatured)
                        <span class="lb-featured-badge" style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #FFFFFF; background: rgba(255, 255, 255, 0.22); padding: 0.2rem 0.625rem; border-radius: 9999px; backdrop-filter: blur(4px);">Featured</span>
                    @endif
                </div>

                <div class="lb-stat-body" style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <div class="lb-stat-value" style="font-size: 1.875rem; font-weight: 800; line-height: 1.15; letter-spacing: -0.025em; {{ $isFeatured ? 'color: #FFFFFF !important;' : 'color: #0A0A0A !important;' }}">
                        {{ $stat['value'] ?? '—' }}
                    </div>
                    <div class="lb-stat-label-wrap" style="display: flex; flex-direction: column; gap: 0.1rem;">
                        <span class="lb-stat-label" style="font-size: 0.8125rem; font-weight: 700; line-height: 1.3; {{ $isFeatured ? 'color: rgba(255, 255, 255, 0.95) !important;' : 'color: #0A0A0A !important;' }}">
                            {{ $stat['label'] ?? '' }}
                        </span>
                        @if (!empty($stat['description']))
                            <span class="lb-stat-desc" style="font-size: 0.75rem; line-height: 1.35; {{ $isFeatured ? 'color: rgba(255, 255, 255, 0.80) !important;' : 'color: #6B6B6B !important;' }}">
                                {{ $stat['description'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Scoped CSS block inside the single root node --}}
    <style>
        .lb-stats-overview-wrapper {
            margin-bottom: 0.5rem;
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

        .lb-stat-card {
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.2s ease !important;
            cursor: default !important;
        }

        .lb-stat-card:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
        }

        .lb-stat-featured:hover {
            box-shadow: 0 10px 30px rgba(255, 95, 0, 0.45) !important;
        }

        .lb-stat-card:hover .lb-stat-icon-wrap {
            transform: scale(1.05);
        }

        .lb-stat-icon-wrap {
            transition: transform 0.2s ease !important;
        }
    </style>
</div>
