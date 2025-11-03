<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReferralCodeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier s'il y a un code de parrainage dans l'URL
        if ($request->has('ref') && !session()->has('referral_code')) {
            $referralCode = $request->get('ref');
            
            // Valider que le code existe et est actif
            $validCode = \App\Models\ReferralCode::where('code', $referralCode)
                                                ->active()
                                                ->exists();
            
            if ($validCode) {
                // Stocker le code en session pour 30 minutes
                session(['referral_code' => $referralCode], 30);
                
                // Ajouter un message flash pour informer l'utilisateur
                session()->flash('referral_info', 'Code de parrainage détecté ! Inscrivez-vous pour bénéficier des points bonus.');
            }
        }

        return $next($request);
    }
}