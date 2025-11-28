

<?php $__env->startSection('title', 'Mes conversations avec les vendeurs'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-blue-600 flex items-center">
                <i class="fas fa-store mr-3"></i>
                Mes conversations avec les vendeurs
            </h2>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Gérez vos discussions et demandes de réduction</p>
        </div>
        <div>
            <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au tableau de bord
            </a>
        </div>
    </div>

    <?php if($vendorContacts->count() > 0): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php $__currentLoopData = $vendorContacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <!-- En-tête avec le vendeur -->
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-t-xl p-4 flex items-center">
                        <div class="mr-3">
                            <?php if($contact->vendor->avatar): ?>
                                <img src="<?php echo e(Storage::url($contact->vendor->avatar)); ?>" 
                                     alt="<?php echo e($contact->vendor->name); ?>" 
                                     class="w-12 h-12 rounded-full object-cover">
                            <?php else: ?>
                                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white text-lg font-semibold">
                                    <?php echo e(strtoupper(substr($contact->vendor->name, 0, 1))); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h6 class="font-semibold text-gray-900 dark:text-white truncate"><?php echo e($contact->vendor->name); ?></h6>
                            <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-calendar mr-1"></i>
                                Contacté <?php echo e($contact->contact_date->diffForHumans()); ?>

                            </p>
                        </div>
                        <?php if($contact->unread_count > 0): ?>
                            <span class="bg-red-500 text-white text-xs font-medium px-2 py-1 rounded-full animate-pulse">
                                <?php echo e($contact->unread_count); ?>

                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Produit concerné -->
                    <div class="p-4">
                        <div class="flex items-start mb-4">
                            <?php if($contact->item && $contact->item->images && count($contact->item->images) > 0): ?>
                                <img src="<?php echo e(Storage::url($contact->item->images[0])); ?>" 
                                     alt="<?php echo e($contact->item->name); ?>" 
                                     class="w-20 h-20 rounded-lg object-cover mr-3 flex-shrink-0">
                            <?php else: ?>
                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-content-center mr-3 flex-shrink-0">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <?php if($contact->item): ?>
                                    <h6 class="font-semibold text-gray-900 dark:text-white mb-1 truncate"><?php echo e($contact->item->name); ?></h6>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-blue-600 font-bold"><?php echo e($contact->item->formatted_price); ?></span>
                                        <?php if($contact->has_discount): ?>
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full flex items-center">
                                                <i class="fas fa-tag mr-1"></i>
                                                Réduction accordée
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($contact->item->category->name); ?></p>
                                <?php else: ?>
                                    <h6 class="font-semibold text-gray-500 dark:text-gray-400 mb-1">Article non disponible</h6>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Dernier message -->
                        <?php if($contact->last_message): ?>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                <div class="flex items-start">
                                    <div class="mr-2 mt-1">
                                        <?php if($contact->last_message->sender_id === Auth::id()): ?>
                                            <i class="fas fa-reply text-blue-500"></i>
                                        <?php else: ?>
                                            <i class="fas fa-comment text-green-500"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-700 dark:text-gray-200 mb-1 truncate">
                                            <?php if($contact->last_message->sender_id === Auth::id()): ?>
                                                <span class="font-medium">Vous :</span>
                                            <?php else: ?>
                                                <span class="font-medium"><?php echo e($contact->vendor->name); ?> :</span>
                                            <?php endif; ?>
                                            <?php echo e($contact->last_message->content ?: 'Fichier joint'); ?>

                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            <?php echo e($contact->last_message_time); ?>

                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="p-4 pt-0">
                        <div class="flex gap-2">
                            <a href="<?php echo e(route('messages.show', ['user' => $contact->vendor_id, 'item_id' => $contact->item_id])); ?>" 
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                                <i class="fas fa-comments mr-2"></i>
                                Ouvrir la conversation
                            </a>
                            <?php if($contact->item): ?>
                                <a href="<?php echo e(route('items.show', $contact->item)); ?>" 
                                   class="bg-gray-100 dark:bg-gray-800 text-gray-700 p-2 rounded-lg hover:bg-gray-200 dark:bg-gray-700 transition-colors flex items-center justify-center">
                                    <i class="fas fa-eye"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 text-center hover:bg-gray-100 dark:bg-gray-800 transition-all duration-300 hover:-translate-y-1">
                <i class="fas fa-store text-4xl text-blue-600 mb-3"></i>
                <h5 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($vendorContacts->count()); ?></h5>
                <p class="text-sm text-gray-600 dark:text-gray-300">Vendeurs contactés</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 text-center hover:bg-gray-100 dark:bg-gray-800 transition-all duration-300 hover:-translate-y-1">
                <i class="fas fa-tag text-4xl text-green-600 mb-3"></i>
                <h5 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($vendorContacts->where('has_discount', true)->count()); ?></h5>
                <p class="text-sm text-gray-600 dark:text-gray-300">Réductions obtenues</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 text-center hover:bg-gray-100 dark:bg-gray-800 transition-all duration-300 hover:-translate-y-1">
                <i class="fas fa-envelope text-4xl text-yellow-600 mb-3"></i>
                <h5 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($vendorContacts->sum('unread_count')); ?></h5>
                <p class="text-sm text-gray-600 dark:text-gray-300">Messages non lus</p>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-16">
            <div class="mb-6">
                <i class="fas fa-store text-6xl text-gray-400"></i>
            </div>
            <h4 class="text-2xl font-medium text-gray-500 dark:text-gray-400 mb-4">Aucun vendeur contacté</h4>
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                Vous n'avez pas encore contacté de vendeurs pour demander des réductions.<br>
                Parcourez les produits et utilisez le bouton "Contacter le vendeur" pour commencer.
            </p>
            <a href="<?php echo e(route('items.index')); ?>" class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>
                Parcourir les produits
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/messages/index.blade.php ENDPATH**/ ?>