<?php

declare(strict_types=1);

namespace Src\Shared\Responses;

use Src\Shared\Exceptions\BaseException;

/**
 * 에러 응답 클래스 (4xx, 5xx)
 *
 * 요청 처리 중 에러가 발생한 경우의 응답
 */
final class ErrorResponse extends BaseResponse
{
    /**
     * @param string $message 에러 메시지
     * @param int $statusCode HTTP 상태 코드 (기본값: 500)
     * @param string|null $errorCode 애플리케이션 고유 에러 코드
     * @param array<string, mixed> $errors 상세 에러 정보
     */
    public function __construct(
        string $message = 'Error',
        int $statusCode = 500,
        private ?string $errorCode = null,
        private array $errors = []
    ) {
        parent::__construct(
            status: 'error',
            statusCode: $statusCode,
            message: $message,
            data: null
        );
    }

    /**
     * BaseException으로부터 ErrorResponse 생성
     */
    public static function fromException(BaseException $exception): self
    {
        return new self(
            message: $exception->getMessage(),
            statusCode: $exception->getStatusCode(),
            errorCode: $exception->getErrorCode(),
            errors: $exception->getContext()
        );
    }

    /**
     * 에러 코드 반환
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * 상세 에러 정보 반환
     *
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * 응답을 배열로 변환
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $response = [
            'status' => $this->status,
            'status_code' => $this->statusCode,
            'message' => $this->message,
        ];

        if ($this->errorCode !== null) {
            $response['error_code'] = $this->errorCode;
        }

        if (!empty($this->errors)) {
            $response['errors'] = $this->errors;
        }

        return $response;
    }
}
