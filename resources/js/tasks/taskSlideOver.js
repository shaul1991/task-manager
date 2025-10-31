/**
 * Task Slide-over Modal
 * Handles task detail view with auto-save functionality
 * Supports responsive layout: Desktop (split) / Mobile (overlay)
 */

// Current task ID
let currentTaskId = null;

// Auto-save debounce timeout
let saveTimeout = null;

// Save state
let isSaving = false;

// Main content click handler reference
let mainContentClickHandler = null;

/**
 * Debounce function
 */
function debounce(func, delay) {
    return function (...args) {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Check if viewport is mobile (< 768px)
 */
function isMobile() {
    return window.innerWidth < 768; // md breakpoint
}

/**
 * Open desktop right panel (split layout)
 */
function openDesktopPanel() {
    const rightPanel = document.getElementById('right-panel');
    const mainContent = document.getElementById('main-content');

    if (!rightPanel || !mainContent) return;

    // Show and animate panel
    rightPanel.classList.remove('md:w-0');
    rightPanel.classList.add('md:w-[30vw]');

    // Adjust Quick Add Task bar width
    const quickAddBar = document.getElementById('quick-add-task-bar');
    if (quickAddBar) {
        quickAddBar.style.right = '30vw';
    }

    // Add click listener to main content (with delay to prevent immediate close)
    setTimeout(() => {
        mainContentClickHandler = function (e) {
            // Only close if clicking directly on main content or its children
            // Don't close if clicking on task items (to allow opening different tasks)
            const clickedElement = e.target;
            const isTaskItem = clickedElement.closest('[data-task-id]');

            if (!isTaskItem) {
                closeDesktopPanel();
            }
        };

        mainContent.addEventListener('click', mainContentClickHandler, {capture: true});
    }, 100);
}

/**
 * Close desktop right panel
 */
function closeDesktopPanel() {
    const rightPanel = document.getElementById('right-panel');
    const mainContent = document.getElementById('main-content');

    if (!rightPanel || !mainContent) return;

    // Animate panel out
    rightPanel.classList.remove('md:w-[30vw]');
    rightPanel.classList.add('md:w-0');

    // Restore Quick Add Task bar width
    const quickAddBar = document.getElementById('quick-add-task-bar');
    if (quickAddBar) {
        quickAddBar.style.right = '0';
    }

    // Remove click listener from main content
    if (mainContentClickHandler) {
        mainContent.removeEventListener('click', mainContentClickHandler, {capture: true});
        mainContentClickHandler = null;
    }

    // Clear current task
    currentTaskId = null;
}

/**
 * Format date to Korean format
 */
function formatDate(dateString) {
    if (!dateString) return '';

    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${year}년 ${month}월 ${day}일 ${hours}:${minutes}`;
}

// Global reference to current saving toast
let currentSavingToast = null;

/**
 * Show save status using toast notifications
 */
function showSaveStatus(status) {
    if (status === 'saving') {
        isSaving = true;
        // Show saving toast (no auto-dismiss)
        if (window.toast) {
            currentSavingToast = window.toast.saving('저장 중...');
        }
    } else if (status === 'saved') {
        isSaving = false;
        // Hide saving toast if exists
        if (currentSavingToast && window.toast) {
            window.toast.hide(currentSavingToast);
            currentSavingToast = null;
        }
        // Show success toast
        if (window.toast) {
            window.toast.success('저장되었습니다', 2000);
        }
    } else if (status === 'error') {
        isSaving = false;
        // Hide saving toast if exists
        if (currentSavingToast && window.toast) {
            window.toast.hide(currentSavingToast);
            currentSavingToast = null;
        }
        // Show error toast
        if (window.toast) {
            window.toast.error('저장에 실패했습니다', 3000);
        }
    }
}

/**
 * Get the active container (desktop or mobile)
 */
function getActiveContainer() {
    if (isMobile()) {
        return document.querySelector('#right-panel-mobile');
    } else {
        return document.querySelector('#right-panel-content');
    }
}

/**
 * Load task data
 */
async function loadTaskData(taskId) {
    const container = getActiveContainer();
    if (!container) return;

    const taskLoading = container.querySelector('#task-loading');
    const taskContent = container.querySelector('#task-content');

    if (!taskLoading || !taskContent) return;

    // Show loading state
    taskLoading.classList.remove('hidden');
    taskContent.classList.add('hidden');

    try {
        const response = await axios.get(`/tasks/${taskId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (response.data.success) {
            const task = response.data.data;

            // Populate form fields
            container.querySelector('#task-title').value = task.title || '';
            container.querySelector('#task-description').value = task.description || '';
            container.querySelector('#task-completed').checked = task.completed;

            // Update timestamps
            container.querySelector('#task-created-at').textContent = formatDate(task.created_at);
            container.querySelector('#task-updated-at').textContent = formatDate(task.updated_at);

            // Show/hide completed datetime
            const completedAtWrapper = container.querySelector('#task-completed-at-wrapper');
            if (task.completed_datetime) {
                container.querySelector('#task-completed-at').textContent = formatDate(task.completed_datetime);
                completedAtWrapper.classList.remove('hidden');
            } else {
                completedAtWrapper.classList.add('hidden');
            }

            // Hide loading, show content
            taskLoading.classList.add('hidden');
            taskContent.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Failed to load task:', error);
        alert('할 일을 불러오는 중 오류가 발생했습니다.');

        // Close based on mode
        if (isMobile()) {
            window.slideOver.close('right-panel-mobile');
        } else {
            closeDesktopPanel();
        }
    }
}

/**
 * Save task data
 */
async function saveTaskData(taskId, data) {
    if (isSaving) return;

    showSaveStatus('saving');

    try {
        const response = await axios.patch(`/tasks/${taskId}`, data, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.data.success) {
            showSaveStatus('saved');

            // Update timestamps in the active container
            const task = response.data.data;
            const container = getActiveContainer();
            if (container) {
                const updatedAtElement = container.querySelector('#task-updated-at');
                if (updatedAtElement) {
                    updatedAtElement.textContent = formatDate(task.updated_at);
                }

                // Update completed datetime if changed
                const completedAtWrapper = container.querySelector('#task-completed-at-wrapper');
                const completedAtElement = container.querySelector('#task-completed-at');
                if (completedAtWrapper && completedAtElement) {
                    if (task.completed_datetime) {
                        completedAtElement.textContent = formatDate(task.completed_datetime);
                        completedAtWrapper.classList.remove('hidden');
                    } else {
                        completedAtWrapper.classList.add('hidden');
                    }
                }
            }

            // Update the task in the list
            updateTaskInList(task);
        }
    } catch (error) {
        console.error('Failed to save task:', error);
        showSaveStatus('error');
    }
}

/**
 * Update task in the list view
 */
function updateTaskInList(task) {
    const taskItem = document.querySelector(`div[data-task-id="${task.id}"]`);
    if (!taskItem) return;

    // Update checkbox
    const checkbox = taskItem.querySelector('input[type="checkbox"]');
    if (checkbox) {
        checkbox.checked = task.completed;
    }

    // Update title (and completed styling)
    const titleElement = taskItem.querySelector('.task-title');
    if (titleElement) {
        titleElement.textContent = task.title;

        // Update completed styling
        if (task.completed) {
            titleElement.classList.add('text-gray-400', 'line-through');
            titleElement.classList.remove('text-gray-900');
        } else {
            titleElement.classList.remove('text-gray-400', 'line-through');
            titleElement.classList.add('text-gray-900');
        }
    }

    // Update description
    const descriptionElement = taskItem.querySelector('.task-description');

    if (task.description) {
        // Description이 있으면 요소 생성 또는 업데이트
        if (descriptionElement) {
            descriptionElement.textContent = task.description;
            descriptionElement.classList.remove('text-gray-400', 'italic');
            descriptionElement.classList.add('text-gray-600');
        } else {
            // 요소가 없으면 새로 생성
            const titleElement = taskItem.querySelector('.task-title');
            if (titleElement && titleElement.parentElement) {
                const newDesc = document.createElement('p');
                newDesc.className = 'task-description mt-1 text-sm text-gray-600';
                newDesc.textContent = task.description;
                titleElement.parentElement.appendChild(newDesc);
            }
        }
    } else {
        // Description이 없으면 요소 제거
        if (descriptionElement) {
            descriptionElement.remove();
        }
    }

    // Update updated_at
    const updatedAtElement = taskItem.querySelector('.task-updated-at');
    if (updatedAtElement) {
        updatedAtElement.textContent = '수정: ' + formatDate(task.updated_at);
    }
}

/**
 * Delete task (BFF 패턴: DELETE 후 GET 2회)
 */
async function deleteTask(taskId) {
    // Confirm modal 사용
    const confirmed = await window.confirmModal.show({
        title: '할 일 삭제',
        message: '정말로 이 할 일을 삭제하시겠습니까?',
        confirmText: '삭제',
        cancelText: '취소',
        type: 'danger'
    });

    if (!confirmed) {
        return;
    }

    // 1. 삭제 전에 필요한 정보 수집
    const taskItem = document.querySelector(`div[data-task-id="${taskId}"]`);
    const checkbox = taskItem?.querySelector('input[type="checkbox"]');
    const wasCompleted = checkbox?.checked || false;

    // 2. 현재 페이지의 task_list_id 가져오기
    const quickAddForm = document.getElementById('quick-add-task-form');
    const taskListId = quickAddForm?.dataset.taskListId;

    try {
        // 3. Task 삭제 실행
        const response = await axios.delete(`/tasks/${taskId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (response.data.success) {
            // 4. Panel 닫기
            if (isMobile()) {
                window.slideOver.close('right-panel-mobile');
            } else {
                closeDesktopPanel();
            }

            // 5. Task를 DOM에서 제거
            if (taskItem) {
                taskItem.remove();
            }

            // 6. BFF 패턴: TaskList 최신 데이터 조회 및 업데이트
            if (taskListId) {
                await updateTaskListData(taskListId);
            }

            // 7. Main-header의 총 갯수 업데이트 (미완료 task였을 경우만)
            if (!wasCompleted) {
                updateMainHeaderCount(-1);
            }

            // 성공 메시지
            if (window.toast) {
                window.toast.success('할 일이 삭제되었습니다.', 2000);
            }

            // 할 일이 없으면 페이지 새로고침
            const remainingTasks = document.querySelectorAll('div[data-task-id]');
            if (remainingTasks.length === 0) {
                window.location.reload();
            }
        }
    } catch (error) {
        console.error('Failed to delete task:', error);
        if (window.toast) {
            window.toast.error('할 일 삭제 중 오류가 발생했습니다.', 3000);
        }
    }
}

/**
 * BFF 패턴: TaskList 최신 데이터 조회 및 사이드바 업데이트
 *
 * GET /task-lists/{id} API를 호출하여 최신 incomplete_task_count를 가져옵니다.
 * TaskGroup에 속한 경우, 추가로 TaskGroup 데이터도 조회합니다.
 */
async function updateTaskListData(taskListId) {
    try {
        // GET 요청 1: TaskList 조회
        const response = await axios.get(`/task-lists/${taskListId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (response.data.success) {
            const taskListData = response.data.data;

            // 사이드바의 TaskList 갯수 업데이트
            updateTaskListCountInSidebar(
                taskListData.id,
                taskListData.incomplete_task_count
            );

            // TaskGroup에 속한 경우, GET 요청 2: TaskGroup 조회
            if (taskListData.task_group_id) {
                updateTaskGroupData(taskListData.task_group_id);
            }
        }
    } catch (error) {
        console.error('Failed to update TaskList data:', error);
    }
}

/**
 * BFF 패턴: TaskGroup 최신 데이터 조회 및 사이드바 업데이트
 *
 * GET /task-groups/{id} API를 호출하여 최신 incomplete_task_count를 가져옵니다.
 * 비동기로 실행되며 결과를 기다리지 않습니다.
 */
async function updateTaskGroupData(taskGroupId) {
    try {
        // GET 요청 2: TaskGroup 조회
        const response = await axios.get(`/task-groups/${taskGroupId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (response.data.success) {
            const taskGroupData = response.data.data;

            // 사이드바의 TaskGroup 갯수 업데이트
            updateTaskGroupCountInSidebar(
                taskGroupData.id,
                taskGroupData.incomplete_task_count
            );
        }
    } catch (error) {
        console.error('Failed to update TaskGroup data:', error);
    }
}

/**
 * 사이드바의 TaskList 갯수 업데이트
 */
function updateTaskListCountInSidebar(taskListId, incompleteCount) {
    const sidebarItem = document.querySelector(`[data-tasklist-id="${taskListId}"] .text-xs`);
    if (sidebarItem) {
        sidebarItem.textContent = incompleteCount;
    }
}

/**
 * 사이드바의 TaskGroup 갯수 업데이트
 */
function updateTaskGroupCountInSidebar(taskGroupId, incompleteCount) {
    const groupHeader = document.querySelector(`[data-task-group-id="${taskGroupId}"] .task-group-header .text-xs`);
    if (groupHeader) {
        groupHeader.textContent = incompleteCount;
    }
}

/**
 * Main-header의 총 할 일 갯수 업데이트
 */
function updateMainHeaderCount(delta) {
    const subtitles = document.querySelectorAll('.text-gray-600');

    for (const subtitle of subtitles) {
        const match = subtitle.textContent.match(/총 (\d+)개의 할 일/);
        if (match) {
            const currentCount = parseInt(match[1]);
            const newCount = Math.max(0, currentCount + delta);
            subtitle.textContent = `총 ${newCount}개의 할 일`;
            break;
        }
    }
}

/**
 * Initialize event listeners for a specific container
 */
function initContainerEventListeners(container) {
    if (!container) return;

    // Title input - auto-save with debounce
    const titleInput = container.querySelector('#task-title');
    if (titleInput && !titleInput.dataset.initialized) {
        // Input event with debounce (typing auto-save)
        titleInput.addEventListener('input', debounce(function () {
            if (!currentTaskId) return;

            const title = this.value.trim();
            const activeContainer = getActiveContainer();
            if (!activeContainer) return;

            // Validation
            const errorElement = activeContainer.querySelector('#task-title-error');
            if (!title) {
                if (errorElement) {
                    errorElement.textContent = '제목을 입력해주세요.';
                    errorElement.classList.remove('hidden');
                }
                return;
            }

            if (title.length > 255) {
                if (errorElement) {
                    errorElement.textContent = '제목은 255자를 초과할 수 없습니다.';
                    errorElement.classList.remove('hidden');
                }
                return;
            }

            if (errorElement) {
                errorElement.classList.add('hidden');
            }

            // Save
            saveTaskData(currentTaskId, {title});
        }, 500));

        // Change event - immediate save when value changed
        titleInput.addEventListener('change', function () {
            // Cancel pending debounced save
            clearTimeout(saveTimeout);

            if (!currentTaskId) return;

            const title = this.value.trim();
            const activeContainer = getActiveContainer();
            if (!activeContainer) return;

            // Validation
            const errorElement = activeContainer.querySelector('#task-title-error');
            if (!title) {
                if (errorElement) {
                    errorElement.textContent = '제목을 입력해주세요.';
                    errorElement.classList.remove('hidden');
                }
                return;
            }

            if (title.length > 255) {
                if (errorElement) {
                    errorElement.textContent = '제목은 255자를 초과할 수 없습니다.';
                    errorElement.classList.remove('hidden');
                }
                return;
            }

            if (errorElement) {
                errorElement.classList.add('hidden');
            }

            // Save immediately
            saveTaskData(currentTaskId, {title});
        });

        titleInput.dataset.initialized = 'true';
    }

    // Description textarea - auto-save with debounce
    const descriptionTextarea = container.querySelector('#task-description');
    if (descriptionTextarea && !descriptionTextarea.dataset.initialized) {
        // Input event with debounce (typing auto-save)
        descriptionTextarea.addEventListener('input', debounce(function () {
            if (!currentTaskId) return;

            const description = this.value.trim();
            saveTaskData(currentTaskId, {description: description || null});
        }, 500));

        // Change event - immediate save when value changed
        descriptionTextarea.addEventListener('change', function () {
            // Cancel pending debounced save
            clearTimeout(saveTimeout);

            if (!currentTaskId) return;

            const description = this.value.trim();
            saveTaskData(currentTaskId, {description: description || null});
        });

        descriptionTextarea.dataset.initialized = 'true';
    }

    // Completed checkbox - immediate save
    const completedCheckbox = container.querySelector('#task-completed');
    if (completedCheckbox && !completedCheckbox.dataset.initialized) {
        completedCheckbox.addEventListener('change', function () {
            if (!currentTaskId) return;

            const completed = this.checked;
            saveTaskData(currentTaskId, {completed});
        });
        completedCheckbox.dataset.initialized = 'true';
    }

    // Delete button
    const deleteButton = container.querySelector('#delete-task-btn');
    if (deleteButton && !deleteButton.dataset.initialized) {
        deleteButton.addEventListener('click', function () {
            if (currentTaskId) {
                deleteTask(currentTaskId);
            }
        });
        deleteButton.dataset.initialized = 'true';
    }
}

/**
 * Initialize slide-over functionality
 */
function initTaskSlideOver() {
    // Initialize desktop panel event listeners
    const desktopContainer = document.querySelector('#right-panel-content');
    if (desktopContainer) {
        initContainerEventListeners(desktopContainer);

        // Desktop panel close button (data-close-sidebar attribute)
        const desktopCloseBtn = desktopContainer.querySelector('[data-close-sidebar="right-panel"]');
        if (desktopCloseBtn) {
            desktopCloseBtn.addEventListener('click', closeDesktopPanel);
        }
    }

    // Initialize mobile modal event listeners
    const mobileContainer = document.querySelector('#right-panel-mobile');
    if (mobileContainer) {
        initContainerEventListeners(mobileContainer);
    }
}

/**
 * Open task detail (responsive: desktop panel or mobile modal)
 */
window.openTaskDetail = function (taskId) {
    currentTaskId = taskId;

    // Determine mode and open accordingly
    if (isMobile()) {
        // Mobile: Open full overlay modal
        window.slideOver.open('right-panel-mobile');
    } else {
        // Desktop: Open right panel
        openDesktopPanel();
    }

    // Load task data (will use the active container)
    loadTaskData(taskId);
};

/**
 * Initialize when DOM is ready
 */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTaskSlideOver);
} else {
    initTaskSlideOver();
}
