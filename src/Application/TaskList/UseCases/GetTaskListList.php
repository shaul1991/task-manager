<?php

declare(strict_types=1);

namespace Src\Application\TaskList\UseCases;

use Src\Application\TaskList\DTOs\TaskListDTO;
use Src\Application\TaskList\DTOs\TaskListListDTO;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Domain\TaskList\Repositories\TaskListRepositoryInterface;

/**
 * Get TaskList List Use Case
 *
 * TaskList 목록을 조회합니다.
 */
final readonly class GetTaskListList
{
    public function __construct(
        private TaskListRepositoryInterface $taskListRepository,
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Use Case 실행
     *
     * @param int|null $userId 사용자 ID (null이면 게스트)
     * @param int $limit 조회 개수 제한 (기본값: 100)
     * @param int $offset 오프셋 (기본값: 0)
     * @return TaskListListDTO TaskList 목록 DTO
     */
    public function execute(
        ?int $userId = null,
        int $limit = 100,
        int $offset = 0
    ): TaskListListDTO {
        // Repository에서 TaskList 목록 조회
        $taskLists = $this->taskListRepository->findAll(
            userId: $userId,
            limit: $limit,
            offset: $offset
        );

        // Domain Entity → DTO 변환 (미완료 Task 개수 포함)
        $taskListDtos = array_map(
            fn($taskList) => TaskListDTO::fromEntity(
                taskList: $taskList,
                incompleteTaskCount: $this->taskRepository->countIncompleteByTaskListId($taskList->id())
            ),
            $taskLists
        );

        // TaskListListDTO 생성 및 반환
        return new TaskListListDTO(
            taskLists: $taskListDtos,
            total: count($taskListDtos)
        );
    }
}
