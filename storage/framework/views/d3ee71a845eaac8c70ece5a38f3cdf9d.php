

<?php $__env->startSection('title', 'Transactions'); ?>

<?php $__env->startSection('page-title', 'Gestion des Transactions'); ?>

<?php $__env->startSection('page-actions'); ?>
<div class="flex gap-3">
    <button type="button" onclick="exportTransactions()" 
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
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
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Filtrer les transactions
                    </h3>
                    <form action="<?php echo e(route('admin.transactions.index')); ?>" method="GET" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Statut</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Tous</option>
                                <option value="pending" <?php if(request('status') == 'pending'): ?> selected <?php endif; ?>>En attente</option>
                                <option value="completed" <?php if(request('status') == 'completed'): ?> selected <?php endif; ?>>Complété</option>
                                <option value="failed" <?php if(request('status') == 'failed'): ?> selected <?php endif; ?>>Échoué</option>
                                <option value="refunded" <?php if(request('status') == 'refunded'): ?> selected <?php endif; ?>>Remboursé</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Méthode de paiement</label>
                            <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Toutes</option>
                                <option value="wallet" <?php if(request('payment_method') == 'wallet'): ?> selected <?php endif; ?>>Wallet</option>
                                <option value="airtel_money" <?php if(request('payment_method') == 'airtel_money'): ?> selected <?php endif; ?>>Airtel Money</option>
                                <option value="orange_money" <?php if(request('payment_method') == 'orange_money'): ?> selected <?php endif; ?>>Orange Money</option>
                                <option value="mpesa" <?php if(request('payment_method') == 'mpesa'): ?> selected <?php endif; ?>>M-Pesa</option>
                                <option value="afrimoney" <?php if(request('payment_method') == 'afrimoney'): ?> selected <?php endif; ?>>Afrimoney</option>
                            <option value="bank" <?php if(request('payment_method') == 'bank'): ?> selected <?php endif; ?>>Banque</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Date début</label>
                            <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" value="<?php echo e(request('start_date')); ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Date fin</label>
                            <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" value="<?php echo e(request('end_date')); ?>">
                        </div>
                    </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:bg-gray-900 transition-colors" onclick="toggleFilterModal()">Fermer</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">Appliquer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des transactions -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Méthode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 dark:bg-gray-900">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"><?php echo e($transaction->id); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <?php if($transaction->user->avatar): ?>
                                        <img src="<?php echo e($transaction->user->avatar_url); ?>" class="w-8 h-8 rounded-full mr-3" alt="Avatar">
                                    <?php else: ?>
                                        <div class="w-8 h-8 bg-gray-500 rounded-full mr-3 flex items-center justify-center">
                                            <span class="text-white text-sm font-medium"><?php echo e($transaction->user->initial); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($transaction->user->name); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo e(number_format($transaction->amount, 2)); ?>

                                    <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($transaction->currency); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-gray-900 dark:text-white">
                                    <?php switch($transaction->payment_method):
                                        case ('wallet'): ?>
                                            <i class="fas fa-wallet text-blue-500 mr-2"></i>
                                            <?php break; ?>
                                        <?php case ('airtel_money'): ?>
                                            <i class="fas fa-mobile-alt text-red-500 mr-2"></i>
                                            <?php break; ?>
                                        <?php case ('orange_money'): ?>
                                            <i class="fas fa-mobile-alt text-orange-500 mr-2"></i>
                                            <?php break; ?>
                                        <?php case ('mpesa'): ?>
                                            <i class="fas fa-mobile-alt text-green-500 mr-2"></i>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <i class="fas fa-money-bill-wave text-gray-500 dark:text-gray-400 mr-2"></i>
                                    <?php endswitch; ?>
                                    <?php echo e(ucfirst(str_replace('_', ' ', $transaction->payment_method))); ?>

                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php echo e($transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($transaction->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100'))); ?>">
                                    <?php echo e(ucfirst($transaction->status)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?php echo e($transaction->created_at->format('d/m/Y H:i')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="<?php echo e(route('admin.transactions.show', $transaction)); ?>" 
                                       class="inline-flex items-center px-3 py-1 border border-blue-300 text-blue-700 rounded-md hover:bg-blue-50 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($transaction->status === 'pending'): ?>
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1 border border-green-300 text-green-700 rounded-md hover:bg-green-50 transition-colors" 
                                                onclick="updateStatus(<?php echo e($transaction->id); ?>, 'completed')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 rounded-md hover:bg-red-50 transition-colors" 
                                                onclick="updateStatus(<?php echo e($transaction->id); ?>, 'failed')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4 text-gray-400"></i>
                                    <p class="text-lg font-medium">Aucune transaction trouvée</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-700 dark:text-gray-200">
                Affichage de <?php echo e($transactions->firstItem() ?? 0); ?>-<?php echo e($transactions->lastItem() ?? 0); ?> sur <?php echo e($transactions->total()); ?> transactions
            </div>
            <div class="pagination-wrapper">
                <?php echo e($transactions->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function toggleFilterModal() {
        const modal = document.getElementById('filterModal');
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('filterModal').addEventListener('click', function(e) {
        if (e.target === this) {
            toggleFilterModal();
        }
    });

    // Fermer le modal avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('filterModal');
            if (!modal.classList.contains('hidden')) {
                toggleFilterModal();
            }
        }
    });

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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/admin/transactions/index.blade.php ENDPATH**/ ?>