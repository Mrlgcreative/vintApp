

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Commande <?php echo e($order->order_number); ?>

                        </h3>
                        <span class="badge <?php echo e($order->status_badge_class); ?> fs-6">
                            <?php echo e($order->status_text); ?>

                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations de l'article -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <?php if($order->item->images && count($order->item->images) > 0): ?>
                                <img src="<?php echo e(asset('storage/' . $order->item->images[0])); ?>" 
                                     class="img-fluid rounded" 
                                     alt="<?php echo e($order->item->name); ?>">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                     style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h4><?php echo e($order->item->name); ?></h4>
                            <p class="text-muted"><?php echo e($order->item->description); ?></p>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Prix unitaire</small>
                                    <div class="h5 text-primary fw-bold"><?php echo e($order->formatted_unit_price); ?></div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Quantité</small>
                                    <div class="h5 fw-bold"><?php echo e($order->quantity); ?></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="badge bg-primary me-2"><?php echo e($order->item->category->name); ?></span>
                                <?php if($order->item->brand): ?>
                                    <span class="badge bg-secondary"><?php echo e($order->item->brand->name); ?></span>
                                <?php endif; ?>
                                <span class="badge condition-badge condition-<?php echo e($order->item->condition); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $order->item->condition))); ?>

                                </span>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Vendeur</small>
                                    <div class="fw-bold"><?php echo e($order->item->user->name); ?></div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Acheteur</small>
                                    <div class="fw-bold"><?php echo e($order->buyer->name); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Informations de livraison -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Adresse de livraison
                            </h5>
                            <div class="card border-primary">
                                <div class="card-body">
                                    <?php if($order->deliveryAddress): ?>
                                        
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user text-purple me-2"></i>
                                                <strong>Destinataire:</strong>
                                            </div>
                                            <p class="ms-4 mb-0"><?php echo e($order->deliveryAddress->full_name); ?></p>
                                        </div>

                                        <?php if($order->deliveryAddress->email): ?>
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-envelope text-info me-2"></i>
                                                    <strong>Email:</strong>
                                                </div>
                                                <p class="ms-4 mb-0">
                                                    <a href="mailto:<?php echo e($order->deliveryAddress->email); ?>" class="text-decoration-none">
                                                        <?php echo e($order->deliveryAddress->email); ?>

                                                    </a>
                                                </p>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-phone text-success me-2"></i>
                                                <strong>Téléphone:</strong>
                                            </div>
                                            <p class="ms-4 mb-0">
                                                <a href="tel:<?php echo e($order->deliveryAddress->phone); ?>" class="text-decoration-none">
                                                    <?php echo e($order->deliveryAddress->phone); ?>

                                                </a>
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-city text-primary me-2"></i>
                                                <strong>Ville / Commune:</strong>
                                            </div>
                                            <p class="ms-4 mb-0"><?php echo e($order->deliveryAddress->city); ?>, <?php echo e($order->deliveryAddress->commune); ?></p>
                                        </div>

                                        <div class="mb-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-home text-info me-2"></i>
                                                <strong>Adresse complète:</strong>
                                            </div>
                                            <p class="ms-4 mb-0 text-muted"><?php echo e($order->deliveryAddress->address); ?></p>
                                        </div>

                                        <?php if($order->deliveryAddress->notes): ?>
                                            <div class="mt-3 p-2 bg-light rounded">
                                                <small class="text-muted">
                                                    <i class="fas fa-sticky-note me-1"></i>
                                                    <strong>Note:</strong> <?php echo e($order->deliveryAddress->notes); ?>

                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        
                                        <?php if($order->shipping_city && $order->shipping_city !== 'À définir'): ?>
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-city text-primary me-2"></i>
                                                    <strong>Ville:</strong>
                                                </div>
                                                <p class="ms-4 mb-0"><?php echo e($order->shipping_city); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if($order->shipping_phone): ?>
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-phone text-success me-2"></i>
                                                    <strong>Téléphone:</strong>
                                                </div>
                                                <p class="ms-4 mb-0">
                                                    <a href="tel:<?php echo e($order->shipping_phone); ?>" class="text-decoration-none">
                                                        <?php echo e($order->shipping_phone); ?>

                                                    </a>
                                                </p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if($order->shipping_address && $order->shipping_address !== 'À définir'): ?>
                                            <div class="mb-0">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-home text-info me-2"></i>
                                                    <strong>Adresse complète:</strong>
                                                </div>
                                                <p class="ms-4 mb-0 text-muted"><?php echo e($order->shipping_address); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if((!$order->shipping_city || $order->shipping_city === 'À définir') && 
                                            (!$order->shipping_address || $order->shipping_address === 'À définir') &&
                                            !$order->deliveryAddress): ?>
                                            <div class="alert alert-warning mb-0" role="alert">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Adresse non définie</strong>
                                                <br>
                                                <small>L'adresse de livraison n'a pas encore été définie pour cette commande.</small>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>
                                <i class="fas fa-money-bill me-2"></i>
                                Détails du paiement
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-6">Prix unitaire:</div>
                                        <div class="col-6 text-end"><?php echo e($order->formatted_unit_price); ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">Quantité:</div>
                                        <div class="col-6 text-end"><?php echo e($order->quantity); ?></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6"><strong>Total:</strong></div>
                                        <div class="col-6 text-end"><strong class="text-primary"><?php echo e($order->formatted_total_price); ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($order->notes): ?>
                        <div class="mb-4">
                            <h5>
                                <i class="fas fa-sticky-note me-2"></i>
                                Notes
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0"><?php echo e($order->notes); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Historique des statuts -->
                    <div class="mb-4">
                        <h5>
                            <i class="fas fa-history me-2"></i>
                            Historique
                        </h5>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>Commande créée</h6>
                                    <p class="text-muted"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></p>
                                </div>
                            </div>
                            
                            <?php if($order->paid_at): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6>Paiement confirmé</h6>
                                        <p class="text-muted"><?php echo e($order->paid_at->format('d/m/Y H:i')); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($order->shipped_at): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6>Expédiée</h6>
                                        <p class="text-muted"><?php echo e($order->shipped_at->format('d/m/Y H:i')); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($order->delivered_at): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6>Livrée</h6>
                                        <p class="text-muted"><?php echo e($order->delivered_at->format('d/m/Y H:i')); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour aux commandes
                        </a>
                        
                        <div>
                            
                            <?php if($order->buyer_id === Auth::id() && $order->status === 'pending'): ?>
                                <form method="POST" action="<?php echo e(route('orders.confirm-payment', $order)); ?>" style="display: inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" 
                                            class="btn btn-success me-2" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir confirmer le paiement de cette commande ?')"
                                            aria-label="Confirmer le paiement de la commande <?php echo e($order->order_number); ?>">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Confirmer le paiement
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            
                            <?php if($order->item->user_id === Auth::id() && $order->status === 'confirmed'): ?>
                                <form method="POST" action="<?php echo e(route('orders.mark-shipped', $order)); ?>" style="display: inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" 
                                            class="btn btn-primary me-2" 
                                            onclick="return confirm('Marquer cette commande comme expédiée ?')"
                                            aria-label="Marquer la commande <?php echo e($order->order_number); ?> comme expédiée">
                                        <i class="fas fa-shipping-fast me-2"></i>
                                        📦 Expédier la commande
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            
                            <?php if($order->item->user_id === Auth::id() && $order->status === 'shipped'): ?>
                                <form method="POST" action="<?php echo e(route('orders.mark-delivered', $order)); ?>" style="display: inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" 
                                            class="btn btn-success me-2" 
                                            onclick="return confirm('Marquer cette commande comme livrée ?')"
                                            aria-label="Marquer la commande <?php echo e($order->order_number); ?> comme livrée">
                                        <i class="fas fa-check-circle me-2"></i>
                                        ✅ Marquer comme livrée
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            
                            <?php if($order->buyer_id === Auth::id() && $order->status === 'pending'): ?>
                                <form method="POST" action="<?php echo e(route('orders.destroy', $order)); ?>" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" 
                                            class="btn btn-danger"
                                            aria-label="Annuler la commande <?php echo e($order->order_number); ?>">
                                        <i class="fas fa-times me-2"></i>
                                        Annuler
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Actions rapides -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('items.show', $order->item)); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>
                            Voir l'article
                        </a>
                        
                        <?php if($order->item->user_id === Auth::id()): ?>
                            <a href="<?php echo e(route('items.edit', $order->item)); ?>" class="btn btn-outline-warning">
                                <i class="fas fa-edit me-2"></i>
                                Modifier l'article
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo e(route('items.show', $order->item)); ?>#contact" class="btn btn-outline-info">
                            <i class="fas fa-envelope me-2"></i>
                            Contacter <?php echo e($order->buyer_id === Auth::id() ? 'le vendeur' : 'l\'acheteur'); ?>

                        </a>

                        
                        <?php if($order->status === 'pending'): ?>
                            <div class="alert alert-warning mb-0" role="alert">
                                <i class="fas fa-clock me-2"></i>
                                <strong>En attente de paiement</strong>
                                <?php if($order->buyer_id === Auth::id()): ?>
                                    <br><small>Veuillez confirmer le paiement pour continuer</small>
                                <?php else: ?>
                                    <br><small>L'acheteur n'a pas encore payé</small>
                                <?php endif; ?>
                            </div>
                        <?php elseif($order->status === 'confirmed'): ?>
                            <div class="alert alert-info mb-0" role="alert">
                                <i class="fas fa-box me-2"></i>
                                <strong>Paiement confirmé</strong>
                                <?php if($order->item->user_id === Auth::id()): ?>
                                    <br><small>Vous pouvez maintenant expédier la commande</small>
                                <?php else: ?>
                                    <br><small>En attente d'expédition par le vendeur</small>
                                <?php endif; ?>
                            </div>
                        <?php elseif($order->status === 'shipped' && !$order->confirmed_by_buyer_at): ?>
                            <?php if($order->buyer_id === Auth::id()): ?>
                                <button class="btn btn-success btn-lg" 
                                        onclick="confirmDelivery()">
                                    <i class="fas fa-check-circle me-2"></i>
                                    ✅ Commande Reçue
                                </button>
                                <div class="alert alert-primary mb-0 mt-2" role="alert">
                                    <i class="fas fa-truck me-2"></i>
                                    <small>Cliquez sur "Commande Reçue" une fois la livraison effectuée</small>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-primary mb-0" role="alert">
                                    <i class="fas fa-shipping-fast me-2"></i>
                                    <strong>Commande expédiée</strong>
                                    <br><small>En attente de confirmation de réception par l'acheteur</small>
                                </div>
                            <?php endif; ?>
                        <?php elseif($order->status === 'delivered' && !$order->confirmed_by_buyer_at): ?>
                            <?php if($order->buyer_id === Auth::id()): ?>
                                <button class="btn btn-success btn-lg" 
                                        onclick="confirmDelivery()">
                                    <i class="fas fa-check-circle me-2"></i>
                                    ✅ Commande Reçue
                                </button>
                                <div class="alert alert-primary mb-0 mt-2" role="alert">
                                    <i class="fas fa-home me-2"></i>
                                    <small>Confirmez la réception pour finaliser la transaction</small>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success mb-0" role="alert">
                                    <i class="fas fa-check me-2"></i>
                                    <strong>Commande livrée</strong>
                                    <br><small>En attente de confirmation par l'acheteur</small>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        
                        <?php if($order->confirmed_by_buyer_at): ?>
                            <div class="alert alert-success mb-0" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>✅ Réception confirmée</strong>
                                <br>
                                <small>Le <?php echo e($order->confirmed_by_buyer_at->format('d/m/Y à H:i')); ?></small>
                                <?php if($order->buyer_confirmation_note): ?>
                                    <br><small class="text-muted fst-italic">"<?php echo e($order->buyer_confirmation_note); ?>"</small>
                                <?php endif; ?>
                                <hr class="my-2">
                                <small class="text-muted">
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    La distribution des fonds a été effectuée
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6"><small class="text-muted">Numéro:</small></div>
                        <div class="col-6 text-end"><small><?php echo e($order->order_number); ?></small></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><small class="text-muted">Créée le:</small></div>
                        <div class="col-6 text-end"><small><?php echo e($order->created_at->format('d/m/Y')); ?></small></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><small class="text-muted">Devise:</small></div>
                        <div class="col-6 text-end"><small><?php echo e($order->currency); ?></small></div>
                    </div>
                    <?php if($order->updated_at !== $order->created_at): ?>
                        <div class="row">
                            <div class="col-6"><small class="text-muted">Modifiée le:</small></div>
                            <div class="col-6 text-end"><small><?php echo e($order->updated_at->format('d/m/Y')); ?></small></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 20px;
    border-left: 2px solid #e9ecef;
    padding-bottom: 10px;
}

.timeline-content h6 {
    margin-bottom: 5px;
}
</style>

<script>
// Script pour confirmer la réception de la commande
function confirmDelivery() {
    const note = prompt('Confirmez-vous avoir reçu votre commande ?\n\nVous pouvez ajouter un commentaire (optionnel) :');
    
    if (note !== null) { // L'utilisateur n'a pas cliqué sur Annuler
        fetch('<?php echo e(route('orders.confirm-delivery', $order)); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                note: note || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.error || 'Erreur lors de la confirmation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la confirmation');
        });
    }
}

console.log('Page de commande chargée');
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/orders/show.blade.php ENDPATH**/ ?>