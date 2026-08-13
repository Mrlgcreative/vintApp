# Body des endpoints API — vintApp

Généré le 2026-08-08 21:38 — 231 routes. Endpoints **sans body** (GET/HEAD/DELETE sans champs) : aucune section body, uniquement la table si des champs sont lus en query.

> Les exemples JSON sont générés automatiquement depuis les règles de validation du code (types et valeurs indicatives). Les champs `file` doivent être envoyés en `multipart/form-data`.

## `GET /api/v1/admin/affiliate/activity`

**Action** : `App\Http\Controllers\Admin\AffiliateController@getRecentActivity`

_Pas de body (GET)._

## `GET /api/v1/admin/affiliate/referrers`

**Action** : `App\Http\Controllers\Admin\AffiliateController@getReferrers`

_Pas de body (GET)._

## `GET /api/v1/admin/affiliate/stats`

**Action** : `App\Http\Controllers\Admin\AffiliateController@getDashboardStats`

_Pas de body (GET)._

## `GET /api/v1/admin/affiliate/top-performers`

**Action** : `App\Http\Controllers\Admin\AffiliateController@getTopPerformers`

_Pas de body (GET)._

## `GET /api/v1/admin/brands`

**Action** : `App\Http\Controllers\Admin\AdminController@apiBrands`

_Pas de body (GET)._

## `GET /api/v1/admin/categories`

**Action** : `App\Http\Controllers\Admin\AdminController@apiCategories`

_Pas de body (GET)._

## `GET /api/v1/admin/dashboard`

**Action** : `App\Http\Controllers\Admin\AdminController@apiDashboard`

_Pas de body (GET)._

## `GET /api/v1/admin/enterprise-wallets`

**Action** : `App\Http\Controllers\Admin\WalletController@apiIndex`

_Pas de body (GET)._

## `GET /api/v1/admin/enterprise-wallets/{wallet}`

**Action** : `App\Http\Controllers\Admin\WalletController@apiShow`

**Paramètres d'URL** :
- `{wallet}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/admin/items`

**Action** : `App\Http\Controllers\Admin\AdminController@apiItems`

_Pas de body (GET)._

## `POST /api/v1/admin/items/{itemId}/status`

**Action** : `App\Http\Controllers\Admin\AdminController@apiItemUpdateStatus`

**Paramètres d'URL** :
- `{itemId}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/v1/admin/monitoring/health`

**Action** : `App\Http\Controllers\Admin\MonitoringController@health`

_Pas de body (GET)._

## `GET /api/v1/admin/monitoring/stats`

**Action** : `App\Http\Controllers\Admin\MonitoringController@stats`

_Pas de body (GET)._

## `GET /api/v1/admin/notifications`

**Action** : `App\Http\Controllers\Admin\AdminController@apiNotifications`

_Pas de body (GET)._

## `GET /api/v1/admin/online-users`

**Action** : `App\Http\Controllers\Admin\AdminController@apiOnlineUsers`

_Pas de body (GET)._

## `GET /api/v1/admin/orders`

**Action** : `App\Http\Controllers\Admin\AdminController@apiOrders`

_Pas de body (GET)._

## `GET /api/v1/admin/refunds`

**Action** : `App\Http\Controllers\Admin\RefundController@apiIndex`

_Pas de body (GET)._

## `GET /api/v1/admin/refunds/{refund}`

**Action** : `App\Http\Controllers\Admin\RefundController@apiShow`

**Paramètres d'URL** :
- `{refund}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/admin/reports`

**Action** : `App\Http\Controllers\Admin\AdminController@apiReports`

_Pas de body (GET)._

## `GET /api/v1/admin/settings`

**Action** : `App\Http\Controllers\Admin\AdminController@apiSettings`

_Pas de body (GET)._

## `PUT /api/v1/admin/settings/{key}`

**Action** : `App\Http\Controllers\Admin\AdminController@apiUpdateSetting`

**Paramètres d'URL** :
- `{key}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/v1/admin/stats/summary`

**Action** : `App\Http\Controllers\Admin\AdminController@apiStatsSummary`

_Pas de body (GET)._

## `GET /api/v1/admin/support`

**Action** : `App\Http\Controllers\Admin\SupportController@apiIndex`

_Pas de body (GET)._

## `GET /api/v1/admin/support-chats`

**Action** : `App\Http\Controllers\Admin\AdminController@apiSupportChats`

_Pas de body (GET)._

## `GET /api/v1/admin/support/stats`

**Action** : `App\Http\Controllers\Admin\SupportController@apiStats`

_Pas de body (GET)._

## `GET /api/v1/admin/support/{supportChat}`

**Action** : `App\Http\Controllers\Admin\SupportController@apiShow`

**Paramètres d'URL** :
- `{supportChat}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/admin/transactions`

**Action** : `App\Http\Controllers\Admin\AdminController@apiTransactions`

_Pas de body (GET)._

## `GET /api/v1/admin/users`

**Action** : `App\Http\Controllers\Admin\AdminController@apiUsers`

_Pas de body (GET)._

## `GET /api/v1/admin/users/{userId}`

**Action** : `App\Http\Controllers\Admin\AdminController@apiUserShow`

**Paramètres d'URL** :
- `{userId}` (obligatoire)

_Pas de body (GET)._

## `POST /api/v1/admin/users/{userId}/status`

**Action** : `App\Http\Controllers\Admin\AdminController@apiUserUpdateStatus`

**Paramètres d'URL** :
- `{userId}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/v1/admin/verification-checks`

