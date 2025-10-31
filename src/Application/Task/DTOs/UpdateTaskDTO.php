<?php

declare(strict_types=1);

namespace Src\Application\Task\DTOs;

/**
 * Update Task DTO
 *
 * Task 수정 요청 데이터
 */
final readonly class UpdateTaskDTO
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?int $taskListId = null,
        public ?bool $completed = null,
    ) {
    }

    /**
     * 배열로부터 DTO 생성
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            description: $data['description'] ?? '',
            taskListId: $data['task_list_id'] ?? null,
            completed: isset($data['completed']) ? (bool) $data['completed'] : null,
        );
    }

    /**
     * 제목 업데이트 여부
     */
    public function hasTitleUpdate(): bool
    {
        return $this->title !== null;
    }

    /**
     * 설명 업데이트 여부
     */
    public function hasDescriptionUpdate(): bool
    {
        return $this->description !== null;
    }

    /**
     * TaskList ID 업데이트 여부
     */
    public function hasTaskListIdUpdate(): bool
    {
        return $this->taskListId !== null;
    }

    /**
     * 완료 상태 업데이트 여부
     */
    public function hasCompletedUpdate(): bool
    {
        return $this->completed !== null;
    }
}
