<?php

declare(strict_types=1);

namespace Src\Domain\TaskList\Exceptions;

use DomainException;

final class InvalidTaskListNameException extends DomainException
{
    public function __construct()
    {
        parent::__construct('TaskList name cannot be empty');
    }

    public function getStatusCode(): int
    {
        return 422;
    }

    public function getErrorCode(): string
    {
        return 'INVALID_TASK_LIST_NAME';
    }
}
