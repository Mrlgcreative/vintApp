@extends('app')

@section('title', 'Paramètres')

@section('content')
<div class="container py-4">
    <!-- En-tête -->
    <div class="mb-4">
        <div class="d-flex align-items-center mb-2">
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-light me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0">Paramètres</h1>
        </div>
        <p class="text-muted mb-0">Gérez vos préférences et votre compte</p>
    </div>

    <!-- Profil -->
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" 
                         alt="{{ Auth::user()->name }}" 
                         class="rounded-circle me-3"
                         style="width: 64px; height: 64px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                         style="width: 64px; height: 64px; font-size: 1.5rem; font-weight: 600;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted mb-0 small">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section : Mon compte -->
    <div class="settings-section mb-4">
        <h6 class="text-muted text-uppercase small fw-bold mb-3">
            <i class="fas fa-user-circle me-2"></i>Mon compte
        </h6>
        
        <div class="card shadow-sm border-0">
            <div class="list-group list-group-flush">
                <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Modifier mon profil</div>
                        <small class="text-muted">Informations personnelles, photo, etc.</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>
                
                <a href="{{ route('items.personalization') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Personnalisation</div>
                        <small class="text-muted">Préférences d'affichage et notifications</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>

                <div class="list-group-item d-flex align-items-center py-3" id="theme-toggle-item" style="cursor: pointer;">
                    <div class="settings-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="fas fa-adjust"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Thème d'affichage</div>
                        <small class="text-muted">Clair, Sombre ou Automatique</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span id="current-theme-label" class="badge bg-secondary me-2">Auto</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section : Navigation rapide -->
    <div class="settings-section mb-4">
        <h6 class="text-muted text-uppercase small fw-bold mb-3">
            <i class="fas fa-compass me-2"></i>Navigation rapide
        </h6>
        
        <div class="card shadow-sm border-0">
            <div class="list-group list-group-flush">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-info bg-opacity-10 text-info me-3">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Dashboard</div>
                        <small class="text-muted">Vue d'ensemble de votre activité</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-danger bg-opacity-10 text-danger me-3">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Mes commandes</div>
                        <small class="text-muted">Historique de vos achats</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('orders.my-sales') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Mes ventes</div>
                        <small class="text-muted">Articles que vous avez vendus</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('items.my-items') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Mes articles</div>
                        <small class="text-muted">Articles en vente</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('wallet.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Mon portefeuille</div>
                        <small class="text-muted">Solde et transactions</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-info bg-opacity-10 text-info me-3">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Messages</div>
                        <small class="text-muted">Conversations avec les vendeurs/acheteurs</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Section : Catalogue -->
    <div class="settings-section mb-4">
        <h6 class="text-muted text-uppercase small fw-bold mb-3">
            <i class="fas fa-store me-2"></i>Catalogue
        </h6>
        
        <div class="card shadow-sm border-0">
            <div class="list-group list-group-flush">
                <a href="{{ route('brands.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-danger bg-opacity-10 text-danger me-3">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Marques</div>
                        <small class="text-muted">Explorer les marques disponibles</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Catégories</div>
                        <small class="text-muted">Parcourir par catégorie</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('items.index') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Tous les articles</div>
                        <small class="text-muted">Explorer le catalogue complet</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Section : Aide & Support -->
    <div class="settings-section mb-4">
        <h6 class="text-muted text-uppercase small fw-bold mb-3">
            <i class="fas fa-question-circle me-2"></i>Aide & Support
        </h6>
        
        <div class="card shadow-sm border-0">
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-info bg-opacity-10 text-info me-3">
                        <i class="fas fa-life-ring"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Centre d'aide</div>
                        <small class="text-muted">FAQ et guides d'utilisation</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>

                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Nous contacter</div>
                        <small class="text-muted">Envoyez-nous un message</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>

                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="settings-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">Conditions d'utilisation</div>
                        <small class="text-muted">CGU et politique de confidentialité</small>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Section : Déconnexion -->
    <div class="settings-section mb-5">
        <div class="card shadow-sm border-0 border-danger">
            <div class="list-group list-group-flush">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="list-group-item list-group-item-action d-flex align-items-center py-3 text-danger border-0 bg-transparent">
                        <div class="settings-icon bg-danger bg-opacity-10 text-danger me-3">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Se déconnecter</div>
                            <small class="text-muted">Déconnexion de votre compte</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Version de l'application -->
    <div class="text-center text-muted mb-5">
        <small>
            {{ config('app.name') }} v1.0.0<br>
            © {{ date('Y') }} Tous droits réservés
        </small>
    </div>
</div>

<!-- Modal de sélection du thème -->
<div class="modal fade" id="themeModal" tabindex="-1" aria-labelledby="themeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="themeModalLabel">
                    <i class="fas fa-adjust me-2"></i>Choisir un thème
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center theme-option" data-theme="light">
                        <div class="settings-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="fas fa-sun"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Clair</div>
                            <small class="text-muted">Thème lumineux</small>
                        </div>
                        <i class="fas fa-check text-success d-none theme-check"></i>
                    </button>
                    
                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center theme-option" data-theme="dark">
                        <div class="settings-icon bg-dark bg-opacity-10 text-dark me-3">
                            <i class="fas fa-moon"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Sombre</div>
                            <small class="text-muted">Thème foncé</small>
                        </div>
                        <i class="fas fa-check text-success d-none theme-check"></i>
                    </button>
                    
                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center theme-option" data-theme="auto">
                        <div class="settings-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="fas fa-magic"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Automatique</div>
                            <small class="text-muted">Suit les préférences système</small>
                        </div>
                        <i class="fas fa-check text-success d-none theme-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.list-group-item {
    border: none;
    transition: all 0.2s ease;
}

