<?php

declare(strict_types=1);

namespace Src\Domain\TaskGroup\Exceptions;

use Exception;

/**
 * Invalid TaskGroup Name Exception
 *
 * TaskGroup 이름이 유효하지 않을 때 발생 (빈 문자열 등)
 */
class InvalidTaskGroupNameException extends Exception
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
        return 'INVALID_TASK_GROUP_NAME';
    }
}
