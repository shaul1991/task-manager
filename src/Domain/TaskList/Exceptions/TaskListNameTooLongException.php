<?php

declare(strict_types=1);

namespace Src\Domain\TaskList\Exceptions;

use DomainException;

final class TaskListNameTooLongException extends DomainException
{
    public function __construct(
        private readonly int $maxLength,
        private readonly int $actualLength
    ) {
        parent::__construct(
            sprintf(
                'TaskList name must not exceed %d characters (got %d)',
                $maxLength,
                $actualLength
            )
        );
    }

    public function getStatusCode(): int
    {
        return 422;
    }

    public function getErrorCode(): string
    {
        return 'TASK_LIST_NAME_TOO_LONG';
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function getActualLength(): int
    {
        return $this->actualLength;
    }
}
