@props([
    'as' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'right',
    'href' => null,
    'type' => 'button',
])

@php
$baseClasses = 'inline-flex items-center justify-center font-semibold rounded-full transition-all duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-lb-orange active:translate-y-0 disabled:opacity-50 disabled:pointer-events-none select-none';

$sizeClasses = match ($size) {
    'sm' => 'px-4 py-2 text-xs gap-1.5',
    'lg' => 'px-8 py-3.5 text-base gap-2.5',
    default => 'px-6 py-2.5 text-sm gap-2',
};

$variantClasses = match ($variant) {
    'dark' => 'bg-lb-black text-white hover:bg-neutral-800 shadow-lb-sm hover:-translate-y-0.5',
    'outline' => 'bg-transparent text-lb-black border border-neutral-300 hover:border-lb-black hover:bg-neutral-50',
    'teal' => 'bg-lb-teal text-white hover:bg-teal-600 shadow-lb-sm hover:-translate-y-0.5',
    'ghost' => 'bg-transparent text-neutral-700 hover:text-lb-orange hover:bg-orange-50/50',
    default => 'bg-lb-orange text-white hover:bg-lb-orange-hover shadow-lb-sm hover:-translate-y-0.5',
};

$classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($as === 'a' || $href)
    <a href="{{ $href ?? '#' }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon && $iconPosition === 'left')
            <i class="{{ $icon }} shrink-0" aria-hidden="true"></i>
        @endif
        <span>{{ $slot }}</span>
        @if ($icon && $iconPosition === 'right')
            <i class="{{ $icon }} shrink-0" aria-hidden="true"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon && $iconPosition === 'left')
            <i class="{{ $icon }} shrink-0" aria-hidden="true"></i>
        @endif
        <span>{{ $slot }}</span>
        @if ($icon && $iconPosition === 'right')
            <i class="{{ $icon }} shrink-0" aria-hidden="true"></i>
        @endif
    </button>
@endif
