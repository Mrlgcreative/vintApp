<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php
        $isExpert = auth()->check() && auth()->user()->isExpert();
        $contextTitle = $isExpert ? 'Expert' : 'Administration';
    ?>
    <title><?php echo $__env->yieldContent('title'); ?> - <?php echo e($contextTitle); ?> <?php echo e($appName ?? 'VintApp'); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset($appFavicon ?? '/favicon.ico')); ?>">
    
    <!-- CSS -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    
    <!-- Lazy Loading CSS -->
    <link href="<?php echo e(asset('css/lazy-loading.css')); ?>" rel="stylesheet">
    
    <!-- Custom Admin Styles -->
    <link href="<?php echo e(asset('css/admin-components.css')); ?>" rel="stylesheet">
    
    <!-- CSS Dynamique VintApp avec Couleurs Actives -->
    <?php if(isset($customCSSUrl) && $customCSSUrl): ?>
        <link href="<?php echo e($customCSSUrl); ?>?v=<?php echo e(time()); ?>" rel="stylesheet">
    <?php endif; ?>
    
    <!-- Variables CSS Dynamiques de Secours -->
    <style>
        <?php echo $activePaletteCSS ?? ''; ?>

    </style>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Page Styles -->
    <?php echo $__env->yieldPushContent('styles'); ?>
    

    
    <!-- Styles complémentaires pour les composants -->
    <style>
        /* Personnalisation des composants externes avec Tailwind */
        .select2-container--default .select2-selection--single {
            @apply border border-gray-300 dark:border-gray-600 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-200;
        }
        
        .flatpickr-input {
            @apply border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-200;
        }

        /* Badge personnalisé pour les notifications */
        .notification-dot {
            @apply absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center;
        }

        /* Transitions fluides */
        .transition-all {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Scrollbar personnalisé */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            @apply bg-primary-800 rounded-full;
            margin: 8px 0;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            @apply bg-white bg-opacity-30 rounded-full;
            border: 2px solid transparent;
            background-clip: padding-box;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            @apply bg-white bg-opacity-50;
        }
        
        /* Scrollbar pour Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) rgba(59, 130, 246, 0.5);
        }

        /* Sidebar responsive */
        @media (max-width: 1023px) {
            #sidebar {
                transform: translateX(-100%);
            }
            #sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 font-sans text-sm leading-relaxed text-gray-900 dark:text-white">
    <div class="flex min-h-screen">
        <?php
            // Détecter si l'utilisateur est un expert
            $isExpert = auth()->check() && auth()->user()->isExpert();
        ?>

        <!-- Sidebar -->
        <nav class="fixed left-0 top-0 z-50 h-screen w-72 bg-gradient-to-b from-primary-700 to-primary-900 shadow-2xl transition-transform duration-300 ease-in-out" id="sidebar">
            <div class="flex h-full flex-col">
                <!-- Brand -->
                <div class="relative border-b border-white/10 bg-primary-600 dark:bg-gray-800/5 p-6">
                    <?php if (isset($component)) { $__componentOriginalac37604bae5cded3771d6931140b8398 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalac37604bae5cded3771d6931140b8398 = $attributes; } ?>
