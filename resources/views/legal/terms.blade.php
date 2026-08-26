@extends('app')

@section('title', 'Conditions d\'utilisation')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">

    <!-- Hero -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.04%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-50"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white/70 text-sm font-medium mb-8 border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Conditions d'utilisation
                </div>
                <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4 tracking-tight">
                    Termes et conditions<br class="hidden sm:block"> d'usage
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
                    ['num' => '1', 'label' => 'Acceptation', 'color' => 'emerald'],
                    ['num' => '2', 'label' => 'Plateforme', 'color' => 'blue'],
                    ['num' => '3', 'label' => 'Transactions', 'color' => 'purple'],
                    ['num' => '4', 'label' => 'Propriété intel.', 'color' => 'indigo'],
                    ['num' => '5', 'label' => 'Responsabilité', 'color' => 'rose'],
                    ['num' => '6', 'label' => 'Résiliation', 'color' => 'orange'],
                    ['num' => '7', 'label' => 'Modifications', 'color' => 'teal'],
                    ['num' => '8', 'label' => 'Contact', 'color' => 'slate'],
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
        <div class="mb-12">
            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-950/30 dark:to-green-950/30 rounded-2xl p-8 sm:p-10 border border-emerald-100 dark:border-emerald-900/30">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-200/20 dark:bg-emerald-800/10 rounded-full -translate-y-16 translate-x-16 blur-2xl"></div>
                <div class="relative">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-600/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Conditions d'utilisation de VintApp</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        En utilisant VintApp, vous acceptez d'être lié par ces conditions. Veuillez les lire attentivement avant d'utiliser nos services.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 1: Acceptation -->
        <section id="section-1" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-lg text-sm font-bold flex items-center justify-center">1</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Acceptation des conditions</h2>
            </div>
            <div class="ml-11 space-y-3">
                <p class="text-gray-600 dark:text-gray-300 text-sm">En accédant et en utilisant VintApp, vous acceptez d'être lié par ces conditions d'utilisation et toutes les lois et réglementations applicables.</p>
                @php
                $accept = [
                    ['label' => "Vous devez avoir au moins 18 ans pour utiliser nos services", 'color' => 'emerald'],
                    ['label' => "Vous vous engagez à fournir des informations exactes et à jour", 'color' => 'emerald'],
                    ['label' => "Vous êtes responsable de la confidentialité de votre compte", 'color' => 'emerald'],
                ];
                @endphp
                @foreach($accept as $a)
                <div class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80">
                    <span class="w-2 h-2 bg-{{ $a['color'] }}-500 rounded-full mt-2 flex-shrink-0"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $a['label'] }}</span>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Section 2: Plateforme -->
        <section id="section-2" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg text-sm font-bold flex items-center justify-center">2</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Utilisation de la plateforme</h2>
            </div>
            <div class="ml-11 space-y-3">
                <!-- Droits -->
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80 overflow-hidden">
                    <div class="p-5 border-b border-gray-50 dark:border-gray-700/50">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            Droits d'utilisation
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">VintApp vous accorde une licence limitée, non exclusive, non transférable et révocable pour utiliser notre plateforme à des fins personnelles et non commerciales.</p>
                    </div>
                </div>
                <!-- Interdits -->
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80 overflow-hidden">
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            Comportements interdits
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Vous vous engagez à ne pas :</p>
                        <div class="space-y-2">
                            @php
                            $forbidden = [
                                ['title' => 'Usage illégal', 'desc' => 'Utiliser la plateforme à des fins illégales ou frauduleuses'],
                                ['title' => 'Contrefaçon', 'desc' => 'Vendre des produits contrefaits ou de contrebande'],
                                ['title' => 'Perturbation', 'desc' => 'Perturber le fonctionnement de la plateforme'],
                                ['title' => 'Collecte', 'desc' => "Collecter des données personnelles d'autres utilisateurs"],
                            ];
                            @endphp
                            @foreach($forbidden as $f)
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-red-50/50 dark:bg-red-950/20 border border-red-100/50 dark:border-red-900/20">
                                <span class="w-5 h-5 bg-red-100 dark:bg-red-900/30 rounded-md flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </span>
                                <div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $f['title'] }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400"> — {{ $f['desc'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 3: Transactions -->
        <section id="section-3" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 rounded-lg text-sm font-bold flex items-center justify-center">3</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Transactions et paiements</h2>
            </div>
            <div class="ml-11 space-y-3">
                <div class="grid sm:grid-cols-2 gap-3">
                    <div class="p-5 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80">
                        <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-3 border border-purple-100 dark:border-purple-800/30">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">Paiements sécurisés</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tous les paiements sont traités de manière sécurisée via nos partenaires certifiés (CinetPay, Stripe).</p>
                    </div>
                    <div class="p-5 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80">
                        <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-3 border border-purple-100 dark:border-purple-800/30">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">Frais de transaction</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Des frais peuvent s'appliquer selon le mode de paiement choisi. Consultez notre grille tarifaire.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 rounded-xl border border-amber-100 dark:border-amber-900/30 bg-amber-50/50 dark:bg-amber-950/20">
                    <span class="w-5 h-5 bg-amber-100 dark:bg-amber-900/30 rounded-md flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </span>
                    <p class="text-sm text-amber-800 dark:text-amber-200"><strong>Important :</strong> Les vendeurs sont responsables de la livraison des produits vendus. VintApp agit uniquement comme intermédiaire.</p>
                </div>
            </div>
        </section>

        <!-- Section 4: Propriété intellectuelle -->
        <section id="section-4" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-lg text-sm font-bold flex items-center justify-center">4</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Propriété intellectuelle</h2>
            </div>
            <div class="ml-11 space-y-3">
                <p class="text-sm text-gray-600 dark:text-gray-300">Tout le contenu présent sur VintApp (textes, graphiques, logos, images, clips audio et vidéo) est la propriété de VintApp ou de ses concédants et est protégé par les lois sur le droit d'auteur.</p>
                <div class="grid grid-cols-3 gap-3">
                    @php
                    $ipItems = [
                        ['title' => 'Marques', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>'],
                        ['title' => 'Images', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'],
                        ['title' => 'Code', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>'],
                    ];
                    @endphp
                    @foreach($ipItems as $i)
                    <div class="text-center p-4 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80">
                        <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mx-auto mb-2 border border-indigo-100 dark:border-indigo-800/30">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $i['icon'] !!}</svg>
                        </div>
                        <p class="text-xs font-medium text-gray-900 dark:text-white">{{ $i['title'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Section 5: Responsabilité -->
        <section id="section-5" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 rounded-lg text-sm font-bold flex items-center justify-center">5</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Limitation de responsabilité</h2>
            </div>
            <div class="ml-11 space-y-2">
                @php
                $liability = [
                    ['title' => 'Dommages indirects', 'desc' => "Tout dommage indirect, accessoire ou consécutif résultant de l'utilisation de nos services"],
                    ['title' => 'Litiges entre utilisateurs', 'desc' => 'Tout litige entre acheteurs et vendeurs concernant les transactions'],
                    ['title' => 'Qualité des produits', 'desc' => "La qualité, l'authenticité ou la conformité des produits vendus sur la plateforme"],
                    ['title' => 'Disponibilité', 'desc' => "Toute interruption ou dysfonctionnement du service"],
                ];
                @endphp
                @foreach($liability as $i => $l)
                <div class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-700/50 bg-white dark:bg-gray-800/80">
                    <span class="w-6 h-6 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-md text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i + 1 }}</span>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $l['title'] }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $l['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Section 6: Résiliation -->
        <section id="section-6" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400 rounded-lg text-sm font-bold flex items-center justify-center">6</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Résiliation</h2>
            </div>
            <div class="ml-11">
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Nous nous réservons le droit de suspendre ou de résilier votre compte à tout moment, sans préavis, si nous estimons que vous avez violé ces conditions d'utilisation.</p>
                <div class="rounded-xl border border-orange-100 dark:border-orange-900/30 bg-orange-50/50 dark:bg-orange-950/20 p-5">
                    <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-3 text-sm">
                        <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                        Motifs de résiliation
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Violation des conditions', 'Activité frauduleuse', 'Comportement abusif', 'Non-paiement des frais'] as $reason)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 border border-orange-100 dark:border-orange-900/30 text-xs font-medium text-gray-700 dark:text-gray-300">
                            <span class="w-1.5 h-1.5 bg-orange-400 rounded-full"></span>
                            {{ $reason }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 7: Modifications -->
        <section id="section-7" class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 rounded-lg text-sm font-bold flex items-center justify-center">7</span>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Modifications des conditions</h2>
            </div>
            <div class="ml-11">
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Nous nous réservons le droit de modifier ces conditions à tout moment. Les modifications entreront en vigueur dès leur publication sur la plateforme.</p>
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

        <!-- Section 8: Contact -->
        <section id="section-8" class="mb-10">
            <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-8 sm:p-10 text-center">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.04%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-50"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-5 border border-white/10">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-white mb-2">Des questions ?</h2>
                    <p class="text-sm text-slate-400 mb-6 max-w-md mx-auto">Pour toute question concernant ces conditions d'utilisation, n'hésitez pas à nous contacter.</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="mailto:support@vintapp.imasomo.com" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-slate-900 rounded-xl text-sm font-medium hover:bg-slate-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            support@vintapp.imasomo.com
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
            <a href="{{ route('privacy') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Politique de confidentialité
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-sm font-medium hover:bg-slate-800 dark:hover:bg-gray-100 transition-colors">
                Retour à l'accueil
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>
</div>
@endsection
