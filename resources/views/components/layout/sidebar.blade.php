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

        <!-- TaskGroup & TaskList Section (Scrollable) -->
        <div class="flex-1 overflow-hidden p-4">
            <div id="sidebar-content-container" class="space-y-2 overflow-y-auto" style="max-height: calc(100vh - 24rem);">
                <!-- TaskGroups with nested TaskLists -->
                @if(isset($taskGroups) && count($taskGroups) > 0)
                    <x-task-group.task-group-list
                        :taskGroups="$taskGroups"
                        :activeTaskGroupId="null"
                    />
                @endif

                <!-- Ungrouped TaskLists -->
                @if(isset($ungroupedTaskLists) && count($ungroupedTaskLists) > 0)
                    <div class="ungrouped-tasklists mt-4">
                        <h4 class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            모든 목록
                        </h4>
                        <div id="ungrouped-tasklist-items-container" class="space-y-1">
                            @foreach($ungroupedTaskLists as $taskList)
                                <div class="group flex items-center justify-between rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 {{ request()->route('task_list') == $taskList->id ? 'bg-blue-50 text-blue-700' : '' }}" data-tasklist-id="{{ $taskList->id }}">
                                    <!-- Left: Drag + Icon + Link -->
                                    <a href="{{ route('task-lists.show', $taskList->id) }}" class="flex flex-1 items-center gap-3">
                                        <!-- Drag Handle Icon -->
                                        <svg
                                            class="drag-handle h-4 w-4 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-label="드래그 핸들"
                                            role="button"
                                            tabindex="0"
                                        >
                                            <path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z" />
                                        </svg>

                                        <x-icons.task_list class="h-4 w-4 flex-shrink-0 {{ request()->route('task_list') == $taskList->id ? 'text-blue-600' : 'text-gray-500' }}" />
                                        <span class="text-sm font-medium">{{ $taskList->name }}</span>
                                    </a>

                                    <!-- Right: Count + More Button -->
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500">{{ $taskList->incompleteTaskCount ?? 0 }}</span>

                                        <!-- More Button (⋯) -->
                                        <button
                                            type="button"
                                            class="more-button transition-opacity duration-200
                                                   w-6 h-6 flex items-center justify-center
                                                   text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded"
                                            data-entity-type="tasklist"
                                            data-entity-id="{{ $taskList->id }}"
                                            data-entity-name="{{ $taskList->name }}"
                                            aria-label="메뉴 열기"
                                            aria-haspopup="true"
                                        >
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Fallback: All TaskLists (if $taskGroups not provided) -->
                @if(!isset($taskGroups) && isset($taskLists))
                    <div id="tasklist-items-container" class="space-y-1">
                        @forelse($taskLists as $taskList)
                            <div class="group flex items-center justify-between rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 {{ request()->route('task_list') == $taskList->id ? 'bg-blue-50 text-blue-700' : '' }}" data-tasklist-id="{{ $taskList->id }}">
                                <!-- Left: Drag + Icon + Link -->
                                <a href="{{ route('task-lists.show', $taskList->id) }}" class="flex flex-1 items-center gap-3">
                                    <!-- Drag Handle Icon -->
                                    <svg
                                        class="drag-handle h-4 w-4 flex-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-label="드래그 핸들"
                                        role="button"
                                        tabindex="0"
                                    >
                                        <path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z" />
                                    </svg>

                                    <x-icons.task_list class="h-4 w-4 flex-shrink-0 {{ request()->route('task_list') == $taskList->id ? 'text-blue-600' : 'text-gray-500' }}" />
                                    <span class="text-sm font-medium">{{ $taskList->name }}</span>
                                </a>

                                <!-- Right: Count + More Button -->
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500">{{ $taskList->incompleteTaskCount ?? 0 }}</span>

                                    <!-- More Button (⋯) -->
                                    <button
                                        type="button"
                                        class="more-button transition-opacity duration-200
                                               w-6 h-6 flex items-center justify-center
                                               text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded"
                                        data-entity-type="tasklist"
                                        data-entity-id="{{ $taskList->id }}"
                                        data-entity-name="{{ $taskList->name }}"
                                        aria-label="메뉴 열기"
                                        aria-haspopup="true"
                                    >
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <!-- 목록이 없을 때는 아무것도 표시하지 않음 -->
                        @endforelse
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Add TaskGroup Form -->
        <x-task-group.task-group-form />

        <!-- Quick Add TaskList Form -->
        <div class="border-t border-gray-200 p-4">
            <form id="quick-add-tasklist-form" class="flex items-center gap-3">
                <x-icons.plus class="h-5 w-5 text-gray-400 flex-shrink-0"/>
                <input
                    type="text"
                    id="quick-add-tasklist-input"
                    name="name"
                    placeholder="목록 추가"
                    autocomplete="off"
                    class="flex-1 border-0 bg-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-0 p-0"
                />
            </form>
        </div>
    </div>
</aside>

{{-- Dropdown Menu (전역 - sidebar 밖에 위치) --}}
<x-ui.dropdown-menu />