**Action** : `App\Http\Controllers\Admin\AdminController@apiVerificationChecks`

_Pas de body (GET)._

## `GET /api/v1/admin/waiting-users`

**Action** : `App\Http\Controllers\Admin\WaitingUsersController@apiIndex`

_Pas de body (GET)._

## `GET /api/v1/admin/waiting-users/stats`

**Action** : `App\Http\Controllers\Admin\WaitingUsersController@apiStats`

_Pas de body (GET)._

## `POST /api/v1/admin/waiting-users/{waitingUser}/approve`

**Action** : `App\Http\Controllers\Admin\WaitingUsersController@apiApprove`

**Paramètres d'URL** :
- `{waitingUser}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/v1/admin/wallets`

**Action** : `App\Http\Controllers\Admin\AdminController@apiWallets`

_Pas de body (GET)._

## `POST /api/v1/admin/wallets/bulk-approve`

**Action** : `App\Http\Controllers\Admin\AdminController@apiBulkApproveWallets`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/v1/admin/wallets/pending`

**Action** : `App\Http\Controllers\Admin\AdminController@apiPendingWallets`

_Pas de body (GET)._

## `POST /api/v1/admin/wallets/{walletId}/approve`

**Action** : `App\Http\Controllers\Admin\AdminController@apiApproveWallet`

**Paramètres d'URL** :
- `{walletId}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/v1/admin/wallets/{walletId}/reject`

**Action** : `App\Http\Controllers\Admin\AdminController@apiRejectWallet`

**Paramètres d'URL** :
- `{walletId}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/v1/authenticity/dashboard`

**Action** : `App\Http\Controllers\Api\Authenticity\AuthenticityController@dashboard`

_Pas de body (GET)._

## `POST /api/v1/authenticity/{check}/confirm-payment`

**Action** : `App\Http\Controllers\Api\Authenticity\AuthenticityController@confirmPayment`

**Paramètres d'URL** :
- `{check}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `PUT /api/v1/authenticity/{check}/update-status`

**Action** : `App\Http\Controllers\Api\Authenticity\AuthenticityController@updateStatus`

**Paramètres d'URL** :
- `{check}` (obligatoire)

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `status` | string | oui | `required|in:expert_approved,expert_rejected` |
| `expert_notes` | string | oui | `required|string|max:1000` |

**Exemple de body JSON** :

```json
{
    "status": "expert_approved",
    "expert_notes": "string"
}
```

## `GET /api/v1/brands`

**Action** : `App\Http\Controllers\Api\Catalog\BrandController@index`

_Pas de body (GET)._

## `POST /api/v1/brands`

**Action** : `App\Http\Controllers\Api\Catalog\BrandController@store`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `name` | string | oui | `required|string|max:100|unique:brands,name` |
| `description` | string | non | `nullable|string|max:500` |
| `logo` | file | non | `nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048` |
| `country` | string | non | `nullable|string|max:100` |
| `type` | string | non | `nullable|string|max:50` |
| `website` | string | non | `nullable|url|max:255` |
| `is_active` | boolean | non | `nullable|boolean` |

**Exemple de body JSON** :

```json
{
    "name": "string",
    "description": "string",
    "logo": "<< fichier multipart (form-data) >>",
    "country": "string",
    "type": "string",
    "website": "string",
    "is_active": true
}
```

## `PUT /api/v1/brands/{id}`

**Action** : `App\Http\Controllers\Api\Catalog\BrandController@update`

**Paramètres d'URL** :
- `{id}` (obligatoire)

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `name` | string | oui | `required|string|max:100|unique:brands,name,' . $brand->i` |
| `description` | string | non | `nullable|string|max:500` |
| `logo` | file | non | `nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048` |
| `country` | string | non | `nullable|string|max:100` |
| `type` | string | non | `nullable|string|max:50` |
| `website` | string | non | `nullable|url|max:255` |
| `is_active` | boolean | non | `nullable|boolean` |

**Exemple de body JSON** :

```json
{
    "name": "string",
    "description": "string",
    "logo": "<< fichier multipart (form-data) >>",
    "country": "string",
    "type": "string",
    "website": "string",
    "is_active": true
}
```

## `DELETE /api/v1/brands/{id}`

**Action** : `App\Http\Controllers\Api\Catalog\BrandController@destroy`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (DELETE)._

## `GET /api/v1/brands/{id}`

**Action** : `App\Http\Controllers\Api\Catalog\BrandController@show`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/brands/{id}/items`

**Action** : `App\Http\Controllers\Api\Catalog\BrandController@items`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/categories`

**Action** : `App\Http\Controllers\Api\Catalog\CategoryController@index`

_Pas de body (GET)._

## `POST /api/v1/categories`

**Action** : `App\Http\Controllers\Api\Catalog\CategoryController@store`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `name` | string | oui | `required|string|max:100|unique:categories,name` |
| `description` | string | non | `nullable|string|max:500` |
| `icon` | string | non | `nullable|string|max:50` |
| `image` | file | non | `nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048` |
| `parent_id` | string | non | `nullable|exists:categories,id` |
| `sort_order` | number | non | `nullable|integer|min:0` |
| `is_active` | boolean | non | `nullable|boolean` |

**Exemple de body JSON** :

```json
{
    "name": "string",
    "description": "string",
    "icon": "string",
    "image": "<< fichier multipart (form-data) >>",
    "parent_id": "string",
    "sort_order": 0,
    "is_active": true
}
```

## `PUT /api/v1/categories/{id}`

**Action** : `App\Http\Controllers\Api\Catalog\CategoryController@update`

