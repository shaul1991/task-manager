{{--
    Task List Component

    Props:
    - $tasks: Collection of tasks to display
    - $emptyMessage: Custom message when no tasks (optional)
--}}

@props([
    'tasks',
    'emptyMessage' => '할 일이 없습니다'
])

<div class="space-y-2 mb-20">
    @if(count($tasks) > 0)
        @foreach($tasks as $task)
            <div class="p-4 hover:bg-gray-50 rounded-lg border border-gray-200 bg-white shadow-sm transition-colors cursor-pointer"
                 data-task-id="{{ $task->id }}">
                <div class="flex items-start gap-4">
                    <!-- Checkbox -->
                    <input
                        type="checkbox"
                        {{ $task->isCompleted ? 'checked' : '' }}
                        class="mt-1 h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        disabled
                    />

                    <!-- Task Content (Clickable to open slide-over) -->
                    <div
                        class="flex-1 min-w-0"
                        onclick="window.openTaskDetail({{ $task->id }})"
                    >
                        {{-- TaskList 뱃지 (있는 경우만 표시) --}}
                        @if($task->taskList)
                            <span class="inline-block px-2 py-0.5 mb-1 text-xs font-medium bg-gray-100 text-gray-600 rounded">
                                {{ $task->taskList->name }}
                            </span>
                        @endif

                        <h3 class="task-title font-medium {{ $task->isCompleted ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                            {{ $task->title }}
                        </h3>

                        @if($task->description)
                            <p class="task-description mt-1 text-sm text-gray-600">
                                {{ Str::limit($task->description, 100) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Empty State -->
        <div class="p-8 text-center rounded-lg border border-gray-200 bg-white shadow-sm">
            <x-icons.clipboard class="mx-auto h-12 w-12 text-gray-400"/>
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $emptyMessage }}</h3>
            <p class="mt-2 text-sm text-gray-600">아래에서 새로운 할 일을 추가해보세요</p>
        </div>
    @endif
</div>
