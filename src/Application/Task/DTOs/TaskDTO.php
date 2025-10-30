<?php

declare(strict_types=1);

namespace Src\Application\Task\DTOs;

use Src\Domain\Task\Entities\Task;

/**
 * Task Data Transfer Object
 *
 * Task Entity를 외부 레이어로 전달하기 위한 DTO
 */
final readonly class TaskDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public ?string $completedDateTime,
        public ?int $groupId,
        public string $createdAt,
        public string $updatedAt,
        public bool $isCompleted,
    ) {
    }

    /**
     * Task Entity로부터 TaskDTO 생성
     */
    public static function fromEntity(Task $task): self
    {
        return new self(
            id: $task->id(),
            title: $task->title()->value(),
            description: $task->description()->value(),
            completedDateTime: $task->completedDateTime()?->toString(),
            groupId: $task->groupId(),
            createdAt: $task->createdAt()->format('Y-m-d H:i:s'),
            updatedAt: $task->updatedAt()->format('Y-m-d H:i:s'),
            isCompleted: $task->isCompleted(),
        );
    }

    /**
     * DTO를 배열로 변환
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'completed_datetime' => $this->completedDateTime,
            'group_id' => $this->groupId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'is_completed' => $this->isCompleted,
        ];
    }
}
