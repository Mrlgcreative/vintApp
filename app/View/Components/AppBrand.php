<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppBrand extends Component
{
    public $showLogo;
    public $showName;
    public $logoHeight;
    public $logoWidth;
    public $nameSize;
    public $nameClass;
    public $class;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $showLogo = true,
        $showName = true,
        $logoHeight = '40px',
        $logoWidth = '120px',
        $nameSize = '1.5rem',
        $nameClass = 'text-dark',
        $class = ''
    ) {
        $this->showLogo = $showLogo;
        $this->showName = $showName;
        $this->logoHeight = $logoHeight;
        $this->logoWidth = $logoWidth;
        $this->nameSize = $nameSize;
        $this->nameClass = $nameClass;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.app-brand');
    }
}
