<?php

declare(strict_types=1);

namespace Src\Domain\Task\ValueObjects;

final readonly class TaskDescription
{
    public function __construct(
        private ?string $value
    ) {
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === null || trim($this->value) === '';
    }

    public function equals(?self $other): bool
    {
        if ($other === null) {
            return $this->value === null;
        }

        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
