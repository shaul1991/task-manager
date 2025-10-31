<?php

declare(strict_types=1);

namespace Src\Application\TaskGroup\DTOs;

use DateTimeImmutable;
use Src\Domain\TaskGroup\Entities\TaskGroup;

/**
 * TaskGroup Data Transfer Object
 *
 * TaskGroup Entity를 외부 레이어로 전달하기 위한 DTO
 */
final readonly class TaskGroupDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public int $incompleteTaskCount,
        public string $createdAt,
        public string $updatedAt,
        public ?string $deletedAt = null
    ) {
    }

    /**
     * Create DTO from Domain Entity
     *
     * @param TaskGroup $taskGroup
     * @param int $incompleteTaskCount 미완료 Task 개수 (기본값: 0)
     */
    public static function fromEntity(TaskGroup $taskGroup, int $incompleteTaskCount = 0): self
    {
        return new self(
            id: $taskGroup->id(),
            name: $taskGroup->name()->value(),
            incompleteTaskCount: $incompleteTaskCount,
            createdAt: $taskGroup->createdAt()->format(DateTimeImmutable::ATOM),
            updatedAt: $taskGroup->updatedAt()->format(DateTimeImmutable::ATOM),
            deletedAt: $taskGroup->deletedAt()?->format(DateTimeImmutable::ATOM)
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'incomplete_task_count' => $this->incompleteTaskCount,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'deleted_at' => $this->deletedAt,
        ];
    }
}
