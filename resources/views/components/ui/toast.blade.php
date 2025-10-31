{{-- Toast Notification Component --}}
{{-- JavaScript will control visibility and content dynamically --}}

<div id="toast-container"
     class="fixed top-20 right-4 z-50 flex flex-col gap-2"
     role="status"
     aria-live="polite"
>
    {{-- Toasts will be dynamically inserted here --}}
</div>

@push('scripts')
<script>
// Toast utility
window.toast = {
    show: function(message, type = 'success', duration = 3000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'toast-item flex items-center gap-3 rounded-lg shadow-lg px-4 py-3 min-w-[280px] max-w-md transform translate-x-full transition-transform duration-300';

        // Set background color based on type
        const bgColors = {
            success: 'bg-green-50 border border-green-200',
            error: 'bg-red-50 border border-red-200',
            info: 'bg-blue-50 border border-blue-200',
            warning: 'bg-yellow-50 border border-yellow-200',
            saving: 'bg-gray-50 border border-gray-200'
        };
        toast.className += ' ' + (bgColors[type] || bgColors.info);

        // Icon based on type
        let icon = '';
        switch(type) {
            case 'success':
                icon = `<svg class="h-5 w-5 text-green-600 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>`;
                break;
            case 'error':
                icon = `<svg class="h-5 w-5 text-red-600 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>`;
                break;
            case 'saving':
                icon = `<svg class="h-5 w-5 animate-spin text-gray-600 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>`;
                break;
            case 'info':
                icon = `<svg class="h-5 w-5 text-blue-600 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
                break;
            case 'warning':
                icon = `<svg class="h-5 w-5 text-yellow-600 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>`;
                break;
        }

        // Text color based on type
        const textColors = {
            success: 'text-green-800',
            error: 'text-red-800',
            info: 'text-blue-800',
            warning: 'text-yellow-800',
            saving: 'text-gray-800'
        };
        const textColor = textColors[type] || textColors.info;

        toast.innerHTML = `
            ${icon}
            <span class="flex-1 text-sm font-medium ${textColor}">${message}</span>
            ${type !== 'saving' ? `
                <button class="toast-close text-gray-400 hover:text-gray-600 flex-shrink-0">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            ` : ''}
        `;

        container.appendChild(toast);

        // Slide in animation
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
        }, 10);

        // Auto dismiss (except for 'saving' type)
        if (type !== 'saving' && duration > 0) {
            setTimeout(() => {
                this.hide(toast);
            }, duration);
        }

        // Close button
        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                this.hide(toast);
            });
        }

        return toast;
    },

    hide: function(toast) {
        if (!toast) return;

        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');

        setTimeout(() => {
            toast.remove();
        }, 300);
    },

    success: function(message, duration = 3000) {
        return this.show(message, 'success', duration);
    },

    error: function(message, duration = 3000) {
        return this.show(message, 'error', duration);
    },

    info: function(message, duration = 3000) {
        return this.show(message, 'info', duration);
    },

    warning: function(message, duration = 3000) {
        return this.show(message, 'warning', duration);
    },

    saving: function(message) {
        return this.show(message, 'saving', 0); // No auto-dismiss for saving
    }
};
</script>
@endpush