**Paramètres d'URL** :
- `{id}` (obligatoire)

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `name` | string | oui | `required|string|max:100|unique:categories,name,' . $category->i` |
| `description` | string | non | `nullable|string|max:500` |
| `icon` | string | non | `nullable|string|max:50` |
| `image` | file | non | `nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048` |
| `parent_id` | string | non | `nullable|exists:categories,id` |
| `sort_order` | number | non | `nullable|integer|min:0` |
| `is_active` | boolean | non | `nullable|boolean` |

**Exemple de body JSON** :

```json
{
    "name": "string",
    "description": "string",
    "icon": "string",
    "image": "<< fichier multipart (form-data) >>",
    "parent_id": "string",
    "sort_order": 0,
    "is_active": true
}
```

## `DELETE /api/v1/categories/{id}`

**Action** : `App\Http\Controllers\Api\Catalog\CategoryController@destroy`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (DELETE)._

## `GET /api/v1/categories/{id}`

**Action** : `App\Http\Controllers\Api\Catalog\CategoryController@show`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/categories/{id}/items`

**Action** : `App\Http\Controllers\Api\Catalog\CategoryController@items`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/currencies`

**Action** : `App\Http\Controllers\Api\System\SystemController@currencies`

_Pas de body (GET)._

## `GET /api/v1/home`

**Action** : `App\Http\Controllers\WelcomeController@apiIndex`

_Pas de body (GET)._

## `GET /api/v1/items`

**Action** : `App\Http\Controllers\Api\Items\ItemController@index`

_Pas de body (GET)._

## `POST /api/v1/items`

**Action** : `App\Http\Controllers\Api\Items\ItemController@store`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `name` | string | oui | `required, string, min:3, max:255` |
| `description` | string | oui | `required, string, min:10, max:5000` |
| `price` | number | oui | `required, numeric, min:0, max:999999999.99` |
| `currency` | string | oui | `required, string, in:USD,EUR,XAF,XOF,CDF` |
| `category_id` | number | oui | `required, integer, exists:categories,id` |
| `brand_id` | number | non | `nullable, integer, exists:brands,id` |
| `condition` | string | oui | `required, in:new,like_new,good,fair,poor` |
| `size` | string | non | `nullable, string, max:50` |
| `color` | string | non | `nullable, string, max:50` |
| `item_number` | string | non | `nullable, string, max:100` |
| `material` | string | non | `nullable, string, max:100` |
| `location` | string | non | `nullable, string, max:255` |
| `quantity` | number | oui | `required, integer, min:1, max:10000` |
| `images` | array | oui | `required, array, min:3, max:10` |
| `images.*` | file | oui | `required, image, mimes:jpeg,jpg,png,webp, max:5120` |
| `authenticity_guaranteed` | boolean | non | `boolean` |
| `tags` | array | non | `nullable, array, max:10` |
| `tags.*` | string | non | `string, max:50` |

**Exemple de body JSON** :

```json
{
    "name": "string",
    "description": "string",
    "price": 0,
    "currency": "USD",
    "category_id": 0,
    "brand_id": 0,
    "condition": "new",
    "size": "string",
    "color": "string",
    "item_number": "string",
    "material": "string",
    "location": "string",
    "quantity": 0,
    "images": [],
    "authenticity_guaranteed": true,
    "tags": []
}
```

## `GET /api/v1/items/{id}`

**Action** : `App\Http\Controllers\Api\Items\ItemController@show`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (GET)._

## `PUT /api/v1/items/{id}`

**Action** : `App\Http\Controllers\Api\Items\ItemController@update`

**Paramètres d'URL** :
- `{id}` (obligatoire)

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `name` | string | non | `sometimes, string, min:3, max:255` |
| `description` | string | non | `sometimes, string, min:10, max:5000` |
| `price` | number | non | `sometimes, numeric, min:0, max:999999999.99` |
| `currency` | string | non | `sometimes, string, in:USD,EUR,XAF,XOF,CDF` |
| `category_id` | number | non | `sometimes, integer, exists:categories,id` |
| `brand_id` | number | non | `nullable, integer, exists:brands,id` |
| `condition` | string | non | `sometimes, in:new,like_new,good,fair,poor` |
| `size` | string | non | `nullable, string, max:50` |
| `color` | string | non | `nullable, string, max:50` |
| `material` | string | non | `nullable, string, max:100` |
| `location` | string | non | `nullable, string, max:255` |
| `quantity` | number | non | `sometimes, integer, min:1, max:10000` |
| `status` | string | non | `sometimes, in:active,sold,inactive` |
| `images` | array | non | `sometimes, array, min:1, max:10` |
| `images.*` | file | non | `image, mimes:jpeg,jpg,png,webp, max:5120` |
| `tags` | array | non | `nullable, array, max:10` |
| `tags.*` | string | non | `string, max:50` |

**Exemple de body JSON** :

```json
{
    "name": "string",
    "description": "string",
    "price": 0,
    "currency": "USD",
    "category_id": 0,
    "brand_id": 0,
    "condition": "new",
    "size": "string",
    "color": "string",
    "material": "string",
    "location": "string",
    "quantity": 0,
    "status": "active",
    "images": [],
    "tags": []
}
```

## `DELETE /api/v1/items/{id}`

**Action** : `App\Http\Controllers\Api\Items\ItemController@destroy`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (DELETE)._

## `GET /api/v1/items/{item}/authenticity/can-verify`

**Action** : `App\Http\Controllers\Api\Authenticity\AuthenticityController@canVerify`

