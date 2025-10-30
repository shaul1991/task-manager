<?php

declare(strict_types=1);

namespace Src\Domain\Task\Exceptions;

use Src\Shared\Exceptions\DomainException;
use Throwable;

final class TaskAlreadyCompletedException extends DomainException
{
    public function __construct(?int $taskId = null, array $context = [], ?Throwable $previous = null)
    {
        $contextData = $context;
        if ($taskId !== null) {
            $contextData['task_id'] = $taskId;
        }

        parent::__construct(
            message: 'Task is already completed',
            statusCode: 409,
            errorCode: 'TASK_001',
            context: $contextData,
            previous: $previous
        );
    }
}
