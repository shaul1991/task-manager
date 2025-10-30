<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Throwable;

/**
 * 서버 내부 에러 예외 (500 Internal Server Error)
 *
 * 예상치 못한 서버 내부 오류가 발생한 경우
 */
final class ServerException extends ApplicationException
{
    public function __construct(
        string $message = 'Internal server error',
        ?string $errorCode = 'SERVER_ERROR',
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
