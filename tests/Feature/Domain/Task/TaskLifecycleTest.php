<?php

declare(strict_types=1);

namespace Tests\Feature\Domain\Task;

use PHPUnit\Framework\TestCase;
use Src\Domain\Task\Entities\Task;
use Src\Domain\Task\ValueObjects\TaskTitle;
use Src\Domain\Task\ValueObjects\TaskDescription;
use Src\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use Src\Domain\Task\Exceptions\TaskNotCompletedException;

/**
 * Task 도메인의 전체 라이프사이클을 테스트하는 통합 테스트
 */
final class TaskLifecycleTest extends TestCase
{
    public function test_생성부터_완료까지_전체_할일_라이프사이클(): void
    {
        // Given - 새로운 할 일 생성
        $task = Task::create(
            new TaskTitle('Buy groceries'),
            new TaskDescription('Milk, eggs, bread')
        );

        $this->assertFalse($task->isCompleted());
        $this->assertNull($task->completedDateTime());

        // When - 제목 수정
        $task->updateTitle(new TaskTitle('Buy groceries and fruits'));

        // Then
        $this->assertEquals('Buy groceries and fruits', $task->title()->value());

        // When - 설명 수정
        $task->updateDescription(new TaskDescription('Milk, eggs, bread, apples, bananas'));

        // Then
        $this->assertEquals('Milk, eggs, bread, apples, bananas', $task->description()->value());

        // When - 완료 처리
        $task->complete();

        // Then
        $this->assertTrue($task->isCompleted());
        $this->assertNotNull($task->completedDateTime());

        // When - 미완료 처리
        $task->uncomplete();

        // Then
        $this->assertFalse($task->isCompleted());
        $this->assertNull($task->completedDateTime());
    }

    public function test_할일_그룹_할당_라이프사이클(): void
    {
        // Given - 할 일 생성 (그룹 없음)
        $task = Task::create(
            new TaskTitle('Team meeting'),
            new TaskDescription('Quarterly review')
        );

        $this->assertNull($task->groupId());

        // When - 그룹에 할당
        $workGroupId = 5;
        $task->assignToGroup($workGroupId);

        // Then
        $this->assertEquals($workGroupId, $task->groupId());

        // When - 다른 그룹으로 변경
        $projectGroupId = 10;
        $task->assignToGroup($projectGroupId);

        // Then
        $this->assertEquals($projectGroupId, $task->groupId());

        // When - 그룹에서 제거
        $task->removeFromGroup();

        // Then
        $this->assertNull($task->groupId());
    }

    public function test_여러_할일의_완료_추적(): void
    {
        // Given - 여러 할 일 생성
        $task1 = Task::create(
            new TaskTitle('Task 1'),
            new TaskDescription('First task')
        );

        $task2 = Task::create(
            new TaskTitle('Task 2'),
            new TaskDescription('Second task')
        );

        $task3 = Task::create(
            new TaskTitle('Task 3'),
            new TaskDescription('Third task')
        );

        // When - 일부만 완료 처리
        $task1->complete();
        $task3->complete();

        // Then - 완료 상태 확인
        $this->assertTrue($task1->isCompleted());
        $this->assertFalse($task2->isCompleted());
        $this->assertTrue($task3->isCompleted());

        // Then - 완료된 작업들은 완료 시간을 가짐
        $this->assertNotNull($task1->completedDateTime());
        $this->assertNull($task2->completedDateTime());
        $this->assertNotNull($task3->completedDateTime());
    }

    public function test_중복_완료_시_예외_처리(): void
    {
        // Given - 할 일 생성 및 완료
        $task = Task::create(
            new TaskTitle('Already completed task'),
            new TaskDescription('This is done')
        );

        $task->complete();
        $this->assertTrue($task->isCompleted());

        // When & Then - 중복 완료 시도는 예외 발생
        $this->expectException(TaskAlreadyCompletedException::class);
        $task->complete();
    }

    public function test_미완료_할일_미완료_처리_시_예외_처리(): void
    {
        // Given - 미완료 상태의 할 일
        $task = Task::create(
            new TaskTitle('Incomplete task'),
            new TaskDescription('Not done yet')
        );

        $this->assertFalse($task->isCompleted());

        // When & Then - 미완료 상태에서 uncomplete 시도는 예외 발생
        $this->expectException(TaskNotCompletedException::class);
        $task->uncomplete();
    }
}
