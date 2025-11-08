

<?php $__env->startSection('title', 'Paramètres - VintApp'); ?>

<?php $__env->startSection('meta_description', 'Gérez vos préférences, votre profil et les paramètres de votre compte VintApp.'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête avec navigation -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <button onclick="history.back()" class="mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors">
                    <i class="fas fa-arrow-left text-lg"></i>
                </button>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Paramètres</h1>
                    <p class="text-gray-600 mt-1">Gérez vos préférences et votre compte</p>
                </div>
            </div>
        </div>

        <!-- Profil utilisateur -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center space-x-4">
                <?php if(Auth::user()->avatar): ?>
                    <?php
                        $avatarUrl = filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL) 
                            ? Auth::user()->avatar 
                            : asset('storage/' . Auth::user()->avatar);
                    ?>
                    <img src="<?php echo e($avatarUrl); ?>" 
                         alt="<?php echo e(Auth::user()->name); ?>" 
                         class="w-16 h-16 rounded-full object-cover border-4 border-purple-200"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-purple-600 to-cyan-400 items-center justify-center text-white font-bold text-xl hidden">
                        <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                    </div>
                <?php else: ?>
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-purple-600 to-cyan-400 flex items-center justify-center text-white font-bold text-xl">
                        <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                    </div>
                <?php endif; ?>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900"><?php echo e(Auth::user()->name); ?></h2>
                    <p class="text-gray-600"><?php echo e(Auth::user()->email); ?></p>
                    <div class="flex items-center mt-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                        <span class="text-sm text-green-600 font-medium">Compte actif</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grille des sections -->
        <div class="space-y-8">
            <!-- Section : Mon compte -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user-circle mr-3 text-purple-600"></i>
                        Mon compte
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <a href="<?php echo e(route('profile.edit')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-user-cog text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Modifier mon profil</h4>
                                    <p class="text-sm text-gray-600">Informations personnelles, photo de profil</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                        </div>
                    </a>

                    <button onclick="openPersonalizationModal()" class="w-full text-left px-6 py-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cogs text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Personnalisation</h4>
                                    <p class="text-sm text-gray-600">Préférences d'affichage et notifications</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                        </div>
                    </button>

                    <button onclick="openThemeModal()" class="w-full text-left px-6 py-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-palette text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Thème d'affichage</h4>
                                    <p class="text-sm text-gray-600">Clair, Sombre ou Automatique</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span id="current-theme-badge" class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full font-medium">Auto</span>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Section : Navigation rapide -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-compass mr-3 text-purple-600"></i>
                        Navigation rapide
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                    <!-- Colonne 1 -->
                    <div class="divide-y divide-gray-200">
                        <a href="<?php echo e(route('dashboard')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Dashboard</h4>
                                    <p class="text-sm text-gray-600">Vue d'ensemble</p>
                                </div>
                            </div>
                        </a>

                        <a href="<?php echo e(route('orders.index')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Mes commandes</h4>
                                    <p class="text-sm text-gray-600">Historique d'achats</p>
                                </div>
                            </div>
                        </a>

                        <a href="<?php echo e(route('orders.my-sales')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Mes ventes</h4>
                                    <p class="text-sm text-gray-600">Articles vendus</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Colonne 2 -->
                    <div class="divide-y divide-gray-200">
                        <a href="<?php echo e(route('items.my-items')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Mes articles</h4>
                                    <p class="text-sm text-gray-600">Articles en vente</p>
                                </div>
                            </div>
                        </a>

                        <a href="<?php echo e(route('wallet.index')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Mon portefeuille</h4>
                                    <p class="text-sm text-gray-600">Solde et transactions</p>
                                </div>
                            </div>
                        </a>

                        <a href="<?php echo e(route('messages.index')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Messages</h4>
                                    <p class="text-sm text-gray-600">Conversations</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Affiliation (section spéciale) -->
                <div class="border-t border-gray-200 bg-gradient-to-r from-purple-50 to-cyan-50">
                    <a href="<?php echo e(route('affiliate.dashboard')); ?>" class="block px-6 py-4 hover:from-purple-100 hover:to-cyan-100 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-cyan-400 text-white rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg">
                                    <i class="fas fa-users text-lg"></i>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-semibold text-gray-900">Programme d'affiliation</h4>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">NOUVEAU</span>
                                    </div>
                                    <p class="text-sm text-gray-600">Parrainez vos amis et gagnez des récompenses</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Section : Catalogue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-store mr-3 text-purple-600"></i>
                        Catalogue
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                    <a href="<?php echo e(route('brands.index')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Marques</h4>
                                <p class="text-sm text-gray-600">Explorer les marques</p>
                            </div>
                        </div>
                    </a>

                    <a href="<?php echo e(route('categories.index')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Catégories</h4>
                                <p class="text-sm text-gray-600">Par catégorie</p>
                            </div>
                        </div>
                    </a>

                    <a href="<?php echo e(route('items.index')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Tous les articles</h4>
                                <p class="text-sm text-gray-600">Catalogue complet</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Section : Aide & Support -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-question-circle mr-3 text-purple-600"></i>
                        Aide & Support
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <a href="<?php echo e(route('help.index')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-life-ring"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Centre d'aide</h4>
                                    <p class="text-sm text-gray-600">FAQ et guides</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                        </div>
                    </a>

                    <a href="<?php echo e(route('help.index')); ?>#contact" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Nous contacter</h4>
                                    <p class="text-sm text-gray-600">Envoyez-nous un message</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                        </div>
                    </a>

                    <a href="<?php echo e(route('terms')); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-contract"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Conditions d'utilisation</h4>
                                    <p class="text-sm text-gray-600">CGU et politique de confidentialité</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Section : Actions du compte -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Déconnexion -->
                <div class="bg-white rounded-xl shadow-sm border border-orange-200 overflow-hidden">
                    <div class="px-6 py-4 bg-orange-50 border-b border-orange-200">
                        <h3 class="text-lg font-semibold text-orange-900 flex items-center">
                            <i class="fas fa-sign-out-alt mr-3 text-orange-600"></i>
                            Déconnexion
                        </h3>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Se déconnecter</span>
                            </button>
                        </form>
                        <p class="text-sm text-gray-600 mt-2 text-center">Déconnexion sécurisée de votre compte</p>
                    </div>
                </div>

                <!-- Zone dangereuse -->
                <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
                    <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                        <h3 class="text-lg font-semibold text-red-900 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3 text-red-600"></i>
                            Zone dangereuse
                        </h3>
                    </div>
                    <div class="p-6">
                        <button onclick="openDeleteAccountModal()" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                            <i class="fas fa-user-slash"></i>
                            <span>Supprimer mon compte</span>
                        </button>
                        <p class="text-sm text-red-600 mt-2 text-center">Action irréversible</p>
                    </div>
                </div>
            </div>

            <!-- Version et copyright -->
            <div class="text-center py-8 text-gray-500">
                <div class="flex items-center justify-center space-x-2 mb-2">
                    <i class="fas fa-mobile-alt"></i>
                    <span class="font-medium"><?php echo e(config('app.name', 'VintApp')); ?> v1.0.0</span>
                </div>
                <p class="text-sm">© <?php echo e(date('Y')); ?> Tous droits réservés</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de sélection du thème -->
