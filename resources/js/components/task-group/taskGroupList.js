/**
 * TaskGroup List - Accordion Toggle
 * Handles expand/collapse for TaskGroup items
 */

/**
 * Initialize TaskGroup accordion functionality
 */
function initTaskGroupAccordion() {
    const taskGroupToggles = document.querySelectorAll('[data-task-group-toggle]');

    taskGroupToggles.forEach(toggle => {
        const taskGroupId = toggle.getAttribute('data-task-group-toggle');
        const contentWrapper = document.querySelector(`[data-task-group-content="${taskGroupId}"]`);
        const chevron = toggle.querySelector('.task-group-chevron');

        if (!contentWrapper || !chevron) return;

        // content의 부모 요소 (실제로 hidden 클래스가 적용되는 요소)
        const content = contentWrapper.parentElement;

        // Load saved state from localStorage
        const savedState = localStorage.getItem(`taskgroup_${taskGroupId}_collapsed`);

        // 기본값은 접힌 상태
        // localStorage에 명시적으로 'false'(펼침)로 저장된 경우만 펼침
        if (savedState === 'false') {
            // Expand
            content.classList.add('expanded');
            chevron.classList.add('rotate-90');
            toggle.setAttribute('aria-expanded', 'true');
        } else {
            // Collapsed (기본 상태 유지)
            content.classList.remove('expanded');
            chevron.classList.remove('rotate-90');
            toggle.setAttribute('aria-expanded', 'false');
        }

        // Toggle on click
        toggle.addEventListener('click', function(e) {
            e.preventDefault();

            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

            if (isExpanded) {
                // Collapse with slide animation
                content.classList.remove('expanded');
                chevron.classList.remove('rotate-90');
                toggle.setAttribute('aria-expanded', 'false');
                localStorage.setItem(`taskgroup_${taskGroupId}_collapsed`, 'true');
            } else {
                // Expand with slide animation
                content.classList.add('expanded');
                chevron.classList.add('rotate-90');
                toggle.setAttribute('aria-expanded', 'true');
                localStorage.setItem(`taskgroup_${taskGroupId}_collapsed`, 'false');
            }
        });
    });
}

/**
 * Initialize when DOM is ready
 */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTaskGroupAccordion);
} else {
    initTaskGroupAccordion();
}
