<?php

declare(strict_types=1);

namespace Src\Domain\Task\ValueObjects;

use InvalidArgumentException;

final readonly class TaskTitle
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
            throw new InvalidArgumentException('Task title cannot be empty');
        }

        if (mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Task title cannot exceed %d characters', self::MAX_LENGTH)
            );
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
