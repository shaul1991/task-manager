<x-layouts.app>
    <x-slot:title>할 일 수정 - Task Manager</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">할 일 수정</h1>
            <p class="mt-2 text-gray-600">할 일의 내용을 수정합니다</p>
        </div>

        <!-- Form Card -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm p-6">
            @include('tasks.form', [
                'task' => $task,
                'action' => route('tasks.update', $task->id),
                'method' => 'PUT'
            ])
        </div>

        <!-- Delete Section -->
        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-6">
            <h3 class="text-lg font-medium text-red-900">위험 영역</h3>
            <p class="mt-2 text-sm text-red-700">이 할 일을 삭제하면 복구할 수 없습니다.</p>

            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="mt-4" onsubmit="return confirm('정말로 이 할 일을 삭제하시겠습니까?');">
                @csrf
                @method('DELETE')

                <x-ui.button variant="danger" type="submit">
                    할 일 삭제
                </x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.app>
