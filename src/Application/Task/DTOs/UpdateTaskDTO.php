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
        public ?int $groupId = null,
    ) {
    }

    /**
     * 배열로부터 DTO 생성
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            groupId: $data['group_id'] ?? null,
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
     * 그룹 ID 업데이트 여부
     */
    public function hasGroupIdUpdate(): bool
    {
        return $this->groupId !== null;
    }
}
