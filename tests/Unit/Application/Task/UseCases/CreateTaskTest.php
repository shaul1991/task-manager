<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Task\UseCases;

use PHPUnit\Framework\TestCase;
use Src\Application\Task\DTOs\CreateTaskDTO;
use Src\Application\Task\UseCases\CreateTask;
use Src\Domain\Task\Entities\Task;
use Src\Domain\Task\Exceptions\InvalidTaskTitleException;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Domain\Task\ValueObjects\TaskDescription;
use Src\Domain\Task\ValueObjects\TaskTitle;

final class CreateTaskTest extends TestCase
{
    public function test_할일을_생성함(): void
    {
        // Given
        $dto = new CreateTaskDTO(
            title: 'New task',
            description: 'Task description',
            groupId: null
        );

        $savedTask = Task::reconstruct(
            id: 1,
            title: new TaskTitle('New task'),
            description: new TaskDescription('Task description'),
            completedDateTime: null,
            groupId: null,
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable()
        );

        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('save')
            ->willReturn($savedTask);

        $useCase = new CreateTask($repository);

        // When
        $result = $useCase->execute($dto);

        // Then
        $this->assertEquals(1, $result->id);
        $this->assertEquals('New task', $result->title);
        $this->assertEquals('Task description', $result->description);
        $this->assertFalse($result->isCompleted);
    }

    public function test_빈_제목으로_생성_시_예외_발생(): void
    {
        // Given
        $dto = new CreateTaskDTO(
            title: '',
            description: 'Description',
            groupId: null
        );

        $repository = $this->createMock(TaskRepositoryInterface::class);
        $useCase = new CreateTask($repository);

        // Then
        $this->expectException(InvalidTaskTitleException::class);

        // When
        $useCase->execute($dto);
    }

    public function test_그룹_ID와_함께_할일을_생성함(): void
    {
        // Given
        $dto = new CreateTaskDTO(
            title: 'Group task',
            description: 'Description',
            groupId: 5
        );

        $savedTask = Task::reconstruct(
            id: 1,
            title: new TaskTitle('Group task'),
            description: new TaskDescription('Description'),
            completedDateTime: null,
            groupId: 5,
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable()
        );

        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('save')
            ->willReturn($savedTask);

        $useCase = new CreateTask($repository);

        // When
        $result = $useCase->execute($dto);

        // Then
        $this->assertEquals(5, $result->groupId);
    }
}
