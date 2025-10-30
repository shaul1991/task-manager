<?php

declare(strict_types=1);

namespace Src\Domain\Task\ValueObjects;

use DateTimeImmutable;
use DateTime;
use InvalidArgumentException;

final readonly class CompletedDateTime
{
    public function __construct(
        private DateTimeImmutable $value
    ) {
    }

    public static function now(): self
    {
        return new self(new DateTimeImmutable());
    }

    public static function fromString(string $datetime): self
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $datetime);

        if ($date === false) {
            throw new InvalidArgumentException(
                'Invalid datetime format. Expected: Y-m-d H:i:s'
            );
        }

        return new self($date);
    }

    public static function fromDateTime(DateTime|DateTimeImmutable $datetime): self
    {
        if ($datetime instanceof DateTime) {
            $datetime = DateTimeImmutable::createFromMutable($datetime);
        }

        return new self($datetime);
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public function toDateTime(): DateTime
    {
        return DateTime::createFromImmutable($this->value);
    }

    public function toString(): string
    {
        return $this->value->format('Y-m-d H:i:s');
    }

    public function equals(?self $other): bool
    {
        if ($other === null) {
            return false;
        }

        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
