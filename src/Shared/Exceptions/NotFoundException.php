<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Throwable;

/**
 * 리소스 미발견 예외 (404 Not Found)
 *
 * 요청한 리소스를 찾을 수 없는 경우
 */
final class NotFoundException extends DomainException
{
    public function __construct(
        string $message = 'Resource not found',
        ?string $errorCode = 'NOT_FOUND',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            statusCode: 404,
            errorCode: $errorCode,
            context: $context,
            previous: $previous
        );
    }

    /**
     * 특정 엔티티 타입에 대한 미발견 예외 생성
     *
     * @param string $entityType 엔티티 타입 (예: Task, Group)
     * @param int|string $id 엔티티 ID
     */
    public static function forEntity(string $entityType, int|string $id): self
    {
        return new self(
            message: "{$entityType} not found",
            errorCode: strtoupper($entityType) . '_NOT_FOUND',
            context: ['entity_type' => $entityType, 'id' => $id]
        );
    }
}
