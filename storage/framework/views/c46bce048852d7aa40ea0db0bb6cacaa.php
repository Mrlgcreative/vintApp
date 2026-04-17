<?php $__env->startSection('title', 'Mes VintPass'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-blue-600 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">🏆</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes VintPass</h1>
                    <p class="text-gray-600 dark:text-gray-400">Certificats d'authenticité de vos articles</p>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo e($passes->total()); ?></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total VintPass</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <p class="text-3xl font-bold text-green-600"><?php echo e($passes->where('status', 'active')->count()); ?></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Actifs</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <p class="text-3xl font-bold text-purple-600"><?php echo e($passes->sum('scan_count')); ?></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Scans totaux</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <p class="text-3xl font-bold text-blue-600"><?php echo e(number_format($passes->avg('final_score') ?? 0, 1)); ?>%</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Score moyen</p>
                </div>
            </div>
        </div>

        <!-- VintPass Grid -->
        <?php if($passes->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $passes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pass): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Header with badge -->
                <div class="bg-gradient-to-r from-gray-900 to-gray-800 p-4 relative">
                    <div class="absolute top-2 right-2">
                        <?php
                            $level = $pass->authenticity_level;
                            $badgeClass = match($level['level']) {
                                'platinum' => 'bg-purple-500',
                                'gold' => 'bg-yellow-500',
                                'silver' => 'bg-gray-400',
                                'bronze' => 'bg-orange-500',
                                default => 'bg-blue-500',
                            };
                        ?>
                        <span class="<?php echo e($badgeClass); ?> text-white text-xs px-2 py-1 rounded-full">
                            <?php echo e($level['icon']); ?> <?php echo e($level['label']); ?>

                        </span>
                    </div>
                    <p class="text-gray-400 text-xs">ID Certificat</p>
                    <p class="text-white font-mono font-bold"><?php echo e($pass->pass_id); ?></p>
                </div>

                <!-- Item Preview -->
                <div class="p-4 flex gap-4">
                    <?php if($pass->item && $pass->item->first_image_url): ?>
                    <img src="<?php echo e($pass->item->first_image_url); ?>" 
                         alt="<?php echo e($pass->item->name); ?>"
                         class="w-20 h-20 object-cover rounded-xl">
                    <?php else: ?>
                    <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">📦</span>
                    </div>
                    <?php endif; ?>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 dark:text-white truncate">
                            <?php echo e($pass->item_snapshot['name'] ?? $pass->item?->name ?? 'Article'); ?>

                        </h3>
                        <?php if($pass->item_snapshot['brand'] ?? $pass->item?->brand): ?>
                        <p class="text-blue-600 text-sm">
                            <?php echo e($pass->item_snapshot['brand'] ?? $pass->item->brand->name); ?>

                        </p>
                        <?php endif; ?>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-lg font-bold text-green-600"><?php echo e(number_format($pass->final_score, 1)); ?>%</span>
                            <span class="text-gray-400 text-sm">Score</span>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="px-4 pb-4">
                    <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                        <span>🔍 <?php echo e($pass->scan_count); ?> scans</span>
                        <span>🔄 <?php echo e($pass->transfer_count); ?> transferts</span>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center gap-2 mb-4">
                        <?php if($pass->status === 'active'): ?>
                        <span class="flex items-center gap-1 text-green-600 text-sm">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Actif
                        </span>
                        <?php elseif($pass->status === 'pending'): ?>
                        <span class="flex items-center gap-1 text-yellow-600 text-sm">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            En attente
                        </span>
                        <?php else: ?>
                        <span class="flex items-center gap-1 text-red-600 text-sm">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            <?php echo e(ucfirst($pass->status)); ?>

                        </span>
                        <?php endif; ?>
                        
                        <?php if($pass->blockchain_hash): ?>
                        <span class="text-blue-500 text-sm ml-auto">🔗 Blockchain</span>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="<?php echo e(route('vintpass.show', $pass)); ?>" 
                           class="flex-1 bg-gray-900 dark:bg-gray-700 text-white text-center py-2 rounded-lg text-sm hover:bg-gray-800 transition-colors">
                            Voir détails
                        </a>
                        <a href="<?php echo e(route('vintpass.download-qr', ['vintPass' => $pass])); ?>" 
                           class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors"
                           title="Télécharger QR Code">
                            📱
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            <?php echo e($passes->links()); ?>

        </div>
        
        <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl">🏆</span>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Aucun VintPass</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                Faites vérifier l'authenticité de vos articles pour obtenir un VintPass - 
                votre certificat d'authenticité blockchain inviolable.
            </p>
            <a href="<?php echo e(route('items.index')); ?>" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full transition-colors">
                <span>Voir mes articles</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        <?php endif; ?>

        <!-- Info Banner -->
        <div class="mt-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-3xl">💎</span>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-xl font-bold mb-2">Qu'est-ce que le VintPass ?</h3>
                    <p class="text-white/80">
                        Le VintPass est un certificat d'authenticité numérique inviolable, 
                        stocké sur la blockchain. Il garantit l'authenticité de vos articles 
                        et les suit tout au long de leur vie, même lors de reventes.
                    </p>
                </div>
                <a href="#" class="bg-white text-blue-600 px-6 py-3 rounded-full font-semibold hover:bg-blue-50 transition-colors flex-shrink-0">
                    En savoir plus
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mes projets\vintApp\resources\views/vintpass/my-passes.blade.php ENDPATH**/ ?>