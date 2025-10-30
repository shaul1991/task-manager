<?php

declare(strict_types=1);

namespace Src\Shared\Responses;

use Src\Shared\Exceptions\BaseException;

/**
 * Response 객체 생성을 위한 팩토리 클래스
 *
 * 편리한 정적 메서드를 통해 응답 객체를 생성합니다.
 */
final class ResponseFactory
{
    /**
     * 성공 응답 생성 (200 OK)
     *
     * @param string $message 성공 메시지
     * @param mixed $data 응답 데이터
     */
    public static function success(string $message = 'Success', mixed $data = null): SuccessResponse
    {
        return new SuccessResponse($message, $data, 200);
    }

    /**
     * 생성 성공 응답 (201 Created)
     *
     * @param string $message 성공 메시지
     * @param mixed $data 생성된 리소스 데이터
     */
    public static function created(string $message = 'Created', mixed $data = null): SuccessResponse
    {
        return SuccessResponse::created($message, $data);
    }

    /**
     * 수정 성공 응답 (200 OK)
     *
     * @param string $message 성공 메시지
     * @param mixed $data 수정된 리소스 데이터
     */
    public static function updated(string $message = 'Updated', mixed $data = null): SuccessResponse
    {
        return SuccessResponse::updated($message, $data);
    }

    /**
     * 삭제 성공 응답 (204 No Content)
     *
     * @param string $message 성공 메시지
     */
    public static function deleted(string $message = 'Deleted'): SuccessResponse
    {
        return SuccessResponse::deleted($message);
    }

    /**
     * 에러 응답 생성
     *
     * @param string $message 에러 메시지
     * @param int $statusCode HTTP 상태 코드
     * @param string|null $errorCode 애플리케이션 고유 에러 코드
     * @param array<string, mixed> $errors 상세 에러 정보
     */
    public static function error(
        string $message = 'Error',
        int $statusCode = 500,
        ?string $errorCode = null,
        array $errors = []
    ): ErrorResponse {
        return new ErrorResponse($message, $statusCode, $errorCode, $errors);
    }

    /**
     * BaseException으로부터 에러 응답 생성
     */
    public static function fromException(BaseException $exception): ErrorResponse
    {
        return ErrorResponse::fromException($exception);
    }

    /**
     * 유효성 검증 실패 응답 (400 Bad Request)
     *
     * @param string $message 에러 메시지
     * @param array<string, array<string>> $errors 필드별 유효성 에러
     */
    public static function validationError(
        string $message = 'Validation failed',
        array $errors = []
    ): ErrorResponse {
        return new ErrorResponse(
            message: $message,
            statusCode: 400,
            errorCode: 'VALIDATION_ERROR',
            errors: ['errors' => $errors]
        );
    }

    /**
     * 인증 실패 응답 (401 Unauthorized)
     *
     * @param string $message 에러 메시지
     */
    public static function unauthorized(string $message = 'Unauthorized'): ErrorResponse
    {
        return new ErrorResponse(
            message: $message,
            statusCode: 401,
            errorCode: 'UNAUTHORIZED'
        );
    }

    /**
     * 권한 없음 응답 (403 Forbidden)
     *
     * @param string $message 에러 메시지
     */
    public static function forbidden(string $message = 'Forbidden'): ErrorResponse
    {
        return new ErrorResponse(
            message: $message,
            statusCode: 403,
            errorCode: 'FORBIDDEN'
        );
    }

    /**
     * 리소스 미발견 응답 (404 Not Found)
     *
     * @param string $message 에러 메시지
     */
    public static function notFound(string $message = 'Resource not found'): ErrorResponse
    {
        return new ErrorResponse(
            message: $message,
            statusCode: 404,
            errorCode: 'NOT_FOUND'
        );
    }

    /**
     * 서버 에러 응답 (500 Internal Server Error)
     *
     * @param string $message 에러 메시지
     */
    public static function serverError(string $message = 'Internal server error'): ErrorResponse
    {
        return new ErrorResponse(
            message: $message,
            statusCode: 500,
            errorCode: 'SERVER_ERROR'
        );
    }
}
