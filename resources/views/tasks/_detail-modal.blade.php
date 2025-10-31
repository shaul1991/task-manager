{{-- Task Detail Modal Content --}}
{{-- This content will be loaded inside the slide-over component --}}

<div id="task-detail-content" class="space-y-6">
    {{-- Loading State --}}
    <div id="task-loading" class="flex items-center justify-center py-12">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">로딩 중...</p>
        </div>
    </div>

    {{-- Task Content (hidden initially) --}}
    <div id="task-content" class="hidden space-y-6">
        {{-- Save Status Indicator --}}
        <div id="save-status" class="hidden">
            <div class="flex items-center justify-between rounded-md bg-gray-50 px-3 py-2">
                <div class="flex items-center gap-2">
                    <svg id="save-spinner" class="hidden h-4 w-4 animate-spin text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg id="save-check" class="hidden h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span id="save-text" class="text-sm text-gray-600"></span>
                </div>
            </div>
        </div>

        {{-- Completed Checkbox --}}
        <div class="flex items-center gap-3">
            <input type="checkbox" id="task-completed" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
            <label for="task-completed" class="text-sm font-medium text-gray-700 cursor-pointer select-none">
                완료됨으로 표시
            </label>
        </div>

        {{-- Title Field --}}
        <div>
            <label for="task-title" class="block text-sm font-medium text-gray-700 mb-2">
                제목 <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="task-title"
                name="title"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="할 일 제목을 입력하세요"
            />
            <p id="task-title-error" class="mt-1 text-sm text-red-600 hidden"></p>
        </div>

        {{-- Description Field --}}
        <div>
            <label for="task-description" class="block text-sm font-medium text-gray-700 mb-2">
                설명
            </label>
            <textarea
                id="task-description"
                name="description"
                rows="6"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y"
                placeholder="할 일에 대한 상세한 설명을 입력하세요 (선택사항)"
            ></textarea>
        </div>

        {{-- Timestamps --}}
        <div class="space-y-2 border-t border-gray-200 pt-4">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">생성일</span>
                <span id="task-created-at" class="text-gray-900"></span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">수정일</span>
                <span id="task-updated-at" class="text-gray-900"></span>
            </div>
            <div id="task-completed-at-wrapper" class="hidden flex items-center justify-between text-sm">
                <span class="text-gray-500">완료일</span>
                <span id="task-completed-at" class="text-green-600"></span>
            </div>
        </div>

        {{-- Delete Section --}}
        <div class="border-t border-gray-200 pt-6">
            <button
                type="button"
                id="delete-task-btn"
                class="flex w-full items-center justify-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            >
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                할 일 삭제
            </button>
        </div>

        {{-- Link to full edit page --}}
        <div class="border-t border-gray-200 pt-4">
            <a
                id="full-edit-link"
                href="#"
                class="block text-center text-sm text-blue-600 hover:text-blue-700 hover:underline"
            >
                전체 페이지에서 수정하기 →
            </a>
        </div>
    </div>
</div>
