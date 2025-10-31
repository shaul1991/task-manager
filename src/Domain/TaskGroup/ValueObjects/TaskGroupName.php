<?php

declare(strict_types=1);

namespace Src\Domain\TaskGroup\ValueObjects;

use Src\Domain\TaskGroup\Exceptions\InvalidTaskGroupNameException;
use Src\Domain\TaskGroup\Exceptions\TaskGroupNameTooLongException;

/**
 * TaskGroup Name Value Object
 *
 * TaskGroup 이름을 나타내는 불변 객체
 */
final readonly class TaskGroupName
{
    private const MAX_LENGTH = 100;

    /**
     * @param string $value TaskGroup 이름
     * @throws InvalidTaskGroupNameException 빈 문자열인 경우
     * @throws TaskGroupNameTooLongException 최대 길이 초과
     */
    public function __construct(
        private string $value
    ) {
        $this->validate();
    }

    /**
     * Validate the task group name
     *
     * @throws InvalidTaskGroupNameException
     * @throws TaskGroupNameTooLongException
     */
    private function validate(): void
    {
        if (trim($this->value) === '') {
            throw new InvalidTaskGroupNameException('TaskGroup name cannot be empty');
        }

        if (mb_strlen($this->value) > self::MAX_LENGTH) {
            throw new TaskGroupNameTooLongException(
                sprintf('TaskGroup name cannot exceed %d characters', self::MAX_LENGTH)
            );
        }
    }

    /**
     * Get the raw value
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Convert to string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Check equality with another TaskGroupName
     */
    public function equals(TaskGroupName $other): bool
    {
        return $this->value === $other->value;
    }
}
