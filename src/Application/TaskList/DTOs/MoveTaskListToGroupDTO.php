<?php

declare(strict_types=1);

namespace Src\Application\TaskList\DTOs;

/**
 * MoveTaskListToGroup DTO
 *
 * TaskList를 다른 TaskGroup으로 이동시키기 위한 DTO
 */
final readonly class MoveTaskListToGroupDTO
{
    /**
     * @param int $taskListId
     * @param int|null $taskGroupId null이면 ungrouped로 이동
     * @param int $order 새로운 순서
     */
    public function __construct(
        public int $taskListId,
        public ?int $taskGroupId,
        public int $order
    ) {
    }

    /**
     * Create from array
     *
     * @param array{task_list_id: int, task_group_id: int|null, order: int} $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            taskListId: $data['task_list_id'],
            taskGroupId: $data['task_group_id'] ?? null,
            order: $data['order']
        );
    }
}
