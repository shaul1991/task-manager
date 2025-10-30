<?php

declare(strict_types=1);

namespace Src\Shared\Responses;

/**
 * 모든 응답의 기반 추상 클래스
 *
 * 일관된 응답 구조를 제공합니다.
 */
abstract class BaseResponse
{
    /**
     * @param string $status 응답 상태 (success | error)
     * @param int $statusCode HTTP 상태 코드
     * @param string $message 응답 메시지
     * @param mixed $data 응답 데이터
     */
    public function __construct(
        protected string $status,
        protected int $statusCode,
        protected string $message,
        protected mixed $data = null
    ) {
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

        if ($this->data !== null) {
            $response['data'] = $this->data;
        }

        return $response;
    }

    /**
     * 응답을 JSON으로 변환
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    /**
     * 응답 상태 반환
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * HTTP 상태 코드 반환
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * 응답 메시지 반환
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * 응답 데이터 반환
     */
    public function getData(): mixed
    {
        return $this->data;
    }
}
