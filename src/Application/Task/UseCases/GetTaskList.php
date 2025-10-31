<?php

declare(strict_types=1);

namespace Src\Application\Task\UseCases;

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
        // Task 목록 조회
        $tasks = $this->taskRepository->findAll(
            taskListId: $taskListId,
            completed: $completed,
            limit: $limit,
            offset: $offset
        );

        // 전체 개수 조회 (필터링 적용)
        // Note: 실제로는 Repository에 count 메서드를 추가하거나,
        // 별도 쿼리로 total을 조회해야 하지만, 지금은 간단히 현재 결과 개수 사용
        $total = count($tasks);

        // DTO로 변환하여 반환
        return TaskListDTO::fromEntities(
            tasks: $tasks,
            total: $total,
            limit: $limit,
            offset: $offset
        );
    }
}
