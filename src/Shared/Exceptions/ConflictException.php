<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Throwable;

/**
 * 충돌 예외 (409 Conflict)
 *
 * 요청이 현재 서버 상태와 충돌하는 경우 (예: 중복 데이터)
 */
final class ConflictException extends DomainException
{
    public function __construct(
        string $message = 'Conflict',
        ?string $errorCode = 'CONFLICT',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            statusCode: 409,
            errorCode: $errorCode,
            context: $context,
            previous: $previous
        );
    }
}
