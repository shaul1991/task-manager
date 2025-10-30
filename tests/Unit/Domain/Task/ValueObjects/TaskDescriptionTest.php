<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\ValueObjects;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\ValueObjects\TaskDescription;

final class TaskDescriptionTest extends TestCase
{
    public function test_create_with_valid_description(): void
    {
        // Given
        $descriptionString = 'This is a detailed task description';

        // When
        $description = new TaskDescription($descriptionString);

        // Then
        $this->assertEquals($descriptionString, $description->value());
        $this->assertFalse($description->isEmpty());
    }

    public function test_create_with_null_description(): void
    {
        // When
        $description = new TaskDescription(null);

        // Then
        $this->assertNull($description->value());
        $this->assertTrue($description->isEmpty());
    }

    public function test_is_empty_returns_true_for_empty_string(): void
    {
        // Given
        $description = new TaskDescription('');

        // When & Then
        $this->assertTrue($description->isEmpty());
    }

    public function test_is_empty_returns_true_for_whitespace(): void
    {
        // Given
        $description = new TaskDescription('   ');

        // When & Then
        $this->assertTrue($description->isEmpty());
    }

    public function test_is_empty_returns_false_for_valid_content(): void
    {
        // Given
        $description = new TaskDescription('Valid content');

        // When & Then
        $this->assertFalse($description->isEmpty());
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        // Given
        $desc1 = new TaskDescription('Same description');
        $desc2 = new TaskDescription('Same description');

        // When
        $result = $desc1->equals($desc2);

        // Then
        $this->assertTrue($result);
    }

    public function test_equals_returns_true_for_both_null(): void
    {
        // Given
        $desc1 = new TaskDescription(null);
        $desc2 = new TaskDescription(null);

        // When
        $result = $desc1->equals($desc2);

        // Then
        $this->assertTrue($result);
    }

    public function test_to_string_returns_empty_for_null(): void
    {
        // Given
        $description = new TaskDescription(null);

        // When
        $result = (string) $description;

        // Then
        $this->assertEquals('', $result);
    }
}