**Paramètres d'URL** :
- `{item}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/items/{item}/authenticity/status`

**Action** : `App\Http\Controllers\Api\Authenticity\AuthenticityController@status`

**Paramètres d'URL** :
- `{item}` (obligatoire)

_Pas de body (GET)._

## `POST /api/v1/items/{item}/authenticity/submit`

**Action** : `App\Http\Controllers\Api\Authenticity\AuthenticityController@submit`

**Paramètres d'URL** :
- `{item}` (obligatoire)

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `product_images.*` | file | oui | `required|image|mimes:jpeg,png,jpg|max:10240` |
| `certificate` | file | non | `nullable|image|mimes:jpeg,png,jpg,pdf|max:5120` |
| `receipt` | file | non | `nullable|image|mimes:jpeg,png,jpg,pdf|max:5120` |
| `serial_number` | string | non | `nullable|string|max:255` |
| `purchase_date` | date | non | `nullable|date` |
| `purchase_location` | string | non | `nullable|string|max:255` |
| `additional_notes` | string | non | `nullable|string|max:1000` |
| `terms_accepted` | string | oui | `required|accepted` |

**Exemple de body JSON** :

```json
{
    "product_images": [],
    "certificate": "<< fichier multipart (form-data) >>",
    "receipt": "<< fichier multipart (form-data) >>",
    "serial_number": "string",
    "purchase_date": "2026-01-01",
    "purchase_location": "string",
    "additional_notes": "string",
    "terms_accepted": "string"
}
```

## `GET /api/v1/messages`

**Action** : `App\Http\Controllers\Api\Messages\MessageController@index`

_Pas de body (GET)._

## `POST /api/v1/messages`

**Action** : `App\Http\Controllers\Api\Messages\MessageController@store`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `recipient_id` | string | oui | `required|exists:users,id` |
| `content` | string | non | `nullable|string|max:5000` |
| `item_id` | string | non | `nullable|exists:items,id` |
| `voice` | file | non | `file|mimes:webm,mp3,ogg,wav,mp4|max:5120` |
| `attachment` | file | non | `file|max:10240` |

**Exemple de body JSON** :

```json
{
    "recipient_id": "string",
    "content": "string",
    "item_id": "string",
    "voice": "<< fichier multipart (form-data) >>",
    "attachment": "<< fichier multipart (form-data) >>"
}
```

## `POST /api/v1/messages/discount/apply`

**Action** : `App\Http\Controllers\Api\Messages\MessageController@applyDiscount`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `item_id` | string | oui | `required|exists:items,id` |
| `buyer_id` | string | oui | `required|exists:users,id` |
| `discount_percentage` | number | oui | `required|numeric|min:1|max:50` |
| `expires_hours` | number | non | `nullable|integer|min:1|max:168` |

**Exemple de body JSON** :

```json
{
    "item_id": "string",
    "buyer_id": "string",
    "discount_percentage": 0,
    "expires_hours": 0
}
```

## `GET /api/v1/messages/discounts/{itemId}`

**Action** : `App\Http\Controllers\Api\Messages\MessageController@getAvailableDiscounts`

**Paramètres d'URL** :
- `{itemId}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/messages/unread/count`

**Action** : `App\Http\Controllers\Api\Messages\MessageController@unreadCount`

_Pas de body (GET)._

## `PUT /api/v1/messages/{messageId}/mark-read`

**Action** : `App\Http\Controllers\Api\Messages\MessageController@markAsRead`

**Paramètres d'URL** :
- `{messageId}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/v1/messages/{userId}`

**Action** : `App\Http\Controllers\Api\Messages\MessageController@show`

**Paramètres d'URL** :
- `{userId}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/notifications`

**Action** : `App\Http\Controllers\Api\Notifications\NotificationController@index`

_Pas de body (GET)._

## `POST /api/v1/notifications/mark-all-read`

**Action** : `App\Http\Controllers\Api\Notifications\NotificationController@markAllAsRead`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `DELETE /api/v1/notifications/read/all`

**Action** : `App\Http\Controllers\Api\Notifications\NotificationController@deleteRead`

_Pas de body (DELETE)._

## `GET /api/v1/notifications/unread`

**Action** : `App\Http\Controllers\Api\Notifications\NotificationController@unread`

_Pas de body (GET)._

## `GET /api/v1/notifications/unread/count`

**Action** : `App\Http\Controllers\Api\Notifications\NotificationController@unreadCount`

_Pas de body (GET)._

## `DELETE /api/v1/notifications/{id}`

**Action** : `App\Http\Controllers\Api\Notifications\NotificationController@destroy`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (DELETE)._

## `POST /api/v1/notifications/{id}/mark-read`

