<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Throwable;

/**
 * 권한 없음 예외 (403 Forbidden)
 *
 * 인증은 되었으나 해당 리소스에 대한 접근 권한이 없는 경우
 */
final class ForbiddenException extends DomainException
{
    public function __construct(
        string $message = 'Forbidden',
        ?string $errorCode = 'FORBIDDEN',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            statusCode: 403,
            errorCode: $errorCode,
            context: $context,
            previous: $previous
        );
    }
}
