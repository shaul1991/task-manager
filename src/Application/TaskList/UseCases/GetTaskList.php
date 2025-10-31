<?php

declare(strict_types=1);

namespace Src\Application\TaskList\UseCases;

use Src\Application\TaskList\DTOs\TaskListDTO;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Domain\TaskList\Repositories\TaskListRepositoryInterface;
use Src\Shared\Exceptions\NotFoundException;

/**
 * Get TaskList Use Case
 *
 * ID로 TaskList를 조회합니다.
 */
final readonly class GetTaskList
{
    public function __construct(
        private TaskListRepositoryInterface $taskListRepository,
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Use Case 실행
     *
     * @param int $id TaskList ID
     * @return TaskListDTO TaskList DTO
     * @throws NotFoundException TaskList를 찾을 수 없는 경우
     */
    public function execute(int $id): TaskListDTO
    {
        // TaskList 조회
        $taskList = $this->taskListRepository->findById($id);

        // 존재하지 않으면 예외 발생
        if ($taskList === null) {
            throw new NotFoundException("TaskList를 찾을 수 없습니다. ID: {$id}");
        }

        // 미완료 Task 개수 조회
        $incompleteTaskCount = $this->taskRepository->countIncompleteByTaskListId($id);

        // DTO로 변환하여 반환
        return TaskListDTO::fromEntity($taskList, $incompleteTaskCount);
    }
}