**Action** : `App\Http\Controllers\Api\Notifications\NotificationController@markAsRead`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/v1/orders`

**Action** : `App\Http\Controllers\Api\Orders\OrderController@index`

_Pas de body (GET)._

## `POST /api/v1/orders`

**Action** : `App\Http\Controllers\Api\Orders\OrderController@store`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `item_id` | number | oui | `required, integer, exists:items,id` |
| `quantity` | number | oui | `required, integer, min:1, max:100` |
| `payment_method` | string | oui | `required, string, in:cinetpay,mobile_money,card,wallet,cash_on_delivery` |
| `delivery_address` | string | oui | `required, string, min:10, max:500` |
| `delivery_city` | string | oui | `required, string, max:100` |
| `delivery_phone` | string | oui | `required, string, regex:/^[+]?[0-9]{8,15}$/` |
| `delivery_notes` | string | non | `nullable, string, max:1000` |
| `coupon_code` | string | non | `nullable, string, max:50, exists:coupons,code` |

**Exemple de body JSON** :

```json
{
    "item_id": 0,
    "quantity": 0,
    "payment_method": "cinetpay",
    "delivery_address": "string",
    "delivery_city": "string",
    "delivery_phone": "+243990000000",
    "delivery_notes": "string",
    "coupon_code": "string"
}
```

## `GET /api/v1/orders/sales`

**Action** : `App\Http\Controllers\Api\Orders\OrderController@mySales`

_Pas de body (GET)._

## `GET /api/v1/orders/{id}`

**Action** : `App\Http\Controllers\Api\Orders\OrderController@show`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (GET)._

## `POST /api/v1/orders/{id}/confirm-delivery`

**Action** : `App\Http\Controllers\Api\Orders\OrderController@confirmDelivery`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/v1/orders/{id}/confirm-payment`

**Action** : `App\Http\Controllers\Api\Orders\OrderController@confirmPayment`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/v1/orders/{id}/mark-delivered`

**Action** : `App\Http\Controllers\Api\Orders\OrderController@markAsDelivered`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/v1/orders/{id}/mark-shipped`