<div id="themeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-palette mr-2 text-purple-600"></i>
                    Choisir un thème
                </h3>
                <button onclick="closeThemeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <button onclick="selectTheme('light')" class="theme-option w-full p-4 border-2 border-gray-200 rounded-lg hover:border-purple-300 transition-colors text-left" data-theme="light">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-sun"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Clair</h4>
                                <p class="text-sm text-gray-600">Thème lumineux</p>
                            </div>
                        </div>
                        <i class="fas fa-check text-green-500 hidden theme-check"></i>
                    </div>
                </button>

                <button onclick="selectTheme('dark')" class="theme-option w-full p-4 border-2 border-gray-200 rounded-lg hover:border-purple-300 transition-colors text-left" data-theme="dark">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-800 text-white rounded-lg flex items-center justify-center">
                                <i class="fas fa-moon"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Sombre</h4>
                                <p class="text-sm text-gray-600">Thème foncé</p>
                            </div>
                        </div>
                        <i class="fas fa-check text-green-500 hidden theme-check"></i>
                    </div>
                </button>

                <button onclick="selectTheme('auto')" class="theme-option w-full p-4 border-2 border-gray-200 rounded-lg hover:border-purple-300 transition-colors text-left" data-theme="auto">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-magic"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Automatique</h4>
                                <p class="text-sm text-gray-600">Suit les préférences système</p>
                            </div>
                        </div>
                        <i class="fas fa-check text-green-500 hidden theme-check"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de personnalisation -->
