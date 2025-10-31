/**
 * Quick Add TaskGroup
 * Handles inline task group creation from the sidebar quick add input
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
 * Get TaskGroup icon SVG (Font Awesome layers icon)
 */
function getTaskGroupIconSvg() {
    return `
        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="currentColor">
            <path d="M296.5 69.2C311.4 62.3 328.6 62.3 343.5 69.2L562.1 170.2C570.6 174.1 576 182.6 576 192C576 201.4 570.6 209.9 562.1 213.8L343.5 314.8C328.6 321.7 311.4 321.7 296.5 314.8L77.9 213.8C69.4 209.8 64 201.3 64 192C64 182.7 69.4 174.1 77.9 170.2L296.5 69.2zM112.1 282.4L276.4 358.3C304.1 371.1 336 371.1 363.7 358.3L528 282.4L562.1 298.2C570.6 302.1 576 310.6 576 320C576 329.4 570.6 337.9 562.1 341.8L343.5 442.8C328.6 449.7 311.4 449.7 296.5 442.8L77.9 341.8C69.4 337.8 64 329.3 64 320C64 310.7 69.4 302.1 77.9 298.2L112 282.4zM77.9 426.2L112 410.4L276.3 486.3C304 499.1 335.9 499.1 363.6 486.3L527.9 410.4L562 426.2C570.5 430.1 575.9 438.6 575.9 448C575.9 457.4 570.5 465.9 562 469.8L343.4 570.8C328.5 577.7 311.3 577.7 296.4 570.8L77.9 469.8C69.4 465.8 64 457.3 64 448C64 438.7 69.4 430.1 77.9 426.2z"/>
        </svg>
    `;
}

/**
 * Get chevron icon SVG
 */
function getChevronIconSvg() {
    return `
        <svg class="task-group-chevron h-4 w-4 transition-transform rotate-90" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
        </svg>
    `;
}

/**
 * Create task group via AJAX
 */
async function quickAddTaskGroup(name) {
    try {
        const response = await axios.post('/task-groups', {
            name: name
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.data.success) {
            return response.data.taskGroup;
        }

        return null;
    } catch (error) {
        console.error('Failed to create task group:', error);

        // Show error message
        if (error.response?.data?.message) {
            alert('그룹 생성 실패: ' + error.response.data.message);
        } else {
            alert('그룹 생성 중 오류가 발생했습니다.');
        }

        return null;
    }
}

/**
 * Add task group to the sidebar DOM
 */
function addTaskGroupToSidebar(taskGroup) {
    // Find the task group list container
    const taskGroupListContainer = document.getElementById('task-group-list-container');

    if (!taskGroupListContainer) {
        console.error('TaskGroup list container not found');
        return;
    }

    // Create task group item HTML
    const taskGroupItemHtml = `
        <div class="task-group-container" data-task-group-id="${taskGroup.id}">
            <button
                type="button"
                class="task-group-header flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-gray-700 hover:bg-gray-100"
                data-task-group-toggle="${taskGroup.id}"
                aria-expanded="true"
            >
                <div class="flex items-center gap-3">
                    ${getChevronIconSvg()}
                    ${getTaskGroupIconSvg()}
                    <span class="text-sm font-semibold">${escapeHtml(taskGroup.name)}</span>
                </div>
                <span class="text-xs text-gray-500">0</span>
            </button>
            <div class="task-group-content" data-task-group-content="${taskGroup.id}">
                <div class="ml-6 space-y-1 border-l-2 border-gray-200 pl-3 py-1">
                    <!-- TaskLists will be added here -->
                </div>
            </div>
        </div>
    `;

    // Insert at the top of the list
    taskGroupListContainer.insertAdjacentHTML('afterbegin', taskGroupItemHtml);

    // Re-initialize accordion for the new item
    const newToggle = document.querySelector(`[data-task-group-toggle="${taskGroup.id}"]`);
    if (newToggle) {
        const content = document.querySelector(`[data-task-group-content="${taskGroup.id}"]`);
        const chevron = newToggle.querySelector('.task-group-chevron');

        if (content && chevron) {
            newToggle.addEventListener('click', function(e) {
                e.preventDefault();

                const isExpanded = newToggle.getAttribute('aria-expanded') === 'true';
                const taskGroupId = taskGroup.id;

                if (isExpanded) {
                    content.classList.add('hidden');
                    chevron.classList.remove('rotate-90');
                    newToggle.setAttribute('aria-expanded', 'false');
                    localStorage.setItem(`taskgroup_${taskGroupId}_collapsed`, 'true');
                } else {
                    content.classList.remove('hidden');
                    chevron.classList.add('rotate-90');
                    newToggle.setAttribute('aria-expanded', 'true');
                    localStorage.setItem(`taskgroup_${taskGroupId}_collapsed`, 'false');
                }
            });
        }
    }
}

/**
 * Initialize quick add task group functionality
 */
function initQuickAddTaskGroup() {
    const form = document.getElementById('quick-add-taskgroup-form');
    const input = document.getElementById('quick-add-taskgroup-input');

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

        // Create task group
        const taskGroup = await quickAddTaskGroup(name);

        // Re-enable input
        input.disabled = false;

        if (taskGroup) {
            // Add to sidebar
            addTaskGroupToSidebar(taskGroup);

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
    document.addEventListener('DOMContentLoaded', initQuickAddTaskGroup);
} else {
    initQuickAddTaskGroup();
}
