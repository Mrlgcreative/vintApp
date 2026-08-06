<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VintApp — Documentation API</title>
    <link rel="icon" type="image/png" href="{{ asset('/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap');
        body { font-family: 'Figtree', sans-serif; }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
        
        /* Sidebar */
        .sidebar { scrollbar-width: thin; scrollbar-color: #475569 #1e293b; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(99, 102, 241, 0.15); color: #818cf8; border-left-color: #818cf8; }
        
        /* Code blocks */
        pre { position: relative; }
        .copy-btn { position: absolute; top: 8px; right: 8px; opacity: 0; transition: opacity 0.2s; }
        pre:hover .copy-btn { opacity: 1; }
        
        /* Method badges */
        .method-get { background: #065f46; color: #6ee7b7; }
        .method-post { background: #1e40af; color: #93c5fd; }
        .method-put { background: #92400e; color: #fcd34d; }
        .method-delete { background: #991b1b; color: #fca5a5; }
        
        /* Sections */
        .endpoint-card { border-left: 3px solid transparent; transition: border-color 0.2s; }
        .endpoint-card:hover { border-left-color: #818cf8; }

        /* Mobile sidebar */
        @media (max-width: 1023px) {
            .sidebar-overlay { background: rgba(0,0,0,0.5); }
        }

        /* Anchor offset */
        [id] { scroll-margin-top: 80px; }
    </style>
</head>
<body class="bg-gray-950 text-gray-200 min-h-screen">

    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-gray-900/95 backdrop-blur border-b border-gray-800">
        <div class="flex items-center justify-between px-4 lg:px-6 h-16">
            <div class="flex items-center gap-3">
                <button id="sidebar-toggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-2">
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">📖 VintApp API</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 font-medium">v1.0.0</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:block relative">
                    <input type="text" id="search-input" placeholder="Rechercher un endpoint..." class="w-64 bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <kbd class="absolute right-2 top-1.5 text-xs text-gray-500 bg-gray-700 px-1.5 py-0.5 rounded">⌘K</kbd>
                </div>
                <span class="text-xs text-gray-500">Mars 2026</span>
            </div>
        </div>
    </header>

    {{-- Sidebar overlay (mobile) --}}
    <div id="sidebar-overlay" class="fixed inset-0 z-40 sidebar-overlay hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="sidebar fixed top-16 left-0 bottom-0 w-72 bg-gray-900 border-r border-gray-800 overflow-y-auto z-40 transform -translate-x-full lg:translate-x-0 transition-transform">
        <nav class="py-4 px-3">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Général</p>
            <a href="#info" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">📋 Informations générales</a>
            <a href="#auth" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">🔑 Authentification</a>
            <a href="#responses" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">📦 Format des réponses</a>
            <a href="#public" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">🌐 Routes publiques</a>

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mt-5 mb-2">Ressources</p>
            <a href="#users" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">👤 Utilisateurs</a>
            <a href="#items" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">📦 Articles</a>
            <a href="#orders" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">🛒 Commandes</a>
            <a href="#messages" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">💬 Messages</a>
            <a href="#reviews" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">⭐ Avis</a>

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mt-5 mb-2">Finances</p>
            <a href="#wallet" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">💰 Portefeuille</a>
            <a href="#payments" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">💳 Paiements</a>

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mt-5 mb-2">Fonctionnalités</p>
            <a href="#notifications" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">🔔 Notifications</a>
            <a href="#support" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">🎧 Support</a>
            <a href="#categories" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">📂 Catégories</a>
            <a href="#brands" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">🏷️ Marques</a>
            <a href="#authenticity" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">✅ Authenticité</a>
            <a href="#vintpass" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">🎫 VintPass</a>
            <a href="#affiliate" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">🤝 Affiliation</a>
            <a href="#dashboard" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">📊 Dashboard</a>
            <a href="#chatbot" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">🤖 Chatbot</a>
            <a href="#fcm" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">📲 Push (FCM)</a>

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mt-5 mb-2">Administration</p>
            <a href="#admin" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">⚙️ Administration</a>
            <a href="#callbacks" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">🔄 Callbacks</a>
            <a href="#errors" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">❌ Codes d'erreur</a>
            <a href="#annexes" class="sidebar-link block px-3 py-2 text-sm rounded-lg border-l-2 border-transparent">📎 Annexes</a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="lg:ml-72 pt-16">
        <div class="max-w-4xl mx-auto px-4 lg:px-8 py-10">

            {{-- Hero --}}
            <div class="mb-12">
                <h1 class="text-4xl font-bold text-white mb-3">Documentation API</h1>
                <p class="text-gray-400 text-lg mb-6">Référence complète de l'API VintApp — +150 endpoints pour intégrer votre marketplace.</p>
                <div class="flex flex-wrap gap-3">
                    <div class="flex items-center gap-2 bg-gray-800 rounded-lg px-3 py-2 text-sm">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        Base URL: <code class="text-indigo-300">{{ url('/api') }}</code>
                    </div>
                    <div class="bg-gray-800 rounded-lg px-3 py-2 text-sm">Auth: <span class="text-yellow-300">Bearer Token (Sanctum)</span></div>
                    <div class="bg-gray-800 rounded-lg px-3 py-2 text-sm">Format: <span class="text-blue-300">JSON</span></div>
                </div>
            </div>

            {{-- ==================== 1. Informations générales ==================== --}}
            <section id="info" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="text-indigo-400">📋</span> 1. Informations générales
                </h2>

                <div class="space-y-6">
                    {{-- Headers requis --}}
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Headers requis</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="border-b border-gray-700 text-gray-400">
                                    <th class="text-left py-2 pr-4">Header</th>
                                    <th class="text-left py-2 pr-4">Valeur</th>
                                    <th class="text-center py-2">Obligatoire</th>
                                </tr></thead>
                                <tbody class="text-gray-300">
                                    <tr class="border-b border-gray-800"><td class="py-2 pr-4"><code class="text-pink-300">Accept</code></td><td><code>application/json</code></td><td class="text-center">✅</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-2 pr-4"><code class="text-pink-300">Content-Type</code></td><td><code>application/json</code></td><td class="text-center">✅ POST/PUT</td></tr>
                                    <tr><td class="py-2 pr-4"><code class="text-pink-300">Authorization</code></td><td><code>Bearer &#123;token&#125;</code></td><td class="text-center">✅ Routes protégées</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Devises --}}
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Devises supportées</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-800 rounded-lg p-4 flex items-center gap-3">
                                <span class="text-3xl">🇺🇸</span>
                                <div><p class="font-semibold text-white">USD</p><p class="text-sm text-gray-400">Dollar américain ($)</p></div>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-4 flex items-center gap-3">
                                <span class="text-3xl">🇨🇩</span>
                                <div><p class="font-semibold text-white">CDF</p><p class="text-sm text-gray-400">Franc congolais (FC)</p></div>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-gray-400">💱 Taux de conversion : <span class="text-white font-medium">1 USD = 2 500 CDF</span></p>
                    </div>

                    {{-- Rate Limiting --}}
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">⚡ Rate Limiting</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @php
                                $limits = [
                                    ['Lecture articles', '100/min'], ['Écriture articles', '20/min'], ['Utilisateurs', '60/min'],
                                    ['Commandes', '40/min'], ['Messages', '50/min'], ['Avis', '20/min'],
                                    ['Notifications', '60/min'], ['Dashboard', '30/min'], ['Affiliation', '30/min'],
                                    ['Callbacks', '100/min'],
                                ];
                            @endphp
                            @foreach($limits as $limit)
                            <div class="bg-gray-800 rounded-lg px-3 py-2 text-sm flex justify-between">
                                <span class="text-gray-400">{{ $limit[0] }}</span>
                                <span class="text-amber-300 font-mono">{{ $limit[1] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==================== 2. Authentification ==================== --}}
            <section id="auth" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="text-indigo-400">🔑</span> 2. Authentification
                </h2>
                <p class="text-gray-400 mb-6">L'API utilise <span class="text-white font-medium">Laravel Sanctum</span> avec des tokens Bearer.</p>

                {{-- Register --}}
                <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6 mb-4">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="method-post text-xs font-bold px-2.5 py-1 rounded">POST</span>
                        <code class="text-white font-mono">/api/register</code>
                        <span class="text-xs text-gray-500">Inscription</span>
                    </div>
                    <div class="grid lg:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Body</p>
                            <pre class="bg-gray-950 rounded-lg p-4 text-sm overflow-x-auto"><code class="text-green-300">{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}</code></pre>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Réponse 201</p>
                            <pre class="bg-gray-950 rounded-lg p-4 text-sm overflow-x-auto"><code class="text-blue-300">{
  "success": true,
  "message": "Inscription réussie",
  "user": { "id": 1, "name": "John Doe", ... },
  "token": "1|abc123...",
  "token_type": "Bearer"
}</code></pre>
                        </div>
                    </div>
                </div>

                {{-- Login --}}
                <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6 mb-4">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="method-post text-xs font-bold px-2.5 py-1 rounded">POST</span>
                        <code class="text-white font-mono">/api/login</code>
                        <span class="text-xs text-gray-500">Connexion</span>
                    </div>
                    <div class="grid lg:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Body</p>
                            <pre class="bg-gray-950 rounded-lg p-4 text-sm overflow-x-auto"><code class="text-green-300">{
  "email": "john@example.com",
  "password": "password123"
}</code></pre>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Réponse 200</p>
                            <pre class="bg-gray-950 rounded-lg p-4 text-sm overflow-x-auto"><code class="text-blue-300">{
  "success": true,
  "message": "Connexion réussie",
  "user": { "id": 1, "name": "John Doe", "role": "user", ... },
  "token": "2|xyz789...",
  "token_type": "Bearer"
}</code></pre>
                        </div>
                    </div>
                </div>

                {{-- Logout --}}
                <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6 mb-4">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="method-post text-xs font-bold px-2.5 py-1 rounded">POST</span>
                        <code class="text-white font-mono">/api/logout</code>
                        <span class="text-xs bg-yellow-500/20 text-yellow-300 px-2 py-0.5 rounded">🔒 Auth</span>
                    </div>
                    <pre class="bg-gray-950 rounded-lg p-4 text-sm"><code class="text-blue-300">{ "success": true, "message": "Déconnexion réussie" }</code></pre>
                </div>

                {{-- Current user --}}
                <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="method-get text-xs font-bold px-2.5 py-1 rounded">GET</span>
                        <code class="text-white font-mono">/api/user</code>
                        <span class="text-xs bg-yellow-500/20 text-yellow-300 px-2 py-0.5 rounded">🔒 Auth</span>
                        <span class="text-xs text-gray-500">Utilisateur connecté</span>
                    </div>
                    <pre class="bg-gray-950 rounded-lg p-4 text-sm overflow-x-auto"><code class="text-blue-300">{
  "success": true,
  "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "avatar": "avatars/john.jpg", "role": "user" }
}</code></pre>
                </div>
            </section>

            {{-- ==================== 3. Format des réponses ==================== --}}
            <section id="responses" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="text-indigo-400">📦</span> 3. Format des réponses
                </h2>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                        <h3 class="text-sm font-semibold text-green-400 mb-3">✅ Succès</h3>
                        <pre class="bg-gray-950 rounded-lg p-3 text-xs overflow-x-auto"><code>{
  "success": true,
  "message": "Opération réussie",
  "data": { ... }
}</code></pre>
                    </div>
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                        <h3 class="text-sm font-semibold text-blue-400 mb-3">📄 Paginée</h3>
                        <pre class="bg-gray-950 rounded-lg p-3 text-xs overflow-x-auto"><code>{
  "success": true,
  "data": [ ... ],
  "pagination": {
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7
  }
}</code></pre>
                    </div>
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                        <h3 class="text-sm font-semibold text-red-400 mb-3">❌ Erreur</h3>
                        <pre class="bg-gray-950 rounded-lg p-3 text-xs overflow-x-auto"><code>{
  "success": false,
  "message": "Description",
  "errors": {
    "field": ["Message"]
  }
}</code></pre>
                    </div>
                </div>
            </section>

            {{-- ==================== 4. Routes publiques ==================== --}}
            <section id="public" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="text-indigo-400">🌐</span> 4. Routes publiques
                </h2>
                <p class="text-gray-400 mb-4">Accessibles <span class="text-white">sans authentification</span>.</p>
                <div class="space-y-4">
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="method-get text-xs font-bold px-2.5 py-1 rounded">GET</span>
                            <code class="text-white font-mono">/api/health</code>
                            <span class="text-xs text-gray-500">⏱️ Cache 60s</span>
                        </div>
                        <pre class="bg-gray-950 rounded-lg p-4 text-sm"><code class="text-blue-300">{ "status": "success", "message": "VintApp API is running", "version": "1.0.0" }</code></pre>
                    </div>
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="method-get text-xs font-bold px-2.5 py-1 rounded">GET</span>
                            <code class="text-white font-mono">/api/v1/home</code>
                            <span class="text-xs text-gray-500">Page d'accueil</span>
                        </div>
                        <p class="text-sm text-gray-400">Retourne : categories, spotlight_items, boosted_items, latest_items, stats, hero_slides.</p>
                    </div>
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="method-get text-xs font-bold px-2.5 py-1 rounded">GET</span>
                            <code class="text-white font-mono">/api/v1/currencies</code>
                            <span class="text-xs text-gray-500">⏱️ Cache 60s</span>
                        </div>
                        <p class="text-sm text-gray-400">Liste des devises supportées (USD, CDF).</p>
                    </div>
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="method-post text-xs font-bold px-2.5 py-1 rounded">POST</span>
                            <code class="text-white font-mono">/api/validate-location</code>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-4">
                            <pre class="bg-gray-950 rounded-lg p-4 text-sm"><code class="text-green-300">// Par ville
{ "city": "Kinshasa" }

// Par coordonnées GPS
{ "latitude": -4.3217, "longitude": 15.3127 }</code></pre>
                            <pre class="bg-gray-950 rounded-lg p-4 text-sm"><code class="text-blue-300">{ "success": true, "allowed": true, "city": "Kinshasa" }</code></pre>
                        </div>
                    </div>
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="method-post text-xs font-bold px-2.5 py-1 rounded">POST</span>
                            <code class="text-white font-mono">/api/validate-referral-code</code>
                            <span class="text-xs text-gray-500">⚡ 10 req/min</span>
                        </div>
                        <pre class="bg-gray-950 rounded-lg p-4 text-sm"><code class="text-green-300">{ "code": "VINT-ABC123" }</code></pre>
                    </div>
                </div>
            </section>

            {{-- ==================== 5. Utilisateurs ==================== --}}
            <section id="users" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">👤</span> 5. Utilisateurs
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise · ⚡ 60 req/min</p>
                <div class="space-y-4">
                    @php
                        $userEndpoints = [
                            ['GET', '/api/v1/user/profile', 'Profil utilisateur'],
                            ['PUT', '/api/v1/user/profile', 'Mettre à jour le profil'],
                            ['PUT', '/api/v1/user/password', 'Changer le mot de passe'],
                            ['POST', '/api/v1/user/avatar', 'Uploader un avatar (multipart/form-data, max 2Mo)'],
                            ['GET', '/api/v1/user/stats', 'Statistiques utilisateur'],
                            ['GET', '/api/v1/user/items', 'Mes articles (?per_page=12)'],
                            ['GET', '/api/v1/user/orders', 'Mes commandes (?per_page=10)'],
                            ['GET', '/api/v1/user/sales', 'Mes ventes (?per_page=10)'],
                            ['GET', '/api/v1/user/reviews', 'Avis reçus (?per_page=10)'],
                            ['DELETE', '/api/v1/user/account', 'Supprimer le compte'],
                        ];
                    @endphp
                    @foreach($userEndpoints as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach

                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h3 class="text-sm font-semibold text-gray-300 mb-3">Exemple — PUT /api/v1/user/profile</h3>
                        <div class="grid lg:grid-cols-2 gap-4">
                            <pre class="bg-gray-950 rounded-lg p-4 text-sm"><code class="text-green-300">{
  "name": "John Updated",
  "email": "john.new@example.com",
  "phone": "+243999000000",
  "city": "Lubumbashi",
  "bio": "Vendeur passionné"
}</code></pre>
                            <pre class="bg-gray-950 rounded-lg p-4 text-sm"><code class="text-blue-300">{
  "success": true,
  "data": {
    "items_count": 12, "sales_count": 8,
    "purchases_count": 5, "total_revenue": 150.00,
    "average_rating": 4.5, "favorites_count": 20
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==================== 6. Articles ==================== --}}
            <section id="items" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">📦</span> 6. Articles (Items)
                </h2>
                <p class="text-gray-400 text-sm mb-6">⏱️ Cache 60s pour les routes publiques · ⚡ 20 req/min pour l'écriture</p>

                <div class="space-y-4">
                    {{-- List items --}}
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="method-get text-xs font-bold px-2.5 py-1 rounded">GET</span>
                            <code class="text-white font-mono">/api/v1/items</code>
                            <span class="text-xs text-gray-500">🌐 Public</span>
                        </div>
                        <h4 class="text-xs text-gray-500 uppercase tracking-wider mb-2">Paramètres de requête</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="border-b border-gray-700 text-gray-400">
                                    <th class="text-left py-2 pr-4">Paramètre</th><th class="text-left py-2 pr-4">Type</th><th class="text-left py-2">Description</th>
                                </tr></thead>
                                <tbody class="text-gray-300">
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>category_id</code></td><td>integer</td><td>Filtrer par catégorie</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>brand_id</code></td><td>integer</td><td>Filtrer par marque</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>min_price</code> / <code>max_price</code></td><td>number</td><td>Fourchette de prix</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>condition</code></td><td>string</td><td>new, like_new, good, fair</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>search</code></td><td>string</td><td>Recherche textuelle</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>sort_by</code> / <code>sort_order</code></td><td>string</td><td>Tri (asc/desc)</td></tr>
                                    <tr><td class="py-1.5"><code>per_page</code></td><td>integer</td><td>Résultats par page (défaut: 15)</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-get text-xs font-bold px-2.5 py-1 rounded">GET</span>
                        <code class="text-white font-mono text-sm">/api/v1/items/{id}</code>
                        <span class="text-xs text-gray-500">🌐 Détails d'un article</span>
                    </div>

                    {{-- Create item --}}
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="method-post text-xs font-bold px-2.5 py-1 rounded">POST</span>
                            <code class="text-white font-mono">/api/v1/items</code>
                            <span class="text-xs bg-yellow-500/20 text-yellow-300 px-2 py-0.5 rounded">🔒 Auth</span>
                            <span class="text-xs text-gray-500">multipart/form-data</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="border-b border-gray-700 text-gray-400">
                                    <th class="text-left py-2 pr-4">Champ</th><th class="text-left py-2 pr-4">Type</th><th class="text-center py-2 pr-4">Requis</th><th class="text-left py-2">Description</th>
                                </tr></thead>
                                <tbody class="text-gray-300">
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>name</code></td><td>string</td><td class="text-center">✅</td><td>Nom de l'article</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>description</code></td><td>string</td><td class="text-center">✅</td><td>Description détaillée</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>price</code></td><td>number</td><td class="text-center">✅</td><td>Prix de vente</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>currency</code></td><td>string</td><td class="text-center">✅</td><td>USD ou CDF</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>quantity</code></td><td>integer</td><td class="text-center">✅</td><td>Quantité disponible</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>condition</code></td><td>string</td><td class="text-center">✅</td><td>new, like_new, good, fair</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>category_id</code></td><td>integer</td><td class="text-center">✅</td><td>ID catégorie</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>brand_id</code></td><td>integer</td><td class="text-center">—</td><td>ID marque</td></tr>
                                    <tr><td class="py-1.5"><code>images[]</code></td><td>file[]</td><td class="text-center">—</td><td>Images de l'article</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-amber-300 mt-3">⚠️ L'article est créé avec le statut <code>pending_verification</code> et doit être approuvé par un admin.</p>
                    </div>

                    @foreach([['PUT', '/api/v1/items/{id}', 'Modifier (propriétaire)'], ['DELETE', '/api/v1/items/{id}', 'Supprimer (propriétaire)'], ['POST', '/api/items/{item}/favorite', 'Toggle favori (⚡ 30/min)'], ['GET', '/api/items/search', 'Recherche (?q=iphone&category=1&min_price=100)']] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 7. Commandes ==================== --}}
            <section id="orders" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">🛒</span> 7. Commandes (Orders)
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise · ⚡ 40 req/min</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/orders', 'Mes commandes (acheteur)'],
                        ['GET', '/api/v1/orders/sales', 'Mes ventes (vendeur)'],
                        ['POST', '/api/v1/orders', 'Créer une commande'],
                        ['GET', '/api/v1/orders/{id}', 'Détails d\'une commande'],
                        ['POST', '/api/v1/orders/{id}/confirm-payment', 'Confirmer le paiement'],
                        ['POST', '/api/v1/orders/{id}/mark-shipped', 'Marquer comme expédiée'],
                        ['POST', '/api/v1/orders/{id}/mark-delivered', 'Marquer comme livrée'],
                        ['POST', '/api/v1/orders/{id}/confirm-delivery', 'Confirmer la réception'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach

                    {{-- Create order body --}}
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h3 class="text-sm font-semibold text-gray-300 mb-3">Body — POST /api/v1/orders</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="border-b border-gray-700 text-gray-400">
                                    <th class="text-left py-2 pr-4">Champ</th><th class="text-left py-2 pr-4">Type</th><th class="text-center py-2 pr-4">Requis</th><th class="text-left py-2">Description</th>
                                </tr></thead>
                                <tbody class="text-gray-300">
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>item_id</code></td><td>integer</td><td class="text-center">✅</td><td>ID de l'article</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>quantity</code></td><td>integer</td><td class="text-center">✅</td><td>Quantité</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>shipping_address</code></td><td>string</td><td class="text-center">✅</td><td>Adresse de livraison</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>shipping_city</code></td><td>string</td><td class="text-center">✅</td><td>Ville</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-1.5"><code>shipping_phone</code></td><td>string</td><td class="text-center">✅</td><td>Téléphone</td></tr>
                                    <tr><td class="py-1.5"><code>notes</code></td><td>string</td><td class="text-center">—</td><td>Notes supplémentaires</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==================== 8. Messages ==================== --}}
            <section id="messages" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">💬</span> 8. Messages
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise · ⚡ 50 req/min</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/messages', 'Liste des conversations'],
                        ['GET', '/api/v1/messages/{userId}', 'Messages d\'une conversation'],
                        ['POST', '/api/v1/messages', 'Envoyer un message'],
                        ['PUT', '/api/v1/messages/{messageId}/mark-read', 'Marquer comme lu'],
                        ['GET', '/api/v1/messages/unread/count', 'Nombre de non lus'],
                        ['POST', '/api/v1/messages/discount/apply', 'Appliquer une réduction'],
                        ['GET', '/api/v1/messages/discounts/{itemId}', 'Réductions disponibles'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 9. Avis ==================== --}}
            <section id="reviews" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">⭐</span> 9. Avis (Reviews)
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise · ⚡ 20 req/min</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/reviews', 'Tous les avis'],
                        ['GET', '/api/v1/reviews/item/{itemId}', 'Avis d\'un article'],
                        ['GET', '/api/v1/reviews/seller/{sellerId}', 'Avis d\'un vendeur'],
                        ['POST', '/api/v1/reviews', 'Créer un avis (order_id, rating 1-5, comment)'],
                        ['PUT', '/api/v1/reviews/{reviewId}', 'Modifier un avis (auteur)'],
                        ['DELETE', '/api/v1/reviews/{reviewId}', 'Supprimer un avis (auteur)'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 10. Portefeuille ==================== --}}
            <section id="wallet" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">💰</span> 10. Portefeuille (Wallet)
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/wallet', 'Consulter les soldes (USD + CDF)'],
                        ['GET', '/api/v1/wallet/transactions', 'Historique des transactions'],
                        ['POST', '/api/v1/wallet/add-funds', 'Recharger le portefeuille'],
                        ['POST', '/api/v1/wallet/withdraw', 'Retirer des fonds'],
                        ['POST', '/api/v1/wallet/withdraw/maishapay', 'Retrait via MaishaPay'],
                        ['GET', '/api/v1/wallet/withdraw/maishapay/status/{id}', 'Statut retrait MaishaPay'],
                        ['GET', '/api/v1/wallet/withdraw/operators', 'Opérateurs de payout'],
                        ['POST', '/api/v1/wallet/convert', 'Convertir entre devises'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach

                    {{-- Opérateurs --}}
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h3 class="text-sm font-semibold text-gray-300 mb-3">📱 Opérateurs Mobile Money</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([['mpesa', 'M-Pesa / Vodacom', '081-083'], ['orange_money', 'Orange Money', '084-085'], ['airtel_money', 'Airtel Money', '097, 099'], ['africell', 'Africell', '090-091'], ['illicocash', 'Illicocash', '—']] as $op)
                            <div class="bg-gray-800 rounded-lg px-3 py-2">
                                <p class="text-white text-sm font-medium">{{ $op[1] }}</p>
                                <p class="text-xs text-gray-400"><code>{{ $op[0] }}</code> · {{ $op[2] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==================== 11. Paiements ==================== --}}
            <section id="payments" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">💳</span> 11. Paiements
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/payments', 'Historique des paiements'],
                        ['GET', '/api/v1/payments/{transactionId}', 'Détails d\'un paiement'],
                        ['GET', '/api/v1/payments/stats', 'Statistiques de paiement'],
                        ['POST', '/api/v1/payments/initiate', 'Initier un paiement mobile money'],
                        ['POST', '/api/v1/payments/maishapay', 'Paiement via MaishaPay'],
                        ['GET', '/api/v1/payments/maishapay/status/{id}', 'Statut MaishaPay'],
                        ['POST', '/api/v1/payments/refund/{orderId}', 'Demander un remboursement'],
                        ['GET', '/api/v1/payments/refund/{refundId}/status', 'Statut d\'un remboursement'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                    <p class="text-xs text-amber-300">⚠️ Les remboursements doivent être demandés dans les <strong>30 jours</strong> suivant la commande.</p>
                </div>
            </section>

            {{-- ==================== 12. Notifications ==================== --}}
            <section id="notifications" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">🔔</span> 12. Notifications
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise · ⚡ 60 req/min</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/notifications', 'Liste des notifications'],
                        ['GET', '/api/v1/notifications/unread', 'Notifications non lues'],
                        ['GET', '/api/v1/notifications/unread/count', 'Nombre de non lues'],
                        ['POST', '/api/v1/notifications/{id}/mark-read', 'Marquer comme lue'],
                        ['POST', '/api/v1/notifications/mark-all-read', 'Marquer toutes comme lues'],
                        ['DELETE', '/api/v1/notifications/{id}', 'Supprimer une notification'],
                        ['DELETE', '/api/v1/notifications/read/all', 'Supprimer toutes les lues'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 13. Support ==================== --}}
            <section id="support" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">🎧</span> 13. Support
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/support', 'Mes tickets'],
                        ['POST', '/api/v1/support', 'Créer un ticket'],
                        ['GET', '/api/v1/support/{id}', 'Détails d\'un ticket'],
                        ['POST', '/api/v1/support/{id}/reply', 'Répondre à un ticket'],
                        ['POST', '/api/v1/support/{id}/close', 'Fermer un ticket'],
                        ['GET', '/api/v1/support/stats', 'Statistiques support'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach

                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h3 class="text-sm font-semibold text-gray-300 mb-3">Catégories :</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['technical' => 'Technique', 'account' => 'Compte', 'payment' => 'Paiement', 'order' => 'Commande', 'general' => 'Général'] as $k => $v)
                            <span class="text-xs bg-gray-800 px-3 py-1 rounded-full"><code>{{ $k }}</code> — {{ $v }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==================== 14. Catégories ==================== --}}
            <section id="categories" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">📂</span> 14. Catégories
                </h2>
                <p class="text-gray-400 text-sm mb-6">🌐 Public (cache 60s) · 🔒 Admin pour CRUD</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/categories', '🌐 Liste des catégories'],
                        ['GET', '/api/v1/categories/{id}', '🌐 Détails'],
                        ['GET', '/api/v1/categories/{id}/items', '🌐 Articles d\'une catégorie'],
                        ['POST', '/api/v1/categories', '🔒 Créer (admin)'],
                        ['PUT', '/api/v1/categories/{id}', '🔒 Modifier (admin)'],
                        ['DELETE', '/api/v1/categories/{id}', '🔒 Supprimer (admin)'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 15. Marques ==================== --}}
            <section id="brands" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">🏷️</span> 15. Marques (Brands)
                </h2>
                <p class="text-gray-400 text-sm mb-6">🌐 Public (cache 60s) · 🔒 Auth pour CRUD</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/brands', '🌐 Liste des marques'],
                        ['GET', '/api/v1/brands/{id}', '🌐 Détails'],
                        ['GET', '/api/v1/brands/{id}/items', '🌐 Articles d\'une marque'],
                        ['POST', '/api/v1/brands', '🔒 Créer'],
                        ['PUT', '/api/v1/brands/{id}', '🔒 Modifier'],
                        ['DELETE', '/api/v1/brands/{id}', '🔒 Supprimer'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 16. Authenticité ==================== --}}
            <section id="authenticity" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">✅</span> 16. Vérification d'authenticité
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/items/{item}/authenticity/can-verify', 'Vérifier l\'éligibilité'],
                        ['POST', '/api/v1/items/{item}/authenticity/submit', 'Soumettre une demande (⚡ 20/min)'],
                        ['GET', '/api/v1/items/{item}/authenticity/status', 'Statut de la vérification'],
                        ['POST', '/api/v1/authenticity/{check}/confirm-payment', 'Confirmer le paiement'],
                        ['GET', '/api/v1/authenticity/dashboard', 'Dashboard des vérifications'],
                        ['PUT', '/api/v1/authenticity/{check}/update-status', 'Mettre à jour (expert)'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 17. VintPass ==================== --}}
            <section id="vintpass" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">🎫</span> 17. VintPass
                </h2>
                <p class="text-gray-400 text-sm mb-6">Certificat d'authenticité numérique</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/vintpass/verify/{shortCode}', '🌐 Public — Vérifier un VintPass'],
                        ['GET', '/api/v1/vintpass', '🔒 Mes VintPass'],
                        ['GET', '/api/v1/vintpass/{vintPassId}', '🔒 Détails'],
                        ['POST', '/api/v1/vintpass/request/{item}', '🔒 Demander un VintPass'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 18. Affiliation ==================== --}}
            <section id="affiliate" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">🤝</span> 18. Programme d'affiliation
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise · ⚡ 30 req/min</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/affiliate/dashboard', 'Dashboard affiliation'],
                        ['GET', '/api/affiliate/referral-codes', 'Liste des codes'],
                        ['POST', '/api/affiliate/referral-codes', 'Générer un code auto'],
                        ['POST', '/api/affiliate/referral-codes/custom', 'Code personnalisé'],
                        ['GET', '/api/affiliate/codes/stats', 'Stats par code'],
                        ['GET', '/api/affiliate/referrals', 'Liste des filleuls'],
                        ['GET', '/api/affiliate/points-history', 'Historique des points'],
                        ['POST', '/api/affiliate/convert-points', 'Convertir en cash'],
                        ['POST', '/api/affiliate/calculate-conversion', 'Simuler la conversion'],
                        ['POST', '/api/affiliate/apply-referral-code', 'Appliquer un code'],
                        ['GET', '/api/affiliate/generate-link', 'Générer un lien'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 19. Dashboard ==================== --}}
            <section id="dashboard" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">📊</span> 19. Dashboard
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise · ⚡ 30 req/min</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/dashboard/analytics', 'Analytics'],
                        ['GET', '/api/dashboard/user', 'Dashboard utilisateur'],
                        ['GET', '/api/dashboard/data', 'Données dashboard (JSON)'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 20. Chatbot ==================== --}}
            <section id="chatbot" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">🤖</span> 20. Chatbot
                </h2>
                <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="method-post text-xs font-bold px-2.5 py-1 rounded">POST</span>
                        <code class="text-white font-mono">/api/bot</code>
                    </div>
                    <div class="grid lg:grid-cols-2 gap-4">
                        <pre class="bg-gray-950 rounded-lg p-4 text-sm"><code class="text-green-300">{
  "question": "Comment vendre un article sur VintApp ?"
}</code></pre>
                        <pre class="bg-gray-950 rounded-lg p-4 text-sm"><code class="text-blue-300">{
  "answer": "Pour vendre un article sur VintApp, suivez ces étapes..."
}</code></pre>
                    </div>
                </div>
            </section>

            {{-- ==================== 21. FCM ==================== --}}
            <section id="fcm" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">📲</span> 21. Notifications Push (FCM)
                </h2>
                <div class="space-y-4">
                    @foreach([
                        ['POST', '/api/fcm-token', 'Enregistrer un token FCM'],
                        ['POST', '/api/test-fcm-notification', '🔒 Tester une notification'],
                        ['POST', '/api/notifications/subscribe', 'S\'abonner'],
                        ['POST', '/api/notifications/unsubscribe', 'Se désabonner'],
                        ['POST', '/api/notifications/test', 'Tester'],
                        ['POST', '/api/notifications/broadcast-test', 'Test broadcast'],
                        ['POST', '/api/admin/broadcast-fcm-test', '🔒 Admin — Broadcast FCM'],
                        ['GET', '/api/admin/fcm-stats', '🔒 Admin — Stats FCM'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 22. Administration ==================== --}}
            <section id="admin" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">⚙️</span> 22. Administration
                </h2>
                <p class="text-gray-400 text-sm mb-6">🔒 Auth requise + Rôle <code>admin</code> · Préfixe: <code>/api/v1/admin</code></p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/admin/dashboard', 'Dashboard admin'],
                        ['GET', '/api/v1/admin/stats/summary', 'Résumé statistiques'],
                        ['GET', '/api/v1/admin/notifications', 'Alertes admin'],
                        ['GET', '/api/v1/admin/reports?period=30', 'Rapports'],
                        ['GET', '/api/v1/admin/online-users', 'Utilisateurs en ligne'],
                        ['GET', '/api/v1/admin/users', 'Liste utilisateurs'],
                        ['GET', '/api/v1/admin/users/{userId}', 'Détails utilisateur'],
                        ['POST', '/api/v1/admin/users/{userId}/status', 'Changer statut utilisateur'],
                        ['GET', '/api/v1/admin/wallets', 'Liste portefeuilles'],
                        ['GET', '/api/v1/admin/wallets/pending', 'En attente d\'approbation'],
                        ['POST', '/api/v1/admin/wallets/{id}/approve', 'Approuver'],
                        ['POST', '/api/v1/admin/wallets/{id}/reject', 'Rejeter'],
                        ['POST', '/api/v1/admin/wallets/bulk-approve', 'Approbation en masse'],
                        ['GET', '/api/v1/admin/transactions', 'Transactions'],
                        ['GET', '/api/v1/admin/orders', 'Commandes'],
                        ['GET', '/api/v1/admin/items', 'Articles'],
                        ['POST', '/api/v1/admin/items/{itemId}/status', 'Changer statut article'],
                        ['GET', '/api/v1/admin/brands', 'Marques'],
                        ['GET', '/api/v1/admin/categories', 'Catégories'],
                        ['GET', '/api/v1/admin/support-chats', 'Support — Chats'],
                        ['GET', '/api/v1/admin/support/stats', 'Support — Stats'],
                        ['GET', '/api/v1/admin/verification-checks', 'Vérifications'],
                        ['GET', '/api/v1/admin/settings', 'Paramètres système'],
                        ['PUT', '/api/v1/admin/settings/{key}', 'Modifier un paramètre'],
                        ['GET', '/api/v1/admin/enterprise-wallets', 'Portefeuilles entreprise'],
                        ['GET', '/api/v1/admin/affiliate/stats', 'Affiliation — Stats'],
                        ['GET', '/api/v1/admin/affiliate/top-performers', 'Top performers'],
                        ['GET', '/api/v1/admin/refunds', 'Remboursements'],
                        ['GET', '/api/v1/admin/waiting-users', 'Utilisateurs en attente'],
                        ['GET', '/api/v1/admin/monitoring/stats', 'Monitoring — Stats'],
                        ['GET', '/api/v1/admin/monitoring/health', 'Monitoring — Health'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-3 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm truncate">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline whitespace-nowrap">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 23. Callbacks ==================== --}}
            <section id="callbacks" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <span class="text-indigo-400">🔄</span> 23. Callbacks de paiement
                </h2>
                <p class="text-gray-400 text-sm mb-6">🌐 Routes publiques (appelées par les opérateurs) · ⚡ 100 req/min</p>
                <div class="space-y-4">
                    @foreach([
                        ['POST', '/api/payment-callbacks/{provider}', 'Callback universel (mpesa, orange_money, ...)'],
                        ['GET', '/api/payment-callbacks/status', 'Vérifier statut (?transaction_id=TX-123)'],
                        ['POST', '/payments/maishapay/callback/{reference?}', 'Webhook MaishaPay (GET ou POST)'],
                        ['POST', '/api/v1/wallet/withdrawals/maishapay/callback', 'Webhook retrait MaishaPay'],
                    ] as $ep)
                    <div class="endpoint-card bg-gray-900 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-xs font-bold px-2.5 py-1 rounded whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-sm">{{ $ep[1] }}</code>
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 24. Codes d'erreur ==================== --}}
            <section id="errors" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="text-indigo-400">❌</span> 24. Codes d'erreur
                </h2>
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead><tr class="border-b border-gray-700 text-gray-400">
                                <th class="text-center py-2 pr-4 w-20">Code</th>
                                <th class="text-left py-2">Signification</th>
                            </tr></thead>
                            <tbody class="text-gray-300">
                                @foreach([
                                    [200, 'Succès', 'green'], [201, 'Ressource créée', 'green'],
                                    [400, 'Requête invalide', 'yellow'], [401, 'Non authentifié', 'yellow'],
                                    [403, 'Accès interdit', 'yellow'], [404, 'Ressource non trouvée', 'yellow'],
                                    [422, 'Erreur de validation', 'red'], [429, 'Trop de requêtes', 'red'],
                                    [500, 'Erreur serveur', 'red'],
                                ] as $err)
                                <tr class="border-b border-gray-800">
                                    <td class="py-2 text-center"><span class="text-{{ $err[2] }}-400 font-mono font-bold">{{ $err[0] }}</span></td>
                                    <td class="py-2">{{ $err[1] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                        <h4 class="text-sm text-red-400 font-semibold mb-2">422 — Validation</h4>
                        <pre class="bg-gray-950 rounded-lg p-3 text-xs"><code>{
  "success": false,
  "message": "Données invalides",
  "errors": {
    "email": ["Le champ email est obligatoire."]
  }
}</code></pre>
                    </div>
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                        <h4 class="text-sm text-yellow-400 font-semibold mb-2">401 — Non authentifié</h4>
                        <pre class="bg-gray-950 rounded-lg p-3 text-xs"><code>{
  "success": false,
  "message": "Informations de connexion incorrectes."
}</code></pre>
                    </div>
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                        <h4 class="text-sm text-amber-400 font-semibold mb-2">429 — Rate Limit</h4>
                        <pre class="bg-gray-950 rounded-lg p-3 text-xs"><code>{
  "message": "Too Many Attempts.",
  "retry_after": 60
}</code></pre>
                    </div>
                </div>
            </section>

            {{-- ==================== Annexes ==================== --}}
            <section id="annexes" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="text-indigo-400">📎</span> Annexes
                </h2>

                <div class="space-y-6">
                    {{-- États des articles --}}
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">États des articles</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['pending_verification' => 'En attente vérification', 'pending' => 'En attente', 'active' => 'Actif', 'sold' => 'Vendu', 'inactive' => 'Désactivé'] as $k => $v)
                            <span class="text-xs bg-gray-800 px-3 py-1.5 rounded-lg"><code class="text-pink-300">{{ $k }}</code> → {{ $v }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- États des commandes --}}
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">États des commandes</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['pending' => 'En attente', 'paid' => 'Payée', 'shipped' => 'Expédiée', 'delivered' => 'Livrée', 'confirmed' => 'Confirmée', 'cancelled' => 'Annulée'] as $k => $v)
                            <span class="text-xs bg-gray-800 px-3 py-1.5 rounded-lg"><code class="text-pink-300">{{ $k }}</code> → {{ $v }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Opérateurs Mobile Money --}}
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Opérateurs Mobile Money — 🇨🇩 RDC</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="border-b border-gray-700 text-gray-400">
                                    <th class="text-left py-2 pr-4">Opérateur</th>
                                    <th class="text-left py-2 pr-4">Code API</th>
                                    <th class="text-left py-2">Préfixes</th>
                                </tr></thead>
                                <tbody class="text-gray-300">
                                    <tr class="border-b border-gray-800"><td class="py-2">Vodacom M-Pesa</td><td><code>mpesa</code> / <code>VODACOM</code></td><td>081, 082, 083</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-2">Orange Money</td><td><code>orange_money</code> / <code>ORANGE</code></td><td>084, 085</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-2">Airtel Money</td><td><code>airtel_money</code> / <code>AIRTEL</code></td><td>097, 099</td></tr>
                                    <tr class="border-b border-gray-800"><td class="py-2">Africell</td><td><code>africell</code> / <code>AFRICELL</code></td><td>090, 091</td></tr>
                                    <tr><td class="py-2">Illicocash</td><td><code>illicocash</code></td><td>—</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Footer --}}
            <footer class="border-t border-gray-800 pt-8 pb-12 text-center">
                <p class="text-gray-500 text-sm">📖 VintApp API Documentation — v1.0.0 — Mars 2026</p>
                <p class="text-gray-600 text-xs mt-2">+150 endpoints · Laravel Sanctum · Mobile Money RDC</p>
            </footer>

        </div>
    </main>

    <script>
        // Toggle sidebar (mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        document.getElementById('sidebar-toggle').addEventListener('click', toggleSidebar);

        // Active sidebar link on scroll
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.sidebar-link');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    navLinks.forEach(link => link.classList.remove('active'));
                    const activeLink = document.querySelector(`.sidebar-link[href="#${entry.target.id}"]`);
                    if (activeLink) activeLink.classList.add('active');
                }
            });
        }, { rootMargin: '-20% 0px -80% 0px' });

        sections.forEach(section => observer.observe(section));

        // Close sidebar on link click (mobile)
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) toggleSidebar();
            });
        });

        // Search
        const searchInput = document.getElementById('search-input');
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.endpoint-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = query && !text.includes(query) ? 'none' : '';
            });
        });

        // Keyboard shortcut
        document.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    </script>
</body>
</html>
