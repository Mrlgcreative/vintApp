<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeNewsletter;

class NewsletterController extends Controller
{
    /**
     * S'abonner à la newsletter
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        try {
            $subscriber = NewsletterSubscriber::create([
                'email' => $request->email,
                'name' => $request->name,
            ]);

            // Envoyer l'email de bienvenue si activé
            if ($subscriber->receive_welcome) {
                Mail::to($subscriber->email)->send(new WelcomeNewsletter($subscriber));
                $subscriber->incrementEmailsSent();
            }

            return response()->json([
                'success' => true,
                'message' => 'Merci pour votre inscription ! Consultez votre email pour confirmer.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }

    /**
     * Se désabonner de la newsletter
     */
    public function unsubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            abort(404, 'Lien de désinscription invalide');
        }

        $subscriber->unsubscribe();

        return view('newsletter.unsubscribed', compact('subscriber'));
    }

    /**
     * Page de gestion des préférences
     */
    public function preferences($token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            abort(404, 'Lien invalide');
        }

        return view('newsletter.preferences', compact('subscriber'));
    }

    /**
     * Mettre à jour les préférences
     */
    public function updatePreferences(Request $request, $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            abort(404, 'Lien invalide');
        }

        $subscriber->update([
            'receive_new_items' => $request->has('receive_new_items'),
            'receive_promotions' => $request->has('receive_promotions'),
            'receive_newsletters' => $request->has('receive_newsletters'),
        ]);

        return redirect()->route('newsletter.preferences', $token)
            ->with('success', 'Vos préférences ont été mises à jour !');
    }

    /**
     * Vérifier l'email
     */
    public function verify($token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            abort(404, 'Lien de vérification invalide');
        }

        if (!$subscriber->email_verified) {
            $subscriber->verify();
        }

        return view('newsletter.verified', compact('subscriber'));
    }

    /**
     * Tracking ouverture email
     */
    public function trackOpen($token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if ($subscriber) {
            $subscriber->incrementEmailsOpened();
        }

        // Retourner une image transparente 1x1 pixel
        return response()->file(public_path('images/pixel.png'));
    }

    /**
     * Tracking clic dans email
     */
    public function trackClick($token, Request $request)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if ($subscriber) {
            $subscriber->incrementEmailsClicked();
        }

        $url = $request->query('url', route('home'));
        return redirect($url);
    }
}
