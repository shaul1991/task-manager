<?php

declare(strict_types=1);

namespace Src\Domain\Task\Entities;

use DateTimeImmutable;
use Src\Domain\Task\ValueObjects\TaskTitle;
use Src\Domain\Task\ValueObjects\TaskDescription;
use Src\Domain\Task\ValueObjects\CompletedDateTime;
use Src\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use Src\Domain\Task\Exceptions\TaskNotCompletedException;

final class Task
{
    private function __construct(
        private ?int $id,
        private TaskTitle $title,
        private TaskDescription $description,
        private ?CompletedDateTime $completedDateTime,
        private ?int $groupId,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    public static function create(
        TaskTitle $title,
        TaskDescription $description,
        ?int $groupId = null
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            id: null,
            title: $title,
            description: $description,
            completedDateTime: null,
            groupId: $groupId,
            createdAt: $now,
            updatedAt: $now
        );
    }

    public static function reconstruct(
        int $id,
        TaskTitle $title,
        TaskDescription $description,
        ?CompletedDateTime $completedDateTime,
        ?int $groupId,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            id: $id,
            title: $title,
            description: $description,
            completedDateTime: $completedDateTime,
            groupId: $groupId,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    // Getters
    public function id(): ?int
    {
        return $this->id;
    }

    public function title(): TaskTitle
    {
        return $this->title;
    }

    public function description(): TaskDescription
    {
        return $this->description;
    }

    public function completedDateTime(): ?CompletedDateTime
    {
        return $this->completedDateTime;
    }

    public function groupId(): ?int
    {
        return $this->groupId;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // 완료 상태 확인
    public function isCompleted(): bool
    {
        return $this->completedDateTime !== null;
    }

    // 완료 처리
    public function complete(): void
    {
        if ($this->isCompleted()) {
            throw new TaskAlreadyCompletedException($this->id);
        }

        $this->completedDateTime = CompletedDateTime::now();
        $this->updatedAt = new DateTimeImmutable();
    }

    // 미완료 처리
    public function uncomplete(): void
    {
        if (!$this->isCompleted()) {
            throw new TaskNotCompletedException($this->id);
        }

        $this->completedDateTime = null;
        $this->updatedAt = new DateTimeImmutable();
    }

    // 제목 수정
    public function updateTitle(TaskTitle $title): void
    {
        $this->title = $title;
        $this->updatedAt = new DateTimeImmutable();
    }

    // 설명 수정
    public function updateDescription(TaskDescription $description): void
    {
        $this->description = $description;
        $this->updatedAt = new DateTimeImmutable();
    }

    // 그룹 할당/해제
    public function assignToGroup(?int $groupId): void
    {
        $this->groupId = $groupId;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function removeFromGroup(): void
    {
        $this->assignToGroup(null);
    }
}
