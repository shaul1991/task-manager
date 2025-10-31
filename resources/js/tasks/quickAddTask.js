/**
 * Quick Add Task
 * Handles inline task creation from the quick add input
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

/**
 * Create task via AJAX
 */
async function quickAddTask(title) {
    try {
        const response = await axios.post('/tasks', {
            title: title
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.data.success) {
            return response.data.task;
        }

        return null;
    } catch (error) {
        console.error('Failed to create task:', error);

        // Show error message
        if (error.response?.data?.message) {
            alert('할 일 생성 실패: ' + error.response.data.message);
        } else {
            alert('할 일 생성 중 오류가 발생했습니다.');
        }

        return null;
    }
}

/**
 * Add task to the list DOM
 */
function addTaskToList(task) {
    // Find the task list container
    const taskListContainer = document.querySelector('.divide-y.divide-gray-200');

    if (!taskListContainer) {
        // If no tasks exist, reload the page to show the new task
        window.location.reload();
        return;
    }

    // Create task item HTML
    const taskItemHtml = `
        <div class="p-4 hover:bg-gray-50 transition-colors" data-task-id="${task.id}">
            <div class="flex items-start gap-4">
                <!-- Checkbox -->
                <input
                    type="checkbox"
                    ${task.completed ? 'checked' : ''}
                    class="mt-1 h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    disabled
                />

                <!-- Task Content (Clickable to open slide-over) -->
                <div
                    class="flex-1 min-w-0 cursor-pointer"
                    onclick="window.openTaskDetail(${task.id})"
                >
                    <h3 class="task-title font-medium ${task.completed ? 'text-gray-400 line-through' : 'text-gray-900'}">
                        ${escapeHtml(task.title)}
                    </h3>

                    ${task.description ? `
                        <p class="task-description mt-1 text-sm text-gray-600">
                            ${escapeHtml(task.description)}
                        </p>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    // Insert at the top of the list
    taskListContainer.insertAdjacentHTML('afterbegin', taskItemHtml);

    // Update total count
    const totalElement = document.querySelector('.text-gray-600');
    if (totalElement && totalElement.textContent.includes('총')) {
        const currentTotal = parseInt(totalElement.textContent.match(/\d+/)[0]);
        totalElement.textContent = `총 ${currentTotal + 1}개의 할 일`;
    }
}

/**
 * Initialize quick add task functionality
 */
function initQuickAddTask() {
    const form = document.getElementById('quick-add-task-form');
    const input = document.getElementById('quick-add-task-input');
    const button = document.getElementById('quick-add-task-btn');

    if (!form || !input || !button) return;

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const title = input.value.trim();

        // Silent ignore if empty
        if (!title) {
            return;
        }

        // Disable input during submission
        input.disabled = true;
        button.disabled = true;
        button.textContent = '추가 중...';

        // Create task
        const task = await quickAddTask(title);

        // Re-enable input
        input.disabled = false;
        button.disabled = false;
        button.textContent = '추가';

        if (task) {
            // Add to list
            addTaskToList(task);

            // Clear input
            input.value = '';

            // Keep focus for continuous input (do not blur)
            // input.blur(); // Removed to maintain focus
        }
    });

    // Show button on focus
    input.addEventListener('focus', function() {
        button.classList.remove('hidden');
    });

    // Hide button on blur (with delay to allow button click)
    input.addEventListener('blur', function() {
        setTimeout(() => {
            // Only hide if input is still blurred (not refocused)
            if (document.activeElement !== input) {
                button.classList.add('hidden');
            }
        }, 150);
    });
}

/**
 * Focus on Quick Add input (called from header button)
 */
window.focusQuickAddInput = function() {
    const input = document.getElementById('quick-add-task-input');
    if (input) {
        // Scroll to bottom (where quick add is)
        window.scrollTo({
            top: document.body.scrollHeight,
            behavior: 'smooth'
        });

        // Focus after scroll animation
        setTimeout(() => {
            input.focus();
        }, 300);
    }
};

/**
 * Initialize when DOM is ready
 */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQuickAddTask);
} else {
    initQuickAddTask();
}
