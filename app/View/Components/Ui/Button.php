<?php

namespace App\View\Components\Ui;

use Illuminate\View\Component;

class Button extends Component
{
    public string $variant;
    public string $size;
    public string $type;

    /**
     * Create a new component instance.
     *
     * @param string $variant Button variant (primary, secondary, danger, outline)
     * @param string $size Button size (small, medium, large)
     * @param string $type Button type (button, submit, reset)
     */
    public function __construct(
        string $variant = 'primary',
        string $size = 'medium',
        string $type = 'button'
    ) {
        $this->variant = $variant;
        $this->size = $size;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.ui.button');
    }
}