<div id="personalizationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-cogs mr-2 text-green-600"></i>
                    Personnalisation
                </h3>
                <button onclick="closePersonalizationModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-6">
            <!-- Notifications -->
            <div>
                <h4 class="font-semibold text-gray-900 mb-3">Notifications</h4>
                <div class="space-y-3">
                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-bell text-blue-600"></i>
                            <span class="text-gray-900">Notifications push</span>
                        </div>
                        <input type="checkbox" class="toggle-switch" checked>
                    </label>
                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-green-600"></i>
                            <span class="text-gray-900">Notifications email</span>
                        </div>
                        <input type="checkbox" class="toggle-switch" checked>
                    </label>
                </div>
            </div>

            <!-- Préférences d'affichage -->
            <div>
                <h4 class="font-semibold text-gray-900 mb-3">Affichage</h4>
                <div class="space-y-3">
                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-eye text-purple-600"></i>
                            <span class="text-gray-900">Mode compact</span>
                        </div>
                        <input type="checkbox" class="toggle-switch">
                    </label>
                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-images text-orange-600"></i>
                            <span class="text-gray-900">Chargement automatique des images</span>
                        </div>
                        <input type="checkbox" class="toggle-switch" checked>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button onclick="closePersonalizationModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Annuler
                </button>
                <button class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Sauvegarder
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression du compte -->
<div id="deleteAccountModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 bg-red-50 border-b border-red-200">
            <h3 class="text-xl font-semibold text-red-900 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>
                Supprimer définitivement mon compte
            </h3>
        </div>
        <div class="p-6">
            <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <i class="fas fa-skull-crossbones mr-2"></i>
                    <strong>ATTENTION !</strong> Cette action est <strong>IRRÉVERSIBLE</strong>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-gray-700 mb-3">En supprimant votre compte, vous perdrez :</p>
                <ul class="space-y-2 text-red-600">
                    <li class="flex items-center">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Tous vos articles</strong> en vente
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Votre historique</strong> de commandes
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Vos messages</strong> et conversations
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Votre portefeuille</strong> et son solde
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Toutes vos données</strong> personnelles
                    </li>
                </ul>
            </div>

            <div class="bg-yellow-100 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                    <div>
                        Si vous avez des <strong>commandes en cours</strong> ou un <strong>solde dans votre portefeuille</strong>, veuillez les finaliser avant de supprimer votre compte.
                    </div>
                </div>
            </div>

            <form id="deleteAccountForm" method="POST" action="<?php echo e(route('profile.destroy')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                
                <div class="space-y-4">
                    <div>
                        <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Pour confirmer, entrez votre mot de passe :
                        </label>
                        <input type="password" 
                               id="delete_password" 
                               name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Votre mot de passe"
                               required
                               autocomplete="current-password">
                    </div>
                    
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="checkbox" id="confirmDelete" class="mt-1" required>
                        <span class="text-sm text-gray-700">
                            Je comprends que cette action est définitive et irréversible
                        </span>
                    </label>
                </div>
            </form>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <button onclick="closeDeleteAccountModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Annuler
                </button>
                <button onclick="confirmDeleteAccount()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center space-x-2">
                    <i class="fas fa-trash-alt"></i>
                    <span>Supprimer définitivement</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le badge de thème
    updateThemeBadge();
});

// ========================================
// Gestion du thème
// ========================================

function openThemeModal() {
    document.getElementById('themeModal').classList.remove('hidden');
    updateThemeSelection();
}

function closeThemeModal() {
    document.getElementById('themeModal').classList.add('hidden');
}

function selectTheme(theme) {
    applyTheme(theme);
    localStorage.setItem('theme', theme);
    updateThemeBadge();
    
    // Sauvegarder sur le serveur
    if (window.isAuthenticated) {
        fetch('/profile/theme', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ theme_preference: theme })
        });
    }
    
    closeThemeModal();
}

