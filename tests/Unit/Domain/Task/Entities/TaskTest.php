<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\Entities;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Entities\Task;
use Src\Domain\Task\ValueObjects\TaskTitle;
use Src\Domain\Task\ValueObjects\TaskDescription;
use Src\Domain\Task\ValueObjects\CompletedDateTime;
use Src\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use Src\Domain\Task\Exceptions\TaskNotCompletedException;

final class TaskTest extends TestCase
{
    // ========== 팩토리 메서드 테스트 ==========

    public function test_create_task_with_title_and_description(): void
    {
        // Given
        $title = new TaskTitle('Buy groceries');
        $description = new TaskDescription('Milk, eggs, bread');

        // When
        $task = Task::create($title, $description);

        // Then
        $this->assertNull($task->id());
        $this->assertEquals($title, $task->title());
        $this->assertEquals($description, $task->description());
        $this->assertNull($task->completedDateTime());
        $this->assertNull($task->groupId());
        $this->assertInstanceOf(DateTimeImmutable::class, $task->createdAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $task->updatedAt());
    }

    public function test_create_task_with_group_id(): void
    {
        // Given
        $title = new TaskTitle('Team meeting');
        $description = new TaskDescription('Quarterly review');
        $groupId = 5;

        // When
        $task = Task::create($title, $description, $groupId);

        // Then
        $this->assertEquals($groupId, $task->groupId());
    }

    public function test_create_task_is_not_completed(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('New task'),
            new TaskDescription('Description')
        );

