<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Vintapp - La marketplace de confiance pour acheter et vendre des articles d\'occasion de qualité'); ?>">
    <meta name="keywords" content="<?php echo $__env->yieldContent('meta_keywords', 'vintapp, marketplace, occasion, vente, achat, articles, vêtements, électronique'); ?>">

    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">
    <meta name="theme-color" content="#7c3aed">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="VintApp">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/icons/icon-512x512.png')); ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo e(asset('images/icons/icon-72x72.png')); ?>">
    <link rel="apple-touch-icon" sizes="96x96" href="<?php echo e(asset('images/icons/icon-96x96.png')); ?>">
    <link rel="apple-touch-icon" sizes="128x128" href="<?php echo e(asset('images/icons/icon-128x128.png')); ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo e(asset('images/icons/icon-144x144.png')); ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo e(asset('images/icons/icon-152x152.png')); ?>">
    <link rel="apple-touch-icon" sizes="192x192" href="<?php echo e(asset('images/icons/icon-192x192.png')); ?>">
    <link rel="apple-touch-icon" sizes="384x384" href="<?php echo e(asset('images/icons/icon-384x384.png')); ?>">
    <link rel="apple-touch-icon" sizes="512x512" href="<?php echo e(asset('images/icons/icon-512x512.png')); ?>">

    <title><?php echo $__env->yieldContent('title', '<?php echo e($appName ?? "Vintapp"); ?>'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset($appFavicon ?? '/favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/icons/icon-512x512.png')); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">

    <!-- Lazy Loading CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/lazy-loading.css')); ?>">

    <!-- Splash Screen CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/splash-screen.css')); ?>">

    <!-- Tailwind CSS -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <!-- Color Palette Variables (loaded AFTER Vite to override default colors) -->
    <link rel="stylesheet" href="<?php echo e(asset('css/dynamic-colors.css')); ?>">

    <!-- Day/Night Theme (système automatique jour/nuit) -->
    <?php if(config('colors.day_night.enabled', false)): ?>
        <link rel="stylesheet" href="<?php echo e(asset('css/day-night-theme.css')); ?>">
    <?php endif; ?>

    <!-- Custom Styles -->
    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <script>
        window.userTheme = "<?php echo e(addslashes(Auth::user()?->theme_preference ?? '')); ?>";
        window.isAuthenticated = <?php echo e(Auth::check() ? 'true' : 'false'); ?>;

        // Configuration jour/nuit multi-palettes (injectée côté serveur)
        <?php
            $dayNightService = app(\App\Services\DayNightService::class);
            $dayNightClientConfig = $dayNightService->getClientConfig();
        ?>
        window.VintAppDayNightConfig = <?php echo json_encode($dayNightClientConfig, 15, 512) ?>;
        
        // Fonction pour appliquer le thème
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            // Gérer la classe dark pour Tailwind
            if (theme === 'dark' || theme === 'night' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-200">
    
    <!-- Header avec barre de profil -->
    <header class="bg-primary lg:bg-white dark:bg-gray-800/95 dark:backdrop-blur-md shadow-sm border-b border-primary-700 lg:border-gray-200 dark:border-gray-700/50 sticky top-0 z-50 transition-colors duration-300">
        <div class="flex items-center justify-between px-4 py-2.5 max-w-7xl lg:mx-auto">
            <?php if(auth()->guard()->check()): ?>
                <!-- Profil utilisateur connecté -->
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('profile.index')); ?>" class="group flex items-center gap-2.5" aria-label="Mon profil">
                        <?php if(Auth::user()->avatar): ?>
                            <?php
                                $avatarUrl = filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL) 
                                    ? Auth::user()->avatar 
                                    : asset('storage/' . Auth::user()->avatar);
                            ?>
                            <img src="<?php echo e($avatarUrl); ?>" 
                                 alt="<?php echo e(Auth::user()->name); ?>" 
                                 class="w-9 h-9 rounded-full object-cover ring-2 ring-white/80 lg:ring-primary-200 group-hover:ring-white lg:group-hover:ring-primary-400 transition-all duration-200"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-500 to-accent-400 items-center justify-center text-white font-bold text-xs hidden shadow-inner">
                                <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                            </div>
                        <?php else: ?>
                            <div class="w-9 h-9 rounded-full bg-white/90 lg:bg-gradient-to-br lg:from-primary-500 lg:to-accent-400 flex items-center justify-center text-primary-600 lg:text-white font-bold text-xs ring-2 ring-white/60 lg:ring-0 shadow-inner">
                                <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                            </div>
                        <?php endif; ?>
                        <span class="font-semibold text-white lg:text-gray-800 dark:text-gray-100 text-sm truncate max-w-[140px] group-hover:opacity-80 transition-opacity"><?php echo e(Auth::user()->name); ?></span>
                    </a>
                </div>
                
                <!-- Actions utilisateur connecté -->
                <div class="flex items-center gap-1">
                    <!-- Notifications -->
                    <button class="relative p-2 hover:bg-white/10 lg:hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200 active:scale-95" onclick="toggleNotifications()" aria-label="Notifications">
                        <svg class="w-5 h-5 text-white lg:text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                        </svg>
                        <?php
                            $unreadNotifications = App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->count();
                        ?>
                        <?php if($unreadNotifications > 0): ?>
                            <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 ring-2 ring-primary lg:ring-white dark:ring-gray-800"></span>
                            </span>
                        <?php endif; ?>
                    </button>
                    
                    <!-- Panier -->
                    <a href="<?php echo e(route('cart.index')); ?>" class="relative p-2 hover:bg-white/10 lg:hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200 active:scale-95" aria-label="Panier">
                        <svg class="w-5 h-5 text-white lg:text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        <?php if(session('cart') && count(session('cart')) > 0): ?>
                            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold ring-2 ring-primary lg:ring-white dark:ring-gray-800">
                                <?php echo e(count(session('cart'))); ?>

                            </span>
                        <?php endif; ?>
                    </a>
                </div>
            <?php else: ?>
                <!-- Logo pour utilisateur non connecté -->
                <div class="flex items-center">
                    <a href="<?php echo e(url('/')); ?>" class="group flex items-center gap-2.5" aria-label="Accueil">
                        <div class="w-9 h-9 rounded-xl bg-white/90 lg:bg-gradient-to-br lg:from-primary-500 lg:to-accent-400 flex items-center justify-center text-primary-600 lg:text-white shadow-sm group-hover:shadow-md transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-white lg:text-gray-800 dark:text-gray-100 text-sm group-hover:opacity-80 transition-opacity"><?php echo e(config('app.name', 'VintApp')); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Navigation principale (desktop seulement) -->
        <nav class="bg-primary hidden lg:block" role="navigation" aria-label="Navigation principale">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between h-14">
                    <!-- Logo et navigation gauche -->
                    <div class="flex items-center gap-8">
                        <!-- Logo -->
                        <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2 text-white hover:opacity-80 transition-opacity">
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
                        <div class="flex items-center gap-1">
                            <?php
                                $desktopNavLinks = [
                                    ['route' => 'dashboard', 'label' => 'Dashboard', 'active' => request()->routeIs('dashboard'), 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z', 'auth' => false],
                                    ['route' => 'items.index', 'label' => 'Articles', 'active' => request()->routeIs('items.index'), 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z', 'auth' => false],
                                    ['route' => 'categories.index', 'label' => 'Catégories', 'active' => request()->routeIs('categories.*'), 'icon' => 'M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3', 'auth' => false],
                                    ['route' => 'brands.index', 'label' => 'Marques', 'active' => request()->routeIs('brands.*'), 'icon' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z M6 6h.008v.008H6V6z', 'auth' => false],
                                ];
                                $authNavLinks = [
                                    ['route' => 'items.my-items', 'label' => 'Mes Articles', 'active' => request()->routeIs('items.my-items'), 'icon' => 'M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z'],
                                    ['route' => 'orders.index', 'label' => 'Commandes', 'active' => request()->routeIs('orders.index'), 'icon' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121 0 2.002-.895 1.924-2.013l-.553-7.72a.75.75 0 00-.746-.687H6.154a.75.75 0 00-.746.687l-.553 7.72a1.924 1.924 0 001.924 2.013zm12.75 3a3 3 0 00-3-3m3 3v.008h-.008V17.25h.008zm-3 0v.008h-.008V17.25h.008z'],
                                    ['route' => 'wallet.index', 'label' => 'Wallet', 'active' => request()->routeIs('wallet.*'), 'icon' => 'M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 110-6h.008A2.25 2.25 0 0021 6.008V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v.008c0 1.243 1.007 2.25 2.25 2.25H15a3 3 0 010 6H5.25A2.25 2.25 0 003 16.5v1.245c0 1.243 1.007 2.25 2.25 2.248h13.5A2.25 2.25 0 0021 17.745V12z'],
                                ];
                            ?>
                            
                            <?php $__currentLoopData = $desktopNavLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route($link['route'])); ?>" class="relative flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 <?php echo e($link['active'] ? 'text-white bg-white/15' : 'text-white/75 hover:text-white hover:bg-white/10'); ?>">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($link['icon']); ?>"/></svg>
                                    <?php echo e($link['label']); ?>

                                    <?php if($link['active']): ?><span class="absolute bottom-0 left-3 right-3 h-0.5 bg-white rounded-full"></span><?php endif; ?>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if(auth()->guard()->check()): ?>
                                <?php $__currentLoopData = $authNavLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route($link['route'])); ?>" class="relative flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 <?php echo e($link['active'] ? 'text-white bg-white/15' : 'text-white/75 hover:text-white hover:bg-white/10'); ?>">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($link['icon']); ?>"/></svg>
                                        <?php echo e($link['label']); ?>

                                        <?php if($link['active']): ?><span class="absolute bottom-0 left-3 right-3 h-0.5 bg-white rounded-full"></span><?php endif; ?>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            
                            <a href="<?php echo e(route('help.index')); ?>" class="relative flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('help.*') ? 'text-white bg-white/15' : 'text-white/75 hover:text-white hover:bg-white/10'); ?>">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                                Aide
                                <?php if(request()->routeIs('help.*')): ?><span class="absolute bottom-0 left-3 right-3 h-0.5 bg-white rounded-full"></span><?php endif; ?>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Barre de recherche et menu utilisateur -->
                    <div class="flex items-center gap-3">
                        <!-- Barre de recherche -->
                        <form class="flex items-center" method="GET" action="<?php echo e(route('items.search')); ?>">
                            <div class="relative group">
                                <input type="search" 
                                       name="q" 
                                       placeholder="Rechercher un article..." 
                                       value="<?php echo e(request('q')); ?>"
                                       class="w-72 px-4 py-2 pl-10 text-sm bg-white/10 text-white placeholder-white/50 border border-white/20 rounded-xl focus:outline-none focus:bg-white/20 focus:border-white/40 focus:ring-0 transition-all duration-200">
                                <svg class="w-4 h-4 absolute left-3 top-2.5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            </div>
                        </form>
                        
                        <?php if(auth()->guard()->check()): ?>
                            <!-- Menu utilisateur -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 text-white hover:text-primary-200 transition-colors p-1.5 rounded-lg hover:bg-white/10" aria-label="Menu utilisateur">
                                    <?php if(Auth::user()->avatar): ?>
                                        <img src="<?php echo e($avatarUrl); ?>" alt="<?php echo e(Auth::user()->name); ?>" class="w-7 h-7 rounded-lg object-cover ring-2 ring-white/30">
                                    <?php else: ?>
                                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold text-xs">
                                            <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                                        </div>
                                    <?php endif; ?>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </button>
                                
                                <!-- Dropdown menu -->
                                <div x-show="open" @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 py-1 z-50 overflow-hidden">
                                    <div class="px-4 py-2.5 text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Profil & Paramètres</div>
                                    <a href="<?php echo e(route('profile.index')); ?>" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                        Mon Profil
                                    </a>
                                    <a href="<?php echo e(route('settings.index')); ?>" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Paramètres
                                    </a>
                                    <a href="<?php echo e(route('messages.index')); ?>" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                                        Messages
                                    </a>
                                    <a href="<?php echo e(route('admin.refunds.index')); ?>" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                        Remboursements
                                    </a>
                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                            Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('login')); ?>" class="text-white/80 hover:text-white px-3 py-1.5 text-sm font-medium rounded-lg hover:bg-white/10 transition-all duration-200">
                                    Connexion
                                </a>
                                <a href="<?php echo e(route('register')); ?>" class="bg-white text-primary-600 hover:bg-white/90 px-4 py-1.5 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm">
                                    S'inscrire
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
        <nav class="bg-gray-50 dark:bg-gray-800/50 py-2 hidden lg:block border-b border-gray-100 dark:border-gray-700/30" aria-label="Fil d'Ariane">
            <div class="max-w-7xl mx-auto px-4">
                <ol class="flex items-center gap-1.5 text-sm">
                    <li>
                        <a href="<?php echo e(url('/')); ?>" class="text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                            Accueil
                        </a>
                    </li>
                    <?php
                        $chevronSvg = '<svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>';
                    ?>
                    <?php if(request()->routeIs('dashboard')): ?>
                        <li class="flex items-center gap-1.5"><?php echo $chevronSvg; ?><span class="text-gray-700 dark:text-gray-200 font-medium">Dashboard</span></li>
                    <?php elseif(request()->routeIs('categories.*')): ?>
                        <li class="flex items-center gap-1.5"><?php echo $chevronSvg; ?><a href="<?php echo e(route('categories.index')); ?>" class="text-gray-500 hover:text-primary-600 dark:text-gray-400 transition-colors">Catégories</a></li>
                        <?php if(request()->routeIs('categories.show')): ?>
                            <li class="flex items-center gap-1.5"><?php echo $chevronSvg; ?><span class="text-gray-700 dark:text-gray-200 font-medium"><?php echo e($category->name ?? 'Détails'); ?></span></li>
                        <?php endif; ?>
                    <?php elseif(request()->routeIs('brands.*')): ?>
                        <li class="flex items-center gap-1.5"><?php echo $chevronSvg; ?><a href="<?php echo e(route('brands.index')); ?>" class="text-gray-500 hover:text-primary-600 dark:text-gray-400 transition-colors">Marques</a></li>
                        <?php if(request()->routeIs('brands.show')): ?>
                            <li class="flex items-center gap-1.5"><?php echo $chevronSvg; ?><span class="text-gray-700 dark:text-gray-200 font-medium"><?php echo e($brand->name ?? 'Détails'); ?></span></li>
                        <?php endif; ?>
                    <?php elseif(request()->routeIs('items.*')): ?>
                        <li class="flex items-center gap-1.5"><?php echo $chevronSvg; ?><a href="<?php echo e(route('items.index')); ?>" class="text-gray-500 hover:text-primary-600 dark:text-gray-400 transition-colors">Articles</a></li>
                        <?php if(request()->routeIs('items.show')): ?>
                            <li class="flex items-center gap-1.5"><?php echo $chevronSvg; ?><span class="text-gray-700 dark:text-gray-200 font-medium"><?php echo e($item->name ?? 'Détails'); ?></span></li>
                        <?php elseif(request()->routeIs('items.my-items')): ?>
                            <li class="flex items-center gap-1.5"><?php echo $chevronSvg; ?><span class="text-gray-700 dark:text-gray-200 font-medium">Mes articles</span></li>
                        <?php endif; ?>
                    <?php elseif(request()->routeIs('wallet.*')): ?>
                        <li class="flex items-center gap-1.5"><?php echo $chevronSvg; ?><span class="text-gray-700 dark:text-gray-200 font-medium">Wallet</span></li>
                    <?php endif; ?>
                </ol>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Contenu principal -->
    <main class="flex-1 pb-20 lg:pb-0">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Notifications en temps réel -->
    <?php if (isset($component)) { $__componentOriginala08f91db378acab53556cdbf9a3befcf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala08f91db378acab53556cdbf9a3befcf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notifications-realtime','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notifications-realtime'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala08f91db378acab53556cdbf9a3befcf)): ?>
<?php $attributes = $__attributesOriginala08f91db378acab53556cdbf9a3befcf; ?>
<?php unset($__attributesOriginala08f91db378acab53556cdbf9a3befcf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala08f91db378acab53556cdbf9a3befcf)): ?>
<?php $component = $__componentOriginala08f91db378acab53556cdbf9a3befcf; ?>
<?php unset($__componentOriginala08f91db378acab53556cdbf9a3befcf); ?>
<?php endif; ?>

    <!-- Footer -->
    <?php if(!request()->routeIs('messages.*')): ?>
        <?php if (isset($component)) { $__componentOriginal8a8716efb3c62a45938aca52e78e0322 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8a8716efb3c62a45938aca52e78e0322 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8a8716efb3c62a45938aca52e78e0322)): ?>
