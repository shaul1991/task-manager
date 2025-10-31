{{--
    Main Header Component
    페이지 상단의 고정 영역 (Title, Breadcrumb, Actions 등)
--}}

@props([
    'title' => null,
    'subtitle' => null,
    'breadcrumbs' => [],
])

<div class="mb-6">
    {{-- Breadcrumbs (선택적) --}}
    @if(!empty($breadcrumbs))
        <nav class="mb-3 flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                @foreach($breadcrumbs as $index => $breadcrumb)
                    <li class="inline-flex items-center">
                        @if($index > 0)
                            <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                        @if($loop->last)
                            <span class="text-sm font-medium text-gray-500">{{ $breadcrumb['label'] }}</span>
                        @else
                            <a href="{{ $breadcrumb['url'] ?? '#' }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                                {{ $breadcrumb['label'] }}
                            </a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif

    {{-- Title & Subtitle Section --}}
    <div class="flex items-center justify-between">
        <div>
            @if($title)
                <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
            @endif
            @if($subtitle)
                <p class="mt-2 text-gray-600">{{ $subtitle }}</p>
            @endif
        </div>

        {{-- Actions Slot (버튼, 필터 등) --}}
        @isset($actions)
            <div class="flex items-center gap-3">
                {{ $actions }}
            </div>
        @endisset
    </div>

    {{-- Additional Content Slot --}}
    @isset($content)
        <div class="mt-4">
            {{ $content }}
        </div>
    @endisset
</div>
