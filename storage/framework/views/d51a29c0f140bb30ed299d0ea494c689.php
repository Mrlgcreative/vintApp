<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Vintapp - La marketplace de confiance pour acheter et vendre des articles d\'occasion de qualité'); ?>">
        <meta name="keywords" content="<?php echo $__env->yieldContent('meta_keywords', 'vintapp, marketplace, occasion, vente, achat, articles, vêtements, électronique'); ?>">

        <title><?php echo $__env->yieldContent('title', '<?php echo e($appName ?? "Vintapp"); ?>'); ?></title>
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset($appFavicon ?? '/favicon.ico')); ?>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">

        <!-- Vinted Violet CSS -->
        <link href="<?php echo e(asset('css/vinted-violet.css')); ?>" rel="stylesheet">

        <!-- Scripts Vite (Tailwind CSS) - Chargé APRÈS Bootstrap pour override -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

        <!-- Custom Styles -->
        <?php echo $__env->yieldPushContent('styles'); ?>
        
        <script>
        window.userTheme = "<?php echo e(addslashes(Auth::user()?->theme_preference ?? '')); ?>";
        window.isAuthenticated = <?php echo e(Auth::check() ? 'true' : 'false'); ?>;
        </script>
    </head>
    <body class="font-sans antialiased bg-white">
        <!-- Barre de profil moderne Tailwind - visible sur toutes les pages -->
        <?php if(auth()->guard()->check()): ?>
        <div class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
            <!-- Profil + Nom + Notifications + Panier -->
            <div class="max-w-7xl mx-auto px-4 py-3">
                <div class="flex items-center justify-between">
                    <!-- Profil + Nom (gauche) -->
                    <div class="flex items-center gap-3">
                        <a href="<?php echo e(route('profile.index')); ?>" class="flex items-center gap-2 hover:opacity-80 transition-opacity no-underline" style="text-decoration: none;">
                            <?php if(Auth::user()->avatar): ?>
                                <?php
                                    // Déterminer si c'est une URL complète ou un chemin local
                                    $avatarUrl = filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL) 
                                        ? Auth::user()->avatar 
                                        : asset('storage/' . Auth::user()->avatar);
                                ?>
                                <img src="<?php echo e($avatarUrl); ?>" 
                                     alt="<?php echo e(Auth::user()->name); ?>" 
                                     class="w-10 h-10 rounded-full object-cover border-2 border-purple-200 ring-2 ring-purple-100"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-600 to-cyan-400 items-center justify-center text-white font-bold text-sm shadow-md" style="display: none;">
                                    <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                                </div>
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-600 to-cyan-400 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                                </div>
                            <?php endif; ?>
                            <span class="font-semibold text-gray-800 text-sm sm:text-base"><?php echo e(Auth::user()->name); ?></span>
                        </a>
                    </div>
                    
                    <!-- Notifications + Panier (droite) -->
                    <div class="flex items-center gap-2">
                        <!-- Notifications -->
                        <button class="relative p-2.5 hover:bg-gray-100 rounded-full transition-colors" onclick="toggleNotifications()">
                            <i class="fas fa-bell text-gray-700 text-lg"></i>
                            <?php
                                $unreadNotifications = App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->count();
                            ?>
                            <?php if($unreadNotifications > 0): ?>
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                            <?php endif; ?>
                        </button>
                        
                        <!-- Panier -->
                        <a href="<?php echo e(route('cart.index')); ?>" class="relative p-2.5 hover:bg-gray-100 rounded-full transition-colors">
                            <i class="fas fa-shopping-cart text-gray-700 text-lg"></i>
                            <?php if(session('cart') && count(session('cart')) > 0): ?>
                                <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-purple-600 text-white text-xs rounded-full flex items-center justify-center font-bold shadow-sm">
                                    <?php echo e(count(session('cart'))); ?>

                                </span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Barre de recherche intégrée sous le profil -->
            
        </div>
        <?php endif; ?>
        
        <!-- Navigation Bootstrap (masquée sur mobile) -->
        <style>
            /* Force Tailwind styles for profile bar */
            .bg-white { background-color: #ffffff !important; }
            .border-b { border-bottom-width: 1px !important; }
            .border-gray-200 { border-color: #e5e7eb !important; }
            .sticky { position: sticky !important; }
            .top-0 { top: 0 !important; }
            .z-50 { z-index: 50 !important; }
            .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important; }
            .max-w-7xl { max-width: 80rem !important; }
            .mx-auto { margin-left: auto !important; margin-right: auto !important; }
            .px-4 { padding-left: 1rem !important; padding-right: 1rem !important; }
            .py-3 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
            .flex { display: flex !important; }
            .items-center { align-items: center !important; }
            .justify-center { justify-content: center !important; }
            .justify-between { justify-content: space-between !important; }
            .gap-3 { gap: 0.75rem !important; }
            .gap-2 { gap: 0.5rem !important; }
            .w-10 { width: 2.5rem !important; }
            .h-10 { height: 2.5rem !important; }
            .w-5 { width: 1.25rem !important; }
            .h-5 { height: 1.25rem !important; }
            .w-2 { width: 0.5rem !important; }
            .h-2 { height: 0.5rem !important; }
            .w-80 { width: 20rem !important; }
            .rounded-full { border-radius: 9999px !important; }
            .rounded-lg { border-radius: 0.5rem !important; }
            .object-cover { object-fit: cover !important; }
            .border-2 { border-width: 2px !important; }
            .border-purple-200 { border-color: #e9d5ff !important; }
            .ring-2 { box-shadow: 0 0 0 2px rgba(243, 232, 255, 1) !important; }
            .ring-purple-100 { --tw-ring-color: #f3e8ff !important; }
            .bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)) !important; }
            .from-purple-600 { --tw-gradient-from: #9333ea !important; --tw-gradient-to: rgba(147, 51, 234, 0) !important; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to) !important; }
            .to-cyan-400 { --tw-gradient-to: #22d3ee !important; }
            .text-white { color: #ffffff !important; }
            .text-gray-800 { color: #1f2937 !important; }
            .text-gray-700 { color: #374151 !important; }
            .text-gray-500 { color: #6b7280 !important; }
            .text-gray-400 { color: #9ca3af !important; }
            .text-gray-600 { color: #4b5563 !important; }
            .text-purple-600 { color: #9333ea !important; }
            .font-bold { font-weight: 700 !important; }
            .font-semibold { font-weight: 600 !important; }
            .text-sm { font-size: 0.875rem !important; line-height: 1.25rem !important; }
            .text-lg { font-size: 1.125rem !important; line-height: 1.75rem !important; }
            .text-xs { font-size: 0.75rem !important; line-height: 1rem !important; }
            .text-base { font-size: 1rem !important; line-height: 1.5rem !important; }
            .relative { position: relative !important; }
            .absolute { position: absolute !important; }
            .fixed { position: fixed !important; }
            .inset-0 { top: 0 !important; right: 0 !important; bottom: 0 !important; left: 0 !important; }
            .p-2\.5 { padding: 0.625rem !important; }
            .p-4 { padding: 1rem !important; }
            .-top-0\.5 { top: -0.125rem !important; }
            .-right-0\.5 { right: -0.125rem !important; }
            .top-1\.5 { top: 0.375rem !important; }
            .right-1\.5 { right: 0.375rem !important; }
            .top-16 { top: 4rem !important; }
            .right-4 { right: 1rem !important; }
            .bg-red-500 { background-color: #ef4444 !important; }
            .bg-purple-600 { background-color: #9333ea !important; }
            .bg-black { background-color: #000000 !important; }
            .bg-opacity-50 { --tw-bg-opacity: 0.5 !important; }
            .hover\:bg-gray-100:hover { background-color: #f3f4f6 !important; }
            .hover\:bg-gray-50:hover { background-color: #f9fafb !important; }
            .hover\:bg-purple-700:hover { background-color: #7e22ce !important; }
            .hover\:opacity-80:hover { opacity: 0.8 !important; }
            .hover\:text-gray-600:hover { color: #4b5563 !important; }
            .transition-colors { transition-property: color, background-color, border-color, text-decoration-color, fill, stroke !important; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1) !important; transition-duration: 150ms !important; }
            .transition-opacity { transition-property: opacity !important; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1) !important; transition-duration: 150ms !important; }
            .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite !important; }
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: .5; }
            }
            .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important; }
            .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important; }
            .max-h-96 { max-height: 24rem !important; }
            .overflow-y-auto { overflow-y: auto !important; }
            .space-y-4 > * + * { margin-top: 1rem !important; }
            .block { display: block !important; }
            .mb-2 { margin-bottom: 0.5rem !important; }
            .mb-3 { margin-bottom: 0.75rem !important; }
            .mt-2 { margin-top: 0.5rem !important; }
            .w-full { width: 100% !important; }
            .max-w-md { max-width: 28rem !important; }
            .border { border-width: 1px !important; }
            .border-gray-300 { border-color: #d1d5db !important; }
            .px-3 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
            .py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
            .focus\:outline-none:focus { outline: 2px solid transparent !important; outline-offset: 2px !important; }
            .focus\:ring-2:focus { --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color) !important; --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color) !important; box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000) !important; }
            .focus\:ring-purple-500:focus { --tw-ring-color: #a855f7 !important; }
            .flex-1 { flex: 1 1 0% !important; }
            .text-center { text-align: center !important; }
            
            /* Remove text decoration from links */
            .no-underline { text-decoration: none !important; }
            a.no-underline:hover { text-decoration: none !important; }
            
            /* Responsive: classes sm: pour écrans >= 640px */
            @media (min-width: 640px) {
                .sm\:text-base { font-size: 1rem !important; line-height: 1.5rem !important; }
            }
            
            /* Responsive: classes md: pour écrans >= 768px */
            @media (min-width: 768px) {
                .md\:hidden { display: none !important; }
            }
            
            /* Responsive: max-w avec calc pour mobile */
            .max-w-\[calc\(100vw-2rem\)\] { max-width: calc(100vw - 2rem) !important; }
            
            @media (max-width: 767.98px) {
                .top-navbar {
                    display: none !important;
                }
                /* Ajustements mobile pour la barre de profil */
                .px-4 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
                .py-3 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
                .gap-3 { gap: 0.5rem !important; }
                .gap-2 { gap: 0.375rem !important; }
                .w-10 { width: 2rem !important; }
                .h-10 { height: 2rem !important; }
                .text-sm { font-size: 0.75rem !important; }
                .p-2\.5 { padding: 0.5rem !important; }
            }
        </style>
        
        <!-- Navigation principale -->
        <nav class="navbar navbar-expand-lg navbar-dark top-navbar" style="background-color:rgb(79, 0, 206);">
            <div class="container">
                <a class="navbar-brand fw-bold" href="<?php echo e(url('/')); ?>">
                    <?php if (isset($component)) { $__componentOriginalac37604bae5cded3771d6931140b8398 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalac37604bae5cded3771d6931140b8398 = $attributes; } ?>
<?php $component = App\View\Components\AppBrand::resolve(['showLogo' => true,'showName' => true,'logoHeight' => '32px','logoWidth' => '100px','nameSize' => '1.5rem','nameClass' => 'text-white','class' => 'd-flex align-items-center'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- Navigation gauche -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('dashboard')); ?>">
                                <i class="fas fa-tachometer-alt me-1"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('items.index') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('items.index')); ?>">
                                <i class="fas fa-box me-1"></i>
                                Articles
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('categories.index')); ?>">
                                <i class="fas fa-layer-group me-1"></i>
                                Catégories
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('brands.*') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('brands.index')); ?>">
                                <i class="fas fa-tags me-1"></i>
                                Marques
                            </a>
                        </li>
                        
                        <?php if(auth()->guard()->check()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('items.my-items') ? 'active' : ''); ?>" 
                                   href="<?php echo e(route('items.my-items')); ?>">
                                    <i class="fas fa-list me-1"></i>
                                    Articles
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('orders.index') ? 'active' : ''); ?>" 
                                   href="<?php echo e(route('orders.index')); ?>">
                                    <i class="fas fa-shopping-cart me-1"></i>
                                    Commandes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('orders.my-sales') ? 'active' : ''); ?>" 
                                   href="<?php echo e(route('orders.my-sales')); ?>">
                                    <i class="fas fa-store me-1"></i>
                                    Ventes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('wallet.*') ? 'active' : ''); ?>" 
                                   href="<?php echo e(route('wallet.index')); ?>">
                                    <i class="fas fa-wallet me-1"></i>
                                    Wallet
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('messages.*') ? 'active' : ''); ?>" 
                                   href="<?php echo e(route('messages.index')); ?>">
                                    <i class="fas fa-comments me-1"></i>
                                    Messages
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <!-- Barre de recherche -->
                    <form class="d-flex me-3" method="GET" action="<?php echo e(route('items.search')); ?>">
                        <div class="input-group" style="min-width: 300px;">
                            <input class="form-control focus-ring" 
                                   type="search" 
                                   name="q" 
                                   placeholder="Rechercher un article..." 
                                   value="<?php echo e(request('q')); ?>">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Navigation droite -->
                    <ul class="navbar-nav">
                        <?php if(auth()->guard()->guest()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('login') ? 'active' : ''); ?>" 
                                   href="<?php echo e(route('login')); ?>">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    Connexion
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('register') ? 'active' : ''); ?>" 
                                   href="<?php echo e(route('register')); ?>">
                                    <i class="fas fa-user-plus me-1"></i>
                                    Inscription
                                </a>
                            </li>
                        <?php else: ?>
                            <!-- Notifications -->
                            <li class="nav-item dropdown me-2">
                                <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" id="notifications-dropdown">
                                    <i class="fas fa-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notifications-badge" style="display: none;">
                                        0
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;" id="notifications-list">
                                    <li><h6 class="dropdown-header">Notifications</h6></li>
                                    <li><div class="dropdown-item text-center text-muted">Aucune notification</div></li>
                                </ul>
                            </li>

                            <!-- Menu utilisateur -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" 
                                   href="#" 
                                   role="button" 
                                   data-bs-toggle="dropdown">
                                    <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center me-2" 
                                         style="width: 32px; height: 32px; font-weight: bold; font-size: 1.1rem;">
                                        <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                                    </div>
                                    <?php echo e(Auth::user()->name); ?>

                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="px-3 py-2 text-muted small">Profil & Paramètres</li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>">
                                            <i class="fas fa-user-cog me-2"></i>
                                            Mon profil
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('items.personalization')); ?>">
                                            <i class="fas fa-cogs me-2"></i>
                                            Personnalisation
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="#" id="theme-toggle">
                                            <i class="fas fa-adjust me-2"></i>
                                            Thème : <span class="ms-2" id="theme-label">Auto</span>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="px-3 py-2 text-muted small">Navigation</li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('categories.index')); ?>">
                                            <i class="fas fa-layer-group me-2"></i>
                                            Catégories
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('brands.index')); ?>">
                                            <i class="fas fa-tags me-2"></i>
                                            Marques
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="px-3 py-2 text-muted small">Ventes & Achats</li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('items.create')); ?>">
                                            <i class="fas fa-plus me-2"></i>
                                            Vendre un article
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('items.my-items')); ?>">
                                            <i class="fas fa-box me-2"></i>
                                            Mes articles
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('orders.index')); ?>">
                                            <i class="fas fa-shopping-cart me-2"></i>
                                            Mes commandes
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('orders.my-sales')); ?>">
                                            <i class="fas fa-store me-2"></i>
                                            Mes ventes
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('wallet.index')); ?>">
                                            <i class="fas fa-wallet me-2"></i>
                                            Wallet
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fas fa-heart me-2"></i>
                                            Mes favoris
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="px-3 py-2 text-muted small">Messagerie</li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('messages.index')); ?>">
                                            <i class="fas fa-envelope me-2"></i>
                                            Messages
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>
                                                Déconnexion
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Breadcrumb -->
        <?php if(!request()->routeIs('welcome')): ?>
            <nav aria-label="breadcrumb" class="bg-light py-2">
                <div class="container">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(url('/')); ?>" class="text-decoration-none">
                                <i class="fas fa-home me-1"></i>
                                Accueil
                            </a>
                        </li>
                        <?php if(request()->routeIs('dashboard')): ?>
                            <li class="breadcrumb-item active">Dashboard</li>
                        <?php elseif(request()->routeIs('categories.*')): ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo e(route('categories.index')); ?>" class="text-decoration-none">Catégories</a>
                            </li>
                            <?php if(request()->routeIs('categories.show')): ?>
                                <li class="breadcrumb-item active"><?php echo e($category->name ?? 'Détails'); ?></li>
                            <?php elseif(request()->routeIs('categories.create')): ?>
                                <li class="breadcrumb-item active">Créer une catégorie</li>
                            <?php elseif(request()->routeIs('categories.edit')): ?>
                                <li class="breadcrumb-item active">Modifier une catégorie</li>
                            <?php endif; ?>
                        <?php elseif(request()->routeIs('brands.*')): ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo e(route('brands.index')); ?>" class="text-decoration-none">Marques</a>
                            </li>
                            <?php if(request()->routeIs('brands.show')): ?>
                                <li class="breadcrumb-item active"><?php echo e($brand->name ?? 'Détails'); ?></li>
                            <?php elseif(request()->routeIs('brands.create')): ?>
                                <li class="breadcrumb-item active">Créer une marque</li>
                            <?php elseif(request()->routeIs('brands.edit')): ?>
                                <li class="breadcrumb-item active">Modifier une marque</li>
                            <?php endif; ?>
                        <?php elseif(request()->routeIs('items.*')): ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo e(route('items.index')); ?>" class="text-decoration-none">Articles</a>
                            </li>
                            <?php if(request()->routeIs('items.show')): ?>
                                <li class="breadcrumb-item active"><?php echo e($item->name ?? 'Détails'); ?></li>
                            <?php elseif(request()->routeIs('items.create')): ?>
                                <li class="breadcrumb-item active">Créer un article</li>
                            <?php elseif(request()->routeIs('items.edit')): ?>
                                <li class="breadcrumb-item active">Modifier un article</li>
                            <?php elseif(request()->routeIs('items.my-items')): ?>
                                <li class="breadcrumb-item active">Mes articles</li>
                            <?php elseif(request()->routeIs('items.search')): ?>
                                <li class="breadcrumb-item active">Recherche</li>
                            <?php endif; ?>
                        <?php elseif(request()->routeIs('profile.*')): ?>
                            <li class="breadcrumb-item active">Profil</li>
                        <?php endif; ?>
                    </ol>
                </div>
            </nav>
        <?php endif; ?>

        <!-- Contenu principal -->
        <main class="min-vh-100">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <!-- Footer -->
        <?php if(!request()->routeIs('messages.*')): ?>
        <footer class="bg-dark text-light py-4 mt-5">
            <div class="container">
                <div class="row footer-row-custom">
                    <div class="col-md-4 col-6 mb-4">
                        <h5>
                            <?php if (isset($component)) { $__componentOriginalac37604bae5cded3771d6931140b8398 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalac37604bae5cded3771d6931140b8398 = $attributes; } ?>
