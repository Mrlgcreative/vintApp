<?php

namespace App\Http\Controllers;

use App\Models\Exposition;
use App\Models\Item;
use Illuminate\View\View;

class ExpositionController extends Controller
{
    /**
     * Annuaire public des expositions en cours.
     */
    public function index(): View
    {
        $expositions = Exposition::running()
            ->with(['user', 'items'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('expositions.index', compact('expositions'));
    }

    /**
     * Page publique d'une exposition.
     */
    public function show(Exposition $exposition): View
    {
        if (!$exposition->isRunning()) {
            abort(404);
        }

        // Compteur de vues simple (sans abuser des requêtes).
        $exposition->increment('views');

        $items = $exposition->items()
            ->where('status', 'active')
            ->orderByDesc('exposition_item.id')
            ->get();

        return view('expositions.show', compact('exposition', 'items'));
    }
}