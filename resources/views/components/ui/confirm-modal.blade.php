{{--
    Confirm Modal Component

    Universal confirm modal with Promise-based API

    Usage:
    <x-ui.confirm-modal />

    JavaScript API:
    const confirmed = await window.confirmModal.show({
        title: '할 일 삭제',
        message: '정말 삭제하시겠습니까?',
        confirmText: '삭제',
        cancelText: '취소',
        type: 'danger'  // 'danger', 'warning', 'info'
    });
--}}

<div id="confirm-modal"
     class="modal fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50"
     role="dialog"
     aria-labelledby="confirm-modal-title"
     aria-modal="true"
>
    <div class="modal-content relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl transform transition-all">
        {{-- Header --}}
        <div class="mb-4 flex items-start justify-between">
            <div class="flex items-center gap-3">
                {{-- Icon (동적 변경) --}}
                <div id="confirm-modal-icon" class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>

                <div>
                    <h3 id="confirm-modal-title" class="text-lg font-semibold text-gray-900">
                        확인
                    </h3>
                </div>
            </div>

            {{-- Close Button --}}
            <button
                type="button"
                class="confirm-modal-close text-gray-400 hover:text-gray-600"
                aria-label="닫기"
            >
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="mb-6">
            <p id="confirm-modal-message" class="text-sm text-gray-600 whitespace-pre-line">
                계속하시겠습니까?
            </p>
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-end gap-3">
            <button
                type="button"
                id="confirm-modal-cancel"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                취소
            </button>
            <button
                type="button"
                id="confirm-modal-confirm"
                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
            >
                확인
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
/**
 * Confirm Modal Utility
 * Promise-based confirmation modal (similar to window.toast pattern)
 */
window.confirmModal = {
    // Current resolver for the promise
    currentResolve: null,

    /**
     * Show confirm modal
     * @param {Object} options
     * @param {string} options.title - Modal title
     * @param {string} options.message - Modal message
     * @param {string} [options.confirmText='확인'] - Confirm button text
     * @param {string} [options.cancelText='취소'] - Cancel button text
     * @param {string} [options.type='danger'] - Modal type: 'danger', 'warning', 'info'
     * @returns {Promise<boolean>} - Resolves to true if confirmed, false if cancelled
     */
    show: function(options = {}) {
        const {
            title = '확인',
            message = '계속하시겠습니까?',
            confirmText = '확인',
            cancelText = '취소',
            type = 'danger'
        } = options;

        const modal = document.getElementById('confirm-modal');
        if (!modal) return Promise.resolve(false);

        // Set content
        document.getElementById('confirm-modal-title').textContent = title;
        document.getElementById('confirm-modal-message').textContent = message;
        document.getElementById('confirm-modal-cancel').textContent = cancelText;
        document.getElementById('confirm-modal-confirm').textContent = confirmText;

        // Set icon and button color based on type
        this.setType(type);

        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Return promise
        return new Promise((resolve) => {
            this.currentResolve = resolve;
        });
    },

    /**
     * Set modal type (icon, button color)
     */
    setType: function(type) {
        const iconContainer = document.getElementById('confirm-modal-icon');
        const confirmButton = document.getElementById('confirm-modal-confirm');

        // Icon configurations
        const icons = {
            danger: {
                bgClass: 'bg-red-100',
                iconColor: 'text-red-600',
                buttonClass: 'bg-red-600 hover:bg-red-700',
                svg: '<path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />'
            },
            warning: {
                bgClass: 'bg-yellow-100',
                iconColor: 'text-yellow-600',
                buttonClass: 'bg-yellow-600 hover:bg-yellow-700',
                svg: '<path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />'
            },
            info: {
                bgClass: 'bg-blue-100',
                iconColor: 'text-blue-600',
                buttonClass: 'bg-blue-600 hover:bg-blue-700',
                svg: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />'
            }
        };

        const config = icons[type] || icons.danger;

        // Update icon container
        iconContainer.className = `flex h-10 w-10 items-center justify-center rounded-full ${config.bgClass}`;
        iconContainer.innerHTML = `<svg class="h-6 w-6 ${config.iconColor}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">${config.svg}</svg>`;

        // Update button
        confirmButton.className = `rounded-lg px-4 py-2 text-sm font-medium text-white ${config.buttonClass}`;
    },

    /**
     * Hide modal
     */
    hide: function() {
        const modal = document.getElementById('confirm-modal');
        if (!modal) return;

        modal.classList.remove('flex');
        modal.classList.add('hidden');
    },

    /**
     * Resolve with confirmation
     */
    confirm: function() {
        if (this.currentResolve) {
            this.currentResolve(true);
            this.currentResolve = null;
        }
        this.hide();
    },

    /**
     * Resolve with cancellation
     */
    cancel: function() {
        if (this.currentResolve) {
            this.currentResolve(false);
            this.currentResolve = null;
        }
        this.hide();
    }
};

/**
 * Initialize confirm modal
 */
function initConfirmModal() {
    const modal = document.getElementById('confirm-modal');
    if (!modal) return;

    // Confirm button
    const confirmButton = document.getElementById('confirm-modal-confirm');
    if (confirmButton) {
        confirmButton.addEventListener('click', function() {
            window.confirmModal.confirm();
        });
    }

    // Cancel button
    const cancelButton = document.getElementById('confirm-modal-cancel');
    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            window.confirmModal.cancel();
        });
    }

    // Close button
    const closeButtons = modal.querySelectorAll('.confirm-modal-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            window.confirmModal.cancel();
        });
    });

    // Backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            window.confirmModal.cancel();
        }
    });

    // Keyboard support
    document.addEventListener('keydown', function(e) {
        if (!modal.classList.contains('flex')) return;

        if (e.key === 'Enter') {
            e.preventDefault();
            window.confirmModal.confirm();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            window.confirmModal.cancel();
        }
    });
}

/**
 * Initialize when DOM is ready
 */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initConfirmModal);
} else {
    initConfirmModal();
}
</script>
@endpush