**Action** : `App\Http\Controllers\Api\Orders\OrderController@markAsShipped`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/v1/payments`

**Action** : `App\Http\Controllers\Api\Payments\PaymentController@index`

_Pas de body (GET)._

## `POST /api/v1/payments/initiate`

**Action** : `App\Http\Controllers\Api\Payments\PaymentController@initiate`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `provider` | string | oui | `required|string|in:orange_money,mpesa,airtel_money,africell,illicocash` |
| `amount` | number | oui | `required|numeric|min:1|max:500000` |
| `phone` | string | oui | `required|string|min:9|max:15` |
| `purpose` | string | oui | `required|string` |
| `currency` | string | non | `nullable|in:USD,CDF` |

**Exemple de body JSON** :

```json
{
    "provider": "orange_money",
    "amount": 0,
    "phone": "+243990000000",
    "purpose": "string",
    "currency": "USD"
}
```

## `POST /api/v1/payments/maishapay` (`api.v1.payments.maishapay.initiate`)

**Action** : `App\Http\Controllers\Api\Payments\PaymentController@initiateMaishaPayment`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `amount` | number | oui | `required|numeric|min:0.01` |
| `phone` | string | oui | `required|string|min:9|max:12` |
| `currency` | string | non | `sometimes|string|in:CDF,USD` |
| `operator` | string | non | `sometimes|string|in:VODACOM,AIRTEL,ORANGE,AFRICELL,vodacom,airtel,orange,africell` |
| `purpose` | string | non | `sometimes|string|max:255` |

**Exemple de body JSON** :

```json
{
    "amount": 0,
    "phone": "+243990000000",
    "currency": "CDF",
    "operator": "VODACOM",
    "purpose": "string"
}
```

## `GET /api/v1/payments/maishapay/status/{transactionId}` (`api.v1.payments.maishapay.status`)

**Action** : `App\Http\Controllers\Api\Payments\PaymentController@checkMaishaStatus`

**Paramètres d'URL** :
- `{transactionId}` (obligatoire)

_Pas de body (GET)._

## `POST /api/v1/payments/refund/{orderId}`

**Action** : `App\Http\Controllers\Api\Payments\PaymentController@requestRefund`

**Paramètres d'URL** :
- `{orderId}` (obligatoire)

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `reason` | string | oui | `required|string|min:10|max:1000` |
| `refund_type` | string | oui | `required|in:partial,full` |
| `refund_amount` | number | non | `nullable|numeric|min:0` |
| `evidence_photos` | array | non | `nullable|array|max:5` |
| `evidence_photos.*` | file | non | `image|mimes:jpeg,png,jpg|max:2048` |

**Exemple de body JSON** :

```json
{
    "reason": "string",
    "refund_type": "partial",
    "refund_amount": 0,
    "evidence_photos": []
}
```

## `GET /api/v1/payments/refund/{refundId}/status`

**Action** : `App\Http\Controllers\Api\Payments\PaymentController@refundStatus`

**Paramètres d'URL** :
- `{refundId}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/payments/stats`

**Action** : `App\Http\Controllers\Api\Payments\PaymentController@stats`

_Pas de body (GET)._

## `GET /api/v1/payments/{transactionId}`

**Action** : `App\Http\Controllers\Api\Payments\PaymentController@show`

**Paramètres d'URL** :
- `{transactionId}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/reviews`

**Action** : `App\Http\Controllers\Api\Reviews\ReviewController@index`

_Pas de body (GET)._

## `POST /api/v1/reviews`

**Action** : `App\Http\Controllers\Api\Reviews\ReviewController@store`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `order_id` | string | oui | `required|exists:orders,id` |
| `rating` | number | oui | `required|integer|min:1|max:5` |
| `comment` | string | non | `nullable|string|max:500` |

**Exemple de body JSON** :

```json
{
    "order_id": "string",
    "rating": 0,
    "comment": "string"
}
```

## `GET /api/v1/reviews/item/{itemId}`

**Action** : `App\Http\Controllers\Api\Reviews\ReviewController@itemReviews`

**Paramètres d'URL** :
- `{itemId}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/reviews/seller/{sellerId}`

**Action** : `App\Http\Controllers\Api\Reviews\ReviewController@sellerReviews`

**Paramètres d'URL** :
- `{sellerId}` (obligatoire)

_Pas de body (GET)._

## `PUT /api/v1/reviews/{reviewId}`

**Action** : `App\Http\Controllers\Api\Reviews\ReviewController@update`

**Paramètres d'URL** :
- `{reviewId}` (obligatoire)

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `rating` | number | oui | `sometimes|required|integer|min:1|max:5` |
| `comment` | string | non | `nullable|string|max:500` |

**Exemple de body JSON** :

```json
{
    "rating": 0,
    "comment": "string"
}
```

## `DELETE /api/v1/reviews/{reviewId}`

**Action** : `App\Http\Controllers\Api\Reviews\ReviewController@destroy`

**Paramètres d'URL** :
- `{reviewId}` (obligatoire)

_Pas de body (DELETE)._

## `GET /api/v1/support`

**Action** : `App\Http\Controllers\Api\Support\SupportController@index`

_Pas de body (GET)._

## `POST /api/v1/support`

**Action** : `App\Http\Controllers\Api\Support\SupportController@store`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `subject` | string | non | `nullable|string|max:255` |
| `category` | string | oui | `required|in:technical,account,payment,order,general` |
| `message` | string | oui | `required|string|max:5000` |
| `priority` | string | non | `nullable|in:low,normal,high,urgent` |
| `attachments` | array | non | `nullable|array` |
| `attachments.*` | file | non | `file|max:5120` |

**Exemple de body JSON** :

```json
{
    "subject": "string",
    "category": "technical",
    "message": "string",
    "priority": "low",
    "attachments": []
}
```

## `GET /api/v1/support/stats`

**Action** : `App\Http\Controllers\Api\Support\SupportController@stats`

_Pas de body (GET)._

## `GET /api/v1/support/{id}`

**Action** : `App\Http\Controllers\Api\Support\SupportController@show`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Pas de body (GET)._

## `POST /api/v1/support/{id}/close`

**Action** : `App\Http\Controllers\Api\Support\SupportController@close`

**Paramètres d'URL** :
- `{id}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/v1/support/{id}/reply`

**Action** : `App\Http\Controllers\Api\Support\SupportController@reply`

**Paramètres d'URL** :
- `{id}` (obligatoire)

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `message` | string | oui | `required|string|max:5000` |
| `attachments` | array | non | `nullable|array` |
| `attachments.*` | file | non | `file|max:5120` |

**Exemple de body JSON** :

```json
{
    "message": "string",
    "attachments": []
}
```

## `DELETE /api/v1/user/account`

**Action** : `App\Http\Controllers\Api\Users\UserController@destroy`

_Pas de body (DELETE)._

## `POST /api/v1/user/avatar`

**Action** : `App\Http\Controllers\Api\Users\UserController@uploadAvatar`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `avatar` | file | oui | `required|image|mimes:jpeg,png,jpg,gif|max:2048` |

**Exemple de body JSON** :

```json
{
    "avatar": "<< fichier multipart (form-data) >>"
}
```

## `GET /api/v1/user/items`

**Action** : `App\Http\Controllers\Api\Users\UserController@getItems`

_Pas de body (GET)._

## `GET /api/v1/user/orders`

**Action** : `App\Http\Controllers\Api\Users\UserController@getOrders`

_Pas de body (GET)._

## `PUT /api/v1/user/password`

**Action** : `App\Http\Controllers\Api\Users\UserController@updatePassword`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `current_password` | string | oui | `required|current_password` |
| `password` | string | oui | `required|string|min:8|confirmed` |

**Exemple de body JSON** :

```json
{
    "current_password": "Motdepasse1!",
    "password": "Motdepasse1!"
}
```

## `GET /api/v1/user/profile`

**Action** : `App\Http\Controllers\Api\Users\UserController@profile`

_Pas de body (GET)._

## `PUT /api/v1/user/profile`

**Action** : `App\Http\Controllers\Api\Users\UserController@updateProfile`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `name` | string | oui | `sometimes|required|string|max:255` |
| `email` | email | oui | `sometimes, required, email, Rule::unique('users')->ignore($user->id)` |
| `phone` | string | non | `nullable|string|max:20` |
| `city` | string | non | `nullable|string|max:255` |
| `bio` | string | non | `nullable|string|max:1000` |
| `location` | string | non | `nullable|string|max:255` |

**Exemple de body JSON** :

```json
{
    "name": "string",
    "email": "client@exemple.com",
    "phone": "+243990000000",
    "city": "string",
    "bio": "string",
    "location": "string"
}
```

## `GET /api/v1/user/reviews`

**Action** : `App\Http\Controllers\Api\Users\UserController@getReviews`

_Pas de body (GET)._

## `GET /api/v1/user/sales`

**Action** : `App\Http\Controllers\Api\Users\UserController@getSales`

_Pas de body (GET)._

## `GET /api/v1/user/stats`

**Action** : `App\Http\Controllers\Api\Users\UserController@getStats`

_Pas de body (GET)._

## `GET /api/v1/vintpass`

**Action** : `App\Http\Controllers\Api\VintPass\VintPassController@myPasses`

_Pas de body (GET)._

## `POST /api/v1/vintpass/request/{item}`

**Action** : `App\Http\Controllers\Api\VintPass\VintPassController@requestPass`

**Paramètres d'URL** :
- `{item}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/v1/vintpass/verify/{shortCode}`

**Action** : `App\Http\Controllers\Api\VintPass\VintPassController@verify`

**Paramètres d'URL** :
- `{shortCode}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/vintpass/{vintPass}`

**Action** : `App\Http\Controllers\Api\VintPass\VintPassController@show`

**Paramètres d'URL** :
- `{vintPass}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/wallet`

**Action** : `App\Http\Controllers\Api\Wallet\WalletController@index`

_Pas de body (GET)._

## `POST /api/v1/wallet/add-funds`

**Action** : `App\Http\Controllers\Api\Wallet\WalletController@addFunds`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `wallet_id` | string | oui | `required|exists:wallets,id` |
| `amount` | number | oui | `required|numeric|min:1` |
| `payment_method` | string | oui | `required|string|in:illicocash,orange_money,airtel_money,mpesa,africell` |

**Exemple de body JSON** :

```json
{
    "wallet_id": "string",
    "amount": 0,
    "payment_method": "illicocash"
}
```

## `POST /api/v1/wallet/convert`

**Action** : `App\Http\Controllers\Api\Wallet\WalletController@convert`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `from_wallet_id` | string | oui | `required|exists:wallets,id` |
| `to_wallet_id` | string | oui | `required|exists:wallets,id` |
| `amount` | number | oui | `required|numeric|min:0.01` |

**Exemple de body JSON** :

```json
{
    "from_wallet_id": "string",
    "to_wallet_id": "string",
    "amount": 0
}
```

## `GET /api/v1/wallet/transactions`

**Action** : `App\Http\Controllers\Api\Wallet\WalletController@transactions`

_Pas de body (GET)._

## `POST /api/v1/wallet/withdraw`

**Action** : `App\Http\Controllers\Api\Wallet\WalletController@withdraw`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `wallet_id` | string | oui | `required|exists:wallets,id` |
| `amount` | number | oui | `required|numeric|min:0.01` |
| `phone_number` | string | oui | `required, string, regex:/^(\+?243|0)?[0-9]{9}$/, min:9, max:15` |
| `payment_method` | string | oui | `required|string|in:orange_money,airtel_money,mpesa,africell,illicocash,agent` |

**Exemple de body JSON** :

```json
{
    "wallet_id": "string",
    "amount": 0,
    "phone_number": "+243990000000",
    "payment_method": "orange_money"
}
```

## `POST /api/v1/wallet/withdraw/maishapay`

**Action** : `App\Http\Controllers\Api\Wallet\WalletController@withdrawMaishaPay`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `wallet_id` | string | oui | `required|exists:wallets,id` |
| `amount` | number | oui | `required|numeric|min:100` |
| `phone_number` | string | oui | `required, string, regex:/^(\+?243|0)?[0-9]{9}$/` |
| `operator` | string | non | `nullable|string|in:VODACOM,ORANGE,AIRTEL,AFRICELL` |

**Exemple de body JSON** :

```json
{
    "wallet_id": "string",
    "amount": 0,
    "phone_number": "+243990000000",
    "operator": "VODACOM"
}
```

## `GET /api/v1/wallet/withdraw/maishapay/status/{transactionId}`

**Action** : `App\Http\Controllers\Api\Wallet\WalletController@withdrawMaishaPayStatus`

**Paramètres d'URL** :
- `{transactionId}` (obligatoire)

_Pas de body (GET)._

## `GET /api/v1/wallet/withdraw/operators`

**Action** : `App\Http\Controllers\Api\Wallet\WalletController@getPayoutOperators`

_Pas de body (GET)._

## `POST /api/v1/wallet/withdrawals/maishapay/callback`

**Action** : `App\Http\Controllers\WalletController@handleWithdrawalWebhook`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/admin/broadcast-fcm-test`

