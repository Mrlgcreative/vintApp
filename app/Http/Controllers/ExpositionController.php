<?php

namespace App\Http\Controllers;

use App\Models\Exposition;
use Illuminate\View\View;

class ExpositionController extends Controller
{
    /**
     * Annuaire public des expositions actives (en cours et à venir).
     */
    public function index(): View
    {
        $expositions = Exposition::publiclyVisible()
            ->with(['user', 'items' => function ($q) {
                $q->withAvg('reviews', 'rating')->withCount(['reviews', 'favoritedBy']);
            }])
            ->orderByRaw('(starts_at IS NOT NULL AND starts_at > UTC_TIMESTAMP()) ASC')
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
        if ($exposition->status !== 'active') {
            abort(404);
        }

        // Compteur de vues simple (sans abuser des requêtes).
        $exposition->increment('views');

        $items = $exposition->items()
            ->where('status', 'active')
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews', 'favoritedBy'])
            ->orderByDesc('exposition_item.id')
            ->get();

        return view('expositions.show', compact('exposition', 'items'));
    }
}
