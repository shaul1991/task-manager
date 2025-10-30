<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Throwable;

/**
 * 데이터베이스 에러 예외 (500 Internal Server Error)
 *
 * 데이터베이스 연결 실패, 쿼리 실패 등 데이터베이스 관련 오류
 */
final class DatabaseException extends ApplicationException
{
    public function __construct(
        string $message = 'Database error',
        ?string $errorCode = 'DATABASE_ERROR',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            statusCode: 500,
            errorCode: $errorCode,
            context: $context,
            previous: $previous
        );
    }
}
