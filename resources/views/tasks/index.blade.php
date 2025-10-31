<x-layouts.app>
    <x-slot:title>할 일 목록 - Task Manager</x-slot:title>

    {{-- Main Header Slot --}}
    <x-slot:mainHeader>
        <x-layout.main-header
                title="할 일 목록"
                subtitle="총 {{ $total }}개의 할 일"
        >
            <x-slot:content>
                <!-- Success Message -->
                @if(session('success'))
                    <div class="rounded-lg bg-green-50 border border-green-200 p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Error Message -->
                @if(session('error'))
                    <x-ui.form-error :message="session('error')"/>
                @endif
            </x-slot:content>
        </x-layout.main-header>
    </x-slot:mainHeader>

    {{-- Main Body Content --}}
    <!-- Tasks List (Scrollable Area) -->
    <x-task.task-list :tasks="$tasks" />

    {{-- Quick Add Task (하단 고정) --}}
    <div id="quick-add-task-bar"
         class="fixed bottom-0 left-0 sm:left-64 right-0 z-10 border-t border-gray-200 bg-white p-4 shadow-lg transition-all duration-300 ease-in-out">
        <form id="quick-add-task-form" class="flex items-center gap-3">
            <x-icons.plus class="h-5 w-5 text-gray-400 flex-shrink-0"/>
            <input
                    type="text"
                    id="quick-add-task-input"
                    name="title"
                    placeholder="작업 추가"
                    autocomplete="off"
                    class="flex-1 border-0 bg-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-0 p-0"
            />
        </form>
    </div>

    {{-- Right Sidebar Slot --}}
    <x-slot:rightSidebar>
        <x-layout.right-sidebar id="right-panel" title="할 일 상세">
            @include('tasks._detail-modal')
        </x-layout.right-sidebar>
    </x-slot:rightSidebar>

    @push('scripts')
        @vite(['resources/js/tasks/taskSlideOver.js', 'resources/js/tasks/quickAddTask.js'])
    @endpush
</x-layouts.app>
