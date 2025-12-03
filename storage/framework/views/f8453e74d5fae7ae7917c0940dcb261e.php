

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Messages Flash -->
        <?php if(session('success')): ?>
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                </div>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
                    <i class="fas fa-times text-green-500"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                </div>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
                    <i class="fas fa-times text-red-500"></i>
                </button>
            </div>
        <?php endif; ?>
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Gestion des remboursements</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-300">Gérez les demandes de remboursement de votre boutique</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg px-4 py-2 shadow-sm border border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total des demandes:</span>
                            <span class="ml-2 text-lg font-semibold text-blue-600"><?php echo e($refunds->total()); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <form method="GET" action="<?php echo e(route('admin.refunds.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Statut</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>En attente</option>
                        <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>Approuvé</option>
                        <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>Rejeté</option>
                        <option value="negotiation" <?php echo e(request('status') === 'negotiation' ? 'selected' : ''); ?>>Négociation</option>
                        <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Terminé</option>
                    </select>
                </div>

                <div>
                    <label for="refund_type" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Type</label>
                    <select name="refund_type" id="refund_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les types</option>
                        <option value="full" <?php echo e(request('refund_type') === 'full' ? 'selected' : ''); ?>>Complet</option>
                        <option value="partial" <?php echo e(request('refund_type') === 'partial' ? 'selected' : ''); ?>>Partiel</option>
                    </select>
                </div>

                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Recherche</label>
                    <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" 
                           placeholder="Numéro de commande, acheteur..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des demandes -->
        <div class="space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $refunds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $refund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            
                            <!-- Informations principales -->
                            <div class="flex-1">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <?php if(!empty($refund->order?->item?->images) && count($refund->order->item->images) > 0): ?>
                                            <img src="<?php echo e(asset('storage/' . $refund->order->item->images[0])); ?>" 
                                                 alt="<?php echo e($refund->order?->item?->name ?? 'Article'); ?>" 
                                                 class="w-16 h-16 object-cover rounded-lg">
                                        <?php else: ?>
                                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                            <?php echo e($refund->order?->item?->name ?? 'Article supprimé'); ?>

                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Commande #<?php echo e($refund->order?->order_number ?? 'N/A'); ?>

                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                            Acheteur: <?php echo e($refund->buyer?->name ?? 'Utilisateur supprimé'); ?> (<?php echo e($refund->buyer?->email ?? 'N/A'); ?>)
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            Demandé le: <?php echo e($refund->created_at?->format('d/m/Y à H:i') ?? 'N/A'); ?>

                                        </p>
                                    </div>
                                </div>
                                
                                <div class="mt-4 text-sm text-gray-700 dark:text-gray-200">
                                    <strong>Raison:</strong> <?php echo e(Str::limit($refund->reason, 100)); ?>

                                </div>
                            </div>

                            <!-- Montants et statut -->
                            <div class="lg:text-right">
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Montant original:</span>
                                        <div class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($refund->currency === 'USD' ? '$' : 'FC'); ?> <?php echo e(number_format($refund->original_amount, 2)); ?></div>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Remboursement demandé:</span>
                                        <div class="text-lg font-semibold text-blue-600"><?php echo e($refund->formatted_refund_amount); ?></div>
                                    </div>
                                    <?php if($refund->status === 'negotiation' && $refund->counter_offer_amount): ?>
                                        <div>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Contre-offre:</span>
                                            <div class="text-lg font-semibold text-orange-600"><?php echo e($refund->currency === 'USD' ? '$' : 'FC'); ?> <?php echo e(number_format($refund->counter_offer_amount, 2)); ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mt-4">
                                    <?php
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'approved' => 'bg-green-100 text-green-800 border-green-200',
                                            'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                            'negotiation' => 'bg-orange-100 text-orange-800 border-orange-200',
                                            'completed' => 'bg-blue-100 text-blue-800 border-blue-200'
                                        ];
                                    ?>
                                    
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border <?php echo e($statusClasses[$refund->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 border-gray-200 dark:border-gray-700'); ?>">
                                        <?php echo e($refund->status_text); ?>

                                    </span>
                                    
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Type: <?php echo e($refund->refund_type === 'full' ? 'Complet' : 'Partiel'); ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <?php if($refund->status === 'pending'): ?>
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex flex-wrap gap-3">
                                    <a href="<?php echo e(route('admin.refunds.show', $refund)); ?>" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                        <i class="fas fa-eye mr-2"></i>Examiner
                                    </a>
                                    
                                    <form method="POST" action="<?php echo e(route('refund.process', $refund)); ?>" class="inline-flex">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" 
                                                onclick="return confirm('Approuver cette demande de remboursement ?')"
                                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                            <i class="fas fa-check mr-2"></i>Approuver
                                        </button>
                                    </form>
                                    
                                    <button onclick="openNegotiationModal('<?php echo e($refund->id); ?>')" 
                                            class="inline-flex items-center px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors duration-200">
                                        <i class="fas fa-handshake mr-2"></i>Négocier
                                    </button>
                                    
                                    <form method="POST" action="<?php echo e(route('refund.process', $refund)); ?>" class="inline-flex">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" 
                                                onclick="return confirm('Rejeter cette demande de remboursement ?')"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                            <i class="fas fa-times mr-2"></i>Rejeter
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 p-12 text-center">
                    <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune demande de remboursement</h3>
                    <p class="text-gray-500 dark:text-gray-400">Il n'y a actuellement aucune demande de remboursement à traiter.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($refunds->hasPages()): ?>
            <div class="mt-8">
                <?php echo e($refunds->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de négociation -->
<div id="negotiationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-2xl bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-handshake text-orange-500 mr-2"></i>
                Proposer une contre-offre
            </h3>
            
            <form id="negotiationForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="refundId" name="refund_id">
                <input type="hidden" name="action" value="negotiate">
                
                <div class="mb-4">
                    <label for="counterOffer" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Montant proposé</label>
                    <div class="relative">
                        <input type="number" id="counterOffer" name="counter_offer" step="0.01" min="0" required
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <span class="absolute left-3 top-2 text-gray-500 dark:text-gray-400">$</span>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="adminNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Notes (optionnel)</label>
                    <textarea id="adminNotes" name="admin_notes" rows="3"
                              placeholder="Expliquez votre contre-offre..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeNegotiationModal()"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200">
                        Proposer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fonctions pour gérer le modal de négociation
function openNegotiationModal(refundId) {
    document.getElementById('refundId').value = refundId;
    document.getElementById('negotiationModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeNegotiationModal() {
    document.getElementById('negotiationModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('negotiationForm').reset();
}

// Fermer le modal en cliquant en dehors
document.getElementById('negotiationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNegotiationModal();
    }
});

// Soumission du formulaire de négociation
document.getElementById('negotiationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const refundId = document.getElementById('refundId').value;
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    // Désactiver le bouton et afficher le chargement
    submitButton.disabled = true;
    submitButton.textContent = 'Traitement...';
    
    fetch(`/refunds/${refundId}/process`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Contre-offre envoyée avec succès !');
            closeNegotiationModal();
            window.location.reload();
        } else {
            alert(data.error || 'Erreur lors de l\'envoi de la contre-offre');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de l\'envoi');
    })
    .finally(() => {
        // Réactiver le bouton
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/admin/refunds/index.blade.php ENDPATH**/ ?>