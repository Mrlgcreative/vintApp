<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VintApp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Vinted Violet CSS -->
        <link href="{{ asset('css/vinted-violet.css') }}" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <!-- Navigation principale -->
        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:rgb(79, 0, 206);">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    <i class="fas fa-store me-2"></i>
                    Vintapp
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- Navigation gauche -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('items.index') ? 'active' : '' }}" 
                               href="{{ route('items.index') }}">
                                <i class="fas fa-box me-1"></i>
                                Articles
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" 
                               href="{{ route('brands.index') }}">
                                <i class="fas fa-tags me-1"></i>
                                Marques
                            </a>
                        </li>
                        
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('items.my-items') ? 'active' : '' }}" 
                                   href="{{ route('items.my-items') }}">
                                    <i class="fas fa-list me-1"></i>
                                    Mes articles
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}" 
                                   href="{{ route('orders.index') }}">
                                    <i class="fas fa-shopping-cart me-1"></i>
                                    Mes Commandes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('orders.my-sales') ? 'active' : '' }}" 
                                   href="{{ route('orders.my-sales') }}">
                                    <i class="fas fa-store me-1"></i>
                                    Mes Ventes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" 
                                   href="{{ route('messages.index') }}">
                                    <i class="fas fa-comments me-1"></i>
                                    Messages
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Barre de recherche -->
                    <form class="d-flex me-3" method="GET" action="{{ route('items.search') }}">
                        <div class="input-group" style="min-width: 300px;">
                            <input class="form-control focus-ring" 
                                   type="search" 
                                   name="q" 
                                   placeholder="Rechercher un article..." 
                                   value="{{ request('q') }}">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Navigation droite -->
                    <ul class="navbar-nav">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" 
                                   href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    Connexion
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" 
                                   href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>
                                    Inscription
                                </a>
                            </li>
                        @else
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
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="px-3 py-2 text-muted small">Profil & Paramètres</li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-cog me-2"></i>
                                            Mon profil
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('items.personalization') }}">
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
                                    <li class="px-3 py-2 text-muted small">Ventes & Achats</li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('items.create') }}">
                                            <i class="fas fa-plus me-2"></i>
                                            Vendre un article
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('items.my-items') }}">
                                            <i class="fas fa-box me-2"></i>
                                            Mes articles
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('orders.index') }}">
                                            <i class="fas fa-shopping-cart me-2"></i>
                                            Mes commandes
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('orders.my-sales') }}">
                                            <i class="fas fa-store me-2"></i>
                                            Mes ventes
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
                                        <a class="dropdown-item" href="{{ route('messages.index') }}">
                                            <i class="fas fa-envelope me-2"></i>
                                            Messages
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>
                                                Déconnexion
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Breadcrumb -->
        @if(!request()->routeIs('welcome'))
            <nav aria-label="breadcrumb" class="bg-light py-2">
                <div class="container">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/') }}" class="text-decoration-none">
                                <i class="fas fa-home me-1"></i>
                                Accueil
                            </a>
                        </li>
                        @if(request()->routeIs('dashboard'))
                            <li class="breadcrumb-item active">Dashboard</li>
                        @elseif(request()->routeIs('items.*'))
                            <li class="breadcrumb-item">
                                <a href="{{ route('items.index') }}" class="text-decoration-none">Articles</a>
                            </li>
                            @if(request()->routeIs('items.show'))
                                <li class="breadcrumb-item active">{{ $item->name ?? 'Détails' }}</li>
                            @elseif(request()->routeIs('items.create'))
                                <li class="breadcrumb-item active">Créer un article</li>
                            @elseif(request()->routeIs('items.edit'))
                                <li class="breadcrumb-item active">Modifier un article</li>
                            @elseif(request()->routeIs('items.my-items'))
                                <li class="breadcrumb-item active">Mes articles</li>
                            @elseif(request()->routeIs('items.search'))
                                <li class="breadcrumb-item active">Recherche</li>
                            @endif
                        @elseif(request()->routeIs('profile.*'))
                            <li class="breadcrumb-item active">Profil</li>
                        @endif
                    </ol>
                </div>
            </nav>
        @endif

        <!-- Contenu principal -->
        <main class="min-vh-100">
            @yield('content')
        </main>

        <!-- Footer -->
        @if(!request()->routeIs('messages.*'))
        <footer class="bg-dark text-light py-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <h5>
                            <i class="fas fa-store me-2"></i>
                            Vintapp
                        </h5>
                        <p class="text-muted">
                            La marketplace de confiance pour acheter et vendre des articles d'occasion.
                        </p>
                    </div>
                    <div class="col-md-2">
                        <h6>Navigation</h6>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('items.index') }}" class="text-muted text-decoration-none">Articles</a></li>
                            <li><a href="{{ route('items.search') }}" class="text-muted text-decoration-none">Recherche</a></li>
                            @auth
                                <li><a href="{{ route('items.my-items') }}" class="text-muted text-decoration-none">Mes articles</a></li>
                            @endauth
                        </ul>
                    </div>
                    <div class="col-md-2">
                        <h6>Support</h6>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-muted text-decoration-none">Aide</a></li>
                            <li><a href="#" class="text-muted text-decoration-none">Contact</a></li>
                            <li><a href="#" class="text-muted text-decoration-none">FAQ</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2">
                        <h6>Légal</h6>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-muted text-decoration-none">CGU</a></li>
                            <li><a href="#" class="text-muted text-decoration-none">Confidentialité</a></li>
                            <li><a href="#" class="text-muted text-decoration-none">Cookies</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2">
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
                <hr class="my-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            © {{ date('Y') }} {{ config('app.name', 'VintApp') }}. Tous droits réservés.
                        </small>
                    </div>
                </div>
            </div>
        </footer>
        @endif

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Scripts personnalisés -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
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
            @auth
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
                        item.innerHTML = `
                            <a class="dropdown-item notification-item" href="#" data-notification-id="${notification.id}">
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
                    e.preventDefault();
                    const notificationId = e.target.closest('.notification-item').getAttribute('data-notification-id');
                    markNotificationAsRead(notificationId);
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
            @endauth
        });
        </script>

        <style>
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
        </style>
    </body>
</html>
