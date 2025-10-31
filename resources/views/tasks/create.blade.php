<x-layouts.app>
    <x-slot:title>할 일 추가 - Task Manager</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">할 일 추가</h1>
            <p class="mt-2 text-gray-600">새로운 할 일을 생성합니다</p>
        </div>

        <!-- Form Card -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm p-6">
            @include('tasks.form', [
                'task' => null,
                'action' => route('tasks.store'),
                'method' => 'POST'
            ])
        </div>
    </div>
</x-layouts.app>
