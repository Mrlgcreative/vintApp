@extends('layouts.admin')

@section('title', 'Gestion des pré-inscriptions')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Couleurs personnalisées */
    .text-purple { color: #8b5cf6; }
    .bg-purple { background-color: #8b5cf6; }
    
    /* Cards avec bordure gauche colorée */
    .card {
        border-radius: 0.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Statistiques cards */
    .card-body h3 {
        font-weight: 700;
        font-size: 2rem;
    }
    
    .card-body .small {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
    }
    
    /* Tableau */
    .table {
        font-size: 0.9rem;
    }
    
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6b7280;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .table tbody tr {
        transition: background-color 0.2s;
    }
    
    .table tbody tr:hover {
        background-color: #f9fafb;
    }
    
    /* Badges de statut */
    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
        font-size: 0.75rem;
        border-radius: 0.375rem;
    }
    
    /* Boutons */
    .btn {
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    .btn-group .btn {
        border-radius: 0;
    }
    
    .btn-group .btn:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }
    
    .btn-group .btn:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
    
    /* Filtres */
    .form-control, .form-select {
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .form-label {
        font-weight: 500;
        font-size: 0.875rem;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    /* Icônes */
    .fas, .far {
        transition: transform 0.2s;
    }
    
    .btn:hover .fas,
    .btn:hover .far {
        transform: scale(1.1);
    }
    
    /* Modal */
    .modal-content {
        border-radius: 0.75rem;
        border: none;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .modal-header {
        border-bottom: 1px solid #e5e7eb;
        padding: 1.25rem;
    }
    
    .modal-title {
        font-weight: 600;
        font-size: 1.125rem;
    }
    
    .modal-body {
        padding: 1.25rem;
    }
    
    .modal-footer {
        border-top: 1px solid #e5e7eb;
        padding: 1rem 1.25rem;
    }
    
    /* Alerts */
    .alert {
        border-radius: 0.5rem;
        border: none;
        padding: 1rem 1.25rem;
        font-weight: 500;
    }
    
    .alert-success {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .alert-warning {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    /* Pagination */
    .pagination {
        margin: 0;
    }
    
    .page-link {
        border-radius: 0.375rem;
        margin: 0 0.25rem;
        border: 1px solid #d1d5db;
        color: #6b7280;
        transition: all 0.2s;
    }
    
    .page-link:hover {
        background-color: #f3f4f6;
        border-color: #6366f1;
        color: #6366f1;
        transform: translateY(-1px);
    }
    
    .page-item.active .page-link {
        background-color: #6366f1;
        border-color: #6366f1;
    }
    
    /* Checkbox styling */
    input[type="checkbox"] {
        width: 1.125rem;
        height: 1.125rem;
        cursor: pointer;
        border-radius: 0.25rem;
        border: 2px solid #d1d5db;
        transition: all 0.2s;
    }
    
    input[type="checkbox"]:checked {
        background-color: #6366f1;
        border-color: #6366f1;
    }
    
    input[type="checkbox"]:hover {
        border-color: #6366f1;
    }
    
    /* Animation pour les cartes de statistiques */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .card {
        animation: fadeInUp 0.5s ease-out;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .table {
            font-size: 0.8rem;
        }
        
        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
        }
        
        .card-body h3 {
            font-size: 1.5rem;
        }
    }
    
    /* État vide */
    .text-muted .fa-inbox {
        opacity: 0.3;
    }
    
    /* Hover sur les lignes du tableau */
    tbody tr {
        cursor: pointer;
    }
    
    /* Badge avec animation */
    .badge {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Amélioration des bordures colorées */
    .card[style*="border-left"] {
        border-left-width: 4px !important;
        border-left-style: solid !important;
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
                                <i class="fas fa-users-cog me-2 text-primary"></i>
                                Gestion des pré-inscriptions
                            </h2>
                            <p class="text-muted mb-0">Gérez les demandes d'inscription en attente</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.waiting-users.export') }}" class="btn btn-success">
                                <i class="fas fa-file-export me-2"></i>Exporter CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #6366f1 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total</p>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="text-primary" style="font-size: 2.5rem;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">En attente</p>
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        </div>
                        <div class="text-warning" style="font-size: 2.5rem;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Approuvés</p>
                            <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                        </div>
                        <div class="text-success" style="font-size: 2.5rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #8b5cf6 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Convertis</p>
                            <h3 class="mb-0">{{ $stats['converted'] }}</h3>
                        </div>
                        <div class="text-purple" style="font-size: 2.5rem; color: #8b5cf6;">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats supplémentaires -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-day text-primary mb-2" style="font-size: 2rem;"></i>
                    <h4 class="mb-0">{{ $stats['today'] }}</h4>
                    <small class="text-muted">Aujourd'hui</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-week text-info mb-2" style="font-size: 2rem;"></i>
                    <h4 class="mb-0">{{ $stats['this_week'] }}</h4>
                    <small class="text-muted">Cette semaine</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt text-success mb-2" style="font-size: 2rem;"></i>
                    <h4 class="mb-0">{{ $stats['this_month'] }}</h4>
                    <small class="text-muted">Ce mois</small>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres et recherche -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.waiting-users.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-search me-1"></i>Recherche</label>
                    <input type="text" name="search" class="form-control" placeholder="Nom, email, téléphone..." value="{{ request('search') }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-filter me-1"></i>Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converti</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-calendar me-1"></i>Date début</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-calendar me-1"></i>Date fin</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card shadow-sm">
        <div class="card-header bg-white dark:bg-gray-800">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Liste des pré-inscriptions ({{ $waitingUsers->total() }})
                </h5>
                
                <!-- Actions en masse -->
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('approve')" id="bulkApproveBtn" disabled>
                        <i class="fas fa-check me-1"></i>Approuver
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('reject')" id="bulkRejectBtn" disabled>
                        <i class="fas fa-times me-1"></i>Rejeter
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Pays</th>
                            <th>Statut</th>
                            <th>Date inscription</th>
                            <th>Attente (jours)</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waitingUsers as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                                </td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                </td>
                                <td>
                                    <i class="fas fa-envelope text-muted me-1"></i>{{ $user->email }}
                                    @if($user->email_confirmed_at)
                                        <i class="fas fa-check-circle text-success ms-1" title="Email confirmé"></i>
                                    @endif
                                </td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>{{ $user->country }}</td>
                                <td>{!! $user->status_badge !!}</td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->waiting_days > 7 ? 'danger' : ($user->waiting_days > 3 ? 'warning' : 'info') }}">
                                        {{ $user->waiting_days }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.waiting-users.show', $user) }}" class="btn btn-sm btn-info" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($user->status === 'confirmed' || $user->status === 'pending')
                                            <form action="{{ route('admin.waiting-users.approve', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Approuver">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <button type="button" class="btn btn-sm btn-warning" onclick="showRejectModal({{ $user->id }})" title="Rejeter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        
                                        <form action="{{ route('admin.waiting-users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette pré-inscription ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucune pré-inscription trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($waitingUsers->hasPages())
            <div class="card-footer bg-white dark:bg-gray-800">
                {{ $waitingUsers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal de rejet -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Rejeter la pré-inscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Raison du rejet</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Expliquez pourquoi cette demande est rejetée..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sélection multiple
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkButtons();
    }

    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkButtons);
    });

    function updateBulkButtons() {
        const checked = document.querySelectorAll('.user-checkbox:checked').length;
        document.getElementById('bulkApproveBtn').disabled = checked === 0;
        document.getElementById('bulkRejectBtn').disabled = checked === 0;
    }

    // Actions en masse
    function bulkAction(action) {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const userIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (userIds.length === 0) return;
        
        if (!confirm(`Confirmer cette action pour ${userIds.length} utilisateur(s) ?`)) return;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.waiting-users.bulk-action") }}';
        
        form.innerHTML = `
            @csrf
            <input type="hidden" name="action" value="${action}">
            ${userIds.map(id => `<input type="hidden" name="user_ids[]" value="${id}">`).join('')}
        `;
        
        document.body.appendChild(form);
        form.submit();
    }

    // Modal de rejet
    function showRejectModal(userId) {
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        const form = document.getElementById('rejectForm');
        form.action = `/admin/waiting-users/${userId}/reject`;
        modal.show();
    }
</script>
@endpush
@endsection
