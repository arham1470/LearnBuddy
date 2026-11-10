@props([
    'variant' => 'white',
    'icon' => null,
])

@php
$baseClasses = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition-colors duration-200 select-none';

$variantClasses = match ($variant) {
    'orange' => 'bg-lb-orange text-white shadow-lb-sm',
    'dark' => 'bg-lb-black text-white shadow-lb-sm',
    'teal' => 'bg-lb-teal text-white shadow-lb-sm',
    'gray' => 'bg-neutral-100 text-neutral-700 border border-neutral-200',
    default => 'bg-white text-lb-black shadow-lb-sm border border-neutral-100',
};
@endphp

<span {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses}"]) }}>
    @if ($icon)
        <i class="{{ $icon }} text-[0.85em] shrink-0" aria-hidden="true"></i>
    @endif
    {{ $slot }}
</span>
