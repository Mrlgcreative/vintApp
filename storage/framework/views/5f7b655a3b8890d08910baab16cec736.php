

<?php $__env->startSection('title', 'Mes conversations avec les vendeurs'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">
                <i class="fas fa-store me-2"></i>
                Mes conversations avec les vendeurs
            </h2>
            <p class="text-muted mb-0">Gérez vos discussions et demandes de réduction</p>
        </div>
        <div>
            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>
                Retour au tableau de bord
            </a>
        </div>
    </div>

    <?php if($vendorContacts->count() > 0): ?>
        <div class="row">
            <?php $__currentLoopData = $vendorContacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm conversation-card">
                        <!-- En-tête avec le vendeur -->
                        <div class="card-header bg-light border-0 d-flex align-items-center">
                            <div class="me-3">
                                <?php if($contact->vendor->avatar): ?>
                                    <img src="<?php echo e(Storage::url($contact->vendor->avatar)); ?>" 
                                         alt="<?php echo e($contact->vendor->name); ?>" 
                                         class="rounded-circle" 
                                         style="width: 45px; height: 45px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                         style="width: 45px; height: 45px; font-size: 1.2rem;">
                                        <?php echo e(strtoupper(substr($contact->vendor->name, 0, 1))); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold"><?php echo e($contact->vendor->name); ?></h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Contacté <?php echo e($contact->contact_date->diffForHumans()); ?>

                                </small>
                            </div>
                            <?php if($contact->unread_count > 0): ?>
                                <span class="badge bg-danger rounded-pill">
                                    <?php echo e($contact->unread_count); ?>

                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Produit concerné -->
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <?php if($contact->item && $contact->item->images && count($contact->item->images) > 0): ?>
                                    <img src="<?php echo e(Storage::url($contact->item->images[0])); ?>" 
                                         alt="<?php echo e($contact->item->name); ?>" 
                                         class="me-3 rounded" 
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                         style="width: 80px; height: 80px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow-1 min-width-0">
                                    <?php if($contact->item): ?>
                                        <h6 class="fw-bold mb-1 text-truncate"><?php echo e($contact->item->name); ?></h6>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="text-primary fw-bold"><?php echo e($contact->item->formatted_price); ?></span>
                                            <?php if($contact->has_discount): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-tag me-1"></i>
                                                    Réduction accordée
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted"><?php echo e($contact->item->category->name); ?></small>
                                    <?php else: ?>
                                        <h6 class="fw-bold mb-1 text-muted">Article non disponible</h6>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Dernier message -->
                            <?php if($contact->last_message): ?>
                                <div class="border-top pt-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-2">
                                            <?php if($contact->last_message->sender_id === Auth::id()): ?>
                                                <i class="fas fa-reply text-primary"></i>
                                            <?php else: ?>
                                                <i class="fas fa-comment text-success"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <p class="mb-1 small text-truncate" style="max-height: 40px; overflow: hidden;">
                                                <?php if($contact->last_message->sender_id === Auth::id()): ?>
                                                    <strong>Vous :</strong>
                                                <?php else: ?>
                                                    <strong><?php echo e($contact->vendor->name); ?> :</strong>
                                                <?php endif; ?>
                                                <?php echo e($contact->last_message->content ?: 'Fichier joint'); ?>

                                            </p>
                                            <small class="text-muted">
                                                <?php echo e($contact->last_message_time); ?>

                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Actions -->
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('messages.show', ['user' => $contact->vendor_id, 'item_id' => $contact->item_id])); ?>" 
                                   class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-comments me-1"></i>
                                    Ouvrir la conversation
                                </a>
                                <?php if($contact->item): ?>
                                    <a href="<?php echo e(route('items.show', $contact->item)); ?>" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Statistiques -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-store fa-2x text-primary mb-2"></i>
                        <h5 class="fw-bold"><?php echo e($vendorContacts->count()); ?></h5>
                        <small class="text-muted">Vendeurs contactés</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-tag fa-2x text-success mb-2"></i>
                        <h5 class="fw-bold"><?php echo e($vendorContacts->where('has_discount', true)->count()); ?></h5>
                        <small class="text-muted">Réductions obtenues</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope fa-2x text-warning mb-2"></i>
                        <h5 class="fw-bold"><?php echo e($vendorContacts->sum('unread_count')); ?></h5>
                        <small class="text-muted">Messages non lus</small>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-store fa-4x text-muted"></i>
            </div>
            <h4 class="text-muted mb-3">Aucun vendeur contacté</h4>
            <p class="text-muted mb-4">
                Vous n'avez pas encore contacté de vendeurs pour demander des réductions.<br>
                Parcourez les produits et utilisez le bouton "Contacter le vendeur" pour commencer.
            </p>
            <a href="<?php echo e(route('items.index')); ?>" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-2"></i>
                Parcourir les produits
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
<style>
.conversation-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.conversation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.badge.bg-danger {
    font-size: 0.7rem;
    padding: 0.4em 0.6em;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { 
        transform: scale(1); 
    }
    50% { 
        transform: scale(1.05); 
    }
}

.min-width-0 {
    min-width: 0;
}

.btn {
    border-radius: 10px;
}

.card {
    border-radius: 15px;
}

/* Hover effect pour les cartes de statistiques */
.bg-light:hover {
    background-color: #f8f9fa !important;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

/* Animation d'apparition des cartes */
.conversation-card {
    animation: slideInUp 0.4s ease-out forwards;
    opacity: 0;
}

.conversation-card:nth-child(1) { animation-delay: 0.1s; }
.conversation-card:nth-child(2) { animation-delay: 0.2s; }
.conversation-card:nth-child(3) { animation-delay: 0.3s; }
.conversation-card:nth-child(4) { animation-delay: 0.4s; }
.conversation-card:nth-child(5) { animation-delay: 0.5s; }
.conversation-card:nth-child(6) { animation-delay: 0.6s; }

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .conversation-card .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .conversation-card .me-3 {
        margin-right: 0 !important;
        margin-bottom: 1rem;
        align-self: center;
    }
    
    .card-body .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .card-body .me-3 {
        margin-right: 0 !important;
        margin-bottom: 1rem;
        align-self: center;
    }
}
</style>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/messages/index.blade.php ENDPATH**/ ?>