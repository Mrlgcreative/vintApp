@extends('layouts.admin')

@section('title', 'Wallets en attente')
@section('page-title', 'Wallets en attente de validation')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-900">
            Wallets en attente ({{ $pendingWallets->total() }})
        </h3>
    </div>
    <div class="p-0">
        @if($pendingWallets->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Devise</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de création</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingWallets as $wallet)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($wallet->user->avatar)
                                            <img src="{{ $wallet->user->avatar_url }}" class="rounded-circle me-3" width="32" height="32" alt="Avatar">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ $wallet->user->initial }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $wallet->user->name }}</div>
                                            <small class="text-muted">{{ $wallet->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $wallet->currency }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold transaction-amount">
                                        {{ number_format($wallet->balance, $wallet->currency === 'USD' ? 2 : 0) }} {{ $wallet->currency }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($wallet->type) }}</span>
                                </td>
                                <td>
                                    <div>{{ $wallet->created_at->format('d/m/Y H:i') }}</div>
                                    <small class="text-muted">{{ $wallet->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('admin.wallets.approve', $wallet) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir approuver ce wallet ?')"
                                                    title="Approuver">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $wallet->id }}"
                                                title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        
                                        <a href="{{ route('admin.users.show', $wallet->user) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Voir utilisateur">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal de rejet -->
                            <div class="modal fade" id="rejectModal{{ $wallet->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.wallets.reject', $wallet) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Rejeter le wallet</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Vous êtes sur le point de rejeter le wallet de <strong>{{ $wallet->user->name }}</strong>.</p>
                                                
                                                <div class="mb-3">
                                                    <label for="reason{{ $wallet->id }}" class="form-label">Raison du rejet *</label>
                                                    <textarea class="form-control" id="reason{{ $wallet->id }}" name="reason" 
                                                            rows="3" required placeholder="Expliquez pourquoi ce wallet est rejeté..."></textarea>
                                                </div>
                                                
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    L'utilisateur sera notifié du rejet avec cette raison.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-danger">Rejeter le wallet</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="text-muted">Aucun wallet en attente</h5>
                <p class="text-muted">Tous les wallets ont été traités.</p>
            </div>
        @endif
    </div>
    
    @if($pendingWallets->hasPages())
        <div class="card-footer">
            {{ $pendingWallets->links() }}
        </div>
    @endif
</div>

@if($pendingWallets->count() > 0)
<!-- Actions groupées -->
<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0">Actions groupées</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Vous pouvez approuver ou rejeter plusieurs wallets en même temps en utilisant les boutons ci-dessous.
        </div>
        
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" onclick="bulkApprove()">
                <i class="fas fa-check me-2"></i>Approuver tous
            </button>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#bulkRejectModal">
                <i class="fas fa-times me-2"></i>Rejeter tous
            </button>
        </div>
    </div>
</div>

<!-- Modal de rejet groupé -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bulkRejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Rejeter tous les wallets</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Vous êtes sur le point de rejeter <strong>{{ $pendingWallets->count() }} wallet(s)</strong>.</p>
                    
                    <div class="mb-3">
                        <label for="bulkReason" class="form-label">Raison du rejet *</label>
                        <textarea class="form-control" id="bulkReason" name="reason" 
                                rows="3" required placeholder="Expliquez pourquoi ces wallets sont rejetés..."></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Tous les utilisateurs concernés seront notifiés du rejet.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter tous les wallets</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function bulkApprove() {
    if (confirm('Êtes-vous sûr de vouloir approuver tous les wallets en attente ?')) {
        // Créer un formulaire pour l'approbation groupée
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.wallets.bulk-approve") }}'; // Route à créer
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto refresh toutes les 30 secondes
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endpush