<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Exceptions;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use Src\Shared\Exceptions\DomainException;

final class TaskAlreadyCompletedExceptionTest extends TestCase
{
    public function test_예외가_올바른_메시지를_가짐(): void
    {
        // When
        $exception = new TaskAlreadyCompletedException();

        // Then
        $this->assertEquals('Task is already completed', $exception->getMessage());
    }

    public function test_예외가_올바른_상태_코드를_가짐(): void
    {
        // When
        $exception = new TaskAlreadyCompletedException();

        // Then
        $this->assertEquals(409, $exception->getStatusCode());
    }

    public function test_예외가_올바른_에러_코드를_가짐(): void
    {
        // When
        $exception = new TaskAlreadyCompletedException();

        // Then
        $this->assertEquals('TASK_001', $exception->getErrorCode());
    }

    public function test_예외가_컨텍스트에_작업_ID를_포함함(): void
    {
        // Given
        $taskId = 42;

        // When
        $exception = new TaskAlreadyCompletedException($taskId);

        // Then
        $context = $exception->getContext();
        $this->assertEquals($taskId, $context['task_id']);
    }

    public function test_예외가_도메인_예외의_인스턴스임(): void
    {
        // When
        $exception = new TaskAlreadyCompletedException();

        // Then
        $this->assertInstanceOf(DomainException::class, $exception);
    }
}
