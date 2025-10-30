<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Throwable;

/**
 * 외부 서비스 장애 예외 (503 Service Unavailable)
 *
 * 외부 API 호출 실패, 서비스 일시적 불가 등
 */
final class ExternalServiceException extends ApplicationException
{
    public function __construct(
        string $message = 'External service unavailable',
        ?string $errorCode = 'EXTERNAL_SERVICE_ERROR',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            statusCode: 503,
            errorCode: $errorCode,
            context: $context,
            previous: $previous
        );
    }
}