function updateThemeSelection() {
    const currentTheme = getPreferredTheme();
    const themeOptions = document.querySelectorAll('.theme-option');
    
    themeOptions.forEach(option => {
        const check = option.querySelector('.theme-check');
        if (option.getAttribute('data-theme') === currentTheme) {
            option.classList.add('border-purple-500', 'bg-purple-50');
            check.classList.remove('hidden');
        } else {
            option.classList.remove('border-purple-500', 'bg-purple-50');
            check.classList.add('hidden');
        }
    });
}

function updateThemeBadge() {
    const currentTheme = getPreferredTheme();
    const badge = document.getElementById('current-theme-badge');
    const labels = {
        'light': 'Clair',
        'dark': 'Sombre', 
        'auto': 'Auto'
    };
    
    if (badge) {
        badge.textContent = labels[currentTheme] || 'Auto';
    }
}

function getPreferredTheme() {
    return localStorage.getItem('theme') || window.userTheme || 'auto';
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
}

// ========================================
// Gestion de la personnalisation  
// ========================================

function openPersonalizationModal() {
    document.getElementById('personalizationModal').classList.remove('hidden');
}

function closePersonalizationModal() {
    document.getElementById('personalizationModal').classList.add('hidden');
}

// ========================================
// Gestion de la suppression du compte
// ========================================

function openDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.remove('hidden');
}

function closeDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.add('hidden');
    // Reset du formulaire
    document.getElementById('deleteAccountForm').reset();
}

function confirmDeleteAccount() {
    const form = document.getElementById('deleteAccountForm');
    const password = document.getElementById('delete_password').value;
    const confirmCheckbox = document.getElementById('confirmDelete');
    
    // Validations
    if (!password) {
        alert('❌ Veuillez entrer votre mot de passe pour confirmer.');
        document.getElementById('delete_password').focus();
        return;
    }
    
    if (!confirmCheckbox.checked) {
        alert('❌ Veuillez cocher la case de confirmation.');
        confirmCheckbox.focus();
        return;
    }
    
    // Confirmation finale
    const finalConfirm = confirm(
        '⚠️ DERNIÈRE CONFIRMATION\n\n' +
        'Êtes-vous ABSOLUMENT SÛR de vouloir supprimer votre compte ?\n\n' +
        '• Cette action est IRRÉVERSIBLE\n' +
        '• Toutes vos données seront DÉFINITIVEMENT supprimées\n' +
        '• Vous ne pourrez PAS récupérer votre compte\n\n' +
        'Cliquez sur OK pour confirmer la suppression définitive.'
    );
    
    if (finalConfirm) {
        form.submit();
    }
}

// Fermer les modaux en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    const modals = ['themeModal', 'personalizationModal', 'deleteAccountModal'];
    
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

// Touches d'échappement pour fermer les modaux
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['themeModal', 'personalizationModal', 'deleteAccountModal'];
        modals.forEach(modalId => {
            document.getElementById(modalId).classList.add('hidden');
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Animations personnalisées */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.max-w-4xl {
    animation: fadeInUp 0.6s ease-out;
}

/* Hover effects améliorés */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.transition-transform {
    transition: transform 0.2s ease;
}

/* Toggle switch custom */
.toggle-switch {
    appearance: none;
    width: 3rem;
    height: 1.5rem;
    border-radius: 9999px;
    background-color: #d1d5db;
    position: relative;
    cursor: pointer;
    transition: background-color 0.2s;
}

.toggle-switch:checked {
    background-color: #9333ea;
}

.toggle-switch::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 50%;
    background-color: white;
    transition: transform 0.2s;
}

.toggle-switch:checked::after {
    transform: translateX(1.5rem);
}

/* Effets de survol pour les cartes */
.group:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Transitions fluides */
* {
    transition: all 0.2s ease;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .grid-cols-1.md\:grid-cols-2 > *,
    .grid-cols-1.md\:grid-cols-3 > * {
        border-right: none !important;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    [data-theme="auto"] .bg-gray-50 {
        background-color: #1f2937;
    }
    
    [data-theme="auto"] .text-gray-900 {
        color: #f9fafb;
    }
    
    [data-theme="auto"] .bg-white {
        background-color: #374151;
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/settings/index.blade.php ENDPATH**/ ?>