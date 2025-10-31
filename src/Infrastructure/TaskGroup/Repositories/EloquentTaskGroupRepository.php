<?php

declare(strict_types=1);

namespace Src\Infrastructure\TaskGroup\Repositories;

use App\Models\TaskGroup as TaskGroupModel;
use App\Models\TaskList as TaskListModel;
use DateTimeImmutable;
use Src\Domain\TaskGroup\Entities\TaskGroup;
use Src\Domain\TaskGroup\Repositories\TaskGroupRepositoryInterface;
use Src\Domain\TaskGroup\ValueObjects\TaskGroupName;

/**
 * Eloquent TaskGroup Repository
 *
 * TaskGroup Repository의 Eloquent 구현
 */
final class EloquentTaskGroupRepository implements TaskGroupRepositoryInterface
{
    /**
     * Save TaskGroup (create or update)
     */
    public function save(TaskGroup $taskGroup): TaskGroup
    {
        if ($taskGroup->id() === null) {
            // Create new TaskGroup
            $model = TaskGroupModel::create([
                'name' => $taskGroup->name()->value(),
            ]);
        } else {
            // Update existing TaskGroup
            $model = TaskGroupModel::findOrFail($taskGroup->id());
            $model->update([
                'name' => $taskGroup->name()->value(),
            ]);
        }

        return $this->toDomain($model);
    }

    /**
     * Find TaskGroup by ID
     */
    public function findById(int $id): ?TaskGroup
    {
        $model = TaskGroupModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    /**
     * Find all TaskGroups
     */
    public function findAll(int $limit = 100, int $offset = 0): array
    {
        $models = TaskGroupModel::query()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $models->map(fn($model) => $this->toDomain($model))->all();
    }

    /**
     * Delete TaskGroup (SoftDelete)
     */
    public function delete(int $id): void
    {
        $model = TaskGroupModel::findOrFail($id);
        $model->delete();
    }

    /**
     * Check if TaskGroup exists by ID
     */
    public function existsById(int $id): bool
    {
        return TaskGroupModel::where('id', $id)->exists();
    }

    /**
     * Count all TaskGroups
     */
    public function count(): int
    {
        return TaskGroupModel::count();
    }

    /**
     * Set all TaskLists' task_group_id to NULL when TaskGroup is deleted
     */
    public function unassignTaskListsFromGroup(int $taskGroupId): void
    {
        TaskListModel::where('task_group_id', $taskGroupId)
            ->update(['task_group_id' => null]);
    }

    /**
     * Convert Eloquent Model to Domain Entity
     */
    private function toDomain(TaskGroupModel $model): TaskGroup
    {
        return TaskGroup::reconstruct(
            id: $model->id,
            name: new TaskGroupName($model->name),
            createdAt: new DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new DateTimeImmutable($model->updated_at->toDateTimeString()),
            deletedAt: $model->deleted_at
                ? new DateTimeImmutable($model->deleted_at->toDateTimeString())
                : null
        );
    }
}