.list-group-item:hover {
    background-color: rgba(79, 0, 206, 0.05);
    transform: translateX(4px);
}

.list-group-item:active {
    background-color: rgba(79, 0, 206, 0.1);
}

.card {
    border-radius: 12px;
    overflow: hidden;
}

.settings-section h6 {
    letter-spacing: 0.5px;
    padding-left: 4px;
}

/* Animation pour les icônes */
.settings-icon i {
    animation: none;
}

.list-group-item:hover .settings-icon i {
    animation: pulse 0.6s ease-in-out;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    
    .settings-icon {
        width: 42px;
        height: 42px;
        font-size: 1.1rem;
    }
    
    .list-group-item {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

/* Modal thème */
.theme-option {
    cursor: pointer;
    padding: 1rem;
    border-radius: 8px !important;
    margin-bottom: 0.5rem;
}

.theme-option:hover {
    background-color: rgba(79, 0, 206, 0.08);
}

.theme-option.active {
    background-color: rgba(79, 0, 206, 0.1);
    border-color: rgb(79, 0, 206);
}

.theme-option.active .theme-check {
    display: inline-block !important;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du modal de thème
    const themeToggleItem = document.getElementById('theme-toggle-item');
    const themeModal = new bootstrap.Modal(document.getElementById('themeModal'));
    const themeOptions = document.querySelectorAll('.theme-option');
    const currentThemeLabel = document.getElementById('current-theme-label');
    
    // Ouvrir le modal
    if (themeToggleItem) {
        themeToggleItem.addEventListener('click', function() {
            themeModal.show();
            updateThemeSelection();
        });
    }
    
    // Gérer la sélection du thème
    themeOptions.forEach(option => {
        option.addEventListener('click', function() {
            const selectedTheme = this.getAttribute('data-theme');
            applyTheme(selectedTheme);
            localStorage.setItem('theme', selectedTheme);
            
            // Mettre à jour le label
            updateThemeLabel(selectedTheme);
            
            // Sauvegarder sur le serveur si authentifié
            if (window.isAuthenticated) {
                fetch('/profile/theme', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ theme_preference: selectedTheme })
                });
            }
            
            // Fermer le modal
            themeModal.hide();
        });
    });
    
    // Mettre à jour la sélection dans le modal
    function updateThemeSelection() {
        const currentTheme = getPreferredTheme();
        themeOptions.forEach(option => {
            const check = option.querySelector('.theme-check');
            if (option.getAttribute('data-theme') === currentTheme) {
                option.classList.add('active');
                check.classList.remove('d-none');
            } else {
                option.classList.remove('active');
                check.classList.add('d-none');
            }
        });
    }
    
    // Mettre à jour le label du thème
    function updateThemeLabel(theme) {
        const labels = {
            'light': 'Clair',
            'dark': 'Sombre',
            'auto': 'Auto'
        };
        if (currentThemeLabel) {
            currentThemeLabel.textContent = labels[theme] || 'Auto';
        }
    }
    
    // Initialiser le label au chargement
    updateThemeLabel(getPreferredTheme());
});
</script>
@endpush
@endsection
