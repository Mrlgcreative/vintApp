<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'VintApp API is running',
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String()
        ]);
    }

    public function currencies(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'code' => 'USD',
                    'name' => 'Dollar américain',
                    'symbol' => '$',
                    'flag' => '🇺🇸'
                ],
                [
                    'code' => 'CDF',
                    'name' => 'Franc congolais',
                    'symbol' => 'FC',
                    'flag' => '🇨🇩'
                ],
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function fees(): JsonResponse
    {
        $commissionPercentage = (float) (DB::table('settings')
            ->where('key', 'platform_commission_percentage')
            ->value('value') ?? 10);

        $transportFeePercentage = (float) (DB::table('settings')
            ->where('key', 'transport_fee_percentage')
            ->value('value') ?? 5);

        return response()->json([
            'success' => true,
            'data' => [
                'commission_percentage' => $commissionPercentage,
                'transport_fee_percentage' => $transportFeePercentage,
            ],
        ]);
    }

    /**
     * RGPD — catalogue pour l'écran "Confidentialité / mes données" du mobile :
     * catégories de données portables, droits, liens légaux et actions disponibles.
     */
    public function privacy(): JsonResponse
    {
        $baseUrl = rtrim(config('app.url'), '/');

        return response()->json([
            'success' => true,
            'data' => [
                'title' => 'Vie privée et données personnelles',
                'description' => 'Conformément au RGPD, vous pouvez consulter, exporter ou supprimer vos données personnelles à tout moment.',
                'portability' => [
                    'label' => 'Portabilité de vos données (RGPD)',
                    'description' => 'Conformément à l\'article 20 du RGPD, récupérez l\'ensemble de vos données personnelles dans un format structuré, couramment utilisé et lisible par machine (JSON).',
                    'categories' => [
                        ['key' => 'identite', 'label' => 'Identité et profil'],
                        ['key' => 'roles', 'label' => 'Rôles et permissions'],
                        ['key' => 'localisation_vendeur', 'label' => 'Localisation vendeur'],
                        ['key' => 'adresses_livraison', 'label' => 'Adresses de livraison'],
                        ['key' => 'annonces_produits', 'label' => 'Annonces / produits'],
                        ['key' => 'favoris', 'label' => 'Favoris'],
                        ['key' => 'commandes', 'label' => 'Commandes (achats et ventes)'],
                        ['key' => 'portefeuille', 'label' => 'Portefeuille et transactions'],
                        ['key' => 'messages', 'label' => 'Messages échangés'],
                        ['key' => 'avis_evaluations', 'label' => 'Avis & évaluations'],
                        ['key' => 'notifications', 'label' => 'Notifications reçues'],
                        ['key' => 'sessions_connexion', 'label' => 'Sessions et connexions'],
                    ],
                ],
                'rights' => [
                    ['key' => 'access', 'article' => '13-15', 'label' => 'Droit d\'accès', 'description' => 'Obtenir une copie de vos données.'],
                    ['key' => 'rectification', 'article' => '16', 'label' => 'Droit de rectification', 'description' => 'Corriger des données inexactes.'],
                    ['key' => 'erasure', 'article' => '17', 'label' => 'Droit à l\'effacement', 'description' => 'Supprimer votre compte et vos données.'],
                    ['key' => 'portability', 'article' => '20', 'label' => 'Droit à la portabilité', 'description' => 'Récupérer vos données dans un format lisible par machine.'],
                    ['key' => 'restriction', 'article' => '18', 'label' => 'Droit à la limitation', 'description' => 'Limiter le traitement de vos données.'],
                    ['key' => 'objection', 'article' => '21', 'label' => 'Droit d\'opposition', 'description' => 'Vous opposer au traitement de vos données.'],
                ],
                'actions' => [
                    [
                        'key' => 'export_data',
                        'label' => 'Télécharger mes données',
                        'description' => 'Téléchargement de toutes vos données personnelles au format JSON.',
                        'method' => 'GET',
                        'endpoint' => '/api/v1/data-export/download',
                        'auth' => true,
                    ],
                    [
                        'key' => 'preview_data',
                        'label' => 'Aperçu de mes données',
                        'description' => 'Consulter un aperçu structuré de vos données.',
                        'method' => 'GET',
                        'endpoint' => '/api/v1/data-export',
                        'auth' => true,
                    ],
                    [
                        'key' => 'delete_account',
                        'label' => 'Supprimer mon compte',
                        'description' => 'Suppression définitive du compte et de ses données.',
                        'method' => 'DELETE',
                        'endpoint' => '/api/user/account',
                        'auth' => true,
                    ],
                ],
                'links' => [
                    ['key' => 'privacy_policy', 'label' => 'Politique de confidentialité', 'url' => $baseUrl . '/privacy'],
                    ['key' => 'terms', 'label' => 'Conditions générales', 'url' => $baseUrl . '/terms'],
                ],
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
