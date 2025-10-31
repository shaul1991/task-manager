<?php

namespace App\View\Components\Ui;

use Illuminate\View\Component;

class Input extends Component
{
    public string $name;
    public ?string $label;
    public string $type;
    public mixed $value;
    public ?string $error;
    public bool $required;
    public ?string $placeholder;

    /**
     * Create a new component instance.
     *
     * @param string $name Input field name
     * @param string|null $label Label text
     * @param string $type Input type (text, email, password, etc.)
     * @param mixed $value Input value
     * @param string|null $error Error message
     * @param bool $required Whether the field is required
     * @param string|null $placeholder Placeholder text
     */
    public function __construct(
        string $name,
        ?string $label = null,
        string $type = 'text',
        mixed $value = null,
        ?string $error = null,
        bool $required = false,
        ?string $placeholder = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->value = old($name, $value);
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
        return view('components.ui.input');
    }
}
