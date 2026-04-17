

<?php $__env->startSection('title', 'Dashboard Boost'); ?>

<?php
use Illuminate\Support\Facades\Storage;
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Dashboard Boost</h1>
            <p class="text-gray-600 dark:text-gray-300">Gérez vos boosts et consultez vos statistiques</p>
        </div>
        <a href="<?php echo e(route('boost.index')); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>Nouveau Boost
        </a>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-600 text-white rounded-xl p-6 text-center transform hover:-translate-y-1 transition-transform duration-200">
            <div class="flex justify-center mb-3">
                <i class="fas fa-rocket text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-1"><?php echo e($stats['active_boosts']); ?></h3>
            <p class="text-blue-100">Boosts Actifs</p>
        </div>
        <div class="bg-green-600 text-white rounded-xl p-6 text-center transform hover:-translate-y-1 transition-transform duration-200">
            <div class="flex justify-center mb-3">
                <i class="fas fa-chart-line text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-1"><?php echo e($stats['total_spent']); ?> CDF</h3>
            <p class="text-green-100">Total Dépensé</p>
        </div>
        <div class="bg-cyan-600 text-white rounded-xl p-6 text-center transform hover:-translate-y-1 transition-transform duration-200">
            <div class="flex justify-center mb-3">
                <i class="fas fa-eye text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-1"><?php echo e($stats['total_views']); ?></h3>
            <p class="text-cyan-100">Vues Générées</p>
        </div>
        <div class="bg-yellow-500 text-white rounded-xl p-6 text-center transform hover:-translate-y-1 transition-transform duration-200">
            <div class="flex justify-center mb-3">
                <i class="fas fa-mouse-pointer text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-1"><?php echo e($stats['total_clicks']); ?></h3>
            <p class="text-yellow-100">Clics Générés</p>
        </div>
    </div>

    <!-- Onglets -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button class="tab-button active border-b-2 border-blue-500 py-2 px-1 text-blue-600 font-medium text-sm whitespace-nowrap" 
                    id="active-tab" data-target="#active" type="button" role="tab">
                <i class="fas fa-play-circle mr-2"></i>Boosts Actifs (<?php echo e($activeBoosts->count()); ?>)
            </button>
            <button class="tab-button border-b-2 border-transparent py-2 px-1 text-gray-500 hover:text-gray-700 dark:text-gray-200 hover:border-gray-300 dark:border-gray-600 font-medium text-sm whitespace-nowrap" 
                    id="expired-tab" data-target="#expired" type="button" role="tab">
                <i class="fas fa-clock mr-2"></i>Boosts Expirés (<?php echo e($expiredBoosts->count()); ?>)
            </button>
            <button class="tab-button border-b-2 border-transparent py-2 px-1 text-gray-500 hover:text-gray-700 dark:text-gray-200 hover:border-gray-300 dark:border-gray-600 font-medium text-sm whitespace-nowrap" 
                    id="cancelled-tab" data-target="#cancelled" type="button" role="tab">
                <i class="fas fa-times-circle mr-2"></i>Boosts Annulés (<?php echo e($cancelledBoosts->count()); ?>)
            </button>
        </nav>
    </div>

    <!-- Contenu des onglets -->
    <div class="tab-content">
        <!-- Boosts Actifs -->
        <div class="tab-pane active" id="active" role="tabpanel">
            <?php $__empty_1 = true; $__currentLoopData = $activeBoosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $boost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-4 p-6 boost-item hover:shadow-md transition-shadow duration-200 border-l-4 border-l-blue-500">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-4 lg:space-y-0 lg:space-x-6">
                    <!-- Image du produit -->
                    <div class="flex-shrink-0">
                        <?php if($boost->item->images && count($boost->item->images) > 0): ?>
                        <img src="<?php echo e(Storage::url($boost->item->images[0])); ?>" alt="<?php echo e($boost->item->name); ?>" 
                             class="w-20 h-20 object-cover rounded-lg">
                        <?php else: ?>
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 flex items-center justify-center rounded-lg">
                            <i class="fas fa-image text-2xl text-gray-400"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Informations du produit -->
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1"><?php echo e($boost->item->name); ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2"><?php echo e($boost->item->category->name ?? 'N/A'); ?></p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" 
                              style="background-color: <?php echo e($boost->boostType->color ?? '#3B82F6'); ?>;">
                            <i class="<?php echo e($boost->boostType->icon ?? 'fas fa-star'); ?> mr-1"></i>
                            <?php echo e($boost->boostType->name); ?>

                        </span>
                    </div>
                    
                    <!-- Progression -->
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Progression</div>
                        <?php
                            $progress = $boost->getProgressPercentage();
                        ?>
                        <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-1">
                            <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: <?php echo e($progress); ?>%"></div>
                        </div>
                        <span class="text-xs text-gray-600 dark:text-gray-300"><?php echo e(round($progress)); ?>%</span>
                    </div>
                    
                    <!-- Temps restant -->
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Temps restant</div>
                        <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($boost->getRemainingTimeForHumans()); ?></div>
                    </div>
                    
                    <!-- Statistiques et actions -->
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Statistiques</div>
                        <div class="flex justify-center space-x-4 text-sm mb-3">
                            <span class="flex items-center text-cyan-600" title="Vues">
                                <i class="fas fa-eye mr-1"></i><?php echo e($boost->views_generated ?? 0); ?>

                            </span>
                            <span class="flex items-center text-yellow-600" title="Clics">
                                <i class="fas fa-mouse-pointer mr-1"></i><?php echo e($boost->clicks_generated ?? 0); ?>

                            </span>
                        </div>
                        <button class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white dark:bg-gray-800 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 cancel-boost-btn" 
                                data-boost-id="<?php echo e($boost->id); ?>" 
                                data-boost-title="<?php echo e($boost->item->name); ?>">
                            <i class="fas fa-stop mr-1.5"></i>Annuler
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-12">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-rocket text-6xl text-gray-300"></i>
                </div>
                <h4 class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">Aucun boost actif</h4>
                <p class="text-gray-400 mb-6">Commencez par booster un de vos produits pour augmenter sa visibilité.</p>
                <a href="<?php echo e(route('boost.index')); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Créer un boost
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Boosts Expirés -->
        <div class="tab-pane hidden" id="expired" role="tabpanel">
            <?php $__empty_1 = true; $__currentLoopData = $expiredBoosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $boost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-4 p-6 boost-item opacity-75 border-l-4 border-l-gray-400">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-4 lg:space-y-0 lg:space-x-6">
                    <!-- Image du produit -->
                    <div class="flex-shrink-0">
                        <?php if($boost->item->images && count($boost->item->images) > 0): ?>
                        <img src="<?php echo e(Storage::url($boost->item->images[0])); ?>" alt="<?php echo e($boost->item->name); ?>" 
                             class="w-20 h-20 object-cover rounded-lg">
                        <?php else: ?>
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 flex items-center justify-center rounded-lg">
                            <i class="fas fa-image text-2xl text-gray-400"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Informations du produit -->
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1"><?php echo e($boost->item->name); ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2"><?php echo e($boost->item->category->name ?? 'N/A'); ?></p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500 text-white">
                            <i class="<?php echo e($boost->boostType->icon ?? 'fas fa-star'); ?> mr-1"></i>
                            <?php echo e($boost->boostType->name); ?>

                        </span>
                    </div>
                    
                    <!-- Durée -->
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Durée</div>
                        <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($boost->duration); ?> jour<?php echo e($boost->duration > 1 ? 's' : ''); ?></div>
                    </div>
                    
                    <!-- Coût total -->
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Coût total</div>
                        <div class="font-semibold text-gray-900 dark:text-white"><?php echo e(number_format($boost->total_price, 0, ',', ' ')); ?> CDF</div>
                    </div>
                    
                    <!-- Résultats -->
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Résultats</div>
                        <div class="flex justify-center space-x-4 text-sm mb-1">
                            <span class="flex items-center text-cyan-600" title="Vues">
                                <i class="fas fa-eye mr-1"></i><?php echo e($boost->views_generated ?? 0); ?>

                            </span>
                            <span class="flex items-center text-yellow-600" title="Clics">
                                <i class="fas fa-mouse-pointer mr-1"></i><?php echo e($boost->clicks_generated ?? 0); ?>

                            </span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Expiré le <?php echo e($boost->expires_at->format('d/m/Y')); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-12">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-clock text-6xl text-gray-300"></i>
                </div>
                <h4 class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">Aucun boost expiré</h4>
                <p class="text-gray-400">Les boosts expirés apparaîtront ici.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Boosts Annulés -->
        <div class="tab-pane hidden" id="cancelled" role="tabpanel">
            <?php $__empty_1 = true; $__currentLoopData = $cancelledBoosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $boost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-4 p-6 boost-item opacity-75 border-l-4 border-l-red-400">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-4 lg:space-y-0 lg:space-x-6">
                    <!-- Image du produit -->
                    <div class="flex-shrink-0">
                        <?php if($boost->item->images && count($boost->item->images) > 0): ?>
                        <img src="<?php echo e(Storage::url($boost->item->images[0])); ?>" alt="<?php echo e($boost->item->name); ?>" 
                             class="w-20 h-20 object-cover rounded-lg">
                        <?php else: ?>
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 flex items-center justify-center rounded-lg">
                            <i class="fas fa-image text-2xl text-gray-400"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Informations du produit -->
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1"><?php echo e($boost->item->name); ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2"><?php echo e($boost->item->category->name ?? 'N/A'); ?></p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white">
                            <i class="fas fa-times mr-1"></i>Annulé
                        </span>
                    </div>
                    
                    <!-- Durée prévue -->
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Durée prévue</div>
                        <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($boost->duration); ?> jour<?php echo e($boost->duration > 1 ? 's' : ''); ?></div>
                    </div>
                    
                    <!-- Remboursé -->
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Remboursé</div>
                        <div class="font-semibold text-green-600"><?php echo e(number_format($boost->refund_amount ?? 0, 0, ',', ' ')); ?> CDF</div>
                    </div>
                    
                    <!-- Résultats -->
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Résultats</div>
                        <div class="flex justify-center space-x-4 text-sm mb-1">
                            <span class="flex items-center text-cyan-600" title="Vues">
                                <i class="fas fa-eye mr-1"></i><?php echo e($boost->views_generated ?? 0); ?>

                            </span>
                            <span class="flex items-center text-yellow-600" title="Clics">
                                <i class="fas fa-mouse-pointer mr-1"></i><?php echo e($boost->clicks_generated ?? 0); ?>

                            </span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Annulé le <?php echo e($boost->cancelled_at?->format('d/m/Y')); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-12">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-times-circle text-6xl text-gray-300"></i>
                </div>
                <h4 class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">Aucun boost annulé</h4>
                <p class="text-gray-400">Les boosts annulés apparaîtront ici.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'annulation -->
