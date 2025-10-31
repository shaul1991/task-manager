/**
 * Task Slide-over Modal
 * Handles task detail view with auto-save functionality
 */

// Current task ID
let currentTaskId = null;

// Auto-save debounce timeout
let saveTimeout = null;

// Save state
let isSaving = false;

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
 * Show save status
 */
function showSaveStatus(status) {
    const saveStatus = document.getElementById('save-status');
    const saveSpinner = document.getElementById('save-spinner');
    const saveCheck = document.getElementById('save-check');
    const saveText = document.getElementById('save-text');

    if (status === 'saving') {
        isSaving = true;
        saveStatus.classList.remove('hidden');
        saveSpinner.classList.remove('hidden');
        saveCheck.classList.add('hidden');
        saveText.textContent = '저장 중...';
    } else if (status === 'saved') {
        isSaving = false;
        saveSpinner.classList.add('hidden');
        saveCheck.classList.remove('hidden');
        saveText.textContent = '저장됨';

        // Hide after 2 seconds
        setTimeout(() => {
            saveStatus.classList.add('hidden');
        }, 2000);
    } else if (status === 'error') {
        isSaving = false;
        saveSpinner.classList.add('hidden');
        saveCheck.classList.add('hidden');
        saveText.textContent = '저장 실패';
        saveStatus.classList.remove('bg-gray-50');
        saveStatus.classList.add('bg-red-50');

        // Revert to gray background after 3 seconds
        setTimeout(() => {
            saveStatus.classList.add('hidden');
            saveStatus.classList.remove('bg-red-50');
            saveStatus.classList.add('bg-gray-50');
        }, 3000);
    }
}

/**
 * Load task data
 */
async function loadTaskData(taskId) {
    const taskLoading = document.getElementById('task-loading');
    const taskContent = document.getElementById('task-content');

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
            document.getElementById('task-title').value = task.title || '';
            document.getElementById('task-description').value = task.description || '';
            document.getElementById('task-completed').checked = task.completed;

            // Update timestamps
            document.getElementById('task-created-at').textContent = formatDate(task.created_at);
            document.getElementById('task-updated-at').textContent = formatDate(task.updated_at);

            // Show/hide completed datetime
            const completedAtWrapper = document.getElementById('task-completed-at-wrapper');
            if (task.completed_datetime) {
                document.getElementById('task-completed-at').textContent = formatDate(task.completed_datetime);
                completedAtWrapper.classList.remove('hidden');
            } else {
                completedAtWrapper.classList.add('hidden');
            }

            // Update full edit link
            document.getElementById('full-edit-link').href = `/tasks/${taskId}/edit`;

            // Hide loading, show content
            taskLoading.classList.add('hidden');
            taskContent.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Failed to load task:', error);
        alert('할 일을 불러오는 중 오류가 발생했습니다.');
        window.slideOver.close('task-detail-modal');
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

            // Update timestamps
            const task = response.data.data;
            document.getElementById('task-updated-at').textContent = formatDate(task.updated_at);

            // Update completed datetime if changed
            const completedAtWrapper = document.getElementById('task-completed-at-wrapper');
            if (task.completed_datetime) {
                document.getElementById('task-completed-at').textContent = formatDate(task.completed_datetime);
                completedAtWrapper.classList.remove('hidden');
            } else {
                completedAtWrapper.classList.add('hidden');
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
    if (descriptionElement) {
        if (task.description) {
            descriptionElement.textContent = task.description;
            descriptionElement.classList.remove('text-gray-400', 'italic');
            descriptionElement.classList.add('text-gray-600');
        } else {
            descriptionElement.textContent = '설명 없음';
            descriptionElement.classList.add('text-gray-400', 'italic');
            descriptionElement.classList.remove('text-gray-600');
        }
    }

    // Update updated_at
    const updatedAtElement = taskItem.querySelector('.task-updated-at');
    if (updatedAtElement) {
        updatedAtElement.textContent = '수정: ' + formatDate(task.updated_at);
    }
}

/**
 * Delete task
 */
async function deleteTask(taskId) {
    if (!confirm('정말로 이 할 일을 삭제하시겠습니까?')) {
        return;
    }

    try {
        const response = await axios.delete(`/tasks/${taskId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (response.data.success) {
            // Close the slide-over
            window.slideOver.close('task-detail-modal');

            // Remove the task from the list
            const taskItem = document.querySelector(`div[data-task-id="${taskId}"]`);
            if (taskItem) {
                taskItem.remove();
            }

            // Show success message (optional, could use toast)
            alert('할 일이 삭제되었습니다.');

            // Reload the page if no tasks left
            const taskItems = document.querySelectorAll('div[data-task-id]');
            if (taskItems.length === 0) {
                window.location.reload();
            }
        }
    } catch (error) {
        console.error('Failed to delete task:', error);
        alert('할 일 삭제 중 오류가 발생했습니다.');
    }
}

/**
 * Initialize slide-over functionality
 */
function initTaskSlideOver() {
    // Title input - auto-save with debounce
    const titleInput = document.getElementById('task-title');
    if (titleInput) {
        titleInput.addEventListener('input', debounce(function () {
            if (!currentTaskId) return;

            const title = this.value.trim();

            // Validation
            const errorElement = document.getElementById('task-title-error');
            if (!title) {
                errorElement.textContent = '제목을 입력해주세요.';
                errorElement.classList.remove('hidden');
                return;
            }

            if (title.length > 255) {
                errorElement.textContent = '제목은 255자를 초과할 수 없습니다.';
                errorElement.classList.remove('hidden');
                return;
            }

            errorElement.classList.add('hidden');

            // Save
            saveTaskData(currentTaskId, { title });
        }, 500));
    }

    // Description textarea - auto-save with debounce
    const descriptionTextarea = document.getElementById('task-description');
    if (descriptionTextarea) {
        descriptionTextarea.addEventListener('input', debounce(function () {
            if (!currentTaskId) return;

            const description = this.value.trim();
            saveTaskData(currentTaskId, { description: description || null });
        }, 500));
    }

    // Completed checkbox - immediate save
    const completedCheckbox = document.getElementById('task-completed');
    if (completedCheckbox) {
        completedCheckbox.addEventListener('change', function () {
            if (!currentTaskId) return;

            // Send the opposite of current state since we want to toggle
            const completed = this.checked;

            // Use CompleteTask or UncompleteTask endpoint
            // For now, we'll just send completed state
            saveTaskData(currentTaskId, { completed });
        });
    }

    // Delete button
    const deleteButton = document.getElementById('delete-task-btn');
    if (deleteButton) {
        deleteButton.addEventListener('click', function () {
            if (currentTaskId) {
                deleteTask(currentTaskId);
            }
        });
    }
}

/**
 * Open task detail slide-over
 */
window.openTaskDetail = function (taskId) {
    currentTaskId = taskId;

    // Reset form
    document.getElementById('task-title').value = '';
    document.getElementById('task-description').value = '';
    document.getElementById('task-completed').checked = false;
    document.getElementById('task-title-error').classList.add('hidden');

    // Open slide-over
    window.slideOver.open('task-detail-modal');

    // Load task data
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
