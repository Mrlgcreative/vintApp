<?php $__env->startSection('title', 'Mes notes'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        <?php echo $__env->make('seller.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Mes notes</h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Avis reçus sur vos articles</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <?php if($reviews->count() > 0): ?>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-primary-600 dark:text-primary-400 flex-shrink-0 font-semibold">
                                            <?php echo e(strtoupper(substr($review->user->name ?? 'A', 0, 2))); ?>

                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-semibold text-gray-900 dark:text-white text-sm"><?php echo e($review->user->name ?? 'Anonyme'); ?></span>
                                                <span class="flex items-center gap-0.5 text-yellow-500">
                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?php echo e($i <= ($review->rating ?? 0) ? 'text-yellow-500' : 'text-gray-200 dark:text-gray-600'); ?> text-xs"></i>
                                                    <?php endfor; ?>
                                                </span>
                                            </div>
                                            <?php if($review->comment): ?>
                                                <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo e($review->comment); ?></p>
                                            <?php endif; ?>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-xs text-gray-400 dark:text-gray-500"><?php echo e($review->created_at->format('d/m/Y')); ?></span>
                                                <?php if($review->item): ?>
                                                    <span class="text-xs text-gray-300 dark:text-gray-600">·</span>
                                                    <span class="text-xs text-gray-400 dark:text-gray-500"><?php echo e($review->item->name); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                            <?php echo e($reviews->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-16">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-star text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun avis</h3>
                            <p class="text-gray-500 dark:text-gray-400">Les avis de vos acheteurs apparaîtront ici</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/seller/reviews.blade.php ENDPATH**/ ?>