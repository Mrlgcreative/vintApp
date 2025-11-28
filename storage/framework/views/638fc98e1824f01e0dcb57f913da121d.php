

<?php $__env->startSection('title', 'Détails de la livraison locale'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Livraison Locale #<?php echo e($localDelivery->id); ?>

                </h1>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    <?php switch($localDelivery->status):
                        case ('proposed'): ?>
                            bg-blue-100 text-blue-800
                            <?php break; ?>
                        <?php case ('accepted'): ?>
                            bg-green-100 text-green-800
                            <?php break; ?>
                        <?php case ('in_transit'): ?>
                            bg-yellow-100 text-yellow-800
                            <?php break; ?>
                        <?php case ('delivered'): ?>
                            bg-primary-100 text-primary-800
                            <?php break; ?>
                        <?php case ('cancelled'): ?>
                            bg-red-100 text-red-800
                            <?php break; ?>
                        <?php default: ?>
                            bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                    <?php endswitch; ?>
                ">
                    <?php echo e(ucfirst(str_replace('_', ' ', $localDelivery->status))); ?>

                </span>
            </div>
            
            <div class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                <p>Commande: <a href="<?php echo e(route('orders.show', $localDelivery->order->id)); ?>" 
                    class="text-blue-600 hover:text-blue-800">#<?php echo e($localDelivery->order->order_number); ?></a></p>
                <p>Type de livraison: <?php echo e($localDelivery->delivery_type_text); ?></p>
                <p>Distance: <?php echo e($localDelivery->distance_km); ?> km</p>
                <p>Frais de livraison: <?php echo e($localDelivery->delivery_fee); ?> <?php echo e($localDelivery->currency); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Informations vendeur -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
                    <i class="fas fa-user-tie mr-2"></i>Vendeur
                </h2>
                <div class="space-y-3">
                    <p><strong>Nom:</strong> <?php echo e($localDelivery->seller->name); ?></p>
                    <p><strong>Email:</strong> <?php echo e($localDelivery->seller->email); ?></p>
                    <?php if($localDelivery->seller_phone): ?>
                        <p><strong>Téléphone:</strong> <?php echo e($localDelivery->seller_phone); ?></p>
                    <?php endif; ?>
                    <?php if($localDelivery->seller_address): ?>
                        <p><strong>Adresse:</strong> <?php echo e($localDelivery->seller_address); ?></p>
                    <?php endif; ?>
                    <?php if($localDelivery->seller_latitude && $localDelivery->seller_longitude): ?>
                        <a href="<?php echo e($localDelivery->getGoogleMapsDirectionUrl()); ?>" 
                           target="_blank" 
                           class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                            <i class="fas fa-map-marker-alt mr-2"></i>Voir l'itinéraire
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informations acheteur -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
                    <i class="fas fa-user mr-2"></i>Acheteur
                </h2>
                <div class="space-y-3">
                    <p><strong>Nom:</strong> <?php echo e($localDelivery->buyer->name); ?></p>
                    <p><strong>Email:</strong> <?php echo e($localDelivery->buyer->email); ?></p>
                    <?php if($localDelivery->buyer_phone): ?>
                        <p><strong>Téléphone:</strong> <?php echo e($localDelivery->buyer_phone); ?></p>
                    <?php endif; ?>
                    <?php if($localDelivery->buyer_address): ?>
                        <p><strong>Adresse:</strong> <?php echo e($localDelivery->buyer_address); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Actions selon le statut et l'utilisateur -->
        <?php if(auth()->guard()->check()): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Actions</h2>
            
            <?php if($localDelivery->status === 'proposed' && auth()->id() === $localDelivery->buyer_id): ?>
                <!-- L'acheteur peut accepter la proposition -->
                <div class="flex space-x-4">
                    <form action="<?php echo e(route('local-delivery.accept', $localDelivery)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md">
                            <i class="fas fa-check mr-2"></i>Accepter la livraison
                        </button>
                    </form>
                    
                    <form action="<?php echo e(route('local-delivery.cancel', $localDelivery)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="reason" value="Refusé par l'acheteur">
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-md">
                            <i class="fas fa-times mr-2"></i>Refuser
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if($localDelivery->status === 'accepted' && auth()->id() === $localDelivery->seller_id): ?>
                <!-- Le vendeur peut marquer comme en transit -->
                <form action="<?php echo e(route('local-delivery.in-transit', $localDelivery)); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-md">
                        <i class="fas fa-truck mr-2"></i>Marquer en transit
                    </button>
                </form>
            <?php endif; ?>

            <?php if($localDelivery->status === 'in_transit'): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                    <p class="text-yellow-800">
                        <strong>Code de vérification:</strong> <?php echo e($localDelivery->delivery_code); ?>

                    </p>
                    <p class="text-sm text-yellow-700 mt-2">
                        Communiquez ce code à l'acheteur lors de la remise en main propre.
                    </p>
                </div>

                <?php if(auth()->id() === $localDelivery->buyer_id): ?>
                    <!-- L'acheteur peut confirmer la livraison avec le code -->
                    <form action="<?php echo e(route('local-delivery.delivered', $localDelivery)); ?>" method="POST" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label for="delivery_code" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Code de vérification reçu du vendeur:
                            </label>
                            <input type="text" name="delivery_code" id="delivery_code" required 
                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-md">
                            <i class="fas fa-check-circle mr-2"></i>Confirmer la réception
                        </button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

            <?php if(in_array($localDelivery->status, ['proposed', 'accepted', 'in_transit']) && 
                (auth()->id() === $localDelivery->seller_id || auth()->id() === $localDelivery->buyer_id)): ?>
                <!-- Annulation possible avant la livraison -->
                <form action="<?php echo e(route('local-delivery.cancel', $localDelivery)); ?>" method="POST" class="inline mt-4">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-4">
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Raison de l'annulation (optionnel):
                            </label>
                            <input type="text" name="reason" id="reason" 
                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Expliquez pourquoi vous annulez...">
                        </div>
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-md">
                            <i class="fas fa-ban mr-2"></i>Annuler la livraison
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Historique -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Historique</h2>
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <div>
                        <p class="font-medium">Livraison proposée</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo e($localDelivery->created_at->format('d/m/Y à H:i')); ?></p>
                    </div>
                </div>

                <?php if($localDelivery->accepted_at): ?>
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <div>
                        <p class="font-medium">Livraison acceptée</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo e($localDelivery->accepted_at->format('d/m/Y à H:i')); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($localDelivery->pickup_time): ?>
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <div>
                        <p class="font-medium">En transit</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo e($localDelivery->pickup_time->format('d/m/Y à H:i')); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($localDelivery->actual_delivery_time): ?>
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-primary-500 rounded-full"></div>
                    <div>
                        <p class="font-medium">Livraison effectuée</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo e($localDelivery->actual_delivery_time->format('d/m/Y à H:i')); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($localDelivery->status === 'cancelled'): ?>
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <div>
                        <p class="font-medium">Livraison annulée</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo e($localDelivery->updated_at->format('d/m/Y à H:i')); ?></p>
                        <?php if($localDelivery->cancellation_reason): ?>
                            <p class="text-sm text-red-600">Raison: <?php echo e($localDelivery->cancellation_reason); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/local-delivery/show.blade.php ENDPATH**/ ?>