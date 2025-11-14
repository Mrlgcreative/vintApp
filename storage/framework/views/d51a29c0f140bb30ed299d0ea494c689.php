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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">

    <!-- Vinted Violet CSS -->
   

    <!-- Tailwind CSS -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <!-- Custom Styles -->
    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <script>
        window.userTheme = "<?php echo e(addslashes(Auth::user()?->theme_preference ?? '')); ?>";
        window.isAuthenticated = <?php echo e(Auth::check() ? 'true' : 'false'); ?>;
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen">
    
    <!-- Header avec barre de profil -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <!-- Barre de profil supérieure -->
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <?php if(auth()->guard()->check()): ?>
                    <!-- Profil utilisateur connecté -->
                    <div class="flex items-center space-x-3">
                        <a href="<?php echo e(route('profile.index')); ?>" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                            <?php if(Auth::user()->avatar): ?>
                                <?php
                                    $avatarUrl = filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL) 
                                        ? Auth::user()->avatar 
                                        : asset('storage/' . Auth::user()->avatar);
                                ?>
                                <img src="<?php echo e($avatarUrl); ?>" 
                                     alt="<?php echo e(Auth::user()->name); ?>" 
                                     class="w-10 h-10 rounded-full object-cover border-2 border-primary-200"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary-600 to-accent-400 items-center justify-center text-white font-bold text-sm hidden">
                                    <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                                </div>
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary-600 to-accent-400 flex items-center justify-center text-white font-bold text-sm">
                                    <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                                </div>
                            <?php endif; ?>
                            <span class="font-semibold text-gray-800 text-sm sm:text-base"><?php echo e(Auth::user()->name); ?></span>
                        </a>
                    </div>
                    
                    <!-- Actions utilisateur connecté -->
                    <div class="flex items-center space-x-2">
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
                                <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-primary-600 text-white text-xs rounded-full flex items-center justify-center font-bold">
                                    <?php echo e(count(session('cart'))); ?>

                                </span>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Logo pour utilisateur non connecté -->
                    <div class="flex items-center space-x-3">
                        <a href="<?php echo e(url('/')); ?>" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary-600 to-accent-400 flex items-center justify-center text-white font-bold text-sm">
                                <i class="fas fa-home"></i>
                            </div>
                            <span class="font-semibold text-gray-800 text-sm sm:text-base"><?php echo e(config('app.name', 'VintApp')); ?></span>
                        </a>
                    </div>
                    
                    <!-- Boutons de connexion -->
                    <!-- <div class="flex items-center space-x-2">
                        <a href="<?php echo e(route('login')); ?>" class="px-4 py-2 text-primary-600 hover:bg-primary-50 rounded-full font-semibold text-sm transition-colors border border-primary-200 hover:border-primary-300">
                            <i class="fas fa-sign-in-alt mr-1"></i>
                            Se connecter
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 bg-primary-600 text-white hover:bg-primary-700 rounded-full font-semibold text-sm transition-colors">
                            <i class="fas fa-user-plus mr-1"></i>
                            S'inscrire
                        </a>
                    </div> -->
                <?php endif; ?>
            </div>
        </div>

        <!-- Navigation principale (desktop seulement) -->
        <nav class="bg-primary hidden lg:block">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo et navigation gauche -->
                    <div class="flex items-center space-x-8">
                        <!-- Logo -->
                        <a href="<?php echo e(url('/')); ?>" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity">
                            <?php if (isset($component)) { $__componentOriginalac37604bae5cded3771d6931140b8398 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalac37604bae5cded3771d6931140b8398 = $attributes; } ?>
