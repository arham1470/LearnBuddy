@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'icon' => null,
    'id' => null,
])

@php
$inputId = $id ?? $name ?? 'input-' . \Illuminate\Support\Str::random(8);
@endphp

<div class="w-full space-y-1.5">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-xs font-semibold text-neutral-800 uppercase tracking-wider">
            {{ $label }}
            @if ($required)
                <span class="text-lb-orange" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-xl">
        @if ($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                <i class="{{ $icon }}" aria-hidden="true"></i>
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'block w-full rounded-xl border text-sm transition-colors duration-200 bg-white placeholder-neutral-400 py-2.5 ' .
                    ($icon ? 'pl-10 ' : 'pl-3.5 ') .
                    'pr-3.5 ' .
                    ($error
                        ? 'border-red-400 text-red-900 focus:border-red-500 focus:ring-2 focus:ring-red-100'
                        : 'border-neutral-200 text-neutral-900 focus:border-lb-orange focus:ring-2 focus:ring-orange-100')
            ]) }}
        >
    </div>

    @if ($error)
        <p class="text-xs text-red-600 flex items-center gap-1 mt-1" role="alert">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            <span>{{ $error }}</span>
        </p>
    @elseif ($hint)
        <p class="text-xs text-neutral-500 mt-1">{{ $hint }}</p>
    @endif
</div>
