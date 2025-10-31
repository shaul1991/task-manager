<?php

declare(strict_types=1);

namespace Src\Application\TaskGroup\UseCases;

use Src\Application\TaskGroup\DTOs\TaskGroupDTO;
use Src\Domain\TaskGroup\Repositories\TaskGroupRepositoryInterface;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Shared\Exceptions\NotFoundException;

/**
 * Get TaskGroup UseCase
 *
 * 단일 TaskGroup 조회 유스케이스
 * BFF 패턴을 위해 incompleteTaskCount 포함
 */
final readonly class GetTaskGroup
{
    public function __construct(
        private TaskGroupRepositoryInterface $taskGroupRepository,
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Execute the use case
     *
     * @param int $id
     * @return TaskGroupDTO
     * @throws NotFoundException
     */
    public function execute(int $id): TaskGroupDTO
    {
        // TaskGroup 조회
        $taskGroup = $this->taskGroupRepository->findById($id);

        if ($taskGroup === null) {
            throw new NotFoundException("TaskGroup with ID {$id} not found");
        }

        // 미완료 Task 개수 조회
        $incompleteTaskCount = $this->taskRepository->countIncompleteByTaskGroupId($id);

        // DTO 변환 (incompleteTaskCount 포함)
        return TaskGroupDTO::fromEntity($taskGroup, $incompleteTaskCount);
    }
}
