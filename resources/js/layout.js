/**
 * Layout JavaScript - 모바일 사이드바 및 사용자 메뉴 인터랙션
 */

document.addEventListener('DOMContentLoaded', () => {
    // ========================================
    // Mobile Sidebar Toggle
    // ========================================
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const mobileOverlay = document.getElementById('mobile-overlay');

    /**
     * 모바일 사이드바 열기
     */
    function openMobileSidebar() {
        if (mobileSidebar && mobileOverlay) {
            mobileSidebar.classList.remove('-translate-x-full');
            mobileOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // 배경 스크롤 방지
        }
    }

    /**
     * 모바일 사이드바 닫기
     */
    function closeMobileSidebar() {
        if (mobileSidebar && mobileOverlay) {
            mobileSidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
            document.body.style.overflow = ''; // 배경 스크롤 복원
        }
    }

    /**
     * 모바일 사이드바 토글
     */
    function toggleMobileSidebar() {
        if (mobileSidebar && mobileSidebar.classList.contains('-translate-x-full')) {
            openMobileSidebar();
        } else {
            closeMobileSidebar();
        }
    }

    // 햄버거 버튼 클릭 이벤트
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', toggleMobileSidebar);
    }

    // 오버레이 클릭 시 사이드바 닫기
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', closeMobileSidebar);
    }

    // ESC 키로 사이드바 닫기
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileSidebar && !mobileSidebar.classList.contains('-translate-x-full')) {
            closeMobileSidebar();
        }
    });

    // ========================================
    // User Menu Dropdown Toggle
    // ========================================
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');

    /**
     * 사용자 메뉴 토글
     */
    function toggleUserMenu() {
        if (userMenu) {
            const isHidden = userMenu.classList.contains('hidden');

            if (isHidden) {
                userMenu.classList.remove('hidden');
                userMenuButton?.setAttribute('aria-expanded', 'true');
            } else {
                userMenu.classList.add('hidden');
                userMenuButton?.setAttribute('aria-expanded', 'false');
            }
        }
    }

    /**
     * 사용자 메뉴 닫기
     */
    function closeUserMenu() {
        if (userMenu) {
            userMenu.classList.add('hidden');
            userMenuButton?.setAttribute('aria-expanded', 'false');
        }
    }

    // 사용자 메뉴 버튼 클릭 이벤트
    if (userMenuButton) {
        userMenuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleUserMenu();
        });
    }

    // 문서 클릭 시 사용자 메뉴 닫기 (외부 클릭 감지)
    document.addEventListener('click', (e) => {
        if (userMenu && userMenuButton) {
            const isClickInsideMenu = userMenu.contains(e.target);
            const isClickOnButton = userMenuButton.contains(e.target);

            if (!isClickInsideMenu && !isClickOnButton) {
                closeUserMenu();
            }
        }
    });

    // ========================================
    // Responsive Breakpoint Detection
    // ========================================
    const MOBILE_BREAKPOINT = 640; // Tailwind 'sm' breakpoint

    /**
     * 화면 크기 변경 시 모바일 사이드바 자동 닫기
     */
    window.addEventListener('resize', () => {
        if (window.innerWidth >= MOBILE_BREAKPOINT) {
            closeMobileSidebar();
        }
    });
});
