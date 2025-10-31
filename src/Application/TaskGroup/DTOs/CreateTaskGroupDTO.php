<?php

declare(strict_types=1);

namespace Src\Application\TaskGroup\DTOs;

/**
 * Create TaskGroup DTO
 *
 * TaskGroup 생성 시 필요한 데이터
 */
final readonly class CreateTaskGroupDTO
{
    public function __construct(
        public string $name
    ) {
    }
}
