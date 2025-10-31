{{--
    Delete Confirmation Modal Component

    Usage:
    <x-ui.delete-confirmation-modal />

    JavaScript will control visibility and content dynamically
--}}

<div id="delete-confirmation-modal"
     class="modal fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50"
     role="dialog"
     aria-labelledby="modal-title"
     aria-modal="true"
>
    <div class="modal-content relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
        {{-- Header --}}
        <div class="mb-4 flex items-start justify-between">
            <div class="flex items-center gap-3">
                {{-- Warning Icon --}}
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>

                <div>
                    <h3 id="modal-title" class="text-lg font-semibold text-gray-900">
                        <span id="modal-entity-type-text">목록</span> 삭제
                    </h3>
                </div>
            </div>

            {{-- Close Button --}}
            <button
                type="button"
                class="modal-close text-gray-400 hover:text-gray-600"
                aria-label="닫기"
            >
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="mb-6">
            <p class="text-sm text-gray-600">
                <span class="font-semibold text-gray-900" id="modal-entity-name">목록명</span><span id="modal-entity-type-ko">을(를)</span> 정말 삭제하시겠습니까?
            </p>

            {{-- TaskList specific warning --}}
            <div id="modal-tasklist-warning" class="mt-3 hidden rounded-lg bg-yellow-50 p-3">
                <p class="text-xs text-yellow-800">
                    ⚠️ 이 목록에 속한 할 일들은 <strong>그룹 없음 상태</strong>로 이동됩니다.
                </p>
            </div>

            {{-- TaskGroup specific warning --}}
            <div id="modal-taskgroup-warning" class="mt-3 hidden rounded-lg bg-yellow-50 p-3">
                <p class="text-xs text-yellow-800">
                    ⚠️ 이 그룹에 속한 모든 <strong>목록과 할 일</strong>이 함께 삭제됩니다.
                </p>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-end gap-3">
            <button
                type="button"
                class="modal-cancel rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                취소
            </button>
            <button
                type="button"
                id="modal-confirm-delete"
                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
            >
                삭제
            </button>
        </div>
    </div>
</div>
