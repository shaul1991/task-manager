<?php

namespace App\View\Components\TaskGroup;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class TaskGroupList extends Component
{
    public Collection|array $taskGroups;
    public ?int $activeTaskGroupId;

    /**
     * Create a new component instance.
     *
     * @param Collection|array $taskGroups TaskGroup 배열 또는 Collection
     * @param int|null $activeTaskGroupId 현재 활성 TaskGroup ID
     */
    public function __construct(
        Collection|array $taskGroups = [],
        ?int $activeTaskGroupId = null
    ) {
        $this->taskGroups = $taskGroups;
        $this->activeTaskGroupId = $activeTaskGroupId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.task-group.task-group-list');
    }
}