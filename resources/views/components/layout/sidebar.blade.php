<!-- Sidebar (PC: fixed, Mobile: slide) -->
<aside
    id="mobile-sidebar"
    class="fixed left-0 top-16 z-40 h-[calc(100vh-4rem)] w-64 -translate-x-full border-r border-gray-200 bg-white transition-transform duration-300 sm:translate-x-0"
>
    <div class="flex h-full flex-col">
        <!-- Navigation Menu -->
        <nav class="border-b border-gray-200 p-4">
            <ul class="space-y-2">
                <li>
                    <a
                        href="#"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100"
                    >
                        <x-icons.calendar class="h-5 w-5" />
                        <span class="font-medium">오늘 할 일</span>
                    </a>
                </li>
                <li>
                    <a
                        href="#"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100"
                    >
                        <x-icons.star class="h-5 w-5" />
                        <span class="font-medium">중요한 일</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('tasks.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100"
                    >
                        <x-icons.clipboard class="h-5 w-5" />
                        <span class="font-medium">전체 할 일</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- TaskList Section -->
        <div class="flex-1 overflow-hidden p-4">
{{--            <!-- Section Header -->--}}
{{--            <div class="mb-4 flex items-center justify-between">--}}
{{--                <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500">내 목록</h3>--}}
{{--                <button--}}
{{--                    type="button"--}}
{{--                    class="rounded-md p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700"--}}
{{--                    aria-label="새 목록 추가"--}}
{{--                >--}}
{{--                    <x-icons.plus class="h-5 w-5" />--}}
{{--                </button>--}}
{{--            </div>--}}

            <!-- TaskList Items (Scrollable) -->
            <div class="space-y-1 overflow-y-auto" style="max-height: calc(100vh - 20rem);">
                <!-- TaskList Item Example 1 -->
                <a
                    href="#"
                    class="flex items-center justify-between rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100"
                >
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                        <span class="text-sm font-medium">업무</span>
                    </div>
                    <span class="text-xs text-gray-500">5</span>
                </a>

                <!-- TaskList Item Example 2 -->
                <a
                    href="#"
                    class="flex items-center justify-between rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100"
                >
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-green-500"></div>
                        <span class="text-sm font-medium">쇼핑</span>
                    </div>
                    <span class="text-xs text-gray-500">3</span>
                </a>

                <!-- TaskList Item Example 3 -->
                <a
                    href="#"
                    class="flex items-center justify-between rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100"
                >
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-purple-500"></div>
                        <span class="text-sm font-medium">운동 루틴</span>
                    </div>
                    <span class="text-xs text-gray-500">7</span>
                </a>

                <!-- TaskList Item Example 4 -->
                <a
                    href="#"
                    class="flex items-center justify-between rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100"
                >
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                        <span class="text-sm font-medium">독서 목록</span>
                    </div>
                    <span class="text-xs text-gray-500">12</span>
                </a>
            </div>
        </div>

        <!-- Add New TaskList Button (Bottom) -->
        <div class="border-t border-gray-200 p-4">
            <button
                type="button"
                class="flex w-full items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <x-icons.plus class="h-5 w-5" />
                <span>새 목록 추가</span>
            </button>
        </div>
    </div>
</aside>
