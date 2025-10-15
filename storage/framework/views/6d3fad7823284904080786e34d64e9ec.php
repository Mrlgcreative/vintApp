

<?php $__env->startSection('title', 'Transactions'); ?>

<?php $__env->startSection('page-title', 'Gestion des Transactions'); ?>

<?php $__env->startSection('page-actions'); ?>
<div class="flex gap-3">
    <button type="button" onclick="exportTransactions()" 
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
        <i class="fas fa-download mr-2"></i>Exporter
    </button>
    <button type="button" onclick="toggleFilterModal()" 
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
        <i class="fas fa-filter mr-2"></i>Filtres
    </button>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Filtres Modal -->
<div id="filterModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Filtrer les transactions
                    </h3>
                    <form action="<?php echo e(route('admin.transactions.index')); ?>" method="GET" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Tous</option>
                                <option value="pending" <?php if(request('status') == 'pending'): ?> selected <?php endif; ?>>En attente</option>
                                <option value="completed" <?php if(request('status') == 'completed'): ?> selected <?php endif; ?>>Complété</option>
                                <option value="failed" <?php if(request('status') == 'failed'): ?> selected <?php endif; ?>>Échoué</option>
                                <option value="refunded" <?php if(request('status') == 'refunded'): ?> selected <?php endif; ?>>Remboursé</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Méthode de paiement</label>
                            <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Toutes</option>
                                <option value="wallet" <?php if(request('payment_method') == 'wallet'): ?> selected <?php endif; ?>>Wallet</option>
                                <option value="airtel_money" <?php if(request('payment_method') == 'airtel_money'): ?> selected <?php endif; ?>>Airtel Money</option>
                                <option value="orange_money" <?php if(request('payment_method') == 'orange_money'): ?> selected <?php endif; ?>>Orange Money</option>
                                <option value="mpesa" <?php if(request('payment_method') == 'mpesa'): ?> selected <?php endif; ?>>M-Pesa</option>
                                <option value="afrimoney" <?php if(request('payment_method') == 'afrimoney'): ?> selected <?php endif; ?>>Afrimoney</option>
                            <option value="bank" <?php if(request('payment_method') == 'bank'): ?> selected <?php endif; ?>>Banque</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date début</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date fin</label>
                                <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Appliquer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tableau des transactions -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Montant</th>
                        <th>Méthode</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($transaction->id); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if($transaction->user->avatar): ?>
                                        <img src="<?php echo e($transaction->user->avatar_url); ?>" class="rounded-circle me-2" width="32" height="32">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px">
                                            <span class="text-white"><?php echo e($transaction->user->initial); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php echo e($transaction->user->name); ?>

                                </div>
                            </td>
                            <td class="transaction-amount">
                                <?php echo e(number_format($transaction->amount, 2)); ?>

                                <small><?php echo e($transaction->currency); ?></small>
                            </td>
                            <td>
                                <?php switch($transaction->payment_method):
                                    case ('wallet'): ?>
                                        <i class="fas fa-wallet text-primary me-1"></i>
                                        <?php break; ?>
                                    <?php case ('airtel_money'): ?>
                                        <i class="fas fa-mobile-alt text-danger me-1"></i>
                                        <?php break; ?>
                                    <?php case ('orange_money'): ?>
                                        <i class="fas fa-mobile-alt text-warning me-1"></i>
                                        <?php break; ?>
                                    <?php case ('mpesa'): ?>
                                        <i class="fas fa-mobile-alt text-success me-1"></i>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <i class="fas fa-money-bill-wave text-secondary me-1"></i>
                                <?php endswitch; ?>
                                <?php echo e(ucfirst(str_replace('_', ' ', $transaction->payment_method))); ?>

                            </td>
                            <td>
                                <span class="badge status-badge bg-<?php echo e($transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : ($transaction->status === 'failed' ? 'danger' : 'secondary'))); ?>">
                                    <?php echo e(ucfirst($transaction->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e($transaction->created_at->format('d/m/Y H:i')); ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?php echo e(route('admin.transactions.show', $transaction)); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($transaction->status === 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="updateStatus(<?php echo e($transaction->id); ?>, 'completed')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="updateStatus(<?php echo e($transaction->id); ?>, 'failed')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Aucune transaction trouvée</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                Affichage de <?php echo e($transactions->firstItem() ?? 0); ?>-<?php echo e($transactions->lastItem() ?? 0); ?> sur <?php echo e($transactions->total()); ?> transactions
            </div>
            <?php echo e($transactions->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function updateStatus(transactionId, newStatus) {
        if (!confirm('Êtes-vous sûr de vouloir mettre à jour le statut de cette transaction ?')) {
            return;
        }

        fetch(`/admin/transactions/${transactionId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            alert('Une erreur est survenue');
            console.error('Error:', error);
        });
    }

    function exportTransactions() {
        const currentUrl = new URL(window.location.href);
        const searchParams = currentUrl.searchParams;
        searchParams.append('export', 'true');
        window.location.href = currentUrl.toString();
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/transactions/index.blade.php ENDPATH**/ ?>