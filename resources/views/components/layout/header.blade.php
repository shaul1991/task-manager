<header class="fixed top-0 left-0 right-0 h-16 bg-white shadow-sm border-b border-gray-200 z-50">
    <div class="h-full px-4 flex items-center justify-between">
        <!-- Left Section: Hamburger Button (Mobile) + Logo -->
        <div class="flex items-center gap-4">
            <!-- Hamburger Button (모바일에서만 표시) -->
            <button
                type="button"
                id="mobile-menu-button"
                class="sm:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                aria-label="메뉴 열기"
            >
                <x-icons.menu class="w-6 h-6" />
            </button>

            <!-- Logo / App Name -->
            <a href="{{ route('main') }}" class="flex items-center gap-2">
                <x-icons.database class="w-10 h-10 text-blue-800" />
                <span class="text-xl font-bold text-gray-900 hidden sm:block">Task Manager</span>
            </a>
        </div>

        <!-- Center Section: Search Bar (PC에서만 표시) -->
        <div class="hidden sm:block flex-1 max-w-2xl mx-8">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icons.search class="w-5 h-5 text-gray-400" />
                </div>
                <input
                    type="search"
                    placeholder="할 일 또는 목록 검색..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
            </div>
        </div>

        <!-- Right Section: Quick Actions + User Profile -->
        <div class="flex items-center gap-3">
            <!-- User Profile Dropdown -->
            <div class="relative">
                <button
                    type="button"
                    id="user-menu-button"
                    class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <!-- User Avatar -->
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                        <span>G</span>
                    </div>
                    <!-- User Name (PC에서만 표시) -->
                    <span class="hidden sm:block text-sm font-medium text-gray-700">게스트</span>
                    <!-- Dropdown Icon -->
                    <x-icons.chevron-down class="hidden sm:block w-4 h-4 text-gray-500" />
                </button>

                <!-- Dropdown Menu (초기 hidden, JavaScript로 토글) -->
                <div
                    id="user-menu"
                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                >
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <x-icons.user class="w-4 h-4" />
                            <span>프로필</span>
                        </div>
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <x-icons.settings class="w-4 h-4" />
                            <span>설정</span>
                        </div>
                    </a>
                    <hr class="my-1 border-gray-200">
                    <a href="#" class="block px-4 py-2 text-sm text-blue-600 hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <x-icons.login class="w-4 h-4" />
                            <span>회원가입</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
