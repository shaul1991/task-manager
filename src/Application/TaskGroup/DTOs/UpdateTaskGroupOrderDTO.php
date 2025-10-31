<?php

declare(strict_types=1);

namespace Src\Application\TaskGroup\DTOs;

/**
 * UpdateTaskGroupOrder DTO
 *
 * TaskGroup의 순서를 변경하기 위한 DTO
 */
final readonly class UpdateTaskGroupOrderDTO
{
    /**
     * @param array<int, int> $orderMap [taskGroupId => order]
     */
    public function __construct(
        public array $orderMap
    ) {
    }

    /**
     * Create from array
     *
     * @param array<array{id: int, order: int}> $items
     * @return self
     */
    public static function fromArray(array $items): self
    {
        $orderMap = [];
        foreach ($items as $item) {
            $orderMap[$item['id']] = $item['order'];
        }

        return new self($orderMap);
    }
}
