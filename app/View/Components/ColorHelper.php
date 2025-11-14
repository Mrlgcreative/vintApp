<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\ColorPaletteService;

class ColorHelper extends Component
{
    protected $colorService;

    public function __construct()
    {
        $this->colorService = app(ColorPaletteService::class);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return '';
    }

    /**
     * Obtenir une couleur spécifique
     */
    public function color(string $colorName): string
    {
        return $this->colorService->getColor($colorName);
    }

    /**
     * Obtenir une classe CSS avec couleur VintApp
     */
    public function class(string $type, string $color, string $shade = ''): string
    {
        $colorValue = $this->colorService->getColor($color);
        
        switch ($type) {
            case 'bg':
                return "style=\"background-color: {$colorValue};\"";
            case 'text':
                return "style=\"color: {$colorValue};\"";
            case 'border':
                return "style=\"border-color: {$colorValue};\"";
            default:
                return "style=\"background-color: {$colorValue};\"";
        }
    }
}