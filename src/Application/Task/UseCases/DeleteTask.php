<?php

declare(strict_types=1);

namespace Src\Application\Task\UseCases;

use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Shared\Exceptions\NotFoundException;

/**
 * Delete Task Use Case
 *
 * Task를 삭제합니다.
 */
final readonly class DeleteTask
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Use Case 실행
     *
     * @param int $taskId 삭제할 Task ID
     * @return void
     * @throws NotFoundException Task가 존재하지 않을 경우
     */
    public function execute(int $taskId): void
    {
        // Task 존재 확인
        if (!$this->taskRepository->existsById($taskId)) {
            throw new NotFoundException(
                message: 'Task not found',
                context: ['task_id' => $taskId]
            );
        }

        // Task 삭제
        $this->taskRepository->delete($taskId);
    }
}
