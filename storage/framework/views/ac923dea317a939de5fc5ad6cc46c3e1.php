

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">
            <i class="fas fa-store me-2 text-primary"></i>
            Mes Ventes
        </h1>
        <span class="badge bg-primary rounded-pill fs-6">
            <?php echo e($orders->total()); ?> commande(s)
        </span>
    </div>

    <!-- Statistiques rapides -->
    <?php if($orders->count() > 0): ?>
        <div class="row g-3 mb-4">
            <!-- En attente (à payer) -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-start border-warning border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-warning text-uppercase fw-semibold small mb-1">En attente</h6>
                                <h2 class="mb-0 fw-bold"><?php echo e($orders->where('status', 'pending')->count()); ?></h2>
                                <p class="text-muted small mb-0">Paiement attendu</p>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payées (à expédier) -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-start border-primary border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-primary text-uppercase fw-semibold small mb-1">À expédier</h6>
                                <h2 class="mb-0 fw-bold"><?php echo e($orders->where('status', 'confirmed')->count()); ?></h2>
                                <p class="text-muted small mb-0">Prêtes à envoyer</p>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-box fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expédiées -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-start border-info border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-info text-uppercase fw-semibold small mb-1">En transit</h6>
                                <h2 class="mb-0 fw-bold"><?php echo e($orders->where('status', 'shipped')->count()); ?></h2>
                                <p class="text-muted small mb-0">En livraison</p>
                            </div>
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-shipping-fast fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Livrées/Terminées -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-start border-success border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-success text-uppercase fw-semibold small mb-1">Terminées</h6>
                                <h2 class="mb-0 fw-bold"><?php echo e($orders->whereIn('status', ['delivered', 'completed'])->count()); ?></h2>
                                <p class="text-muted small mb-0">Paiement distribué</p>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filtres rapides -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo e(route('orders.my-sales')); ?>" 
                   class="btn <?php echo e(!request('status') ? 'btn-primary' : 'btn-outline-secondary'); ?>">
                    <i class="fas fa-list me-2"></i>
                    Toutes (<?php echo e($orders->total()); ?>)
                </a>
                <a href="<?php echo e(route('orders.my-sales', ['status' => 'pending'])); ?>" 
                   class="btn <?php echo e(request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning'); ?>">
                    <i class="fas fa-clock me-2"></i>
                    En attente
                </a>
                <a href="<?php echo e(route('orders.my-sales', ['status' => 'confirmed'])); ?>" 
                   class="btn <?php echo e(request('status') === 'confirmed' ? 'btn-primary' : 'btn-outline-primary'); ?>">
                    <i class="fas fa-box me-2"></i>
                    À expédier
                </a>
                <a href="<?php echo e(route('orders.my-sales', ['status' => 'shipped'])); ?>" 
                   class="btn <?php echo e(request('status') === 'shipped' ? 'btn-info' : 'btn-outline-info'); ?>">
                    <i class="fas fa-shipping-fast me-2"></i>
                    Expédiées
                </a>
                <a href="<?php echo e(route('orders.my-sales', ['status' => 'delivered,completed'])); ?>" 
                   class="btn <?php echo e(in_array(request('status'), ['delivered', 'completed']) ? 'btn-success' : 'btn-outline-success'); ?>">
                    <i class="fas fa-check-circle me-2"></i>
                    Terminées
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des commandes -->
    <div class="card shadow-sm">
        <?php if($orders->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Commande</th>
                            <th>Article</th>
                            <th>Acheteur</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div class="fw-bold">#<?php echo e($order->id); ?></div>
                                    <small class="text-muted"><?php echo e($order->order_number); ?></small>
                                    <div><small class="text-muted">Qté: <?php echo e($order->quantity); ?></small></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if($order->item && $order->item->images && count($order->item->images) > 0): ?>
                                            <img src="<?php echo e(asset('storage/' . $order->item->images[0])); ?>" 
                                                 class="rounded me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 alt="<?php echo e($order->item->name); ?>">
                                        <?php else: ?>
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-semibold"><?php echo e(Str::limit($order->item->name ?? 'Article supprimé', 30)); ?></div>
                                            <small class="text-muted"><?php echo e($order->item->category->name ?? 'Catégorie inconnue'); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if($order->buyer && $order->buyer->avatar): ?>
                                            <img src="<?php echo e($order->buyer->avatar_url); ?>" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 35px; height: 35px; object-fit: cover;"
                                                 alt="Avatar">
                                        <?php else: ?>
                                            <div class="bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center text-primary fw-semibold" 
                                                 style="width: 35px; height: 35px; font-size: 0.75rem;">
                                                <?php echo e($order->buyer->initial ?? '?'); ?>

                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-semibold"><?php echo e($order->buyer->name ?? 'Utilisateur inconnu'); ?></div>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo e($order->shipping_city ?? 'Ville non spécifiée'); ?>

                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo e(number_format($order->total_amount, 2)); ?> <?php echo e($order->currency); ?></div>
                                    <small class="text-muted"><?php echo e(number_format($order->unit_price, 2)); ?> × <?php echo e($order->quantity); ?></small>
                                </td>
                                <td>
                                    <?php
                                        $statusConfig = [
                                            'pending' => ['class' => 'bg-warning', 'icon' => 'fa-clock'],
                                            'confirmed' => ['class' => 'bg-primary', 'icon' => 'fa-check'],
                                            'shipped' => ['class' => 'bg-info', 'icon' => 'fa-shipping-fast'],
                                            'delivered' => ['class' => 'bg-success', 'icon' => 'fa-box-check'],
                                            'completed' => ['class' => 'bg-success', 'icon' => 'fa-check-circle'],
                                            'cancelled' => ['class' => 'bg-danger', 'icon' => 'fa-times-circle'],
                                        ];
                                        $config = $statusConfig[$order->status] ?? ['class' => 'bg-secondary', 'icon' => 'fa-question'];
                                    ?>
                                    <span class="badge <?php echo e($config['class']); ?>">
                                        <i class="fas <?php echo e($config['icon']); ?> me-1"></i>
                                        <?php echo e($order->status_text); ?>

                                    </span>
                                </td>
                                <td>
                                    <div><?php echo e($order->created_at->format('d/m/Y')); ?></div>
                                    <small class="text-muted"><?php echo e($order->created_at->format('H:i')); ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Voir détails -->
                                        <a href="<?php echo e(route('orders.show', $order)); ?>" 
                                           class="btn btn-outline-primary"
                                           title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Expédier (si confirmée) -->
                                        <?php if($order->status === 'confirmed'): ?>
                                            <form method="POST" action="<?php echo e(route('orders.mark-shipped', $order)); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" 
                                                        onclick="return confirm('Marquer cette commande comme expédiée ?')"
                                                        class="btn btn-outline-info"
                                                        title="Expédier">
                                                    <i class="fas fa-shipping-fast"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <!-- Marquer livrée (si expédiée) -->
                                        <?php if($order->status === 'shipped'): ?>
                                            <form method="POST" action="<?php echo e(route('orders.mark-delivered', $order)); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" 
                                                        onclick="return confirm('Marquer cette commande comme livrée ?')"
                                                        class="btn btn-outline-success"
                                                        title="Marquer livrée">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($orders->hasPages()): ?>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <?php echo e($orders->links()); ?>

                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 80px; height: 80px;">
                        <i class="fas fa-store fa-3x text-muted"></i>
                    </div>
                </div>
                <h3 class="h4 mb-3">Aucune vente pour le moment</h3>
                <p class="text-muted mb-4">Vous n'avez pas encore reçu de commandes pour vos articles.</p>
                <a href="<?php echo e(route('items.create')); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>
                    Vendre un article
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/orders/my-sales.blade.php ENDPATH**/ ?>