<?php

use App\Services\SettingService;

if (! function_exists('setting')) {
    /**
     * Récupère ou définit une valeur de setting
     *
     * @param  string|array|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        $settingService = app(SettingService::class);

        if (is_null($key)) {
            return $settingService;
        }

        if (is_array($key)) {
            return $settingService->getMultiple($key, $default);
        }

        return $settingService->get($key, $default);
    }
}

if (! function_exists('settings')) {
    /**
     * Récupère plusieurs settings ou tous les settings publics
     *
     * @param  mixed  $default
     * @return Collection
     */
    function settings(?array $keys = null, $default = null)
    {
        $settingService = app(SettingService::class);

        if (is_null($keys)) {
            return $settingService->getPublicSettings();
        }

        return $settingService->getMultiple($keys, $default);
    }
}

if (! function_exists('setting_bool')) {
    /**
     * Récupère une valeur de setting comme boolean
     */
    function setting_bool(string $key, bool $default = false): bool
    {
        return app(SettingService::class)->getBool($key, $default);
    }
}

if (! function_exists('setting_int')) {
    /**
     * Récupère une valeur de setting comme integer
     */
    function setting_int(string $key, int $default = 0): int
    {
        return app(SettingService::class)->getInt($key, $default);
    }
}

if (! function_exists('setting_float')) {
    /**
     * Récupère une valeur de setting comme float
     */
    function setting_float(string $key, float $default = 0.0): float
    {
        return app(SettingService::class)->getFloat($key, $default);
    }
}

if (! function_exists('setting_string')) {
    /**
     * Récupère une valeur de setting comme string
     */
    function setting_string(string $key, string $default = ''): string
    {
        return app(SettingService::class)->getString($key, $default);
    }
}

if (! function_exists('setting_array')) {
    /**
     * Récupère une valeur de setting comme array
     */
    function setting_array(string $key, array $default = []): array
    {
        return app(SettingService::class)->getArray($key, $default);
    }
}

if (! function_exists('app_name')) {
    /**
     * Récupère le nom de l'application
     */
    function app_name(): string
    {
        return app(SettingService::class)->getAppName();
    }
}

if (! function_exists('commission_rate')) {
    /**
     * Récupère le taux de commission
     */
    function commission_rate(): float
    {
        return app(SettingService::class)->getCommissionRate();
    }
}

if (! function_exists('encaissement_fee_percentage')) {
    /**
     * Taux de frais d'encaissement (%) ajouté sur le prix de base à l'achat.
     * Inclus dans le prix affiché à l'acheteur : ce dernier ne voit jamais
     * le supplément, et le vendeur reçoit exactement le prix qu'il a fixé.
     */
    function encaissement_fee_percentage(): float
    {
        return app(SettingService::class)->getFloat('encaissement_fee_percentage', 4.5);
    }
}

if (! function_exists('buyer_price')) {
    /**
     * Prix affiché à l'acheteur : prix vendeur + supplément d'encaissement.
     * Calculé uniquement côté serveur ; les clients se contentent d'afficher le résultat.
     */
    function buyer_price(float $price): float
    {
        return round($price * (1 + encaissement_fee_percentage() / 100), 2);
    }
}

if (! function_exists('transport_fee_percentage')) {
    /**
     * Taux de frais de transport (%).
     */
    function transport_fee_percentage(): float
    {
        return (float) (DB::table('settings')->where('key', 'transport_fee_percentage')->value('value') ?? 5);
    }
}

if (! function_exists('cart_totals')) {
    /**
     * Totaux acheteur pour un panier (prix affichés = prix vendeur + supplément encaissement).
     *
     * @param  array  $cart  Lignes avec price/quantity (+currency ignorée ici)
     * @return array{subtotal: float, transport_fee_percentage: float, transport_fee: float, total: float}
     */
    function cart_totals(array $cart): array
    {
        $subtotal = 0.0;
        foreach ($cart as $item) {
            $line = buyer_price((float) ($item['price'] ?? 0)) * ($item['quantity'] ?? 1);
            $subtotal += $line;
        }
        $subtotal = round($subtotal, 2);

        $transportFeePercentage = transport_fee_percentage();
        $transportFee = round($subtotal * $transportFeePercentage / 100, 2);

        return [
            'subtotal' => $subtotal,
            'transport_fee_percentage' => $transportFeePercentage,
            'transport_fee' => $transportFee,
            'total' => round($subtotal + $transportFee, 2),
        ];
    }
}

if (! function_exists('withdrawal_fee_percentage')) {
    /**
     * Frais de retrait (%) prélevés par l'agrégateur sur chaque retrait.
     * La plateforme ne prélève rien : seule la part de l'agrégateur est estimée.
     */
    function withdrawal_fee_percentage(): float
    {
        return app(SettingService::class)->getFloat('withdrawal_fee_percentage', 0.0);
    }
}