**Action** : `App\Http\Controllers\Api\Notifications\FcmController@adminBroadcast`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `title` | string | oui | `required|string|max:255` |
| `message` | string | oui | `required|string|max:500` |

**Exemple de body JSON** :

```json
{
    "title": "string",
    "message": "string"
}
```

## `GET /api/admin/fcm-stats`

**Action** : `App\Http\Controllers\Api\Notifications\FcmController@adminStats`

_Pas de body (GET)._

## `POST /api/affiliate/apply-referral-code`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@applyReferralCode`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/affiliate/calculate-conversion`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@calculateConversion`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/affiliate/codes/stats`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@getCodesStats`

_Pas de body (GET)._

## `POST /api/affiliate/convert-points`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@convertPointsToCash`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/affiliate/dashboard`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@dashboard`

_Pas de body (GET)._

## `GET /api/affiliate/generate-link`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@generateReferralLink`

_Pas de body (GET)._

## `GET /api/affiliate/points-history`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@getPointsHistory`

_Pas de body (GET)._

## `GET /api/affiliate/redemptions`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@getRedemptions`

_Pas de body (GET)._

## `GET /api/affiliate/referral-codes`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@getReferralCodes`

_Pas de body (GET)._

## `POST /api/affiliate/referral-codes`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@createReferralCode`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/affiliate/referrals`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@getReferrals`

_Pas de body (GET)._

## `POST /api/bot`

**Action** : `App\Http\Controllers\BotController@ask`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/dashboard/data`

**Action** : `App\Http\Controllers\DashboardController@apiData`

_Pas de body (GET)._

## `POST /api/fcm-token`

**Action** : `App\Http\Controllers\Api\Notifications\FcmController@registerToken`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `token` | string | oui | `required|string` |
| `device_type` | string | non | `nullable|string|max:20` |

**Exemple de body JSON** :

```json
{
    "token": "string",
    "device_type": "string"
}
```

## `GET /api/health`

**Action** : `App\Http\Controllers\Api\System\SystemController@health`

_Pas de body (GET)._

## `GET /api/items/search`

**Action** : `App\Http\Controllers\Api\Items\ItemController@search`

_Pas de body (GET)._

## `POST /api/items/{item}/favorite`

**Action** : `App\Http\Controllers\Api\Items\ItemController@toggleFavorite`

**Paramètres d'URL** :
- `{item}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/login`

**Action** : `App\Http\Controllers\Api\Auth\AuthController@login`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `email` | email | oui | `required|string|email` |
| `password` | string | oui | `required|string` |

**Exemple de body JSON** :

```json
{
    "email": "client@exemple.com",
    "password": "Motdepasse1!"
}
```

## `POST /api/logout`

**Action** : `App\Http\Controllers\Api\Auth\AuthController@logout`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/notifications/broadcast-test`

