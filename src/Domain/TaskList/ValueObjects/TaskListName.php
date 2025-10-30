<?php

declare(strict_types=1);

namespace Src\Domain\TaskList\ValueObjects;

use Src\Domain\TaskList\Exceptions\InvalidTaskListNameException;
use Src\Domain\TaskList\Exceptions\TaskListNameTooLongException;

final readonly class TaskListName
{
    private const MAX_LENGTH = 255;

    public function __construct(
        private string $value
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        $trimmed = trim($this->value);

        if ($trimmed === '') {
            throw new InvalidTaskListNameException();
        }

        $actualLength = mb_strlen($trimmed);
        if ($actualLength > self::MAX_LENGTH) {
            throw new TaskListNameTooLongException(self::MAX_LENGTH, $actualLength);
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