if (! function_exists('withdrawal_fee_breakdown')) {
    /**
     * Détail d'un retrait : frais de l'agrégateur et montant net reçu par le vendeur.
     * Les frais d'encaissement ne sont jamais déduits au retrait.
     *
     * @return array{percentage: float, fee: float, net: float}
     */
    function withdrawal_fee_breakdown(float $amount): array
    {
        $percentage = withdrawal_fee_percentage();
        $fee = round($amount * $percentage / 100, 2);

        return [
            'percentage' => $percentage,
            'fee' => $fee,
            'net' => round($amount - $fee, 2),
        ];
    }
}

if (! function_exists('min_withdrawal_amount')) {
    /**
     * Récupère le montant minimum de retrait
     */
    function min_withdrawal_amount(): int
    {
        return app(SettingService::class)->getMinWithdrawalAmount();
    }
}

if (! function_exists('currency_symbol')) {
    /**
     * Symbole d'une devise de la plateforme
     *
     * La devise est portée par l'enregistrement (colonne `currency`), pas par
     * un réglage global : la plateforme est bilingue USD / CDF (FC). Toute valeur
     * inconnue ou nulle retombe sur USD, qui est le défaut des colonnes enum.
     */
    function currency_symbol(?string $currency): string
    {
        return $currency === 'CDF' ? 'FC' : '$';
    }
}

if (! function_exists('is_maintenance_mode')) {
    /**
     * Vérifie si l'application est en mode maintenance
     */
    function is_maintenance_mode(): bool
    {
        return app(SettingService::class)->isMaintenanceMode();
    }
}

if (! function_exists('is_registration_enabled')) {
    /**
     * Vérifie si l'inscription est activée
     */
    function is_registration_enabled(): bool
    {
        return app(SettingService::class)->isRegistrationEnabled();
    }
}

if (! function_exists('is_payment_enabled')) {
    /**
     * Vérifie si les paiements sont activés
     */
    function is_payment_enabled(): bool
    {
        return app(SettingService::class)->isPaymentEnabled();
    }
}

if (! function_exists('max_images_per_item')) {
    /**
     * Récupère le nombre maximum d'images par article
     */
    function max_images_per_item(): int
    {
        return app(SettingService::class)->getMaxImagesPerItem();
    }
}

if (! function_exists('settings_by_category')) {
    /**
     * Récupère les settings par catégorie
     *
     * @return Collection
     */
    function settings_by_category(string $category)
    {
        return app(SettingService::class)->getByCategory($category);
    }
}

if (! function_exists('app_logo')) {
    /**
     * Récupère le chemin du logo de l'application
     */
    function app_logo(): string
    {
        return setting_string('app_logo', '/images/logo.png');
    }
}

if (! function_exists('app_favicon')) {
    /**
     * Récupère le chemin du favicon
     */
    function app_favicon(): string
    {
        return setting_string('app_favicon', '/favicon.ico');
    }
}

if (! function_exists('app_description')) {
    /**
     * Récupère la description de l'application
     */
    function app_description(): string
    {
        return setting_string('app_description', 'Plateforme de vente vintage');
    }
}

if (! function_exists('max_upload_size')) {
    /**
     * Récupère la taille maximale d'upload en MB
     */
    function max_upload_size(): int
    {
        return setting_int('max_file_size', 10);
    }
}

if (! function_exists('allowed_file_types')) {
    /**
     * Récupère les types de fichiers autorisés
     */
    function allowed_file_types(): array
    {
        $types = setting_string('allowed_file_types', 'jpg,jpeg,png,gif,pdf');

        return array_map('trim', explode(',', $types));
    }
}

// =================================
// Helpers pour le système de couleurs VintApp
// =================================

use App\Helpers\ColorSystemHelper;
use App\Models\Cart;
use App\Models\DeliveryAddress;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

if (! function_exists('color_palette')) {
    /**
     * Récupère la palette de couleurs active
     */
    function color_palette($color = null)
    {
        $palette = ColorSystemHelper::getActivePalette();

        if ($color) {
            return "var(--color-{$color})";
        }

        return $palette;
    }
}

if (! function_exists('theme_color')) {
    /**
     * Génère un style inline avec une couleur thématique
     */
    function theme_color($property, $color, $variant = null)
    {
        $colorVar = $variant ? "{$color}-{$variant}" : $color;

        switch ($property) {
            case 'bg':
                return "style=\"background-color: var(--color-{$colorVar})\"";
            case 'text':
                return "style=\"color: var(--color-{$colorVar})\"";
            case 'border':
                return "style=\"border-color: var(--color-{$colorVar})\"";
            default:
                return "style=\"{$property}: var(--color-{$colorVar})\"";
        }
    }
}

if (! function_exists('dynamic_class')) {
    /**
     * Génère des classes Tailwind dynamiques basées sur la palette
     */
    function dynamic_class($type, $color, $variant = null)
    {
        // Les classes Tailwind utilisent directement nos couleurs définies
        $suffix = $variant ? "-{$variant}" : '';

        switch ($type) {
            case 'bg':
                return "bg-{$color}{$suffix}";
            case 'text':
                return "text-{$color}{$suffix}";
            case 'border':
                return "border-{$color}{$suffix}";
            case 'hover-bg':
                return "hover:bg-{$color}{$suffix}";
            case 'hover-text':
                return "hover:text-{$color}{$suffix}";
            default:
                return '';
        }
    }
}

