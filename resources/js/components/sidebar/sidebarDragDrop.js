/**
 * Sidebar Drag & Drop
 * TaskGroup 및 TaskList의 드래그 앤 드롭 기능 구현
 */

import Sortable from 'sortablejs';

/**
 * TaskGroup 드래그 앤 드롭 초기화
 */
function initTaskGroupDragDrop() {
    const taskGroupContainer = document.getElementById('task-group-list-container');

    if (!taskGroupContainer) {
        return;
    }

    Sortable.create(taskGroupContainer, {
        animation: 150,
        delay: 1000, // 1초 이상 누르고 있어야 드래그 시작
        delayOnTouchOnly: false, // 모든 디바이스에서 delay 적용
        handle: '.task-group-header',
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        filter: '.task-group-content', // 내부 콘텐츠는 드래그 불가
        onEnd: async function(evt) {
            // 드래그 완료 후 순서 계산
            const taskGroupItems = taskGroupContainer.querySelectorAll('.task-group-container');
            const items = [];

            taskGroupItems.forEach((item, index) => {
                const taskGroupId = parseInt(item.getAttribute('data-task-group-id'));
                items.push({
                    id: taskGroupId,
                    order: index
                });
            });

            // 서버에 순서 저장
            try {
                const response = await window.axios.patch('/task-groups/reorder', {
                    items: items
                });

                if (!response.data.success) {
                    console.error('TaskGroup 순서 저장 실패:', response.data.message);
                    // 실패 시 원래 위치로 롤백
                    evt.item.parentNode.insertBefore(evt.item, evt.item.parentNode.children[evt.oldIndex]);
                }
            } catch (error) {
                console.error('TaskGroup 순서 저장 중 오류:', error);
                // 실패 시 원래 위치로 롤백
                evt.item.parentNode.insertBefore(evt.item, evt.item.parentNode.children[evt.oldIndex]);
            }
        }
    });
}

/**
 * TaskList 드래그 앤 드롭 초기화 (TaskGroup 내부)
 */
function initTaskListDragDropInGroups() {
    const taskGroupContents = document.querySelectorAll('[data-task-group-content]');

    taskGroupContents.forEach(content => {
        const taskGroupId = parseInt(content.getAttribute('data-task-group-content'));

        Sortable.create(content, {
            group: 'task-lists', // 같은 그룹끼리 이동 가능
            animation: 150,
            delay: 1000, // 1초 이상 누르고 있어야 드래그 시작
            delayOnTouchOnly: false, // 모든 디바이스에서 delay 적용
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: async function(evt) {
                const from = evt.from;
                const to = evt.to;
                const taskListId = parseInt(evt.item.getAttribute('data-tasklist-id'));

                // 이동 전후의 TaskGroup ID 확인
                const fromGroupId = from.getAttribute('data-task-group-content');
                const toGroupId = to.getAttribute('data-task-group-content');

                // 그룹이 변경되었는지 확인
                if (fromGroupId !== toGroupId) {
                    // 다른 그룹으로 이동
                    const newGroupId = toGroupId ? parseInt(toGroupId) : null;
                    const newOrder = evt.newIndex;

                    try {
                        const response = await window.axios.patch(`/task-lists/${taskListId}/move`, {
                            task_group_id: newGroupId,
                            order: newOrder
                        });

                        if (!response.data.success) {
                            console.error('TaskList 이동 실패:', response.data.message);
                            // 실패 시 원래 위치로 롤백
                            from.insertBefore(evt.item, from.children[evt.oldIndex]);
                        } else {
                            // 성공 시 페이지 새로고침 (카운트 업데이트를 위해)
                            setTimeout(() => window.location.reload(), 300);
                        }
                    } catch (error) {
                        console.error('TaskList 이동 중 오류:', error);
                        // 실패 시 원래 위치로 롤백
                        from.insertBefore(evt.item, from.children[evt.oldIndex]);
                    }
                } else {
                    // 같은 그룹 내에서 순서만 변경
                    const taskListItems = to.querySelectorAll('[data-tasklist-id]');
                    const items = [];

                    taskListItems.forEach((item, index) => {
                        const id = parseInt(item.getAttribute('data-tasklist-id'));
                        items.push({
                            id: id,
                            order: index
                        });
                    });

                    try {
                        const response = await window.axios.patch('/task-lists/reorder', {
                            items: items
                        });

                        if (!response.data.success) {
                            console.error('TaskList 순서 저장 실패:', response.data.message);
                            // 실패 시 원래 위치로 롤백
                            to.insertBefore(evt.item, to.children[evt.oldIndex]);
                        }
                    } catch (error) {
                        console.error('TaskList 순서 저장 중 오류:', error);
                        // 실패 시 원래 위치로 롤백
                        to.insertBefore(evt.item, to.children[evt.oldIndex]);
                    }
                }
            }
        });
    });
}

