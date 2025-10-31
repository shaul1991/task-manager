<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TaskList Eloquent Model
 *
 * Infrastructure 레이어에서 사용하는 Eloquent 모델
 * Domain Entity와 별개로 데이터베이스 영속성을 담당
 */
class TaskList extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'task_group_id',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * TaskList belongs to TaskGroup
     */
    public function taskGroup()
    {
        return $this->belongsTo(TaskGroup::class, 'task_group_id');
    }

    /**
     * TaskList has many Tasks
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'task_list_id');
    }
}
