<?php

declare(strict_types=1);

namespace Src\Application\TaskList\UseCases;

use Src\Application\TaskList\DTOs\MoveTaskListToGroupDTO;
use Src\Domain\TaskList\Repositories\TaskListRepositoryInterface;
use Src\Shared\Exceptions\NotFoundException;

/**
 * MoveTaskListToGroup UseCase
 *
 * TaskList를 다른 TaskGroup으로 이동시킵니다
 */
final readonly class MoveTaskListToGroup
{
    public function __construct(
        private TaskListRepositoryInterface $taskListRepository
    ) {
    }

    /**
     * Execute the use case
     *
     * @param MoveTaskListToGroupDTO $dto
     * @return void
     * @throws NotFoundException TaskList를 찾을 수 없을 때
     */
    public function execute(MoveTaskListToGroupDTO $dto): void
    {
        // TaskList 존재 확인
        if (!$this->taskListRepository->existsById($dto->taskListId)) {
            throw new NotFoundException("TaskList not found: ID {$dto->taskListId}");
        }

        // Repository를 통해 그룹 이동 및 순서 변경
        $this->taskListRepository->moveToGroup(
            $dto->taskListId,
            $dto->taskGroupId,
            $dto->order
        );
    }
}
