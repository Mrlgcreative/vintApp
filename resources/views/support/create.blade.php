@extends('app')

@section('title', 'Nouvelle demande de support')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('support.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">Nouvelle demande de support</h1>
                    <p class="text-muted mb-0">Décrivez votre problème et nous vous aiderons rapidement</p>
                </div>
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Formulaire -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Catégorie -->
                        <div class="mb-4">
                            <label for="category" class="form-label fw-semibold">
                                Catégorie <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category" 
                                    required>
                                <option value="">Sélectionnez une catégorie</option>
                                <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>
                                    🔧 Problème technique
                                </option>
                                <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>
                                    👤 Compte utilisateur
                                </option>
                                <option value="payment" {{ old('category') === 'payment' ? 'selected' : '' }}>
                                    💳 Paiement
                                </option>
                                <option value="order" {{ old('category') === 'order' ? 'selected' : '' }}>
                                    📦 Commande
                                </option>
                                <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>
                                    💬 Question générale
                                </option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Choisissez la catégorie qui correspond le mieux à votre demande
                            </small>
                        </div>

                        <!-- Sujet -->
                        <div class="mb-4">
                            <label for="subject" class="form-label fw-semibold">
                                Sujet (optionnel)
                            </label>
                            <input type="text" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject') }}"
                                   maxlength="255"
                                   placeholder="Ex: Problème de connexion, question sur un paiement...">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priorité -->
                        <div class="mb-4">
                            <label for="priority" class="form-label fw-semibold">
                                Priorité
                            </label>
                            <select class="form-select @error('priority') is-invalid @enderror" 
                                    id="priority" 
                                    name="priority">
                                <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>
                                    Normal
                                </option>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>
                                    Basse
                                </option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                                    Haute
                                </option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>
                                    Urgente
                                </option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <label for="message" class="form-label fw-semibold">
                                Votre message <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" 
                                      name="message" 
                                      rows="8" 
                                      maxlength="5000"
                                      required
                                      placeholder="Décrivez votre problème de manière détaillée...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between mt-1">
                                <small class="form-text text-muted">
                                    Soyez aussi précis que possible pour obtenir une réponse rapide
                                </small>
                                <small class="text-muted">
                                    <span id="charCount">{{ strlen(old('message', '')) }}</span>/5000
                                </small>
                            </div>
                        </div>

                        <!-- Pièces jointes -->
                        <div class="mb-4">
                            <label for="attachments" class="form-label fw-semibold">
                                Pièces jointes (optionnel)
                            </label>
                            <input type="file" 
                                   class="form-control @error('attachments.*') is-invalid @enderror" 
                                   id="attachments" 
                                   name="attachments[]" 
                                   multiple
                                   accept="image/*,.pdf,.doc,.docx,.txt">
                            @error('attachments.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Maximum 5 MB par fichier. Formats acceptés: images, PDF, documents
                            </small>
                        </div>

                        <!-- Aperçu des fichiers -->
                        <div id="filePreview" class="mb-4 d-none">
                            <div class="alert alert-info">
                                <strong>Fichiers sélectionnés:</strong>
                                <div id="fileList" class="mt-2"></div>
                            </div>
                        </div>

                        <!-- Conseils -->
                        <div class="alert alert-light border mb-4">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                Conseils pour une réponse rapide
                            </h6>
                            <ul class="mb-0 small">
                                <li>Soyez précis dans votre description</li>
                                <li>Incluez des captures d'écran si possible</li>
                                <li>Mentionnez les étapes pour reproduire le problème</li>
                                <li>Indiquez votre navigateur et système d'exploitation si pertinent</li>
                            </ul>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('support.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>
                                Envoyer la demande
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-clock text-primary me-2"></i>
                        Temps de réponse
                    </h5>
                    <p class="card-text text-muted mb-0">
                        Notre équipe de support s'engage à répondre dans les 24 heures ouvrables.
                        Pour les demandes urgentes, nous faisons de notre mieux pour répondre dans les 2 heures.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compteur de caractères
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    
    if (messageTextarea && charCount) {
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length > 4500) {
                charCount.classList.add('text-danger', 'fw-bold');
            } else {
                charCount.classList.remove('text-danger', 'fw-bold');
            }
        });
    }
    
    // Aperçu des fichiers
    const attachmentsInput = document.getElementById('attachments');
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');
    
    if (attachmentsInput && filePreview && fileList) {
        attachmentsInput.addEventListener('change', function() {
            const files = this.files;
            
            if (files.length > 0) {
                filePreview.classList.remove('d-none');
                fileList.innerHTML = '';
                
                Array.from(files).forEach((file, index) => {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    const fileItem = document.createElement('div');
                    fileItem.className = 'mb-1';
                    fileItem.innerHTML = `
                        <i class="fas fa-file me-2"></i>
                        <strong>${file.name}</strong> 
                        <span class="text-muted">(${fileSize} MB)</span>
                    `;
                    fileList.appendChild(fileItem);
                });
            } else {
                filePreview.classList.add('d-none');
                fileList.innerHTML = '';
            }
        });
    }
    
    // Validation du formulaire
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';
            }
        });
    }
});
</script>
@endpush
@endsection
