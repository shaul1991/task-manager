<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Task Manager' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/layout.js'])
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Fixed Header -->
        <x-layout.header />

        <!-- Flex Container for Sidebar and Main -->
        <div class="flex pt-16">
            <!-- Sidebar (PC: fixed, Mobile: slide) -->
            <x-layout.sidebar />

            <!-- Main Contents -->
            <main class="flex-1 sm:ml-64">
                <div class="p-6">
                    <!-- Page Title (optional) -->
                    @isset($pageTitle)
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
                            @isset($pageDescription)
                                <p class="mt-2 text-gray-600">{{ $pageDescription }}</p>
                            @endisset
                        </div>
                    @endisset

                    <!-- Main Content -->
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Footer -->
        <x-layout.footer class="sm:ml-64" />

        <!-- Mobile Overlay -->
        <x-layout.overlay />
    </div>
</body>
</html>
