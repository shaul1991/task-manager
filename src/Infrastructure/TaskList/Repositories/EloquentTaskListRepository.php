<?php

declare(strict_types=1);

namespace Src\Infrastructure\TaskList\Repositories;

use App\Models\TaskList as TaskListEloquentModel;
use DateTimeImmutable;
use Src\Domain\TaskList\Entities\TaskList;
use Src\Domain\TaskList\Repositories\TaskListRepositoryInterface;
use Src\Domain\TaskList\ValueObjects\TaskListName;
use Src\Domain\TaskList\ValueObjects\TaskListDescription;

/**
 * Eloquent TaskList Repository 구현
 *
 * TaskListRepositoryInterface를 Eloquent ORM을 사용하여 구현
 */
final class EloquentTaskListRepository implements TaskListRepositoryInterface
{
    public function save(TaskList $taskList): TaskList
    {
        $data = [
            'name' => $taskList->name()->value(),
            'description' => $taskList->description()->value(),
        ];

        if ($taskList->id() === null) {
            // 새로운 TaskList 생성
            $eloquentTaskList = TaskListEloquentModel::create($data);
        } else {
            // 기존 TaskList 업데이트
            $eloquentTaskList = TaskListEloquentModel::findOrFail($taskList->id());
            $eloquentTaskList->update($data);
        }

        return $this->toDomain($eloquentTaskList);
    }

    public function findById(int $id): ?TaskList
    {
        $eloquentTaskList = TaskListEloquentModel::find($id);

        if ($eloquentTaskList === null) {
            return null;
        }

        return $this->toDomain($eloquentTaskList);
    }

    public function findAll(
        ?int $userId = null,
        int $limit = 100,
        int $offset = 0
    ): array {
        $query = TaskListEloquentModel::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id'); // 게스트
        }

        $eloquentTaskLists = $query
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $eloquentTaskLists->map(fn($eloquentTaskList) => $this->toDomain($eloquentTaskList))->all();
    }

    public function delete(int $id): void
    {
        TaskListEloquentModel::where('id', $id)->delete();
    }

    public function existsById(int $id): bool
    {
        return TaskListEloquentModel::where('id', $id)->exists();
    }

    public function countByUserId(?int $userId): int
    {
        $query = TaskListEloquentModel::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        return $query->count();
    }

    /**
     * Eloquent Model을 Domain Entity로 변환
     */
    private function toDomain(TaskListEloquentModel $eloquentTaskList): TaskList
    {
        return TaskList::reconstruct(
            id: $eloquentTaskList->id,
            name: new TaskListName($eloquentTaskList->name),
            description: new TaskListDescription($eloquentTaskList->description),
            createdAt: new DateTimeImmutable($eloquentTaskList->created_at->toDateTimeString()),
            updatedAt: new DateTimeImmutable($eloquentTaskList->updated_at->toDateTimeString())
        );
    }
}
