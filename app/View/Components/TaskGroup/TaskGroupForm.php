<?php

namespace App\View\Components\TaskGroup;

use Illuminate\View\Component;

class TaskGroupForm extends Component
{
    public ?object $taskGroup;
    public string $formId;

    /**
     * Create a new component instance.
     *
     * @param object|null $taskGroup TaskGroup 객체 (수정시)
     * @param string $formId Form element ID
     */
    public function __construct(
        ?object $taskGroup = null,
        string $formId = 'task-group-form'
    ) {
        $this->taskGroup = $taskGroup;
        $this->formId = $formId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.task-group.task-group-form');
    }
}