<?php

declare(strict_types=1);

namespace Src\Application\TaskGroup\UseCases;

use Src\Application\TaskGroup\DTOs\CreateTaskGroupDTO;
use Src\Application\TaskGroup\DTOs\TaskGroupDTO;
use Src\Domain\TaskGroup\Entities\TaskGroup;
use Src\Domain\TaskGroup\Repositories\TaskGroupRepositoryInterface;
use Src\Domain\TaskGroup\ValueObjects\TaskGroupName;

/**
 * Create TaskGroup UseCase
 *
 * TaskGroup 생성 유스케이스
 */
final readonly class CreateTaskGroup
{
    public function __construct(
        private TaskGroupRepositoryInterface $taskGroupRepository
    ) {
    }

    /**
     * Execute the use case
     *
     * @param CreateTaskGroupDTO $dto
     * @return TaskGroupDTO
     */
    public function execute(CreateTaskGroupDTO $dto): TaskGroupDTO
    {
        // Create Domain Entity
        $taskGroup = TaskGroup::create(
            name: new TaskGroupName($dto->name)
        );

        // Save to repository
        $savedTaskGroup = $this->taskGroupRepository->save($taskGroup);

        // Return DTO
        return TaskGroupDTO::fromEntity($savedTaskGroup);
    }
}
