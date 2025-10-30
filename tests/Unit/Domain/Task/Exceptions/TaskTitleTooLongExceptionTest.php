<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Exceptions;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Exceptions\TaskTitleTooLongException;
use Src\Shared\Exceptions\DomainException;

final class TaskTitleTooLongExceptionTest extends TestCase
{
    public function test_예외가_올바른_메시지를_가짐(): void
    {
        // Given
        $maxLength = 255;
        $actualLength = 300;

        // When
        $exception = new TaskTitleTooLongException($maxLength, $actualLength);

        // Then
        $this->assertEquals('Task title cannot exceed 255 characters', $exception->getMessage());
    }

    public function test_예외가_올바른_상태_코드를_가짐(): void
    {
        // When
        $exception = new TaskTitleTooLongException(255, 300);

        // Then
        $this->assertEquals(422, $exception->getStatusCode());
    }

    public function test_예외가_올바른_에러_코드를_가짐(): void
    {
        // When
        $exception = new TaskTitleTooLongException(255, 300);

        // Then
        $this->assertEquals('TASK_VALIDATION_002', $exception->getErrorCode());
    }

    public function test_예외가_컨텍스트를_포함함(): void
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

    public function test_예외가_도메인_예외의_인스턴스임(): void
    {
        // When
        $exception = new TaskTitleTooLongException(255, 300);

        // Then
        $this->assertInstanceOf(DomainException::class, $exception);
    }
}
