<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Task\UseCases;

use PHPUnit\Framework\TestCase;
use Src\Application\Task\UseCases\GetTask;
use Src\Domain\Task\Entities\Task;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Domain\Task\ValueObjects\TaskDescription;
use Src\Domain\Task\ValueObjects\TaskTitle;
use Src\Shared\Exceptions\NotFoundException;

final class GetTaskTest extends TestCase
{
    public function test_할일을_조회함(): void
    {
        // Given
        $task = Task::reconstruct(
            id: 1,
            title: new TaskTitle('Task title'),
            description: new TaskDescription('Description'),
            completedDateTime: null,
            groupId: null,
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable()
        );

        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($task);

        $useCase = new GetTask($repository);

        // When
        $result = $useCase->execute(1);

        // Then
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Task title', $result->title);
    }

    public function test_존재하지_않는_할일_조회_시_예외_발생(): void
    {
        // Given
        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $useCase = new GetTask($repository);

        // Then
        $this->expectException(NotFoundException::class);

        // When
        $useCase->execute(999);
    }
}
