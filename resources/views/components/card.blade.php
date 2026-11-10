@props([
    'hover' => true,
    'padding' => 'default', // 'none', 'sm', 'default', 'lg'
    'variant' => 'white',   // 'white', 'off-white', 'peach', 'teal'
])

@php
$paddingClasses = match ($padding) {
    'none' => 'p-0',
    'sm' => 'p-4',
    'lg' => 'p-8 sm:p-10',
    default => 'p-6',
};

$variantClasses = match ($variant) {
    'off-white' => 'bg-lb-off-white border border-neutral-200/70 text-lb-black',
    'peach' => 'bg-lb-peach border border-orange-100 text-lb-black',
    'teal' => 'bg-lb-teal text-white border-0',
    default => 'bg-white border border-neutral-200/60 text-lb-black shadow-lb-sm',
};

$hoverClasses = $hover ? 'transition-all duration-300 hover:-translate-y-1 hover:shadow-lb-md' : '';
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl {$variantClasses} {$paddingClasses} {$hoverClasses} overflow-hidden"]) }}>
    {{ $slot }}
</div>
