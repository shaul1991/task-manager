<x-layouts.app>
    <x-slot:title>대시보드 - Task Manager</x-slot:title>

    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">대시보드</h1>
        <p class="mt-2 text-gray-600">오늘의 할 일을 확인하고 관리하세요</p>
    </div>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Tasks Card -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">전체 할 일</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">24</p>
                </div>
                <div class="rounded-full bg-blue-100 p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Tasks Card -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">완료된 할 일</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">12</p>
                </div>
                <div class="rounded-full bg-green-100 p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Tasks Card -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">진행 중</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">12</p>
                </div>
                <div class="rounded-full bg-yellow-100 p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- TaskLists Card -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">전체 목록</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">4</p>
                </div>
                <div class="rounded-full bg-purple-100 p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tasks Section -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-xl font-bold text-gray-900">최근 할 일</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <!-- Task Item 1 -->
                <div class="flex items-center gap-4 rounded-lg border border-gray-200 p-4 hover:bg-gray-50">
                    <input
                        type="checkbox"
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900">프로젝트 문서 작성</h3>
                        <p class="text-sm text-gray-600">업무</p>
                    </div>
                    <span class="text-sm text-gray-500">오늘</span>
                </div>

                <!-- Task Item 2 -->
                <div class="flex items-center gap-4 rounded-lg border border-gray-200 p-4 hover:bg-gray-50">
                    <input
                        type="checkbox"
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900">우유 사기</h3>
                        <p class="text-sm text-gray-600">쇼핑</p>
                    </div>
                    <span class="text-sm text-gray-500">내일</span>
                </div>

                <!-- Task Item 3 -->
                <div class="flex items-center gap-4 rounded-lg border border-gray-200 p-4 hover:bg-gray-50">
                    <input
                        type="checkbox"
                        checked
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-400 line-through">헬스장 가기</h3>
                        <p class="text-sm text-gray-500">운동 루틴</p>
                    </div>
                    <span class="text-sm text-gray-500">완료</span>
                </div>

                <!-- Task Item 4 -->
                <div class="flex items-center gap-4 rounded-lg border border-gray-200 p-4 hover:bg-gray-50">
                    <input
                        type="checkbox"
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900">클린 코드 읽기</h3>
                        <p class="text-sm text-gray-600">독서 목록</p>
                    </div>
                    <span class="text-sm text-gray-500">이번 주</span>
                </div>
            </div>

            <!-- View All Button -->
            <div class="mt-6 text-center">
                <a href="#" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700">
                    <span class="font-medium">모든 할 일 보기</span>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
