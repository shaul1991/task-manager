<?php

namespace App\View\Components\TaskGroup;

use Illuminate\View\Component;

class TaskGroupItem extends Component
{
    public mixed $taskGroup;
    public bool $isExpanded;
    public bool $isActive;

    /**
     * Create a new component instance.
     *
     * @param mixed $taskGroup TaskGroup 데이터 객체 (Model 또는 DTO)
     * @param bool $isExpanded 펼침 상태 (기본: true)
     * @param bool $isActive 현재 활성 상태 (기본: false)
     */
    public function __construct(
        mixed $taskGroup,
        bool $isExpanded = true,
        bool $isActive = false
    ) {
        $this->taskGroup = $taskGroup;
        $this->isExpanded = $isExpanded;
        $this->isActive = $isActive;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.task-group.task-group-item');
    }
}