<?php $component = App\View\Components\AppBrand::resolve(['showLogo' => true,'showName' => true,'logoHeight' => '30px','logoWidth' => '100px','nameSize' => '1.25rem','nameClass' => 'text-white font-bold'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-brand'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppBrand::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $attributes = $__attributesOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__attributesOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $component = $__componentOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__componentOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>
                    <div class="absolute bottom-0 left-6 right-6 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 space-y-1 p-4 custom-scrollbar overflow-y-auto">
                    <?php if($isExpert): ?>
                        <!-- Menu Expert -->
                        <a href="<?php echo e(route('expert.dashboard')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('expert.dashboard*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-shield-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Dashboard Expert</span>
                        </a>

                        <a href="<?php echo e(route('expert.verifications.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('expert.verifications*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-search w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Mes Vérifications</span>
                            <?php
                                $pendingVerifications = \App\Models\ProductAuthenticityCheck::where('expert_id', auth()->id())
                                    ->where('status', 'expert_review')
                                    ->count();
                            ?>
                            <?php if($pendingVerifications > 0): ?>
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-orange-500 rounded-full">
                                    <?php echo e($pendingVerifications); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <a href="<?php echo e(route('expert.verifications.index', ['status' => 'expert_review'])); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('expert.verifications.index') && request('status') === 'expert_review'): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-clock w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">En attente d'examen</span>
                            <?php if($pendingVerifications > 0): ?>
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full animate-pulse">
                                    <?php echo e($pendingVerifications); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <a href="<?php echo e(route('expert.verifications.index', ['status' => 'expert_approved'])); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('expert.verifications.index') && request('status') === 'expert_approved'): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-check-circle w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Approuvées</span>
                        </a>

                        <a href="<?php echo e(route('expert.verifications.index', ['status' => 'expert_rejected'])); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('expert.verifications.index') && request('status') === 'expert_rejected'): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-times-circle w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Rejetées</span>
                        </a>

                        <!-- Séparateur -->
                        <div class="my-4 h-px bg-white dark:bg-gray-800/10"></div>

                        <a href="<?php echo e(route('expert.profile')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('expert.profile*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-user-cog w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Mon Profil Expert</span>
                        </a>

                        <!-- Statistiques rapides -->
                        <div class="mt-6 rounded-xl bg-primary-600 dark:bg-gray-800/5 p-4">
                            <h4 class="text-sm font-semibold text-white/80 mb-3">Statistiques</h4>
                            <?php
                                $expertStats = [
                                    'total' => \App\Models\ProductAuthenticityCheck::where('expert_id', auth()->id())->count(),
                                    'completed_today' => \App\Models\ProductAuthenticityCheck::where('expert_id', auth()->id())
                                        ->whereDate('expert_completed_at', today())->count(),
                                    'approval_rate' => auth()->user()->expertProfile->approval_rate ?? 0
                                ];
                            ?>
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between text-white/60">
                                    <span>Total traité</span>
                                    <span class="text-white font-medium"><?php echo e($expertStats['total']); ?></span>
                                </div>
                                <div class="flex justify-between text-white/60">
                                    <span>Aujourd'hui</span>
                                    <span class="text-green-400 font-medium"><?php echo e($expertStats['completed_today']); ?></span>
                                </div>
                                <div class="flex justify-between text-white/60">
                                    <span>Taux succès</span>
                                    <span class="text-blue-400 font-medium"><?php echo e(number_format($expertStats['approval_rate'], 1)); ?>%</span>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Menu Admin -->
                        <a href="<?php echo e(route('admin.dashboard')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.dashboard')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-tachometer-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Tableau de bord</span>
                        </a>

                        <a href="<?php echo e(route('admin.users.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') || request()->routeIs('admin.users.edit')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-users w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Utilisateurs</span>
                            <?php if(isset($pendingUsersCount) && $pendingUsersCount > 0): ?>
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                    <?php echo e($pendingUsersCount); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- 🆕 Menu Utilisateurs Connectés -->
                        <a href="<?php echo e(route('admin.users.online')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.users.online')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-user-check w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Utilisateurs Connectés</span>
                            <span class="inline-flex items-center justify-center w-3 h-3 bg-green-400 rounded-full animate-pulse shadow-lg shadow-green-400/50"></span>
                        </a>

                        <!-- 🎯 Menu Gestion des Experts -->
                        <a href="<?php echo e(route('admin.experts.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.experts.*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-user-graduate w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Experts</span>
                            <?php
                                $totalExperts = \App\Models\ExpertProfile::count();
                                $activeExperts = \App\Models\ExpertProfile::where('is_active', true)->count();
                            ?>
                            <?php if($totalExperts > 0): ?>
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-primary-500 rounded-full">
                                    <?php echo e($activeExperts); ?>/<?php echo e($totalExperts); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <a href="<?php echo e(route('admin.transactions.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.transactions.*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-exchange-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Transactions</span>
                        </a>

                        <a href="<?php echo e(route('admin.wallets.pending')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.wallets.pending')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-clock w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Wallets en attente</span>
                            <?php if(isset($pendingWalletsCount) && $pendingWalletsCount > 0): ?>
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-800 dark:text-gray-100 bg-yellow-400 rounded-full">
                                    <?php echo e($pendingWalletsCount); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <a href="<?php echo e(route('admin.orders.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.orders.*') && !request()->routeIs('admin.orders.tracking')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-shopping-cart w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Commandes</span>
                        </a>

                        <!-- 🆕 Menu Vérification Items IA -->
                        <a href="<?php echo e(route('admin.items.pending_verification')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.items.pending_verification')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-search-plus w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Vérification IA</span>
                            <?php
                                $pendingItemsCount = \App\Models\Item::where('verification_status', 'pending')->count();
                            ?>
                            <?php if($pendingItemsCount > 0): ?>
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-purple-500 rounded-full animate-pulse">
                                    <?php echo e($pendingItemsCount); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- 🆕 Menu Remboursements -->
                        <a href="<?php echo e(route('admin.refunds.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.refunds.*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-undo w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Remboursements</span>
                            <?php
                                $pendingRefundsCount = \App\Models\Refund::where('status', 'pending')->count();
                            ?>
                            <?php if($pendingRefundsCount > 0): ?>
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-orange-500 rounded-full animate-pulse">
                                    <?php echo e($pendingRefundsCount); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- 🆕 Menu Traçage GPS -->
                        <a href="<?php echo e(route('admin.orders.tracking.list')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.orders.tracking*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-map-marker-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Traçage GPS</span>
                            <span class="inline-flex items-center justify-center w-3 h-3 bg-primary-400 rounded-full animate-pulse shadow-lg shadow-primary-400/50"></span>
                        </a>

                        <a href="<?php echo e(route('admin.brands.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.brands.*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-tags w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Marques</span>
                        </a>

                        <a href="<?php echo e(route('admin.categories.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.categories.*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-list w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Catégories</span>
                        </a>

                        <a href="<?php echo e(route('admin.support.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.support.*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-headset w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Support Client</span>
                            <?php
                                $unassignedSupport = \App\Models\SupportChat::whereNull('admin_id')
                                    ->whereIn('status', ['open', 'in_progress'])->count();
                            ?>
                            <?php if($unassignedSupport > 0): ?>
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-orange-500 rounded-full">
                                    <?php echo e($unassignedSupport); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- 🎯 Menu Affiliation et Récompenses -->
                        <a href="<?php echo e(route('admin.affiliate.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.affiliate.*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-users-cog w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Affiliation</span>
                            <?php
                                $topPerformersCount = \App\Models\User::whereHas('referrals', function($q) {
                                    $q->whereDate('created_at', '>=', now()->subDays(30));
                                })->count();
                            ?>
                            <?php if($topPerformersCount > 0): ?>
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-800 dark:text-gray-100 bg-yellow-400 rounded-full">
                                    <?php echo e($topPerformersCount); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <a href="<?php echo e(route('admin.reports')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.reports')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-chart-bar w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Rapports</span>
                        </a>

                        <a href="<?php echo e(route('admin.monitoring.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.monitoring.*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-heartbeat w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Monitoring</span>
                            <span class="inline-flex items-center justify-center w-2 h-2 bg-green-400 rounded-full animate-pulse shadow-lg shadow-green-400/50"></span>
                        </a>

                        <a href="<?php echo e(route('admin.logs')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.logs')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-list-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Logs système</span>
                        </a>

                        <a href="<?php echo e(route('admin.settings.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.settings.*') && !request()->routeIs('admin.locations.*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-cog w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Paramètres</span>
                        </a>

                        <a href="<?php echo e(route('admin.locations.index')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.locations.*')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-map-marked-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span>Zones autorisées</span>
                        </a>

                        <!-- 🔔 Broadcast Notifications FCM -->
                        <a href="<?php echo e(route('admin.broadcast.fcm')); ?>" 
                           class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white/90 <?php if(request()->routeIs('admin.broadcast.fcm')): ?> bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 <?php endif; ?>">
                            <i class="fas fa-bullhorn w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="flex-1">Broadcast Push</span>
                            <span class="inline-flex items-center justify-center w-2 h-2 bg-orange-400 rounded-full animate-pulse shadow-lg shadow-orange-400/50"></span>
                        </a>
                    <?php endif; ?>
                </nav>

                <!-- Footer -->
                <div class="mt-auto p-4 space-y-2">
                    <a href="<?php echo e(route('home')); ?>" 
                       class="flex w-full items-center justify-center rounded-xl border border-white/20 bg-transparent px-4 py-3 text-white/80 transition-all duration-300 hover:bg-primary-600 dark:bg-gray-800/10 hover:text-white">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour au site
                    </a>
                    <?php if($isExpert): ?>
                        <!-- Actions spécifiques expert -->
                        <div class="text-center text-xs text-white/50 py-2">
                            Interface Expert VintApp
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="w-full">
                        <?php echo csrf_field(); ?>
                        <button type="submit" 
                                class="flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-3 text-white transition-all duration-300 hover:from-red-600 hover:to-red-700 hover:shadow-lg">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Overlay pour mobile -->
        <div class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm transition-opacity duration-300 lg:hidden" id="sidebar-overlay" style="display: none; opacity: 0;"></div>

        <!-- Contenu principal -->
        <main class="flex-1 transition-all duration-300" id="main-content">
            <!-- Header -->
            <header class="sticky top-0 z-30 border-b border-primary-700 bg-primary dark:bg-gray-800/95 p-4 shadow-sm backdrop-blur-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Toggle Button -->
                        <button class="rounded-lg p-2 text-white transition-all duration-300 hover:bg-primary-700 dark:bg-gray-800 hover:text-white dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-white" 
                                id="sidebar-toggle"
                                aria-label="Toggle sidebar">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-bold text-white dark:text-white lg:text-2xl"><?php echo $__env->yieldContent('page-title'); ?></h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="relative rounded-lg p-2 text-white transition-colors hover:bg-primary-700 dark:bg-gray-800 hover:text-white dark:hover:text-white" 
                                    type="button" id="notificationsDropdown">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="notification-dot hidden" id="notification-badge">0</span>
                            </button>
                            
                            <!-- Dropdown notifications -->
                            <div class="absolute right-0 top-full mt-2 hidden w-80 origin-top-right rounded-xl bg-white dark:bg-gray-800 shadow-xl ring-1 ring-black/5" 
                                 id="notifications-dropdown">
                                <div class="p-4">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto" id="notifications-container">
                                    <!-- Les notifications seront injectées ici -->
                                </div>
                                <div class="border-t border-gray-100 p-4">
                                    <a href="/admin/notifications" 
                                       class="block text-center text-sm font-medium text-primary-600 hover:text-primary-700">
                                        Voir toutes les notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Profil -->
                        <div class="relative">
                            <button class="flex items-center rounded-lg p-2 text-white dark:text-gray-300 transition-colors hover:bg-primary-700 dark:hover:bg-gray-700" 
                                    type="button" id="userDropdown">
                                <?php if(auth()->user()->avatar): ?>
                                    <?php
                                        $avatarUrl = filter_var(auth()->user()->avatar, FILTER_VALIDATE_URL) 
                                            ? auth()->user()->avatar 
                                            : asset('storage/' . auth()->user()->avatar);
                                    ?>
                                    <img src="<?php echo e($avatarUrl); ?>" 
                                         alt="<?php echo e(auth()->user()->name); ?>" 
                                         class="h-8 w-8 rounded-full object-cover border-2 border-white mr-2"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="mr-2 hidden h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 text-white text-sm font-semibold">
                                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                                    </div>
                                <?php else: ?>
                                    <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 text-white text-sm font-semibold">
                                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                                    </div>
                                <?php endif; ?>
                                <span class="hidden text-sm font-medium text-white dark:text-white lg:block"><?php echo e(auth()->user()->name); ?></span>
                                <i class="fas fa-chevron-down ml-2 text-xs text-white dark:text-gray-500"></i>
                            </button>
                            
                            <!-- Dropdown profil -->
                            <div class="absolute right-0 top-full mt-2 hidden w-48 origin-top-right rounded-xl bg-white dark:bg-gray-800 shadow-xl ring-1 ring-black/5" 
                                 id="user-dropdown">
                                <div class="p-1">
                                    <a href="<?php echo e(route('profile.edit')); ?>" 
                                       class="flex items-center rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <i class="fas fa-user mr-3 w-4 text-center text-gray-400"></i>
                                        Mon profil
                                    </a>
                                    <div class="my-1 h-px bg-gray-100 dark:bg-gray-700"></div>
                                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" 
                                                class="flex w-full items-center rounded-lg px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            <i class="fas fa-sign-out-alt mr-3 w-4 text-center"></i>
                                            Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Actions de page -->
            <?php if (! empty(trim($__env->yieldContent('page-actions')))): ?>
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <?php echo $__env->yieldContent('page-actions'); ?>
                </div>
            <?php endif; ?>

            <!-- Alertes -->
            <div class="p-4 space-y-4">
                <?php if(session('success')): ?>
                    <div class="flex items-center rounded-xl bg-green-50 p-4 text-green-800 animate-fade-in" role="alert">
                        <i class="fas fa-check-circle mr-3 text-green-500"></i>
                        <span class="flex-1"><?php echo e(session('success')); ?></span>
                        <button type="button" class="ml-4 text-green-500 hover:text-green-700" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="flex items-center rounded-xl bg-red-50 p-4 text-red-800 animate-fade-in" role="alert">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                        <span class="flex-1"><?php echo e(session('error')); ?></span>
                        <button type="button" class="ml-4 text-red-500 hover:text-red-700" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if(session('warning')): ?>
                    <div class="flex items-center rounded-xl bg-yellow-50 p-4 text-yellow-800 animate-fade-in" role="alert">
                        <i class="fas fa-exclamation-triangle mr-3 text-yellow-500"></i>
                        <span class="flex-1"><?php echo e(session('warning')); ?></span>
                        <button type="button" class="ml-4 text-yellow-500 hover:text-yellow-700" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Contenu principal -->
            <div class="flex-1 p-4 lg:p-8" data-page-type="dashboard">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

    <script>
        // Attendre que le DOM et jQuery soient complètement chargés
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Admin layout JavaScript chargé');

            // Initialisation des composants jQuery
            if (typeof $ !== 'undefined') {
                // Select2
                $('.select2').select2();

                // Flatpickr (datepicker)
                flatpickr(".datepicker", {
                    locale: "fr",
                    dateFormat: "Y-m-d",
                    allowInput: true
                });

                // Flatpickr (datetimepicker)
                flatpickr(".datetimepicker", {
                    locale: "fr",
                    dateFormat: "Y-m-d H:i",
                    enableTime: true,
                    time_24hr: true,
                    allowInput: true
                });
            }

            // Sidebar Toggle - Gestion responsive améliorée
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const mainContent = document.getElementById('main-content');
            
            console.log('🔍 Éléments sidebar:', {
                toggle: !!sidebarToggle,
                sidebar: !!sidebar,
                overlay: !!sidebarOverlay,
                mainContent: !!mainContent
            });
            
            // État initial basé sur la taille de l'écran
            function initSidebar() {
                if (window.innerWidth >= 1024) {
                    // Desktop: sidebar visible, margin sur le contenu
                    sidebar.classList.remove('active');
                    sidebar.style.transform = 'translateX(0)';
                    mainContent.style.marginLeft = '288px'; // 18rem = 288px
                    if (sidebarOverlay) {
                        sidebarOverlay.style.display = 'none';
                        sidebarOverlay.style.opacity = '0';
                    }
                } else {
                    // Mobile: sidebar cachée
                    sidebar.classList.remove('active');
                    sidebar.style.transform = 'translateX(-100%)';
                    mainContent.style.marginLeft = '0';
                    if (sidebarOverlay) {
                        sidebarOverlay.style.display = 'none';
                        sidebarOverlay.style.opacity = '0';
                    }
                }
            }

            // Initialiser au chargement
            initSidebar();

            // Réinitialiser lors du redimensionnement
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (!sidebar.classList.contains('active')) {
                        initSidebar();
                    }
                }, 250);
            });

            // Toggle du sidebar
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    console.log('🔘 Sidebar toggle cliqué');
                    const isActive = sidebar.classList.toggle('active');
                    
                    if (window.innerWidth >= 1024) {
                        // Desktop: toggle avec animation
                        if (isActive) {
                            sidebar.style.transform = 'translateX(-100%)';
                            mainContent.style.marginLeft = '0';
                        } else {
                            sidebar.style.transform = 'translateX(0)';
                            mainContent.style.marginLeft = '288px';
                        }
                    } else {
                        // Mobile: toggle avec overlay
                        if (isActive) {
                            sidebar.style.transform = 'translateX(0)';
                            if (sidebarOverlay) {
                                sidebarOverlay.style.display = 'block';
                                setTimeout(() => {
                                    sidebarOverlay.style.opacity = '1';
                                }, 10);
                            }
                        } else {
                            sidebar.style.transform = 'translateX(-100%)';
                            if (sidebarOverlay) {
                                sidebarOverlay.style.opacity = '0';
                                setTimeout(() => {
                                    sidebarOverlay.style.display = 'none';
                                }, 300);
                            }
                        }
                    }
                });
            } else {
                console.error('❌ Bouton sidebar-toggle non trouvé');
            }

            // Fermer le sidebar sur clic overlay (mobile uniquement)
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    console.log('🔘 Overlay cliqué');
                    sidebar.classList.remove('active');
                    sidebar.style.transform = 'translateX(-100%)';
                    sidebarOverlay.style.opacity = '0';
                    setTimeout(() => {
                        sidebarOverlay.style.display = 'none';
                    }, 300);
                });
            }

            // Fermer le sidebar en cliquant sur un lien (mobile uniquement)
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        sidebar.classList.remove('active');
                        sidebar.style.transform = 'translateX(-100%)';
                        if (sidebarOverlay) {
                            sidebarOverlay.style.opacity = '0';
                            setTimeout(() => {
                                sidebarOverlay.style.display = 'none';
                            }, 300);
                        }
                    }
                });
            });

            // Dropdowns
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            const notificationsDropdownMenu = document.getElementById('notifications-dropdown');
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = document.getElementById('user-dropdown');

            console.log('🔍 Éléments dropdowns:', {
                notificationsDropdown: !!notificationsDropdown,
                notificationsDropdownMenu: !!notificationsDropdownMenu,
                userDropdown: !!userDropdown,
                userDropdownMenu: !!userDropdownMenu
            });

            // Toggle notifications dropdown
            if (notificationsDropdown && notificationsDropdownMenu) {
                notificationsDropdown.addEventListener('click', function(e) {
                    console.log('🔔 Dropdown notifications cliqué');
                    e.stopPropagation();
                    notificationsDropdownMenu.classList.toggle('hidden');
                    userDropdownMenu.classList.add('hidden');
                });
            } else {
                console.error('❌ Dropdown notifications non trouvé');
            }

            // Toggle user dropdown
            if (userDropdown && userDropdownMenu) {
                userDropdown.addEventListener('click', function(e) {
                    console.log('👤 Dropdown profil cliqué');
                    e.stopPropagation();
                    userDropdownMenu.classList.toggle('hidden');
                    notificationsDropdownMenu.classList.add('hidden');
                });
            } else {
                console.error('❌ Dropdown profil non trouvé');
            }

            // Fermer les dropdowns en cliquant ailleurs
            document.addEventListener('click', function() {
                if (notificationsDropdownMenu) notificationsDropdownMenu.classList.add('hidden');
                if (userDropdownMenu) userDropdownMenu.classList.add('hidden');
            });
        });

        // Gestion des notifications
        function fetchNotifications() {
            if (typeof $ === 'undefined') {
                console.error('❌ jQuery non chargé');
                return;
            }

            // Vérifier si on a un token CSRF (indicateur d'authentification)
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            if (!csrfToken) {
                console.warn('⚠️ Pas de token CSRF - utilisateur possiblement non authentifié');
                return;
            }

            $.get('/admin/notifications', function(data) {
                const badge = document.getElementById('notification-badge');
                
                // Mise à jour du badge
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }

                // Mise à jour du conteneur de notifications
                let notificationsHtml = '';
                if (data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        notificationsHtml += `
                            <a href="${notification.link}" class="block px-4 py-3 hover:bg-gray-50 dark:bg-gray-900 ${!notification.read_at ? 'bg-blue-50' : ''}">
                                <div class="flex items-center">
                                    <i class="fas ${notification.icon} mr-3 text-gray-400"></i>
                                    <div class="flex-1">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">${notification.created_at}</div>
                                        <div class="text-sm text-gray-900 dark:text-white">${notification.message}</div>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                } else {
                    notificationsHtml = '<div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Aucune notification</div>';
                }
                
                const container = document.getElementById('notifications-container');
                if (container) {
                    container.innerHTML = notificationsHtml;
                }
            }).fail(function(xhr, status, error) {
                // En cas d'erreur, masquer le badge et logger l'erreur
                console.warn('⚠️ Erreur lors du chargement des notifications:', status, error);
                const badge = document.getElementById('notification-badge');
                if (badge) badge.classList.add('hidden');
                
                // Si 401/403, ne pas retry automatiquement (problème d'auth)
                if (xhr.status === 401 || xhr.status === 403) {
                    console.warn('🚫 Problème d\'authentification pour les notifications');
                }
            });
        }

        // Rafraîchir les notifications toutes les 30 secondes
        if (typeof $ !== 'undefined') {
            fetchNotifications();
            setInterval(fetchNotifications, 30000);

            // Protection CSRF pour les requêtes AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }
    </script>

    <!-- Network Speed Adapter (doit être chargé en premier) -->
    <script src="<?php echo e(asset('js/network-adapter.js')); ?>"></script>

    <!-- Network Speed Adapter (doit être chargé en premier) -->
    <script src="<?php echo e(asset('js/network-adapter.js')); ?>"></script>

    <!-- Admin Utils JavaScript -->
    <script src="<?php echo e(asset('js/admin-utils.js')); ?>"></script>

    <!-- Lazy Loading & PWA Scripts -->
    <script src="<?php echo e(asset('js/content-visibility.js')); ?>"></script>
    <script src="<?php echo e(asset('js/page-skeleton.js')); ?>"></script>
    <script src="<?php echo e(asset('js/admin-skeleton-config.js')); ?>"></script>
    <script src="<?php echo e(asset('js/navigation-skeleton.js')); ?>"></script>
    <script src="<?php echo e(asset('js/lazy-loading.js')); ?>" defer></script>

    <!-- Performance Monitor (Development only) -->
    <?php if(config('app.env') === 'local' || config('app.debug')): ?>
    <script src="<?php echo e(asset('js/performance-monitor.js')); ?>"></script>
    <?php endif; ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/layouts/admin.blade.php ENDPATH**/ ?>