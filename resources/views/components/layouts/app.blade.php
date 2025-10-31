<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Task Manager' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/layout.js', 'resources/js/task-lists/quickAddTaskList.js'])
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Fixed Header -->
        <x-layout.header />

        <!-- Flex Container for Sidebar, Main, and Right Sidebar -->
        <div class="flex pt-16">
            <!-- Left Sidebar (PC: fixed, Mobile: slide) -->
            <x-layout.sidebar />

            <!-- Main Contents -->
            <main class="flex-1 sm:ml-64 flex flex-col h-[calc(100vh-4rem)]">
                <div class="flex-1 flex flex-col overflow-hidden">
                    <div class="flex-1 flex overflow-hidden">
                        <!-- Main Content Area -->
                        <div id="main-content" class="flex-1 flex flex-col overflow-hidden">
                            <!-- Main Header (고정 영역) -->
                            @isset($mainHeader)
                                <div class="flex-shrink-0 p-6 pb-0">
                                    {{ $mainHeader }}
                                </div>
                            @endif

                            <!-- Main Body (스크롤 가능 영역) -->
                            <div class="flex-1 overflow-y-auto p-6">
                                {{ $slot }}
                            </div>
                        </div>

                        <!-- Right Sidebar (토글 가능) -->
                        @isset($rightSidebar)
                            {{ $rightSidebar }}
                        @endisset
                    </div>
                </div>
            </main>
        </div>

        <!-- Footer (Hidden - Quick Add UI replaces footer area) -->
        <x-layout.footer class="hidden sm:ml-64" />

        <!-- Mobile Overlay -->
        <x-layout.overlay />

        <!-- Toast Notification Container -->
        <x-ui.toast />
    </div>

    <!-- Page-specific Scripts -->
    @stack('scripts')
</body>
</html>