<div class="fixed inset-0 z-50 hidden" id="cancelBoostModal" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeCancelModal()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Annuler le boost</h3>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 transition-colors" onclick="closeCancelModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6">
                <p class="text-gray-700 dark:text-gray-200 mb-4">
                    Êtes-vous sûr de vouloir annuler le boost pour le produit <strong id="boostItemTitle" class="text-gray-900 dark:text-white"></strong> ?
                </p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Politique de remboursement :</h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>• Annulation dans les 24h : remboursement complet</li>
                                <li>• Annulation après 24h : remboursement partiel basé sur le temps restant</li>
                                <li>• Annulation après 50% de la durée : aucun remboursement</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50 dark:bg-gray-900">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors" onclick="closeCancelModal()">
                    Non, garder le boost
                </button>
                <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" id="confirmCancelBoost">
                    <i class="fas fa-stop mr-2"></i>Oui, annuler le boost
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>



<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedBoostId = null;
    const cancelBoostUrlTemplate = '<?php echo e(url("boost/cancel")); ?>';

    // Gestion des onglets
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const target = this.dataset.target;
            
            // Désactiver tous les onglets
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700 dark:text-gray-200', 'hover:border-gray-300 dark:border-gray-600');
            });
            
            // Activer l'onglet cliqué
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700 dark:text-gray-200', 'hover:border-gray-300 dark:border-gray-600');
            
            // Masquer tous les contenus
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.add('hidden');
                pane.classList.remove('active');
            });
            
            // Afficher le contenu ciblé
            const targetPane = document.querySelector(target);
            if (targetPane) {
                targetPane.classList.remove('hidden');
                targetPane.classList.add('active');
            }
        });
    });

    // Fonctions pour le modal
    window.showCancelModal = function() {
        document.getElementById('cancelBoostModal').classList.remove('hidden');
    };
    
    window.closeCancelModal = function() {
        document.getElementById('cancelBoostModal').classList.add('hidden');
    };

    // Gestion de l'annulation de boost
    document.querySelectorAll('.cancel-boost-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedBoostId = this.dataset.boostId;
            const itemTitle = this.dataset.boostTitle;
            
            document.getElementById('boostItemTitle').textContent = itemTitle;
            showCancelModal();
        });
    });

    // Confirmation d'annulation
    document.getElementById('confirmCancelBoost').addEventListener('click', function() {
        if (!selectedBoostId) return;

        const btn = this;
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Annulation...';
        btn.disabled = true;

        fetch(`${cancelBoostUrlTemplate}/${selectedBoostId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Succès
                alert('Boost annulé avec succès !');
                window.location.reload();
            } else {
                // Erreur
                alert(data.message || 'Une erreur est survenue lors de l\'annulation');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'annulation du boost');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            closeCancelModal();
        });
    });

    // Fermer le modal avec Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCancelModal();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mes projets\vintApp\resources\views/boost/dashboard.blade.php ENDPATH**/ ?>