**Action** : `App\Http\Controllers\Api\NotificationController@broadcastTest`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/notifications/closed`

**Action** : `App\Http\Controllers\Api\NotificationController@closed`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `tag` | string | oui | `required|string` |
| `timestamp` | number | oui | `required|integer` |

**Exemple de body JSON** :

```json
{
    "tag": "string",
    "timestamp": 0
}
```

## `POST /api/notifications/subscribe`

**Action** : `App\Http\Controllers\Api\NotificationController@subscribe`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `token` | string | oui | `required|string` |
| `device_type` | string | non | `nullable|string|in:mobile,tablet,desktop` |
| `browser` | string | non | `nullable|string|max:50` |

**Exemple de body JSON** :

```json
{
    "token": "string",
    "device_type": "mobile",
    "browser": "string"
}
```

## `GET /api/notifications/test`

**Action** : `App\Http\Controllers\Api\NotificationController@test`

_Pas de body (GET)._

## `POST /api/notifications/unsubscribe`

**Action** : `App\Http\Controllers\Api\NotificationController@unsubscribe`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/payment-callbacks/status` (`payment.status`)

**Action** : `App\Http\Controllers\Api\Webhooks\PaymentCallbackController@checkStatus`

_Pas de body (GET)._

## `POST /api/payment-callbacks/{provider}` (`payment.callback`)

**Action** : `App\Http\Controllers\Api\Webhooks\PaymentCallbackController@handleCallback`

**Paramètres d'URL** :
- `{provider}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/payment-callbacks/{transaction}/force-complete` (`payment.force-complete`)

**Action** : `App\Http\Controllers\Api\Webhooks\PaymentCallbackController@forceComplete`

**Paramètres d'URL** :
- `{transaction}` (obligatoire)

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/register`

**Action** : `App\Http\Controllers\Api\Auth\AuthController@register`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `name` | string | oui | `required|string|max:255` |
| `email` | email | oui | `required|string|email|max:255|unique:users` |
| `password` | string | oui | `required|string|min:8|confirmed` |

**Exemple de body JSON** :

```json
{
    "name": "string",
    "email": "client@exemple.com",
    "password": "Motdepasse1!"
}
```

## `POST /api/test-fcm-notification`

**Action** : `App\Http\Controllers\Api\Notifications\FcmController@testNotification`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `GET /api/user`

**Action** : `App\Http\Controllers\Api\Auth\AuthController@me`

_Pas de body (GET)._

## `DELETE /api/user/account`

**Action** : `App\Http\Controllers\UserController@destroy`

_Pas de body (DELETE)._

## `POST /api/user/avatar`

**Action** : `App\Http\Controllers\UserController@uploadAvatar`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `avatar` | file | oui | `required|image|mimes:jpeg,png,jpg,gif|max:2048` |

**Exemple de body JSON** :

```json
{
    "avatar": "<< fichier multipart (form-data) >>"
}
```

## `GET /api/user/items`

**Action** : `App\Http\Controllers\UserController@getItems`

_Pas de body (GET)._

## `GET /api/user/orders`

**Action** : `App\Http\Controllers\UserController@getOrders`

_Pas de body (GET)._

## `GET /api/user/profile`

**Action** : `App\Http\Controllers\UserController@profile`

_Pas de body (GET)._

## `PUT /api/user/profile`

**Action** : `App\Http\Controllers\UserController@updateProfile`

**Body** (JSON / form-data) :

| Champ | Type | Requis | Règles |
|---|---|---|---|
| `name` | string | oui | `sometimes|required|string|max:255` |
| `email` | email | oui | `sometimes, required, email, Rule::unique('users')->ignore($user->id)` |
| `phone` | string | non | `nullable|string|max:20` |
| `city` | string | non | `nullable|string|max:255` |
| `bio` | string | non | `nullable|string|max:1000` |
| `location` | string | non | `nullable|string|max:255` |

**Exemple de body JSON** :

```json
{
    "name": "string",
    "email": "client@exemple.com",
    "phone": "+243990000000",
    "city": "string",
    "bio": "string",
    "location": "string"
}
```

## `GET /api/user/reviews`

**Action** : `App\Http\Controllers\UserController@getReviews`

_Pas de body (GET)._

## `GET /api/user/sales`

**Action** : `App\Http\Controllers\UserController@getSales`

_Pas de body (GET)._

## `GET /api/user/stats`

**Action** : `App\Http\Controllers\UserController@getStats`

_Pas de body (GET)._

## `POST /api/validate-location`

**Action** : `App\Http\Controllers\LocationValidationController@validateLocation`

_Aucun champ validé (body libre ou lu via `$request->all()`)._

## `POST /api/validate-referral-code`

**Action** : `App\Http\Controllers\Api\Affiliate\AffiliateController@validateReferralCode`

_Aucun champ validé (body libre ou lu via `$request->all()`)._
