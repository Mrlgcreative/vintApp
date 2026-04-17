

<?php $__env->startSection('title', 'Détails utilisateur - ' . $user->name); ?>

<?php $__env->startSection('page-title', 'Détails de l\'utilisateur'); ?>

<?php $__env->startSection('page-actions'); ?>
<div class="flex flex-wrap gap-2">
    <a href="<?php echo e(route('admin.users.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Retour
    </a>
    <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-edit mr-2"></i>Modifier
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 text-center">
                <?php if($user->avatar): ?>
                    <img src="<?php echo e($user->avatar_url); ?>" class="rounded-full mx-auto mb-4 w-32 h-32 object-cover" alt="Avatar">
                <?php else: ?>
                    <div class="rounded-full bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center mx-auto mb-4 w-32 h-32 text-5xl font-bold">
                        <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                    </div>
                <?php endif; ?>
                
                <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-1"><?php echo e($user->name); ?></h4>
                <p class="text-gray-500 dark:text-gray-400 mb-4"><?php echo e($user->email); ?></p>
                
                <div class="flex justify-center gap-2 mb-4 flex-wrap">
                    <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo e($role->slug === 'admin' ? 'bg-red-100 text-red-700' : 'bg-primary-100 text-primary-700'); ?>">
                            <?php echo e($role->name); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <div class="flex justify-center gap-2 flex-wrap">
                    <?php if($user->is_active ?? true): ?>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">Actif</span>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">Inactif</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-6 py-4">
                <h5 class="text-xl font-bold text-white">Informations</h5>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <i class="fas fa-box text-3xl text-blue-600 mb-2"></i>
                        <h4 class="text-2xl font-bold"><?php echo e($user->items()->count()); ?></h4>
                        <p class="text-sm">Articles</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <i class="fas fa-shopping-cart text-3xl text-green-600 mb-2"></i>
                        <h4 class="text-2xl font-bold"><?php echo e($user->ordersAsBuyer()->count()); ?></h4>
                        <p class="text-sm">Achats</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mes projets\vintApp\resources\views/admin/users/show.blade.php ENDPATH**/ ?>