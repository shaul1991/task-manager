<?php

declare(strict_types=1);

namespace Src\Domain\TaskGroup\Entities;

use DateTimeImmutable;
use Src\Domain\TaskGroup\ValueObjects\TaskGroupName;

/**
 * TaskGroup Entity (Aggregate Root)
 *
 * TaskList들을 그룹화하는 최상위 계층
 */
final class TaskGroup
{
    private function __construct(
        private ?int $id,
        private TaskGroupName $name,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $deletedAt = null
    ) {
    }

    /**
     * Create a new TaskGroup
     */
    public static function create(
        TaskGroupName $name
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            id: null,
            name: $name,
            createdAt: $now,
            updatedAt: $now
        );
    }

    /**
     * Reconstruct TaskGroup from persistence
     */
    public static function reconstruct(
        int $id,
        TaskGroupName $name,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        ?DateTimeImmutable $deletedAt = null
    ): self {
        return new self(
            id: $id,
            name: $name,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            deletedAt: $deletedAt
        );
    }

    /**
     * Update name
     */
    public function updateName(TaskGroupName $newName): void
    {
        $this->name = $newName;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Getters
     */
    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): TaskGroupName
    {
        return $this->name;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * Check if soft deleted
     */
    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
