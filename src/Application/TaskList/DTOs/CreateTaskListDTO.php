<?php

declare(strict_types=1);

namespace Src\Application\TaskList\DTOs;

/**
 * Create TaskList DTO
 *
 * TaskList 생성 요청 데이터
 */
final readonly class CreateTaskListDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {
    }

    /**
     * 배열로부터 DTO 생성
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
        );
    }
}
