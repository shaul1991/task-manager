<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskGroup extends Model
{
    use SoftDeletes;

    protected $table = 'task_groups';

    protected $fillable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * TaskGroup has many TaskLists
     */
    public function taskLists(): HasMany
    {
        return $this->hasMany(TaskList::class, 'task_group_id');
    }

    /**
     * Get incomplete task count across all task lists in this group
     */
    public function getIncompleteTaskCountAttribute(): int
    {
        return $this->taskLists->sum(function ($taskList) {
            return $taskList->incompleteTaskCount ?? 0;
        });
    }
}
