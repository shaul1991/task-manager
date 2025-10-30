<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Exceptions;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Exceptions\InvalidCompletedDateTimeException;
use Src\Shared\Exceptions\DomainException;

final class InvalidCompletedDateTimeExceptionTest extends TestCase
{
    public function test_예외가_올바른_메시지를_가짐(): void
    {
        // Given
        $invalidDateTime = '2024/10/30 14:30:00';

        // When
        $exception = new InvalidCompletedDateTimeException($invalidDateTime);

        // Then
        $this->assertEquals('Invalid datetime format. Expected: Y-m-d H:i:s', $exception->getMessage());
    }

    public function test_예외가_올바른_상태_코드를_가짐(): void
    {
        // When
        $exception = new InvalidCompletedDateTimeException('invalid');

        // Then
        $this->assertEquals(422, $exception->getStatusCode());
    }

    public function test_예외가_올바른_에러_코드를_가짐(): void
    {
        // When
        $exception = new InvalidCompletedDateTimeException('invalid');

        // Then
        $this->assertEquals('TASK_VALIDATION_003', $exception->getErrorCode());
    }

    public function test_예외가_컨텍스트를_포함함(): void
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

    public function test_예외가_도메인_예외의_인스턴스임(): void
    {
        // When
        $exception = new InvalidCompletedDateTimeException('invalid');

        // Then
        $this->assertInstanceOf(DomainException::class, $exception);
    }
}
