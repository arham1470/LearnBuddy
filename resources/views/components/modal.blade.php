@props([
    'name',
    'title' => null,
    'maxWidth' => 'md',
    'overflow' => 'overflow-visible',
])

@php
$maxWidthClasses = match ($maxWidth) {
    'sm' => 'max-w-sm',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    default => 'max-w-md',
};
@endphp

<div
    x-data="{ show: false }"
    x-show="show"
    x-cloak
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $name }}-title"
>
    <!-- Backdrop Overlay -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm transition-opacity"
        @click="show = false"
    ></div>

    <!-- Modal Box -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="bg-white rounded-2xl {{ $overflow }} shadow-2xl transform transition-all w-full {{ $maxWidthClasses }} z-10 border border-neutral-100"
        @click.stop
    >
        @if ($title)
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100">
                <h3 class="text-base font-bold text-neutral-900" id="{{ $name }}-title">
                    {{ $title }}
                </h3>
                <button
                    type="button"
                    @click="show = false"
                    class="text-neutral-400 hover:text-neutral-700 p-1.5 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-lb-orange"
                    aria-label="Close modal"
                >
                    <i class="fas fa-times text-sm" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        <div class="p-4 sm:px-6 sm:py-5">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="px-6 py-3.5 bg-neutral-50 border-t border-neutral-100 flex items-center justify-end gap-2.5">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
