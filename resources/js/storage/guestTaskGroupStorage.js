/**
 * Guest TaskGroup Storage
 * LocalStorage-based storage for TaskGroup in guest mode
 */

// LocalStorage key
const STORAGE_KEY = 'guest_task_groups';

/**
 * Generate UUID v4
 */
function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

/**
 * Guest TaskGroup Storage
 */
export const guestTaskGroupStorage = {
    /**
     * Get all TaskGroups
     */
    getAll() {
        const taskGroups = localStorage.getItem(STORAGE_KEY);
        return taskGroups ? JSON.parse(taskGroups) : [];
    },

    /**
     * Add a new TaskGroup
     */
    add(taskGroup) {
        const taskGroups = this.getAll();
        const maxOrder = taskGroups.reduce((max, tg) => Math.max(max, tg.order || 0), -1);
        const newTaskGroup = {
            id: generateUUID(),
            ...taskGroup,
            order: maxOrder + 1,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        taskGroups.push(newTaskGroup);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(taskGroups));
        return newTaskGroup;
    },

    /**
     * Update a TaskGroup
     */
    update(id, updates) {
        const taskGroups = this.getAll();
        const index = taskGroups.findIndex(tg => tg.id === id);
        if (index !== -1) {
            taskGroups[index] = {
                ...taskGroups[index],
                ...updates,
                updated_at: new Date().toISOString()
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(taskGroups));
            return taskGroups[index];
        }
        return null;
    },

    /**
     * Delete a TaskGroup
     */
    delete(id) {
        const taskGroups = this.getAll();
        const filtered = taskGroups.filter(tg => tg.id !== id);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(filtered));

        // Also unassign all TaskLists from this TaskGroup
        this.unassignTaskListsFromGroup(id);
    },

    /**
     * Find a TaskGroup by ID
     */
    findById(id) {
        const taskGroups = this.getAll();
        return taskGroups.find(tg => tg.id === id) || null;
    },

    /**
     * Unassign all TaskLists from a TaskGroup (set task_group_id to null)
     */
    unassignTaskListsFromGroup(taskGroupId) {
        // Import guest task list storage (assuming it exists)
        const taskListKey = 'guest_task_lists';
        const taskLists = JSON.parse(localStorage.getItem(taskListKey) || '[]');

        const updatedTaskLists = taskLists.map(taskList => {
            if (taskList.task_group_id === taskGroupId) {
                return {
                    ...taskList,
                    task_group_id: null,
                    updated_at: new Date().toISOString()
                };
            }
            return taskList;
        });

        localStorage.setItem(taskListKey, JSON.stringify(updatedTaskLists));
    },

    /**
     * Update order of multiple TaskGroups
     */
    updateOrders(orderMap) {
        const taskGroups = this.getAll();
        const updatedTaskGroups = taskGroups.map(tg => {
            if (orderMap[tg.id] !== undefined) {
                return {
                    ...tg,
                    order: orderMap[tg.id],
                    updated_at: new Date().toISOString()
                };
            }
            return tg;
        });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(updatedTaskGroups));
    }
};

/**
 * Export for use in guest mode
 */
window.guestTaskGroupStorage = guestTaskGroupStorage;
