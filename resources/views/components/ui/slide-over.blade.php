{{-- Slide-over Modal Component --}}
{{-- Usage: <x-ui.slide-over id="task-detail" title="할 일 상세">content</x-ui.slide-over> --}}

<div id="{{ $id }}" class="slide-over fixed inset-0 z-50 hidden overflow-hidden" aria-labelledby="{{ $id }}-title" role="dialog" aria-modal="true">
    {{-- Background overlay --}}
    <div class="slide-over-backdrop absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity opacity-0"></div>

    {{-- Slide-over panel container --}}
    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
        {{-- Slide-over panel --}}
        <div class="slide-over-panel pointer-events-none relative w-screen {{ $getWidthClass() }} translate-x-full transition-transform">
            <div class="pointer-events-auto flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                {{-- Header --}}
                <div class="bg-gray-50 px-4 py-6 sm:px-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900" id="{{ $id }}-title">
                            {{ $title }}
                        </h2>
                        <div class="ml-3 flex h-7 items-center">
                            <button type="button" class="slide-over-close rounded-md bg-gray-50 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <span class="sr-only">닫기</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="relative flex-1 px-4 py-6 sm:px-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Slide-over utility functions
window.slideOver = {
    open: function(id) {
        const element = document.getElementById(id);
        if (!element) return;

        const backdrop = element.querySelector('.slide-over-backdrop');
        const panel = element.querySelector('.slide-over-panel');

        // Show the slide-over
        element.classList.remove('hidden');

        // Trigger animations
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-x-full');
            panel.classList.add('translate-x-0');
        }, 10);

        // Add event listeners
        const closeButton = element.querySelector('.slide-over-close');
        const backdropElement = element.querySelector('.slide-over-backdrop');

        const closeHandler = () => this.close(id);

        closeButton.addEventListener('click', closeHandler);
        backdropElement.addEventListener('click', closeHandler);

        // ESC key to close
        const escHandler = (e) => {
            if (e.key === 'Escape') {
                this.close(id);
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);

        // Store handlers for cleanup
        element._closeHandler = closeHandler;
        element._escHandler = escHandler;
    },

    close: function(id) {
        const element = document.getElementById(id);
        if (!element) return;

        const backdrop = element.querySelector('.slide-over-backdrop');
        const panel = element.querySelector('.slide-over-panel');

        // Trigger close animations
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-x-0');
        panel.classList.add('translate-x-full');

        // Hide after animation completes
        setTimeout(() => {
            element.classList.add('hidden');
        }, 300);

        // Cleanup event listeners
        if (element._escHandler) {
            document.removeEventListener('keydown', element._escHandler);
        }
    },

    isOpen: function(id) {
        const element = document.getElementById(id);
        return element && !element.classList.contains('hidden');
    }
};
</script>
@endpush
