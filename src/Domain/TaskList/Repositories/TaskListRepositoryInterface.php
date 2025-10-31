<?php

declare(strict_types=1);

namespace Src\Domain\TaskList\Repositories;

use Src\Domain\TaskList\Entities\TaskList;

/**
 * TaskList Repository Interface
 *
 * TaskList 엔티티의 영속성 계층 인터페이스
 * Infrastructure 레이어에서 구현됨
 */
interface TaskListRepositoryInterface
{
    /**
     * TaskList를 저장하거나 업데이트합니다.
     *
     * @param TaskList $taskList 저장할 TaskList 엔티티
     * @return TaskList 저장된 TaskList 엔티티 (ID 포함)
     */
    public function save(TaskList $taskList): TaskList;

    /**
     * ID로 TaskList를 조회합니다.
     *
     * @param int $id TaskList ID
     * @return TaskList|null TaskList 엔티티 또는 null (없을 경우)
     */
    public function findById(int $id): ?TaskList;

    /**
     * 모든 TaskList 목록을 조회합니다.
     *
     * @param int|null $userId 사용자 ID 필터 (null이면 게스트)
     * @param int $limit 조회 개수 제한 (기본값: 100)
     * @param int $offset 오프셋 (기본값: 0)
     * @return array<TaskList> TaskList 엔티티 배열
     */
    public function findAll(
        ?int $userId = null,
        int $limit = 100,
        int $offset = 0
    ): array;

    /**
     * TaskList를 삭제합니다.
     *
     * @param int $id 삭제할 TaskList ID
     * @return void
     */
    public function delete(int $id): void;

    /**
     * TaskList가 존재하는지 확인합니다.
     *
     * @param int $id TaskList ID
     * @return bool 존재 여부
     */
    public function existsById(int $id): bool;

    /**
     * 사용자의 TaskList 개수를 조회합니다.
     *
     * @param int|null $userId 사용자 ID (null이면 게스트)
     * @return int TaskList 개수
     */
    public function countByUserId(?int $userId): int;

    /**
     * 복수 TaskList의 순서를 업데이트합니다.
     *
     * @param array<int, int> $orderMap [taskListId => order]
     * @return void
     */
    public function updateOrders(array $orderMap): void;

    /**
     * TaskList를 다른 그룹으로 이동시킵니다.
     *
     * @param int $id TaskList ID
     * @param int|null $taskGroupId 이동할 TaskGroup ID (null이면 ungrouped)
     * @param int $order 새로운 순서
     * @return void
     */
    public function moveToGroup(int $id, ?int $taskGroupId, int $order): void;
}
