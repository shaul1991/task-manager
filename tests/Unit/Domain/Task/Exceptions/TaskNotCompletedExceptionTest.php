<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Exceptions;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Exceptions\TaskNotCompletedException;
use Src\Shared\Exceptions\DomainException;

final class TaskNotCompletedExceptionTest extends TestCase
{
    public function test_exception_has_correct_message(): void
    {
        // When
        $exception = new TaskNotCompletedException();

        // Then
        $this->assertEquals('Task is not completed yet', $exception->getMessage());
    }

    public function test_exception_has_correct_status_code(): void
    {
        // When
        $exception = new TaskNotCompletedException();

        // Then
        $this->assertEquals(400, $exception->getStatusCode());
    }

    public function test_exception_has_correct_error_code(): void
    {
        // When
        $exception = new TaskNotCompletedException();

        // Then
        $this->assertEquals('TASK_002', $exception->getErrorCode());
    }

    public function test_exception_includes_task_id_in_context(): void
    {
        // Given
        $taskId = 42;

        // When
        $exception = new TaskNotCompletedException($taskId);

        // Then
        $context = $exception->getContext();
        $this->assertEquals($taskId, $context['task_id']);
    }

    public function test_exception_is_instance_of_domain_exception(): void
    {
        // When
        $exception = new TaskNotCompletedException();

        // Then
        $this->assertInstanceOf(DomainException::class, $exception);
    }
}
