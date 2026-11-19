<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'LearnBuddy · Smart Learning Platform' }}</title>
    <meta name="description" content="Commercial-quality Learning Management System in Sri Lanka offering verified online, physical classroom, and hybrid courses.">

    <!-- Brand Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets (Tailwind CSS, Font Awesome, Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-lb-black font-sans antialiased min-h-screen flex flex-col selection:bg-orange-500 selection:text-white no-overflow-x">
    <!-- Accessible Skip to Content Link -->
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-lb-orange focus:text-white focus:rounded-full focus:shadow-lg focus:outline-none"
    >
        Skip to main content
    </a>

    <!-- Application Shell Header -->
    <x-header />

    <!-- Main Content Slot -->
    <main id="main-content" class="flex-grow w-full" x-data>
        {{ $slot }}
    </main>

    <!-- Application Shell Footer -->
    <x-footer />

    <!-- Shared Search Modal -->
    <x-search-modal />

    <!-- Modern Shared Confirmation & Delete Dialog -->
    <x-confirm-dialog />

    <!-- Global Support Modal -->
    <x-support-modal />

    @stack('scripts')
</body>
</html>
