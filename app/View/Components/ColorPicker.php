<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\ColorPaletteService;

class ColorPicker extends Component
{
    public $palettes;
    public $activePalette;
    
    public function __construct()
    {
        $colorService = new ColorPaletteService();
        $this->palettes = $colorService->getAvailablePalettes();
        $this->activePalette = config('colors.active_palette');
    }

    public function render()
    {
        return view('components.color-picker');
    }
}