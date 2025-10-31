<?php

declare(strict_types=1);

namespace Src\Application\TaskGroup\UseCases;

use Src\Application\TaskGroup\DTOs\TaskGroupDTO;
use Src\Domain\TaskGroup\Repositories\TaskGroupRepositoryInterface;

/**
 * Get TaskGroup List UseCase
 *
 * TaskGroup 목록 조회 유스케이스
 */
final readonly class GetTaskGroupList
{
    public function __construct(
        private TaskGroupRepositoryInterface $taskGroupRepository
    ) {
    }

    /**
     * Execute the use case
     *
     * @param int $limit
     * @param int $offset
     * @return array<TaskGroupDTO>
     */
    public function execute(int $limit = 100, int $offset = 0): array
    {
        // Get all TaskGroups from repository
        $taskGroups = $this->taskGroupRepository->findAll($limit, $offset);

        // Convert to DTOs
        return array_map(
            fn($taskGroup) => TaskGroupDTO::fromEntity($taskGroup),
            $taskGroups
        );
    }
}
