/**
 * Quick Add TaskList
 * Handles inline task list creation from the sidebar quick add input
 */

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Get TaskList icon SVG (Font Awesome list icon)
 */
function getTaskListIconSvg() {
    return `
        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="currentColor">
            <path d="M104 112C90.7 112 80 122.7 80 136L80 184C80 197.3 90.7 208 104 208L152 208C165.3 208 176 197.3 176 184L176 136C176 122.7 165.3 112 152 112L104 112zM256 128C238.3 128 224 142.3 224 160C224 177.7 238.3 192 256 192L544 192C561.7 192 576 177.7 576 160C576 142.3 561.7 128 544 128L256 128zM256 288C238.3 288 224 302.3 224 320C224 337.7 238.3 352 256 352L544 352C561.7 352 576 337.7 576 320C576 302.3 561.7 288 544 288L256 288zM256 448C238.3 448 224 462.3 224 480C224 497.7 238.3 512 256 512L544 512C561.7 512 576 497.7 576 480C576 462.3 561.7 448 544 448L256 448zM80 296L80 344C80 357.3 90.7 368 104 368L152 368C165.3 368 176 357.3 176 344L176 296C176 282.7 165.3 272 152 272L104 272C90.7 272 80 282.7 80 296zM104 432C90.7 432 80 442.7 80 456L80 504C80 517.3 90.7 528 104 528L152 528C165.3 528 176 517.3 176 504L176 456C176 442.7 165.3 432 152 432L104 432z"/>
        </svg>
    `;
}

/**
 * Create task list via AJAX
 */
async function quickAddTaskList(name) {
    try {
        const response = await axios.post('/task-lists', {
            name: name
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.data.success) {
            return response.data.taskList;
        }

        return null;
    } catch (error) {
        console.error('Failed to create task list:', error);

        // Show error message
        if (error.response?.data?.message) {
            alert('목록 생성 실패: ' + error.response.data.message);
        } else {
            alert('목록 생성 중 오류가 발생했습니다.');
        }

        return null;
    }
}

/**
 * Add task list to the sidebar DOM
 */
function addTaskListToSidebar(taskList) {
    // Find the ungrouped task list container (미분류 목록 영역)
    let taskListContainer = document.getElementById('ungrouped-tasklist-items-container');

    // Fallback: 구버전 호환성을 위해 기존 컨테이너도 확인
    if (!taskListContainer) {
        taskListContainer = document.getElementById('tasklist-items-container');
    }

    if (!taskListContainer) {
        console.error('TaskList container not found');
        // 페이지 새로고침으로 대체
        window.location.reload();
        return;
    }

    // Create task list item HTML
    const taskListItemHtml = `
        <a
            href="${window.location.origin}/task-lists/${taskList.id}"
            class="flex items-center justify-between rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100"
            data-tasklist-id="${taskList.id}"
        >
            <div class="flex items-center gap-3">
                ${getTaskListIconSvg()}
                <span class="text-sm font-medium">${escapeHtml(taskList.name)}</span>
            </div>
            <span class="text-xs text-gray-500">0</span>
        </a>
    `;

    // Insert at the top of the list
    taskListContainer.insertAdjacentHTML('afterbegin', taskListItemHtml);
}

/**
 * Initialize quick add task list functionality
 */
function initQuickAddTaskList() {
    const form = document.getElementById('quick-add-tasklist-form');
    const input = document.getElementById('quick-add-tasklist-input');

    if (!form || !input) return;

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const name = input.value.trim();

        // Silent ignore if empty
        if (!name) {
            return;
        }

        // Disable input during submission
        input.disabled = true;

        // Create task list
        const taskList = await quickAddTaskList(name);

        // Re-enable input
        input.disabled = false;

        if (taskList) {
            // Add to sidebar
            addTaskListToSidebar(taskList);

            // Clear input
            input.value = '';

            // Keep focus for continuous input
            // input.blur(); // Removed to maintain focus
        }
    });
}

/**
 * Initialize when DOM is ready
 */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQuickAddTaskList);
} else {
    initQuickAddTaskList();
}
