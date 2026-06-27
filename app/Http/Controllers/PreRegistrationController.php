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
        $enabled = Setting::get('preregistration_enabled', false);
        
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
        Log::info('🔄 Début de la préinscription', $request->all());
        
        try {
            // Vérifier si la pré-inscription est activée
            $enabled = Setting::get('preregistration_enabled', false);
            Log::info('📋 Préinscription activée:', ['enabled' => $enabled]);

            if (!$enabled) {
                Log::warning('🚫 Préinscription désactivée');
                return response()->json([
                    'success' => false,
                    'message' => 'Les pré-inscriptions sont actuellement fermées.'
                ], 403);
            }

            // Vérifier la limite
            $limit = Setting::get('preregistration_limit', 0);
            Log::info('📊 Limite de préinscriptions:', ['limit' => $limit]);

            if ($limit > 0) {
                $currentCount = UserWaiting::count();
                Log::info('📈 Nombre actuel de préinscriptions:', ['count' => $currentCount]);

                if ($currentCount >= $limit) {
                    Log::warning('🚫 Limite de préinscriptions atteinte');
                    return response()->json([
                        'success' => false,
                        'message' => 'Le nombre maximum de pré-inscriptions a été atteint.'
                    ], 403);
                }
            }

            // Récupérer les paramètres
            $requirePhone = Setting::get('preregistration_require_phone', false);
            Log::info('📞 Téléphone requis:', ['required' => $requirePhone]);

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users_waiting,email', 'unique:users,email'],
                'phone' => $requirePhone
                    ? ['required', 'string', 'regex:/^(\+?243|0)?[0-9]{9}$/', 'max:15']
                    : ['nullable', 'string', 'regex:/^(\+?243|0)?[0-9]{9}$/', 'max:15'],
                'country' => ['required', 'string', 'max:100'],
                'reasons' => ['nullable', 'array'],
                'reasons.*' => ['string', 'max:255'],
                'firebase_uid' => ['required', 'string', 'max:128'],
            ];

            $validator = Validator::make($request->all(), $rules, [
                'email.unique' => 'Cette adresse email est déjà enregistrée.',
                'phone.regex' => 'Format de téléphone invalide. Ex: 0812345678 ou +243812345678',
                'phone.required' => 'Le numéro de téléphone est obligatoire.',
                'firebase_uid.required' => 'L\'identifiant Firebase est requis.',
            ]);

            if ($validator->fails()) {
                Log::error('❌ Erreurs de validation:', $validator->errors());
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            Log::info('✅ Validation passée');
            Log::info('🔄 Création du UserWaiting...');

            $userWaiting = UserWaiting::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'message' => $request->reasons ? implode(', ', $request->reasons) : null,
                'reasons' => $request->reasons,
                'firebase_uid' => $request->firebase_uid,
                'confirmation_token' => UserWaiting::generateUniqueToken(),
                'status' => 'confirmed',
                'email_confirmed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Log::info('✅ UserWaiting créé avec ID:', ['id' => $userWaiting->id]);
            Log::info("Nouvelle pré-inscription Firebase: {$userWaiting->email} (UID: {$request->firebase_uid})");

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie !',
                'data' => [
                    'id' => $userWaiting->id,
                    'name' => $userWaiting->name,
                    'email' => $userWaiting->email
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error("❌ Erreur lors de la pré-inscription Firebase: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement.'
            ], 500);
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
