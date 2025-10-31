<?php

declare(strict_types=1);

namespace Src\Application\TaskGroup\UseCases;

use Src\Application\TaskGroup\DTOs\UpdateTaskGroupDTO;
use Src\Application\TaskGroup\DTOs\TaskGroupDTO;
use Src\Domain\TaskGroup\Repositories\TaskGroupRepositoryInterface;
use Src\Domain\TaskGroup\ValueObjects\TaskGroupName;

/**
 * Update TaskGroup UseCase
 *
 * TaskGroup 업데이트 유스케이스
 */
final readonly class UpdateTaskGroup
{
    public function __construct(
        private TaskGroupRepositoryInterface $taskGroupRepository
    ) {
    }

    /**
     * Execute the use case
     *
     * @param UpdateTaskGroupDTO $dto
     * @return TaskGroupDTO
     * @throws \Exception TaskGroup not found
     */
    public function execute(UpdateTaskGroupDTO $dto): TaskGroupDTO
    {
        // Find TaskGroup
        $taskGroup = $this->taskGroupRepository->findById($dto->id);

        if ($taskGroup === null) {
            throw new \Exception("TaskGroup with ID {$dto->id} not found");
        }

        // Update name
        $taskGroup->updateName(new TaskGroupName($dto->name));

        // Save to repository
        $savedTaskGroup = $this->taskGroupRepository->save($taskGroup);

        // Return DTO
        return TaskGroupDTO::fromEntity($savedTaskGroup);
    }
}
