<?php

declare(strict_types=1);

namespace Src\Application\Task\DTOs;

/**
 * Create Task DTO
 *
 * Task 생성 요청 데이터
 */
final readonly class CreateTaskDTO
{
    public function __construct(
        public string $title,
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
            title: $data['title'],
            description: $data['description'] ?? null,
            groupId: $data['group_id'] ?? null,
        );
    }
}
