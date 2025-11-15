@extends('app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar du profil -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="card-title mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <div class="d-grid">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editAvatarModal">
                            <i class="fas fa-camera me-2"></i>Changer l'avatar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="h4 text-primary mb-1">{{ $stats['total_items'] }}</div>
                            <small class="text-muted">Articles</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 text-success mb-1">{{ $stats['completed_orders'] }}</div>
                            <small class="text-muted">Ventes</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 text-info mb-1">{{ $stats['total_revenue'] }}€</div>
                            <small class="text-muted">Revenus</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 text-warning mb-1">{{ number_format($stats['average_rating'], 1) }}</div>
                            <small class="text-muted">Note</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white dark:bg-gray-800">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>Général
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>Sécurité
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="preferences-tab" data-bs-toggle="tab" data-bs-target="#preferences" type="button" role="tab">
                                <i class="fas fa-cog me-2"></i>Préférences
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">
                                <i class="fas fa-bell me-2"></i>Notifications
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Onglet Général -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PATCH')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nom complet</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Localisation</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                               id="location" name="location" value="{{ old('location', $user->location) }}">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" 
                                              id="bio" name="bio" rows="3">{{ old('bio', $user->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Sauvegarder
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Onglet Sécurité -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <form method="POST" action="{{ route('profile.password') }}">
                                @csrf
                                @method('PATCH')
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mot de passe actuel</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Nouveau mot de passe</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                        <input type="password" class="form-control" 
                                               id="password_confirmation" name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-2"></i>Changer le mot de passe
                                    </button>
                                </div>
                            </form>

                            <hr class="my-4">

                            <div class="alert alert-danger">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Zone de danger
                                </h6>
                                <p class="mb-3">Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées.</p>
                                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?')">
                                    @csrf
                                    @method('DELETE')
                                    <div class="mb-3">
                                        <label for="delete_password" class="form-label">Mot de passe</label>
                                        <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                                               id="delete_password" name="password" required>
                                        @error('password', 'userDeletion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-2"></i>Supprimer le compte
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Onglet Préférences -->
                        <div class="tab-pane fade" id="preferences" role="tabpanel">
                            <form method="POST" action="{{ route('profile.theme') }}">
                                @csrf
                                @method('PATCH')
                                
                                <div class="mb-4">
                                    <label class="form-label">Thème de l'interface</label>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="card theme-option">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-adjust fa-2x text-muted mb-2"></i>
                                                    <h6>Auto</h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="theme_preference" 
                                                               id="theme_auto" value="auto" {{ $user->theme_preference === 'auto' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="theme_auto">
                                                            Suit les préférences système
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card theme-option">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-sun fa-2x text-warning mb-2"></i>
                                                    <h6>Clair</h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="theme_preference" 
                                                               id="theme_light" value="light" {{ $user->theme_preference === 'light' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="theme_light">
                                                            Thème clair
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card theme-option">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-moon fa-2x text-info mb-2"></i>
                                                    <h6>Sombre</h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="theme_preference" 
                                                               id="theme_dark" value="dark" {{ $user->theme_preference === 'dark' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="theme_dark">
                                                            Thème sombre
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Sauvegarder les préférences
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Onglet Notifications -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Historique des notifications</h6>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-check-double me-2"></i>Marquer tout comme lu
                                </button>
                            </div>
                            
                            <div class="list-group">
                                @forelse($user->notifications()->latest()->take(10)->get() as $notification)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $notification->title }}</h6>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ $notification->message }}</p>
                                        @if(!$notification->read_at)
                                            <span class="badge bg-primary">Nouveau</span>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Aucune notification</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour changer l'avatar -->
<div class="modal fade" id="editAvatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer l'avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Sélectionner une image</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Changer l'avatar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.theme-option {
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent;
}
.theme-option:hover {
    border-color: #007bff;
    transform: translateY(-2px);
}
.theme-option .form-check-input:checked + .form-check-label {
    color: #007bff;
    font-weight: 600;
}
</style>

<script>
// Gestion des onglets
document.addEventListener('DOMContentLoaded', function() {
    // Activer l'onglet depuis l'URL si spécifié
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab) {
        const tab = new bootstrap.Tab(document.querySelector(`#${activeTab}-tab`));
        tab.show();
    }
});
</script>
@endsection 