        // When & Then
        $this->assertFalse($task->isCompleted());
        $this->assertNull($task->completedDateTime());
    }

    public function test_reconstruct_task_with_all_properties(): void
    {
        // Given
        $id = 42;
        $title = new TaskTitle('Reconstructed task');
        $description = new TaskDescription('From database');
        $completedDateTime = CompletedDateTime::now();
        $groupId = 10;
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-02 15:30:00');

        // When
        $task = Task::reconstruct(
            $id,
            $title,
            $description,
            $completedDateTime,
            $groupId,
            $createdAt,
            $updatedAt
        );

        // Then
        $this->assertEquals($id, $task->id());
        $this->assertEquals($title, $task->title());
        $this->assertEquals($description, $task->description());
        $this->assertEquals($completedDateTime, $task->completedDateTime());
        $this->assertEquals($groupId, $task->groupId());
        $this->assertEquals($createdAt, $task->createdAt());
        $this->assertEquals($updatedAt, $task->updatedAt());
    }

    public function test_reconstruct_completed_task(): void
    {
        // Given
        $completedDateTime = CompletedDateTime::fromString('2024-10-30 14:30:00');

        // When
        $task = Task::reconstruct(
            1,
            new TaskTitle('Completed task'),
            new TaskDescription('Already done'),
            $completedDateTime,
            null,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        // Then
        $this->assertTrue($task->isCompleted());
        $this->assertEquals($completedDateTime, $task->completedDateTime());
    }

    // ========== 완료 상태 관리 테스트 ==========

    public function test_complete_task_sets_completed_datetime(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Test task'),
            new TaskDescription('Test description')
        );
        $beforeComplete = new DateTimeImmutable();

        // When
        $task->complete();
        $afterComplete = new DateTimeImmutable();

        // Then
        $this->assertTrue($task->isCompleted());
        $this->assertNotNull($task->completedDateTime());

        $completedTime = $task->completedDateTime()->value();
        $this->assertGreaterThanOrEqual(
            $beforeComplete->getTimestamp(),
            $completedTime->getTimestamp()
        );
        $this->assertLessThanOrEqual(
            $afterComplete->getTimestamp(),
            $completedTime->getTimestamp()
        );
    }

    public function test_complete_already_completed_task_throws_exception(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Already done'),
            new TaskDescription('Completed task')
        );
        $task->complete();

        // Then
        $this->expectException(TaskAlreadyCompletedException::class);

        // When
        $task->complete();
    }

    public function test_is_completed_returns_true_for_completed_task(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Task'),
            new TaskDescription('Description')
        );

        // When
        $task->complete();

        // Then
        $this->assertTrue($task->isCompleted());
    }

    public function test_uncomplete_task_clears_completed_datetime(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Completed task'),
            new TaskDescription('To be uncompleted')
        );
        $task->complete();

        // When
        $task->uncomplete();

        // Then
        $this->assertFalse($task->isCompleted());
        $this->assertNull($task->completedDateTime());
    }

    public function test_uncomplete_not_completed_task_throws_exception(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Not completed'),
            new TaskDescription('Task')
        );

        // Then
        $this->expectException(TaskNotCompletedException::class);

        // When
        $task->uncomplete();
    }

    public function test_is_completed_returns_false_for_incomplete_task(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Incomplete task'),
            new TaskDescription('Not done yet')
        );

        // When & Then
        $this->assertFalse($task->isCompleted());
    }

    // ========== 업데이트 메서드 테스트 ==========

    public function test_update_title_changes_title(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Original title'),
            new TaskDescription('Description')
        );
        $newTitle = new TaskTitle('New title');

        // When
        $task->updateTitle($newTitle);

        // Then
        $this->assertEquals($newTitle, $task->title());
    }

    public function test_update_title_updates_updated_at(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Original title'),
            new TaskDescription('Description')
        );
        $originalUpdatedAt = $task->updatedAt();

        sleep(1); // 시간 차이 확보

        // When
        $task->updateTitle(new TaskTitle('New title'));

        // Then
        $this->assertGreaterThan(
            $originalUpdatedAt->getTimestamp(),
            $task->updatedAt()->getTimestamp()
        );
    }

    public function test_update_description_changes_description(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Title'),
            new TaskDescription('Original description')
        );
        $newDescription = new TaskDescription('New description');

        // When
        $task->updateDescription($newDescription);

        // Then
        $this->assertEquals($newDescription, $task->description());
    }

    public function test_update_description_updates_updated_at(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Title'),
            new TaskDescription('Original')
        );
        $originalUpdatedAt = $task->updatedAt();

        sleep(1);

        // When
        $task->updateDescription(new TaskDescription('New'));

        // Then
        $this->assertGreaterThan(
            $originalUpdatedAt->getTimestamp(),
            $task->updatedAt()->getTimestamp()
        );
    }

    // ========== 그룹 관리 테스트 ==========

    public function test_assign_to_group_sets_group_id(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Task'),
            new TaskDescription('Description')
        );
        $groupId = 5;

        // When
        $task->assignToGroup($groupId);

        // Then
        $this->assertEquals($groupId, $task->groupId());
    }

    public function test_remove_from_group_clears_group_id(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Task'),
            new TaskDescription('Description'),
            5
        );

        // When
        $task->removeFromGroup();

        // Then
        $this->assertNull($task->groupId());
    }

    public function test_assign_to_group_updates_updated_at(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Task'),
            new TaskDescription('Description')
        );
        $originalUpdatedAt = $task->updatedAt();

        sleep(1);

        // When
        $task->assignToGroup(10);

        // Then
        $this->assertGreaterThan(
            $originalUpdatedAt->getTimestamp(),
            $task->updatedAt()->getTimestamp()
        );
    }

    // ========== 엣지 케이스 테스트 ==========

    public function test_complete_and_uncomplete_cycle(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Cyclic task'),
            new TaskDescription('Testing cycle')
        );

        // When - 완료 -> 미완료 -> 다시 완료
        $this->assertFalse($task->isCompleted());

        $task->complete();
        $this->assertTrue($task->isCompleted());

        $task->uncomplete();
        $this->assertFalse($task->isCompleted());

        $task->complete();

        // Then
        $this->assertTrue($task->isCompleted());
        $this->assertNotNull($task->completedDateTime());
    }

    public function test_task_immutability_of_created_at(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Immutable test'),
            new TaskDescription('Description')
        );
        $originalCreatedAt = $task->createdAt();

        // When - 여러 변경 작업 수행
        $task->updateTitle(new TaskTitle('New title'));
        $task->updateDescription(new TaskDescription('New description'));
        $task->complete();
        $task->uncomplete();
        $task->assignToGroup(5);

        // Then - createdAt은 변경되지 않아야 함
        $this->assertEquals(
            $originalCreatedAt->getTimestamp(),
            $task->createdAt()->getTimestamp()
        );
    }
}
