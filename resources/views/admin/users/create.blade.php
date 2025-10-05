@extends('layouts.admin')

@section('title', 'Créer un utilisateur')
@section('page-title', 'Nouvel utilisateur')

@section('page-actions')
<a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Informations personnelles -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations personnelles</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" name="name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                       id="email" name="email" value="{{ old('email') }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">Téléphone</label>
                                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                                       id="phone" name="phone" value="{{ old('phone') }}">
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="date_of_birth" class="form-label">Date de naissance</label>
                                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                                @error('date_of_birth')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Biographie</label>
                                        <textarea class="form-control @error('bio') is-invalid @enderror" 
                                                  id="bio" name="bio" rows="3">{{ old('bio') }}</textarea>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Description ou biographie de l'utilisateur</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sécurité et mot de passe -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Sécurité et accès</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                           id="password" name="password" required>
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                                        <i class="fas fa-eye" id="password-eye"></i>
                                                    </button>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Minimum 8 caractères</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" 
                                                           id="password_confirmation" name="password_confirmation" required>
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                                        <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                                    <option value="">Sélectionner un rôle</option>
                                                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Utilisateur</option>
                                                    <option value="seller" {{ old('role') === 'seller' ? 'selected' : '' }}>Vendeur</option>
                                                    <option value="moderator" {{ old('role') === 'moderator' ? 'selected' : '' }}>Modérateur</option>
                                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                                </select>
                                                @error('role')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Statut du compte</label>
                                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Actif</option>
                                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                                    <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                                    <option value="banned" {{ old('status') === 'banned' ? 'selected' : '' }}>Banni</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Adresse -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Adresse</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Adresse</label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                               id="address" name="address" value="{{ old('address') }}">
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="city" class="form-label">Ville</label>
                                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                       id="city" name="city" value="{{ old('city') }}">
                                                @error('city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="postal_code" class="form-label">Code postal</label>
                                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                                       id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                                                @error('postal_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="country" class="form-label">Pays</label>
                                                <select class="form-select @error('country') is-invalid @enderror" id="country" name="country">
                                                    <option value="">Sélectionner un pays</option>
                                                    <option value="FR" {{ old('country', 'FR') === 'FR' ? 'selected' : '' }}>France</option>
                                                    <option value="BE" {{ old('country') === 'BE' ? 'selected' : '' }}>Belgique</option>
                                                    <option value="CH" {{ old('country') === 'CH' ? 'selected' : '' }}>Suisse</option>
                                                    <option value="CA" {{ old('country') === 'CA' ? 'selected' : '' }}>Canada</option>
                                                    <option value="US" {{ old('country') === 'US' ? 'selected' : '' }}>États-Unis</option>
                                                </select>
                                                @error('country')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sidebar avec avatar et options -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Photo de profil</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <div class="avatar-preview" id="avatarPreview">
                                            <div class="avatar-placeholder">
                                                <i class="fas fa-user fa-3x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                           id="avatar" name="avatar" accept="image/*">
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">JPG, PNG (max 2MB)</div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Options du compte</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="1" 
                                               id="email_verified" name="email_verified" {{ old('email_verified') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_verified">
                                            Email vérifié
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="1" 
                                               id="notifications_enabled" name="notifications_enabled" {{ old('notifications_enabled', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="notifications_enabled">
                                            Notifications activées
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="1" 
                                               id="marketing_emails" name="marketing_emails" {{ old('marketing_emails') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="marketing_emails">
                                            Emails marketing
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" 
                                               id="is_seller" name="is_seller" {{ old('is_seller') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_seller">
                                            Peut vendre des articles
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Préférences</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="language" class="form-label">Langue</label>
                                        <select class="form-select @error('language') is-invalid @enderror" id="language" name="language">
                                            <option value="fr" {{ old('language', 'fr') === 'fr' ? 'selected' : '' }}>Français</option>
                                            <option value="en" {{ old('language') === 'en' ? 'selected' : '' }}>English</option>
                                            <option value="es" {{ old('language') === 'es' ? 'selected' : '' }}>Español</option>
                                        </select>
                                        @error('language')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="timezone" class="form-label">Fuseau horaire</label>
                                        <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone">
                                            <option value="Europe/Paris" {{ old('timezone', 'Europe/Paris') === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                            <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                            <option value="America/New_York" {{ old('timezone') === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                        </select>
                                        @error('timezone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </button>
                                
                                <div>
                                    <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                                        <i class="fas fa-save me-2"></i>Créer l'utilisateur
                                    </button>
                                    <button type="submit" name="action" value="save_and_continue" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Créer et continuer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-preview {
    width: 120px;
    height: 120px;
    margin: 0 auto 20px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #dee2e6;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aperçu de l'avatar
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.innerHTML = `<img src="${e.target.result}" alt="Avatar preview">`;
            };
            reader.readAsDataURL(file);
        } else {
            avatarPreview.innerHTML = `
                <div class="avatar-placeholder">
                    <i class="fas fa-user fa-3x text-muted"></i>
                </div>
            `;
        }
    });
    
    // Validation du formulaire
    const form = document.getElementById('userForm');
    form.addEventListener('submit', function(e) {
        let valid = true;
        
        // Validation du mot de passe
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        
        if (password !== passwordConfirmation) {
            showError(document.getElementById('password_confirmation'), 'Les mots de passe ne correspondent pas');
            valid = false;
        }
        
        if (password.length < 8) {
            showError(document.getElementById('password'), 'Le mot de passe doit contenir au moins 8 caractères');
            valid = false;
        }
        
        // Validation de l'email
        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError(document.getElementById('email'), 'Adresse email invalide');
            valid = false;
        }
        
        if (!valid) {
            e.preventDefault();
        }
    });
    
    // Vérification en temps réel de la correspondance des mots de passe
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    
    function checkPasswordMatch() {
        if (passwordConfirmInput.value && passwordInput.value !== passwordConfirmInput.value) {
            passwordConfirmInput.classList.add('is-invalid');
            showError(passwordConfirmInput, 'Les mots de passe ne correspondent pas');
        } else {
            passwordConfirmInput.classList.remove('is-invalid');
            removeError(passwordConfirmInput);
        }
    }
    
    passwordInput.addEventListener('input', checkPasswordMatch);
    passwordConfirmInput.addEventListener('input', checkPasswordMatch);
    
    function showError(input, message) {
        input.classList.add('is-invalid');
        let feedback = input.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            // Gérer le cas des input-group
            const parent = input.closest('.input-group') || input.parentNode;
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            parent.appendChild(feedback);
        }
        feedback.textContent = message;
        feedback.style.display = 'block';
    }
    
    function removeError(input) {
        const feedback = input.parentNode.querySelector('.invalid-feedback') || 
                        input.closest('.input-group')?.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.style.display = 'none';
        }
    }
});

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}
</script>
@endpush