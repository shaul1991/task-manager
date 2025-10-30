<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Exceptions;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Exceptions\TaskNotCompletedException;
use Src\Shared\Exceptions\DomainException;

final class TaskNotCompletedExceptionTest extends TestCase
{
    public function test_예외가_올바른_메시지를_가짐(): void
    {
        // When
        $exception = new TaskNotCompletedException();

        // Then
        $this->assertEquals('Task is not completed yet', $exception->getMessage());
    }

    public function test_예외가_올바른_상태_코드를_가짐(): void
    {
        // When
        $exception = new TaskNotCompletedException();

        // Then
        $this->assertEquals(400, $exception->getStatusCode());
    }

    public function test_예외가_올바른_에러_코드를_가짐(): void
    {
        // When
        $exception = new TaskNotCompletedException();

        // Then
        $this->assertEquals('TASK_002', $exception->getErrorCode());
    }

    public function test_예외가_컨텍스트에_작업_ID를_포함함(): void
    {
        // Given
        $taskId = 42;

        // When
        $exception = new TaskNotCompletedException($taskId);

        // Then
        $context = $exception->getContext();
        $this->assertEquals($taskId, $context['task_id']);
    }

    public function test_예외가_도메인_예외의_인스턴스임(): void
    {
        // When
        $exception = new TaskNotCompletedException();

        // Then
        $this->assertInstanceOf(DomainException::class, $exception);
    }
}
