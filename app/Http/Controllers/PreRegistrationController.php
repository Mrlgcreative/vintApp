<?php

namespace App\Http\Controllers;

use App\Models\UserWaiting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PreRegistrationController extends Controller
{
    /**
     * Afficher le formulaire de pré-inscription
     */
    public function index()
    {
        // Vérifier si la pré-inscription est activée
        $enabled = Setting::get('preregistration_enabled', true);
        
        if (!$enabled) {
            $message = Setting::get('preregistration_closed_message', 'Les pré-inscriptions sont actuellement fermées.');
            $contactEmail = Setting::get('preregistration_notification_email', 'contact@vintapp.com');
            return view('preregistration.closed', compact('message', 'contactEmail'));
        }

        // Vérifier la limite de pré-inscriptions
        $limit = Setting::get('preregistration_limit', 0);
        if ($limit > 0) {
            $currentCount = UserWaiting::count();
            if ($currentCount >= $limit) {
                return view('preregistration.limit-reached');
            }
        }

        return view('preregistration.index');
    }

    /**
     * Enregistrer une nouvelle pré-inscription
     */
    public function store(Request $request)
    {
        // Vérifier si la pré-inscription est activée
        $enabled = Setting::get('preregistration_enabled', true);
        
        if (!$enabled) {
            return redirect()->route('preregistration.index')
                ->with('error', 'Les pré-inscriptions sont actuellement fermées.');
        }

        // Vérifier la limite
        $limit = Setting::get('preregistration_limit', 0);
        if ($limit > 0) {
            $currentCount = UserWaiting::count();
            if ($currentCount >= $limit) {
                return redirect()->route('preregistration.index')
                    ->with('error', 'Le nombre maximum de pré-inscriptions a été atteint.');
            }
        }

        // Récupérer les paramètres
        $requirePhone = Setting::get('preregistration_require_phone', false);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users_waiting,email', 'unique:users,email'],
            'phone' => $requirePhone 
                ? ['required', 'string', 'regex:/^(\+?243|0)?[0-9]{9}$/', 'max:15']
                : ['nullable', 'string', 'regex:/^(\+?243|0)?[0-9]{9}$/', 'max:15'],
            'country' => ['required', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];

        $validator = Validator::make($request->all(), $rules, [
            'email.unique' => 'Cette adresse email est déjà enregistrée.',
            'phone.regex' => 'Format de téléphone invalide. Ex: 0812345678 ou +243812345678',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Créer la pré-inscription
            $userWaiting = UserWaiting::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'message' => $request->message,
                'confirmation_token' => UserWaiting::generateUniqueToken(),
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Envoyer l'email de confirmation
            $userWaiting->sendConfirmationEmail();

            Log::info("Nouvelle pré-inscription: {$userWaiting->email}");

            return redirect()->route('preregistration.success')
                ->with('success', 'Merci pour votre inscription ! Vérifiez votre email pour confirmer.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la pré-inscription: {$e->getMessage()}");

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Page de succès après inscription
     */
    public function success()
    {
        return view('preregistration.success');
    }

    /**
     * Confirmer l'email via le token
     */
    public function confirm($token)
    {
        $userWaiting = UserWaiting::where('confirmation_token', $token)->first();

        if (!$userWaiting) {
            return redirect()->route('preregistration.index')
                ->with('error', 'Lien de confirmation invalide.');
        }

        if ($userWaiting->isConfirmed() || $userWaiting->isApproved()) {
            return redirect()->route('preregistration.already-confirmed')
                ->with('info', 'Votre email a déjà été confirmé !');
        }

        // Confirmer l'email
        $userWaiting->confirmEmail();

        return view('preregistration.confirmed', compact('userWaiting'));
    }

    /**
     * Page email déjà confirmé
     */
    public function alreadyConfirmed()
    {
        return view('preregistration.already-confirmed');
    }

    /**
     * Statistiques publiques (optionnel)
     */
    public function stats()
    {
        $stats = [
            'total' => UserWaiting::count(),
            'confirmed' => UserWaiting::confirmed()->count(),
            'approved' => UserWaiting::approved()->count(),
            'converted' => UserWaiting::converted()->count(),
        ];

        return view('preregistration.stats', compact('stats'));
    }
}
