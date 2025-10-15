



<?php $__env->startSection('title', 'Détails de l\'utilisateur'); ?><?php $__env->startSection('title', 'Détails utilisateur - ' . $user->name); ?>

<?php $__env->startSection('page-title', $user->name); ?><?php $__env->startSection('page-title', 'Détails de l\'utilisateur'); ?>



<?php $__env->startSection('page-actions'); ?><?php $__env->startSection('page-actions'); ?>

<div class="d-flex gap-2"><div class="d-flex gap-2">

    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-secondary">

        <i class="fas fa-arrow-left me-2"></i>Retour à la liste        <i class="fas fa-arrow-left me-2"></i>Retour

    </a>    </a>

    <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-primary">    <div class="dropdown">

        <i class="fas fa-edit me-2"></i>Modifier        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">

    </a>            <i class="fas fa-cog me-2"></i>Actions

    <div class="dropdown">        </button>

        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">        <ul class="dropdown-menu">

            <i class="fas fa-ellipsis-v"></i>            <?php if($user->is_active ?? true): ?>

        </button>                <li>

        <ul class="dropdown-menu">                    <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="d-inline">

            <li><a class="dropdown-item" href="#" onclick="toggleStatus()">                        <?php echo csrf_field(); ?>

                <i class="fas fa-<?php echo e($user->status === 'active' ? 'pause' : 'play'); ?> me-2"></i>                        <?php echo method_field('PATCH'); ?>

                <?php echo e($user->status === 'active' ? 'Suspendre' : 'Activer'); ?>                        <input type="hidden" name="action" value="deactivate">

            </a></li>                        <button type="submit" class="dropdown-item text-warning" 

            <li><a class="dropdown-item" href="#" onclick="sendPasswordReset()">                                onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?')">

                <i class="fas fa-key me-2"></i>Réinitialiser le mot de passe                            <i class="fas fa-pause me-2"></i>Désactiver

            </a></li>                        </button>

            <li><a class="dropdown-item" href="#" onclick="sendWelcomeEmail()">                    </form>

                <i class="fas fa-envelope me-2"></i>Envoyer email de bienvenue                </li>

            </a></li>            <?php else: ?>

            <li><hr class="dropdown-divider"></li>                <li>

            <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete()">                    <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="d-inline">

                <i class="fas fa-trash me-2"></i>Supprimer                        <?php echo csrf_field(); ?>

            </a></li>                        <?php echo method_field('PATCH'); ?>

        </ul>                        <input type="hidden" name="action" value="activate">

    </div>                        <button type="submit" class="dropdown-item text-success">

</div>                            <i class="fas fa-play me-2"></i>Activer

<?php $__env->stopSection(); ?>                        </button>

                    </form>

<?php $__env->startSection('content'); ?>                </li>

