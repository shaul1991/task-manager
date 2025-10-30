<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\ValueObjects;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\ValueObjects\TaskDescription;

final class TaskDescriptionTest extends TestCase
{
    public function test_유효한_설명으로_생성(): void
    {
        // Given
        $descriptionString = 'This is a detailed task description';

        // When
        $description = new TaskDescription($descriptionString);

        // Then
        $this->assertEquals($descriptionString, $description->value());
        $this->assertFalse($description->isEmpty());
    }

    public function test_널_설명으로_생성(): void
    {
        // When
        $description = new TaskDescription(null);

        // Then
        $this->assertNull($description->value());
        $this->assertTrue($description->isEmpty());
    }

    public function test_빈_문자열이면_isEmpty_참_반환(): void
    {
        // Given
        $description = new TaskDescription('');

        // When & Then
        $this->assertTrue($description->isEmpty());
    }

    public function test_공백_문자이면_isEmpty_참_반환(): void
    {
        // Given
        $description = new TaskDescription('   ');

        // When & Then
        $this->assertTrue($description->isEmpty());
    }

    public function test_유효한_내용이면_isEmpty_거짓_반환(): void
    {
        // Given
        $description = new TaskDescription('Valid content');

        // When & Then
        $this->assertFalse($description->isEmpty());
    }

    public function test_같은_값이면_동등성_참_반환(): void
    {
        // Given
        $desc1 = new TaskDescription('Same description');
        $desc2 = new TaskDescription('Same description');

        // When
        $result = $desc1->equals($desc2);

        // Then
        $this->assertTrue($result);
    }

    public function test_둘_다_널이면_동등성_참_반환(): void
    {
        // Given
        $desc1 = new TaskDescription(null);
        $desc2 = new TaskDescription(null);

        // When
        $result = $desc1->equals($desc2);

        // Then
        $this->assertTrue($result);
    }

    public function test_널이면_문자열_변환_시_빈_문자열_반환(): void
    {
        // Given
        $description = new TaskDescription(null);

        // When
        $result = (string) $description;

        // Then
        $this->assertEquals('', $result);
    }
}
