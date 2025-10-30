<?php

declare(strict_types=1);

namespace Src\Application\Task\UseCases;

use Src\Application\Task\DTOs\TaskDTO;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Shared\Exceptions\NotFoundException;

/**
 * Get Task Use Case
 *
 * 단일 Task를 조회합니다.
 */
final readonly class GetTask
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Use Case 실행
     *
     * @param int $taskId 조회할 Task ID
     * @return TaskDTO Task DTO
     * @throws NotFoundException Task가 존재하지 않을 경우
     */
    public function execute(int $taskId): TaskDTO
    {
        // Task 조회
        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            throw new NotFoundException(
                message: 'Task not found',
                context: ['task_id' => $taskId]
            );
        }

        // DTO로 변환하여 반환
        return TaskDTO::fromEntity($task);
    }
}
