<x-layouts.app>
    <x-slot:title>할 일 목록 - Task Manager</x-slot:title>

    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">할 일 목록</h1>
            <p class="mt-2 text-gray-600">총 {{ $total }}개의 할 일</p>
        </div>

        <a href="{{ route('tasks.create') }}">
            <x-ui.button variant="primary">
                <x-icons.plus class="w-5 h-5 mr-2" />
                할 일 추가
            </x-ui.button>
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <x-ui.form-error :message="session('error')" class="mb-6" />
    @endif

    <!-- Tasks List -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        @if(count($tasks) > 0)
            <div class="divide-y divide-gray-200">
                @foreach($tasks as $task)
                    <div class="p-4 hover:bg-gray-50 transition-colors" data-task-id="{{ $task->id }}">
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
                                class="flex-1 min-w-0 cursor-pointer"
                                onclick="window.openTaskDetail({{ $task->id }})"
                            >
                                <h3 class="task-title font-medium {{ $task->isCompleted ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                    {{ $task->title }}
                                </h3>

                                @if($task->description)
                                    <p class="task-description mt-1 text-sm text-gray-600">
                                        {{ Str::limit($task->description, 100) }}
                                    </p>
                                @else
                                    <p class="task-description mt-1 text-sm text-gray-400 italic">설명 없음</p>
                                @endif

                                <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                                    <span>생성: {{ date('Y-m-d H:i', strtotime($task->createdAt)) }}</span>

                                    @if($task->completedDateTime)
                                        <span class="text-green-600">
                                            완료: {{ date('Y-m-d H:i', strtotime($task->completedDateTime)) }}
                                        </span>
                                    @endif

                                    <span class="task-updated-at">수정: {{ date('Y-m-d H:i', strtotime($task->updatedAt)) }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tasks.edit', $task->id) }}">
                                    <x-ui.button variant="outline" size="small">
                                        수정
                                    </x-ui.button>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <x-icons.clipboard class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900">할 일이 없습니다</h3>
                <p class="mt-2 text-sm text-gray-600">새로운 할 일을 추가해보세요</p>
                <div class="mt-6">
                    <a href="{{ route('tasks.create') }}">
                        <x-ui.button variant="primary">
                            <x-icons.plus class="w-5 h-5 mr-2" />
                            할 일 추가
                        </x-ui.button>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Task Detail Slide-over Modal -->
    <x-ui.slide-over id="task-detail-modal" title="할 일 상세" width="lg">
        @include('tasks._detail-modal')
    </x-ui.slide-over>

    @push('scripts')
        @vite(['resources/js/tasks/taskSlideOver.js'])
    @endpush
</x-layouts.app>
