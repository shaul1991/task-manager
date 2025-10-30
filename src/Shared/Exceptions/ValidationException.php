<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Throwable;

/**
 * 유효성 검증 실패 예외 (400 Bad Request)
 *
 * 입력 데이터가 유효성 검증 규칙을 통과하지 못한 경우
 */
final class ValidationException extends DomainException
{
    /**
     * @param string $message 에러 메시지
     * @param array<string, array<string>> $errors 필드별 유효성 에러 목록
     * @param string|null $errorCode 에러 코드
     * @param Throwable|null $previous 이전 예외
     */
    public function __construct(
        string $message = 'Validation failed',
        array $errors = [],
        ?string $errorCode = 'VALIDATION_ERROR',
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            statusCode: 400,
            errorCode: $errorCode,
            context: ['errors' => $errors],
            previous: $previous
        );
    }

    /**
     * 필드별 유효성 에러 목록 반환
     *
     * @return array<string, array<string>>
     */
    public function getErrors(): array
    {
        return $this->context['errors'] ?? [];
    }
}
