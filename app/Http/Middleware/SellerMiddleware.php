<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isSeller()) {
            return redirect()->route('home')->with('error', 'Vous devez avoir au moins un article en vente pour accéder à cet espace.');
        }

        return $next($request);
    }
}
