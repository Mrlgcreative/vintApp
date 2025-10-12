

<?php $__env->startSection('title', 'Détails de la commande #' . $order->id); ?>
<?php $__env->startSection('page-title', 'Détails de la commande #' . $order->id); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('admin.orders.index')); ?>" class="inline-flex items-center text-primary-600 hover:text-primary-700">
        <i class="fas fa-arrow-left mr-2"></i> Retour aux commandes
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne principale -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Informations de la commande -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-primary-50 to-primary-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-shopping-cart text-primary-600 mr-2"></i>
                    Commande #<?php echo e($order->id); ?>

                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Date de commande</label>
                        <p class="text-base text-gray-900">
                            <i class="far fa-calendar text-gray-400 mr-2"></i>
                            <?php echo e($order->created_at->format('d/m/Y à H:i')); ?>

                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Statut</label>
                        <p>
                            <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'shipped' => 'bg-purple-100 text-purple-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'En attente',
                                    'confirmed' => 'Confirmée',
                                    'shipped' => 'Expédiée',
                                    'delivered' => 'Livrée',
                                    'cancelled' => 'Annulée',
                                ];
                            ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($statusColors[$order->status] ?? 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo e($statusLabels[$order->status] ?? $order->status); ?>

                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Quantité</label>
                        <p class="text-base text-gray-900">
                            <i class="fas fa-box text-gray-400 mr-2"></i>
                            <?php echo e($order->quantity); ?> article(s)
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Montant total</label>
                        <p class="text-xl font-bold text-primary-600">
                            <?php echo e(number_format($order->total_price, 2)); ?> <?php echo e($order->currency ?? 'USD'); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Article commandé -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-tag text-primary-600 mr-2"></i>
                    Article commandé
                </h3>
            </div>
            <div class="p-6">
                <?php if($order->item): ?>
                <div class="flex items-start gap-4">
                    <?php if(!empty($order->item->images) && is_array($order->item->images)): ?>
                    <img src="<?php echo e(Storage::url($order->item->images[0])); ?>" 
                         alt="<?php echo e($order->item->name); ?>" 
                         class="w-24 h-24 object-cover rounded-lg border border-gray-200">
                    <?php else: ?>
                    <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                    </div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 text-lg mb-2"><?php echo e($order->item->name); ?></h4>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?php echo e($order->item->description); ?></p>
                        <div class="flex flex-wrap gap-3">
                            <?php if($order->item->category): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-folder mr-1"></i> <?php echo e($order->item->category->name); ?>

                            </span>
                            <?php endif; ?>
                            <?php if($order->item->brand): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-copyright mr-1"></i> <?php echo e($order->item->brand->name); ?>

                            </span>
                            <?php endif; ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-dollar-sign mr-1"></i> <?php echo e(number_format($order->item->price, 2)); ?> <?php echo e($order->item->currency ?? 'USD'); ?>

                            </span>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-center py-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Article non disponible
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Transaction associée -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-receipt text-primary-600 mr-2"></i>
                    Informations de paiement
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Numéro de commande</label>
                        <p class="text-base text-gray-900 font-mono"><?php echo e($order->order_number); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Montant payé</label>
                        <p class="text-base text-gray-900 font-semibold">
                            <?php echo e(number_format($order->total_amount, 2)); ?> <?php echo e($order->currency ?? 'USD'); ?>

                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Statut du paiement</label>
                        <p>
                            <?php if($order->paid_at): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Payé
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> En attente
                            </span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php if($order->paid_at): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Date de paiement</label>
                        <p class="text-base text-gray-900">
                            <i class="far fa-calendar text-gray-400 mr-2"></i>
                            <?php echo e($order->paid_at->format('d/m/Y à H:i')); ?>

                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Colonne latérale -->
    <div class="space-y-6">
        <!-- Acheteur -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-blue-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user text-blue-600 mr-2"></i>
                    Acheteur
                </h3>
            </div>
            <div class="p-6">
                <?php if($order->buyer): ?>
                <div class="text-center mb-4">
                    <?php if($order->buyer->profile_image): ?>
                    <img src="<?php echo e(Storage::url($order->buyer->profile_image)); ?>" 
                         alt="<?php echo e($order->buyer->name); ?>" 
                         class="w-20 h-20 rounded-full mx-auto mb-3 border-2 border-blue-200">
                    <?php else: ?>
                    <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user text-blue-600 text-2xl"></i>
                    </div>
                    <?php endif; ?>
                    <h4 class="font-semibold text-gray-900"><?php echo e($order->buyer->name); ?></h4>
                    <p class="text-sm text-gray-500"><?php echo e($order->buyer->email); ?></p>
                </div>
                <div class="space-y-2 pt-4 border-t border-gray-100">
                    <?php if($order->buyer->phone): ?>
                    <p class="text-sm">
                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                        <?php echo e($order->buyer->phone); ?>

                    </p>
                    <?php endif; ?>
                    <?php if($order->buyer->city): ?>
                    <p class="text-sm">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                        <?php echo e($order->buyer->city); ?>

                    </p>
                    <?php endif; ?>
                    <a href="<?php echo e(route('admin.users.show', $order->buyer_id)); ?>" 
                       class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 mt-2">
                        <i class="fas fa-external-link-alt mr-1"></i> Voir le profil
                    </a>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-center">Utilisateur non disponible</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Vendeur -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-green-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-store text-green-600 mr-2"></i>
                    Vendeur
                </h3>
            </div>
            <div class="p-6">
                <?php if($order->seller): ?>
                <div class="text-center mb-4">
                    <?php if($order->seller->profile_image): ?>
                    <img src="<?php echo e(Storage::url($order->seller->profile_image)); ?>" 
                         alt="<?php echo e($order->seller->name); ?>" 
                         class="w-20 h-20 rounded-full mx-auto mb-3 border-2 border-green-200">
                    <?php else: ?>
                    <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user text-green-600 text-2xl"></i>
                    </div>
                    <?php endif; ?>
                    <h4 class="font-semibold text-gray-900"><?php echo e($order->seller->name); ?></h4>
                    <p class="text-sm text-gray-500"><?php echo e($order->seller->email); ?></p>
                </div>
                <div class="space-y-2 pt-4 border-t border-gray-100">
                    <?php if($order->seller->phone): ?>
                    <p class="text-sm">
                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                        <?php echo e($order->seller->phone); ?>

                    </p>
                    <?php endif; ?>
                    <?php if($order->seller->city): ?>
                    <p class="text-sm">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                        <?php echo e($order->seller->city); ?>

                    </p>
                    <?php endif; ?>
                    <a href="<?php echo e(route('admin.users.show', $order->seller_id)); ?>" 
                       class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 mt-2">
                        <i class="fas fa-external-link-alt mr-1"></i> Voir le profil
                    </a>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-center">Utilisateur non disponible</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-cog text-primary-600 mr-2"></i>
                    Actions
                </h3>
            </div>
            <div class="p-6 space-y-3">
                <button onclick="window.print()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-print mr-2"></i> Imprimer
                </button>
                <a href="<?php echo e(route('admin.orders.index')); ?>" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-list mr-2"></i> Toutes les commandes
                </a>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<style>
@media print {
    .sidebar, nav, button, a[href] {
        display: none !important;
    }
    .lg\:col-span-2 {
        grid-column: span 3 / span 3 !important;
    }
}
</style>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>