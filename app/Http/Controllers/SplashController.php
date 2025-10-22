<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SplashController extends Controller
{
    public function show()
    {
        return view('splash');
    }

    public function redirect()
    {
        // Redirection vers la page d'accueil après le splash screen
        return redirect()->route('home');
    }
}