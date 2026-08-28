@extends('app')

@section('title', "Centre d'aide - VintApp")
@section('meta_description', "Centre d'aide VintApp - FAQ, guides d'utilisation et support pour acheter et vendre en toute securite")
@section('meta_keywords', 'aide, FAQ, support, guide, tutoriel, VintApp, assistance')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-gray-900">

    {{-- Header --}}
    <header class="bg-primary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="text-center max-w-2xl mx-auto">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-primary-300 text-xs font-medium border border-white/15 mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Centre d'aide
                </span>
                <h1 class="text-3xl lg:text-5xl font-bold text-white tracking-tight mb-4">
                    Comment pouvons-nous<br><span class="text-primary-200">vous aider ?</span>
                </h1>
                <p class="text-primary-200 text-lg mb-6">Trouvez rapidement les reponses a vos questions parmi nos guides et notre FAQ</p>

                {{-- Search --}}
                <div class="max-w-xl mx-auto relative">
                    <div class="flex">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="searchInput"
                            @focus="open = true"
                            placeholder="Rechercher dans l'aide..."
                            autocomplete="off"
                            class="w-full pl-11 pr-4 py-3 rounded-l-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm border border-gray-200 dark:border-gray-700 border-r-0 focus:outline-none focus:ring-4 focus:ring-white/20 focus:border-primary shadow-md"
                        >
                        <button id="searchBtn" class="px-5 bg-primary-600 hover:bg-primary-500 rounded-r-lg flex items-center justify-center transition-colors border border-primary-600 hover:border-primary-500 shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="searchResults" class="hidden absolute w-full mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden z-50 max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-700" style="display:none;"></div>
                </div>
            </div>
        </div>
    </header>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">

            {{-- Sidebar --}}
            <aside class="lg:col-span-1">
                <div class="sticky top-6 space-y-6">
                    <nav class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-700">
                            <h5 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                Categories
                            </h5>
                        </div>
                        <div class="p-2.5 space-y-1">
                            <a href="#getting-started" data-category="getting-started" class="category-link flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium text-primary-700 bg-primary-400 border border-primary-300 transition-all">
                                <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                                Premiers pas
                            </a>
                            <a href="#buying" data-category="buying" class="category-link flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white border border-transparent transition-all">
                                <span class="w-2 h-2 rounded-full bg-primary-400"></span>
                                Acheter
                            </a>
                            <a href="#selling" data-category="selling" class="category-link flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white border border-transparent transition-all">
                                <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                                Vendeur
                            </a>
                            <a href="#payments" data-category="payments" class="category-link flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white border border-transparent transition-all">
                                <span class="w-2 h-2 rounded-full bg-primary-300"></span>
                                Paiements
                            </a>
                            <a href="#account" data-category="account" class="category-link flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white border border-transparent transition-all">
                                <span class="w-2 h-2 rounded-full bg-primary-400"></span>
                                Compte
                            </a>
                            <a href="#affiliate" data-category="affiliate" class="category-link flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white border border-transparent transition-all">
                                <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                                Affiliation
                            </a>
                            <a href="#safety" data-category="safety" class="category-link flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white border border-transparent transition-all">
                                <span class="w-2 h-2 rounded-full bg-primary-400"></span>
                                Securite
                            </a>
                            <a href="#contact" data-category="contact" class="category-link flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white border border-transparent transition-all">
                                <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                                Contact
                            </a>
                        </div>
                    </nav>

                    {{-- Contact card --}}
                    <div class="bg-primary rounded-xl p-6 text-white text-center shadow-sm">
                        <div class="w-11 h-11 bg-white/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h6 class="font-semibold mb-1">Besoin d'aide ?</h6>
                        <p class="text-sm text-primary-200 mb-5">Notre equipe vous repond sous 24h</p>
                        <a href="#contact" data-category="contact" class="category-link inline-flex items-center gap-2 px-5 py-2.5 bg-white text-primary-700 rounded-lg text-sm font-semibold hover:bg-primary-100 transition-colors">
                            Nous contacter
                        </a>
                    </div>
                </div>
            </aside>

            {{-- Main content --}}
            <main class="lg:col-span-1 space-y-12">

                {{-- Section: Premiers pas --}}
                <section id="getting-started" class="help-section">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Premiers pas</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Decouvrez comment utiliser VintApp</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-10">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 hover:border-primary-300 transition-colors">
                            <div class="w-11 h-11 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Creer un compte</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Inscrivez-vous gratuitement et configurez votre profil pour commencer a acheter et vendre.</p>
                            <a href="#guide-signup" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700">Voir le guide
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 hover:border-primary-300 transition-colors">
                            <div class="w-11 h-11 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Premiere annonce</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Apprenez a creer votre premiere annonce avec de belles photos et une description attractive.</p>
                            <a href="#guide-first-listing" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700">Voir le guide
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">Guides pas a pas</h3>

                    {{-- Guide: Creer un compte --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6" id="guide-signup">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">Creer un compte VintApp</h5>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <div class="lg:col-span-2 space-y-4">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 w-9 h-9 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center font-bold text-sm">1</div>
                                        <div class="flex-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border-l-2 border-primary-500">
                                            <h6 class="font-bold text-gray-900 dark:text-white mb-1 text-sm">Accedez a la page d'inscription</h6>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Cliquez sur "S'inscrire" en haut a droite de la page d'accueil</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 w-9 h-9 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center font-bold text-sm">2</div>
                                        <div class="flex-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border-l-2 border-primary-500">
                                            <h6 class="font-bold text-gray-900 dark:text-white mb-1 text-sm">Choisissez votre methode</h6>
                                            <ul class="text-sm text-gray-600 dark:text-gray-300 list-disc list-inside space-y-0.5">
                                                <li>Inscription par email (recommande)</li>
                                                <li>Connexion avec Google, Facebook ou Apple</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 w-9 h-9 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center font-bold text-sm">3</div>
                                        <div class="flex-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border-l-2 border-primary-500">
                                            <h6 class="font-bold text-gray-900 dark:text-white mb-1 text-sm">Remplissez vos informations</h6>
                                            <ul class="text-sm text-gray-600 dark:text-gray-300 list-disc list-inside space-y-0.5">
                                                <li>Nom, prenom, email valide</li>
                                                <li>Mot de passe securise (min. 8 caracteres)</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 w-9 h-9 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center font-bold text-sm">4</div>
                                        <div class="flex-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border-l-2 border-primary-500">
                                            <h6 class="font-bold text-gray-900 dark:text-white mb-1 text-sm">Verifiez votre email</h6>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Un email de confirmation vous sera envoye. Cliquez sur le lien pour activer votre compte.</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 w-9 h-9 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center font-bold text-sm">5</div>
                                        <div class="flex-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border-l-2 border-primary-500">
                                            <h6 class="font-bold text-gray-900 dark:text-white mb-1 text-sm">Completez votre profil</h6>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Ajoutez une photo de profil et completez vos informations.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-5 h-full">
                                        <h6 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                            </svg>
                                            Conseils
                                        </h6>
                                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                            <li class="flex items-start gap-2">
                                                <span class="w-5 h-5 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">&#10003;</span>
                                                <span>Utilisez un mot de passe unique</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <span class="w-5 h-5 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">&#10003;</span>
                                                <span>Verifiez votre email immediatement</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <span class="w-5 h-5 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">&#10003;</span>
                                                <span>Completez votre profil a 100%</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <span class="w-5 h-5 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">&#10003;</span>
                                                <span>Activez l'authentification 2FA</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">Questions frequentes</h3>
                    <div class="space-y-3" x-data="{ openFaq: 'faq1' }">
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                            <button @click="openFaq = openFaq === 'faq1' ? '' : 'faq1'" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <span class="font-medium text-gray-900 dark:text-white text-sm">Comment creer un compte sur VintApp ?</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="openFaq === 'faq1' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openFaq === 'faq1'" x-collapse>
                                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Vous pouvez creer un compte de plusieurs facons :</p>
                                    <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-300 space-y-1 mb-4">
                                        <li>Inscription par email avec verification</li>
                                        <li>Connexion avec Google, Facebook ou Apple (instantanee)</li>
                                    </ul>
                                    <div class="bg-primary-400 border-l-2 border-primary-500 rounded p-3 text-sm text-gray-800">
                                        <strong>Astuce :</strong> Les connexions sociales sont plus rapides et securisees !
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                            <button @click="openFaq = openFaq === 'faq2' ? '' : 'faq2'" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <span class="font-medium text-gray-900 dark:text-white text-sm">VintApp est-il gratuit ?</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="openFaq === 'faq2' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openFaq === 'faq2'" x-collapse>
                                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3"><strong class="text-primary-600 dark:text-primary-300">Oui, VintApp est entierement gratuit !</strong></p>
                                    <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-300 space-y-1 mb-3">
                                        <li>Inscription gratuite sans frais caches</li>
                                        <li>Publication d'annonces illimitee</li>
                                        <li>Messagerie et notifications gratuites</li>
                                    </ul>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Nous prenons uniquement une petite commission sur les ventes reussies.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Section: Acheter --}}
                <section id="buying" class="help-section hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Acheter</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tout ce que vous devez savoir pour acheter en toute securite</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 text-center hover:border-primary-300 transition-colors">
                            <div class="w-11 h-11 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Rechercher</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Utilisez les filtres pour trouver exactement ce que vous cherchez</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 text-center hover:border-primary-300 transition-colors">
                            <div class="w-11 h-11 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Discuter</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Posez vos questions au vendeur via la messagerie integree</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 text-center hover:border-primary-300 transition-colors">
                            <div class="w-11 h-11 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Payer</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Payez en securite avec nos methodes de paiement protegees</p>
                        </div>
                    </div>

                    <div class="bg-primary-400 border-l-2 border-primary-500 rounded-lg p-6">
                        <h6 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Signaux d'alarme
                        </h6>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <span class="w-5 h-5 rounded-full bg-white text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold border border-primary-300">!</span>
                                <span>Prix trop bas par rapport au marche</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-5 h-5 rounded-full bg-white text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold border border-primary-300">!</span>
                                <span>Vendeur qui refuse de rencontrer en personne</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-5 h-5 rounded-full bg-white text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold border border-primary-300">!</span>
                                <span>Demande de paiement hors plateforme</span>
                            </li>
                        </ul>
                    </div>
                </section>

                {{-- Section: Vendeur --}}
                <section id="selling" class="help-section hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Vendeur</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Maximisez vos ventes sur VintApp</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 text-center hover:border-primary-300 transition-colors">
                            <div class="w-11 h-11 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Publier</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Creez une annonce avec photos HD et description complete</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 text-center hover:border-primary-300 transition-colors">
                            <div class="w-11 h-11 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Booster</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Promouvez vos articles avec le systeme de Boost</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 text-center hover:border-primary-300 transition-colors">
                            <div class="w-11 h-11 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Gagner</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Recevez vos paiements sur votre wallet et retirez</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">Fonctionnalites vendeur</h3>

                    {{-- Gestion articles --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">Gestion des articles</h5>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="flex gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center font-bold text-xs flex-shrink-0">1</div>
                                        <div>
                                            <h6 class="font-bold text-gray-900 dark:text-white text-sm">Creer une annonce</h6>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Ajoutez photos HD, titre, description, categorie et prix</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center font-bold text-xs flex-shrink-0">2</div>
                                        <div>
                                            <h6 class="font-bold text-gray-900 dark:text-white text-sm">Gerer le statut</h6>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Activez, desactivez ou marquez comme vendu</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center font-bold text-xs flex-shrink-0">3</div>
                                        <div>
                                            <h6 class="font-bold text-gray-900 dark:text-white text-sm">Modifier ou Supprimer</h6>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Mettez a jour vos annonces a tout moment</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-5">
                                    <h6 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                        Conseils photos
                                    </h6>
                                    <ul class="space-y-1.5 text-xs text-gray-600 dark:text-gray-300">
                                        <li class="flex items-start gap-2"><span class="w-4 h-4 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold">&#10003;</span><span>Lumiere naturelle sans flash</span></li>
                                        <li class="flex items-start gap-2"><span class="w-4 h-4 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold">&#10003;</span><span>Cadrez sous plusieurs angles</span></li>
                                        <li class="flex items-start gap-2"><span class="w-4 h-4 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold">&#10003;</span><span>Fond neutre et degage</span></li>
                                        <li class="flex items-start gap-2"><span class="w-4 h-4 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold">&#10003;</span><span>Montrez les defauts honnetement</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ventes et commandes --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">Ventes et commandes</h5>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    <h6 class="font-bold text-gray-900 dark:text-white text-sm">Cycle de vie d'une commande</h6>
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 rounded-full bg-primary-400 text-primary-700 flex items-center justify-center text-xs font-bold">1</div>
                                        <div>
                                            <span class="font-semibold text-gray-900 dark:text-white text-sm">En attente</span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">L'acheteur a paye, le montant est securise</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 rounded-full bg-primary-400 text-primary-700 flex items-center justify-center text-xs font-bold">2</div>
                                        <div>
                                            <span class="font-semibold text-gray-900 dark:text-white text-sm">Confirmee</span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Vous confirmez la commande et preparez l'article</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 rounded-full bg-primary-400 text-primary-700 flex items-center justify-center text-xs font-bold">3</div>
                                        <div>
                                            <span class="font-semibold text-gray-900 dark:text-white text-sm">Expediee</span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Vous marquez la commande comme expediee</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 rounded-full bg-primary-400 text-primary-700 flex items-center justify-center text-xs font-bold">4</div>
                                        <div>
                                            <span class="font-semibold text-gray-900 dark:text-white text-sm">Livree</span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">L'acheteur confirme la reception, les fonds sont debloques</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-5">
                                    <h6 class="font-bold text-gray-900 dark:text-white mb-3 text-sm">Livraison locale</h6>
                                    <ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
                                        <li class="flex items-start gap-2"><span class="text-primary-600 flex-shrink-0">&#9679;</span><span><strong>Remise en main propre</strong> chez vous</span></li>
                                        <li class="flex items-start gap-2"><span class="text-primary-600 flex-shrink-0">&#9679;</span><span><strong>Point de rencontre</strong> lieu neutre GPS</span></li>
                                        <li class="flex items-start gap-2"><span class="text-primary-600 flex-shrink-0">&#9679;</span><span><strong>Retrait</strong> l'acheteur vient chercher</span></li>
                                    </ul>
                                    <div class="mt-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 text-xs text-gray-700 dark:text-gray-200">
                                        <strong>Frais de transport :</strong> $2 pour les 5 premiers km, $0.50/km supplementaire
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Systeme de Boost --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">Systeme de Boost</h5>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <div class="lg:col-span-2 space-y-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Le Boost augmente la visibilite de vos articles dans les recherches :</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                                            <h6 class="font-bold text-gray-900 dark:text-white text-sm mb-2">Types de Boost</h6>
                                            <ul class="text-xs space-y-1 text-gray-600 dark:text-gray-300">
                                                <li><span class="text-primary-600 mr-1">&#8226;</span> Spotlight - mise en avant</li>
                                                <li><span class="text-primary-600 mr-1">&#8226;</span> Premium - priorite de recherche</li>
                                                <li><span class="text-primary-600 mr-1">&#8226;</span> Urgent - vente rapide</li>
                                                <li><span class="text-primary-600 mr-1">&#8226;</span> Top - classement eleve</li>
                                            </ul>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                                            <h6 class="font-bold text-gray-900 dark:text-white text-sm mb-2">Duree et tarifs</h6>
                                            <ul class="text-xs space-y-1 text-gray-600 dark:text-gray-300">
                                                <li><span class="text-primary-600 mr-1">&#8226;</span> Prix degressif par jour</li>
                                                <li><span class="text-primary-600 mr-1">&#8226;</span> Durees : 1, 3, 7, 14, 21 ou 30 jours</li>
                                                <li><span class="text-primary-600 mr-1">&#8226;</span> Paiement depuis votre wallet</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="bg-primary-400 border-l-2 border-primary-500 rounded p-3 text-xs text-gray-800">
                                        <strong>Remboursement :</strong> Integral sous 24h, partiel apres, non remboursable passe 50% de la duree.
                                    </div>
                                </div>
                                <div class="bg-primary rounded-xl p-6 text-white flex flex-col justify-center text-center shadow-sm">
                                    <div class="text-3xl font-bold mb-1">+70%</div>
                                    <p class="text-xs text-primary-200">de visibilite en moyenne avec un Boost</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Verification d'authenticite --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">Verification d'authenticite</h5>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Rassurez vos acheteurs en faisant verifier vos articles par nos experts :</p>
                                    <ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
                                        <li class="flex items-start gap-2"><span class="w-4 h-4 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold">&#10003;</span><span><strong>Analyse IA</strong> - premiere evaluation automatique</span></li>
                                        <li class="flex items-start gap-2"><span class="w-4 h-4 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold">&#10003;</span><span><strong>Expert humain</strong> - verification approfondie</span></li>
                                        <li class="flex items-start gap-2"><span class="w-4 h-4 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold">&#10003;</span><span><strong>Badge authentifie</strong> visible sur l'annonce</span></li>
                                        <li class="flex items-start gap-2"><span class="w-4 h-4 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold">&#10003;</span><span><strong>VintPass</strong> certificat numerique avec QR code</span></li>
                                    </ul>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-5">
                                    <h6 class="font-bold text-gray-900 dark:text-white text-sm mb-3">Niveaux de certification</h6>
                                    <div class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
                                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-primary-500"></span><span>Platinum (95+)</span></div>
                                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-primary-400"></span><span>Gold (85+)</span></div>
                                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-primary-300"></span><span>Silver (75+)</span></div>
                                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-primary-600"></span><span>Bronze (60+)</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Portefeuille et retrait --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">Portefeuille et retraits</h5>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Votre argent est securise et debloque apres confirmation de livraison :</p>
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div>
                                            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Wallet en attente</h6>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Fonds bloques tant que l'acheteur n'a pas confirme</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        </div>
                                        <div>
                                            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Wallet principal</h6>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Fonds disponibles pour retrait ou achat</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </div>
                                        <div>
                                            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Retrait</h6>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Retirez vers M-Pesa, Orange Money, Airtel Money ou Africell</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-5">
                                    <h6 class="font-bold text-gray-900 dark:text-white text-sm mb-3">Commission plateforme</h6>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mb-3">Une commission de <strong>10%</strong> est prelevee sur chaque vente.</p>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 text-xs text-gray-600 dark:text-gray-300">
                                        <strong>Exemple :</strong> Vente a 100$ => 10$ de commission => 90$ reverse au vendeur
                                    </div>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-3">La commission est automatiquement distribuee au wallet entreprise.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Section: Paiements --}}
                <section id="payments" class="help-section hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Paiements</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tout sur nos methodes de paiement</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
                            <h6 class="font-semibold text-gray-900 dark:text-white text-xs">M-Pesa</h6>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                            <h6 class="font-semibold text-gray-900 dark:text-white text-xs">Orange Money</h6>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
                            <h6 class="font-semibold text-gray-900 dark:text-white text-xs">Airtel Money</h6>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                            <h6 class="font-semibold text-gray-900 dark:text-white text-xs">CinetPay</h6>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
                            <h6 class="font-semibold text-gray-900 dark:text-white text-xs">Africell</h6>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                            <h6 class="font-semibold text-gray-900 dark:text-white text-xs">AfribaPay</h6>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
                            <h6 class="font-semibold text-gray-900 dark:text-white text-xs">Illicocash</h6>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 bg-primary-400 text-primary-700 rounded-lg flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                            <h6 class="font-semibold text-gray-900 dark:text-white text-xs">Cartes bancaires</h6>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
                        <h5 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Systeme de Wallet
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                                <h6 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">Wallet principal</h6>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Solde disponible pour acheter ou retirer.</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                                <h6 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">Wallet en attente</h6>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Fonds bloques en attente de confirmation</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                                <h6 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">Conversion de devises</h6>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Convertissez USD / CDF au taux du jour</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-primary-400 border-l-2 border-primary-500 rounded-lg p-6">
                        <h6 class="font-bold text-gray-900 mb-3 flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Securite maximale
                        </h6>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start gap-2"><span class="w-5 h-5 rounded-full bg-white text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold border border-primary-300">&#10003;</span><span>Chiffrement SSL 256 bits</span></li>
                            <li class="flex items-start gap-2"><span class="w-5 h-5 rounded-full bg-white text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold border border-primary-300">&#10003;</span><span>Pas de stockage des donnees bancaires</span></li>
                            <li class="flex items-start gap-2"><span class="w-5 h-5 rounded-full bg-white text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold border border-primary-300">&#10003;</span><span>Partenaires de paiement certifies PCI-DSS</span></li>
                        </ul>
                    </div>
                </section>

                {{-- Section: Compte --}}
                <section id="account" class="help-section hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Compte</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gerer vos parametres et preferences</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 rounded-lg bg-primary-400 flex items-center justify-center mb-4">
                                <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Profil personnel</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Modifiez vos informations, photo et bio</p>
                            <a href="/profile" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700">Acceder au profil
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 hover:border-primary-300 transition-colors">
                            <div class="w-10 h-10 rounded-lg bg-primary-400 flex items-center justify-center mb-4">
                                <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Notifications</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configurez vos preferences</p>
                            <a href="/settings/notifications" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700">Configurer
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </section>

                {{-- Section: Affiliation --}}
                <section id="affiliate" class="help-section hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Affiliation</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gagnez des points en parrainant vos amis</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4 text-lg font-bold">1</div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Partagez</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Envoyez votre lien de parrainage a vos amis</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4 text-lg font-bold">2</div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Ils s'inscrivent</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Vos amis creent un compte via votre lien</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4 text-lg font-bold">3</div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Gagnez</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Recevez 500 points par filleul inscrit</p>
                        </div>
                    </div>

                    <div class="bg-primary rounded-xl p-8 text-white text-center shadow-sm">
                        <div class="text-5xl font-bold mb-1">500</div>
                        <h5 class="text-lg font-semibold mb-1">Points par parrainage</h5>
                        <p class="text-sm text-primary-200 mb-6">+ 100 points bonus pour votre filleul</p>
                        <a href="{{ route('affiliate.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary-700 rounded-lg font-semibold hover:bg-primary-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            Commencer maintenant
                        </a>
                    </div>
                </section>

                {{-- Section: Securite --}}
                <section id="safety" class="help-section hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Securite</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Votre securite est notre priorite</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        <div class="lg:col-span-2">
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <h5 class="font-bold text-gray-900 dark:text-white text-sm">Nos engagements securite</h5>
                                </div>
                                <div class="p-5 space-y-2">
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <span class="w-5 h-5 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">&#10003;</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Chiffrement SSL 256 bits pour toutes les donnees</span>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <span class="w-5 h-5 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">&#10003;</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Authentification a deux facteurs disponible</span>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <span class="w-5 h-5 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">&#10003;</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Vos donnees personnelles ne sont jamais partagees</span>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <span class="w-5 h-5 rounded-full bg-primary-200 text-primary-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">&#10003;</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Paiements securises avec partenaires certifies</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 text-center h-full flex flex-col justify-center">
                                <svg class="w-14 h-14 text-primary-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                <h6 class="font-bold text-gray-900 dark:text-white mb-1">Certification</h6>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Conforme aux standards RGPD et normes internationales</p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Section: Contact --}}
                <section id="contact" class="help-section hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Nous contacter</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Notre equipe support est la pour vous aider</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-8 text-center hover:border-primary-300 transition-colors">
                            <div class="w-11 h-11 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Email</h5>
                            <p class="text-primary-600 dark:text-primary-300 font-semibold text-sm mb-1">support@vintapp.com</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Reponse sous 24h</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-8 text-center hover:border-primary-300 transition-colors">
                            <div class="w-11 h-11 rounded-lg bg-primary-400 text-primary-700 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-1">Chat en direct</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Lun-Ven, 9h-18h</p>
                            <button onclick="alert('Le chat sera bientot disponible!')" class="px-5 py-2.5 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-100 transition-colors">
                                Ouvrir le chat
                            </button>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryLinks = document.querySelectorAll('[data-category]');
    const sections = document.querySelectorAll('.help-section');

    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.getAttribute('data-category');

            categoryLinks.forEach(l => {
                l.classList.remove('bg-primary-400', 'text-primary-700', 'border-primary-300');
                l.classList.add('text-gray-600', 'dark:text-gray-300', 'border-transparent');
                const dot = l.querySelector('span:first-child');
                if (dot) dot.style.opacity = '0.5';
            });
            this.classList.add('bg-primary-400', 'text-primary-700', 'border-primary-300');
            this.classList.remove('text-gray-600', 'dark:text-gray-300', 'border-transparent');
            const dot = this.querySelector('span:first-child');
            if (dot) dot.style.opacity = '1';

            sections.forEach(section => section.classList.add('hidden'));
            const targetSection = document.getElementById(categoryId);
            if (targetSection) {
                targetSection.classList.remove('hidden');
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const searchResults = document.getElementById('searchResults');

    const searchData = {
        'inscription': { title: 'Comment creer un compte ?', section: 'getting-started', content: 'Creez un compte gratuitement avec email, Google ou Facebook' },
        'vendre': { title: 'Comment vendre un article ?', section: 'selling', content: 'Publiez votre annonce avec photos et description detaillee' },
        'vendeur': { title: 'Guide complet du vendeur', section: 'selling', content: 'Gestion des articles, ventes, boost, verification, wallet et retraits' },
        'boost-annonce': { title: 'Systeme de Boost', section: 'selling', content: 'Promouvez vos articles : spotlight, premium, top, urgent' },
        'wallet-vendeur': { title: 'Portefeuille et retraits', section: 'selling', content: 'Wallet principal, wallet en attente, retrait vers mobile money' },
        'commission': { title: 'Commission plateforme', section: 'selling', content: '10% de commission sur chaque vente reussie' },
        'verification': { title: 'Verification d authenticite', section: 'selling', content: 'Faites verifier vos articles par IA et experts, obtenez un VintPass' },
        'livraison': { title: 'Livraison locale', section: 'selling', content: 'Remise en main propre, point de rencontre, retrait avec frais de transport' },
        'retrait-fonds': { title: 'Retrait des fonds', section: 'selling', content: 'Retirez votre argent vers M-Pesa, Orange Money, Airtel Money ou Africell' },
        'acheter': { title: 'Comment acheter en securite ?', section: 'buying', content: 'Recherchez, discutez avec le vendeur et payez en securite' },
        'paiement': { title: 'Methodes de paiement acceptees', section: 'payments', content: 'M-Pesa, Orange Money, Airtel Money, CinetPay, Africell, AfribaPay, Illicocash, cartes bancaires' },
        'wallet-paiement': { title: 'Systeme de Wallet', section: 'payments', content: 'Wallet principal, wallet en attente, conversion de devises' },
        'retrait-wallet': { title: 'Retrait d argent', section: 'payments', content: 'Retirez vos fonds vers M-Pesa, Orange Money, Airtel Money' },
        'mpesa': { title: 'M-Pesa', section: 'payments', content: 'Paiement et retrait via M-Pesa' },
        'orange-money': { title: 'Orange Money', section: 'payments', content: 'Paiement et retrait via Orange Money' },
        'airtel-money': { title: 'Airtel Money', section: 'payments', content: 'Paiement et retrait via Airtel Money' },
        'africell': { title: 'Africell', section: 'payments', content: 'Paiement via Africell' },
        'cinetpay': { title: 'CinetPay', section: 'payments', content: 'Passerelle de paiement CinetPay' },
        'afribapay': { title: 'AfribaPay', section: 'payments', content: 'Paiement mobile AfribaPay' },
        'illicocash': { title: 'Illicocash', section: 'payments', content: 'Paiement mobile via Illicocash' },
        'vintpass': { title: 'VintPass', section: 'selling', content: 'Certificat numerique d authenticite avec QR code' },
        'qr-code': { title: 'Scan QR', section: 'buying', content: 'Scannez le QR code de votre commande pour confirmer la livraison' },
        'favoris': { title: 'Favoris et liste de souhaits', section: 'buying', content: 'Ajoutez des articles a vos favoris' },
        'parrainage': { title: "Programme d affiliation", section: 'affiliate', content: 'Gagnez 500 points en parrainant vos amis' },
        'points': { title: 'Systeme de points', section: 'affiliate', content: 'Gagnez des points : 2% sur achats, 1% sur ventes' },
        '2fa': { title: 'Authentification a deux facteurs', section: 'safety', content: 'Activez la 2FA avec Google Authenticator' },
        'rgpd': { title: 'Confidentialite et RGPD', section: 'safety', content: 'Vos donnees sont protegees, jamais partagees' }
    };

    function performSearch() {
        const query = searchInput.value.toLowerCase().trim();
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            searchResults.style.display = 'none';
            return;
        }

        const results = [];
        Object.keys(searchData).forEach(key => {
            const item = searchData[key];
            if (key.includes(query) || item.title.toLowerCase().includes(query) || item.content.toLowerCase().includes(query)) {
                results.push(item);
            }
        });

        if (results.length > 0) {
            searchResults.innerHTML = results.map(item => {
                const sectionId = item.section;
                const sectionName = document.querySelector('[data-category="' + sectionId + '"]');
                const sectionLabel = sectionName ? sectionName.textContent.trim() : sectionId;
                return '<div class="search-result-item px-5 py-3.5 hover:bg-primary-400 cursor-pointer transition-colors border-b border-gray-200 dark:border-gray-700 last:border-b-0 dark:hover:bg-gray-700" data-section="' + item.section + '">' +
                    '<div class="flex items-center gap-2 mb-0.5">' +
                    '<h6 class="font-semibold text-gray-900 dark:text-white text-sm">' + item.title + '</h6>' +
                    '<span class="text-[10px] font-medium text-primary-600 dark:text-primary-300 bg-primary-400 dark:bg-gray-700 px-2 py-0.5 rounded-full">' + sectionLabel + '</span>' +
                    '</div>' +
                    '<p class="text-xs text-gray-500 dark:text-gray-400">' + item.content + '</p>' +
                    '</div>';
            }).join('');

            searchResults.querySelectorAll('.search-result-item').forEach(item => {
                item.addEventListener('click', function() {
                    const sectionId = this.getAttribute('data-section');
                    const targetLink = document.querySelector('[data-category="' + sectionId + '"]');
                    if (targetLink) {
                        targetLink.click();
                        searchResults.classList.add('hidden');
                        searchResults.style.display = 'none';
                        searchInput.value = '';
                    }
                });
            });

            searchResults.classList.remove('hidden');
            searchResults.style.display = 'block';
        } else {
            searchResults.innerHTML = '<div class="px-5 py-6 text-center"><p class="text-sm text-gray-500 dark:text-gray-400">Aucun resultat trouve</p></div>';
            searchResults.classList.remove('hidden');
            searchResults.style.display = 'block';
        }
    }

    if (searchInput && searchBtn && searchResults) {
        searchInput.addEventListener('input', performSearch);
        searchBtn.addEventListener('click', performSearch);

        document.addEventListener('click', function(e) {
            if (!searchResults.contains(e.target) && !searchInput.contains(e.target) && !searchBtn.contains(e.target)) {
                searchResults.classList.add('hidden');
                searchResults.style.display = 'none';
            }
        });

        searchResults.style.display = 'none';
    }
});
</script>
@endpush
