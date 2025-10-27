

<?php $__env->startSection('title', 'Wallets Pending'); ?>
<?php $__env->startSection('page-title', 'Wallets Pending - Argent en Attente de Confirmation'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-blue-900">À propos des Wallets Pending</h3>
                <div class="mt-2 text-sm text-blue-700 space-y-1">
                    <p>• Ces wallets contiennent de l'argent en attente de confirmation de réception par l'acheteur</p>
                    <p>• L'argent reste bloqué jusqu'à ce que l'acheteur clique sur "Commande Reçue"</p>
                    <p>• Après confirmation, la distribution automatique transfère : Commission (10%) + Transport (5%) → Plateforme | Reste (85%) → Vendeur</p>
                    <p>• <strong>Type</strong> : "pending" (sécurisé, non retirable) | <strong>Type</strong> : "main" (retirable après confirmation)</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-wallet text-yellow-600 mr-2"></i>
                Wallets Pending (<?php echo e($pendingWallets->total()); ?>)
            </h3>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Total en attente :</span>
                <span class="text-lg font-bold text-yellow-600">
                    $<?php echo e(number_format($pendingWallets->where('currency', 'USD')->sum('balance'), 2)); ?>

                </span>
                <span class="text-gray-400">|</span>
                <span class="text-lg font-bold text-yellow-600">
                    <?php echo e(number_format($pendingWallets->where('currency', 'CDF')->sum('balance'), 0, ',', ' ')); ?> FC
                </span>
            </div>
        </div>
    </div>
    <div class="p-0">
        <?php if($pendingWallets->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendeur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Devise</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière MAJ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $pendingWallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if($wallet->user->avatar): ?>
                                            <img src="<?php echo e($wallet->user->avatar_url); ?>" class="w-10 h-10 rounded-full mr-3" alt="Avatar">
                                        <?php else: ?>
                                            <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                                                <?php echo e($wallet->user->initial); ?>

                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($wallet->user->name); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo e($wallet->user->email); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($wallet->currency === 'USD' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'); ?>">
                                        <?php echo e($wallet->currency); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        <?php echo e(number_format($wallet->balance, $wallet->currency === 'USD' ? 2 : 0, ',', ' ')); ?> <?php echo e($wallet->currency); ?>

                                    </div>
                                    <?php
                                        $commission = $wallet->balance * 0.10;
                                        $transport = $wallet->balance * 0.05;
                                        $seller = $wallet->balance - $commission - $transport;
                                    ?>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Distribution : <?php echo e(number_format($seller, $wallet->currency === 'USD' ? 2 : 0)); ?> vendeur + <?php echo e(number_format($commission + $transport, $wallet->currency === 'USD' ? 2 : 0)); ?> plateforme
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-lock mr-1"></i>
                                        <?php echo e(ucfirst($wallet->type)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($wallet->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        <?php echo e($wallet->is_active ? 'Actif' : 'Inactif'); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div><?php echo e($wallet->updated_at->format('d/m/Y H:i')); ?></div>
                                    <div class="text-xs text-gray-400"><?php echo e($wallet->updated_at->diffForHumans()); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?php echo e(route('admin.users.show', $wallet->user)); ?>" 
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-150"
                                           title="Voir utilisateur">
                                            <i class="fas fa-user text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Profil</span>
                                        </a>
                                        <a href="<?php echo e(route('orders.index')); ?>?seller_id=<?php echo e($wallet->user_id); ?>&status=pending" 
                                           class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors duration-150"
                                           title="Voir commandes en attente">
                                            <i class="fas fa-shopping-cart text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Commandes</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-600"></i>
                </div>
                <h5 class="text-lg font-semibold text-gray-900 mb-2">Aucun wallet pending</h5>
                <p class="text-gray-500 mb-4">Tous les paiements ont été confirmés et distribués.</p>
                <p class="text-sm text-gray-400">Les wallets pending apparaissent ici lorsqu'un acheteur paie mais n'a pas encore confirmé la réception.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if($pendingWallets->hasPages()): ?>
        <div class="border-t border-gray-200 px-6 py-4">
            <?php echo e($pendingWallets->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/wallets/pending.blade.php ENDPATH**/ ?>