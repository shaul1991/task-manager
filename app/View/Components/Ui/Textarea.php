<?php

namespace App\View\Components\Ui;

use Illuminate\View\Component;

class Textarea extends Component
{
    public string $name;
    public ?string $label;
    public mixed $value;
    public int $rows;
    public ?string $error;
    public bool $required;
    public ?string $placeholder;

    /**
     * Create a new component instance.
     *
     * @param string $name Textarea field name
     * @param string|null $label Label text
     * @param mixed $value Textarea value
     * @param int $rows Number of rows
     * @param string|null $error Error message
     * @param bool $required Whether the field is required
     * @param string|null $placeholder Placeholder text
     */
    public function __construct(
        string $name,
        ?string $label = null,
        mixed $value = null,
        int $rows = 4,
        ?string $error = null,
        bool $required = false,
        ?string $placeholder = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->value = old($name, $value);
        $this->rows = $rows;
        $this->error = $error;
        $this->required = $required;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the error message for this field
     */
    public function getError(): ?string
    {
        if ($this->error) {
            return $this->error;
        }

        // Get error from Laravel's error bag
        $errors = session('errors');
        if ($errors && $errors->has($this->name)) {
            return $errors->first($this->name);
        }

        return null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.ui.textarea');
    }
}
