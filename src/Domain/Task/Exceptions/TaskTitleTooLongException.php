<?php

declare(strict_types=1);

namespace Src\Domain\Task\Exceptions;

use Src\Shared\Exceptions\DomainException;
use Throwable;

/**
 * Task 제목 길이 초과 예외 (422 Unprocessable Entity)
 *
 * Task 제목이 최대 허용 길이를 초과한 경우
 */
final class TaskTitleTooLongException extends DomainException
{
    public function __construct(
        int $maxLength,
        int $actualLength,
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: sprintf('Task title cannot exceed %d characters', $maxLength),
            statusCode: 422,
            errorCode: 'TASK_VALIDATION_002',
            context: array_merge([
                'max_length' => $maxLength,
                'actual_length' => $actualLength,
            ], $context),
            previous: $previous
        );
    }
}
