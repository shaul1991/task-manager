<?php

declare(strict_types=1);

namespace Src\Application\TaskList\DTOs;

/**
 * TaskList List Data Transfer Object
 *
 * TaskList 목록을 외부 레이어로 전달하기 위한 DTO
 */
final readonly class TaskListListDTO
{
    /**
     * @param array<TaskListDTO> $taskLists TaskList DTO 배열
     * @param int $total 전체 TaskList 개수
     */
    public function __construct(
        public array $taskLists,
        public int $total,
    ) {
    }
}
