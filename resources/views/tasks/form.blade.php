{{-- Task Form Partial --}}
{{-- Variables: $task (null for create), $action, $method --}}

<form action="{{ $action }}" method="POST" id="task-form">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <!-- Error Messages -->
    @if(session('error'))
        <x-ui.form-error :message="session('error')" />
    @endif

    <!-- Title Field -->
    <x-ui.input
        name="title"
        label="제목"
        :value="$task?->title ?? ''"
        required
        placeholder="할 일 제목을 입력하세요"
    />

    <!-- Description Field -->
    <x-ui.textarea
        name="description"
        label="설명"
        :value="$task?->description ?? ''"
        :rows="6"
        placeholder="할 일에 대한 상세한 설명을 입력하세요 (선택사항)"
    />

    <!-- Form Actions -->
    <div class="flex items-center justify-end gap-3 mt-6">
        <a href="{{ route('tasks.index') }}">
            <x-ui.button variant="outline" type="button">
                취소
            </x-ui.button>
        </a>

        <x-ui.button variant="primary" type="submit">
            {{ $task ? '수정하기' : '생성하기' }}
        </x-ui.button>
    </div>
</form>

@push('scripts')
    @vite(['resources/js/tasks/taskForm.js'])
@endpush
