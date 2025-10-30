<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\ValueObjects;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\ValueObjects\TaskTitle;
use Src\Domain\Task\Exceptions\InvalidTaskTitleException;
use Src\Domain\Task\Exceptions\TaskTitleTooLongException;

final class TaskTitleTest extends TestCase
{
    public function test_create_with_valid_title(): void
    {
        // Given
        $titleString = 'Buy groceries';

        // When
        $title = new TaskTitle($titleString);

        // Then
        $this->assertEquals($titleString, $title->value());
    }

    public function test_create_with_max_length_title(): void
    {
        // Given
        $maxLengthTitle = str_repeat('a', 255);

        // When
        $title = new TaskTitle($maxLengthTitle);

        // Then
        $this->assertEquals($maxLengthTitle, $title->value());
    }

    public function test_empty_string_throws_exception(): void
    {
        // Then
        $this->expectException(InvalidTaskTitleException::class);
        $this->expectExceptionMessage('Task title cannot be empty');

        // When
        new TaskTitle('');
    }

    public function test_title_exceeding_max_length_throws_exception(): void
    {
        // Given
        $longTitle = str_repeat('a', 256);

        // Then
        $this->expectException(TaskTitleTooLongException::class);

        // When
        new TaskTitle($longTitle);
    }

    public function test_multibyte_length_validation(): void
    {
        // Given - 한글은 1글자가 3바이트이지만, mb_strlen은 1로 계산
        $koreanTitle = str_repeat('가', 255); // 255자

        // When
        $title = new TaskTitle($koreanTitle);

        // Then
        $this->assertEquals($koreanTitle, $title->value());

        // 256자는 예외 발생
        $this->expectException(TaskTitleTooLongException::class);
        new TaskTitle(str_repeat('가', 256));
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        // Given
        $title1 = new TaskTitle('Same title');
        $title2 = new TaskTitle('Same title');

        // When
        $result = $title1->equals($title2);

        // Then
        $this->assertTrue($result);
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        // Given
        $title1 = new TaskTitle('Title A');
        $title2 = new TaskTitle('Title B');

        // When
        $result = $title1->equals($title2);

        // Then
        $this->assertFalse($result);
    }

    public function test_to_string_returns_value(): void
    {
        // Given
        $titleString = 'String conversion test';
        $title = new TaskTitle($titleString);

        // When
        $result = (string) $title;

        // Then
        $this->assertEquals($titleString, $result);
    }
}
