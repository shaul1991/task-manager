<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Exceptions;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Exceptions\InvalidCompletedDateTimeException;
use Src\Shared\Exceptions\DomainException;

final class InvalidCompletedDateTimeExceptionTest extends TestCase
{
    public function test_exception_has_correct_message(): void
    {
        // Given
        $invalidDateTime = '2024/10/30 14:30:00';

        // When
        $exception = new InvalidCompletedDateTimeException($invalidDateTime);

        // Then
        $this->assertEquals('Invalid datetime format. Expected: Y-m-d H:i:s', $exception->getMessage());
    }

    public function test_exception_has_correct_status_code(): void
    {
        // When
        $exception = new InvalidCompletedDateTimeException('invalid');

        // Then
        $this->assertEquals(422, $exception->getStatusCode());
    }

    public function test_exception_has_correct_error_code(): void
    {
        // When
        $exception = new InvalidCompletedDateTimeException('invalid');

        // Then
        $this->assertEquals('TASK_VALIDATION_003', $exception->getErrorCode());
    }

    public function test_exception_includes_context(): void
    {
        // Given
        $invalidDateTime = '2024/10/30';

        // When
        $exception = new InvalidCompletedDateTimeException($invalidDateTime);

        // Then
        $context = $exception->getContext();
        $this->assertEquals($invalidDateTime, $context['invalid_datetime']);
        $this->assertEquals('Y-m-d H:i:s', $context['expected_format']);
    }

    public function test_exception_is_instance_of_domain_exception(): void
    {
        // When
        $exception = new InvalidCompletedDateTimeException('invalid');

        // Then
        $this->assertInstanceOf(DomainException::class, $exception);
    }
}
