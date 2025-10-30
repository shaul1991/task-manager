<?php

declare(strict_types=1);

namespace Src\Domain\Task\Exceptions;

use Src\Shared\Exceptions\DomainException;
use Throwable;

/**
 * Task 제목 유효성 검증 실패 예외 (422 Unprocessable Entity)
 *
 * Task 제목이 비어있거나 유효하지 않은 경우
 */
final class InvalidTaskTitleException extends DomainException
{
    public function __construct(
        string $reason = 'Task title cannot be empty',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $reason,
            statusCode: 422,
            errorCode: 'TASK_VALIDATION_001',
            context: $context,
            previous: $previous
        );
    }
}
