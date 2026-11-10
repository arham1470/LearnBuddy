@props([
    'type' => 'info', // 'info', 'success', 'warning', 'error'
    'dismissible' => false,
    'title' => null,
])

@php
$config = match ($type) {
    'success' => [
        'bg' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
        'icon' => 'fas fa-check-circle text-emerald-600',
    ],
    'warning' => [
        'bg' => 'bg-amber-50 border-amber-200 text-amber-900',
        'icon' => 'fas fa-exclamation-triangle text-amber-600',
    ],
    'error' => [
        'bg' => 'bg-rose-50 border-rose-200 text-rose-900',
        'icon' => 'fas fa-times-circle text-rose-600',
    ],
    default => [
        'bg' => 'bg-orange-50 border-orange-200 text-neutral-900',
        'icon' => 'fas fa-info-circle text-lb-orange',
    ],
};
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    role="alert"
    {{ $attributes->merge(['class' => "flex items-start gap-3 p-4 rounded-xl border {$config['bg']} text-sm relative"]) }}
>
    <div class="shrink-0 mt-0.5">
        <i class="{{ $config['icon'] }} text-base" aria-hidden="true"></i>
    </div>

    <div class="flex-1">
        @if ($title)
            <h4 class="font-bold text-sm mb-1 leading-snug">{{ $title }}</h4>
        @endif
        <div class="text-xs sm:text-sm leading-relaxed opacity-95">
            {{ $slot }}
        </div>
    </div>

    @if ($dismissible)
        <button
            type="button"
            @click="show = false"
            class="shrink-0 text-neutral-400 hover:text-neutral-700 transition p-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-lb-orange"
            aria-label="Dismiss alert"
        >
            <i class="fas fa-times text-xs" aria-hidden="true"></i>
        </button>
    @endif
</div>
