@extends('layouts.admin')

@section('title', 'Paramètres de pré-inscription')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .setting-card {
        transition: all 0.3s;
    }
    
    .setting-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .form-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
        cursor: pointer;
    }
    
    .form-switch .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
    
    .badge-enabled {
        background-color: #10b981;
        color: white;
    }
    
    .badge-disabled {
        background-color: #ef4444;
        color: white;
    }
    
    .benefit-item {
        background: #f9fafb;
        border-left: 3px solid #6366f1;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 0.375rem;
    }
    
    .btn-add-benefit {
        border: 2px dashed #d1d5db;
        color: #6b7280;
        transition: all 0.2s;
    }
    
    .btn-add-benefit:hover {
        border-color: #6366f1;
        color: #6366f1;
        background: #f0f4ff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">
                                <i class="fas fa-cog me-2 text-primary"></i>
                                Paramètres de pré-inscription
                            </h2>
                            <p class="text-muted mb-0">Configurez les options de la page de pré-inscription</p>
                        </div>
                        <div>
                            <a href="{{ route('preregistration.index') }}" class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>Voir la page
                            </a>
                            <a href="{{ route('admin.waiting-users.index') }}" class="btn btn-primary">
                                <i class="fas fa-users me-2"></i>Gérer les inscriptions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.preregistration.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Statut de la pré-inscription -->
            <div class="col-md-12 mb-4">
                <div class="card setting-card shadow-sm">
                    <div class="card-header bg-gradient-to-r from-primary-500 to-primary-600" style="background: linear-gradient(to right, #6366f1, #4f46e5);">
                        <h5 class="text-white mb-0">
                            <i class="fas fa-toggle-on me-2"></i>Statut de la pré-inscription
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $isEnabled = Setting::get('preregistration_enabled', false);
                        @endphp
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Activer la pré-inscription</h6>
                                <p class="text-muted small mb-0">
                                    Les utilisateurs {{ $isEnabled ? 'peuvent' : 'ne peuvent pas' }} actuellement s'inscrire
                                </p>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="preregistration_enabled" 
                                       name="preregistration_enabled"
                                       value="1"
                                       {{ $isEnabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="preregistration_enabled">
                                    <span class="badge {{ $isEnabled ? 'badge-enabled' : 'badge-disabled' }}" id="status-badge">
                                        {{ $isEnabled ? 'Activée' : 'Désactivée' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu de la page -->
            <div class="col-md-6 mb-4">
                <div class="card setting-card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2 text-primary"></i>Contenu de la page
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="preregistration_title" class="form-label">
                                <i class="fas fa-heading me-1"></i>Titre principal
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="preregistration_title" 
                                   name="preregistration_title" 
                                   value="{{ Setting::get('preregistration_title', 'Rejoignez-nous en avant-première !') }}"
                                   placeholder="Rejoignez-nous en avant-première !">
                        </div>

                        <div class="mb-3">
                            <label for="preregistration_subtitle" class="form-label">
                                <i class="fas fa-text-height me-1"></i>Sous-titre
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="preregistration_subtitle" 
                                   name="preregistration_subtitle" 
                                   value="{{ Setting::get('preregistration_subtitle', 'Inscrivez-vous maintenant...') }}"
                                   placeholder="Inscrivez-vous maintenant...">
                        </div>

                        <div class="mb-3">
                            <label for="preregistration_message" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Message d'accueil
                            </label>
                            <textarea class="form-control" 
                                      id="preregistration_message" 
                                      name="preregistration_message" 
                                      rows="4"
                                      placeholder="Nous préparons quelque chose de spécial...">{{ Setting::get('preregistration_message', 'Nous préparons quelque chose de spécial...') }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label for="preregistration_closed_message" class="form-label">
                                <i class="fas fa-ban me-1"></i>Message de fermeture
                            </label>
                            <textarea class="form-control" 
                                      id="preregistration_closed_message" 
                                      name="preregistration_closed_message" 
                                      rows="3"
                                      placeholder="Les pré-inscriptions sont fermées...">{{ Setting::get('preregistration_closed_message', 'Les pré-inscriptions sont fermées...') }}</textarea>
                            <small class="text-muted">Affiché lorsque la pré-inscription est désactivée</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avantages -->
            <div class="col-md-6 mb-4">
                <div class="card setting-card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-gift me-2 text-success"></i>Avantages de la pré-inscription
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="benefits-container">
                            @php
                                $benefits = Setting::get('preregistration_benefits', []);
                                if (is_string($benefits)) {
                                    $benefits = json_decode($benefits, true) ?? [];
                                }
                            @endphp
                            
                            @forelse($benefits as $index => $benefit)
                                <div class="benefit-item mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               name="preregistration_benefits[]" 
                                               value="{{ $benefit }}"
                                               placeholder="Avantage {{ $index + 1 }}">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.parentElement.remove()">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="benefit-item mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               name="preregistration_benefits[]" 
                                               value="Accès prioritaire lors du lancement"
                                               placeholder="Avantage 1">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.parentElement.remove()">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <button type="button" class="btn btn-add-benefit w-100 mt-2" onclick="addBenefit()">
                            <i class="fas fa-plus me-2"></i>Ajouter un avantage
                        </button>
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="col-md-6 mb-4">
                <div class="card setting-card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-sliders-h me-2 text-warning"></i>Options du formulaire
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="preregistration_require_phone" 
                                       name="preregistration_require_phone"
                                       value="1"
                                       {{ Setting::get('preregistration_require_phone', false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="preregistration_require_phone">
                                    <strong>Téléphone obligatoire</strong>
                                    <br>
                                    <small class="text-muted">Les utilisateurs doivent fournir un numéro de téléphone</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="preregistration_require_confirmation" 
                                       name="preregistration_require_confirmation"
                                       value="1"
                                       {{ Setting::get('preregistration_require_confirmation', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="preregistration_require_confirmation">
                                    <strong>Confirmation email obligatoire</strong>
                                    <br>
                                    <small class="text-muted">Les utilisateurs doivent confirmer leur email</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="preregistration_limit" class="form-label">
                                <i class="fas fa-users me-1"></i>Limite de pré-inscriptions
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="preregistration_limit" 
                                   name="preregistration_limit" 
                                   value="{{ Setting::get('preregistration_limit', 0) }}"
                                   min="0"
                                   placeholder="0 = illimité">
                            <small class="text-muted">Nombre maximum de pré-inscriptions (0 = illimité)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="col-md-6 mb-4">
                <div class="card setting-card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-bell me-2 text-info"></i>Notifications
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-0">
                            <label for="preregistration_notification_email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email de notification admin
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="preregistration_notification_email" 
                                   name="preregistration_notification_email" 
                                   value="{{ Setting::get('preregistration_notification_email', 'admin@vintapp.com') }}"
                                   placeholder="admin@vintapp.com">
                            <small class="text-muted">Recevra les notifications de nouvelles pré-inscriptions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Enregistrer les paramètres
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle du statut avec mise à jour du badge
    document.getElementById('preregistration_enabled').addEventListener('change', function() {
        const badge = document.getElementById('status-badge');
        if (this.checked) {
            badge.textContent = 'Activée';
            badge.classList.remove('badge-disabled');
            badge.classList.add('badge-enabled');
        } else {
            badge.textContent = 'Désactivée';
            badge.classList.remove('badge-enabled');
            badge.classList.add('badge-disabled');
        }
    });

    // Ajouter un nouvel avantage
    function addBenefit() {
        const container = document.getElementById('benefits-container');
        const count = container.querySelectorAll('.benefit-item').length + 1;
        
        const benefitHtml = `
            <div class="benefit-item mb-2">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-check-circle text-success"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           name="preregistration_benefits[]" 
                           placeholder="Avantage ${count}">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', benefitHtml);
    }
</script>
@endpush
