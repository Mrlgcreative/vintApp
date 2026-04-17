

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50/30 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300 mb-8">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-blue-600 transition-colors">Accueil</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 dark:text-white font-medium">Mes articles</span>
        </nav>

        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-box text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Mes articles</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez votre inventaire et vos ventes</p>
                </div>
            </div>
            <a href="<?php echo e(route('items.create')); ?>" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-300 font-semibold shadow-lg hover:shadow-xl hover:-translate-y-1">
                <i class="fas fa-plus mr-2"></i>
                Vendre un article
            </a>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-blue-100 text-sm font-medium mb-2">Total</h3>
                        <p class="text-3xl font-bold"><?php echo e($items->total()); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-white dark:bg-gray-800/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-box text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Actifs -->
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-emerald-100 text-sm font-medium mb-2">Actifs</h3>
                        <p class="text-3xl font-bold"><?php echo e($items->where('status', 'active')->count()); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-white dark:bg-gray-800/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Vendus -->
            <div class="bg-gradient-to-r from-amber-600 to-amber-700 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-amber-100 text-sm font-medium mb-2">Vendus</h3>
                        <p class="text-3xl font-bold"><?php echo e($items->where('status', 'sold')->count()); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-white dark:bg-gray-800/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Inactifs -->
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-100 text-sm font-medium mb-2">Inactifs</h3>
                        <p class="text-3xl font-bold"><?php echo e($items->where('status', 'inactive')->count()); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-white dark:bg-gray-800/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-pause-circle text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des articles -->
        <div class="w-full">
            <?php if($items->count() > 0): ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl shadow-xl shadow-blue-600/10 border border-gray-100/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-list"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Gérer mes articles</h2>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <!-- Table responsive pour desktop -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left py-4 px-2 font-semibold text-gray-700 dark:text-gray-200">Image</th>
                                        <th class="text-left py-4 px-2 font-semibold text-gray-700 dark:text-gray-200">Nom</th>
                                        <th class="text-left py-4 px-2 font-semibold text-gray-700 dark:text-gray-200">Prix</th>
                                        <th class="text-left py-4 px-2 font-semibold text-gray-700 dark:text-gray-200">Statut</th>
                                        <th class="text-left py-4 px-2 font-semibold text-gray-700 dark:text-gray-200">Vues</th>
                                        <th class="text-left py-4 px-2 font-semibold text-gray-700 dark:text-gray-200">Date</th>
                                        <th class="text-left py-4 px-2 font-semibold text-gray-700 dark:text-gray-200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 dark:bg-gray-900/50 transition-colors duration-200">
                                            <td class="py-4 px-2">
                                                <?php if($item->images && count($item->images) > 0): ?>
                                                    <img src="<?php echo e(Storage::url($item->images[0])); ?>" 
                                                         alt="<?php echo e($item->name); ?>"
                                                         class="w-12 h-12 object-cover rounded-lg shadow-sm"
                                                         loading="lazy">
                                                <?php else: ?>
                                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-image text-gray-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-4 px-2">
                                                <div>
                                                    <p class="font-semibold text-gray-900 dark:text-white"><?php echo e(Str::limit($item->name, 30)); ?></p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($item->category->name); ?></p>
                                                </div>
                                            </td>
                                            <td class="py-4 px-2">
                                                <span class="font-bold text-blue-600"><?php echo e($item->formatted_price); ?></span>
                                            </td>
                                            <td class="py-4 px-2">
                                                <select class="px-3 py-2 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:border-blue-600 focus:ring-4 focus:ring-blue-600/20 outline-none transition-all duration-300 text-sm status-select" 
                                                        data-item-id="<?php echo e($item->id); ?>">
                                                    <option value="active" <?php echo e($item->status == 'active' ? 'selected' : ''); ?>>Actif</option>
                                                    <option value="inactive" <?php echo e($item->status == 'inactive' ? 'selected' : ''); ?>>Inactif</option>
                                                    <option value="sold" <?php echo e($item->status == 'sold' ? 'selected' : ''); ?>>Vendu</option>
                                                </select>
                                            </td>
                                            <td class="py-4 px-2">
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-lg text-sm font-medium"><?php echo e($item->views); ?></span>
                                            </td>
                                            <td class="py-4 px-2">
                                                <span class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($item->created_at->format('d/m/Y')); ?></span>
                                            </td>
                                            <td class="py-4 px-2">
                                                <div class="flex space-x-2">
                                                    <a href="<?php echo e(route('items.show', $item)); ?>" 
                                                       class="w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg flex items-center justify-center transition-all duration-300"
                                                       title="Voir">
                                                        <i class="fas fa-eye text-sm"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('items.edit', $item)); ?>" 
                                                       class="w-8 h-8 bg-amber-100 hover:bg-amber-200 text-amber-600 rounded-lg flex items-center justify-center transition-all duration-300"
                                                       title="Modifier">
                                                        <i class="fas fa-edit text-sm"></i>
                                                    </a>
                                                    <?php
                                                        $hasActiveBoost = $item->activeBoosts && $item->activeBoosts->count() > 0;
                                                    ?>
                                                    <?php if(!$hasActiveBoost && $item->status === 'active'): ?>
                                                        <a href="<?php echo e(route('boost.index')); ?>?item_id=<?php echo e($item->id); ?>" 
                                                           class="w-8 h-8 bg-primary-100 hover:bg-primary-200 text-primary-600 rounded-lg flex items-center justify-center transition-all duration-300" 
                                                           title="Booster cet article">
                                                            <i class="fas fa-rocket text-sm"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-lg flex items-center justify-center" 
                                                             title="<?php echo e($hasActiveBoost ? 'Article déjà boosté' : 'Article inactif'); ?>">
                                                            <i class="fas fa-<?php echo e($hasActiveBoost ? 'star' : 'pause'); ?> text-sm"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <button type="button" 
                                                            class="w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition-all duration-300 delete-item" 
                                                            data-item-id="<?php echo e($item->id); ?>"
                                                            title="Supprimer">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Cards responsives pour mobile/tablette -->
                        <div class="lg:hidden space-y-4">
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start space-x-4">
                                        <!-- Image -->
                                        <div class="flex-shrink-0">
                                            <?php if($item->images && count($item->images) > 0): ?>
                                                <img src="<?php echo e(Storage::url($item->images[0])); ?>" 
                                                     alt="<?php echo e($item->name); ?>"
                                                     class="w-16 h-16 object-cover rounded-lg shadow-sm"
                                                     loading="lazy">
                                            <?php else: ?>
                                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Contenu -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h3 class="font-semibold text-gray-900 dark:text-white truncate"><?php echo e($item->name); ?></h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($item->category->name); ?></p>
                                                </div>
                                                <span class="font-bold text-blue-600 text-lg"><?php echo e($item->formatted_price); ?></span>
                                            </div>
                                            
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center space-x-3">
                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium"><?php echo e($item->views); ?> vues</span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($item->created_at->format('d/m/Y')); ?></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Statut -->
                                            <div class="mb-3">
                                                <select class="w-full px-3 py-2 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:border-blue-600 focus:ring-4 focus:ring-blue-600/20 outline-none transition-all duration-300 text-sm status-select" 
                                                        data-item-id="<?php echo e($item->id); ?>">
                                                    <option value="active" <?php echo e($item->status == 'active' ? 'selected' : ''); ?>>Actif</option>
                                                    <option value="inactive" <?php echo e($item->status == 'inactive' ? 'selected' : ''); ?>>Inactif</option>
                                                    <option value="sold" <?php echo e($item->status == 'sold' ? 'selected' : ''); ?>>Vendu</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="flex space-x-2">
                                                <a href="<?php echo e(route('items.show', $item)); ?>" 
                                                   class="flex-1 bg-blue-100 hover:bg-blue-200 text-blue-600 py-2 rounded-lg flex items-center justify-center transition-all duration-300">
                                                    <i class="fas fa-eye mr-2"></i>
                                                    Voir
                                                </a>
                                                <a href="<?php echo e(route('items.edit', $item)); ?>" 
                                                   class="flex-1 bg-amber-100 hover:bg-amber-200 text-amber-600 py-2 rounded-lg flex items-center justify-center transition-all duration-300">
                                                    <i class="fas fa-edit mr-2"></i>
                                                    Modifier
                                                </a>
                                                <?php
                                                    $hasActiveBoost = $item->activeBoosts && $item->activeBoosts->count() > 0;
                                                ?>
                                                <?php if(!$hasActiveBoost && $item->status === 'active'): ?>
                                                    <a href="<?php echo e(route('boost.index')); ?>?item_id=<?php echo e($item->id); ?>" 
                                                       class="flex-1 bg-primary-100 hover:bg-primary-200 text-primary-600 py-2 rounded-lg flex items-center justify-center transition-all duration-300">
                                                        <i class="fas fa-rocket mr-2"></i>
                                                        Booster
                                                    </a>
                                                <?php endif; ?>
                                                <button type="button" 
                                                        class="flex-1 bg-red-100 hover:bg-red-200 text-red-600 py-2 rounded-lg flex items-center justify-center transition-all duration-300 delete-item" 
                                                        data-item-id="<?php echo e($item->id); ?>">
                                                    <i class="fas fa-trash mr-2"></i>
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-center mt-8">
                            <?php echo e($items->links()); ?>

                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl shadow-xl shadow-blue-600/10 border border-gray-100/50 p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-box-open text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Vous n'avez pas encore d'articles</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-8">Commencez par vendre votre premier article !</p>
                    <a href="<?php echo e(route('items.create')); ?>" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-4 rounded-xl transition-all duration-300 font-bold text-lg shadow-lg hover:shadow-xl hover:-translate-y-1 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Vendre mon premier article
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du changement de statut
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const newStatus = this.value;
            
            // Ajouter un indicateur de chargement
            const originalContent = this.innerHTML;
            this.disabled = true;
            this.style.opacity = '0.7';
            
            fetch(`/items/${itemId}/status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    this.dataset.originalValue = newStatus;
                } else {
                    showNotification('Erreur lors de la mise à jour', 'error');
                    // Remettre l'ancienne valeur
                    this.value = this.dataset.originalValue;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Une erreur est survenue', 'error');
                // Remettre l'ancienne valeur
                this.value = this.dataset.originalValue;
            })
            .finally(() => {
                this.disabled = false;
                this.style.opacity = '1';
            });
        });
        
        // Sauvegarder la valeur originale
        select.dataset.originalValue = select.value;
    });

    // Gestion de la suppression
    const deleteButtons = document.querySelectorAll('.delete-item');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            const row = this.closest('tr') || this.closest('.bg-gray-50 dark:bg-gray-900');
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.')) {
                // Désactiver le bouton pendant la suppression
                this.disabled = true;
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                fetch(`/items/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        // Animation de suppression
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-100%)';
                        setTimeout(() => {
                            row.remove();
                            // Vérifier s'il reste des éléments
                            const remainingItems = document.querySelectorAll('.delete-item');
                            if (remainingItems.length === 0) {
                                // Recharger la page pour afficher le message "aucun article"
                                window.location.reload();
                            }
                        }, 300);
                    } else {
                        showNotification(data.message || 'Erreur lors de la suppression', 'error');
                        // Réactiver le bouton
                        this.disabled = false;
                        this.innerHTML = originalContent;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Une erreur est survenue lors de la suppression', 'error');
                    // Réactiver le bouton
                    this.disabled = false;
                    this.innerHTML = originalContent;
                });
            }
        });
    });
});

// Fonction pour afficher les notifications
function showNotification(message, type = 'info') {
    const colors = {
        success: 'from-emerald-500 to-emerald-600',
        error: 'from-red-500 to-red-600',
        warning: 'from-amber-500 to-amber-600',
        info: 'from-blue-500 to-blue-600'
    };

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    const notification = document.createElement('div');
    notification.className = `fixed top-5 right-5 z-50 min-w-80 max-w-md bg-gradient-to-r ${colors[type]} text-white rounded-2xl shadow-2xl p-4 transform transition-all duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="w-6 h-6 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas ${icons[type]}"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                class="w-6 h-6 rounded-full hover:bg-white dark:bg-gray-800/20 flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Suppression automatique
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}


</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mes projets\vintApp\resources\views/items/my-items.blade.php ENDPATH**/ ?>