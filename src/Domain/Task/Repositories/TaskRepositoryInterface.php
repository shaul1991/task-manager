<?php

declare(strict_types=1);

namespace Src\Domain\Task\Repositories;

use Src\Domain\Task\Entities\Task;

/**
 * Task Repository Interface
 *
 * Task 엔티티의 영속성 계층 인터페이스
 * Infrastructure 레이어에서 구현됨
 */
interface TaskRepositoryInterface
{
    /**
     * Task를 저장하거나 업데이트합니다.
     *
     * @param Task $task 저장할 Task 엔티티
     * @return Task 저장된 Task 엔티티 (ID 포함)
     */
    public function save(Task $task): Task;

    /**
     * ID로 Task를 조회합니다.
     *
     * @param int $id Task ID
     * @return Task|null Task 엔티티 또는 null (없을 경우)
     */
    public function findById(int $id): ?Task;

    /**
     * 필터 조건에 맞는 Task 목록을 조회합니다.
     *
     * @param int|null $groupId 그룹 ID 필터 (null이면 필터링 안함)
     * @param bool|null $completed 완료 상태 필터 (null이면 필터링 안함)
     * @param int $limit 조회 개수 제한 (기본값: 100)
     * @param int $offset 오프셋 (기본값: 0)
     * @return array<Task> Task 엔티티 배열
     */
    public function findAll(
        ?int $groupId = null,
        ?bool $completed = null,
        int $limit = 100,
        int $offset = 0
    ): array;

    /**
     * Task를 삭제합니다.
     *
     * @param int $id 삭제할 Task ID
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Task가 존재하는지 확인합니다.
     *
     * @param int $id Task ID
     * @return bool 존재 여부
     */
    public function existsById(int $id): bool;

    /**
     * 그룹에 속한 Task 개수를 조회합니다.
     *
     * @param int $groupId 그룹 ID
     * @return int Task 개수
     */
    public function countByGroupId(int $groupId): int;

    /**
     * 완료된 Task 개수를 조회합니다.
     *
     * @param int|null $groupId 그룹 ID (null이면 전체)
     * @return int 완료된 Task 개수
     */
    public function countCompleted(?int $groupId = null): int;
}
