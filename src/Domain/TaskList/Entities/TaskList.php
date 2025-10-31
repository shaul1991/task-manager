<?php

declare(strict_types=1);

namespace Src\Domain\TaskList\Entities;

use DateTimeImmutable;
use Src\Domain\TaskList\ValueObjects\TaskListName;
use Src\Domain\TaskList\ValueObjects\TaskListDescription;

final class TaskList
{
    private function __construct(
        private ?int $id,
        private TaskListName $name,
        private TaskListDescription $description,
        private ?int $taskGroupId,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    public static function create(
        TaskListName $name,
        TaskListDescription $description,
        ?int $taskGroupId = null
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            id: null,
            name: $name,
            description: $description,
            taskGroupId: $taskGroupId,
            createdAt: $now,
            updatedAt: $now
        );
    }

    public static function reconstruct(
        int $id,
        TaskListName $name,
        TaskListDescription $description,
        ?int $taskGroupId,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            id: $id,
            name: $name,
            description: $description,
            taskGroupId: $taskGroupId,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    // Getters
    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): TaskListName
    {
        return $this->name;
    }

    public function description(): TaskListDescription
    {
        return $this->description;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function taskGroupId(): ?int
    {
        return $this->taskGroupId;
    }

    // 이름 수정
    public function updateName(TaskListName $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable();
    }

    // 설명 수정
    public function updateDescription(TaskListDescription $description): void
    {
        $this->description = $description;
        $this->updatedAt = new DateTimeImmutable();
    }
}
