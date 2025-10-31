/**
 * Edit TaskList Title & Description (Inline Editing)
 * Handles inline editing for TaskList title and description in main-header
 */

let isEditingTitle = false;
let isEditingDescription = false;
let originalTitle = '';
let originalDescription = '';

/**
 * Initialize inline title editing
 */
function initEditableTitle() {
    const container = document.getElementById('editable-title-container');
    if (!container) return;

    const displayElement = document.getElementById('editable-title-display');
    const inputElement = document.getElementById('editable-title-input');
    const taskListId = container.dataset.taskListId;

    if (!displayElement || !inputElement || !taskListId) return;

    // Click on title to edit
    displayElement.addEventListener('click', function() {
        enterTitleEditMode();
    });

    // Save on Enter key
    inputElement.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveTitle();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            cancelTitleEdit();
        }
    });

    // Save on blur
    inputElement.addEventListener('blur', function() {
        // Delay to allow click on save button if needed
        setTimeout(() => {
            if (isEditingTitle) {
                saveTitle();
            }
        }, 100);
    });
}

/**
 * Initialize inline description editing
 */
function initEditableDescription() {
    const container = document.getElementById('editable-description-container');
    if (!container) return;

    const displayElement = document.getElementById('editable-description-display');
    const inputElement = document.getElementById('editable-description-input');
    const taskListId = container.dataset.taskListId;

    if (!displayElement || !inputElement || !taskListId) return;

    // Click on description to edit
    displayElement.addEventListener('click', function() {
        enterDescriptionEditMode();
    });

    // Save on Enter key
    inputElement.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveDescription();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            cancelDescriptionEdit();
        }
    });

    // Save on blur
    inputElement.addEventListener('blur', function() {
        // Delay to allow click on save button if needed
        setTimeout(() => {
            if (isEditingDescription) {
                saveDescription();
            }
        }, 100);
    });
}

/**
 * Enter title edit mode
 */
function enterTitleEditMode() {
    if (isEditingTitle) return;

    const displayElement = document.getElementById('editable-title-display');
    const inputElement = document.getElementById('editable-title-input');

    originalTitle = displayElement.textContent.trim();
    inputElement.value = originalTitle;

    displayElement.classList.add('hidden');
    inputElement.classList.remove('hidden');
    inputElement.focus();
    inputElement.select();

    isEditingTitle = true;
}

/**
 * Cancel title edit
 */
function cancelTitleEdit() {
    if (!isEditingTitle) return;

    const displayElement = document.getElementById('editable-title-display');
    const inputElement = document.getElementById('editable-title-input');

    inputElement.value = originalTitle;
    inputElement.classList.add('hidden');
    displayElement.classList.remove('hidden');

    isEditingTitle = false;
}

/**
 * Enter description edit mode
 */
function enterDescriptionEditMode() {
    if (isEditingDescription) return;

    const displayElement = document.getElementById('editable-description-display');
    const inputElement = document.getElementById('editable-description-input');

    originalDescription = displayElement.textContent.trim();

    // "설명을 추가하려면 클릭하세요"인 경우 빈 문자열로 시작
    if (displayElement.classList.contains('text-gray-400')) {
        inputElement.value = '';
    } else {
        inputElement.value = originalDescription;
    }

    displayElement.classList.add('hidden');
    inputElement.classList.remove('hidden');
    inputElement.focus();
    inputElement.select();

    isEditingDescription = true;
}

/**
 * Cancel description edit
 */
function cancelDescriptionEdit() {
    if (!isEditingDescription) return;

    const displayElement = document.getElementById('editable-description-display');
    const inputElement = document.getElementById('editable-description-input');

    inputElement.value = originalDescription;
    inputElement.classList.add('hidden');
    displayElement.classList.remove('hidden');

    isEditingDescription = false;
}

/**
 * Save title via AJAX
 */