<div class="row">            <?php endif; ?>

    <!-- Informations principales -->            

    <div class="col-lg-8">            <?php if(!($user->is_suspended ?? false)): ?>

        <div class="card">                <li>

            <div class="card-body">                    <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="d-inline">

                <div class="row">                        <?php echo csrf_field(); ?>

                    <div class="col-md-8">                        <?php echo method_field('PATCH'); ?>

                        <div class="d-flex align-items-center mb-3">                        <input type="hidden" name="action" value="suspend">

                            <?php if($user->avatar): ?>                        <button type="submit" class="dropdown-item text-warning" 

                                <img src="<?php echo e($user->avatar_url); ?>" class="rounded-circle me-3" width="80" height="80" alt="Avatar <?php echo e($user->name); ?>">                                onclick="return confirm('Êtes-vous sûr de vouloir suspendre cet utilisateur ?')">

                            <?php else: ?>                            <i class="fas fa-ban me-2"></i>Suspendre

                                <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 80px;">                        </button>

                                    <i class="fas fa-user fa-2x text-muted"></i>                    </form>

                                </div>                </li>

                            <?php endif; ?>            <?php endif; ?>

                            <div>        </ul>

                                <h3 class="card-title mb-1"><?php echo e($user->name); ?></h3>    </div>

                                <p class="text-muted mb-1"><?php echo e($user->email); ?></p></div>

                                <div><?php $__env->stopSection(); ?>

                                    <span class="badge bg-<?php echo e($user->status === 'active' ? 'success' : ($user->status === 'suspended' ? 'warning' : 'danger')); ?>">

                                        <?php echo e(ucfirst($user->status ?? 'active')); ?><?php $__env->startSection('content'); ?>

                                    </span><div class="row">

                                    <?php if($user->role): ?>    <!-- Informations de base -->

                                        <span class="badge bg-primary"><?php echo e(ucfirst($user->role)); ?></span>    <div class="col-md-4">

                                    <?php endif; ?>        <div class="card">

                                    <?php if($user->email_verified_at): ?>            <div class="card-body text-center">

                                        <span class="badge bg-info">Email vérifié</span>                <?php if($user->avatar): ?>

                                    <?php endif; ?>                    <img src="<?php echo e($user->avatar_url); ?>" class="rounded-circle mb-3" width="120" height="120" alt="Avatar">

                                    <?php if($user->is_seller): ?>                <?php else: ?>

                                        <span class="badge bg-secondary">Vendeur</span>                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; font-size: 3rem;">

                                    <?php endif; ?>                        <?php echo e($user->initial); ?>


                                </div>                    </div>

                            </div>                <?php endif; ?>

                        </div>                

                                        <h4><?php echo e($user->name); ?></h4>

                        <?php if($user->bio): ?>                <p class="text-muted"><?php echo e($user->email); ?></p>

                            <div class="mb-4">                

                                <h6>Biographie</h6>                <div class="d-flex justify-content-center gap-2 mb-3">

                                <p class="text-muted"><?php echo e($user->bio); ?></p>                    <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            </div>                        <span class="badge bg-<?php echo e($role->slug === 'admin' ? 'danger' : 'primary'); ?>">

                        <?php endif; ?>                            <?php echo e($role->name); ?>


                                                </span>

                        <div class="row">                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php if($user->phone): ?>                </div>

                                <div class="col-md-6 mb-3">                

                                    <h6>Téléphone</h6>                <?php if($user->isOnline()): ?>

                                    <p class="mb-0">                    <span class="badge bg-success">En ligne</span>

                                        <i class="fas fa-phone me-2"></i>                <?php else: ?>

                                        <?php echo e($user->phone); ?>                    <span class="badge bg-secondary">Hors ligne</span>

                                    </p>                <?php endif; ?>

                                </div>                

                            <?php endif; ?>                <?php if($user->is_active ?? true): ?>

                                                <span class="badge bg-success">Actif</span>

                            <?php if($user->date_of_birth): ?>                <?php else: ?>

                                <div class="col-md-6 mb-3">                    <span class="badge bg-danger">Inactif</span>

                                    <h6>Date de naissance</h6>                <?php endif; ?>

                                    <p class="mb-0">                

                                        <i class="fas fa-birthday-cake me-2"></i>                <?php if($user->is_suspended ?? false): ?>

                                        <?php echo e($user->date_of_birth->format('d/m/Y')); ?>                     <span class="badge bg-warning">Suspendu</span>

                                        (<?php echo e($user->date_of_birth->age); ?> ans)                <?php endif; ?>

                                    </p>            </div>

                                </div>        </div>

                            <?php endif; ?>        

                                    <!-- Informations personnelles -->

                            <?php if($user->address || $user->city || $user->country): ?>        <div class="card mt-4">

                                <div class="col-md-12 mb-3">            <div class="card-header">

                                    <h6>Adresse</h6>                <h5 class="mb-0">Informations personnelles</h5>

                                    <p class="mb-0">            </div>

                                        <i class="fas fa-map-marker-alt me-2"></i>            <div class="card-body">

                                        <?php if($user->address): ?><?php echo e($user->address); ?><br><?php endif; ?>                <div class="row mb-2">

                                        <?php if($user->city || $user->postal_code): ?>                    <div class="col-sm-4"><strong>ID:</strong></div>

                                            <?php echo e($user->postal_code); ?> <?php echo e($user->city); ?><br>                    <div class="col-sm-8"><?php echo e($user->id); ?></div>

                                        <?php endif; ?>                </div>

                                        <?php if($user->country): ?><?php echo e($user->country); ?><?php endif; ?>                

                                    </p>                <div class="row mb-2">

                                </div>                    <div class="col-sm-4"><strong>Téléphone:</strong></div>

                            <?php endif; ?>                    <div class="col-sm-8"><?php echo e($user->phone ?? 'Non renseigné'); ?></div>

                                            </div>

                            <?php if($user->language || $user->timezone): ?>                

                                <div class="col-md-6 mb-3">                <div class="row mb-2">

                                    <h6>Préférences</h6>                    <div class="col-sm-4"><strong>Adresse:</strong></div>

                                    <?php if($user->language): ?>                    <div class="col-sm-8"><?php echo e($user->address ?? 'Non renseignée'); ?></div>

                                        <p class="mb-1">                </div>

                                            <i class="fas fa-globe me-2"></i>                

                                            <?php echo e(strtoupper($user->language)); ?>                <div class="row mb-2">

                                        </p>                    <div class="col-sm-4"><strong>Localisation:</strong></div>

                                    <?php endif; ?>                    <div class="col-sm-8"><?php echo e($user->location ?? 'Non renseignée'); ?></div>

                                    <?php if($user->timezone): ?>                </div>

                                        <p class="mb-0">                

                                            <i class="fas fa-clock me-2"></i>                <div class="row mb-2">

                                            <?php echo e($user->timezone); ?>                    <div class="col-sm-4"><strong>Email vérifié:</strong></div>

                                        </p>                    <div class="col-sm-8">

                                    <?php endif; ?>                        <?php if($user->email_verified_at): ?>

                                </div>                            <span class="text-success">Oui</span>

                            <?php endif; ?>                            <small class="text-muted d-block"><?php echo e($user->email_verified_at->format('d/m/Y H:i')); ?></small>

                        </div>                        <?php else: ?>

                    </div>                            <span class="text-danger">Non</span>

                                            <?php endif; ?>

                    <div class="col-md-4">                    </div>

                        <!-- Informations de connexion -->                </div>

                        <div class="card border">                

                            <div class="card-body">                <div class="row mb-2">

                                <h6 class="card-title">Dernière activité</h6>                    <div class="col-sm-4"><strong>Inscription:</strong></div>

                                <?php if($user->last_login_at): ?>                    <div class="col-sm-8"><?php echo e($user->created_at->format('d/m/Y H:i')); ?></div>

                                    <p class="mb-2">                </div>

                                        <strong>Dernière connexion:</strong><br>                

                                        <?php echo e($user->last_login_at->format('d/m/Y H:i')); ?>                <div class="row mb-2">

                                    </p>                    <div class="col-sm-4"><strong>Dernière connexion:</strong></div>

                                <?php endif; ?>                    <div class="col-sm-8">

                                <p class="mb-2">                        <?php if($user->last_seen): ?>

                                    <strong>Membre depuis:</strong><br>                            <?php echo e($user->last_seen->format('d/m/Y H:i')); ?>


                                    <?php echo e($user->created_at->format('d/m/Y')); ?>                            <small class="text-muted d-block"><?php echo e($user->last_seen->diffForHumans()); ?></small>

                                </p>                        <?php else: ?>

                                <?php if($user->last_seen_at): ?>                            <span class="text-muted">Jamais connecté</span>

                                    <p class="mb-0">                        <?php endif; ?>

                                        <strong>Vu pour la dernière fois:</strong><br>                    </div>

                                        <?php echo e($user->last_seen_at->diffForHumans()); ?>                </div>

                                    </p>            </div>

                                <?php endif; ?>        </div>

                            </div>    </div>

                        </div>    

                    </div>    <!-- Statistiques et activité -->

                </div>    <div class="col-md-8">

            </div>        <!-- Statistiques -->

        </div>        <div class="row">

                    <div class="col-md-3 mb-4">

        <!-- Articles de l'utilisateur -->                <div class="card text-center">

        <?php if($user->items && $user->items->count() > 0): ?>                    <div class="card-body">

            <div class="card mt-4">                        <i class="fas fa-box fa-2x text-primary mb-2"></i>

                <div class="card-header d-flex justify-content-between align-items-center">                        <h4><?php echo e($stats['total_items']); ?></h4>

                    <h5 class="card-title mb-0">Articles mis en vente</h5>                        <small class="text-muted">Articles</small>

                    <a href="<?php echo e(route('admin.items.index', ['user' => $user->id])); ?>" class="btn btn-sm btn-outline-primary">                    </div>

                        Voir tous les articles                </div>

                    </a>            </div>

                </div>            

                <div class="card-body">            <div class="col-md-3 mb-4">

                    <div class="table-responsive">                <div class="card text-center">

                        <table class="table table-hover">                    <div class="card-body">

                            <thead>                        <i class="fas fa-shopping-cart fa-2x text-success mb-2"></i>

                                <tr>                        <h4><?php echo e($stats['total_orders']); ?></h4>

                                    <th>Article</th>                        <small class="text-muted">Commandes</small>

                                    <th>Catégorie</th>                    </div>

                                    <th>Prix</th>                </div>

                                    <th>Statut</th>            </div>

                                    <th>Créé le</th>            

                                    <th>Actions</th>            <div class="col-md-3 mb-4">

                                </tr>                <div class="card text-center">

                            </thead>                    <div class="card-body">

                            <tbody>                        <i class="fas fa-dollar-sign fa-2x text-warning mb-2"></i>

                                <?php $__currentLoopData = $user->items->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>                        <h4>$<?php echo e(number_format($stats['total_revenue'], 2)); ?></h4>

                                    <tr>                        <small class="text-muted">Revenus</small>

                                        <td>                    </div>

                                            <div class="d-flex align-items-center">                </div>

                                                <?php if($item->images && $item->images->first()): ?>            </div>

                                                    <img src="<?php echo e($item->images->first()->url); ?>"             

                                                         class="rounded me-2"             <div class="col-md-3 mb-4">

                                                         width="40"                 <div class="card text-center">

                                                         height="40"                     <div class="card-body">

                                                         alt="<?php echo e($item->title); ?>">                        <i class="fas fa-star fa-2x text-info mb-2"></i>

                                                <?php endif; ?>                        <h4><?php echo e(number_format($stats['average_rating'], 1)); ?></h4>

                                                <div>                        <small class="text-muted">Note moyenne</small>

                                                    <div class="fw-bold"><?php echo e($item->title); ?></div>                    </div>

                                                    <small class="text-muted"><?php echo e(Str::limit($item->description, 30)); ?></small>                </div>

                                                </div>            </div>

                                            </div>        </div>

                                        </td>        

                                        <td><?php echo e($item->category->name ?? 'Sans catégorie'); ?></td>        <!-- Wallets -->

                                        <td><?php echo e(number_format($item->price, 2)); ?> €</td>        <div class="card mb-4">

                                        <td>            <div class="card-header">

                                            <span class="badge bg-<?php echo e($item->status === 'active' ? 'success' : 'secondary'); ?>">                <h5 class="mb-0">Portefeuilles</h5>

                                                <?php echo e(ucfirst($item->status)); ?>            </div>

                                            </span>            <div class="card-body">

                                        </td>                <div class="row">

                                        <td><?php echo e($item->created_at->format('d/m/Y')); ?></td>                    <?php if($user->usdWallet()): ?>

                                        <td>                        <div class="col-md-6">

                                            <a href="<?php echo e(route('admin.items.show', $item)); ?>" class="btn btn-sm btn-outline-info">                            <div class="border rounded p-3">

                                                <i class="fas fa-eye"></i>                                <div class="d-flex justify-content-between align-items-center">

                                            </a>                                    <div>

                                        </td>                                        <h6 class="mb-0">Wallet USD</h6>

                                    </tr>                                        <small class="text-muted">Devise principale</small>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                    </div>

                            </tbody>                                    <div class="text-end">

                        </table>                                        <h5 class="mb-0">$<?php echo e(number_format($user->usdWallet()->balance, 2)); ?></h5>

                    </div>                                        <small class="text-muted">USD</small>

                </div>                                    </div>

            </div>                                </div>

        <?php endif; ?>                            </div>

                                </div>

        <!-- Commandes récentes -->                    <?php endif; ?>

        <?php if($user->orders && $user->orders->count() > 0): ?>                    

            <div class="card mt-4">                    <?php if($user->cdfWallet()): ?>

                <div class="card-header d-flex justify-content-between align-items-center">                        <div class="col-md-6">

                    <h5 class="card-title mb-0">Commandes récentes</h5>                            <div class="border rounded p-3">

                    <a href="<?php echo e(route('admin.orders.index', ['user' => $user->id])); ?>" class="btn btn-sm btn-outline-primary">                                <div class="d-flex justify-content-between align-items-center">

                        Voir toutes les commandes                                    <div>

                    </a>                                        <h6 class="mb-0">Wallet CDF</h6>

                </div>                                        <small class="text-muted">Devise locale</small>

                <div class="card-body">                                    </div>

                    <div class="table-responsive">                                    <div class="text-end">

                        <table class="table table-hover">                                        <h5 class="mb-0"><?php echo e(number_format($user->cdfWallet()->balance, 0)); ?></h5>

                            <thead>                                        <small class="text-muted">CDF</small>

                                <tr>                                    </div>

                                    <th>Commande</th>                                </div>

                                    <th>Total</th>                            </div>

                                    <th>Statut</th>                        </div>

                                    <th>Date</th>                    <?php endif; ?>

                                    <th>Actions</th>                </div>

                                </tr>            </div>

                            </thead>        </div>

                            <tbody>        

                                <?php $__currentLoopData = $user->orders->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>        <!-- Dernières transactions -->

                                    <tr>        <div class="card">

                                        <td>#<?php echo e($order->id); ?></td>            <div class="card-header d-flex justify-content-between align-items-center">

                                        <td><?php echo e(number_format($order->total, 2)); ?> €</td>                <h5 class="mb-0">Dernières transactions</h5>

                                        <td>                <a href="<?php echo e(route('admin.transactions.index', ['search' => $user->email])); ?>" class="btn btn-sm btn-outline-primary">

                                            <span class="badge bg-<?php echo e($order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info')); ?>">                    Voir toutes

                                                <?php echo e(ucfirst($order->status)); ?>                </a>

                                            </span>            </div>

                                        </td>            <div class="card-body">

                                        <td><?php echo e($order->created_at->format('d/m/Y')); ?></td>                <?php if($recentTransactions->count() > 0): ?>

                                        <td>                    <div class="table-responsive">

                                            <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="btn btn-sm btn-outline-info">                        <table class="table table-sm">

                                                <i class="fas fa-eye"></i>                            <thead>

                                            </a>                                <tr>

                                        </td>                                    <th>Date</th>

                                    </tr>                                    <th>Type</th>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                    <th>Montant</th>

                            </tbody>                                    <th>Statut</th>

                        </table>                                    <th>Description</th>

                    </div>                                </tr>

                </div>                            </thead>

            </div>                            <tbody>

        <?php endif; ?>                                <?php $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    </div>                                    <tr>

                                            <td><?php echo e($transaction->created_at->format('d/m/Y H:i')); ?></td>

    <!-- Sidebar avec statistiques -->                                        <td>

    <div class="col-lg-4">                                            <span class="badge bg-secondary"><?php echo e($transaction->type); ?></span>

        <div class="card">                                        </td>

            <div class="card-header">                                        <td class="font-monospace">

                <h5 class="card-title mb-0">Statistiques</h5>                                            <?php echo e(number_format($transaction->amount, 2)); ?> <?php echo e($transaction->currency); ?>


            </div>                                        </td>

            <div class="card-body">                                        <td>

                <div class="row text-center">                                            <?php if($transaction->status === 'completed'): ?>

                    <div class="col-6 mb-3">                                                <span class="badge bg-success">Terminé</span>

                        <div class="border-end">                                            <?php elseif($transaction->status === 'pending'): ?>

                            <h3 class="text-primary"><?php echo e($user->items_count ?? 0); ?></h3>                                                <span class="badge bg-warning">En attente</span>

                            <small class="text-muted">Articles</small>                                            <?php else: ?>

                        </div>                                                <span class="badge bg-danger">Échoué</span>

                    </div>                                            <?php endif; ?>

                    <div class="col-6 mb-3">                                        </td>

                        <h3 class="text-success"><?php echo e($user->orders_count ?? 0); ?></h3>                                        <td><?php echo e($transaction->description); ?></td>

                        <small class="text-muted">Commandes</small>                                    </tr>

                    </div>                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <div class="col-6">                            </tbody>

                        <div class="border-end">                        </table>

                            <h3 class="text-info"><?php echo e($user->reviews_count ?? 0); ?></h3>                    </div>

                            <small class="text-muted">Avis donnés</small>                <?php else: ?>

                        </div>                    <div class="text-center text-muted">

                    </div>                        <i class="fas fa-receipt fa-3x mb-3"></i>

                    <div class="col-6">                        <p>Aucune transaction récente</p>

                        <h3 class="text-warning"><?php echo e(number_format($user->rating ?? 0, 1)); ?>/5</h3>                    </div>

                        <small class="text-muted">Note moyenne</small>                <?php endif; ?>

                    </div>            </div>

                </div>        </div>

            </div>    </div>

        </div></div>

        <?php $__env->stopSection(); ?>
        <!-- Portefeuille -->
        <?php if($user->wallet): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Portefeuille</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="text-success"><?php echo e(number_format($user->wallet->balance, 2)); ?> €</h2>
                        <p class="text-muted">Solde disponible</p>
                    </div>
                    <div class="d-grid">
                        <a href="<?php echo e(route('admin.wallet.show', $user->wallet)); ?>" class="btn btn-outline-primary">
                            Voir les transactions
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Informations système -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Informations système</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>ID:</strong>
                        <span><?php echo e($user->id); ?></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>Inscrit le:</strong>
                        <span><?php echo e($user->created_at->format('d/m/Y H:i')); ?></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>Modifié le:</strong>
                        <span><?php echo e($user->updated_at->format('d/m/Y H:i')); ?></span>
                    </div>
                </div>
                
                <?php if($user->email_verified_at): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <strong>Email vérifié le:</strong>
                            <span><?php echo e($user->email_verified_at->format('d/m/Y H:i')); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if($user->last_login_at): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <strong>Dernière connexion:</strong>
                            <span><?php echo e($user->last_login_at->format('d/m/Y H:i')); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Notifications et préférences -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Préférences</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <i class="fas fa-<?php echo e($user->notifications_enabled ?? 1 ? 'bell' : 'bell-slash'); ?> me-2"></i>
                    Notifications <?php echo e($user->notifications_enabled ?? 1 ? 'activées' : 'désactivées'); ?>

                </div>
                <div class="mb-2">
                    <i class="fas fa-<?php echo e($user->marketing_emails ? 'envelope' : 'envelope-open-text'); ?> me-2"></i>
                    Emails marketing <?php echo e($user->marketing_emails ? 'acceptés' : 'refusés'); ?>

                </div>
                <?php if($user->language): ?>
                    <div class="mb-2">
                        <i class="fas fa-language me-2"></i>
                        Langue: <?php echo e(strtoupper($user->language)); ?>

                    </div>
                <?php endif; ?>
                <?php if($user->timezone): ?>
                    <div class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Fuseau: <?php echo e($user->timezone); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Modifier l'utilisateur
                    </a>
                    
                    <button class="btn btn-outline-secondary" onclick="sendPasswordReset()">
                        <i class="fas fa-key me-2"></i>Réinitialiser le mot de passe
                    </button>
                    
                    <button class="btn btn-outline-info" onclick="sendMessage()">
                        <i class="fas fa-envelope me-2"></i>Envoyer un message
                    </button>
                    
                    <button class="btn btn-outline-warning" onclick="exportUserData()">
                        <i class="fas fa-download me-2"></i>Exporter les données
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong><?php echo e($user->name); ?></strong> ?</p>
                <?php if($user->items_count > 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cet utilisateur possède <?php echo e($user->items_count); ?> article(s).
                    </div>
                <?php endif; ?>
                <?php if($user->orders_count > 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cet utilisateur a <?php echo e($user->orders_count); ?> commande(s) associée(s).
                    </div>
                <?php endif; ?>
                <p class="text-danger small">Cette action est irréversible et supprimera toutes les données associées.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function toggleStatus() {
    fetch(`/admin/users/<?php echo e($user->id); ?>/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification du statut');
        }
    });
}

function sendPasswordReset() {
    if (confirm('Envoyer un email de réinitialisation du mot de passe à <?php echo e($user->name); ?> ?')) {
        fetch(`/admin/users/<?php echo e($user->id); ?>/send-password-reset`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Email de réinitialisation envoyé avec succès');
            } else {
                alert('Erreur lors de l\'envoi de l\'email');
            }
        });
    }
}

function sendWelcomeEmail() {
    if (confirm('Envoyer un email de bienvenue à <?php echo e($user->name); ?> ?')) {
        fetch(`/admin/users/<?php echo e($user->id); ?>/send-welcome`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Email de bienvenue envoyé avec succès');
            } else {
                alert('Erreur lors de l\'envoi de l\'email');
            }
        });
    }
}

function sendMessage() {
    const message = prompt('Message à envoyer à <?php echo e($user->name); ?> :');
    if (message) {
        fetch(`/admin/users/<?php echo e($user->id); ?>/send-message`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Message envoyé avec succès');
            } else {
                alert('Erreur lors de l\'envoi du message');
            }
        });
    }
}

function exportUserData() {
    window.location.href = `/admin/users/<?php echo e($user->id); ?>/export`;
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/users/show.blade.php ENDPATH**/ ?>