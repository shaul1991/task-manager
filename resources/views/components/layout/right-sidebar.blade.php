{{--
    Right Sidebar Component
    Main content와 독립적으로 관리되는 우측 사이드바
    필요할 때만 토글하여 노출
--}}

@props([
    'id' => 'right-sidebar',
    'title' => '상세정보',
    'width' => '30vw', // 기본 너비 30vw
])

{{-- Desktop Right Sidebar (md 이상에서만 표시) --}}
<div
    id="{{ $id }}"
    class="hidden md:block md:w-0 overflow-hidden transition-all duration-300 ease-in-out bg-white border-l border-gray-200 shadow-xl"
    data-width="{{ $width }}"
>
    <div id="{{ $id }}-content" class="h-full overflow-y-auto" style="width: {{ $width }};">
        {{-- Header with Close Button --}}
        <div class="sticky top-0 z-10 bg-gray-50 px-4 py-6 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">{{ $title }}</h2>
                <button
                    type="button"
                    data-close-sidebar="{{ $id }}"
                    class="rounded-md bg-gray-50 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <span class="sr-only">닫기</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Sidebar Content --}}
        <div class="px-4 py-6 sm:px-6">
            {{ $slot }}
        </div>
    </div>
</div>

{{-- Mobile Slide-over Modal (md 미만에서만 표시) --}}
<div class="md:hidden">
    <x-ui.slide-over :id="$id . '-mobile'" :title="$title" width="lg">
        {{ $slot }}
    </x-ui.slide-over>
</div>
