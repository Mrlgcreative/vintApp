<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Piège anti-robot pour les formulaires web d'authentification.
 *
 * Deux filtres complémentaires, tous deux fail-closed :
 *  - les champs honeypot (config/honeypot.php) ne doivent jamais être remplis ;
 *  - la soumission ne doit pas arriver dans la seconde suivant le rendu.
 *
 * Volontairement NON appliqué sur l'API mobile (/api/register, /api/password/email) :
 * le client Capacitor n'envoie pas ces champs et serait cassé.
 */
class ProtectAgainstBots
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $mode = 'error'): Response
    {
        $reason = $this->detect($request);

        if ($reason === null) {
            return $next($request);
        }

        Log::channel(config('honeypot.log_channel'))->warning('bot.trap', [
            'reason' => $reason,
            'route' => $request->route()?->getName(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $mode === 'silent'
            ? $this->fakeSuccess($request)
            : $this->reject($request);
    }

    /**
     * Retourne le motif du rejet, ou null si la soumission paraît humaine.
     */
    protected function detect(Request $request): ?string
    {
        foreach ((array) config('honeypot.fields') as $field) {
            if (filled($request->input($field))) {
                return "honeypot_filled:{$field}";
            }
        }

        $renderedAt = $request->input(config('honeypot.timestamp_field'));

        // Champ absent : ce n'est pas un navigateur qui a rendu notre formulaire.
        if (! is_numeric($renderedAt)) {
            return 'timestamp_missing';
        }

        $elapsed = time() - (int) $renderedAt;

        // Timestamp dans le futur = valeur fabriquée à la main.
        if ($elapsed < 0) {
            return 'timestamp_in_future';
        }

        if ($elapsed < (int) config('honeypot.min_seconds')) {
            return 'timestamp_too_recent';
        }

        return null;
    }

    /**
     * Rejet explicite, avec un message unique quel que soit le motif.
     */
    protected function reject(Request $request): Response
    {
        $message = (string) config('honeypot.message');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => ['form' => [$message]],
            ], 422);
        }

        return redirect()->back()
            ->withInput($request->except(array_merge(
                (array) config('honeypot.fields'),
                [config('honeypot.timestamp_field')],
            )))
            ->withErrors(['form' => $message]);
    }

    /**
     * Fausse réussite : indispensable sur /forgot-password.
     *
     * Renvoyer une erreur apprend au bot qu'il est filtré et lui permet de
     * contourner le piège. Ici on renvoie exactement la réponse d'un succès
     * sans déclencher l'envoi du mail, donc sans coût Brevo ni email bombing.
     */
    protected function fakeSuccess(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('passwords.sent'),
                'status' => __('passwords.sent'),
            ]);
        }

        return redirect()->back()->with('status', __('passwords.sent'));
    }
}
