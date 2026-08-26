@extends('app')

@section('title', 'Politique de confidentialité')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">

    <!-- Hero -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.04%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-50"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white/70 text-sm font-medium mb-8 border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Politique de confidentialité
                </div>
                <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4 tracking-tight">
                    Protection de vos<br class="hidden sm:block"> données personnelles
                </h1>
                <p class="text-base sm:text-lg text-slate-400 max-w-xl mx-auto">
                    Dernière mise à jour : {{ date('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Sommaire -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
        <nav class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-4 sm:p-6 mb-10">
            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Sommaire</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                @php
                    $sections = [
                        ['num' => '1', 'label' => 'Données collectées', 'color' => 'blue'],
                        ['num' => '2', 'label' => 'Utilisation', 'color' => 'indigo'],
                        ['num' => '3', 'label' => 'Partage', 'color' => 'purple'],
                        ['num' => '4', 'label' => 'Protection', 'color' => 'emerald'],
                        ['num' => '5', 'label' => 'Vos droits', 'color' => 'amber'],
                        ['num' => '6', 'label' => 'Cookies', 'color' => 'teal'],
                        ['num' => '7', 'label' => 'Conservation', 'color' => 'rose'],
                        ['num' => '8', 'label' => 'Modifications', 'color' => 'yellow'],
                        ['num' => '9', 'label' => 'Conformité RDC', 'color' => 'indigo'],
                        ['num' => '10', 'label' => 'Contact', 'color' => 'slate'],
                    ];
                @endphp
                @foreach($sections as $s)
                    <a href="#section-{{ $s['num'] }}" class="group flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <span class="flex-shrink-0 w-6 h-6 rounded-md bg-{{ $s['color'] }}-100 dark:bg-{{ $s['color'] }}-900/30 text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400 text-xs font-bold flex items-center justify-center">{{ $s['num'] }}</span>
                        <span class="text-sm text-gray-600 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ $s['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

        <!-- Engagement -->
        <div id="section-0" class="mb-12">
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 rounded-2xl p-8 sm:p-10 border border-blue-100 dark:border-blue-900/30">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200/20 dark:bg-blue-800/10 rounded-full -translate-y-16 translate-x-16 blur-2xl"></div>
                <div class="relative">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Notre engagement</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        Chez VintApp, nous prenons très au sérieux la protection de vos données personnelles. Cette politique explique comment nous collectons, utilisons et protégeons vos informations.
                    </p>
                </div>
            </div>
        </div>

        {{-- Section components --}}
        @php
        $sectionColor = fn($color) => match($color) {
            'blue' => ['bg' => 'bg-blue-50 dark:bg-blue-950/30', 'border' => 'border-blue-100 dark:border-blue-900/30', 'accent' => 'blue'],
            'indigo' => ['bg' => 'bg-indigo-50 dark:bg-indigo-950/30', 'border' => 'border-indigo-100 dark:border-indigo-900/30', 'accent' => 'indigo'],
            'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-950/30', 'border' => 'border-purple-100 dark:border-purple-900/30', 'accent' => 'purple'],
            'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/30', 'border' => 'border-emerald-100 dark:border-emerald-900/30', 'accent' => 'emerald'],
            'amber' => ['bg' => 'bg-amber-50 dark:bg-amber-950/30', 'border' => 'border-amber-100 dark:border-amber-900/30', 'accent' => 'amber'],
            'teal' => ['bg' => 'bg-teal-50 dark:bg-teal-950/30', 'border' => 'border-teal-100 dark:border-teal-900/30', 'accent' => 'teal'],
            'rose' => ['bg' => 'bg-rose-50 dark:bg-rose-950/30', 'border' => 'border-rose-100 dark:border-rose-900/30', 'accent' => 'rose'],
            'yellow' => ['bg' => 'bg-yellow-50 dark:bg-yellow-950/30', 'border' => 'border-yellow-100 dark:border-yellow-900/30', 'accent' => 'yellow'],
            'slate' => ['bg' => 'bg-slate-50 dark:bg-slate-800', 'border' => 'border-slate-200 dark:border-slate-700', 'accent' => 'slate'],
        };
        @endphp

        <!-- Section 1: Données collectées -->
        <section id="section-1" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg text-sm font-bold flex items-center justify-center">1</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Données collectées</h2>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mb-6 ml-11">Nous collectons différents types de données pour vous fournir et améliorer nos services :</p>

            <div class="ml-11 space-y-3">
                @php
                $dataCards = [
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>', 'title' => 'Informations personnelles', 'items' => ['Nom, prénom, pseudonyme', 'Adresse email et numéro de téléphone', 'Adresse de livraison et de facturation', 'Date de naissance (si fournie)'], 'color' => 'blue'],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>', 'title' => 'Données de transaction', 'items' => ['Historique des achats et ventes', 'Informations de paiement (cryptées)', 'Historique des transactions de portefeuille'], 'color' => 'purple'],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>', 'title' => "Données d'utilisation", 'items' => ['Adresse IP et données de connexion', 'Type de navigateur et système d\'exploitation', 'Pages visitées et durée de navigation', 'Cookies et technologies similaires'], 'color' => 'emerald'],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>', 'title' => "Données de l'appareil mobile", 'items' => ["Type d'appareil (mobile, tablette, ordinateur)", "Système d'exploitation (Android, iOS) et version", 'Navigateur utilisé (Chrome, Safari, etc.)', 'Token de notification push (FCM / Expo)'], 'color' => 'amber'],
                ];
                @endphp

                @foreach($dataCards as $card)
                <div class="group relative overflow-hidden rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80 p-5 hover:shadow-md transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-900/30 rounded-xl flex items-center justify-center border border-{{ $card['color'] }}-100 dark:border-{{ $card['color'] }}-800/30">
                            <svg class="w-5 h-5 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $card['icon'] !!}</svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $card['title'] }}</h3>
                            <ul class="space-y-1.5">
                                @foreach($card['items'] as $item)
                                <li class="flex items-start text-sm text-gray-500 dark:text-gray-400">
                                    <span class="text-{{ $card['color'] }}-400 mr-2 mt-1.5 w-1 h-1 rounded-full bg-current flex-shrink-0"></span>
                                    {{ $item }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Notifications & Géolocalisation -->
            <div class="ml-11 mt-6">
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80 overflow-hidden">
                    <div class="p-5 border-b border-gray-50 dark:border-gray-700/50">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 bg-cyan-500 rounded-full"></span>
                            Notifications push
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">VintApp utilise <strong>Firebase Cloud Messaging (FCM)</strong> et <strong>Expo Push</strong> pour vous envoyer des alertes concernant les messages, le statut de vos commandes, l'approbation d'articles et les offres personnalisées.</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Vous pouvez désactiver les notifications à tout moment depuis les paramètres de votre appareil. Le token est supprimé lors de la déconnexion.</p>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 bg-cyan-500 rounded-full"></span>
                            Géolocalisation
                        </h3>
                        <ul class="space-y-1.5 mt-2">
                            <li class="flex items-start text-sm text-gray-500 dark:text-gray-400">
                                <span class="text-cyan-400 mr-2 mt-1.5 w-1 h-1 rounded-full bg-current flex-shrink-0"></span>
                                <span><strong>Géolocalisation par IP</strong> — détermine votre ville de connexion (ip-api.com), collectée automatiquement à chaque session.</span>
                            </li>
                            <li class="flex items-start text-sm text-gray-500 dark:text-gray-400">
                                <span class="text-cyan-400 mr-2 mt-1.5 w-1 h-1 rounded-full bg-current flex-shrink-0"></span>
                                <span><strong>Coordonnées GPS</strong> — utilisées pour la livraison locale entre utilisateurs proches (avec votre consentement).</span>
                            </li>
                            <li class="flex items-start text-sm text-gray-500 dark:text-gray-400">
                                <span class="text-cyan-400 mr-2 mt-1.5 w-1 h-1 rounded-full bg-current flex-shrink-0"></span>
                                <span><strong>Adresses de livraison</strong> — coordonnées GPS stockées pour faciliter la livraison locale.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Interactions sociales -->
            <div class="ml-11 mt-3">
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80 overflow-hidden">
                    <div class="grid sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-gray-50 dark:divide-gray-700/50">
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 bg-pink-500 rounded-full"></span>
                                Visible par les autres
                            </h3>
                            <ul class="space-y-1.5">
                                @foreach(['Nom, pseudonyme et avatar', 'Articles publiés (photos, descriptions, prix)', 'Avis et évaluations reçus', 'Ville de publication'] as $item)
                                <li class="flex items-start text-sm text-gray-500 dark:text-gray-400">
                                    <span class="text-pink-400 mr-2 mt-1.5 w-1 h-1 rounded-full bg-current flex-shrink-0"></span>
                                    {{ $item }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                Privé (non partagé)
                            </h3>
                            <ul class="space-y-1.5">
                                @foreach(['Messages privés entre utilisateurs', 'Articles mis en favoris', 'Historique des achats et ventes', 'Adresses de livraison', 'Solde du portefeuille'] as $item)
                                <li class="flex items-start text-sm text-gray-500 dark:text-gray-400">
                                    <span class="text-gray-400 mr-2 mt-1.5 w-1 h-1 rounded-full bg-current flex-shrink-0"></span>
                                    {{ $item }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 2: Utilisation -->
        <section id="section-2" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-lg text-sm font-bold flex items-center justify-center">2</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Utilisation de vos données</h2>
            </div>
            <div class="ml-11 grid sm:grid-cols-2 gap-3">
                @php
                $usages = [
                    ['title' => 'Fourniture des services', 'desc' => 'Traiter vos commandes, gérer votre compte et fournir un support client', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'],
                    ['title' => 'Amélioration continue', 'desc' => "Analyser l'utilisation pour améliorer nos services et votre expérience", 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>'],
                    ['title' => 'Communication', 'desc' => 'Vous envoyer des notifications importantes et des offres personnalisées', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>'],
                    ['title' => 'Sécurité', 'desc' => 'Prévenir la fraude, détecter les abus et assurer la sécurité de la plateforme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>'],
                ];
                @endphp
                @foreach($usages as $u)
                <div class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80">
                    <div class="w-8 h-8 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $u['icon'] !!}</svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $u['title'] }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $u['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Section 3: Partage -->
        <section id="section-3" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 rounded-lg text-sm font-bold flex items-center justify-center">3</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Partage de vos données</h2>
            </div>
            <div class="ml-11">
                <div class="bg-purple-50 dark:bg-purple-950/30 border border-purple-100 dark:border-purple-900/30 rounded-xl p-4 mb-4">
                    <p class="text-sm text-purple-800 dark:text-purple-200 font-medium">Nous ne vendons jamais vos données personnelles.</p>
                </div>
                <div class="space-y-2">
                    @php
                    $shares = [
                        ['title' => 'Prestataires de services', 'desc' => 'Partenaires de confiance pour le paiement, la livraison et l\'hébergement'],
                        ['title' => 'Obligations légales', 'desc' => 'Lorsque la loi l\'exige ou pour protéger nos droits'],
                        ['title' => 'Avec votre consentement', 'desc' => 'Uniquement avec votre autorisation explicite'],
                    ];
                    @endphp
                    @foreach($shares as $i => $s)
                    <div class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80">
                        <span class="w-6 h-6 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-md text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i + 1 }}</span>
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $s['title'] }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $s['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Section 4: Protection -->
        <section id="section-4" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-lg text-sm font-bold flex items-center justify-center">4</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Protection de vos données</h2>
            </div>
            <div class="ml-11 grid grid-cols-2 sm:grid-cols-3 gap-3">
                @php
                $securities = [
                    ['title' => 'SSL/TLS', 'desc' => 'Connexions sécurisées', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>'],
                    ['title' => 'Pare-feu', 'desc' => 'Anti-acès non autorisés', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>'],
                    ['title' => 'Cryptage', 'desc' => 'Mots de passe bcrypt', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>'],
                    ['title' => 'Backups', 'desc' => 'Sauvegardes quotidiennes', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>'],
                    ['title' => 'Monitoring', 'desc' => 'Surveillance 24/7', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>'],
                    ['title' => 'Audits', 'desc' => 'Tests de pénétration', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path>'],
                ];
                @endphp
                @foreach($securities as $s)
                <div class="text-center p-4 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80">
                    <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mx-auto mb-2 border border-emerald-100 dark:border-emerald-800/30">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $s['icon'] !!}</svg>
                    </div>
                    <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $s['title'] }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $s['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Section 5: Droits -->
        <section id="section-5" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-lg text-sm font-bold flex items-center justify-center">5</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Vos droits (RGPD)</h2>
            </div>
            <div class="ml-11 grid sm:grid-cols-2 gap-3">
                @php
                $rights = [
                    ['title' => 'Accès', 'desc' => 'Obtenir une copie de vos données', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>'],
                    ['title' => 'Rectification', 'desc' => 'Corriger les données inexactes', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>'],
                    ['title' => 'Effacement', 'desc' => 'Supprimer vos données personnelles', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>'],
                    ['title' => 'Opposition', 'desc' => "Vous opposer au traitement", 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>'],
                    ['title' => 'Portabilité', 'desc' => 'Récupérer vos données structurées', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>'],
                    ['title' => 'Limitation', 'desc' => 'Limiter le traitement de vos données', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>'],
                ];
                @endphp
                @foreach($rights as $r)
                <div class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80">
                    <div class="w-8 h-8 bg-amber-50 dark:bg-amber-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $r['icon'] !!}</svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $r['title'] }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $r['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="ml-11 mt-4 bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/30 rounded-xl p-4">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <strong>Pour exercer vos droits :</strong> Envoyez un email à
                    <a href="mailto:privacy@vintapp.com" class="underline font-semibold hover:text-blue-600">privacy@vintapp.com</a>
                    avec une copie de votre pièce d'identité. Réponse sous 30 jours.
                </p>
            </div>
        </section>

        <!-- Section 6: Cookies -->
        <section id="section-6" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 rounded-lg text-sm font-bold flex items-center justify-center">6</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cookies et technologies similaires</h2>
            </div>
            <div class="ml-11 space-y-3">
                @php
                $cookies = [
                    ['title' => 'Cookies essentiels', 'desc' => 'Nécessaires au fonctionnement (authentification, panier, langue)', 'color' => 'emerald'],
                    ['title' => 'Cookies analytiques', 'desc' => "Analyse d'utilisation pour améliorer nos services", 'color' => 'teal'],
                    ['title' => 'Cookies marketing', 'desc' => 'Publicités et offres personnalisées (avec consentement)', 'color' => 'cyan'],
                ];
                @endphp
                @foreach($cookies as $c)
                <div class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80">
                    <span class="w-2 h-2 bg-{{ $c['color'] }}-500 rounded-full mt-2 flex-shrink-0"></span>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $c['title'] }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $c['desc'] }}</p>
                    </div>
                </div>
                @endforeach
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-800/50 p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Cookies utilisés par VintApp :</p>
                    <div class="grid sm:grid-cols-2 gap-2 text-xs text-gray-400 dark:text-gray-500">
                        <div><code class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-300">laravel_session</code> — session (fermeture navigateur)</div>
                        <div><code class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-300">csrf_token</code> — sécurité CSRF (fermeture navigateur)</div>
                        <div><code class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-300">XSRF-TOKEN</code> — requêtes AJAX (24h)</div>
                        <div><code class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-300">remember_web_*</code> — rester connecté (5 ans)</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 7: Conservation -->
        <section id="section-7" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 rounded-lg text-sm font-bold flex items-center justify-center">7</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Conservation de vos données</h2>
            </div>
            <div class="ml-11">
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                <th class="text-left px-4 py-3 font-semibold text-gray-900 dark:text-white">Type de donnée</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-900 dark:text-white">Durée</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @php
                            $retention = [
                                ['type' => 'Compte utilisateur', 'duration' => 'Actif'],
                                ['type' => 'Sessions mobile & web', 'duration' => '30 jours'],
                                ['type' => 'Tokens FCM', 'duration' => 'Déconnexion'],
                                ['type' => 'Historique de navigation', 'duration' => '30 jours'],
                                ['type' => 'Favoris & messages', 'duration' => 'Durée du compte'],
                                ['type' => 'Données de commande', 'duration' => '10 ans'],
                            ];
                            @endphp
                            @foreach($retention as $r)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $r['type'] }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $r['duration'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Section 8: Modifications -->
        <section id="section-8" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/40 text-yellow-600 dark:text-yellow-400 rounded-lg text-sm font-bold flex items-center justify-center">8</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Modifications de la politique</h2>
            </div>
            <div class="ml-11">
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">Nous pouvons modifier cette politique à tout moment. En cas de changements significatifs, nous vous en informerons par :</p>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Email
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        Notification push
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Date en haut de page
                    </span>
                </div>
            </div>
        </section>

        <!-- Section 9: Conformité RDC -->
        <section id="section-9" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-lg text-sm font-bold flex items-center justify-center">9</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Conformité légale — RDC</h2>
            </div>
            <div class="ml-11 space-y-3">
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80 overflow-hidden divide-y divide-gray-50 dark:divide-gray-700/50">
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-2">Cadre juridique applicable</h3>
                        <ul class="space-y-1.5 text-sm text-gray-500 dark:text-gray-400">
                            <li class="flex items-start gap-2"><span class="text-indigo-400 mt-1.5 w-1 h-1 rounded-full bg-current flex-shrink-0"></span><span><strong>Constitution de la RDC (2006)</strong> — Art. 19 : droit à la vie privée</span></li>
                            <li class="flex items-start gap-2"><span class="text-indigo-400 mt-1.5 w-1 h-1 rounded-full bg-current flex-shrink-0"></span><span><strong>Loi n° 20/017</strong> — Régime juridique des télécommunications</span></li>
                            <li class="flex items-start gap-2"><span class="text-indigo-400 mt-1.5 w-1 h-1 rounded-full bg-current flex-shrink-0"></span><span><strong>Ordonnance-loi n° 23/010</strong> — Protection des données à caractère personnel</span></li>
                            <li class="flex items-start gap-2"><span class="text-indigo-400 mt-1.5 w-1 h-1 rounded-full bg-current flex-shrink-0"></span><span><strong>RGPD (UE)</strong> — Pour les utilisateurs dans l'EEE</span></li>
                        </ul>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-2">Transferts internationaux</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Vos données peuvent être stockées hors de la RDC. Des garanties appropriées (clauses contractuelles conformes au RGPD et à la législation congolaise) sont en place.</p>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-2">Autorité de contrôle</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En cas de litige, contactez notre DPO à <a href="mailto:privacy@vintapp.com" class="underline font-medium text-indigo-600 dark:text-indigo-400">privacy@vintapp.com</a>. Vous pouvez également introduire une réclamation auprès de l'autorité compétente en RDC.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 10: Contact -->
        <section id="section-10" class="mb-10">
            <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-8 sm:p-10 text-center">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.04%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-50"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-5 border border-white/10">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-white mb-2">Délégué à la protection des données</h2>
                    <p class="text-sm text-slate-400 mb-6 max-w-md mx-auto">Pour toute question concernant vos données personnelles, contactez notre DPO.</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="mailto:privacy@vintapp.com" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-slate-900 rounded-xl text-sm font-medium hover:bg-slate-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            privacy@vintapp.com
                        </a>
                        <a href="{{ route('help.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white/10 text-white rounded-xl text-sm font-medium hover:bg-white/20 transition-colors border border-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Centre d'aide
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Navigation -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-100 dark:border-gray-800">
            <a href="{{ route('terms') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Conditions d'utilisation
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-sm font-medium hover:bg-slate-800 dark:hover:bg-gray-100 transition-colors">
                Retour à l'accueil
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>
</div>
@endsection
