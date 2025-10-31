{{--
    Main Header Component
    페이지 상단의 고정 영역 (Title, Breadcrumb, Actions 등)
--}}

@props([
    'title' => null,
    'subtitle' => null,
    'description' => null,
    'breadcrumbs' => [],
    'editable' => false,
    'taskListId' => null,
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
        <div class="flex-1">
            @if($title)
                @if($editable && $taskListId)
                    {{-- 편집 가능한 제목 --}}
                    <div id="editable-title-container" data-task-list-id="{{ $taskListId }}">
                        <h1
                            id="editable-title-display"
                            class="text-3xl font-bold text-gray-900 cursor-pointer hover:text-gray-700 transition-colors"
                            title="클릭하여 편집"
                        >{{ $title }}</h1>
                        <input
                            type="text"
                            id="editable-title-input"
                            class="hidden text-3xl font-bold text-gray-900 border-b-2 border-blue-500 bg-transparent focus:outline-none w-full"
                            value="{{ $title }}"
                        />
                    </div>
                @else
                    <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
                @endif
            @endif

            @if($editable && $taskListId)
                {{-- 편집 가능한 설명 --}}
                <div id="editable-description-container" class="mt-2" data-task-list-id="{{ $taskListId }}">
                    @if($description)
                        <p
                            id="editable-description-display"
                            class="text-sm text-gray-600 cursor-pointer hover:text-gray-800 transition-colors"
                            title="클릭하여 편집"
                        >{{ $description }}</p>
                    @else
                        <p
                            id="editable-description-display"
                            class="text-sm text-gray-400 cursor-pointer hover:text-gray-600 transition-colors italic"
                            title="클릭하여 설명 추가"
                        >설명을 추가하려면 클릭하세요</p>
                    @endif
                    <input
                        type="text"
                        id="editable-description-input"
                        class="hidden text-sm text-gray-900 border-b-2 border-blue-500 bg-transparent focus:outline-none w-full"
                        placeholder="설명을 입력하세요"
                        value="{{ $description }}"
                    />
                </div>
            @elseif($description)
                <p class="mt-2 text-sm text-gray-600">{{ $description }}</p>
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
