@props(['taskGroups' => [], 'activeTaskGroupId' => null])

<div id="task-group-list-container" class="space-y-2">
    @forelse($taskGroups as $taskGroup)
        <x-task-group.task-group-item
            :taskGroup="$taskGroup"
            :isExpanded="true"
            :isActive="$activeTaskGroupId === $taskGroup->id"
        >
            {{-- TaskLists will be inserted here by parent component --}}
            @if(isset($taskGroup->taskLists) && $taskGroup->taskLists->isNotEmpty())
                @foreach($taskGroup->taskLists as $taskList)
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
            @endif
        </x-task-group.task-group-item>
    @empty
        {{-- No TaskGroups --}}
    @endforelse
</div>
