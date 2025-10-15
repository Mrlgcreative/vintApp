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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Vinted Violet CSS -->
        <link href="<?php echo e(asset('css/vinted-violet.css')); ?>" rel="stylesheet">

        <!-- Custom Styles -->
        <?php echo $__env->yieldPushContent('styles'); ?>

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <script>
        window.userTheme = "<?php echo e(addslashes(Auth::user()?->theme_preference ?? '')); ?>";
        window.isAuthenticated = <?php echo e(Auth::check() ? 'true' : 'false'); ?>;
        </script>
    </head>
    <body class="font-sans antialiased">
        <!-- Bande violette mobile avec nom de l'app -->
        <div id="mobile-top-bar" class="d-md-none d-lg-none d-xl-none">
            <div class="mobile-top-bar-content justify-content-between">
                <span class="mobile-app-name"><?php echo e($appName ?? 'Vintapp'); ?></span>
                <span class="mobile-notification-link d-flex align-items-center" id="mobile-notification-btn" style="cursor:pointer;">
                    <i class="fas fa-bell fa-lg text-white"></i>
                </span>
            </div>
            <!-- Dropdown notifications mobile, caché par défaut -->
            <div id="mobile-notification-dropdown" style="display:none; position:fixed; top:56px; right:12px; left:auto; z-index:2000; min-width:300px; max-width:90vw;">
                <ul class="dropdown-menu show dropdown-menu-end" style="min-width: 300px; position:static; float:none;">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><div class="dropdown-item text-center text-muted">Aucune notification</div></li>
                </ul>
            </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Affichage du dropdown notifications sur mobile
    const notifBtn = document.getElementById('mobile-notification-btn');
    const notifDropdown = document.getElementById('mobile-notification-dropdown');
    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.style.display = notifDropdown.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', function(e) {
            if (!notifDropdown.contains(e.target) && e.target !== notifBtn) {
                notifDropdown.style.display = 'none';
            }
        });
    }
});
</script>
        </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // CORRECTION NAVBAR - Script de débogage et correction forcée
            console.log('🔧 Initialisation correction navbar...');
            
            // Vérifier la largeur d'écran
            const screenWidth = window.innerWidth;
            const isMobile = /Mobile|Android|iPhone|iPad/i.test(navigator.userAgent);
            const isTouchDevice = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
            
            console.log('📱 Détection appareil:', {
                width: screenWidth,
                isMobile: isMobile,
                isTouch: isTouchDevice,
                userAgent: navigator.userAgent.substring(0, 50) + '...'
            });
            
            // Forcer la visibilité de la navbar sur écrans larges
            const navbar = document.querySelector('nav.top-navbar');
            const navbarCollapse = document.querySelector('.navbar-collapse');
            
            if (navbar && screenWidth >= 768) {
                console.log('✅ Écran ≥ 768px - Forcer affichage navbar');
                navbar.style.display = 'block';
                navbar.style.visibility = 'visible';
                
                if (navbarCollapse) {
                    // Sur desktop (≥ 992px), afficher directement
                    if (screenWidth >= 992) {
                        navbarCollapse.classList.add('show');
                        navbarCollapse.style.display = 'flex';
                        console.log('💻 Mode Desktop - navbar-collapse visible');
                    } else {
                        // Sur tablette/mobile large (768-991px), utiliser le toggle
                        console.log('📱 Mode Tablette - navbar-collapse en mode toggle');
                    }
                }
                
                // Masquer les éléments mobiles
                const mobileTopBar = document.getElementById('mobile-top-bar');
                const mobileBottomNav = document.getElementById('mobile-bottom-nav');
                if (mobileTopBar) mobileTopBar.style.display = 'none';
                if (mobileBottomNav) mobileBottomNav.style.display = 'none';
            }
            
            // Améliorer le fonctionnement du bouton toggle Bootstrap
            const navbarToggler = document.querySelector('.navbar-toggler');
            if (navbarToggler && navbarCollapse) {
                navbarToggler.addEventListener('click', function() {
                    console.log('🔘 Toggle navbar cliqué');
                    setTimeout(() => {
                        const isShown = navbarCollapse.classList.contains('show');
                        console.log('📋 État navbar-collapse:', isShown ? 'OUVERT' : 'FERMÉ');
                    }, 100);
                });
            }
            
            // Animation des notifications
            const notificationBadge = document.querySelector('.badge');
            if (notificationBadge) {
                notificationBadge.addEventListener('click', function() {
                    this.style.animation = 'pulse 0.5s ease-in-out';
                    setTimeout(() => {
                        this.style.animation = '';
                    }, 500);
                });
            }

            // Amélioration de la navigation active
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            // Tooltip pour les icônes
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Gestion des notifications en temps réel
            <?php if(auth()->guard()->check()): ?>
            let notificationCount = 0;
            let notificationInterval;

            function loadNotifications() {
                fetch('/notifications')
                    .then(response => response.json())
                    .then(data => {
                        updateNotificationBadge(data.count);
                        updateNotificationList(data.notifications);
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des notifications:', error);
                    });
            }

            function updateNotificationBadge(count) {
                const badge = document.getElementById('notifications-badge');
                if (badge) {
                    if (count > 0) {
                        badge.textContent = count;
                        badge.style.display = 'block';
                        notificationCount = count;
                    } else {
                        badge.style.display = 'none';
                        notificationCount = 0;
                    }
                }
            }

            function updateNotificationList(notifications) {
                const list = document.getElementById('notifications-list');
                if (!list) return;

                // Supprimer les éléments existants sauf le header
                const header = list.querySelector('.dropdown-header');
                list.innerHTML = '';
                list.appendChild(header);

                if (notifications.length === 0) {
                    const noNotificationItem = document.createElement('li');
                    noNotificationItem.innerHTML = '<div class="dropdown-item text-center text-muted">Aucune notification</div>';
                    list.appendChild(noNotificationItem);
                } else {
                    notifications.forEach(notification => {
                        const item = document.createElement('li');
                        const notificationUrl = notification.url || '/messages';
                        item.innerHTML = `
                            <a class="dropdown-item notification-item" href="${notificationUrl}" data-notification-id="${notification.id}">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-bell text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <div class="fw-bold">${notification.title}</div>
                                        <div class="small text-muted">${notification.message}</div>
                                        <div class="small text-muted">${new Date(notification.created_at).toLocaleString()}</div>
                                    </div>
                                </div>
                            </a>
                        `;
                        list.appendChild(item);
                    });

                    // Ajouter un lien "Voir toutes"
                    const viewAllItem = document.createElement('li');
                    viewAllItem.innerHTML = '<hr class="dropdown-divider">';
                    list.appendChild(viewAllItem);

                    const viewAllLink = document.createElement('li');
                    viewAllLink.innerHTML = '<a class="dropdown-item text-center" href="/messages">Voir toutes</a>';
                    list.appendChild(viewAllLink);
                }
            }

            function markNotificationAsRead(notificationId) {
                fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications(); // Recharger les notifications
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du marquage de la notification:', error);
                });
            }

            // Gestionnaire de clic sur les notifications
            document.addEventListener('click', function(e) {
                if (e.target.closest('.notification-item')) {
                    const notificationItem = e.target.closest('.notification-item');
                    const notificationId = notificationItem.getAttribute('data-notification-id');
                    const notificationUrl = notificationItem.getAttribute('href');
                    
                    // Marquer comme lue et rediriger
                    markNotificationAsRead(notificationId);
                    
                    // Permettre la redirection naturelle si l'URL n'est pas "#"
                    if (notificationUrl && notificationUrl !== '#') {
                        // La redirection naturelle du lien se fera
                        return true;
                    } else {
                        e.preventDefault();
                    }
                }
            });

            // Charger les notifications au chargement de la page
            loadNotifications();

            // Actualiser les notifications toutes les 10 secondes
            notificationInterval = setInterval(loadNotifications, 10000);

            // Arrêter l'intervalle quand la page n'est plus visible
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    clearInterval(notificationInterval);
                } else {
                    notificationInterval = setInterval(loadNotifications, 10000);
                }
            });
            <?php endif; ?>

            // Gestion spéciale des catégories
            initializeCategoryFeatures();
        });

        // Fonctions spécifiques aux catégories
        function initializeCategoryFeatures() {
            // Animation des cartes de catégories
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Recherche live dans les catégories
            const categorySearch = document.getElementById('category-search');
            if (categorySearch) {
                categorySearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const categoryItems = document.querySelectorAll('.category-list-item');
                    
                    categoryItems.forEach(item => {
                        const categoryName = item.querySelector('.category-name')?.textContent.toLowerCase() || '';
                        const categoryDescription = item.querySelector('.category-description')?.textContent.toLowerCase() || '';
                        
                        if (categoryName.includes(searchTerm) || categoryDescription.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Gestion des sous-catégories expandables
            const expandableCategories = document.querySelectorAll('.expandable-category');
            expandableCategories.forEach(category => {
                const toggle = category.querySelector('.category-toggle');
                const subcategories = category.querySelector('.subcategories');
                
                if (toggle && subcategories) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        const isExpanded = subcategories.style.display === 'block';
                        subcategories.style.display = isExpanded ? 'none' : 'block';
                        
                        const icon = toggle.querySelector('i');
                        if (icon) {
                            icon.className = isExpanded ? 'fas fa-chevron-right' : 'fas fa-chevron-down';
                        }
                    });
                }
            });

            // Préchargement des catégories populaires
            if (window.location.pathname === '/categories') {
                preloadPopularCategories();
            }
        }

        function preloadPopularCategories() {
            // Précharger les images des catégories les plus populaires
            fetch('/api/categories/popular')
                .then(response => response.json())
                .then(categories => {
                    categories.forEach(category => {
                        if (category.image) {
                            const img = new Image();
                            img.src = category.image;
                        }
                    });
                })
                .catch(error => {
                    console.log('Préchargement des catégories non disponible');
                });
        }
        </script>

      
        <script>
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
        function handleSystemThemeChange(e) {
            const current = getPreferredTheme();
            if (current === 'auto') {
                applyTheme('auto');
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            applyTheme(getPreferredTheme());
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', handleSystemThemeChange);
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    let current = getPreferredTheme();
                    let next = current === 'auto' ? 'light' : current === 'light' ? 'dark' : 'auto';
                    applyTheme(next);
                    localStorage.setItem('theme', next);
                    if (window.isAuthenticated) {
                        fetch('/profile/theme', {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ theme_preference: next })
                        });
                    }
                });
            }
        });
        </script>

        <style>
        /* Bande violette mobile en haut */
        #mobile-top-bar {
            display: flex;
            align-items: center;
            background: rgb(79, 0, 206);
            height: 48px;
            width: 100vw;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1100;
            box-shadow: 0 2px 8px rgba(79,0,206,0.07);
        }
        .mobile-top-bar-content {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-left: 18px;
            padding-right: 12px;
        }
        .mobile-profile-link {
            text-decoration: none;
            color: #fff;
            font-size: 1rem;
            font-weight: 500;
        }
        .mobile-profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background: #eee;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        /* .mobile-profile-name { display: none; } */
        .mobile-app-name {
            color: #fff;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        @media (min-width: 768px) {
            #mobile-top-bar {
                display: none !important;
            }
        }
        /* Décale le contenu principal vers le bas sur mobile pour ne pas être caché par la top bar */
        @media (max-width: 767.98px) {
            main.min-vh-100 {
                padding-top: 56px;
            }
        }
        
        /* CORRECTION NAVBAR - Solution complète pour mobile/desktop */
        
        /* 1. Masquer navbar UNIQUEMENT sur vrais mobiles (tactiles) */
        @media (max-width: 767.98px) and (hover: none) and (pointer: coarse) {
            nav.top-navbar {
                display: none !important;
            }
        }
        
        /* 2. FORCER l'affichage navbar sur écrans ≥ 768px (desktop + mobile mode desktop) */
        @media (min-width: 768px) {
            nav.top-navbar {
                display: block !important;
            }
            .navbar-collapse {
                display: flex !important;
            }
            #mobile-top-bar,
            #mobile-bottom-nav {
                display: none !important;
            }
        }
        
        /* 3. Assurer le collapse Bootstrap fonctionne */
        @media (max-width: 991.98px) {
            .navbar-collapse.collapse:not(.show) {
                display: none !important;
            }
            .navbar-collapse.collapse.show {
                display: block !important;
            }
        }

        /* Barre de navigation mobile (bottom nav) */
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
            position: relative;
        }
        .bottom-nav-link i {
            font-size: 22px;
            margin-bottom: 2px;
        }
        .mobile-profile-initials {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 600;
            margin-left: 2px;
            letter-spacing: 0.5px;
        }
        .bottom-nav-link.active,
        .bottom-nav-link:active,
        .bottom-nav-link:focus {
            color: rgb(79, 0, 206);
        }
        .bottom-nav-link.active i {
            color: rgb(79, 0, 206);
        }
        .bottom-nav-link span {
            font-size: 11px;
            margin-top: 2px;
        }
        @media (min-width: 768px) {
            #mobile-bottom-nav {
                display: none !important;
            }
        }

         /* Styles Vinted Violet personnalisés */
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
        }

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
            border-radius: 0;
        }

        .dropdown-item:hover {
            background-color: var(--vinted-accent);
            color: var(--vinted-primary);
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            color: var(--vinted-gray-400);
        }

        /* Animation pour les notifications */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* Responsive - Large screens */
        @media (max-width: 1200px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .navbar .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Responsive - Medium screens */
        @media (max-width: 992px) {
            .navbar-brand {
                font-size: 1.25rem;
            }
            
            .navbar-nav {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .navbar-collapse {
                background: rgba(255, 255, 255, 0.05);
                border-radius: 0.5rem;
                padding: 1rem;
                margin-top: 0.5rem;
            }
            
            .input-group {
                min-width: auto !important;
                margin-bottom: 1rem;
            }
            
            .navbar-nav .nav-link {
                padding: 0.75rem 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .navbar-nav .nav-link:last-child {
                border-bottom: none;
            }
            
            /* Footer responsive */
            footer .row {
                text-align: center;
            }
            
            footer .col-md-4,
            footer .col-md-2 {
                margin-bottom: 2rem;
            }
            
            footer .col-md-4:last-child,
            footer .col-md-2:last-child {
                margin-bottom: 0;
            }
        }

        /* Responsive - Small screens */
        @media (max-width: 768px) {
            .navbar {
                padding: 0.5rem 0;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .navbar-toggler {
                border: none;
                padding: 0.25rem 0.5rem;
                font-size: 1rem;
            }
            
            .navbar-toggler:focus {
                box-shadow: none;
            }
            
            .navbar-nav {
                margin-top: 0.5rem;
                padding-top: 0.5rem;
            }
            
            .navbar-nav .nav-link {
                padding: 0.5rem 0;
                font-size: 0.9rem;
            }
            
            .input-group {
                margin-bottom: 0.5rem;
            }
            
            .input-group .form-control {
                font-size: 16px; /* Évite le zoom sur iOS */
            }
            
            .dropdown-menu {
                position: static !important;
                float: none;
                width: 100%;
                margin-top: 0.5rem;
                border-radius: 0.5rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }
            
            .dropdown-item {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
            
            /* Breadcrumb mobile */
            .breadcrumb {
                font-size: 0.8rem;
                padding: 0.5rem 0;
            }
            
            .breadcrumb-item {
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            /* Footer mobile */
            footer {
                padding: 2rem 0;
            }
            
            footer .row {
                text-align: center;
            }
            
            footer .col-md-4,
            footer .col-md-2 {
                margin-bottom: 1.5rem;
            }
            
            footer h5,
            footer h6 {
                margin-bottom: 1rem;
            }
            
            footer .d-flex.gap-2 {
                justify-content: center;
            }
        }

        /* Responsive - Extra small screens */
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1rem;
            }
            
            .navbar-nav .nav-link {
                font-size: 0.85rem;
                padding: 0.5rem 0;
            }
            
            .input-group {
                margin-bottom: 0.5rem;
            }
            
            .input-group .form-control {
                font-size: 16px;
                padding: 0.5rem 0.75rem;
            }
            
            .input-group .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
            }
            
            .dropdown-item {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }
            
            /* Breadcrumb très mobile */
            .breadcrumb {
                font-size: 0.75rem;
                padding: 0.25rem 0;
            }
            
            .breadcrumb-item {
                max-width: 100px;
            }
            
            /* Footer très mobile */
            footer {
                padding: 1.5rem 0;
            }
            
            footer .container {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            footer .row {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }
            
            footer .col-md-4,
            footer .col-md-2 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
                margin-bottom: 1rem;
            }
            
            footer h5 {
                font-size: 1rem;
            }
            
            footer h6 {
                font-size: 0.9rem;
            }
            
            footer p,
            footer li,
            footer small {
                font-size: 0.8rem;
            }
        }

        /* Responsive - Extra extra small screens */
        @media (max-width: 480px) {
            .navbar {
                padding: 0.25rem 0;
            }
            
            .navbar-brand {
                font-size: 0.9rem;
            }
            
            .navbar-nav .nav-link {
                font-size: 0.8rem;
                padding: 0.375rem 0;
            }
            
            .input-group .form-control {
                font-size: 16px;
                padding: 0.375rem 0.5rem;
            }
            
            .input-group .btn {
                padding: 0.375rem 0.5rem;
                font-size: 0.8rem;
            }
            
            .dropdown-item {
                padding: 0.375rem 0.5rem;
                font-size: 0.8rem;
            }
            
            /* Breadcrumb extra mobile */
            .breadcrumb {
                font-size: 0.7rem;
                padding: 0.25rem 0;
            }
            
            .breadcrumb-item {
                max-width: 80px;
            }
            
            /* Footer extra mobile */
            footer {
                padding: 1rem 0;
            }
            
            footer .container {
                padding-left: 0.25rem;
                padding-right: 0.25rem;
            }
            
            footer .row {
                margin-left: -0.25rem;
                margin-right: -0.25rem;
            }
            
            footer .col-md-4,
            footer .col-md-2 {
                padding-left: 0.25rem;
                padding-right: 0.25rem;
                margin-bottom: 0.75rem;
            }
            
            footer h5 {
                font-size: 0.9rem;
            }
            
            footer h6 {
                font-size: 0.8rem;
            }
            
            footer p,
            footer li,
            footer small {
                font-size: 0.75rem;
            }
        }

        /* Styles pour les écrans tactiles */
        @media (hover: none) and (pointer: coarse) {
            .nav-link {
                min-height: 44px;
                display: flex;
                align-items: center;
            }
            
            .dropdown-item {
                min-height: 44px;
                display: flex;
                align-items: center;
            }
            
            .btn {
                min-height: 44px;
            }
            
            .form-control,
            .form-select {
                min-height: 44px;
            }
        }

        /* Styles pour les écrans haute densité */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .navbar {
                border-width: 0.5px;
            }
            
            .card {
                border-width: 0.5px;
            }
            
            .btn {
                border-width: 0.5px;
            }
        }

        /* Styles pour les écrans en mode paysage sur mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .navbar {
                padding: 0.25rem 0;
            }
            
            .navbar-brand {
                font-size: 0.9rem;
            }
            
            .navbar-nav {
                margin-top: 0.25rem;
                padding-top: 0.25rem;
            }
            
            .navbar-nav .nav-link {
                padding: 0.25rem 0;
                font-size: 0.8rem;
            }
        }

        /* Effets de hover pour les cartes */
        .card.hover-lift {
            transition: all 0.2s ease;
        }

        .card.hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        /* Boutons avec effet de lift */
        .btn.hover-lift {
            transition: all 0.2s ease;
        }

        .btn.hover-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }

        /* Amélioration de l'accessibilité */
        .nav-link:focus,
        .dropdown-item:focus,
        .btn:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }

        /* Amélioration de la lisibilité */
        @media (prefers-reduced-motion: reduce) {
            .nav-link,
            .dropdown-item,
            .btn,
            .card {
                transition: none;
            }
        }

        /* Styles spécifiques pour les catégories */
        .category-card {
            transition: all 0.3s ease;
            border: 1px solid var(--bs-border-color);
        }

        .category-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border-color: var(--vinted-primary);
        }

        .category-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--vinted-primary), var(--vinted-secondary));
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .category-stats {
            font-size: 0.875rem;
            color: var(--bs-text-muted);
        }

        .category-list-item {
            transition: all 0.2s ease;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .category-list-item:hover {
            background-color: var(--bs-light);
            transform: translateX(4px);
        }

        .subcategory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .subcategory-grid {
                grid-template-columns: 1fr;
            }
            
            .category-icon {
                width: 40px;
                height: 40px;
                font-size: 1.25rem;
            }
        }
        </style>

        <!-- Composant Notifications Temps Réel -->
        <?php if(auth()->guard()->check()): ?>
        <div id="realtime-notification-container" style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;"></div>
        
        <script type="module">
            // Notifications en temps réel avec Laravel Echo
            if (window.Echo && <?php echo e(Auth::id()); ?>) {
                console.log('🔔 Initialisation des notifications temps réel pour l\'utilisateur <?php echo e(Auth::id()); ?>');
                
                // Écouter le canal privé de l'utilisateur
                window.Echo.private('user.<?php echo e(Auth::id()); ?>')
                    .listen('.order.notification', (data) => {
                        console.log('📬 Notification reçue:', data);
                        showRealtimeNotification(data);
                        
                        // Mettre à jour le compteur de notifications
                        updateNotificationBadge();
                        
                        // Jouer un son (optionnel)
                        playNotificationSound();
                    })
                    .error((error) => {
                        console.error('❌ Erreur canal notifications:', error);
                    });

                // Fonction pour afficher la notification
                function showRealtimeNotification(data) {
                    const container = document.getElementById('realtime-notification-container');
                    if (!container) return;

                    // Icône selon le type de notification
                    const icons = {
                        'new_order': '<i class="fas fa-shopping-cart text-primary"></i>',
                        'payment_confirmed': '<i class="fas fa-check-circle text-success"></i>',
                        'order_shipped': '<i class="fas fa-shipping-fast text-info"></i>',
                        'order_delivered': '<i class="fas fa-box-check text-success"></i>',
                        'order_completed': '<i class="fas fa-star text-warning"></i>'
                    };

                    // Couleurs selon le type
                    const colors = {
                        'new_order': 'primary',
                        'payment_confirmed': 'success',
                        'order_shipped': 'info',
                        'order_delivered': 'success',
                        'order_completed': 'warning'
                    };

                    const icon = icons[data.type] || '<i class="fas fa-bell text-secondary"></i>';
                    const color = colors[data.type] || 'secondary';

                    // Créer la notification
                    const notification = document.createElement('div');
                    notification.className = `alert alert-${color} alert-dismissible fade show shadow-lg mb-3`;
                    notification.style.cssText = 'animation: slideInRight 0.5s ease; border-left: 4px solid currentColor;';
                    notification.innerHTML = `
                        <div class="d-flex align-items-start">
                            <div class="me-3" style="font-size: 1.5rem;">
                                ${icon}
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-1">${data.message}</h6>
                                <small class="d-block text-muted">
                                    <strong>Commande #${data.order_number}</strong><br>
                                    ${data.item_name} - ${data.total_amount} ${data.currency}
                                </small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;

                    container.appendChild(notification);

                    // Auto-fermeture après 8 secondes
                    setTimeout(() => {
                        notification.classList.remove('show');
                        setTimeout(() => notification.remove(), 500);
                    }, 8000);

                    // Click pour voir la commande
                    notification.style.cursor = 'pointer';
                    notification.addEventListener('click', (e) => {
                        if (!e.target.classList.contains('btn-close')) {
                            window.location.href = `/orders/${data.order_id}`;
                        }
                    });
                }

                // Mettre à jour le badge de compteur
                function updateNotificationBadge() {
                    const badge = document.querySelector('#notificationsDropdown .badge');
                    if (badge) {
                        const currentCount = parseInt(badge.textContent) || 0;
                        badge.textContent = currentCount + 1;
                        badge.classList.remove('d-none');
                    }
                }

                // Son de notification (optionnel)
                function playNotificationSound() {
                    try {
                        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvPTgjoIGWS45+mjUBELTKXh6rdmHgU2jdXxzYAuBSh+zPLaizsIHGe96+mnUhILP5vd78hwKAUsgc3z1oU6CBxmvO3qp1MRDEyj4Oq4aiIFNozT8c6GMAUqf83y2Ik4CBtlu+zpp1USCz6Z3O7LcisEK4DN8daFOggcZrvs6qdUEAw/m9vvy3IrBSh+zfPYiTgIG2a77OilVBILP5nb78txLAUogM3z1oU6CBxmvO3qp1MRDUqj4Oq4aiIENozT8c6HLwUpfs3y2Ik4CBtlu+zpp1QSDECb3O7McisFKIDN89aGOgcbZrvt6qhVEgxNo+Dqt2ogBTWM0/HOhzEFKYDN8tiIOgcbZLvs66dUEgxAm9zuy3IsJCh+zPPYiToIG2S77OqmUhEMT6Ph6bllHwU3i9Pwy4YwBSh+zfLZiTkIG2S76+unVBIMQJrc78txLAUof8zz2Io5CBtkuuvqp1QSDEyj4Oq2Zh4FNY3T8c+HLwUpfszy2Ik6BxtkuuzqplISC0yj4Oq3Zh4FNY3T8c+GLgUpfszy2Ik5CBxku+vqplUSDE2k4eu4aR0FNYzS8c6DLwUpf8zy2Ik5CBxku+zqqFURDEyj4eu3ZR4FNo3S8c6HMQUpfszy2Ig5CBxku+zqp1MRDEyk4eu3Zh4FNY3S8c6HLgUpfszy2Yo5CBxku+vrp1MSC0uk4Ou3Zh4FNY3S8c6HLgUpf83y2Yk5CBtlu+zqp1QSDEyk4eu3Zh4FNY3S8c6HLgUpf83y2Yk5CBtlvOzqp1QRDEuk4Ou1ZR4FNo3S8c6FLgUpf83y2Yk6CBtlvOzqp1QRC0qj4eu1ZR4FNo3S8c2FLgUpf8zy2Yk6BxplvOzpp1QRDEqj4euyZB4FNozS8c6FLgUpgM3y2Ik6BxplvOzqp1QRDEuj4euyZB4FNo3S8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euyZB4FNo3S8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euuZB4FNozS8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euuZB4FNozS8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euuZB4FNozS8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euuZB4FNozS8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euuZB4FNozS8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euuZB4FNozS8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euuZB4FNozS8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euuZB4FNozS8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euuZB4FNozS8c6FMQUpgM3y2Ik6BxplvOzqp1MRDEqj4euuZB4=');
                        audio.volume = 0.3;
                        audio.play().catch(() => {}); // Ignore si l'utilisateur n'a pas interagi
                    } catch (e) {}
                }

                console.log('✅ Notifications temps réel activées');
            }
        </script>

        <!-- Script Newsletter -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const newsletterForm = document.getElementById('newsletterForm');
                const newsletterMessage = document.getElementById('newsletterMessage');
                
                if (newsletterForm) {
                    newsletterForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        
                        const email = document.getElementById('newsletterEmail').value;
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalBtnText = submitBtn.innerHTML;
                        
                        // Désactiver le bouton pendant l'envoi
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Envoi...';
                        
                        try {
                            const response = await fetch('<?php echo e(route("newsletter.subscribe")); ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ email: email })
                            });
                            
                            const data = await response.json();
                            
                            if (data.success) {
                                newsletterMessage.innerHTML = `
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>${data.message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                `;
                                newsletterForm.reset();
                            } else {
                                newsletterMessage.innerHTML = `
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>${data.message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                `;
                            }
                        } catch (error) {
                            newsletterMessage.innerHTML = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>Une erreur est survenue. Veuillez réessayer.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                        }
                        
                        // Réactiver le bouton
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        
                        // Auto-masquer le message après 5 secondes
                        setTimeout(() => {
                            newsletterMessage.innerHTML = '';
                        }, 5000);
                    });
                }
            });
        </script>

        <style>
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            #realtime-notification-container .alert {
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            #realtime-notification-container .alert:hover {
                transform: scale(1.02);
                box-shadow: 0 8px 16px rgba(0,0,0,0.2) !important;
            }
        </style>
        <?php endif; ?>

        <!-- Widget d'assistance : affiché uniquement sur desktop -->
        <div class="d-none d-md-block">
            <?php echo $__env->make('support.widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/app.blade.php ENDPATH**/ ?>