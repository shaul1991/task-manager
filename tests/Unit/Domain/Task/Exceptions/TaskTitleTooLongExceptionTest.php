<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Exceptions;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Exceptions\TaskTitleTooLongException;
use Src\Shared\Exceptions\DomainException;

final class TaskTitleTooLongExceptionTest extends TestCase
{
    public function test_exception_has_correct_message(): void
    {
        // Given
        $maxLength = 255;
        $actualLength = 300;

        // When
        $exception = new TaskTitleTooLongException($maxLength, $actualLength);

        // Then
        $this->assertEquals('Task title cannot exceed 255 characters', $exception->getMessage());
    }

    public function test_exception_has_correct_status_code(): void
    {
        // When
        $exception = new TaskTitleTooLongException(255, 300);

        // Then
        $this->assertEquals(422, $exception->getStatusCode());
    }

    public function test_exception_has_correct_error_code(): void
    {
        // When
        $exception = new TaskTitleTooLongException(255, 300);

        // Then
        $this->assertEquals('TASK_VALIDATION_002', $exception->getErrorCode());
    }

    public function test_exception_includes_context(): void
    {
        // Given
        $maxLength = 255;
        $actualLength = 300;

        // When
        $exception = new TaskTitleTooLongException($maxLength, $actualLength);

        // Then
        $context = $exception->getContext();
        $this->assertEquals($maxLength, $context['max_length']);
        $this->assertEquals($actualLength, $context['actual_length']);
    }

    public function test_exception_is_instance_of_domain_exception(): void
    {
        // When
        $exception = new TaskTitleTooLongException(255, 300);

        // Then
        $this->assertInstanceOf(DomainException::class, $exception);
    }
}
