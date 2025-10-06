<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter les paramètres de pré-inscription
        $settings = [
            [
                'key' => 'preregistration_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'preregistration',
                'label' => 'Activer la pré-inscription',
                'description' => 'Permet aux utilisateurs de se pré-inscrire avant le lancement officiel de l\'application',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'preregistration_title',
                'value' => 'Rejoignez-nous en avant-première !',
                'type' => 'string',
                'category' => 'preregistration',
                'label' => 'Titre de la page',
                'description' => 'Titre principal affiché sur la page de pré-inscription',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'preregistration_subtitle',
                'value' => 'Inscrivez-vous maintenant et soyez parmi les premiers à découvrir notre plateforme',
                'type' => 'string',
                'category' => 'preregistration',
                'label' => 'Sous-titre de la page',
                'description' => 'Sous-titre descriptif affiché sous le titre principal',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'preregistration_message',
                'value' => 'Nous préparons quelque chose de spécial pour vous. Pré-inscrivez-vous dès maintenant pour être notifié lors du lancement et bénéficier d\'avantages exclusifs !',
                'type' => 'text',
                'category' => 'preregistration',
                'label' => 'Message d\'accueil',
                'description' => 'Message détaillé expliquant les avantages de la pré-inscription',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'preregistration_benefits',
                'value' => json_encode([
                    'Accès prioritaire lors du lancement',
                    'Bonus de bienvenue exclusif',
                    'Notifications en avant-première',
                ]),
                'type' => 'json',
                'category' => 'preregistration',
                'label' => 'Avantages de la pré-inscription',
                'description' => 'Liste des avantages offerts aux pré-inscrits (format JSON array)',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'preregistration_limit',
                'value' => '0',
                'type' => 'integer',
                'category' => 'preregistration',
                'label' => 'Limite de pré-inscriptions',
                'description' => 'Nombre maximum de pré-inscriptions autorisées (0 = illimité)',
                'is_public' => false,
                'is_encrypted' => false,
            ],
            [
                'key' => 'preregistration_require_phone',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'preregistration',
                'label' => 'Téléphone obligatoire',
                'description' => 'Rendre le champ téléphone obligatoire dans le formulaire',
                'is_public' => false,
                'is_encrypted' => false,
            ],
            [
                'key' => 'preregistration_require_confirmation',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'preregistration',
                'label' => 'Confirmation email obligatoire',
                'description' => 'Les utilisateurs doivent confirmer leur email avant validation',
                'is_public' => false,
                'is_encrypted' => false,
            ],
            [
                'key' => 'preregistration_notification_email',
                'value' => 'admin@vintapp.com',
                'type' => 'email',
                'category' => 'preregistration',
                'label' => 'Email de notification admin',
                'description' => 'Email qui recevra les notifications de nouvelles pré-inscriptions',
                'is_public' => false,
                'is_encrypted' => false,
            ],
            [
                'key' => 'preregistration_closed_message',
                'value' => 'Les pré-inscriptions sont actuellement fermées. Merci de votre intérêt !',
                'type' => 'text',
                'category' => 'preregistration',
                'label' => 'Message de fermeture',
                'description' => 'Message affiché lorsque les pré-inscriptions sont désactivées',
                'is_public' => true,
                'is_encrypted' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer tous les paramètres de pré-inscription
        Setting::where('category', 'preregistration')->delete();
    }
};
