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
        const content = document.querySelector(`[data-task-group-content="${taskGroupId}"]`);
        const chevron = toggle.querySelector('.task-group-chevron');

        if (!content || !chevron) return;

        // Load saved state from localStorage
        const savedState = localStorage.getItem(`taskgroup_${taskGroupId}_collapsed`);
        const isCollapsed = savedState === 'true';

        if (isCollapsed) {
            content.classList.add('hidden');
            chevron.classList.remove('rotate-90');
            toggle.setAttribute('aria-expanded', 'false');
        }

        // Toggle on click
        toggle.addEventListener('click', function(e) {
            e.preventDefault();

            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

            if (isExpanded) {
                // Collapse
                content.classList.add('hidden');
                chevron.classList.remove('rotate-90');
                toggle.setAttribute('aria-expanded', 'false');
                localStorage.setItem(`taskgroup_${taskGroupId}_collapsed`, 'true');
            } else {
                // Expand
                content.classList.remove('hidden');
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
