@props(['taskGroup', 'isExpanded' => true, 'isActive' => false])

<div class="task-group-container" data-task-group-id="{{ $taskGroup->id }}">
    <!-- TaskGroup Header (Accordion Trigger) -->
    <button
        type="button"
        class="task-group-header flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-gray-700 hover:bg-gray-100 {{ $isActive ? 'bg-blue-50 text-blue-700' : '' }}"
        data-task-group-toggle="{{ $taskGroup->id }}"
        aria-expanded="{{ $isExpanded ? 'true' : 'false' }}"
    >
        <div class="flex items-center gap-3">
            <!-- Expand/Collapse Icon -->
            <svg
                class="task-group-chevron h-4 w-4 transition-transform {{ $isExpanded ? 'rotate-90' : '' }}"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
            </svg>

            <!-- TaskGroup Icon -->
            <x-icons.task_group class="h-4 w-4 {{ $isActive ? 'text-blue-600' : 'text-gray-500' }}" />

            <!-- TaskGroup Name -->
            <span class="text-sm font-semibold">{{ $taskGroup->name }}</span>
        </div>

        <!-- Incomplete Task Count -->
        <span class="text-xs text-gray-500">{{ $taskGroup->incompleteTaskCount ?? 0 }}</span>
    </button>

    <!-- TaskGroup Content (TaskLists) -->
    <div
        class="task-group-content {{ $isExpanded ? '' : 'hidden' }}"
        data-task-group-content="{{ $taskGroup->id }}"
    >
        <div class="ml-6 space-y-1 border-l-2 border-gray-200 pl-3 py-1">
            {{ $slot }}
        </div>
    </div>
</div>
