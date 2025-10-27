

<?php $__env->startSection('title', 'Mon Profil - ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- En-tête du profil -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <?php if($user->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" 
                                     alt="<?php echo e($user->name); ?>" 
                                     class="rounded-circle" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-gradient-to-r from-purple-600 to-cyan-400 d-flex align-items-center justify-content-center text-white" 
                                     style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
                                    <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <h1 class="h3 mb-2 fw-bold text-dark"><?php echo e($user->name); ?></h1>
                            <p class="text-muted mb-1">
                                <i class="fas fa-envelope me-1"></i>
                                <?php echo e($user->email); ?>

                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Membre depuis <?php echo e($user->created_at->format('F Y')); ?>

                            </p>
                        </div>
                        <div class="col-auto">
                            <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-1"></i>
                                Modifier le profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-box text-primary fa-2x mb-2"></i>
                    <h4 class="fw-bold"><?php echo e($stats['total_items']); ?></h4>
                    <p class="text-muted mb-0">Articles publiés</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-shopping-cart text-success fa-2x mb-2"></i>
                    <h4 class="fw-bold"><?php echo e($stats['total_orders']); ?></h4>
                    <p class="text-muted mb-0">Commandes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-euro-sign text-warning fa-2x mb-2"></i>
                    <h4 class="fw-bold"><?php echo e(number_format($stats['total_revenue'], 2)); ?>€</h4>
                    <p class="text-muted mb-0">Revenus totaux</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-star text-info fa-2x mb-2"></i>
                    <h4 class="fw-bold"><?php echo e(number_format($stats['average_rating'], 1)); ?>/5</h4>
                    <p class="text-muted mb-0">Note moyenne</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation des sections -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                        Tableau de bord
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Articles -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-box text-primary fa-3x mb-3"></i>
                                    <h6 class="fw-bold">Mes Articles</h6>
                                    <p class="text-muted small">
                                        <?php echo e($stats['active_items']); ?> actifs · <?php echo e($stats['sold_items']); ?> vendus
                                    </p>
                                    <a href="<?php echo e(route('items.my-items')); ?>" class="btn btn-primary btn-sm">
                                        Gérer mes articles
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Commandes -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-cart text-success fa-3x mb-3"></i>
                                    <h6 class="fw-bold">Mes Commandes</h6>
                                    <p class="text-muted small">
                                        <?php echo e($stats['total_orders']); ?> commandes passées
                                    </p>
                                    <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-success btn-sm">
                                        Voir mes commandes
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-comments text-info fa-3x mb-3"></i>
                                    <h6 class="fw-bold">Messages</h6>
                                    <p class="text-muted small">
                                        <?php echo e($stats['unread_messages']); ?> non lus
                                    </p>
                                    <a href="<?php echo e(route('messages.index')); ?>" class="btn btn-info btn-sm">
                                        Voir mes messages
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Wallet -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-wallet text-warning fa-3x mb-3"></i>
                                    <h6 class="fw-bold">Wallet</h6>
                                    <p class="text-muted small">
                                        Gérez vos paiements
                                    </p>
                                    <a href="<?php echo e(route('wallet.index')); ?>" class="btn btn-warning btn-sm">
                                        Accéder au wallet
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Paramètres -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-cog text-secondary fa-3x mb-3"></i>
                                    <h6 class="fw-bold">Paramètres</h6>
                                    <p class="text-muted small">
                                        Sécurité et préférences
                                    </p>
                                    <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-secondary btn-sm">
                                        Configurer
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border border-dark">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar text-dark fa-3x mb-3"></i>
                                    <h6 class="fw-bold">Statistiques</h6>
                                    <p class="text-muted small">
                                        Analyse détaillée
                                    </p>
                                    <a href="<?php echo e(route('profile.stats')); ?>" class="btn btn-dark btn-sm">
                                        Voir les stats
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activité récente -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        Activité récente
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($stats['unread_messages'] > 0): ?>
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fas fa-envelope me-2"></i>
                            <div>
                                Vous avez <strong><?php echo e($stats['unread_messages']); ?></strong> message(s) non lu(s).
                                <a href="<?php echo e(route('messages.index')); ?>" class="alert-link">Les consulter maintenant</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($stats['active_items'] > 0): ?>
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>
                                Vous avez <strong><?php echo e($stats['active_items']); ?></strong> article(s) en ligne.
                                <a href="<?php echo e(route('items.my-items')); ?>" class="alert-link">Les gérer</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($stats['total_items'] === 0): ?>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-plus-circle me-2"></i>
                            <div>
                                Vous n'avez encore publié aucun article.
                                <a href="<?php echo e(route('items.create')); ?>" class="alert-link">Publier votre premier article</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
}

.bg-gradient-to-r {
    background: linear-gradient(to right, #9333ea, #22d3ee);
}

.from-purple-600 {
    --tw-gradient-from: #9333ea;
    --tw-gradient-to: rgba(147, 51, 234, 0);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}

.to-cyan-400 {
    --tw-gradient-to: #22d3ee;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/profile/index.blade.php ENDPATH**/ ?>