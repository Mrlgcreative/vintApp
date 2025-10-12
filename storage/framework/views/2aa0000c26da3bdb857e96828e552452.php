

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 border-danger error-card">
                <div class="card-body text-center p-5">
                    <!-- Icône d'erreur avec animation -->
                    <div class="error-animation mb-4">
                        <div class="error-circle">
                            <div class="error-icon">
                                <i class="fas fa-times-circle fa-5x text-danger"></i>
                            </div>
                        </div>
                    </div>
                    
                    <h2 class="mb-3 text-danger fw-bold">Paiement Échoué</h2>
                    <p class="text-muted mb-4">Votre transaction n'a pas pu être traitée</p>
                    
                    <!-- Message d'erreur -->
                    <?php if(isset($error) && $error): ?>
                        <div class="alert alert-danger mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong><?php echo e($error); ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Détails de la tentative -->
                    <div class="error-details bg-light rounded-3 p-4 mb-4 text-start">
                        <h5 class="border-bottom pb-2 mb-3 text-danger">
                            <i class="fas fa-info-circle me-2"></i>Détails de la tentative
                        </h5>
                        
                        <?php if(isset($amount) && $amount > 0): ?>
                            <div class="detail-row">
                                <span class="detail-label">Montant tenté</span>
                                <span class="detail-value">
                                    <?php echo e(number_format($amount, 2)); ?> USD
                                    <small class="text-muted">(<?php echo e(number_format($amount * 2450, 0)); ?> CDF)</small>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($provider) && $provider): ?>
                            <div class="detail-row">
                                <span class="detail-label">Opérateur</span>
                                <span class="detail-value">
                                    <i class="fas fa-mobile-alt me-1"></i><?php echo e($provider); ?>

                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="detail-row">
                            <span class="detail-label">Date de la tentative</span>
                            <span class="detail-value">
                                <i class="far fa-calendar me-1"></i><?php echo e(now()->format('d/m/Y à H:i')); ?>

                            </span>
                        </div>
                        
                        <div class="detail-row border-0">
                            <span class="detail-label">Statut</span>
                            <span class="detail-value">
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i>Échec
                                </span>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Causes possibles -->
                    <div class="mt-4 p-3 bg-warning bg-opacity-10 rounded-3 text-start">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>Causes possibles :
                        </h6>
                        <ul class="small text-muted mb-0">
                            <li>Solde insuffisant sur votre compte Mobile Money</li>
                            <li>Numéro de téléphone invalide ou inactif</li>
                            <li>Délai d'attente de l'opérateur dépassé</li>
                            <li>Transaction refusée par l'opérateur</li>
                            <li>Problème de connexion réseau</li>
                        </ul>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="d-grid gap-3 mt-4">
                        <a href="<?php echo e(route('cart.pay')); ?>" class="btn btn-danger btn-lg">
                            <i class="fas fa-redo me-2"></i>Réessayer le Paiement
                        </a>
                        <a href="<?php echo e(route('support.index')); ?>" class="btn btn-outline-danger">
                            <i class="fas fa-headset me-2"></i>Contacter le Support
                        </a>
                        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Retour au Dashboard
                        </a>
                    </div>
                    
                    <!-- Message d'aide -->
                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-question-circle me-1"></i>
                            Besoin d'aide ? Notre équipe support est disponible 24/7
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Animation de la carte */
.error-card {
    animation: slideUp 0.6s ease-out;
    border-top: 4px solid #dc3545;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animation de l'icône d'erreur */
.error-animation {
    animation: shake 0.5s ease-in-out;
}

.error-circle {
    animation: pulse-error 1.5s ease-in-out infinite;
}

.error-icon {
    animation: bounce-error 0.8s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

@keyframes pulse-error {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.05);
        opacity: 0.9;
    }
}

@keyframes bounce-error {
    0% {
        transform: scale(0) rotate(-180deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.2) rotate(0deg);
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

/* Détails de transaction */
.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: #6c757d;
    font-weight: 500;
}

.detail-value {
    color: #212529;
    font-weight: 600;
    text-align: right;
}

/* Animation du fond */
.error-card {
    position: relative;
    overflow: hidden;
}

.error-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(220, 53, 69, 0.05), transparent);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/payments/error.blade.php ENDPATH**/ ?>