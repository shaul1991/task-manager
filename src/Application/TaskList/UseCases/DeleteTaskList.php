<?php

declare(strict_types=1);

namespace Src\Application\TaskList\UseCases;

use Src\Domain\TaskList\Repositories\TaskListRepositoryInterface;
use Src\Application\Exceptions\NotFoundException;

/**
 * TaskList 삭제 UseCase
 *
 * TaskList를 삭제하고, 속한 Task들을 고아 상태(task_list_id = null)로 만듭니다.
 */
final readonly class DeleteTaskList
{
    public function __construct(
        private TaskListRepositoryInterface $taskListRepository
    ) {
    }

    /**
     * TaskList를 삭제합니다.
     *
     * @param int $taskListId 삭제할 TaskList ID
     * @return void
     * @throws NotFoundException TaskList를 찾을 수 없을 때
     */
    public function execute(int $taskListId): void
    {
        // 1. TaskList 존재 여부 확인
        if (!$this->taskListRepository->existsById($taskListId)) {
            throw new NotFoundException("TaskList with ID {$taskListId} not found.");
        }

        // 2. TaskList에 속한 Task들을 고아 상태로 만들기
        $this->taskListRepository->orphanTasks($taskListId);

        // 3. TaskList 삭제 (SoftDelete)
        $this->taskListRepository->delete($taskListId);
    }
}
