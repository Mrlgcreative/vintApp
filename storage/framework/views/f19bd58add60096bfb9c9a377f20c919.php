

<?php $__env->startSection('title', 'Confirmation de réception - Commande #' . $order->order_number); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Messages de notification -->
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if(session('info')): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <?php echo e(session('info')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- En-tête de la commande -->
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <?php if($order->confirmed_by_buyer_at): ?>
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                        <?php else: ?>
                            <i class="fas fa-qrcode text-primary" style="font-size: 3rem;"></i>
                        <?php endif; ?>
                    </div>
                    <h4 class="mb-2">Commande #<?php echo e($order->order_number); ?></h4>
                    <p class="text-muted mb-2">
                        <i class="fas fa-calendar me-1"></i>
                        <?php echo e($order->created_at->format('d/m/Y à H:i')); ?>

                    </p>
                    <?php if($order->confirmed_by_buyer_at): ?>
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>
                            Réception confirmée le <?php echo e($order->confirmed_by_buyer_at->format('d/m/Y à H:i')); ?>

                        </span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-clock me-1"></i>
                            En attente de confirmation
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Image et détails du produit -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        Détails du produit
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Image du produit -->
                    <div class="text-center mb-3">
                        <?php if($order->item && $order->item->images && is_array($order->item->images) && count($order->item->images) > 0): ?>
                            <img src="<?php echo e(asset('storage/' . $order->item->images[0])); ?>" 
                                 alt="<?php echo e($order->item->name); ?>" 
                                 class="img-fluid rounded shadow-sm"
                                 style="max-height: 300px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                 style="height: 300px;">
                                <i class="fas fa-image text-muted" style="font-size: 4rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Nom et description -->
                    <h5 class="mb-2"><?php echo e($order->item->name); ?></h5>
                    
                    <?php if($order->item->description): ?>
                        <p class="text-muted mb-3"><?php echo e(Str::limit($order->item->description, 150)); ?></p>
                    <?php endif; ?>
                    
                    <!-- Prix et quantité -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <small class="text-muted d-block">Prix unitaire</small>
                                <strong><?php echo e(number_format($order->price, 2)); ?> <?php echo e($order->currency); ?></strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <small class="text-muted d-block">Quantité</small>
                                <strong><?php echo e($order->quantity); ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Montant total -->
                    <div class="bg-light rounded p-3 text-center">
                        <small class="text-muted d-block mb-1">Montant total</small>
                        <h4 class="mb-0 text-primary">
                            <?php echo e(number_format($order->total_amount, 2)); ?> <?php echo e($order->currency); ?>

                        </h4>
                    </div>
                </div>
            </div>
            
            <!-- Informations de livraison -->
            <?php if($order->deliveryAddress): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Adresse de livraison
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <i class="fas fa-user me-2 text-primary"></i>
                        <strong><?php echo e($order->deliveryAddress->full_name); ?></strong>
                    </p>
                    
                    <?php if($order->deliveryAddress->phone): ?>
                    <p class="mb-2">
                        <i class="fas fa-phone me-2 text-primary"></i>
                        <a href="tel:<?php echo e($order->deliveryAddress->phone); ?>">
                            <?php echo e($order->deliveryAddress->phone); ?>

                        </a>
                    </p>
                    <?php endif; ?>
                    
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <?php echo e($order->deliveryAddress->address); ?>

                    </p>
                    
                    <p class="mb-0">
                        <i class="fas fa-city me-2 text-primary"></i>
                        <?php echo e($order->deliveryAddress->city); ?>

                        <?php if($order->deliveryAddress->commune): ?>
                            , <?php echo e($order->deliveryAddress->commune); ?>

                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Informations vendeur -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2"></i>
                        Vendeur
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <?php if($order->seller && $order->seller->profile_picture): ?>
                            <img src="<?php echo e(asset('storage/' . $order->seller->profile_picture)); ?>" 
                                 alt="<?php echo e($order->seller->name); ?>" 
                                 class="rounded-circle me-3"
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h6 class="mb-0"><?php echo e($order->seller->name ?? 'Vendeur'); ?></h6>
                            <?php if($order->seller && $order->seller->email): ?>
                                <small class="text-muted"><?php echo e($order->seller->email); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bouton de confirmation -->
            <?php if(!$order->confirmed_by_buyer_at): ?>
            <div class="card shadow-sm border-success">
                <div class="card-body text-center py-4">
                    <h5 class="mb-3">Confirmez la réception de votre commande</h5>
                    <p class="text-muted mb-4">
                        En confirmant, vous attestez avoir bien reçu votre commande dans les conditions décrites.
                        Les fonds seront alors transférés au vendeur.
                    </p>
                    
                    <form action="<?php echo e(route('orders.scan.confirm', $order->scan_token)); ?>" method="POST" id="confirmForm">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Note optionnelle -->
                        <div class="mb-3">
                            <textarea name="note" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Note optionnelle (satisfaction, remarques...)"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg w-100" id="confirmBtn">
                            <i class="fas fa-check-circle me-2"></i>
                            Confirmer la réception
                        </button>
                    </form>
                </div>
            </div>
            
            <script>
                document.getElementById('confirmForm').addEventListener('submit', function(e) {
                    const btn = document.getElementById('confirmBtn');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Confirmation en cours...';
                });
            </script>
            <?php else: ?>
            <!-- Déjà confirmé -->
            <div class="card shadow-sm border-success">
                <div class="card-body text-center py-4">
                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                    <h5 class="text-success mb-2">Réception confirmée</h5>
                    <p class="text-muted mb-0">
                        Cette commande a été confirmée le <?php echo e($order->confirmed_by_buyer_at->format('d/m/Y à H:i')); ?>

                    </p>
                    
                    <?php if($order->buyer_confirmation_note): ?>
                        <div class="mt-3 p-3 bg-light rounded">
                            <small class="text-muted d-block mb-1">Note de confirmation :</small>
                            <p class="mb-0"><?php echo e($order->buyer_confirmation_note); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Lien vers les commandes -->
            <div class="text-center mt-4">
                <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Voir toutes mes commandes
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/orders/scan.blade.php ENDPATH**/ ?>