<?php

declare(strict_types=1);

namespace Src\Application\Task\UseCases;

use Src\Application\Task\DTOs\TaskDTO;
use Src\Application\Task\DTOs\TaskListDTO;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;

/**
 * Get Task List Use Case
 *
 * Task 목록을 조회합니다.
 */
final readonly class GetTaskList
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Use Case 실행
     *
     * @param int|null $taskListId TaskList ID 필터 (null이면 필터링 안함)
     * @param bool|null $completed 완료 상태 필터 (null이면 필터링 안함)
     * @param int $limit 조회 개수 제한
     * @param int $offset 오프셋
     * @return TaskListDTO Task 목록 DTO
     */
    public function execute(
        ?int $taskListId = null,
        ?bool $completed = null,
        int $limit = 100,
        int $offset = 0
    ): TaskListDTO {
        // TaskList 이름을 포함한 Task 목록 조회
        $tasksWithTaskListName = $this->taskRepository->findAllWithTaskListName(
            taskListId: $taskListId,
            completed: $completed,
            limit: $limit,
            offset: $offset
        );

        // Domain Entity → DTO 변환 (taskListName 포함)
        $taskDTOs = array_map(
            fn(array $item) => TaskDTO::fromEntity($item['task'], $item['taskListName']),
            $tasksWithTaskListName
        );

        // 전체 개수 조회
        $total = count($taskDTOs);

        // TaskListDTO 생성
        return new TaskListDTO(
            tasks: $taskDTOs,
            total: $total,
            limit: $limit,
            offset: $offset
        );
    }
}