<?php $component = App\View\Components\AppBrand::resolve(['showLogo' => true,'showName' => true,'logoHeight' => '32px','logoWidth' => '100px','nameSize' => '1.5rem','nameClass' => 'text-white','class' => 'flex items-center'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                        
                        <!-- Navigation links -->
                        <div class="flex items-center space-x-6">
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo e(request()->routeIs('dashboard') ? 'bg-primary-700' : ''); ?>">
                                <i class="fas fa-tachometer-alt mr-1"></i>
                                Dashboard
                            </a>
                            <a href="<?php echo e(route('items.index')); ?>" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo e(request()->routeIs('items.index') ? 'bg-primary-700' : ''); ?>">
                                <i class="fas fa-box mr-1"></i>
                                Articles
                            </a>
                            <a href="<?php echo e(route('categories.index')); ?>" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo e(request()->routeIs('categories.*') ? 'bg-primary-700' : ''); ?>">
                                <i class="fas fa-layer-group mr-1"></i>
                                Catégories
                            </a>
                            <a href="<?php echo e(route('brands.index')); ?>" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo e(request()->routeIs('brands.*') ? 'bg-primary-700' : ''); ?>">
                                <i class="fas fa-tags mr-1"></i>
                                Marques
                            </a>
                            
                            <?php if(auth()->guard()->check()): ?>
                                <a href="<?php echo e(route('items.my-items')); ?>" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo e(request()->routeIs('items.my-items') ? 'bg-primary-700' : ''); ?>">
                                    <i class="fas fa-list mr-1"></i>
                                    Mes Articles
                                </a>
                                <a href="<?php echo e(route('orders.index')); ?>" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo e(request()->routeIs('orders.index') ? 'bg-primary-700' : ''); ?>">
                                    <i class="fas fa-shopping-cart mr-1"></i>
                                    Commandes
                                </a>
                                <a href="<?php echo e(route('wallet.index')); ?>" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo e(request()->routeIs('wallet.*') ? 'bg-primary-700' : ''); ?>">
                                    <i class="fas fa-wallet mr-1"></i>
                                    Wallet
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?php echo e(route('help.index')); ?>" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo e(request()->routeIs('help.*') ? 'bg-primary-700' : ''); ?>">
                                <i class="fas fa-question-circle mr-1"></i>
                                Aide
                            </a>
                        </div>
                    </div>
                    
                    <!-- Barre de recherche et menu utilisateur -->
                    <div class="flex items-center space-x-4">
                        <!-- Barre de recherche -->
                        <form class="flex items-center" method="GET" action="<?php echo e(route('items.search')); ?>">
                            <div class="relative">
                                <input type="search" 
                                       name="q" 
                                       placeholder="Rechercher un article..." 
                                       value="<?php echo e(request('q')); ?>"
                                       class="w-80 px-4 py-2 pl-10 pr-4 text-sm bg-white border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                                <button type="submit" class="absolute right-1 top-1 bottom-1 px-3 bg-primary-500 text-white rounded-full hover:bg-primary-600 transition-colors">
                                    <i class="fas fa-search text-xs"></i>
                                </button>
                            </div>
                        </form>
                        
                        <?php if(auth()->guard()->check()): ?>
                            <!-- Menu utilisateur -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 text-white hover:text-primary-200 transition-colors">
                                    <?php if(Auth::user()->avatar): ?>
                                        <img src="<?php echo e($avatarUrl); ?>" alt="<?php echo e(Auth::user()->name); ?>" class="w-8 h-8 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-primary-700 flex items-center justify-center text-white font-bold text-xs">
                                            <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                                        </div>
                                    <?php endif; ?>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                
                                <!-- Dropdown menu -->
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50">
                                    <div class="px-4 py-2 text-xs text-gray-500 border-b">Profil & Paramètres</div>
                                    <a href="<?php echo e(route('profile.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Mon Profil
                                    </a>
                                    <a href="<?php echo e(route('settings.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i> Paramètres
                                    </a>
                                    <a href="<?php echo e(route('messages.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-comments mr-2"></i> Messages
                                    </a>
                                    <a href="<?php echo e(route('admin.refunds.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-undo mr-2"></i> Remboursements
                                    </a>
                                    <div class="border-t my-1"></div>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center space-x-2">
                                <a href="<?php echo e(route('login')); ?>" class="text-white hover:text-primary-200 px-3 py-2 text-sm font-medium">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Connexion
                                </a>
                                <a href="<?php echo e(route('register')); ?>" class="bg-white text-primary-600 hover:bg-gray-100 px-4 py-2 rounded-full text-sm font-medium transition-colors">
                                    <i class="fas fa-user-plus mr-1"></i> S'inscrire
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Fil d'Ariane -->
    <?php if(!request()->routeIs('welcome')): ?>
        <nav class="bg-gray-100 py-2 hidden lg:block">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center space-x-2 text-sm">
                    <a href="<?php echo e(url('/')); ?>" class="text-primary-600 hover:text-primary-800">
                        <i class="fas fa-home mr-1"></i> Accueil
                    </a>
                    <?php if(request()->routeIs('dashboard')): ?>
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-600">Dashboard</span>
                    <?php elseif(request()->routeIs('categories.*')): ?>
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="<?php echo e(route('categories.index')); ?>" class="text-primary-600 hover:text-primary-800">Catégories</a>
                        <?php if(request()->routeIs('categories.show')): ?>
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-600"><?php echo e($category->name ?? 'Détails'); ?></span>
                        <?php endif; ?>
                    <?php elseif(request()->routeIs('brands.*')): ?>
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="<?php echo e(route('brands.index')); ?>" class="text-primary-600 hover:text-primary-800">Marques</a>
                        <?php if(request()->routeIs('brands.show')): ?>
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-600"><?php echo e($brand->name ?? 'Détails'); ?></span>
                        <?php endif; ?>
                    <?php elseif(request()->routeIs('items.*')): ?>
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="<?php echo e(route('items.index')); ?>" class="text-primary-600 hover:text-primary-800">Articles</a>
                        <?php if(request()->routeIs('items.show')): ?>
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-600"><?php echo e($item->name ?? 'Détails'); ?></span>
                        <?php elseif(request()->routeIs('items.my-items')): ?>
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-600">Mes articles</span>
                        <?php endif; ?>
                    <?php elseif(request()->routeIs('wallet.*')): ?>
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-600">Wallet</span>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Contenu principal -->
    <main class="flex-1 pb-20 lg:pb-0">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <?php if(!request()->routeIs('messages.*')): ?>
        <footer class="bg-gray-800 text-gray-300 py-12 mt-8">
            <div class="max-w-7xl mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <!-- À propos -->
                    <div class="col-span-2 md:col-span-1">
                        <h5 class="font-semibold text-white mb-4">
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
                        <p class="text-sm text-gray-400">
                            <?php echo e($appDescription ?? 'La marketplace de confiance pour acheter et vendre des articles d\'occasion.'); ?>

                        </p>
                    </div>
                    
                    <!-- Navigation -->
                    <div>
                        <h6 class="font-semibold text-white mb-4">Navigation</h6>
                        <ul class="space-y-2 text-sm">
                            <li><a href="<?php echo e(route('items.index')); ?>" class="hover:text-white transition-colors">Articles</a></li>
                            <li><a href="<?php echo e(route('categories.index')); ?>" class="hover:text-white transition-colors">Catégories</a></li>
                            <li><a href="<?php echo e(route('brands.index')); ?>" class="hover:text-white transition-colors">Marques</a></li>
                            <?php if(auth()->guard()->check()): ?>
                                <li><a href="<?php echo e(route('items.my-items')); ?>" class="hover:text-white transition-colors">Mes articles</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <!-- Support -->
                    <div>
                        <h6 class="font-semibold text-white mb-4">Support</h6>
                        <ul class="space-y-2 text-sm">
                            <li><a href="<?php echo e(route('help.index')); ?>" class="hover:text-white transition-colors">Centre d'aide</a></li>
                            <li><a href="<?php echo e(route('help.index')); ?>#contact" class="hover:text-white transition-colors">Contact</a></li>
                            <li><a href="<?php echo e(route('terms')); ?>" class="hover:text-white transition-colors">CGU</a></li>
                            <li><a href="<?php echo e(route('privacy')); ?>" class="hover:text-white transition-colors">Confidentialité</a></li>
                        </ul>
                    </div>
                    
                    <!-- Réseaux sociaux -->
                    <div>
                        <h6 class="font-semibold text-white mb-4">Suivez-nous</h6>
                        <div class="flex space-x-3">
                            <a href="https://facebook.com/vintapp" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fab fa-facebook-f text-lg"></i>
                            </a>
                            <a href="https://twitter.com/vintapp" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fab fa-twitter text-lg"></i>
                            </a>
                            <a href="https://instagram.com/vintapp" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fab fa-instagram text-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Newsletter -->
                <div class="border-t border-gray-700 mt-8 pt-8">
                    <div class="text-center max-w-md mx-auto">
                        <h5 class="font-semibold text-white mb-3">📧 Newsletter</h5>
                        <p class="text-sm text-gray-400 mb-4">Recevez nos dernières offres et nouveautés.</p>
                        <form id="newsletterForm" class="flex gap-2">
                            <?php echo csrf_field(); ?>
                            <input type="email" id="newsletterEmail" 
                                   class="flex-1 px-3 py-2 bg-gray-700 text-white rounded-md border border-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                                   placeholder="Votre email" required>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        <div id="newsletterMessage" class="mt-2 text-sm"></div>
                    </div>
                </div>
                
                <!-- Copyright -->
                <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                    <p class="text-sm text-gray-400">
                        © <?php echo e(date('Y')); ?> <?php echo e($appName ?? config('app.name', 'VintApp')); ?>. Tous droits réservés.
                    </p>
                </div>
            </div>
        </footer>
    <?php endif; ?>

    <!-- Navigation mobile (bottom) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
        <div class="grid grid-cols-5 h-16">
            <a href="<?php echo e(url('/')); ?>" class="flex flex-col items-center justify-center text-gray-500 hover:text-primary-600 <?php echo e(request()->is('/') ? 'text-primary-600' : ''); ?>">
                <i class="fas fa-home text-lg"></i>
                <span class="text-xs mt-1">Accueil</span>
            </a>
            <a href="<?php echo e(route('items.create')); ?>" class="flex flex-col items-center justify-center text-gray-500 hover:text-primary-600 <?php echo e(request()->routeIs('items.create') ? 'text-primary-600' : ''); ?>">
                <i class="fas fa-plus-circle text-lg"></i>
                <span class="text-xs mt-1">Vendre</span>
            </a>
            <a href="<?php echo e(route('items.index')); ?>" class="flex flex-col items-center justify-center text-gray-500 hover:text-primary-600 <?php echo e(request()->routeIs('items.index') ? 'text-primary-600' : ''); ?>">
                <i class="fas fa-box text-lg"></i>
                <span class="text-xs mt-1">Articles</span>
            </a>
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('wallet.index')); ?>" class="flex flex-col items-center justify-center text-gray-500 hover:text-primary-600 <?php echo e(request()->routeIs('wallet.*') ? 'text-primary-600' : ''); ?>">
                    <i class="fas fa-wallet text-lg"></i>
                    <span class="text-xs mt-1">Wallet</span>
                </a>
                <a href="<?php echo e(route('settings.index')); ?>" class="flex flex-col items-center justify-center text-gray-500 hover:text-primary-600 <?php echo e(request()->routeIs('settings.*') ? 'text-primary-600' : ''); ?>">
                    <i class="fas fa-cog text-lg"></i>
                    <span class="text-xs mt-1">Profil</span>
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="flex flex-col items-center justify-center text-gray-500 hover:text-primary-600">
                    <i class="fas fa-sign-in-alt text-lg"></i>
                    <span class="text-xs mt-1">Connexion</span>
                </a>
                <a href="<?php echo e(route('register')); ?>" class="flex flex-col items-center justify-center text-gray-500 hover:text-primary-600">
                    <i class="fas fa-user-plus text-lg"></i>
                    <span class="text-xs mt-1">S'inscrire</span>
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts personnalisés -->
    <script>
        // Fonction pour afficher les notifications
        function toggleNotifications() {
            if (!window.isAuthenticated) {
                window.location.href = '<?php echo e(route("login")); ?>';
                return;
            }
            
            const existingPanel = document.getElementById('notifications-panel');
            
            if (existingPanel) {
                existingPanel.remove();
                return;
            }
            
            const panel = document.createElement('div');
            panel.id = 'notifications-panel';
            panel.className = 'fixed top-20 right-4 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto';
            panel.innerHTML = `
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Notifications</h3>
                    <button onclick="this.closest('#notifications-panel').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4" id="notifications-content">
                    <p class="text-gray-500 text-sm text-center">Chargement...</p>
                </div>
            `;
            
            document.body.appendChild(panel);
            loadNotifications();
            
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

        <?php if(auth()->guard()->check()): ?>
        function loadNotifications() {
            const content = document.getElementById('notifications-content');
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
                            <div class="flex items-start space-x-3">
                                <i class="fas ${getNotificationIcon(notification.type)} text-primary-600 mt-1"></i>
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
                if (url && url !== '#') {
                    window.location.href = url;
                }
            });
        }
        <?php else: ?>
        function loadNotifications() {
            const content = document.getElementById('notifications-content');
            if (content) {
                content.innerHTML = '<p class="text-gray-500 text-sm text-center">Connectez-vous pour voir vos notifications</p>';
            }
        }
        <?php endif; ?>

        // Newsletter
        document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('newsletterEmail').value;
            const messageDiv = document.getElementById('newsletterMessage');
            
            fetch('/newsletter/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = '<p class="text-green-500">✅ Inscription réussie !</p>';
                    document.getElementById('newsletterEmail').value = '';
                } else {
                    messageDiv.innerHTML = '<p class="text-red-500">❌ Erreur lors de l\'inscription</p>';
                }
            })
            .catch(error => {
                messageDiv.innerHTML = '<p class="text-red-500">❌ Erreur lors de l\'inscription</p>';
            });
        });

        // Thème
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
        }

        function getPreferredTheme() {
            return localStorage.getItem('theme') || window.userTheme || 'auto';
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 VintApp chargé avec Tailwind CSS');
            applyTheme(getPreferredTheme());
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/app.blade.php ENDPATH**/ ?>