<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Exceptions;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use Src\Shared\Exceptions\DomainException;

final class TaskAlreadyCompletedExceptionTest extends TestCase
{
    public function test_exception_has_correct_message(): void
    {
        // When
        $exception = new TaskAlreadyCompletedException();

        // Then
        $this->assertEquals('Task is already completed', $exception->getMessage());
    }

    public function test_exception_has_correct_status_code(): void
    {
        // When
        $exception = new TaskAlreadyCompletedException();

        // Then
        $this->assertEquals(409, $exception->getStatusCode());
    }

    public function test_exception_has_correct_error_code(): void
    {
        // When
        $exception = new TaskAlreadyCompletedException();

        // Then
        $this->assertEquals('TASK_001', $exception->getErrorCode());
    }

    public function test_exception_includes_task_id_in_context(): void
    {
        // Given
        $taskId = 42;

        // When
        $exception = new TaskAlreadyCompletedException($taskId);

        // Then
        $context = $exception->getContext();
        $this->assertEquals($taskId, $context['task_id']);
    }

    public function test_exception_is_instance_of_domain_exception(): void
    {
        // When
        $exception = new TaskAlreadyCompletedException();

        // Then
        $this->assertInstanceOf(DomainException::class, $exception);
    }
}
