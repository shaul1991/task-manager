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
                                <a
                                    href="{{ route('task-lists.show', $taskList->id) }}"
                                    class="flex items-center justify-between rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 {{ request()->route('task_list') == $taskList->id ? 'bg-blue-50 text-blue-700' : '' }}"
                                    data-tasklist-id="{{ $taskList->id }}"
                                >
                                    <div class="flex items-center gap-3">
                                        <x-icons.task_list class="h-4 w-4 {{ request()->route('task_list') == $taskList->id ? 'text-blue-600' : 'text-gray-500' }}" />
                                        <span class="text-sm font-medium">{{ $taskList->name }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $taskList->incompleteTaskCount ?? 0 }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Fallback: All TaskLists (if $taskGroups not provided) -->
                @if(!isset($taskGroups) && isset($taskLists))
                    <div id="tasklist-items-container" class="space-y-1">
                        @forelse($taskLists as $taskList)
                            <a
                                href="{{ route('task-lists.show', $taskList->id) }}"
                                class="flex items-center justify-between rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 {{ request()->route('task_list') == $taskList->id ? 'bg-blue-50 text-blue-700' : '' }}"
                                data-tasklist-id="{{ $taskList->id }}"
                            >
                                <div class="flex items-center gap-3">
                                    <x-icons.task_list class="h-4 w-4 {{ request()->route('task_list') == $taskList->id ? 'text-blue-600' : 'text-gray-500' }}" />
                                    <span class="text-sm font-medium">{{ $taskList->name }}</span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $taskList->incompleteTaskCount ?? 0 }}</span>
                            </a>
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
