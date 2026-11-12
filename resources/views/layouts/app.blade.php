<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SME Nexus') }}</title>

        <script>
            try { document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('darkMode') === 'true' ? 'dark' : 'light'); } catch (e) {}
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
            [x-cloak] { display: none !important; }
            ::selection { background: rgba(20, 184, 166, .25); }
        </style>
    </head>
    <body class="antialiased text-gray-900 dark:text-gray-100">
        @include('layouts.navigation')

        @isset($header)
            <header class="nx-header py-3">
                <div class="container">{{ $header }}</div>
            </header>
        @endisset

        <main class="container py-4">
            {{ $slot }}
        </main>

                <footer class="mx-auto w-full max-w-7xl px-4 py-8 text-center text-xs text-gray-400 dark:text-gray-500 sm:px-6 lg:px-8">
                    &copy; {{ date('Y') }} {{ config('app.name', 'SME Nexus') }}. Semua hak dilindungi.
                </footer>
    </body>
</html>