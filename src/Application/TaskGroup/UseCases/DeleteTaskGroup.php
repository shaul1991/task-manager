<?php

declare(strict_types=1);

namespace Src\Application\TaskGroup\UseCases;

use Src\Domain\TaskGroup\Repositories\TaskGroupRepositoryInterface;

/**
 * Delete TaskGroup UseCase
 *
 * TaskGroup 삭제 유스케이스
 * 하위 TaskList들의 task_group_id를 NULL로 변경한 후 TaskGroup을 SoftDelete 처리
 */
final readonly class DeleteTaskGroup
{
    public function __construct(
        private TaskGroupRepositoryInterface $taskGroupRepository
    ) {
    }

    /**
     * Execute the use case
     *
     * @param int $id
     * @return void
     * @throws \Exception TaskGroup not found
     */
    public function execute(int $id): void
    {
        // Check if TaskGroup exists
        if (!$this->taskGroupRepository->existsById($id)) {
            throw new \Exception("TaskGroup with ID {$id} not found");
        }

        // Unassign all TaskLists from this TaskGroup (set task_group_id to NULL)
        $this->taskGroupRepository->unassignTaskListsFromGroup($id);

        // Delete TaskGroup (SoftDelete)
        $this->taskGroupRepository->delete($id);
    }
}
