<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Throwable;

/**
 * 인증 실패 예외 (401 Unauthorized)
 *
 * 인증이 필요하거나, 인증 정보가 유효하지 않은 경우
 */
final class UnauthorizedException extends DomainException
{
    public function __construct(
        string $message = 'Unauthorized',
        ?string $errorCode = 'UNAUTHORIZED',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            statusCode: 401,
            errorCode: $errorCode,
            context: $context,
            previous: $previous
        );
    }
}
