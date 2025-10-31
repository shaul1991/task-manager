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
 * Get color for the task list based on ID (consistent with server-side logic)
 */
function getColorForTaskList(taskListId) {
    const colors = [
        'bg-blue-500',
        'bg-green-500',
        'bg-purple-500',
        'bg-yellow-500',
        'bg-red-500',
        'bg-pink-500',
        'bg-indigo-500',
        'bg-teal-500',
    ];
    return colors[taskListId % colors.length];
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
    // Find the task list container
    const taskListContainer = document.getElementById('tasklist-items-container');

    if (!taskListContainer) {
        console.error('TaskList container not found');
        return;
    }

    // Get color for the new task list (consistent with server-side logic)
    const colorClass = getColorForTaskList(taskList.id);

    // Create task list item HTML
    const taskListItemHtml = `
        <a
            href="#"
            class="flex items-center justify-between rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100"
            data-tasklist-id="${taskList.id}"
        >
            <div class="flex items-center gap-3">
                <div class="h-3 w-3 rounded-full ${colorClass}"></div>
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
