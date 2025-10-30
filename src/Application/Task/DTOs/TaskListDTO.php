<?php

declare(strict_types=1);

namespace Src\Application\Task\DTOs;

/**
 * Task List DTO
 *
 * Task 목록 응답 데이터
 */
final readonly class TaskListDTO
{
    /**
     * @param array<TaskDTO> $tasks
     */
    public function __construct(
        public array $tasks,
        public int $total,
        public int $limit,
        public int $offset,
    ) {
    }

    /**
     * Task 엔티티 배열로부터 TaskListDTO 생성
     *
     * @param array<\Src\Domain\Task\Entities\Task> $tasks
     */
    public static function fromEntities(
        array $tasks,
        int $total,
        int $limit,
        int $offset
    ): self {
        $taskDTOs = array_map(
            fn($task) => TaskDTO::fromEntity($task),
            $tasks
        );

        return new self(
            tasks: $taskDTOs,
            total: $total,
            limit: $limit,
            offset: $offset,
        );
    }

    /**
     * DTO를 배열로 변환
     */
    public function toArray(): array
    {
        return [
            'tasks' => array_map(fn(TaskDTO $dto) => $dto->toArray(), $this->tasks),
            'total' => $this->total,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];
    }
}
