/**
 * Sidebar Delete Menu
 * TaskGroup 및 TaskList 삭제 드롭다운 메뉴 및 모달 관리
 */

// 전역 상태
let currentEntity = {
    type: null,  // 'tasklist' or 'taskgroup'
    id: null,
    name: null
};

/**
 * 드롭다운 메뉴 초기화
 */
function initDropdownMenu() {
    const dropdownMenu = document.getElementById('dropdown-menu');
    if (!dropdownMenu) return;

    // 더보기 버튼 클릭 이벤트 (Event Delegation)
    document.addEventListener('click', function(e) {
        const moreButton = e.target.closest('.more-button');

        if (moreButton) {
            e.preventDefault();
            e.stopPropagation();

            // 현재 엔티티 정보 저장
            currentEntity = {
                type: moreButton.dataset.entityType,  // 'tasklist' or 'taskgroup'
                id: parseInt(moreButton.dataset.entityId),
                name: moreButton.dataset.entityName
            };

            // 드롭다운 위치 계산 및 표시
            showDropdown(moreButton, dropdownMenu);
        }
    });

    // 드롭다운 "삭제" 버튼 클릭
    const deleteButton = dropdownMenu.querySelector('[data-action="delete"]');
    if (deleteButton) {
        deleteButton.addEventListener('click', function(e) {
            e.preventDefault();
            hideDropdown();
            showDeleteModal();
        });
    }

    // 외부 클릭 시 드롭다운 닫기
    document.addEventListener('click', function(e) {
        if (!dropdownMenu.contains(e.target) && !e.target.closest('.more-button')) {
            hideDropdown();
        }
    });

    // ESC 키로 드롭다운 닫기
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && dropdownMenu.classList.contains('show')) {
            hideDropdown();
        }
    });
}

/**
 * 드롭다운 표시
 */
function showDropdown(triggerButton, dropdownMenu) {
    const rect = triggerButton.getBoundingClientRect();
    const dropdownHeight = 50; // 예상 드롭다운 높이
    const dropdownWidth = 160;

    // 위치 계산 (버튼 아래, 오른쪽 정렬)
    let top = rect.bottom + 4;
    let left = rect.right - dropdownWidth;

    // 화면 밖으로 나가는지 확인
    if (top + dropdownHeight > window.innerHeight) {
        top = rect.top - dropdownHeight - 4; // 버튼 위로 표시
    }

    if (left < 0) {
        left = rect.left; // 왼쪽 정렬로 변경
    }

    dropdownMenu.style.top = `${top}px`;
    dropdownMenu.style.left = `${left}px`;
    dropdownMenu.classList.add('show');
}

/**
 * 드롭다운 숨기기
 */
function hideDropdown() {
    const dropdownMenu = document.getElementById('dropdown-menu');
    if (dropdownMenu) {
        dropdownMenu.classList.remove('show');
    }
}

/**
 * 모달 초기화 (더 이상 필요 없음 - window.confirmModal 사용)
 */
function initDeleteModal() {
    // confirm-modal.blade.php에서 전역으로 초기화됨
    // 이 함수는 호환성을 위해 유지
}

/**
 * 삭제 모달 표시 (Promise 기반으로 변경)
 */
async function showDeleteModal() {
    if (!currentEntity.type || !currentEntity.id) {
        console.error('Invalid entity for deletion');
        return;
    }

    // 엔티티 타입에 따라 메시지 구성
    let title, message, warningMessage;

    if (currentEntity.type === 'tasklist') {
        title = '목록 삭제';
        message = `${currentEntity.name}을(를) 정말 삭제하시겠습니까?`;
        warningMessage = '\n\n⚠️ 이 목록에 속한 할 일들은 그룹 없음 상태로 이동됩니다.';
    } else if (currentEntity.type === 'taskgroup') {
        title = '그룹 삭제';
        message = `${currentEntity.name}을(를) 정말 삭제하시겠습니까?`;
        warningMessage = '\n\n⚠️ 이 그룹에 속한 모든 목록과 할 일이 함께 삭제됩니다.';
    }

    // window.confirmModal.show() 사용
    const confirmed = await window.confirmModal.show({
        title: title,
        message: message + warningMessage,
        confirmText: '삭제',
        cancelText: '취소',
        type: 'danger'
    });

    if (confirmed) {
        await handleDelete();
    }
}

/**
 * 삭제 처리
 */
async function handleDelete() {
    if (!currentEntity.type || !currentEntity.id) {
        console.error('Invalid entity for deletion');
        return;
    }

    try {
        if (currentEntity.type === 'tasklist') {
            await deleteTaskList(currentEntity.id);
        } else if (currentEntity.type === 'taskgroup') {
            await deleteTaskGroup(currentEntity.id);
        }
    } catch (error) {
        console.error('Delete failed:', error);
        if (window.toast) {
            window.toast.error('삭제 중 오류가 발생했습니다.', 3000);
        }
    }
}

/**
 * TaskList 삭제
 */
async function deleteTaskList(taskListId) {
    const response = await window.axios.delete(`/task-lists/${taskListId}`);

    if (response.data.success) {
        // TaskList 상세 페이지에서 삭제한 경우 메인 페이지로 이동
        // 다른 페이지에서 삭제한 경우 새로고침
        const currentPath = window.location.pathname;
        if (currentPath.includes(`/task-lists/${taskListId}`)) {
            window.location.href = '/';
        } else {
            window.location.reload();
        }
    } else {
        throw new Error(response.data.message || 'Failed to delete TaskList');
    }
}

/**
 * TaskGroup 삭제
 */
async function deleteTaskGroup(taskGroupId) {
    const response = await window.axios.delete(`/task-groups/${taskGroupId}`);

    if (response.data.success) {
        // TaskGroup 상세 페이지에서 삭제한 경우 메인 페이지로 이동
        // 다른 페이지에서 삭제한 경우 새로고침
        const currentPath = window.location.pathname;
        if (currentPath.includes(`/task-groups/${taskGroupId}`)) {
            window.location.href = '/';
        } else {
            window.location.reload();
        }
    } else {
        throw new Error(response.data.message || 'Failed to delete TaskGroup');
    }
}

/**
 * 모든 초기화 실행
 */
export function initDeleteMenu() {
    initDropdownMenu();
    initDeleteModal();
}

/**
 * DOM 로드 시 자동 초기화
 */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDeleteMenu);
} else {
    initDeleteMenu();
}
