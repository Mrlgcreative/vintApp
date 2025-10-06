@extends('layouts.admin')

@section('title', 'Détails de la pré-inscription')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        align-items: start;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 24px;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-item i {
        font-size: 1.2rem;
        position: relative;
        z-index: 1;
        background: white;
        padding: 2px;
    }

    .text-purple {
        color: #8b5cf6;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.waiting-users.index') }}">Pré-inscriptions</a></li>
            <li class="breadcrumb-item active">{{ $waitingUser->name }}</li>
        </ol>
    </nav>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user me-2"></i>Informations de l'utilisateur</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted mb-1"><i class="fas fa-user me-1"></i>Nom complet</label>
                            <p class="h5">{{ $waitingUser->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1"><i class="fas fa-envelope me-1"></i>Email</label>
                            <p class="h5">
                                {{ $waitingUser->email }}
                                @if($waitingUser->email_confirmed_at)
                                    <i class="fas fa-check-circle text-success ms-2" title="Vérifié"></i>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted mb-1"><i class="fas fa-phone me-1"></i>Téléphone</label>
                            <p class="h5">{{ $waitingUser->phone ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1"><i class="fas fa-globe me-1"></i>Pays</label>
                            <p class="h5">{{ $waitingUser->country }}</p>
                        </div>
                    </div>

                    @if($waitingUser->message)
                        <div class="mb-3">
                            <label class="text-muted mb-1"><i class="fas fa-comment me-1"></i>Message</label>
                            <div class="alert alert-info">
                                {{ $waitingUser->message }}
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted mb-1"><i class="fas fa-network-wired me-1"></i>Adresse IP</label>
                            <p>{{ $waitingUser->ip_address ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1"><i class="fas fa-laptop me-1"></i>Navigateur</label>
                            <p><small>{{ $waitingUser->user_agent ?? '-' }}</small></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes admin -->
            @if($waitingUser->admin_notes)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes administrateur</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $waitingUser->admin_notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Compte converti -->
            @if($waitingUser->converted_user_id)
                <div class="card shadow-sm mt-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Compte utilisateur créé</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-muted mb-1">User ID</label>
                                <p class="h5">#{{ $waitingUser->convertedUser->id }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted mb-1">Date de conversion</label>
                                <p class="h5">{{ $waitingUser->converted_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-success mt-2">
                            <i class="fas fa-external-link-alt me-1"></i>Voir le profil utilisateur
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Panneau latéral -->
        <div class="col-lg-4">
            <!-- Statut -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Statut</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        {!! $waitingUser->status_badge !!}
                    </div>
                    <p class="text-muted mb-0">
                        <i class="fas fa-clock me-1"></i>
                        En attente depuis {{ $waitingUser->waiting_days }} jour(s)
                    </p>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historique</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <i class="fas fa-plus-circle text-primary"></i>
                            <div>
                                <strong>Inscription</strong><br>
                                <small class="text-muted">{{ $waitingUser->created_at->format('d/m/Y à H:i') }}</small>
                            </div>
                        </div>

                        @if($waitingUser->email_confirmed_at)
                            <div class="timeline-item">
                                <i class="fas fa-check-circle text-success"></i>
                                <div>
                                    <strong>Email confirmé</strong><br>
                                    <small class="text-muted">{{ $waitingUser->email_confirmed_at->format('d/m/Y à H:i') }}</small>
                                </div>
                            </div>
                        @endif

                        @if($waitingUser->approved_at)
                            <div class="timeline-item">
                                <i class="fas fa-thumbs-up text-success"></i>
                                <div>
                                    <strong>Approuvé</strong><br>
                                    <small class="text-muted">{{ $waitingUser->approved_at->format('d/m/Y à H:i') }}</small>
                                </div>
                            </div>
                        @endif

                        @if($waitingUser->rejected_at)
                            <div class="timeline-item">
                                <i class="fas fa-times-circle text-danger"></i>
                                <div>
                                    <strong>Rejeté</strong><br>
                                    <small class="text-muted">{{ $waitingUser->rejected_at->format('d/m/Y à H:i') }}</small>
                                </div>
                            </div>
                        @endif

                        @if($waitingUser->converted_at)
                            <div class="timeline-item">
                                <i class="fas fa-user-check text-purple"></i>
                                <div>
                                    <strong>Compte créé</strong><br>
                                    <small class="text-muted">{{ $waitingUser->converted_at->format('d/m/Y à H:i') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($waitingUser->status === 'pending')
                            <form action="{{ route('admin.waiting-users.resend-confirmation', $waitingUser) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="fas fa-envelope me-2"></i>Renvoyer confirmation
                                </button>
                            </form>
                        @endif

                        @if($waitingUser->status === 'confirmed' || $waitingUser->status === 'pending')
                            <form action="{{ route('admin.waiting-users.approve', $waitingUser) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check me-2"></i>Approuver
                                </button>
                            </form>

                            <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times me-2"></i>Rejeter
                            </button>
                        @endif

                        <form action="{{ route('admin.waiting-users.destroy', $waitingUser) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette pré-inscription ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>Supprimer
                            </button>
                        </form>

                        <a href="{{ route('admin.waiting-users.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de rejet -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.waiting-users.reject', $waitingUser) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Rejeter la pré-inscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Raison du rejet</label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Expliquez pourquoi cette demande est rejetée..." required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cette action ne peut pas être annulée facilement.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Confirmer le rejet</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        align-items-start;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 24px;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-item i {
        font-size: 1.2rem;
        position: relative;
        z-index: 1;
        background: white;
        padding: 2px;
    }

    .text-purple {
        color: #8b5cf6;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@endsection
