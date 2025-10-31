{{--
    Dropdown Menu Component

    Usage:
    <x-ui.dropdown-menu />

    JavaScript will control visibility and position dynamically
--}}

<div id="dropdown-menu"
     class="dropdown-menu fixed z-50 hidden min-w-[160px] rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
     style="top: 0; left: 0;"
     role="menu"
     aria-labelledby="dropdown-trigger"
>
    {{-- Delete Option --}}
    <button
        type="button"
        class="dropdown-item flex w-full items-center gap-3 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50"
        data-action="delete"
        role="menuitem"
    >
        {{-- Trash Icon --}}
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
        </svg>
        <span class="font-medium">삭제</span>
    </button>
</div>
