

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- En-tête -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold">Mes demandes de support</h1>
                <p class="text-muted mt-1">Gérez vos conversations avec notre équipe d'assistance</p>
            </div>
            <a href="<?php echo e(route('support.create')); ?>" 
               class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle demande
            </a>
        </div>

        <!-- Statistiques rapides -->
        <?php if($chats->count() > 0): ?>
            <div class="row g-3 mb-4">
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card shadow-sm border-start border-4 border-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="small text-muted mb-1">Total</p>
                                    <p class="h4 fw-bold mb-0"><?php echo e($chats->count()); ?></p>
                                </div>
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                    <i class="fas fa-comments text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card shadow-sm border-start border-4 border-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="small text-muted mb-1">En cours</p>
                                    <p class="h4 fw-bold text-warning mb-0">
                                        <?php echo e($chats->whereIn('status', ['open', 'in_progress'])->count()); ?>

                                    </p>
                                </div>
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                    <i class="fas fa-clock text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card shadow-sm border-start border-4 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="small text-muted mb-1">En attente</p>
                                    <p class="h4 fw-bold text-info mb-0">
                                        <?php echo e($chats->where('status', 'waiting_user')->count()); ?>

                                    </p>
                                </div>
                                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                    <i class="fas fa-hourglass-half text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card shadow-sm border-start border-4 border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="small text-muted mb-1">Résolues</p>
                                    <p class="h4 fw-bold text-success mb-0">
                                        <?php echo e($chats->where('status', 'closed')->count()); ?>

                                    </p>
                                </div>
                                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Liste des conversations -->
    <div class="card shadow-sm">
        <?php if($chats->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3">Référence</th>
                            <th class="px-3 py-3">Sujet</th>
                            <th class="px-3 py-3">Statut</th>
                            <th class="px-3 py-3">Priorité</th>
                            <th class="px-3 py-3">Agent</th>
                            <th class="px-3 py-3">Dernière activité</th>
                            <th class="px-3 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tbody>
                        <?php $__currentLoopData = $chats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <span class="fw-medium"><?php echo e($chat->reference); ?></span>
                                        <?php if($chat->unread_count_for_user > 0): ?>
                                            <span class="ms-2 badge bg-primary">
                                                <?php echo e($chat->unread_count_for_user); ?> nouveau(x)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <td class="px-3 py-3">
                                    <div class="small fw-medium">
                                        <?php echo e($chat->subject ?: 'Demande d\'assistance'); ?>

                                    </div>
                                    <div class="small text-muted"><?php echo e($chat->formatted_category); ?></div>
                                </td>
                                
                                <td class="px-3 py-3">
                                    <span class="badge 
                                        <?php echo e($chat->status === 'open' ? 'bg-danger' : ''); ?>

                                        <?php echo e($chat->status === 'in_progress' ? 'bg-warning text-dark' : ''); ?>

                                        <?php echo e($chat->status === 'waiting_user' ? 'bg-info' : ''); ?>

                                        <?php echo e($chat->status === 'closed' ? 'bg-success' : ''); ?>">
                                        <?php echo e($chat->formatted_status); ?>

                                    </span>
                                </td>
                                
                                <td class="px-3 py-3">
                                    <span class="badge
                                        <?php echo e($chat->priority === 'low' ? 'bg-secondary' : ''); ?>

                                        <?php echo e($chat->priority === 'normal' ? 'bg-primary' : ''); ?>

                                        <?php echo e($chat->priority === 'high' ? 'bg-warning text-dark' : ''); ?>

                                        <?php echo e($chat->priority === 'urgent' ? 'bg-danger' : ''); ?>">
                                        <?php echo e($chat->formatted_priority); ?>

                                    </span>
                                </td>
                                
                                <td class="px-3 py-3">
                                    <?php if($chat->admin): ?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <?php if($chat->admin->avatar): ?>
                                                    <img class="rounded-circle" 
                                                         src="<?php echo e(asset('storage/' . $chat->admin->avatar)); ?>" 
                                                         alt=""
                                                         style="width: 24px; height: 24px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-primary bg-opacity-25 d-flex align-items-center justify-content-center"
                                                         style="width: 24px; height: 24px;">
                                                        <i class="fas fa-user text-primary" style="font-size: 0.75rem;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <span class="small"><?php echo e($chat->admin->name); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic small">En attente d'assignation</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-3 py-3 small text-muted">
                                    <?php if($chat->last_message_at): ?>
                                        <?php echo e($chat->last_message_at->diffForHumans()); ?>

                                    <?php else: ?>
                                        <?php echo e($chat->created_at->diffForHumans()); ?>

                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-3 py-3 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?php echo e(route('support.show', $chat)); ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if($chat->status !== 'closed'): ?>
                                            <button onclick="closeChat('<?php echo e($chat->id); ?>')" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Fermer">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- État vide -->
            <div class="card-body text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-comments display-1 mb-3 opacity-25"></i>
                    <h3 class="h5 mb-2">Aucune demande de support</h3>
                    <p class="text-muted mb-4">Vous n'avez pas encore créé de demande d'assistance.</p>
                    <a href="<?php echo e(route('support.create')); ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer ma première demande
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Aide et informations -->
    <div class="alert alert-info mt-4" role="alert">
        <div class="d-flex">
            <div class="flex-shrink-0 me-3">
                <i class="fas fa-info-circle fs-4"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading">Comment fonctionne le support ?</h5>
                <ul class="mb-3 small">
                    <li><strong>1. Créez votre demande</strong> - Décrivez votre problème en détail</li>
                    <li><strong>2. Assignation automatique</strong> - Un agent vous sera assigné selon la priorité</li>
                    <li><strong>3. Échange en temps réel</strong> - Communiquez directement avec votre agent</li>
                    <li><strong>4. Résolution</strong> - Une fois votre problème résolu, la conversation sera fermée</li>
                </ul>
                <div class="d-flex flex-wrap gap-3 small">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-danger me-2" style="width: 12px; height: 12px;"></span>
                        <span>Ouvert</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-warning me-2" style="width: 12px; height: 12px;"></span>
                        <span>En cours</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-info me-2" style="width: 12px; height: 12px;"></span>
                        <span>En attente de votre réponse</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2" style="width: 12px; height: 12px;"></span>
                        <span>Résolu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function closeChat(chatId) {
    if (confirm('Êtes-vous sûr de vouloir fermer cette conversation ? Elle sera marquée comme résolue.')) {
        fetch(`/support/${chatId}/close`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la fermeture de la conversation.');
        });
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/support/index.blade.php ENDPATH**/ ?>