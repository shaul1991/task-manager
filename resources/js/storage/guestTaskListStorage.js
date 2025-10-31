/**
 * Guest TaskList Storage
 * LocalStorage-based storage for TaskList in guest mode
 */

// LocalStorage key
const STORAGE_KEY = 'guest_task_lists';

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
 * Guest TaskList Storage
 */
export const guestTaskListStorage = {
    /**
     * Get all TaskLists
     */
    getAll() {
        const taskLists = localStorage.getItem(STORAGE_KEY);
        return taskLists ? JSON.parse(taskLists) : [];
    },

    /**
     * Add a new TaskList
     */
    add(taskList) {
        const taskLists = this.getAll();
        const maxOrder = taskLists.reduce((max, tl) => Math.max(max, tl.order || 0), -1);
        const newTaskList = {
            id: generateUUID(),
            ...taskList,
            order: maxOrder + 1,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        taskLists.push(newTaskList);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(taskLists));
        return newTaskList;
    },

    /**
     * Update a TaskList
     */
    update(id, updates) {
        const taskLists = this.getAll();
        const index = taskLists.findIndex(tl => tl.id === id);
        if (index !== -1) {
            taskLists[index] = {
                ...taskLists[index],
                ...updates,
                updated_at: new Date().toISOString()
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(taskLists));
            return taskLists[index];
        }
        return null;
    },

    /**
     * Delete a TaskList
     */
    delete(id) {
        const taskLists = this.getAll();
        const filtered = taskLists.filter(tl => tl.id !== id);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(filtered));
    },

    /**
     * Find a TaskList by ID
     */
    findById(id) {
        const taskLists = this.getAll();
        return taskLists.find(tl => tl.id === id) || null;
    },

    /**
     * Update order of multiple TaskLists
     */
    updateOrders(orderMap) {
        const taskLists = this.getAll();
        const updatedTaskLists = taskLists.map(tl => {
            if (orderMap[tl.id] !== undefined) {
                return {
                    ...tl,
                    order: orderMap[tl.id],
                    updated_at: new Date().toISOString()
                };
            }
            return tl;
        });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(updatedTaskLists));
    },

    /**
     * Move TaskList to a different TaskGroup
     */
    moveToGroup(id, taskGroupId, order) {
        const taskLists = this.getAll();
        const index = taskLists.findIndex(tl => tl.id === id);
        if (index !== -1) {
            taskLists[index] = {
                ...taskLists[index],
                task_group_id: taskGroupId,
                order: order,
                updated_at: new Date().toISOString()
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(taskLists));
            return taskLists[index];
        }
        return null;
    }
};

/**
 * Export for use in guest mode
 */
window.guestTaskListStorage = guestTaskListStorage;
