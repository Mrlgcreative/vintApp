<?php $__env->startSection('title', 'Mes articles'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        <?php echo $__env->make('seller.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main class="flex-1 p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Mes articles</h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-1"><?php echo e($items->total()); ?> article(s) publié(s)</p>
                    </div>
                    <a href="<?php echo e(route('items.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors">
                        <i class="fas fa-plus"></i> Nouvel article
                    </a>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <?php if($items->count() > 0): ?>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                                        <?php if($item->images && count($item->images) > 0): ?>
                                            <img src="<?php echo e(Storage::url($item->images[0])); ?>" class="w-full h-full object-cover" alt="">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h6 class="font-semibold text-gray-900 dark:text-white truncate"><?php echo e($item->name); ?></h6>
                                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($item->category->name ?? 'N/A'); ?> · <?php echo e($item->views); ?> vues</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="font-bold text-gray-900 dark:text-white"><?php echo e($item->formatted_price); ?></p>
                                        <span class="text-xs px-2 py-0.5 rounded-full <?php echo e($item->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'); ?>"><?php echo e($item->status); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="<?php echo e(route('boost.index', ['item_id' => $item->id])); ?>" class="px-3 py-2 text-sm rounded-lg border border-yellow-300 dark:border-yellow-700 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors" title="Booster">
                                            <i class="fas fa-rocket"></i>
                                        </a>
                                        <a href="<?php echo e(route('items.edit', $item)); ?>" class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                            <?php echo e($items->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-16">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-box text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun article</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">Commencez par publier votre premier article</p>
                            <a href="<?php echo e(route('items.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors">
                                <i class="fas fa-plus"></i> Publier un article
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/seller/items.blade.php ENDPATH**/ ?>