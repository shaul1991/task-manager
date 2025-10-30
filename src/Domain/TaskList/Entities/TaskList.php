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
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    public static function create(
        TaskListName $name,
        TaskListDescription $description
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            id: null,
            name: $name,
            description: $description,
            createdAt: $now,
            updatedAt: $now
        );
    }

    public static function reconstruct(
        int $id,
        TaskListName $name,
        TaskListDescription $description,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            id: $id,
            name: $name,
            description: $description,
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
