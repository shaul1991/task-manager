<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Throwable;

/**
 * 애플리케이션 레벨 예외 (5xx 에러)
 *
 * 서버 내부 오류, 데이터베이스 연결 실패, 외부 서비스 장애 등
 * 서버 측 에러를 나타냅니다.
 */
abstract class ApplicationException extends BaseException
{
    public function __construct(
        string $message,
        int $statusCode = 500,
        ?string $errorCode = null,
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $errorCode, $context, $previous);
    }
}