<?php $attributes = $__attributesOriginal8a8716efb3c62a45938aca52e78e0322; ?>
<?php unset($__attributesOriginal8a8716efb3c62a45938aca52e78e0322); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8a8716efb3c62a45938aca52e78e0322)): ?>
<?php $component = $__componentOriginal8a8716efb3c62a45938aca52e78e0322; ?>
<?php unset($__componentOriginal8a8716efb3c62a45938aca52e78e0322); ?>
<?php endif; ?>
    <?php endif; ?>

    <!-- Navigation mobile (bottom) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50" role="navigation" aria-label="Navigation mobile">
        <!-- Fond avec blur -->
        <div class="absolute inset-0 bg-white/80 dark:bg-gray-900/90 backdrop-blur-xl border-t border-gray-200/60 dark:border-gray-700/50"></div>
        
        <div class="relative grid grid-cols-5 h-16 max-w-lg mx-auto px-2 safe-area-bottom">
            <?php
                $mobileNav = [
                    ['url' => url('/'), 'label' => 'Accueil', 'active' => request()->is('/'),
                     'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
                     'iconFilled' => 'M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.432z'],
                    ['url' => route('items.create'), 'label' => 'Vendre', 'active' => request()->routeIs('items.create'),
                     'icon' => 'M12 4.5v15m7.5-7.5h-15',
                     'iconFilled' => 'M12 4.5v15m7.5-7.5h-15', 'special' => true],
                    ['url' => route('items.index'), 'label' => 'Articles', 'active' => request()->routeIs('items.index'),
                     'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
                     'iconFilled' => 'M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375zm0 0 M3.087 9l.54 9.176A3 3 0 006.62 21h10.757a3 3 0 002.995-2.824L20.913 9H3.087zm6.163 3.75A.75.75 0 0110 12h4a.75.75 0 010 1.5h-4a.75.75 0 01-.75-.75z'],
                ];
            ?>
            
            <?php $__currentLoopData = $mobileNav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($item['url']); ?>" class="group flex flex-col items-center justify-center gap-0.5 relative" aria-label="<?php echo e($item['label']); ?>">
                    <?php if(!empty($item['special'])): ?>
                        
                        <div class="w-10 h-10 -mt-3 rounded-xl flex items-center justify-center transition-all duration-200 <?php echo e($item['active'] ? 'bg-primary-600 shadow-lg shadow-primary-500/30' : 'bg-primary-500 shadow-md shadow-primary-500/20 group-active:scale-90'); ?>">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($item['icon']); ?>"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold <?php echo e($item['active'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400'); ?>"><?php echo e($item['label']); ?></span>
                    <?php else: ?>
                        <div class="relative p-1">
                            <?php if($item['active']): ?>
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" viewBox="0 0 24 24" fill="currentColor"><?php $__currentLoopData = explode(' M', $item['iconFilled']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><path d="<?php echo e($i > 0 ? 'M' : ''); ?><?php echo e($path); ?>"/><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></svg>
                            <?php else: ?>
                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($item['icon']); ?>"/></svg>
                            <?php endif; ?>
                        </div>
                        <span class="text-[10px] font-medium <?php echo e($item['active'] ? 'text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-500 dark:text-gray-400'); ?>"><?php echo e($item['label']); ?></span>
                        <?php if($item['active']): ?>
                            <span class="absolute top-0 left-1/2 -translate-x-1/2 w-4 h-0.5 bg-primary-500 rounded-full"></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <?php if(auth()->guard()->check()): ?>
                <?php
                    $authMobileNav = [
                        ['url' => route('wallet.index'), 'label' => 'Wallet', 'active' => request()->routeIs('wallet.*'),
                         'icon' => 'M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 110-6h.75a.75.75 0 000-1.5H15a4.5 4.5 0 000 9h3.75A.75.75 0 0019.5 12v0a.75.75 0 00-.75-.75H15a3 3 0 100 6h3.75A2.25 2.25 0 0021 15V12zM3 5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25v13.5A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 18.75V5.25z',
                         'iconSimple' => 'M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 110-6h.008A2.25 2.25 0 0021 6.008V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v.008c0 1.243 1.007 2.25 2.25 2.25H15a3 3 0 010 6H5.25A2.25 2.25 0 003 16.5v1.245c0 1.243 1.007 2.25 2.25 2.248h13.5A2.25 2.25 0 0021 17.745V12z'],
                        ['url' => route('settings.index'), 'label' => 'Profil', 'active' => request()->routeIs('settings.*'),
                         'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
                         'iconSimple' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                    ];
                ?>
                <?php $__currentLoopData = $authMobileNav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($item['url']); ?>" class="group flex flex-col items-center justify-center gap-0.5 relative" aria-label="<?php echo e($item['label']); ?>">
                        <div class="relative p-1">
                            <svg class="w-5 h-5 transition-colors <?php echo e($item['active'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300'); ?>" fill="<?php echo e($item['active'] ? 'currentColor' : 'none'); ?>" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($item['active'] ? ($item['iconSimple'] ?? $item['icon']) : $item['icon']); ?>"/></svg>
                        </div>
                        <span class="text-[10px] font-medium <?php echo e($item['active'] ? 'text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-500 dark:text-gray-400'); ?>"><?php echo e($item['label']); ?></span>
                        <?php if($item['active']): ?>
                            <span class="absolute top-0 left-1/2 -translate-x-1/2 w-4 h-0.5 bg-primary-500 rounded-full"></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="group flex flex-col items-center justify-center gap-0.5 relative" aria-label="Connexion">
                    <div class="relative p-1">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                    </div>
                    <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Connexion</span>
                </a>
                <a href="<?php echo e(route('register')); ?>" class="group flex flex-col items-center justify-center gap-0.5 relative" aria-label="S'inscrire">
                    <div class="relative p-1">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
                    </div>
                    <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">S'inscrire</span>
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Network Speed Adapter (doit être chargé en premier) -->
    <script src="<?php echo e(asset('js/network-adapter.js')); ?>?v=<?php echo e(filemtime(public_path('js/network-adapter.js'))); ?>"></script>

    <!-- Content Visibility Manager (charger en premier) -->
    <script src="<?php echo e(asset('js/content-visibility.js')); ?>"></script>

    <!-- Page Skeleton Loader -->
    <script src="<?php echo e(asset('js/page-skeleton.js')); ?>"></script>

    <!-- Navigation Skeleton Manager (pour les transitions entre pages) -->
    <script src="<?php echo e(asset('js/navigation-skeleton.js')); ?>"></script>

    <!-- Lazy Loading Manager -->
    <script src="<?php echo e(asset('js/lazy-loading.js')); ?>" defer></script>

    <!-- PWA Manager -->
    <script src="<?php echo e(asset('js/pwa.js')); ?>?v=<?php echo e(time()); ?>" defer></script>
    
    <!-- Push Notification Manager -->
    <script type="module" src="<?php echo e(asset('js/push-manager.js')); ?>?v=<?php echo e(time()); ?>"></script>
    
    <!-- Background Sync Manager -->
    <script src="<?php echo e(asset('js/background-sync.js')); ?>?v=<?php echo e(time()); ?>"></script>

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
            panel.className = 'fixed top-20 right-4 w-80 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-y-auto';
            panel.innerHTML = `
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Notifications</h3>
                    <button onclick="this.closest('#notifications-panel').remove()" class="text-gray-400 hover:text-gray-600 dark:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4" id="notifications-content">
                    <p class="text-gray-500 dark:text-gray-400 text-sm text-center">Chargement...</p>
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
                        content.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm text-center">Aucune notification</p>';
                        return;
                    }
                    
                    content.innerHTML = data.notifications.map(notification => `
                        <div class="p-3 border-b border-gray-100 hover:bg-gray-50 dark:bg-gray-900 cursor-pointer" 
                             onclick="markNotificationAsRead(${notification.id}, '${notification.data?.url || '#'}')">
                            <div class="flex items-start space-x-3">
                                <i class="fas ${getNotificationIcon(notification.type)} text-primary-600 mt-1"></i>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm">${notification.title}</div>
                                    <div class="text-gray-600 dark:text-gray-300 text-xs mt-1">${notification.message}</div>
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
                content.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm text-center">Connectez-vous pour voir vos notifications</p>';
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
            
            // Gérer la classe dark pour Tailwind
            if (theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        function getPreferredTheme() {
            return localStorage.getItem('theme') || window.userTheme || 'auto';
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            const theme = getPreferredTheme();
            applyTheme(theme);
            
            // Écouter les changements de préférences système pour le mode auto
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (getPreferredTheme() === 'auto') {
                    applyTheme('auto');
                }
            });
        });
    </script>

    <?php if(auth()->guard()->check()): ?>
    <!-- Firebase SDK pour notifications push -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>
    
    <script>
        // Configuration Firebase
        const firebaseConfig = {
            apiKey: "<?php echo e(config('services.firebase.api_key')); ?>",
            authDomain: "<?php echo e(config('services.firebase.auth_domain')); ?>",
            projectId: "<?php echo e(config('services.firebase.project_id')); ?>",
            storageBucket: "<?php echo e(config('services.firebase.storage_bucket')); ?>",
            messagingSenderId: "<?php echo e(config('services.firebase.messaging_sender_id')); ?>",
            appId: "<?php echo e(config('services.firebase.app_id')); ?>"
        };

        // Initialiser Firebase
        const firebaseApp = firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // VAPID Key pour les notifications push web
        const vapidKey = "<?php echo e(config('services.firebase.vapid_key')); ?>";

        async function initFirebasePushNotifications() {
            try {
                // Vérifier si le navigateur supporte les notifications
                if (!('Notification' in window)) {
                    return;
                }

                // Vérifier si le Service Worker est supporté
                if (!('serviceWorker' in navigator)) {
                    return;
                }

                // Enregistrer le Service Worker principal (qui inclut Firebase)
                let registration = await navigator.serviceWorker.register('/sw.js');

                // Attendre que le Service Worker soit actif
                if (registration.installing) {
                    await new Promise((resolve) => {
                        registration.installing.addEventListener('statechange', (e) => {
                            if (e.target.state === 'activated') {
                                resolve();
                            }
                        });
                    });
                } else if (registration.waiting) {
                    await navigator.serviceWorker.ready;
                } else if (!registration.active) {
                    await navigator.serviceWorker.ready;
                }

                // S'assurer qu'on a bien un Service Worker actif
                registration = await navigator.serviceWorker.ready;

                // Demander la permission de notification
                const permission = await requestNotificationPermission();
                
                if (permission === 'granted') {
                    // Récupérer le token FCM
                    const currentToken = await messaging.getToken({
                        vapidKey: vapidKey,
                        serviceWorkerRegistration: registration
                    });

                    if (currentToken) {
                        await saveFCMToken(currentToken);
                    }

                    // Écouter les messages en premier plan (app ouverte)
                    messaging.onMessage((payload) => {
                        displayForegroundNotification(payload);
                    });
                }

            } catch (error) {
                // Erreur silencieuse
            }
        }

        async function requestNotificationPermission() {
            try {
                const permission = await Notification.requestPermission();
                return permission;
            } catch (error) {
                return 'denied';
            }
        }

        async function saveFCMToken(token) {
            try {
                const response = await fetch('/api/fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        token: token,
                        device_type: /iPhone|iPad|iPod|Android/i.test(navigator.userAgent) ? 'mobile' : 'desktop'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    localStorage.setItem('fcm_token', token);
                }
            } catch (error) {
                // Erreur silencieuse
            }
        }

        function displayForegroundNotification(payload) {
            const title = payload.notification?.title || payload.data?.title || 'VintApp';
            const options = {
                body: payload.notification?.body || payload.data?.body || 'Nouvelle notification',
                icon: payload.notification?.icon || payload.data?.icon || '/images/icons/icon-192x192.png',
                badge: '/images/icons/icon-96x96.png',
                vibrate: [200, 100, 200],
                data: payload.data || {},
                requireInteraction: false
            };

            // Afficher notification système (si permission accordée)
            if (Notification.permission === 'granted') {
                const notification = new Notification(title, options);
                
                notification.onclick = function(event) {
                    event.preventDefault();
                    const url = payload.data?.url || payload.fcmOptions?.link || '/';
                    window.open(url, '_blank');
                    notification.close();
                };
            }

            // Aussi afficher le toast dans l'app
            if (typeof showNotification === 'function' && payload.data) {
                showNotification(payload.data);
            }
        }

        // Rafraîchir le token périodiquement (toutes les 24h)
        setInterval(async () => {
            try {
                const currentToken = await messaging.getToken({ vapidKey: vapidKey });
                const savedToken = localStorage.getItem('fcm_token');
                
                if (currentToken && currentToken !== savedToken) {
                    await saveFCMToken(currentToken);
                }
            } catch (error) {
                // Erreur silencieuse
            }
        }, 24 * 60 * 60 * 1000); // 24 heures

        // Initialiser les notifications push au chargement
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFirebasePushNotifications);
        } else {
            initFirebasePushNotifications();
        }
    </script>
    <?php endif; ?>

    <!-- Système Jour/Nuit automatique -->
    <?php if(config('colors.day_night.enabled', false)): ?>
        <script src="<?php echo e(asset('js/day-night.js')); ?>" defer></script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH D:\Mes projets\vintApp\resources\views/app.blade.php ENDPATH**/ ?>