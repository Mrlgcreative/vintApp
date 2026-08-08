<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VintApp — Documentation API</title>
    <link rel="icon" type="image/png" href="{{ asset('/favicon.png') }}">
    <meta name="description" content="Référence complète de l'API VintApp : authentification, articles, commandes, paiements, portefeuille, notifications et administration.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'Fira Code', 'ui-monospace', 'monospace'],
                    },
                },
            },
        };
    </script>
    <style>
        @import url('https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap');
        @import url('https://fonts.bunny.net/css?family=jetbrains-mono:400,500,600&display=swap');

        body { font-family: 'Figtree', sans-serif; }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0b1020; }
        ::-webkit-scrollbar-thumb { background: #2a3350; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #3b4670; }

        .sidebar { scrollbar-width: thin; scrollbar-color: #2a3350 #0b1020; }

        [id] { scroll-margin-top: 96px; }

        /* Active sidebar link */
        .sidebar-link { position: relative; transition: all 0.25s ease; }
        .sidebar-link::before {
            content: '';
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 0; border-radius: 3px;
            background: linear-gradient(180deg, #8b5cf6, #d946ef);
            transition: height 0.25s ease;
        }
        .sidebar-link:hover { background: rgba(139, 92, 246, 0.08); color: #e9d5ff; }
        .sidebar-link.active { background: rgba(139, 92, 246, 0.12); color: #c4b5fd; }
        .sidebar-link.active::before { height: 60%; }

        /* Code blocks */
        pre { position: relative; }
        pre code { display: block; }
        .copy-btn { opacity: 0; transition: opacity 0.2s; }
        pre:hover .copy-btn, .copy-btn.copied { opacity: 1; }

        /* Method badges */
        .method-get { background: rgba(16, 185, 129, 0.12); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .method-post { background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); }
        .method-put { background: rgba(245, 158, 11, 0.12); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .method-delete { background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .method-patch { background: rgba(56, 189, 248, 0.12); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); }

        /* Endpoint cards */
        .endpoint-card {
            background: linear-gradient(160deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
            border: 1px solid rgba(148, 163, 184, 0.12);
            transition: all 0.25s ease;
        }
        .endpoint-card:hover {
            border-color: rgba(139, 92, 246, 0.35);
            transform: translateY(-1px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.35), 0 0 0 1px rgba(139,92,246,0.08);
        }

        /* Ambient glow blobs */
        .glow-blob { position: absolute; border-radius: 9999px; filter: blur(90px); opacity: 0.35; pointer-events: none; }

        /* Skeleton shimmer for code line numbers not needed */

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeUp 0.5s ease-out both; }
    </style>
</head>
<body class="bg-[#0b1020] text-slate-300 min-h-screen antialiased selection:bg-violet-500/30 selection:text-white">

    {{-- Ambient background --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="glow-blob w-[500px] h-[500px] -top-40 -right-40 bg-violet-600/40"></div>
        <div class="glow-blob w-[400px] h-[400px] top-1/2 -left-48 bg-fuchsia-600/30"></div>
        <div class="glow-blob w-[350px] h-[350px] bottom-0 right-1/3 bg-indigo-600/25"></div>
        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.015)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.015)_1px,transparent_1px)] bg-[size:56px_56px]"></div>
    </div>

    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-[#0b1020]/80 backdrop-blur-xl border-b border-white/5">
        <div class="flex items-center justify-between px-4 lg:px-6 h-16">
            <div class="flex items-center gap-3">
                <button id="sidebar-toggle" class="lg:hidden p-2 rounded-lg hover:bg-white/5 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center shadow-lg shadow-violet-500/25 group-hover:shadow-violet-500/40 transition-shadow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/></svg>
                    </div>
                    <span class="text-lg font-bold text-white tracking-tight">VintApp <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-fuchsia-400">API</span></span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-violet-500/15 text-violet-300 border border-violet-500/30">v1.0.0</span>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:block relative">
                    <input type="text" id="search-input" placeholder="Rechercher un endpoint…" class="w-64 lg:w-72 bg-white/5 border border-white/10 rounded-lg pl-3 pr-10 py-1.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all">
                    <kbd class="absolute right-2 top-1.5 text-[10px] text-slate-500 bg-white/5 border border-white/10 px-1.5 py-0.5 rounded">⌘K</kbd>
                </div>
                <a href="{{ route('docs.api') }}" class="hidden md:inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-white/5 text-slate-300 border border-white/10 hover:bg-white/10 hover:text-white transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Télécharger
                </a>
            </div>
        </div>
    </header>

    {{-- Sidebar overlay (mobile) --}}
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="sidebar fixed top-16 left-0 bottom-0 w-72 bg-[#0d1326]/90 backdrop-blur-xl border-r border-white/5 overflow-y-auto z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <nav class="py-5 px-4">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] px-3 mb-2">Général</p>
            <a href="#info" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">📋 Informations générales</a>
            <a href="#auth" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">🔑 Authentification</a>
            <a href="#responses" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">📦 Format des réponses</a>
            <a href="#public" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">🌐 Routes publiques</a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] px-3 mt-6 mb-2">Ressources</p>
            <a href="#users" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">👤 Utilisateurs</a>
            <a href="#items" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">📦 Articles</a>
            <a href="#orders" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">🛒 Commandes</a>
            <a href="#messages" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">💬 Messages</a>
            <a href="#reviews" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">⭐ Avis</a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] px-3 mt-6 mb-2">Finances</p>
            <a href="#wallet" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">💰 Portefeuille</a>
            <a href="#payments" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">💳 Paiements</a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] px-3 mt-6 mb-2">Fonctionnalités</p>
            <a href="#notifications" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">🔔 Notifications</a>
            <a href="#support" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">🎧 Support</a>
            <a href="#categories" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">📂 Catégories</a>
            <a href="#brands" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">🏷️ Marques</a>
            <a href="#authenticity" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">✅ Authenticité</a>
            <a href="#vintpass" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">🎫 VintPass</a>
            <a href="#affiliate" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">🤝 Affiliation</a>
            <a href="#dashboard" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">📊 Dashboard</a>
            <a href="#chatbot" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">🤖 Chatbot</a>
            <a href="#fcm" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">📲 Push (FCM)</a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] px-3 mt-6 mb-2">Administration</p>
            <a href="#admin" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">⚙️ Administration</a>
            <a href="#callbacks" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">🔄 Callbacks</a>
            <a href="#errors" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">❌ Codes d'erreur</a>
            <a href="#annexes" class="sidebar-link block px-3 py-2 text-[13px] rounded-lg text-slate-400 hover:text-slate-200">📎 Annexes</a>

            <div class="mt-8 px-3">
                <div class="rounded-xl border border-violet-500/20 bg-violet-500/5 p-4">
                    <p class="text-xs font-semibold text-violet-300 mb-1">Besoin d'aide ?</p>
                    <p class="text-[11px] text-slate-500 leading-relaxed mb-3">Une question sur l'API ? Contactez notre équipe.</p>
                    <a href="mailto:{{ config('mail.from.address') }}" class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-violet-300 hover:text-violet-200 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        Support technique
                    </a>
                </div>
            </div>
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="lg:ml-72 pt-16 relative z-10">
        <div class="max-w-4xl mx-auto px-4 lg:px-8 py-10">

            {{-- Hero --}}
            <div class="mb-12 animate-fade-up">
                <div class="inline-flex items-center gap-2 text-xs font-medium text-violet-300 bg-violet-500/10 border border-violet-500/20 rounded-full px-3 py-1 mb-5">
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-pulse"></span>
                    API REST · Laravel Sanctum · JSON
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-4">
                    Documentation
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-fuchsia-400 to-violet-400">API VintApp</span>
                </h1>
                <p class="text-slate-400 text-lg mb-8 max-w-2xl">Référence complète de l'API VintApp — <span class="text-white font-semibold">172 endpoints</span> pour intégrer votre marketplace, gérer les paiements Mobile Money et le portefeuille.</p>
                <div class="flex flex-wrap gap-3">
                    <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-slate-400">Base URL:</span>
                        <code class="text-violet-300 font-mono text-[13px]">{{ url('/api') }}</code>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm">
                        <span class="text-slate-400">Auth:</span> <span class="text-amber-300 font-medium">Bearer Token (Sanctum)</span>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm">
                        <span class="text-slate-400">Format:</span> <span class="text-sky-300 font-medium">JSON</span>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm">
                        <span class="text-slate-400">Version:</span> <span class="text-fuchsia-300 font-medium">v1.0.0</span>
                    </div>
                </div>
            </div>

            {{-- ==================== 1. Informations générales ==================== --}}
            <section id="info" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">📋</span>
                    1. Informations générales
                </h2>
                <p class="text-slate-500 text-sm mb-6">Principes de base pour interagir avec l'API VintApp.</p>

                <div class="space-y-6">
                    {{-- Headers requis --}}
                    <div class="endpoint-card rounded-2xl p-6">
                        <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Headers requis
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="border-b border-white/10 text-slate-500">
                                    <th class="text-left py-2.5 pr-4 font-medium">Header</th>
                                    <th class="text-left py-2.5 pr-4 font-medium">Valeur</th>
                                    <th class="text-center py-2.5 font-medium">Obligatoire</th>
                                </tr></thead>
                                <tbody class="text-slate-300">
                                    <tr class="border-b border-white/5"><td class="py-2.5 pr-4"><code class="text-pink-400 font-mono text-[13px]">Accept</code></td><td class="font-mono text-[13px] text-slate-400">application/json</td><td class="text-center">✅</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-2.5 pr-4"><code class="text-pink-400 font-mono text-[13px]">Content-Type</code></td><td class="font-mono text-[13px] text-slate-400">application/json</td><td class="text-center">✅ POST/PUT</td></tr>
                                    <tr><td class="py-2.5 pr-4"><code class="text-pink-400 font-mono text-[13px]">Authorization</code></td><td class="font-mono text-[13px] text-slate-400">Bearer &#123;token&#125;</td><td class="text-center">✅ Routes protégées</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Devises --}}
                    <div class="endpoint-card rounded-2xl p-6">
                        <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            Devises supportées
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/5 rounded-xl p-4 flex items-center gap-3 border border-white/5 hover:border-white/10 transition-colors">
                                <span class="text-3xl">🇺🇸</span>
                                <div><p class="font-semibold text-white">USD</p><p class="text-sm text-slate-500">Dollar américain ($)</p></div>
                            </div>
                            <div class="bg-white/5 rounded-xl p-4 flex items-center gap-3 border border-white/5 hover:border-white/10 transition-colors">
                                <span class="text-3xl">🇨🇩</span>
                                <div><p class="font-semibold text-white">CDF</p><p class="text-sm text-slate-500">Franc congolais (FC)</p></div>
                            </div>
                        </div>
                        <p class="mt-4 text-sm text-slate-500">💱 Taux de conversion : <span class="text-white font-medium">1 USD = 2 500 CDF</span></p>
                    </div>

                    {{-- Rate Limiting --}}
                    <div class="endpoint-card rounded-2xl p-6">
                        <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Rate Limiting
                        </h3>
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
                            <div class="bg-white/5 rounded-lg px-3 py-2.5 text-sm flex justify-between items-center border border-white/5">
                                <span class="text-slate-400">{{ $limit[0] }}</span>
                                <span class="text-amber-300 font-mono text-[13px]">{{ $limit[1] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==================== 2. Authentification ==================== --}}
            <section id="auth" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">🔑</span>
                    2. Authentification
                </h2>
                <p class="text-slate-500 text-sm mb-6">L'API utilise <span class="text-white font-medium">Laravel Sanctum</span> avec des tokens Bearer.</p>

                {{-- Register --}}
                <div class="endpoint-card rounded-2xl p-6 mb-4">
                    <div class="flex items-center gap-3 mb-4 flex-wrap">
                        <span class="method-post text-[11px] font-bold px-2.5 py-1 rounded-md">POST</span>
                        <code class="text-white font-mono text-sm">{{ url('/api/register') }}</code>
                        <span class="text-xs text-slate-500">Inscription</span>
                    </div>
                    <div class="grid lg:grid-cols-2 gap-4">
                        <div>
                            <p class="text-[11px] text-slate-500 uppercase tracking-wider mb-2">Body</p>
                            <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm overflow-x-auto"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-emerald-300 font-mono">{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}</code></pre>
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-500 uppercase tracking-wider mb-2">Réponse 201</p>
                            <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm overflow-x-auto"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-sky-300 font-mono">{
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
                <div class="endpoint-card rounded-2xl p-6 mb-4">
                    <div class="flex items-center gap-3 mb-4 flex-wrap">
                        <span class="method-post text-[11px] font-bold px-2.5 py-1 rounded-md">POST</span>
                        <code class="text-white font-mono text-sm">{{ url('/api/login') }}</code>
                        <span class="text-xs text-slate-500">Connexion</span>
                    </div>
                    <div class="grid lg:grid-cols-2 gap-4">
                        <div>
                            <p class="text-[11px] text-slate-500 uppercase tracking-wider mb-2">Body</p>
                            <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm overflow-x-auto"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-emerald-300 font-mono">{
  "email": "john@example.com",
  "password": "password123"
}</code></pre>
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-500 uppercase tracking-wider mb-2">Réponse 200</p>
                            <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm overflow-x-auto"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-sky-300 font-mono">{
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
                <div class="endpoint-card rounded-2xl p-6 mb-4">
                    <div class="flex items-center gap-3 mb-3 flex-wrap">
                        <span class="method-post text-[11px] font-bold px-2.5 py-1 rounded-md">POST</span>
                        <code class="text-white font-mono text-sm">{{ url('/api/logout') }}</code>
                        <span class="text-[11px] bg-amber-500/10 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-md">🔒 Auth</span>
                    </div>
                    <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-sky-300 font-mono">{ "success": true, "message": "Déconnexion réussie" }</code></pre>
                </div>

                {{-- Current user --}}
                <div class="endpoint-card rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-3 flex-wrap">
                        <span class="method-get text-[11px] font-bold px-2.5 py-1 rounded-md">GET</span>
                        <code class="text-white font-mono text-sm">{{ url('/api/user') }}</code>
                        <span class="text-[11px] bg-amber-500/10 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-md">🔒 Auth</span>
                        <span class="text-xs text-slate-500">Utilisateur connecté</span>
                    </div>
                    <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm overflow-x-auto"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-sky-300 font-mono">{
  "success": true,
  "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "avatar": "avatars/john.jpg", "role": "user" }
}</code></pre>
                </div>
            </section>

            {{-- ==================== 3. Format des réponses ==================== --}}
            <section id="responses" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">📦</span>
                    3. Format des réponses
                </h2>
                <p class="text-slate-500 text-sm mb-6">Toutes les réponses suivent une structure JSON cohérente.</p>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="endpoint-card rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-emerald-400 mb-3 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>✅ Succès
                        </h3>
                        <pre class="bg-[#0b1020] border border-white/5 rounded-lg p-3.5 text-xs overflow-x-auto"><code class="font-mono">{
  "success": true,
  "message": "Opération réussie",
  "data": { ... }
}</code></pre>
                    </div>
                    <div class="endpoint-card rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-sky-400 mb-3 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>📄 Paginée
                        </h3>
                        <pre class="bg-[#0b1020] border border-white/5 rounded-lg p-3.5 text-xs overflow-x-auto"><code class="font-mono">{
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
                    <div class="endpoint-card rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-red-400 mb-3 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>❌ Erreur
                        </h3>
                        <pre class="bg-[#0b1020] border border-white/5 rounded-lg p-3.5 text-xs overflow-x-auto"><code class="font-mono">{
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
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">🌐</span>
                    4. Routes publiques
                </h2>
                <p class="text-slate-500 text-sm mb-6">Accessibles <span class="text-white font-medium">sans authentification</span>.</p>
                <div class="space-y-4">
                    <div class="endpoint-card rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3 flex-wrap">
                            <span class="method-get text-[11px] font-bold px-2.5 py-1 rounded-md">GET</span>
                            <code class="text-white font-mono text-sm">{{ url('/api/health') }}</code>
                            <span class="text-xs text-slate-500">⏱️ Cache 60s</span>
                        </div>
                        <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-sky-300 font-mono">{ "status": "success", "message": "VintApp API is running", "version": "1.0.0" }</code></pre>
                    </div>
                    <div class="endpoint-card rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3 flex-wrap">
                            <span class="method-get text-[11px] font-bold px-2.5 py-1 rounded-md">GET</span>
                            <code class="text-white font-mono text-sm">{{ url('/api/v1/home') }}</code>
                            <span class="text-xs text-slate-500">Page d'accueil</span>
                        </div>
                        <p class="text-sm text-slate-400">Retourne : <code class="text-violet-300 font-mono text-[13px]">categories, spotlight_items, boosted_items, latest_items, stats, hero_slides</code>.</p>
                    </div>
                    <div class="endpoint-card rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3 flex-wrap">
                            <span class="method-get text-[11px] font-bold px-2.5 py-1 rounded-md">GET</span>
                            <code class="text-white font-mono text-sm">{{ url('/api/v1/currencies') }}</code>
                            <span class="text-xs text-slate-500">⏱️ Cache 60s</span>
                        </div>
                        <p class="text-sm text-slate-400">Liste des devises supportées (USD, CDF).</p>
                    </div>
                    <div class="endpoint-card rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3 flex-wrap">
                            <span class="method-post text-[11px] font-bold px-2.5 py-1 rounded-md">POST</span>
                            <code class="text-white font-mono text-sm">{{ url('/api/validate-location') }}</code>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-4">
                            <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-emerald-300 font-mono">// Par ville
{ "city": "Kinshasa" }

// Par coordonnées GPS
{ "latitude": -4.3217, "longitude": 15.3127 }</code></pre>
                            <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-sky-300 font-mono">{ "success": true, "allowed": true, "city": "Kinshasa" }</code></pre>
                        </div>
                    </div>
                    <div class="endpoint-card rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3 flex-wrap">
                            <span class="method-post text-[11px] font-bold px-2.5 py-1 rounded-md">POST</span>
                            <code class="text-white font-mono text-sm">{{ url('/api/validate-referral-code') }}</code>
                            <span class="text-xs text-slate-500">⚡ 10 req/min</span>
                        </div>
                        <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-emerald-300 font-mono">{ "code": "VINT-ABC123" }</code></pre>
                    </div>
                </div>
            </section>

            {{-- ==================== 5. Utilisateurs ==================== --}}
            <section id="users" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">👤</span>
                    5. Utilisateurs
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise · ⚡ 60 req/min</p>
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
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach

                    <div class="endpoint-card rounded-2xl p-6">
                        <h3 class="text-sm font-semibold text-slate-200 mb-3">Exemple — PUT {{ url('/api/v1/user/profile') }}</h3>
                        <div class="grid lg:grid-cols-2 gap-4">
                            <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-emerald-300 font-mono">{
  "name": "John Updated",
  "email": "john.new@example.com",
  "phone": "+243999000000",
  "city": "Lubumbashi",
  "bio": "Vendeur passionné"
}</code></pre>
                            <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-sky-300 font-mono">{
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
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">📦</span>
                    6. Articles (Items)
                </h2>
                <p class="text-slate-500 text-sm mb-6">⏱️ Cache 60s pour les routes publiques · ⚡ 20 req/min pour l'écriture</p>

                <div class="space-y-4">
                    {{-- List items --}}
                    <div class="endpoint-card rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-4 flex-wrap">
                            <span class="method-get text-[11px] font-bold px-2.5 py-1 rounded-md">GET</span>
                            <code class="text-white font-mono text-sm">{{ url('/api/v1/items') }}</code>
                            <span class="text-[11px] bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 rounded-md">🌐 Public</span>
                        </div>
                        <h4 class="text-[11px] text-slate-500 uppercase tracking-wider mb-2">Paramètres de requête</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="border-b border-white/10 text-slate-500">
                                    <th class="text-left py-2 pr-4 font-medium">Paramètre</th><th class="text-left py-2 pr-4 font-medium">Type</th><th class="text-left py-2 font-medium">Description</th>
                                </tr></thead>
                                <tbody class="text-slate-300">
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">category_id</code></td><td class="text-[13px]">integer</td><td>Filtrer par catégorie</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">brand_id</code></td><td class="text-[13px]">integer</td><td>Filtrer par marque</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">min_price</code> / <code class="font-mono text-[13px]">max_price</code></td><td class="text-[13px]">number</td><td>Fourchette de prix</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">condition</code></td><td class="text-[13px]">string</td><td>new, like_new, good, fair</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">search</code></td><td class="text-[13px]">string</td><td>Recherche textuelle</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">sort_by</code> / <code class="font-mono text-[13px]">sort_order</code></td><td class="text-[13px]">string</td><td>Tri (asc/desc)</td></tr>
                                    <tr><td class="py-1.5"><code class="font-mono text-[13px]">per_page</code></td><td class="text-[13px]">integer</td><td>Résultats par page (défaut: 15)</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-get text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">GET</span>
                        <code class="text-white font-mono text-[13px]">/api/v1/items/{id}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">🌐 Détails d'un article</span>
                    </div>

                    {{-- Create item --}}
                    <div class="endpoint-card rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-4 flex-wrap">
                            <span class="method-post text-[11px] font-bold px-2.5 py-1 rounded-md">POST</span>
                            <code class="text-white font-mono text-sm">{{ url('/api/v1/items') }}</code>
                            <span class="text-[11px] bg-amber-500/10 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-md">🔒 Auth</span>
                            <span class="text-xs text-slate-500">multipart/form-data</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="border-b border-white/10 text-slate-500">
                                    <th class="text-left py-2 pr-4 font-medium">Champ</th><th class="text-left py-2 pr-4 font-medium">Type</th><th class="text-center py-2 pr-4 font-medium">Requis</th><th class="text-left py-2 font-medium">Description</th>
                                </tr></thead>
                                <tbody class="text-slate-300">
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">name</code></td><td class="text-[13px]">string</td><td class="text-center">✅</td><td>Nom de l'article</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">description</code></td><td class="text-[13px]">string</td><td class="text-center">✅</td><td>Description détaillée</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">price</code></td><td class="text-[13px]">number</td><td class="text-center">✅</td><td>Prix de vente</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">currency</code></td><td class="text-[13px]">string</td><td class="text-center">✅</td><td>USD ou CDF</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">quantity</code></td><td class="text-[13px]">integer</td><td class="text-center">✅</td><td>Quantité disponible</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">condition</code></td><td class="text-[13px]">string</td><td class="text-center">✅</td><td>new, like_new, good, fair</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">category_id</code></td><td class="text-[13px]">integer</td><td class="text-center">✅</td><td>ID catégorie</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">brand_id</code></td><td class="text-[13px]">integer</td><td class="text-center">—</td><td>ID marque</td></tr>
                                    <tr><td class="py-1.5"><code class="font-mono text-[13px]">images[]</code></td><td class="text-[13px]">file[]</td><td class="text-center">—</td><td>Images de l'article</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-amber-300/90 mt-3">⚠️ L'article est créé avec le statut <code class="font-mono">pending_verification</code> et doit être approuvé par un admin.</p>
                    </div>

                    @foreach([['PUT', '/api/v1/items/{id}', 'Modifier (propriétaire)'], ['DELETE', '/api/v1/items/{id}', 'Supprimer (propriétaire)'], ['POST', '/api/items/{item}/favorite', 'Toggle favori (⚡ 30/min)'], ['GET', '/api/items/search', 'Recherche (?q=iphone&category=1&min_price=100)']] as $ep)
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 7. Commandes ==================== --}}
            <section id="orders" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">🛒</span>
                    7. Commandes (Orders)
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise · ⚡ 40 req/min</p>
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
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach

                    {{-- Create order body --}}
                    <div class="endpoint-card rounded-2xl p-6">
                        <h3 class="text-sm font-semibold text-slate-200 mb-3">Body — POST {{ url('/api/v1/orders') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="border-b border-white/10 text-slate-500">
                                    <th class="text-left py-2 pr-4 font-medium">Champ</th><th class="text-left py-2 pr-4 font-medium">Type</th><th class="text-center py-2 pr-4 font-medium">Requis</th><th class="text-left py-2 font-medium">Description</th>
                                </tr></thead>
                                <tbody class="text-slate-300">
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">item_id</code></td><td class="text-[13px]">integer</td><td class="text-center">✅</td><td>ID de l'article</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">quantity</code></td><td class="text-[13px]">integer</td><td class="text-center">✅</td><td>Quantité</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">shipping_address</code></td><td class="text-[13px]">string</td><td class="text-center">✅</td><td>Adresse de livraison</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">shipping_city</code></td><td class="text-[13px]">string</td><td class="text-center">✅</td><td>Ville</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-1.5"><code class="font-mono text-[13px]">shipping_phone</code></td><td class="text-[13px]">string</td><td class="text-center">✅</td><td>Téléphone</td></tr>
                                    <tr><td class="py-1.5"><code class="font-mono text-[13px]">notes</code></td><td class="text-[13px]">string</td><td class="text-center">—</td><td>Notes supplémentaires</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==================== 8. Messages ==================== --}}
            <section id="messages" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">💬</span>
                    8. Messages
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise · ⚡ 50 req/min</p>
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
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 9. Avis ==================== --}}
            <section id="reviews" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">⭐</span>
                    9. Avis (Reviews)
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise · ⚡ 20 req/min</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/reviews', 'Tous les avis'],
                        ['GET', '/api/v1/reviews/item/{itemId}', 'Avis d\'un article'],
                        ['GET', '/api/v1/reviews/seller/{sellerId}', 'Avis d\'un vendeur'],
                        ['POST', '/api/v1/reviews', 'Créer un avis (order_id, rating 1-5, comment)'],
                        ['PUT', '/api/v1/reviews/{reviewId}', 'Modifier un avis (auteur)'],
                        ['DELETE', '/api/v1/reviews/{reviewId}', 'Supprimer un avis (auteur)'],
                    ] as $ep)
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 10. Portefeuille ==================== --}}
            <section id="wallet" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">💰</span>
                    10. Portefeuille (Wallet)
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise</p>
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
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach

                    {{-- Opérateurs --}}
                    <div class="endpoint-card rounded-2xl p-6">
                        <h3 class="text-sm font-semibold text-slate-200 mb-3">📱 Opérateurs Mobile Money</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([['mpesa', 'M-Pesa / Vodacom', '081-083'], ['orange_money', 'Orange Money', '084-085'], ['airtel_money', 'Airtel Money', '097, 099'], ['africell', 'Africell', '090-091'], ['illicocash', 'Illicocash', '—']] as $op)
                            <div class="bg-white/5 rounded-lg px-3 py-2.5 border border-white/5">
                                <p class="text-white text-sm font-medium">{{ $op[1] }}</p>
                                <p class="text-xs text-slate-500"><code class="font-mono text-[11px]">{{ $op[0] }}</code> · {{ $op[2] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==================== 11. Paiements ==================== --}}
            <section id="payments" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">💳</span>
                    11. Paiements
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise</p>
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
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                    <p class="text-xs text-amber-300/90 px-1">⚠️ Les remboursements doivent être demandés dans les <strong>30 jours</strong> suivant la commande.</p>
                </div>
            </section>

            {{-- ==================== 12. Notifications ==================== --}}
            <section id="notifications" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">🔔</span>
                    12. Notifications
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise · ⚡ 60 req/min</p>
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
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 13. Support ==================== --}}
            <section id="support" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">🎧</span>
                    13. Support
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/support', 'Mes tickets'],
                        ['POST', '/api/v1/support', 'Créer un ticket'],
                        ['GET', '/api/v1/support/{id}', 'Détails d\'un ticket'],
                        ['POST', '/api/v1/support/{id}/reply', 'Répondre à un ticket'],
                        ['POST', '/api/v1/support/{id}/close', 'Fermer un ticket'],
                        ['GET', '/api/v1/support/stats', 'Statistiques support'],
                    ] as $ep)
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach

                    <div class="endpoint-card rounded-2xl p-6">
                        <h3 class="text-sm font-semibold text-slate-200 mb-3">Catégories :</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['technical' => 'Technique', 'account' => 'Compte', 'payment' => 'Paiement', 'order' => 'Commande', 'general' => 'Général'] as $k => $v)
                            <span class="text-xs bg-white/5 border border-white/10 px-3 py-1 rounded-full"><code class="font-mono text-violet-300">{{ $k }}</code> — {{ $v }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==================== 14. Catégories ==================== --}}
            <section id="categories" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">📂</span>
                    14. Catégories
                </h2>
                <p class="text-slate-500 text-sm mb-6">🌐 Public (cache 60s) · 🔒 Admin pour CRUD</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/categories', '🌐 Liste des catégories'],
                        ['GET', '/api/v1/categories/{id}', '🌐 Détails'],
                        ['GET', '/api/v1/categories/{id}/items', '🌐 Articles d\'une catégorie'],
                        ['POST', '/api/v1/categories', '🔒 Créer (admin)'],
                        ['PUT', '/api/v1/categories/{id}', '🔒 Modifier (admin)'],
                        ['DELETE', '/api/v1/categories/{id}', '🔒 Supprimer (admin)'],
                    ] as $ep)
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 15. Marques ==================== --}}
            <section id="brands" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">🏷️</span>
                    15. Marques (Brands)
                </h2>
                <p class="text-slate-500 text-sm mb-6">🌐 Public (cache 60s) · 🔒 Auth pour CRUD</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/brands', '🌐 Liste des marques'],
                        ['GET', '/api/v1/brands/{id}', '🌐 Détails'],
                        ['GET', '/api/v1/brands/{id}/items', '🌐 Articles d\'une marque'],
                        ['POST', '/api/v1/brands', '🔒 Créer'],
                        ['PUT', '/api/v1/brands/{id}', '🔒 Modifier'],
                        ['DELETE', '/api/v1/brands/{id}', '🔒 Supprimer'],
                    ] as $ep)
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 16. Authenticité ==================== --}}
            <section id="authenticity" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">✅</span>
                    16. Vérification d'authenticité
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/items/{item}/authenticity/can-verify', 'Vérifier l\'éligibilité'],
                        ['POST', '/api/v1/items/{item}/authenticity/submit', 'Soumettre une demande (⚡ 20/min)'],
                        ['GET', '/api/v1/items/{item}/authenticity/status', 'Statut de la vérification'],
                        ['POST', '/api/v1/authenticity/{check}/confirm-payment', 'Confirmer le paiement'],
                        ['GET', '/api/v1/authenticity/dashboard', 'Dashboard des vérifications'],
                        ['PUT', '/api/v1/authenticity/{check}/update-status', 'Mettre à jour (expert)'],
                    ] as $ep)
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 17. VintPass ==================== --}}
            <section id="vintpass" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">🎫</span>
                    17. VintPass
                </h2>
                <p class="text-slate-500 text-sm mb-6">Certificat d'authenticité numérique</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/v1/vintpass/verify/{shortCode}', '🌐 Public — Vérifier un VintPass'],
                        ['GET', '/api/v1/vintpass', '🔒 Mes VintPass'],
                        ['GET', '/api/v1/vintpass/{vintPassId}', '🔒 Détails'],
                        ['POST', '/api/v1/vintpass/request/{item}', '🔒 Demander un VintPass'],
                    ] as $ep)
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 18. Affiliation ==================== --}}
            <section id="affiliate" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">🤝</span>
                    18. Programme d'affiliation
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise · ⚡ 30 req/min</p>
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
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 19. Dashboard ==================== --}}
            <section id="dashboard" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">📊</span>
                    19. Dashboard
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise · ⚡ 30 req/min</p>
                <div class="space-y-4">
                    @foreach([
                        ['GET', '/api/dashboard/analytics', 'Analytics'],
                        ['GET', '/api/dashboard/user', 'Dashboard utilisateur'],
                        ['GET', '/api/dashboard/data', 'Données dashboard (JSON)'],
                    ] as $ep)
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 20. Chatbot ==================== --}}
            <section id="chatbot" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">🤖</span>
                    20. Chatbot
                </h2>
                <div class="endpoint-card rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-4 flex-wrap">
                        <span class="method-post text-[11px] font-bold px-2.5 py-1 rounded-md">POST</span>
                        <code class="text-white font-mono text-sm">{{ url('/api/bot') }}</code>
                    </div>
                    <div class="grid lg:grid-cols-2 gap-4">
                        <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-emerald-300 font-mono">{
  "question": "Comment vendre un article sur VintApp ?"
}</code></pre>
                        <pre class="bg-[#0b1020] border border-white/5 rounded-xl p-4 text-sm"><button class="copy-btn text-xs text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 px-2 py-1 rounded-md" onclick="copyCode(this)">Copier</button><code class="text-sky-300 font-mono">{
  "answer": "Pour vendre un article sur VintApp, suivez ces étapes..."
}</code></pre>
                    </div>
                </div>
            </section>

            {{-- ==================== 21. FCM ==================== --}}
            <section id="fcm" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">📲</span>
                    21. Notifications Push (FCM)
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
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 22. Administration ==================== --}}
            <section id="admin" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">⚙️</span>
                    22. Administration
                </h2>
                <p class="text-slate-500 text-sm mb-6">🔒 Auth requise + Rôle <code class="font-mono text-violet-300">admin</code> · Préfixe: <code class="font-mono text-violet-300">/api/v1/admin</code></p>
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
                    <div class="endpoint-card rounded-xl px-5 py-3 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px] truncate">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline whitespace-nowrap ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 23. Callbacks ==================== --}}
            <section id="callbacks" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">🔄</span>
                    23. Callbacks de paiement
                </h2>
                <p class="text-slate-500 text-sm mb-6">🌐 Routes publiques (appelées par les opérateurs) · ⚡ 100 req/min</p>
                <div class="space-y-4">
                    @foreach([
                        ['POST', '/api/payment-callbacks/{provider}', 'Callback universel (mpesa, orange_money, ...)'],
                        ['GET', '/api/payment-callbacks/status', 'Vérifier statut (?transaction_id=TX-123)'],
                        ['POST', '/payments/maishapay/callback/{reference?}', 'Webhook MaishaPay (GET ou POST)'],
                        ['POST', '/api/v1/wallet/withdrawals/maishapay/callback', 'Webhook retrait MaishaPay'],
                    ] as $ep)
                    <div class="endpoint-card rounded-xl px-5 py-3.5 flex items-center gap-3">
                        <span class="method-{{ strtolower($ep[0]) }} text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">{{ $ep[0] }}</span>
                        <code class="text-white font-mono text-[13px]">{{ $ep[1] }}</code>
                        <span class="text-xs text-slate-500 hidden sm:inline ml-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ==================== 24. Codes d'erreur ==================== --}}
            <section id="errors" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">❌</span>
                    24. Codes d'erreur
                </h2>
                <div class="endpoint-card rounded-2xl p-6 mb-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead><tr class="border-b border-white/10 text-slate-500">
                                <th class="text-center py-2.5 pr-4 w-20 font-medium">Code</th>
                                <th class="text-left py-2.5 font-medium">Signification</th>
                            </tr></thead>
                            <tbody class="text-slate-300">
                                @foreach([
                                    [200, 'Succès', 'green'], [201, 'Ressource créée', 'green'],
                                    [400, 'Requête invalide', 'yellow'], [401, 'Non authentifié', 'yellow'],
                                    [403, 'Accès interdit', 'yellow'], [404, 'Ressource non trouvée', 'yellow'],
                                    [422, 'Erreur de validation', 'red'], [429, 'Trop de requêtes', 'red'],
                                    [500, 'Erreur serveur', 'red'],
                                ] as $err)
                                <tr class="border-b border-white/5">
                                    <td class="py-2 text-center"><span class="text-{{ $err[2] }}-400 font-mono font-bold">{{ $err[0] }}</span></td>
                                    <td class="py-2">{{ $err[1] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="endpoint-card rounded-2xl p-5">
                        <h4 class="text-sm text-red-400 font-semibold mb-2">422 — Validation</h4>
                        <pre class="bg-[#0b1020] border border-white/5 rounded-lg p-3.5 text-xs"><code class="font-mono">{
  "success": false,
  "message": "Données invalides",
  "errors": {
    "email": ["Le champ email est obligatoire."]
  }
}</code></pre>
                    </div>
                    <div class="endpoint-card rounded-2xl p-5">
                        <h4 class="text-sm text-yellow-400 font-semibold mb-2">401 — Non authentifié</h4>
                        <pre class="bg-[#0b1020] border border-white/5 rounded-lg p-3.5 text-xs"><code class="font-mono">{
  "success": false,
  "message": "Informations de connexion incorrectes."
}</code></pre>
                    </div>
                    <div class="endpoint-card rounded-2xl p-5">
                        <h4 class="text-sm text-amber-400 font-semibold mb-2">429 — Rate Limit</h4>
                        <pre class="bg-[#0b1020] border border-white/5 rounded-lg p-3.5 text-xs"><code class="font-mono">{
  "message": "Too Many Attempts.",
  "retry_after": 60
}</code></pre>
                    </div>
                </div>
            </section>

            {{-- ==================== Annexes ==================== --}}
            <section id="annexes" class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-sm shadow-lg shadow-violet-500/25">📎</span>
                    Annexes
                </h2>

                <div class="space-y-6">
                    {{-- États des articles --}}
                    <div class="endpoint-card rounded-2xl p-6">
                        <h3 class="text-base font-semibold text-white mb-4">États des articles</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['pending_verification' => 'En attente vérification', 'pending' => 'En attente', 'active' => 'Actif', 'sold' => 'Vendu', 'inactive' => 'Désactivé'] as $k => $v)
                            <span class="text-xs bg-white/5 border border-white/10 px-3 py-1.5 rounded-lg"><code class="font-mono text-pink-300">{{ $k }}</code> → {{ $v }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- États des commandes --}}
                    <div class="endpoint-card rounded-2xl p-6">
                        <h3 class="text-base font-semibold text-white mb-4">États des commandes</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['pending' => 'En attente', 'paid' => 'Payée', 'shipped' => 'Expédiée', 'delivered' => 'Livrée', 'confirmed' => 'Confirmée', 'cancelled' => 'Annulée'] as $k => $v)
                            <span class="text-xs bg-white/5 border border-white/10 px-3 py-1.5 rounded-lg"><code class="font-mono text-pink-300">{{ $k }}</code> → {{ $v }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Opérateurs Mobile Money --}}
                    <div class="endpoint-card rounded-2xl p-6">
                        <h3 class="text-base font-semibold text-white mb-4">Opérateurs Mobile Money — 🇨🇩 RDC</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead><tr class="border-b border-white/10 text-slate-500">
                                    <th class="text-left py-2.5 pr-4 font-medium">Opérateur</th>
                                    <th class="text-left py-2.5 pr-4 font-medium">Code API</th>
                                    <th class="text-left py-2.5 font-medium">Préfixes</th>
                                </tr></thead>
                                <tbody class="text-slate-300">
                                    <tr class="border-b border-white/5"><td class="py-2.5">Vodacom M-Pesa</td><td><code class="font-mono text-[13px]">mpesa</code> / <code class="font-mono text-[13px]">VODACOM</code></td><td>081, 082, 083</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-2.5">Orange Money</td><td><code class="font-mono text-[13px]">orange_money</code> / <code class="font-mono text-[13px]">ORANGE</code></td><td>084, 085</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-2.5">Airtel Money</td><td><code class="font-mono text-[13px]">airtel_money</code> / <code class="font-mono text-[13px]">AIRTEL</code></td><td>097, 099</td></tr>
                                    <tr class="border-b border-white/5"><td class="py-2.5">Africell</td><td><code class="font-mono text-[13px]">africell</code> / <code class="font-mono text-[13px]">AFRICELL</code></td><td>090, 091</td></tr>
                                    <tr><td class="py-2.5">Illicocash</td><td><code class="font-mono text-[13px]">illicocash</code></td><td>—</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Footer --}}
            <footer class="border-t border-white/5 pt-8 pb-12 text-center relative">
                <div class="flex flex-col items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center shadow-lg shadow-violet-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/></svg>
                    </div>
                    <p class="text-slate-400 text-sm font-medium">📖 VintApp API Documentation — <span class="text-white">v1.0.0</span></p>
                    <p class="text-slate-600 text-xs">172 endpoints · Laravel Sanctum · Mobile Money RDC</p>
                </div>
                <div class="flex items-center justify-center gap-4 text-xs text-slate-500">
                    <a href="{{ url('/') }}" class="hover:text-violet-300 transition-colors">Accueil</a>
                    <span class="text-slate-700">•</span>
                    <a href="mailto:{{ config('mail.from.address') }}" class="hover:text-violet-300 transition-colors">Support</a>
                    <span class="text-slate-700">•</span>
                    <span>© {{ date('Y') }} VintApp</span>
                </div>
            </footer>

        </div>
    </main>

    {{-- Back to top --}}
    <button id="back-to-top" class="fixed bottom-6 right-6 z-40 w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-500 text-white shadow-lg shadow-violet-500/30 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 hover:scale-105" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Retour en haut">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
    </button>

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

        // Copy to clipboard
        function copyCode(btn) {
            const pre = btn.parentElement;
            const code = pre.querySelector('code').innerText;
            navigator.clipboard.writeText(code).then(() => {
                const original = btn.textContent;
                btn.textContent = '✓ Copié !';
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.textContent = original;
                    btn.classList.remove('copied');
                }, 2000);
            });
        }

        // Back to top visibility
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 600) {
                backToTop.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                backToTop.classList.add('opacity-0', 'pointer-events-none');
            }
        }, { passive: true });
    </script>
</body>
</html>
