<?php

namespace App\Http\Controllers;

use App\Models\LocalDelivery;
use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LocalDeliveryController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Helper method pour retourner une réponse selon le type de requête
     */
    private function respond($request, $data, $redirectRoute = null, $status = 200)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($data, $status);
        } else {
            if (isset($data['error'])) {
                return back()->withInput()->withErrors(['error' => $data['error']]);
            } else {
                $route = $redirectRoute ?? 'local-delivery.user.seller';
                return redirect()->route($route)->with('success', $data['message'] ?? 'Opération réussie');
            }
        }
    }

    /**
     * Afficher les livraisons locales pour l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer les livraisons selon le rôle
        $deliveriesQuery = LocalDelivery::with(['order.item', 'seller', 'buyer']);
        
        if ($request->get('role') === 'seller') {
            $deliveriesQuery->where('seller_id', $user->id);
        } else {
            $deliveriesQuery->where('buyer_id', $user->id);
        }

        // Filtres
        if ($request->get('status')) {
            $deliveriesQuery->where('status', $request->get('status'));
        }

        if ($request->get('delivery_type')) {
            $deliveriesQuery->where('delivery_type', $request->get('delivery_type'));
        }

        $deliveries = $deliveriesQuery->latest()->paginate(10);

        return view('local-deliveries.index', compact('deliveries'));
    }

    /**
     * Afficher le formulaire de création d'une livraison locale
     */
    public function create()
    {
        $user = Auth::user();
        
        // Récupérer les commandes du vendeur qui n'ont pas encore de livraison locale
        $orders = Order::where('seller_id', $user->id)
            ->whereNotIn('id', function($query) {
                $query->select('order_id')->from('local_deliveries');
            })
            ->with(['buyer.deliveryAddresses', 'deliveryAddress', 'item'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ajouter les informations de géolocalisation pour chaque commande
        $orders->each(function($order) {
            // Prioriser l'adresse de livraison spécifique à la commande
            $primaryAddress = $order->deliveryAddress;
            
            // Si pas d'adresse spécifique ou pas de coordonnées, utiliser l'adresse par défaut de l'acheteur
            if (!$primaryAddress || (!$primaryAddress->latitude || !$primaryAddress->longitude)) {
                $primaryAddress = $order->buyer->deliveryAddresses()
                    ->where('is_default', true)
                    ->first();
            }
            
            // Si toujours pas de coordonnées, utiliser la première adresse disponible avec des coordonnées
            if (!$primaryAddress || (!$primaryAddress->latitude || !$primaryAddress->longitude)) {
                $primaryAddress = $order->buyer->deliveryAddresses()
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->first();
            }
            
            // Ajouter les informations calculées à la commande
            $order->buyer_address_data = [
                'address' => $primaryAddress->address ?? $order->shipping_address,
                'phone' => $primaryAddress->phone ?? $order->shipping_phone,
                'latitude' => $primaryAddress->latitude ?? null,
                'longitude' => $primaryAddress->longitude ?? null,
                'has_coordinates' => ($primaryAddress && $primaryAddress->latitude && $primaryAddress->longitude),
                'source' => $primaryAddress ? ($primaryAddress->id == $order->delivery_address_id ? 'order' : 'user_default') : 'manual'
            ];
        });

        return view('local-delivery.create', compact('orders'));
    }

    /**
     * Afficher les détails d'une livraison locale
     */
    public function show(LocalDelivery $localDelivery)
    {
        // Vérifier que l'utilisateur peut voir cette livraison
        $user = Auth::user();
        if ($localDelivery->seller_id !== $user->id && $localDelivery->buyer_id !== $user->id) {
            abort(403, 'Non autorisé à voir cette livraison');
        }

        // Charger les relations nécessaires
        $localDelivery->load(['order', 'seller', 'buyer']);

        return view('local-delivery.show', compact('localDelivery'));
    }

    /**
     * Proposer une livraison locale pour une commande
     */
    public function proposeDelivery(Request $request)
    {
        try {
            // Debug : loguer les données reçues
            Log::info('Données reçues pour proposition de livraison:', $request->all());
            
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'delivery_type' => 'required|in:hand_delivery,pickup,meetup',
                'seller_latitude' => 'required|numeric|between:-90,90',
                'seller_longitude' => 'required|numeric|between:-180,180',
                'seller_address' => 'required|string|max:500',
                'seller_phone' => 'required|string|max:20',
                'buyer_latitude' => 'required|numeric|between:-90,90',
                'buyer_longitude' => 'required|numeric|between:-180,180',
                'buyer_address' => 'required|string|max:500',
                'buyer_phone' => 'nullable|string|max:20',
                'delivery_instructions' => 'nullable|string|max:1000',
                'estimated_delivery_time' => 'nullable|date|after:now'
            ]);

            // Récupérer la commande
            $order = Order::findOrFail($request->order_id);

            // Vérifier que l'utilisateur est le vendeur
            if ($order->seller_id !== Auth::id()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            // Vérifier qu'il n'y a pas déjà une livraison locale pour cette commande
            if ($order->localDelivery) {
                return response()->json(['error' => 'Une livraison locale existe déjà pour cette commande'], 400);
            }

            // Calculer la distance avec les coordonnées fournies
            $distance = LocalDelivery::calculateDistance(
                $request->seller_latitude,
                $request->seller_longitude,
                $request->buyer_latitude,
                $request->buyer_longitude
            );

            // Vérifier l'éligibilité (max 50km)
            if ($distance > 50) {
                return response()->json([
                    'error' => "La distance ({$distance}km) dépasse la limite autorisée de 50km"
                ], 400);
            }

            // Créer la livraison locale
            $localDelivery = LocalDelivery::create([
                'order_id' => $order->id,
                'seller_id' => $order->seller_id,
                'buyer_id' => $order->buyer_id,
                'delivery_type' => $request->delivery_type,
                'status' => 'proposed',
                
                // Coordonnées vendeur
                'seller_latitude' => $request->seller_latitude,
                'seller_longitude' => $request->seller_longitude,
                'seller_address' => $request->seller_address,
                'seller_phone' => $request->seller_phone,
                
                // Coordonnées acheteur (depuis le formulaire)
                'buyer_latitude' => $request->buyer_latitude,
                'buyer_longitude' => $request->buyer_longitude,
                'buyer_address' => $request->buyer_address,
                'buyer_phone' => $request->buyer_phone,
                
                // Calculs
                'distance_km' => $distance,
                'delivery_fee' => $this->calculateDeliveryFee($distance),
                'currency' => 'CDF',
                
                // Planning
                'estimated_delivery_time' => $request->estimated_delivery_time,
                'delivery_instructions' => $request->delivery_instructions,
                'delivery_code' => strtoupper(\Illuminate\Support\Str::random(6))
            ]);

            // Notifier l'acheteur
            $this->notificationService->createLocalDeliveryProposedNotification($localDelivery);

            // Déterminer le type de réponse selon le type de requête
            if ($request->expectsJson() || $request->ajax()) {
                // Réponse JSON pour les requêtes API/AJAX
                return response()->json([
                    'success' => true,
                    'message' => 'Livraison locale proposée avec succès',
                    'delivery' => $localDelivery->load(['seller', 'buyer'])
                ]);
            } else {
                // Redirection pour les formulaires web
                return redirect()->route('local-delivery.show', $localDelivery)
                    ->with('success', 'Livraison locale proposée avec succès !');
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors de la proposition de livraison locale: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Erreur lors de la proposition: ' . $e->getMessage()
                ], 500);
            } else {
                return back()->withInput()->withErrors(['error' => 'Erreur lors de la proposition: ' . $e->getMessage()]);
            }
        }
    }

    /**
     * Accepter une livraison locale (acheteur)
     */
    public function acceptDelivery(Request $request, LocalDelivery $localDelivery)
    {
        try {
            // Vérifier que l'utilisateur est l'acheteur
            if ($localDelivery->buyer_id !== Auth::id()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            if ($localDelivery->status !== 'pending') {
                return response()->json(['error' => 'Cette livraison ne peut plus être acceptée'], 400);
            }

            $localDelivery->update([
                'status' => 'accepted',
                'buyer_confirmed' => true
            ]);

            // Notifier le vendeur
            $this->notificationService->createLocalDeliveryAcceptedNotification($localDelivery);

            return response()->json([
                'success' => true,
                'message' => 'Livraison locale acceptée',
                'delivery' => $localDelivery
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'acceptation de livraison: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'acceptation'], 500);
        }
    }

    /**
     * Marquer comme en transit (vendeur)
     */
    public function markInTransit(Request $request, LocalDelivery $localDelivery)
    {
        try {
            if ($localDelivery->seller_id !== Auth::id()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            if ($localDelivery->status !== 'accepted') {
                return response()->json(['error' => 'La livraison doit être acceptée d\'abord'], 400);
            }

            $localDelivery->update([
                'status' => 'in_transit',
                'actual_pickup_time' => now()
            ]);

            // Notifier l'acheteur
            $this->notificationService->createLocalDeliveryInTransitNotification($localDelivery);

            return response()->json([
                'success' => true,
                'message' => 'Livraison marquée en transit',
                'delivery' => $localDelivery
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage en transit: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du marquage'], 500);
        }
    }

    /**
     * Marquer comme livrée avec code de vérification
     */
    public function markDelivered(Request $request, LocalDelivery $localDelivery)
    {
        try {
            $request->validate([
                'delivery_code' => 'required|string|size:6'
            ]);

            if ($localDelivery->seller_id !== Auth::id()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            if ($localDelivery->status !== 'in_transit') {
                return response()->json(['error' => 'La livraison doit être en transit'], 400);
            }

            if (strtoupper($request->delivery_code) !== strtoupper($localDelivery->delivery_code)) {
                return response()->json(['error' => 'Code de vérification incorrect'], 400);
            }

            $localDelivery->update([
                'status' => 'delivered',
                'actual_delivery_time' => now(),
                'seller_confirmed' => true
            ]);

            // Marquer la commande comme livrée
            $localDelivery->order->update(['status' => 'delivered']);

            // Notifier l'acheteur
            $this->notificationService->createLocalDeliveryDeliveredNotification($localDelivery);

            return response()->json([
                'success' => true,
                'message' => 'Livraison confirmée avec succès',
                'delivery' => $localDelivery
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la confirmation de livraison: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la confirmation'], 500);
        }
    }

    /**
     * Annuler une livraison locale
     */
    public function cancelDelivery(Request $request, LocalDelivery $localDelivery)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            // Vérifier les autorisations
            if ($localDelivery->seller_id !== Auth::id() && $localDelivery->buyer_id !== Auth::id()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            if (in_array($localDelivery->status, ['delivered', 'cancelled'])) {
                return response()->json(['error' => 'Cette livraison ne peut plus être annulée'], 400);
            }

            $localDelivery->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->reason
            ]);

            // Notifier l'autre partie
            if ($localDelivery->seller_id === Auth::id()) {
                $this->notificationService->createLocalDeliveryCancelledNotification($localDelivery, 'buyer');
            } else {
                $this->notificationService->createLocalDeliveryCancelledNotification($localDelivery, 'seller');
            }

            return response()->json([
                'success' => true,
                'message' => 'Livraison annulée',
                'delivery' => $localDelivery
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'annulation: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'annulation'], 500);
        }
    }

    /**
     * Rechercher des vendeurs/acheteurs proches
     */
    public function findNearbyUsers(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius_km' => 'nullable|integer|min:1|max:50',
                'role' => 'required|in:seller,buyer'
            ]);

            $lat = $request->latitude;
            $lng = $request->longitude;
            $radius = $request->radius_km ?? 10; // Par défaut 10km

            // Rechercher les utilisateurs proches avec des adresses GPS
            $query = User::join('delivery_addresses', 'users.id', '=', 'delivery_addresses.user_id')
                ->whereNotNull('delivery_addresses.latitude')
                ->whereNotNull('delivery_addresses.longitude')
                ->where('delivery_addresses.is_default', true)
                ->where('users.id', '!=', Auth::id())
                ->select('users.*', 'delivery_addresses.latitude', 'delivery_addresses.longitude', 
                         'delivery_addresses.city', 'delivery_addresses.commune', 'delivery_addresses.address');

            // Calcul de distance avec la formule Haversine
            $query->selectRaw(
                "(6371 * acos(cos(radians(?)) * cos(radians(delivery_addresses.latitude)) * 
                cos(radians(delivery_addresses.longitude) - radians(?)) + 
                sin(radians(?)) * sin(radians(delivery_addresses.latitude)))) AS distance",
                [$lat, $lng, $lat]
            );

            $nearbyUsers = $query->having('distance', '<=', $radius)
                ->orderBy('distance')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'users' => $nearbyUsers,
                'count' => $nearbyUsers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche d\'utilisateurs proches: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la recherche'], 500);
        }
    }

    /**
     * Calculer les frais de livraison
     */
    private function calculateDeliveryFee(float $distanceKm): float
    {
        // Tarification: 2$ pour les 5 premiers km, puis 0.5$ par km
        $baseFee = 2.00;
        $perKmFee = 0.50;
        $freeDistanceKm = 5;

        if ($distanceKm <= $freeDistanceKm) {
            return $baseFee;
        }

        return $baseFee + (($distanceKm - $freeDistanceKm) * $perKmFee);
    }

        /**
     * Afficher les livraisons d'un utilisateur par type (seller/buyer)
     */
    public function getUserDeliveries($type)
    {
        $user = Auth::user();
        
        if ($type === 'seller') {
            $deliveries = LocalDelivery::where('seller_id', $user->id)
                ->with(['order', 'buyer'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
            $counts = [
                'seller' => LocalDelivery::where('seller_id', $user->id)->count(),
                'buyer' => LocalDelivery::where('buyer_id', $user->id)->count()
            ];
        } else {
            $deliveries = LocalDelivery::where('buyer_id', $user->id)
                ->with(['order', 'seller'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
            $counts = [
                'seller' => LocalDelivery::where('seller_id', $user->id)->count(),
                'buyer' => LocalDelivery::where('buyer_id', $user->id)->count()
            ];
        }

        return view('local-delivery.index', compact('deliveries', 'counts'));
    }

    /**
     * API pour géocoder une adresse (obtenir latitude/longitude)
     */
    public function geocodeAddress(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:500'
        ]);

        $address = $request->input('address');
        
        // Ici vous pourriez utiliser un service de géocodage comme Google Maps, OpenStreetMap, etc.
        // Pour l'instant, nous retournons une réponse indiquant que le service n'est pas configuré
        
        return response()->json([
            'success' => false,
            'message' => 'Service de géocodage non configuré. Veuillez utiliser la géolocalisation GPS ou saisir les coordonnées manuellement.',
            'suggestion' => 'Utilisez le bouton "Obtenir ma position" pour obtenir vos coordonnées GPS automatiquement.'
        ]);
    }
}
