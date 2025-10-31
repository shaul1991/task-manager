/**
 * Task Form Validation
 *
 * 클라이언트 측 폼 유효성 검증
 */

document.addEventListener('DOMContentLoaded', function() {
    const taskForm = document.getElementById('task-form');

    if (!taskForm) {
        return;
    }

    const titleInput = taskForm.querySelector('input[name="title"]');
    const descriptionTextarea = taskForm.querySelector('textarea[name="description"]');

    /**
     * 실시간 유효성 검증
     */
    titleInput?.addEventListener('input', function() {
        validateTitle(this);
    });

    /**
     * 폼 제출 시 유효성 검증
     */
    taskForm.addEventListener('submit', function(event) {
        let isValid = true;

        // 제목 검증
        if (!validateTitle(titleInput)) {
            isValid = false;
        }

        // 유효하지 않으면 제출 방지
        if (!isValid) {
            event.preventDefault();
        }
    });

    /**
     * 제목 유효성 검증
     *
     * @param {HTMLInputElement} input
     * @returns {boolean}
     */
    function validateTitle(input) {
        const value = input.value.trim();
        const errorElement = input.parentElement.querySelector('.text-red-600');

        // 기존 에러 메시지 제거
        if (errorElement && !errorElement.classList.contains('mt-1')) {
            errorElement.remove();
        }

        // 빈 값 검증
        if (value === '') {
            showError(input, '제목은 필수 입력 항목입니다.');
            return false;
        }

        // 길이 검증
        if (value.length > 255) {
            showError(input, '제목은 255자를 초과할 수 없습니다.');
            return false;
        }

        // 성공 시 에러 스타일 제거
        removeError(input);
        return true;
    }

    /**
     * 에러 메시지 표시
     *
     * @param {HTMLElement} input
     * @param {string} message
     */
    function showError(input, message) {
        // Input에 에러 스타일 추가
        input.classList.remove('border-gray-300', 'focus:ring-blue-500');
        input.classList.add('border-red-300', 'focus:ring-red-500');

        // 기존 에러 메시지가 없으면 추가
        let errorElement = input.parentElement.querySelector('.text-red-600.mt-1');
        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'mt-1 text-sm text-red-600';
            input.parentElement.appendChild(errorElement);
        }

        errorElement.textContent = message;
    }

    /**
     * 에러 스타일 및 메시지 제거
     *
     * @param {HTMLElement} input
     */
    function removeError(input) {
        input.classList.remove('border-red-300', 'focus:ring-red-500');
        input.classList.add('border-gray-300', 'focus:ring-blue-500');

        const errorElement = input.parentElement.querySelector('.text-red-600.mt-1');
        if (errorElement) {
            errorElement.remove();
        }
    }
});