async function saveTitle() {
    if (!isEditingTitle) return;

    const container = document.getElementById('editable-title-container');
    const displayElement = document.getElementById('editable-title-display');
    const inputElement = document.getElementById('editable-title-input');
    const taskListId = container.dataset.taskListId;

    const newTitle = inputElement.value.trim();

    // Validate
    if (!newTitle) {
        alert('제목을 입력해주세요.');
        inputElement.focus();
        return;
    }

    if (newTitle === originalTitle) {
        // No change
        cancelTitleEdit();
        return;
    }

    // Disable input during save
    inputElement.disabled = true;

    try {
        const response = await axios.put(`/task-lists/${taskListId}`, {
            name: newTitle
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.data.success) {
            // Update display
            displayElement.textContent = newTitle;
            originalTitle = newTitle;

            // Update sidebar
            updateSidebarTitle(taskListId, newTitle);

            // Exit edit mode
            inputElement.classList.add('hidden');
            displayElement.classList.remove('hidden');
            isEditingTitle = false;

            // Show success feedback (optional)
            console.log('TaskList 제목이 성공적으로 수정되었습니다.');
        } else {
            throw new Error(response.data.message || '제목 수정 실패');
        }
    } catch (error) {
        console.error('Failed to update title:', error);

        // Show error message
        if (error.response?.data?.message) {
            alert('제목 수정 실패: ' + error.response.data.message);
        } else {
            alert('제목 수정 중 오류가 발생했습니다.');
        }

        // Restore original title
        inputElement.value = originalTitle;
    } finally {
        inputElement.disabled = false;
        inputElement.focus();
    }
}

/**
 * Save description via AJAX
 */
async function saveDescription() {
    if (!isEditingDescription) return;

    const container = document.getElementById('editable-description-container');
    const displayElement = document.getElementById('editable-description-display');
    const inputElement = document.getElementById('editable-description-input');
    const taskListId = container.dataset.taskListId;

    const newDescription = inputElement.value.trim();

    if (newDescription === originalDescription) {
        // No change
        cancelDescriptionEdit();
        return;
    }

    // Disable input during save
    inputElement.disabled = true;

    try {
        const response = await axios.put(`/task-lists/${taskListId}`, {
            description: newDescription
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.data.success) {
            // Update display
            if (newDescription) {
                displayElement.textContent = newDescription;
                displayElement.classList.remove('text-gray-400', 'italic');
                displayElement.classList.add('text-gray-600');
                displayElement.title = '클릭하여 편집';
            } else {
                displayElement.textContent = '설명을 추가하려면 클릭하세요';
                displayElement.classList.remove('text-gray-600');
                displayElement.classList.add('text-gray-400', 'italic');
                displayElement.title = '클릭하여 설명 추가';
            }
            originalDescription = newDescription;

            // Exit edit mode
            inputElement.classList.add('hidden');
            displayElement.classList.remove('hidden');
            isEditingDescription = false;

            // Show success feedback (optional)
            console.log('TaskList 설명이 성공적으로 수정되었습니다.');
        } else {
            throw new Error(response.data.message || '설명 수정 실패');
        }
    } catch (error) {
        console.error('Failed to update description:', error);

        // Show error message
        if (error.response?.data?.message) {
            alert('설명 수정 실패: ' + error.response.data.message);
        } else {
            alert('설명 수정 중 오류가 발생했습니다.');
        }

        // Restore original description
        inputElement.value = originalDescription;
    } finally {
        inputElement.disabled = false;
        inputElement.focus();
    }
}

/**
 * Update sidebar TaskList title
 */
function updateSidebarTitle(taskListId, newTitle) {
    const sidebarItem = document.querySelector(`[data-tasklist-id="${taskListId}"] span.text-sm`);
    if (sidebarItem) {
        sidebarItem.textContent = newTitle;
    }
}

/**
 * Initialize all editable elements
 */
function initEditableElements() {
    initEditableTitle();
    initEditableDescription();
}

/**
 * Initialize when DOM is ready
 */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEditableElements);
} else {
    initEditableElements();
}
