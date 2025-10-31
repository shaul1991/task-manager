<?php

declare(strict_types=1);

namespace Src\Domain\TaskGroup\Exceptions;

use Exception;

/**
 * TaskGroup Name Too Long Exception
 *
 * TaskGroup 이름이 최대 길이를 초과할 때 발생
 */
class TaskGroupNameTooLongException extends Exception
{
    /**
     * Get HTTP status code
     */
    public function getStatusCode(): int
    {
        return 422; // Unprocessable Entity
    }

    /**
     * Get error code
     */
    public function getErrorCode(): string
    {
        return 'TASK_GROUP_NAME_TOO_LONG';
    }
}
