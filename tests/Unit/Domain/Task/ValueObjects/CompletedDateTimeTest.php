<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\ValueObjects;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Src\Domain\Task\ValueObjects\CompletedDateTime;
use Src\Domain\Task\Exceptions\InvalidCompletedDateTimeException;

final class CompletedDateTimeTest extends TestCase
{
    public function test_now는_현재_일시_생성(): void
    {
        // Given
        $before = new DateTimeImmutable();

        // When
        $completedDateTime = CompletedDateTime::now();
        $after = new DateTimeImmutable();

        // Then
        $this->assertInstanceOf(CompletedDateTime::class, $completedDateTime);
        $value = $completedDateTime->value();
        $this->assertGreaterThanOrEqual($before->getTimestamp(), $value->getTimestamp());
        $this->assertLessThanOrEqual($after->getTimestamp(), $value->getTimestamp());
    }

    public function test_유효한_형식_문자열로_생성(): void
    {
        // Given
        $dateString = '2024-10-30 14:30:00';

        // When
        $completedDateTime = CompletedDateTime::fromString($dateString);

        // Then
        $this->assertEquals($dateString, $completedDateTime->toString());
    }

    public function test_잘못된_형식_문자열은_예외_발생(): void
    {
        // Given
        $invalidDateString = '2024/10/30 14:30:00'; // 잘못된 형식

        // Then
        $this->expectException(InvalidCompletedDateTimeException::class);
        $this->expectExceptionMessage('Invalid datetime format. Expected: Y-m-d H:i:s');

        // When
        CompletedDateTime::fromString($invalidDateString);
    }

    public function test_DateTime_객체로_생성(): void
    {
        // Given
        $dateTime = new DateTime('2024-10-30 14:30:00');

        // When
        $completedDateTime = CompletedDateTime::fromDateTime($dateTime);

        // Then
        $this->assertEquals('2024-10-30 14:30:00', $completedDateTime->toString());
    }

    public function test_DateTimeImmutable_객체로_생성(): void
    {
        // Given
        $dateTime = new DateTimeImmutable('2024-10-30 14:30:00');

        // When
        $completedDateTime = CompletedDateTime::fromDateTime($dateTime);

        // Then
        $this->assertEquals($dateTime, $completedDateTime->value());
    }

    public function test_toDateTime은_가변_DateTime으로_변환(): void
    {
        // Given
        $completedDateTime = CompletedDateTime::fromString('2024-10-30 14:30:00');

        // When
        $mutableDateTime = $completedDateTime->toDateTime();

        // Then
        $this->assertInstanceOf(DateTime::class, $mutableDateTime);
        $this->assertEquals('2024-10-30 14:30:00', $mutableDateTime->format('Y-m-d H:i:s'));
    }

    public function test_toString은_올바른_형식으로_반환(): void
    {
        // Given
        $completedDateTime = CompletedDateTime::fromString('2024-10-30 14:30:00');

        // When
        $result = $completedDateTime->toString();

        // Then
        $this->assertEquals('2024-10-30 14:30:00', $result);
    }

    public function test_같은_타임스탬프면_동등성_참_반환(): void
    {
        // Given
        $datetime1 = CompletedDateTime::fromString('2024-10-30 14:30:00');
        $datetime2 = CompletedDateTime::fromString('2024-10-30 14:30:00');

        // When
        $result = $datetime1->equals($datetime2);

        // Then
        $this->assertTrue($result);
    }

    public function test_널과_비교_시_동등성_거짓_반환(): void
    {
        // Given
        $datetime = CompletedDateTime::now();

        // When
        $result = $datetime->equals(null);

        // Then
        $this->assertFalse($result);
    }
}
