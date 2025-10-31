<?php

declare(strict_types=1);

namespace Src\Domain\TaskGroup\Repositories;

use Src\Domain\TaskGroup\Entities\TaskGroup;

/**
 * TaskGroup Repository Interface
 *
 * TaskGroup의 영속성을 담당하는 Repository 인터페이스
 * Domain 레이어에서 정의하고 Infrastructure 레이어에서 구현
 */
interface TaskGroupRepositoryInterface
{
    /**
     * Save TaskGroup (create or update)
     *
     * @param TaskGroup $taskGroup
     * @return TaskGroup Saved TaskGroup with ID
     */
    public function save(TaskGroup $taskGroup): TaskGroup;

    /**
     * Find TaskGroup by ID
     *
     * @param int $id
     * @return TaskGroup|null
     */
    public function findById(int $id): ?TaskGroup;

    /**
     * Find all TaskGroups
     *
     * @param int $limit
     * @param int $offset
     * @return array<TaskGroup>
     */
    public function findAll(int $limit = 100, int $offset = 0): array;

    /**
     * Delete TaskGroup (SoftDelete)
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Check if TaskGroup exists by ID
     *
     * @param int $id
     * @return bool
     */
    public function existsById(int $id): bool;

    /**
     * Count all TaskGroups
     *
     * @return int
     */
    public function count(): int;

    /**
     * Set all TaskLists' task_group_id to NULL when TaskGroup is deleted
     *
     * @param int $taskGroupId
     * @return void
     */
    public function unassignTaskListsFromGroup(int $taskGroupId): void;
}
