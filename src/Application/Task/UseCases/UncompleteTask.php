<?php

declare(strict_types=1);

namespace Src\Application\Task\UseCases;

use Src\Application\Task\DTOs\TaskDTO;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Shared\Exceptions\NotFoundException;

/**
 * Uncomplete Task Use Case
 *
 * Task를 미완료 처리합니다.
 */
final readonly class UncompleteTask
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Use Case 실행
     *
     * @param int $taskId 미완료 처리할 Task ID
     * @return TaskDTO 미완료 처리된 Task DTO
     * @throws NotFoundException Task가 존재하지 않을 경우
     * @throws \Src\Domain\Task\Exceptions\TaskNotCompletedException
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

        // 미완료 처리
        $task->uncomplete();

        // Repository를 통해 저장
        $uncompletedTask = $this->taskRepository->save($task);

        // DTO로 변환하여 반환
        return TaskDTO::fromEntity($uncompletedTask);
    }
}
