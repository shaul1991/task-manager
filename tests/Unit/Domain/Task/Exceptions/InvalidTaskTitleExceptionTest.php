<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Exceptions;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Exceptions\InvalidTaskTitleException;
use Src\Shared\Exceptions\DomainException;

final class InvalidTaskTitleExceptionTest extends TestCase
{
    public function test_예외가_올바른_메시지를_가짐(): void
    {
        // When
        $exception = new InvalidTaskTitleException();

        // Then
        $this->assertEquals('Task title cannot be empty', $exception->getMessage());
    }

    public function test_예외가_올바른_상태_코드를_가짐(): void
    {
        // When
        $exception = new InvalidTaskTitleException();

        // Then
        $this->assertEquals(422, $exception->getStatusCode());
    }

    public function test_예외가_올바른_에러_코드를_가짐(): void
    {
        // When
        $exception = new InvalidTaskTitleException();

        // Then
        $this->assertEquals('TASK_VALIDATION_001', $exception->getErrorCode());
    }

    public function test_예외가_도메인_예외의_인스턴스임(): void
    {
        // When
        $exception = new InvalidTaskTitleException();

        // Then
        $this->assertInstanceOf(DomainException::class, $exception);
    }
}