/**
 * Ungrouped TaskList 드래그 앤 드롭 초기화
 */
function initUngroupedTaskListDragDrop() {
    const ungroupedContainer = document.getElementById('ungrouped-tasklist-items-container');

    if (!ungroupedContainer) {
        return;
    }

    Sortable.create(ungroupedContainer, {
        group: 'task-lists', // TaskGroup의 TaskList와 같은 그룹
        animation: 150,
        delay: 1000, // 1초 이상 누르고 있어야 드래그 시작
        delayOnTouchOnly: false, // 모든 디바이스에서 delay 적용
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        onEnd: async function(evt) {
            const from = evt.from;
            const to = evt.to;
            const taskListId = parseInt(evt.item.getAttribute('data-tasklist-id'));

            // from이 TaskGroup 내부인지 확인
            const fromGroupId = from.getAttribute('data-task-group-content');
            const toIsUngrouped = to.id === 'ungrouped-tasklist-items-container';

            if (fromGroupId && toIsUngrouped) {
                // TaskGroup에서 Ungrouped로 이동
                try {
                    const response = await window.axios.patch(`/task-lists/${taskListId}/move`, {
                        task_group_id: null,
                        order: evt.newIndex
                    });

                    if (!response.data.success) {
                        console.error('TaskList Ungrouped로 이동 실패:', response.data.message);
                        // 실패 시 원래 위치로 롤백
                        from.insertBefore(evt.item, from.children[evt.oldIndex]);
                    } else {
                        // 성공 시 페이지 새로고침
                        setTimeout(() => window.location.reload(), 300);
                    }
                } catch (error) {
                    console.error('TaskList 이동 중 오류:', error);
                    // 실패 시 원래 위치로 롤백
                    from.insertBefore(evt.item, from.children[evt.oldIndex]);
                }
            } else if (!fromGroupId && !toIsUngrouped) {
                // Ungrouped에서 TaskGroup으로 이동
                const toGroupId = to.getAttribute('data-task-group-content');
                if (toGroupId) {
                    try {
                        const response = await window.axios.patch(`/task-lists/${taskListId}/move`, {
                            task_group_id: parseInt(toGroupId),
                            order: evt.newIndex
                        });

                        if (!response.data.success) {
                            console.error('TaskList 그룹으로 이동 실패:', response.data.message);
                            // 실패 시 원래 위치로 롤백
                            from.insertBefore(evt.item, from.children[evt.oldIndex]);
                        } else {
                            // 성공 시 페이지 새로고침
                            setTimeout(() => window.location.reload(), 300);
                        }
                    } catch (error) {
                        console.error('TaskList 이동 중 오류:', error);
                        // 실패 시 원래 위치로 롤백
                        from.insertBefore(evt.item, from.children[evt.oldIndex]);
                    }
                }
            } else {
                // Ungrouped 내에서 순서만 변경
                const taskListItems = ungroupedContainer.querySelectorAll('[data-tasklist-id]');
                const items = [];

                taskListItems.forEach((item, index) => {
                    const id = parseInt(item.getAttribute('data-tasklist-id'));
                    items.push({
                        id: id,
                        order: index
                    });
                });

                try {
                    const response = await window.axios.patch('/task-lists/reorder', {
                        items: items
                    });

                    if (!response.data.success) {
                        console.error('TaskList 순서 저장 실패:', response.data.message);
                        // 실패 시 원래 위치로 롤백
                        ungroupedContainer.insertBefore(evt.item, ungroupedContainer.children[evt.oldIndex]);
                    }
                } catch (error) {
                    console.error('TaskList 순서 저장 중 오류:', error);
                    // 실패 시 원래 위치로 롤백
                    ungroupedContainer.insertBefore(evt.item, ungroupedContainer.children[evt.oldIndex]);
                }
            }
        }
    });
}

/**
 * 모든 드래그 앤 드롭 초기화
 */
export function initSidebarDragDrop() {
    initTaskGroupDragDrop();
    initTaskListDragDropInGroups();
    initUngroupedTaskListDragDrop();
}

/**
 * DOM이 로드되면 자동 초기화
 */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebarDragDrop);
} else {
    initSidebarDragDrop();
}
