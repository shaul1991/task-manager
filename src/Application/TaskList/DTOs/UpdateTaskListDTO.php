<?php

declare(strict_types=1);

namespace Src\Application\TaskList\DTOs;

/**
 * Update TaskList Data Transfer Object
 *
 * TaskList 업데이트 요청 데이터를 전달하기 위한 DTO
 */
final readonly class UpdateTaskListDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
    ) {
    }

    /**
     * 배열로부터 DTO 생성
     *
     * @param array $data 요청 데이터
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
        );
    }
}
