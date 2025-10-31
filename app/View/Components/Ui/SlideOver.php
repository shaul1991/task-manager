<?php

namespace App\View\Components\Ui;

use Illuminate\View\Component;

class SlideOver extends Component
{
    public string $id;
    public string $title;
    public string $width;

    /**
     * Create a new component instance.
     *
     * @param string $id Unique identifier for the slide-over
     * @param string $title Title displayed in the header
     * @param string $width Width of the slide-over panel (sm, md, lg, xl)
     */
    public function __construct(
        string $id,
        string $title = '',
        string $width = 'md'
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->width = $width;
    }

    /**
     * Get the width class based on size
     */
    public function getWidthClass(): string
    {
        return match ($this->width) {
            'sm' => 'max-w-sm',
            'md' => 'max-w-md',
            'lg' => 'max-w-lg',
            'xl' => 'max-w-xl',
            '2xl' => 'max-w-2xl',
            default => 'max-w-md',
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.ui.slide-over');
    }
}
