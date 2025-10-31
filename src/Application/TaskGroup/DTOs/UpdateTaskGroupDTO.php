<?php

declare(strict_types=1);

namespace Src\Application\TaskGroup\DTOs;

/**
 * Update TaskGroup DTO
 *
 * TaskGroup 업데이트 시 필요한 데이터
 */
final readonly class UpdateTaskGroupDTO
{
    public function __construct(
        public int $id,
        public string $name
    ) {
    }
}
