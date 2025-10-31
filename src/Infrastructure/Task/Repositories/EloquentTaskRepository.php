<?php

declare(strict_types=1);

namespace Src\Infrastructure\Task\Repositories;

use App\Models\Task as TaskEloquentModel;
use DateTimeImmutable;
use Src\Domain\Task\Entities\Task;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Domain\Task\ValueObjects\CompletedDateTime;
use Src\Domain\Task\ValueObjects\TaskDescription;
use Src\Domain\Task\ValueObjects\TaskTitle;

/**
 * Eloquent Task Repository 구현
 *
 * TaskRepositoryInterface를 Eloquent ORM을 사용하여 구현
 */
final class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function save(Task $task): Task
    {
        $data = [
            'title' => $task->title()->value(),
            'description' => $task->description()->value(),
            'completed_datetime' => $task->completedDateTime()?->toDateTime(),
            'task_list_id' => $task->taskListId(),
        ];

        if ($task->id() === null) {
            // 새로운 Task 생성
            $eloquentTask = TaskEloquentModel::create($data);
        } else {
            // 기존 Task 업데이트
            $eloquentTask = TaskEloquentModel::findOrFail($task->id());
            $eloquentTask->update($data);
        }

        return $this->toDomain($eloquentTask);
    }

    public function findById(int $id): ?Task
    {
        $eloquentTask = TaskEloquentModel::find($id);

        if ($eloquentTask === null) {
            return null;
        }

        return $this->toDomain($eloquentTask);
    }

    public function findAll(
        ?int $taskListId = null,
        ?bool $completed = null,
        int $limit = 100,
        int $offset = 0
    ): array {
        $query = TaskEloquentModel::query();

        // Eager load taskList relationship
        $query->with('taskList');

        // TaskList ID 필터
        if ($taskListId !== null) {
            $query->where('task_list_id', $taskListId);
        }

        // 완료 상태 필터
        if ($completed !== null) {
            if ($completed) {
                $query->whereNotNull('completed_datetime');
            } else {
                $query->whereNull('completed_datetime');
            }
        }

        // 정렬: 최신순
        $query->orderBy('created_at', 'desc');

        // 페이지네이션
        $query->limit($limit)->offset($offset);

        $eloquentTasks = $query->get();

        return $eloquentTasks->map(fn(TaskEloquentModel $task) => $this->toDomain($task))->all();
    }

    public function delete(int $id): void
    {
        TaskEloquentModel::where('id', $id)->delete();
    }

    public function existsById(int $id): bool
    {
        return TaskEloquentModel::where('id', $id)->exists();
    }

    public function countByTaskListId(int $taskListId): int
    {
        return TaskEloquentModel::where('task_list_id', $taskListId)->count();
    }

    public function countCompleted(?int $taskListId = null): int
    {
        $query = TaskEloquentModel::whereNotNull('completed_datetime');

        if ($taskListId !== null) {
            $query->where('task_list_id', $taskListId);
        }

        return $query->count();
    }

    /**
     * {@inheritDoc}
     */
    public function countIncompleteByTaskListId(int $taskListId): int
    {
        return TaskEloquentModel::query()
            ->where('task_list_id', $taskListId)
            ->whereNull('completed_datetime')
            ->count();
    }

    public function countIncompleteByTaskGroupId(int $taskGroupId): int
    {
        return TaskEloquentModel::query()
            ->whereHas('taskList', function ($query) use ($taskGroupId) {
                $query->where('task_group_id', $taskGroupId);
            })
            ->whereNull('completed_datetime')
            ->count();
    }

    /**
     * {@inheritDoc}
     */
    public function findAllWithTaskListName(
        ?int $taskListId = null,
        ?bool $completed = null,
        int $limit = 100,
        int $offset = 0
    ): array {
        $query = TaskEloquentModel::query();

        // Eager load taskList relationship
        $query->with('taskList');

        // TaskList ID 필터
        if ($taskListId !== null) {
            $query->where('task_list_id', $taskListId);
        }

        // 완료 상태 필터
        if ($completed !== null) {
            if ($completed) {
                $query->whereNotNull('completed_datetime');
            } else {
                $query->whereNull('completed_datetime');
            }
        }

        // 정렬: 최신순
        $query->orderBy('created_at', 'desc');

        // 페이지네이션
        $query->limit($limit)->offset($offset);

        // Eloquent 모델을 Domain Entity + TaskList 이름으로 변환
        return $query->get()->map(function (TaskEloquentModel $eloquentTask) {
            return [
                'task' => $this->toDomain($eloquentTask),
                'taskListName' => $eloquentTask->taskList?->name,
            ];
        })->all();
    }

    /**
     * Eloquent Model을 Domain Entity로 변환
     */
    private function toDomain(TaskEloquentModel $eloquentTask): Task
    {
        $completedDateTime = $eloquentTask->completed_datetime !== null
            ? CompletedDateTime::fromDateTime($eloquentTask->completed_datetime)
            : null;

        return Task::reconstruct(
            id: $eloquentTask->id,
            title: new TaskTitle($eloquentTask->title),
            description: new TaskDescription($eloquentTask->description),
            completedDateTime: $completedDateTime,
            taskListId: $eloquentTask->task_list_id,
            createdAt: new DateTimeImmutable($eloquentTask->created_at->toDateTimeString()),
            updatedAt: new DateTimeImmutable($eloquentTask->updated_at->toDateTimeString())
        );
    }
}
