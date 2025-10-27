

<?php $__env->startSection('title', 'Détails de la transaction'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-semibold">Détails de la transaction</h1>
        <a href="<?php echo e(route('admin.transactions.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
            Retour à la liste
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <!-- Informations principales -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-xl font-semibold mb-4">Informations générales</h2>
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Transaction</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e($transaction->id); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Montant</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <?php echo e(number_format($transaction->amount, 2)); ?> <?php echo e($transaction->currency); ?>

                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo e($transaction->type === 'deposit' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'); ?>">
                                    <?php echo e($transaction->type === 'deposit' ? 'Dépôt' : 'Retrait'); ?>

                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Méthode de paiement</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <?php echo e(ucfirst($transaction->payment_method)); ?>

                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <?php echo e($transaction->created_at->format('d/m/Y H:i')); ?>

                            </dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4">Informations utilisateur</h2>
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-12 w-12">
                            <img class="h-12 w-12 rounded-full" src="<?php echo e($transaction->user->profile_photo_url); ?>" alt="<?php echo e($transaction->user->name); ?>">
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                <?php echo e($transaction->user->name); ?>

                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <?php echo e($transaction->user->email); ?>

                            </div>
                        </div>
                    </div>
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Utilisateur</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e($transaction->user->id); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date d'inscription</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <?php echo e($transaction->user->created_at->format('d/m/Y')); ?>

                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Statut et actions -->
        <div class="p-6 bg-gray-50 dark:bg-gray-900">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Statut actuel</h3>
                    <?php
                        $statusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                            'completed' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                            'failed' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                            'refunded' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100',
                        ];
                        $statusLabels = [
                            'pending' => 'En attente',
                            'completed' => 'Complétée',
                            'failed' => 'Échouée',
                            'refunded' => 'Remboursée',
                        ];
                    ?>
                    <span class="mt-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full <?php echo e($statusClasses[$transaction->status]); ?>">
                        <?php echo e($statusLabels[$transaction->status]); ?>

                    </span>
                </div>

                <?php if($transaction->status === 'pending'): ?>
                <div class="flex space-x-4">
                    <button type="button" 
                            data-status-update="completed"
                            data-transaction-id="<?php echo e($transaction->id); ?>"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                        Marquer comme complétée
                    </button>
                    <button type="button"
                            data-status-update="failed"
                            data-transaction-id="<?php echo e($transaction->id); ?>"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">
                        Marquer comme échouée
                    </button>
                </div>
                <?php elseif($transaction->status === 'completed'): ?>
                <button type="button"
                        data-status-update="refunded"
                        data-transaction-id="<?php echo e($transaction->id); ?>"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
                    Marquer comme remboursée
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Historique des mises à jour -->
        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Historique des mises à jour</h3>
            <div class="space-y-4">
                <?php $__currentLoopData = $transaction->status_history ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <span class="w-2 h-2 rounded-full <?php echo e($statusClasses[$history->status]); ?> inline-block"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            <?php echo e($statusLabels[$history->status]); ?>

                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <?php echo e($history->created_at->format('d/m/Y H:i')); ?>

                        </p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des mises à jour de statut
    const updateTransactionStatus = async (transactionId, newStatus) => {
        try {
            const response = await fetch(`/admin/transactions/${transactionId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus })
            });

            const data = await response.json();

            if (response.ok) {
                // Rafraîchir la page pour montrer le nouveau statut
                window.location.reload();
            } else {
                alert('Erreur lors de la mise à jour du statut: ' + data.message);
            }
        } catch (error) {
            alert('Une erreur est survenue lors de la mise à jour du statut');
            console.error('Erreur:', error);
        }
    };

    // Gestionnaire pour les boutons de mise à jour de statut
    document.querySelectorAll('[data-status-update]').forEach(button => {
        button.addEventListener('click', function() {
            const transactionId = this.dataset.transactionId;
            const newStatus = this.dataset.statusUpdate;
            
            if (confirm('Êtes-vous sûr de vouloir modifier le statut de cette transaction ?')) {
                updateTransactionStatus(transactionId, newStatus);
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/transactions/show.blade.php ENDPATH**/ ?>