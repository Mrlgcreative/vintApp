<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * POST /firebase/register est le seul point d'entrée d'inscription public qui
 * n'était pas protégé par un rate limiter, alors que chaque appel réussi crée
 * un compte ET déclenche un email Brevo.
 *
 * On ne peut pas produire un vrai idToken Firebase dans un test, mais le
 * middleware de throttling s'exécute avant le contrôleur et incrémente son
 * compteur à chaque requête, quelle que soit la réponse du contrôleur.
 */
class FirebaseRegisterThrottleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Budget défini par RateLimiter::for('auth.firebase') dans
     * app/Providers/RouteServiceProvider.php — à garder synchronisé.
     */
    private const BUDGET_PER_HOUR = 10;

    public function test_firebase_registration_is_rate_limited_per_ip(): void
    {
        // Le contrôleur avale la ValidationException et renvoie 500
        // (bug préexistant, cf. FirebaseAuthController::registerWithFirebase) ;
        // on n'affirme donc que l'absence de 429, ce qui prouve que le
        // compteur du limiteur tourne.
        for ($attempt = 1; $attempt <= self::BUDGET_PER_HOUR; $attempt++) {
            $response = $this->postJson('/firebase/register', []);

            $this->assertNotSame(
                429,
                $response->getStatusCode(),
                "La tentative {$attempt} n'aurait pas dû être bloquée."
            );
        }

        $this->postJson('/firebase/register', [])
            ->assertStatus(429);
    }

    public function test_the_firebase_login_rate_limit_is_still_its_own(): void
    {
        // Le login Firebase garde son limiteur dédié (throttle.login),
        // indépendant du nouveau quota d'inscription.
        $this->postJson('/firebase/login', [])
            ->assertStatus(422);
    }
}
