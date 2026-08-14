<?php

use App\Services\SettingService;

if (!function_exists('setting')) {
    /**
     * Récupère ou définit une valeur de setting
     * 
     * @param string|array|null $key
     * @param mixed $default
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

if (!function_exists('settings')) {
    /**
     * Récupère plusieurs settings ou tous les settings publics
     * 
     * @param array|null $keys
     * @param mixed $default
     * @return \Illuminate\Support\Collection
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

if (!function_exists('setting_bool')) {
    /**
     * Récupère une valeur de setting comme boolean
     * 
     * @param string $key
     * @param bool $default
     * @return bool
     */
    function setting_bool(string $key, bool $default = false): bool
    {
        return app(SettingService::class)->getBool($key, $default);
    }
}

if (!function_exists('setting_int')) {
    /**
     * Récupère une valeur de setting comme integer
     * 
     * @param string $key
     * @param int $default
     * @return int
     */
    function setting_int(string $key, int $default = 0): int
    {
        return app(SettingService::class)->getInt($key, $default);
    }
}

if (!function_exists('setting_float')) {
    /**
     * Récupère une valeur de setting comme float
     * 
     * @param string $key
     * @param float $default
     * @return float
     */
    function setting_float(string $key, float $default = 0.0): float
    {
        return app(SettingService::class)->getFloat($key, $default);
    }
}

if (!function_exists('setting_string')) {
    /**
     * Récupère une valeur de setting comme string
     * 
     * @param string $key
     * @param string $default
     * @return string
     */
    function setting_string(string $key, string $default = ''): string
    {
        return app(SettingService::class)->getString($key, $default);
    }
}

if (!function_exists('setting_array')) {
    /**
     * Récupère une valeur de setting comme array
     * 
     * @param string $key
     * @param array $default
     * @return array
     */
    function setting_array(string $key, array $default = []): array
    {
        return app(SettingService::class)->getArray($key, $default);
    }
}

if (!function_exists('app_name')) {
    /**
     * Récupère le nom de l'application
     * 
     * @return string
     */
    function app_name(): string
    {
        return app(SettingService::class)->getAppName();
    }
}

if (!function_exists('commission_rate')) {
    /**
     * Récupère le taux de commission
     * 
     * @return float
     */
    function commission_rate(): float
    {
        return app(SettingService::class)->getCommissionRate();
    }
}

if (!function_exists('min_withdrawal_amount')) {
    /**
     * Récupère le montant minimum de retrait
     * 
     * @return int
     */
    function min_withdrawal_amount(): int
    {
        return app(SettingService::class)->getMinWithdrawalAmount();
    }
}

if (!function_exists('is_maintenance_mode')) {
    /**
     * Vérifie si l'application est en mode maintenance
     * 
     * @return bool
     */
    function is_maintenance_mode(): bool
    {
        return app(SettingService::class)->isMaintenanceMode();
    }
}

if (!function_exists('is_registration_enabled')) {
    /**
     * Vérifie si l'inscription est activée
     * 
     * @return bool
     */
    function is_registration_enabled(): bool
    {
        return app(SettingService::class)->isRegistrationEnabled();
    }
}

if (!function_exists('is_payment_enabled')) {
    /**
     * Vérifie si les paiements sont activés
     * 
     * @return bool
     */
    function is_payment_enabled(): bool
    {
        return app(SettingService::class)->isPaymentEnabled();
    }
}

if (!function_exists('max_images_per_item')) {
    /**
     * Récupère le nombre maximum d'images par article
     * 
     * @return int
     */
    function max_images_per_item(): int
    {
        return app(SettingService::class)->getMaxImagesPerItem();
    }
}

if (!function_exists('settings_by_category')) {
    /**
     * Récupère les settings par catégorie
     * 
     * @param string $category
     * @return \Illuminate\Support\Collection
     */
    function settings_by_category(string $category)
    {
        return app(SettingService::class)->getByCategory($category);
    }
}

if (!function_exists('app_logo')) {
    /**
     * Récupère le chemin du logo de l'application
     * 
     * @return string
     */
    function app_logo(): string
    {
        return setting_string('app_logo', '/images/logo.png');
    }
}

if (!function_exists('app_favicon')) {
    /**
     * Récupère le chemin du favicon
     * 
     * @return string
     */
    function app_favicon(): string
    {
        return setting_string('app_favicon', '/favicon.ico');
    }
}

if (!function_exists('app_description')) {
    /**
     * Récupère la description de l'application
     * 
     * @return string
     */
    function app_description(): string
    {
        return setting_string('app_description', 'Plateforme de vente vintage');
    }
}

if (!function_exists('max_upload_size')) {
    /**
     * Récupère la taille maximale d'upload en MB
     * 
     * @return int
     */
    function max_upload_size(): int
    {
        return setting_int('max_file_size', 10);
    }
}

if (!function_exists('allowed_file_types')) {
    /**
     * Récupère les types de fichiers autorisés
     * 
     * @return array
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

if (!function_exists('color_palette')) {
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

if (!function_exists('theme_color')) {
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

if (!function_exists('dynamic_class')) {
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

if (!function_exists('cart_count')) {
    function cart_count(): int
    {
        $sessionId = session()->getId();
        $userId = auth()->id();
        return \App\Models\Cart::where(function ($q) use ($sessionId, $userId) {
            $q->where('session_id', $sessionId);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->sum('quantity');
    }
}

if (!function_exists('clear_cart')) {
    function clear_cart(): void
    {
        $sessionId = session()->getId();
        $userId = auth()->id();
        \App\Models\Cart::where(function ($q) use ($sessionId, $userId) {
            $q->where('session_id', $sessionId);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->delete();
    }
}

if (!function_exists('create_orders_from_transaction')) {
    function create_orders_from_transaction($transaction): array
    {
        $orders = [];

        if (!$transaction || $transaction->status !== 'completed') {
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

        $deliveryAddress = $deliveryAddressId
            ? \App\Models\DeliveryAddress::find($deliveryAddressId)
            : \App\Models\DeliveryAddress::where('user_id', $buyerId)->where('is_default', true)->first();

        foreach ($cart as $itemId => $cartItem) {
            $item = \App\Models\Item::find($itemId);

            if (!$item) {
                continue;
            }

            $orderAmount = $item->price * $cartItem['quantity'];

            $seller = \App\Models\User::find($item->user_id);
            if (!$seller) {
                continue;
            }

            $sellerPendingWallet = \App\Models\Wallet::firstOrCreate(
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
                'unit_price' => $item->price,
                'total_amount' => $orderAmount,
                'currency' => $item->currency,
                'status' => 'confirmed',
                'paid_at' => now(),
                'notes' => 'Paiement via ' . ($transaction->provider ?? 'VintApp') . ' - Transaction #' . $transaction->id,
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

            $order = \App\Models\Order::create($orderData);
            $orders[] = $order;

            try {
                app(\App\Services\NotificationService::class)->createOrderNotification(
                    $buyerId,
                    $item->user_id,
                    $item->name,
                    $order->id,
                    $order->order_number
                );
                app(\App\Services\NotificationService::class)->createOrderConfirmedNotification(
                    $buyerId,
                    $order->id,
                    $order->order_number,
                    $item->name
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Erreur notification commande (transaction): ' . $e->getMessage());
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

if (!function_exists('get_cart_array')) {
    function get_cart_array(): array
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        $cartRows = \App\Models\Cart::where(function ($q) use ($sessionId, $userId) {
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