<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Exception;
use Throwable;

/**
 * 모든 애플리케이션 예외의 기반 추상 클래스
 *
 * HTTP 상태 코드, 에러 코드, 컨텍스트 정보를 포함합니다.
 */
abstract class BaseException extends Exception
{
    /**
     * @param string $message 에러 메시지
     * @param int $statusCode HTTP 상태 코드
     * @param string|null $errorCode 애플리케이션 고유 에러 코드 (예: TASK_001)
     * @param array<string, mixed> $context 추가 컨텍스트 정보
     * @param Throwable|null $previous 이전 예외
     */
    public function __construct(
        string $message,
        protected int $statusCode = 500,
        protected ?string $errorCode = null,
        protected array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * HTTP 상태 코드 반환
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * 애플리케이션 고유 에러 코드 반환
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * 컨텍스트 정보 반환
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * 예외를 배열로 변환
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'status_code' => $this->statusCode,
            'error_code' => $this->errorCode,
            'context' => $this->context,
        ];
    }
}