if (! function_exists('cart_count')) {
    function cart_count(): int
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        return Cart::where(function ($q) use ($sessionId, $userId) {
            $q->where('session_id', $sessionId);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->sum('quantity');
    }
}

if (! function_exists('clear_cart')) {
    function clear_cart(): void
    {
        $sessionId = session()->getId();
        $userId = auth()->id();
        Cart::where(function ($q) use ($sessionId, $userId) {
            $q->where('session_id', $sessionId);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->delete();
    }
}

if (! function_exists('create_orders_from_transaction')) {
    function create_orders_from_transaction($transaction): array
    {
        $orders = [];

        if (! $transaction || $transaction->status !== 'completed') {
            return $orders;
        }

        $metadata = is_string($transaction->metadata)
            ? json_decode($transaction->metadata ?? '{}', true)
            : ($transaction->metadata ?? []);
        $cart = $metadata['cart'] ?? [];
        $deliveryAddressId = $metadata['delivery_address_id'] ?? null;
        $buyerId = $transaction->buyer_id ?? $transaction->user_id;
        $phone = $transaction->phone ?? null;

        if (empty($cart)) {
            return $orders;
        }

        // --- Idempotence : éviter de recréer les commandes si la transaction
        // a déjà été traitée (double callback, retry, force-complete + callback).
        $transactionMarker = 'Transaction #'.$transaction->id;
        $alreadyCreated = Order::where('buyer_id', $buyerId)
            ->where('notes', 'like', '%'.$transactionMarker.'%')
            ->exists();

        if ($alreadyCreated) {
            return $orders;
        }

        $deliveryAddress = $deliveryAddressId
            ? DeliveryAddress::find($deliveryAddressId)
            : DeliveryAddress::where('user_id', $buyerId)->where('is_default', true)->first();

        foreach ($cart as $itemId => $cartItem) {
            $item = Item::find($itemId);

            if (! $item) {
                continue;
            }

            $unitPrice = $item->activeOffer()
                ? (float) $item->activeOffer()->discountPriceFor($item)
                : (float) $item->price;

            $orderAmount = round($unitPrice * $cartItem['quantity'], 2);

            $seller = User::find($item->user_id);
            if (! $seller) {
                continue;
            }

            $sellerPendingWallet = Wallet::firstOrCreate(
                [
                    'user_id' => $seller->id,
                    'type' => 'pending',
                    'currency' => $item->currency,
                ],
                [
                    'balance' => 0,
                    'status' => 'active',
                    'is_active' => true,
                ]
            );

            $sellerPendingWallet->increment('balance', $orderAmount);

            $orderData = [
                'buyer_id' => $buyerId,
                'seller_id' => $item->user_id,
                'item_id' => $item->id,
                'quantity' => $cartItem['quantity'],
                'unit_price' => $unitPrice,
                'total_amount' => $orderAmount,
                'currency' => $item->currency,
                'status' => 'confirmed',
                'paid_at' => now(),
                'notes' => 'Paiement via '.($transaction->provider ?? 'VintApp').' - Transaction #'.$transaction->id,
            ];

            if ($deliveryAddress) {
                $orderData['delivery_address_id'] = $deliveryAddress->id;
                $orderData['shipping_address'] = $deliveryAddress->address;
                $orderData['shipping_city'] = $deliveryAddress->city;
                $orderData['shipping_phone'] = $deliveryAddress->phone;
            } else {
                $orderData['shipping_address'] = 'À définir';
                $orderData['shipping_city'] = 'À définir';
                $orderData['shipping_phone'] = $phone ?? 'N/A';
            }

            $order = Order::create($orderData);
            $orders[] = $order;

            try {
                app(NotificationService::class)->createOrderNotification(
                    $buyerId,
                    $item->user_id,
                    $item->name,
                    $order->id,
                    $order->order_number
                );
                app(NotificationService::class)->createOrderConfirmedNotification(
                    $buyerId,
                    $order->id,
                    $order->order_number,
                    $item->name
                );
            } catch (Throwable $e) {
                Log::error('Erreur notification commande (transaction): '.$e->getMessage());
            }

            $item->quantity -= $cartItem['quantity'];
            if ($item->quantity <= 0) {
                $item->status = 'sold';
            }
            $item->save();
        }

        clear_cart();

        return $orders;
    }
}

if (! function_exists('get_cart_array')) {
    function get_cart_array(): array
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        $cartRows = Cart::where(function ($q) use ($sessionId, $userId) {
            $q->where('session_id', $sessionId);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->get();

        $cart = [];
        foreach ($cartRows as $row) {
            $item = [
                'id' => $row->item_id,
                'name' => $row->item_name,
                'price' => (float) $row->price,
                'currency' => $row->currency,
                'quantity' => $row->quantity,
                'image' => $row->image,
            ];
            if ($row->has_discount) {
                $item['original_price'] = (float) $row->original_price;
                $item['discount_id'] = $row->discount_id;
                $item['discount_percentage'] = (float) $row->discount_percentage;
                $item['has_discount'] = true;
            }
            $cart[$row->item_id] = $item;
        }

        return $cart;
    }
}
