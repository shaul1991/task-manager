<?php

declare(strict_types=1);

namespace Src\Shared\Responses;

/**
 * 성공 응답 클래스 (2xx)
 *
 * 요청이 성공적으로 처리된 경우의 응답
 */
final class SuccessResponse extends BaseResponse
{
    /**
     * @param string $message 성공 메시지
     * @param mixed $data 응답 데이터
     * @param int $statusCode HTTP 상태 코드 (기본값: 200)
     */
    public function __construct(
        string $message = 'Success',
        mixed $data = null,
        int $statusCode = 200
    ) {
        parent::__construct(
            status: 'success',
            statusCode: $statusCode,
            message: $message,
            data: $data
        );
    }

    /**
     * 생성 성공 응답 (201 Created)
     *
     * @param string $message 성공 메시지
     * @param mixed $data 생성된 리소스 데이터
     */
    public static function created(string $message = 'Created', mixed $data = null): self
    {
        return new self($message, $data, 201);
    }

    /**
     * 수정 성공 응답 (200 OK)
     *
     * @param string $message 성공 메시지
     * @param mixed $data 수정된 리소스 데이터
     */
    public static function updated(string $message = 'Updated', mixed $data = null): self
    {
        return new self($message, $data, 200);
    }

    /**
     * 삭제 성공 응답 (204 No Content)
     *
     * @param string $message 성공 메시지
     */
    public static function deleted(string $message = 'Deleted'): self
    {
        return new self($message, null, 204);
    }

    /**
     * 응답을 배열로 변환 (데이터가 없으면 data 필드 제외)
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
}
