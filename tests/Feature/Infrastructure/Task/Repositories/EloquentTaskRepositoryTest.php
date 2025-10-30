<?php

declare(strict_types=1);

namespace Tests\Feature\Infrastructure\Task\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Domain\Task\Entities\Task;
use Src\Domain\Task\ValueObjects\TaskDescription;
use Src\Domain\Task\ValueObjects\TaskTitle;
use Src\Infrastructure\Task\Repositories\EloquentTaskRepository;
use Tests\TestCase;

final class EloquentTaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentTaskRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentTaskRepository();
    }

    public function test_새로운_할일을_저장함(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('New task'),
            new TaskDescription('Task description')
        );

        // When
        $savedTask = $this->repository->save($task);

        // Then
        $this->assertNotNull($savedTask->id());
        $this->assertEquals('New task', $savedTask->title()->value());
        $this->assertEquals('Task description', $savedTask->description()->value());
        $this->assertFalse($savedTask->isCompleted());
        $this->assertDatabaseHas('tasks', [
            'id' => $savedTask->id(),
            'title' => 'New task',
        ]);
    }

    public function test_기존_할일을_업데이트함(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Original title'),
            new TaskDescription('Original description')
        );
        $savedTask = $this->repository->save($task);

        $savedTask->updateTitle(new TaskTitle('Updated title'));
        $savedTask->updateDescription(new TaskDescription('Updated description'));

        // When
        $updatedTask = $this->repository->save($savedTask);

        // Then
        $this->assertEquals($savedTask->id(), $updatedTask->id());
        $this->assertEquals('Updated title', $updatedTask->title()->value());
        $this->assertEquals('Updated description', $updatedTask->description()->value());
        $this->assertDatabaseHas('tasks', [
            'id' => $updatedTask->id(),
            'title' => 'Updated title',
            'description' => 'Updated description',
        ]);
    }

    public function test_ID로_할일을_조회함(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Find me'),
            new TaskDescription('Description')
        );
        $savedTask = $this->repository->save($task);

        // When
        $foundTask = $this->repository->findById($savedTask->id());

        // Then
        $this->assertNotNull($foundTask);
        $this->assertEquals($savedTask->id(), $foundTask->id());
        $this->assertEquals('Find me', $foundTask->title()->value());
    }

    public function test_존재하지_않는_ID_조회_시_null_반환(): void
    {
        // When
        $foundTask = $this->repository->findById(999);

        // Then
        $this->assertNull($foundTask);
    }

    public function test_전체_할일_목록을_조회함(): void
    {
        // Given
        $task1 = Task::create(new TaskTitle('Task 1'), new TaskDescription('Desc 1'));
        $task2 = Task::create(new TaskTitle('Task 2'), new TaskDescription('Desc 2'));
        $task3 = Task::create(new TaskTitle('Task 3'), new TaskDescription('Desc 3'));

        $this->repository->save($task1);
        $this->repository->save($task2);
        $this->repository->save($task3);

        // When
        $tasks = $this->repository->findAll();

        // Then
        $this->assertCount(3, $tasks);
    }

    public function test_그룹_ID로_필터링하여_조회함(): void
    {
        // Given
        $task1 = Task::create(new TaskTitle('Group 1 Task'), new TaskDescription('Desc'), 1);
        $task2 = Task::create(new TaskTitle('Group 2 Task'), new TaskDescription('Desc'), 2);
        $task3 = Task::create(new TaskTitle('Group 1 Task 2'), new TaskDescription('Desc'), 1);

        $this->repository->save($task1);
        $this->repository->save($task2);
        $this->repository->save($task3);

        // When
        $group1Tasks = $this->repository->findAll(groupId: 1);
        $group2Tasks = $this->repository->findAll(groupId: 2);

        // Then
        $this->assertCount(2, $group1Tasks);
        $this->assertCount(1, $group2Tasks);
    }

    public function test_완료_상태로_필터링하여_조회함(): void
    {
        // Given
        $task1 = Task::create(new TaskTitle('Task 1'), new TaskDescription('Desc'));
        $task2 = Task::create(new TaskTitle('Task 2'), new TaskDescription('Desc'));
        $task3 = Task::create(new TaskTitle('Task 3'), new TaskDescription('Desc'));

        $savedTask1 = $this->repository->save($task1);
        $task2->complete();
        $savedTask2 = $this->repository->save($task2);
        $task3->complete();
        $savedTask3 = $this->repository->save($task3);

        // When
        $completedTasks = $this->repository->findAll(completed: true);
        $incompleteTasks = $this->repository->findAll(completed: false);

        // Then
        $this->assertCount(2, $completedTasks);
        $this->assertCount(1, $incompleteTasks);
    }

    public function test_할일을_삭제함(): void
    {
        // Given
        $task = Task::create(new TaskTitle('To be deleted'), new TaskDescription('Desc'));
        $savedTask = $this->repository->save($task);
        $taskId = $savedTask->id();

        // When
        $this->repository->delete($taskId);

        // Then
        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
        $this->assertFalse($this->repository->existsById($taskId));
    }

    public function test_할일_존재_여부를_확인함(): void
    {
        // Given
        $task = Task::create(new TaskTitle('Exists'), new TaskDescription('Desc'));
        $savedTask = $this->repository->save($task);

        // When & Then
        $this->assertTrue($this->repository->existsById($savedTask->id()));
        $this->assertFalse($this->repository->existsById(999));
    }

    public function test_그룹별_할일_개수를_조회함(): void
    {
        // Given
        $task1 = Task::create(new TaskTitle('Task 1'), new TaskDescription('Desc'), 1);
        $task2 = Task::create(new TaskTitle('Task 2'), new TaskDescription('Desc'), 1);
        $task3 = Task::create(new TaskTitle('Task 3'), new TaskDescription('Desc'), 2);

        $this->repository->save($task1);
        $this->repository->save($task2);
        $this->repository->save($task3);

        // When
        $count = $this->repository->countByGroupId(1);

        // Then
        $this->assertEquals(2, $count);
    }

    public function test_완료된_할일_개수를_조회함(): void
    {
        // Given
        $task1 = Task::create(new TaskTitle('Task 1'), new TaskDescription('Desc'));
        $task2 = Task::create(new TaskTitle('Task 2'), new TaskDescription('Desc'));
        $task3 = Task::create(new TaskTitle('Task 3'), new TaskDescription('Desc'));

        $this->repository->save($task1);
        $task2->complete();
        $this->repository->save($task2);
        $task3->complete();
        $this->repository->save($task3);

        // When
        $count = $this->repository->countCompleted();

        // Then
        $this->assertEquals(2, $count);
    }

    public function test_그룹별_완료된_할일_개수를_조회함(): void
    {
        // Given
        $task1 = Task::create(new TaskTitle('Task 1'), new TaskDescription('Desc'), 1);
        $task2 = Task::create(new TaskTitle('Task 2'), new TaskDescription('Desc'), 1);
        $task3 = Task::create(new TaskTitle('Task 3'), new TaskDescription('Desc'), 2);

        $task1->complete();
        $this->repository->save($task1);
        $this->repository->save($task2);
        $task3->complete();
        $this->repository->save($task3);

        // When
        $count = $this->repository->countCompleted(groupId: 1);

        // Then
        $this->assertEquals(1, $count);
    }
}
