<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Exceptions;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Exceptions\InvalidTaskTitleException;
use Src\Shared\Exceptions\DomainException;

final class InvalidTaskTitleExceptionTest extends TestCase
{
    public function test_exception_has_correct_message(): void
    {
        // When
        $exception = new InvalidTaskTitleException();

        // Then
        $this->assertEquals('Task title cannot be empty', $exception->getMessage());
    }

    public function test_exception_has_correct_status_code(): void
    {
        // When
        $exception = new InvalidTaskTitleException();

        // Then
        $this->assertEquals(422, $exception->getStatusCode());
    }

    public function test_exception_has_correct_error_code(): void
    {
        // When
        $exception = new InvalidTaskTitleException();

        // Then
        $this->assertEquals('TASK_VALIDATION_001', $exception->getErrorCode());
    }

    public function test_exception_is_instance_of_domain_exception(): void
    {
        // When
        $exception = new InvalidTaskTitleException();

        // Then
        $this->assertInstanceOf(DomainException::class, $exception);
    }
}
