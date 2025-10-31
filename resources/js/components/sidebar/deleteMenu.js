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
 * 모달 초기화
 */
function initDeleteModal() {
    const modal = document.getElementById('delete-confirmation-modal');
    if (!modal) return;

    // 모달 닫기 버튼들
    const closeButtons = modal.querySelectorAll('.modal-close, .modal-cancel');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            hideDeleteModal();
        });
    });

    // 모달 배경 클릭 시 닫기
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideDeleteModal();
        }
    });

    // ESC 키로 모달 닫기
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            hideDeleteModal();
        }
    });

    // 삭제 확인 버튼
    const confirmButton = modal.querySelector('#modal-confirm-delete');
    if (confirmButton) {
        confirmButton.addEventListener('click', function() {
            handleDelete();
        });
    }
}

/**
 * 삭제 모달 표시
 */
function showDeleteModal() {
    const modal = document.getElementById('delete-confirmation-modal');
    if (!modal) return;

    // 엔티티 타입에 따라 텍스트 변경
    const entityTypeText = modal.querySelector('#modal-entity-type-text');
    const entityNameText = modal.querySelector('#modal-entity-name');
    const entityTypeKo = modal.querySelector('#modal-entity-type-ko');
    const tasklistWarning = modal.querySelector('#modal-tasklist-warning');
    const taskgroupWarning = modal.querySelector('#modal-taskgroup-warning');

    if (currentEntity.type === 'tasklist') {
        entityTypeText.textContent = '목록';
        entityTypeKo.textContent = '을(를)';
        tasklistWarning.classList.remove('hidden');
        taskgroupWarning.classList.add('hidden');
    } else if (currentEntity.type === 'taskgroup') {
        entityTypeText.textContent = '그룹';
        entityTypeKo.textContent = '을(를)';
        tasklistWarning.classList.add('hidden');
        taskgroupWarning.classList.remove('hidden');
    }

    entityNameText.textContent = currentEntity.name;

    modal.classList.add('show');
}

/**
 * 삭제 모달 숨기기
 */
function hideDeleteModal() {
    const modal = document.getElementById('delete-confirmation-modal');
    if (!modal) return;

    modal.classList.add('closing');

    setTimeout(() => {
        modal.classList.remove('show', 'closing');
    }, 150); // closing 애니메이션 시간과 일치
}

/**
 * 삭제 처리
 */
async function handleDelete() {
    if (!currentEntity.type || !currentEntity.id) {
        console.error('Invalid entity for deletion');
        return;
    }

    hideDeleteModal();

    try {
        if (currentEntity.type === 'tasklist') {
            await deleteTaskList(currentEntity.id);
        } else if (currentEntity.type === 'taskgroup') {
            await deleteTaskGroup(currentEntity.id);
        }
    } catch (error) {
        console.error('Delete failed:', error);
        alert('삭제 중 오류가 발생했습니다.');
    }
}

/**
 * TaskList 삭제
 */
async function deleteTaskList(taskListId) {
    const response = await window.axios.delete(`/task-lists/${taskListId}`);

    if (response.data.success) {
        // 성공 시 페이지 새로고침
        window.location.reload();
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
        // 성공 시 페이지 새로고침
        window.location.reload();
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
