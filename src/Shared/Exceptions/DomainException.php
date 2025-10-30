<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Throwable;

/**
 * 도메인 레벨 예외 (4xx 에러)
 *
 * 비즈니스 로직 위반, 유효하지 않은 입력, 리소스 미발견 등
 * 클라이언트 측 에러를 나타냅니다.
 */
abstract class DomainException extends BaseException
{
    public function __construct(
        string $message,
        int $statusCode = 400,
        ?string $errorCode = null,
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $errorCode, $context, $previous);
    }
}
