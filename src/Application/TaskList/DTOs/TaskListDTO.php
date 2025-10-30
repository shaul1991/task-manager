<?php

declare(strict_types=1);

namespace Src\Application\TaskList\DTOs;

use Src\Domain\TaskList\Entities\TaskList;

/**
 * TaskList Data Transfer Object
 *
 * TaskList Entity를 외부 레이어로 전달하기 위한 DTO
 */
final readonly class TaskListDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    /**
     * TaskList Entity로부터 TaskListDTO 생성
     */
    public static function fromEntity(TaskList $taskList): self
    {
        return new self(
            id: $taskList->id(),
            name: $taskList->name()->value(),
            description: $taskList->description()->value(),
            createdAt: $taskList->createdAt()->format('Y-m-d H:i:s'),
            updatedAt: $taskList->updatedAt()->format('Y-m-d H:i:s'),
        );
    }

    /**
     * DTO를 배열로 변환
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