<?php $component = App\View\Components\AppBrand::resolve(['showLogo' => true,'showName' => true,'logoHeight' => '24px','logoWidth' => '80px','nameSize' => '1.25rem','nameClass' => 'text-white'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                        </h5>
                        <p class="text-muted">
                            <?php echo e($appDescription ?? 'La marketplace de confiance pour acheter et vendre des articles d\'occasion.'); ?>

                        </p>
                    </div>
                    <div class="col-md-2 col-6 mb-4">
                        <h6>Navigation</h6>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo e(route('items.index')); ?>" class="text-muted text-decoration-none">Articles</a></li>
                            <li><a href="<?php echo e(route('categories.index')); ?>" class="text-muted text-decoration-none">Catégories</a></li>
                            <li><a href="<?php echo e(route('brands.index')); ?>" class="text-muted text-decoration-none">Marques</a></li>
                            <li><a href="<?php echo e(route('items.search')); ?>" class="text-muted text-decoration-none">Recherche</a></li>
                            <?php if(auth()->guard()->check()): ?>
                                <li><a href="<?php echo e(route('items.my-items')); ?>" class="text-muted text-decoration-none">Mes articles</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col-md-2 col-6 mb-4">
                        <h6>Support</h6>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-muted text-decoration-none">Aide</a></li>
                            <li><a href="#" class="text-muted text-decoration-none">Contact</a></li>
                            <li><a href="#" class="text-muted text-decoration-none">FAQ</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2 col-6 mb-4">
                        <h6>Légal</h6>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-muted text-decoration-none">CGU</a></li>
                            <li><a href="#" class="text-muted text-decoration-none">Confidentialité</a></li>
                            <li><a href="#" class="text-muted text-decoration-none">Cookies</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2 col-6 mb-4">
                        <h6>Suivez-nous</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="text-muted text-decoration-none">
                                <i class="fab fa-facebook fa-lg"></i>
                            </a>
                            <a href="#" class="text-muted text-decoration-none">
                                <i class="fab fa-twitter fa-lg"></i>
                            </a>
                            <a href="#" class="text-muted text-decoration-none">
                                <i class="fab fa-instagram fa-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Newsletter Subscription -->
                <hr class="my-4">
                <div class="row justify-content-center">
                    <div class="col-md-6 text-center">
                        <h5 class="mb-3">📧 Restez informé !</h5>
                        <p class="text-muted">Inscrivez-vous à notre newsletter pour recevoir nos dernières offres et nouveautés.</p>
                        <form id="newsletterForm" class="d-flex gap-2 justify-content-center">
                            <?php echo csrf_field(); ?>
                            <input type="email" id="newsletterEmail" class="form-control" placeholder="Votre email" required style="max-width: 300px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>S'abonner
                            </button>
                        </form>
                        <div id="newsletterMessage" class="mt-2"></div>
                    </div>
                </div>
                
                <style>
                /* Footer responsive : 2 colonnes sur mobile */
                @media (max-width: 767.98px) {
                    .footer-row-custom > [class^="col-"],
                    .footer-row-custom > [class*=" col-"] {
                        flex: 0 0 50%;
                        max-width: 50%;
                        margin-bottom: 1.5rem;
                    }
                }
                </style>
                
                <hr class="my-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            © <?php echo e(date('Y')); ?> <?php echo e($appName ?? config('app.name', 'VintApp')); ?>. Tous droits réservés.
                        </small>
                    </div>
                </div>
            </div>
        </footer>
        <?php endif; ?>

        <!-- Barre de navigation mobile (bottom nav) -->
        <nav id="mobile-bottom-nav" class="d-md-none d-lg-none d-xl-none">
            <ul class="bottom-nav-list">
                <li>
                    <a href="<?php echo e(url('/')); ?>" class="bottom-nav-link <?php echo e(request()->is('/') ? 'active' : ''); ?>">
                        <i class="fas fa-home"></i>
                        <span>Accueil</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('items.create')); ?>" class="bottom-nav-link <?php echo e(request()->routeIs('items.create') ? 'active' : ''); ?>">
                        <i class="fas fa-plus-circle"></i>
                        <span>Vente</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('items.index')); ?>" class="bottom-nav-link <?php echo e(request()->routeIs('items.*') ? 'active' : ''); ?>">
                        <i class="fas fa-box"></i>
                        <span>Articles</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('wallet.index')); ?>" class="bottom-nav-link <?php echo e(request()->routeIs('wallet.*') ? 'active' : ''); ?>">
                        <i class="fas fa-wallet"></i>
                        <span>Wallet</span>
                    </a>
                </li>
                <li class="position-relative">
                    <a href="<?php echo e(route('settings.index')); ?>" class="bottom-nav-link <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Custom Scripts -->
        <?php echo $__env->yieldPushContent('scripts'); ?>

        <!-- Scripts personnalisés -->
        <script>
        // Fonction pour afficher les notifications (panneau Tailwind)
        function toggleNotifications() {
            const existingPanel = document.getElementById('notifications-panel');
            
            if (existingPanel) {
                existingPanel.remove();
                return;
            }
            
            const panel = document.createElement('div');
            panel.id = 'notifications-panel';
            panel.className = 'fixed top-16 right-4 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-lg shadow-2xl border border-gray-200 z-50 max-h-96 overflow-y-auto';
            panel.innerHTML = `
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Notifications</h3>
                    <button onclick="this.closest('#notifications-panel').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4" id="mobile-notifications-content">
                    <p class="text-gray-500 text-sm text-center">Chargement...</p>
                </div>
            `;
            
            document.body.appendChild(panel);
            
            // Charger les notifications
            loadMobileNotifications();
            
            // Fermer en cliquant à l'extérieur
            setTimeout(() => {
                document.addEventListener('click', function closePanel(e) {
                    if (!panel.contains(e.target) && !e.target.closest('[onclick*="toggleNotifications"]')) {
                        panel.remove();
                        document.removeEventListener('click', closePanel);
                    }
                });
            }, 100);
        }
        
        // Fonction pour afficher les filtres (modal Tailwind)
        function toggleFilters() {
            const existingModal = document.getElementById('filters-modal');
            
            if (existingModal) {
                existingModal.remove();
                return;
            }
            
            const modal = document.createElement('div');
            modal.id = 'filters-modal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
            modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-2xl w-full max-w-md">
                    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">Filtres de recherche</h3>
                        <button onclick="this.closest('#filters-modal').remove()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option>Toutes les catégories</option>
                                <option>Vêtements</option>
                                <option>Électronique</option>
                                <option>Maison</option>
                                <option>Sports</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prix</label>
                            <div class="flex gap-2">
                                <input type="number" placeholder="Min" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <input type="number" placeholder="Max" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">État</label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option>Tous les états</option>
                                <option>Neuf</option>
                                <option>Comme neuf</option>
                                <option>Très bon état</option>
                                <option>Bon état</option>
                            </select>
                        </div>
                    </div>
                    <div class="p-4 border-t border-gray-200 flex gap-2">
                        <button onclick="resetFilters()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Réinitialiser
                        </button>
                        <button onclick="applyFilters()" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Appliquer
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Fermer en cliquant sur le fond
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }
        
        function resetFilters() {
            document.getElementById('filters-modal').remove();
        }
        
        function applyFilters() {
            // Logique d'application des filtres
            document.getElementById('filters-modal').remove();
        }
        
        function loadMobileNotifications() {
            <?php if(auth()->guard()->check()): ?>
            const content = document.getElementById('mobile-notifications-content');
            if (!content) return;
            
            fetch('/notifications')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications.length === 0) {
                        content.innerHTML = '<p class="text-gray-500 text-sm text-center">Aucune notification</p>';
                        return;
                    }
                    
                    content.innerHTML = data.notifications.map(notification => `
                        <div class="p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer" 
                             onclick="markNotificationAsRead(${notification.id}, '${notification.data?.url || '#'}')">
                            <div class="flex items-start gap-3">
                                <i class="fas ${getNotificationIcon(notification.type)} text-purple-600 mt-1"></i>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800 text-sm">${notification.title}</div>
                                    <div class="text-gray-600 text-xs mt-1">${notification.message}</div>
                                    <div class="text-gray-400 text-xs mt-1">${formatDate(notification.created_at)}</div>
                                </div>
                                ${!notification.read_at ? '<div class="w-2 h-2 bg-red-500 rounded-full"></div>' : ''}
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    content.innerHTML = '<p class="text-red-500 text-sm text-center">Erreur de chargement</p>';
                });
            <?php else: ?>
            document.getElementById('mobile-notifications-content').innerHTML = '<p class="text-gray-500 text-sm text-center">Connectez-vous pour voir vos notifications</p>';
            <?php endif; ?>
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Application Vintapp chargée');
            
            // Gestion du thème
            applyTheme(getPreferredTheme());
            
            // Initialisation du service worker pour notifications push
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('✅ Service Worker enregistré:', registration);
                    })
                    .catch(error => {
                        console.error('❌ Erreur Service Worker:', error);
                    });
            }
            
            // Demander permission pour notifications
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission().then(permission => {
                    console.log('🔔 Permission notifications:', permission);
                });
            }
            
            // Gestion des notifications en temps réel
            <?php if(auth()->guard()->check()): ?>
            loadNotifications();
            setInterval(loadNotifications, 30000); // Toutes les 30 secondes
            <?php endif; ?>
        });
        
        <?php if(auth()->guard()->check()): ?>
        function loadNotifications() {
            fetch('/notifications')
                .then(response => response.json())
                .then(data => {
                    console.log('📋 Notifications chargées:', data);
                    updateNotificationBadge(data.unread_count);
                    updateNotificationsList(data.notifications);
                })
                .catch(error => {
                    console.error('❌ Erreur chargement notifications:', error);
                });
        }
        
        function updateNotificationBadge(count) {
            const badge = document.getElementById('notifications-badge');
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
            
            // Badge mobile (Tailwind)
            const mobileBadge = document.querySelector('.animate-pulse');
            if (mobileBadge) {
                mobileBadge.style.display = count > 0 ? 'block' : 'none';
            }
        }
        
        function updateNotificationsList(notifications) {
            const list = document.getElementById('notifications-list');
            if (!list) return;
            
            list.innerHTML = '<li><h6 class="dropdown-header">Notifications</h6></li>';
            
            if (notifications.length === 0) {
                list.innerHTML += '<li><div class="dropdown-item text-center text-muted">Aucune notification</div></li>';
                return;
            }
            
            notifications.forEach(notification => {
                const item = document.createElement('li');
                item.innerHTML = `
                    <a class="dropdown-item ${!notification.read_at ? 'fw-bold' : ''}" 
                       href="#" 
                       onclick="markNotificationAsRead(${notification.id}, '${notification.data?.url || '#'}')">
                        <div class="d-flex align-items-start">
                            <i class="fas ${getNotificationIcon(notification.type)} me-2 mt-1 text-primary"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold">${notification.title}</div>
                                <small class="text-muted">${notification.message}</small>
                                <br><small class="text-muted">${formatDate(notification.created_at)}</small>
                            </div>
                        </div>
                    </a>
                `;
                list.appendChild(item);
            });
        }
        
        function getNotificationIcon(type) {
            const icons = {
                'new_message': 'fa-comment',
                'new_order': 'fa-shopping-cart',
                'discount_applied': 'fa-percentage',
                'item_favorited': 'fa-heart'
            };
            return icons[type] || 'fa-bell';
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'À l\'instant';
            if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
            if (diff < 86400000) return Math.floor(diff / 3600000) + ' h';
            return Math.floor(diff / 86400000) + ' j';
        }
        
        function markNotificationAsRead(notificationId, url) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                loadNotifications(); // Recharger les notifications
                if (url && url !== '#') {
                    window.location.href = url;
                }
            });
        }
        <?php endif; ?>
        
        // Gestion du thème
        function applyTheme(theme) {
            if (theme === 'auto') {
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', systemTheme);
            } else {
                document.documentElement.setAttribute('data-theme', theme);
            }
            document.body.classList.remove('theme-light', 'theme-dark', 'theme-auto');
            document.body.classList.add('theme-' + theme);
            const label = document.getElementById('theme-label');
            if (label) {
                label.textContent = theme.charAt(0).toUpperCase() + theme.slice(1);
            }
        }
        
        function getPreferredTheme() {
            const localTheme = localStorage.getItem('theme');
            if (localTheme) return localTheme;
            if (window.userTheme && window.userTheme !== '') return window.userTheme;
            return 'auto';
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    let current = getPreferredTheme();
                    let next = current === 'auto' ? 'light' : current === 'light' ? 'dark' : 'auto';
                    applyTheme(next);
                    localStorage.setItem('theme', next);
                });
            }
        });
        </script>

        <style>
        /* Styles personnalisés */
        .top-navbar {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 767.98px) {
            .top-navbar {
                display: none !important;
            }
            
            main.min-vh-100 {
                padding-bottom: 80px;
            }
        }
        
        @media (min-width: 768px) {
            #mobile-bottom-nav {
                display: none !important;
            }
        }
        
        /* Bottom Navigation Mobile */
        #mobile-bottom-nav {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1050;
            background: #fff;
            border-top: 1px solid #e5e5e5;
            box-shadow: 0 -2px 16px rgba(79,0,206,0.07);
            padding: 0;
            height: 64px;
            display: flex;
            align-items: center;
        }
        
        .bottom-nav-list {
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: 100%;
            margin: 0;
            padding: 0;
            list-style: none;
            height: 100%;
        }
        
        .bottom-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #888;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            height: 100%;
            width: 64px;
            transition: color 0.2s;
        }
        
        .bottom-nav-link i {
            font-size: 22px;
            margin-bottom: 2px;
        }
        
        .bottom-nav-link.active,
        .bottom-nav-link:active {
            color: rgb(79, 0, 206);
        }
        
        .bottom-nav-link span {
            font-size: 11px;
            margin-top: 2px;
        }
        
        /* Navbar styling */
        .nav-link {
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover {
            color: rgba(255, 255, 255, 0.9) !important;
            transform: translateY(-1px);
        }
        
        .nav-link.active {
            font-weight: 600;
            position: relative;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: white;
            border-radius: 1px;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border-radius: 12px;
        }
        
        .dropdown-item {
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f3e8ff;
            color: rgb(79, 0, 206);
        }
        </style>
    </body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/app.blade.php ENDPATH**/ ?>