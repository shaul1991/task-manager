<?php

declare(strict_types=1);

namespace Src\Domain\Task\Exceptions;

use Src\Shared\Exceptions\DomainException;
use Throwable;

/**
 * 완료 일시 형식 오류 예외 (422 Unprocessable Entity)
 *
 * 완료 일시의 형식이 유효하지 않은 경우
 */
final class InvalidCompletedDateTimeException extends DomainException
{
    public function __construct(
        string $invalidDateTime,
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: 'Invalid datetime format. Expected: Y-m-d H:i:s',
            statusCode: 422,
            errorCode: 'TASK_VALIDATION_003',
            context: array_merge([
                'invalid_datetime' => $invalidDateTime,
                'expected_format' => 'Y-m-d H:i:s',
            ], $context),
            previous: $previous
        );
    }
}
