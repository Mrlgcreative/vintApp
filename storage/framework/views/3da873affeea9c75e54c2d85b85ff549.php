<?php $__env->startSection('content'); ?>

<!-- 🎉 MODALE DE SUCCÈS - Affichée uniquement après vérification d'email -->
<?php if(session('email_verified')): ?>
<div class="modal fade show" id="emailVerifiedModal" tabindex="-1" aria-labelledby="emailVerifiedModalLabel" style="display: block; background-color: rgba(0, 0, 0, 0.5);" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header avec animation -->
            <div class="modal-header border-0 bg-gradient text-white text-center d-block" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
                <div class="text-center py-3">
                    <!-- Icône animée -->
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white" style="width: 80px; height: 80px; animation: bounce 1s infinite;">
                            <i class="fas fa-check-circle text-success" style="font-size: 3.5rem;"></i>
                        </div>
                    </div>
                    <h3 class="modal-title fw-bold mb-0" id="emailVerifiedModalLabel">
                        🎉 Email Vérifié avec Succès !
                    </h3>
                </div>
            </div>
            
            <!-- Contenu -->
            <div class="modal-body px-4 py-4 text-center">
                <p class="text-dark fs-5 mb-3">
                    <strong>Bienvenue sur VintApp !</strong>
                </p>
                <p class="text-muted mb-4">
                    Votre compte est maintenant <span class="badge bg-success">ACTIF</span>. Vous avez désormais accès à toutes les fonctionnalités de la plateforme.
                </p>
                
                <!-- Liste des fonctionnalités débloquées -->
                <div class="text-start bg-light rounded-3 p-3 mb-4">
                    <p class="fw-semibold text-dark mb-2">
                        <i class="fas fa-unlock text-success me-2"></i>
                        Fonctionnalités débloquées :
                    </p>
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <span>Créer et vendre des articles</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <span>Passer des commandes</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <span>Envoyer des messages</span>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <span>Gérer votre profil</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Confetti symbolique -->
                <div class="mb-3" style="font-size: 2rem;">
                    🎊 🎉 ✨ 🎊
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-success btn-lg px-5 shadow-sm" data-bs-dismiss="modal" onclick="this.closest('.modal').style.display='none'">
                    <i class="fas fa-rocket me-2"></i>
                    Commencer à Explorer
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }
    
    .bg-gradient {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    }
    
    #emailVerifiedModal .modal-content {
        animation: slideInDown 0.5s ease-out;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    // Fermer la modale si on clique en dehors
    document.getElementById('emailVerifiedModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
    
    // Fermer avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('emailVerifiedModal');
            if (modal) modal.style.display = 'none';
        }
    });
</script>
<?php endif; ?>

<style>
/* Override Bootstrap avec des styles Tailwind-like */
.tw-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1rem;
    margin-top: 2rem;
}
.tw-grid {
    display: grid;
    gap: 1.5rem;
}
.tw-grid-cols-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}
.tw-grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}
.tw-grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}
.tw-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    border: 1px solid #f3f4f6;
    transition: all 0.2s;
}
.tw-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px) scale(1.02);
}
.tw-p-6 { padding: 1.5rem; }
.tw-p-4 { padding: 1rem; }
.tw-mb-8 { margin-bottom: 2rem; }
.tw-mb-4 { margin-bottom: 1rem; }
.tw-flex { display: flex; }
.tw-items-center { align-items: center; }
.tw-space-x-4 > * + * { margin-left: 1rem; }
.tw-space-y-3 > * + * { margin-top: 0.75rem; }
.tw-w-12 { width: 3rem; }
.tw-h-12 { height: 3rem; }
.tw-rounded-full { border-radius: 9999px; }
.tw-bg-gradient-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
.tw-bg-gradient-green { background: linear-gradient(135deg, #10b981, #34d399); }
.tw-bg-gradient-blue { background: linear-gradient(135deg, #06b6d4, #67e8f9); }
.tw-bg-gradient-orange { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.tw-text-white { color: white; }
.tw-text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
.tw-font-bold { font-weight: 700; }
.tw-font-semibold { font-weight: 600; }
.tw-text-gray-900 { color: #111827; }
.tw-text-gray-600 { color: #4b5563; }
.tw-text-gray-500 { color: #6b7280; }
.tw-text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.tw-bg-slate-50 { background-color: #f8fafc; }
.tw-border-b { border-bottom-width: 1px; }
.tw-border-gray-100 { border-color: #f3f4f6; }
.tw-rounded-t-xl { border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem; }
.tw-bg-gray-50 { background-color: #f9fafb; }
.tw-rounded-lg { border-radius: 0.5rem; }
.tw-hover-violet:hover { background-color: #f3f0ff; }
.tw-hover-green:hover { background-color: #ecfdf5; }
.tw-hover-blue:hover { background-color: #f0f9ff; }
.tw-hover-orange:hover { background-color: #fffbeb; }
.tw-px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
.tw-py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
.tw-bg-violet-500 { background-color: #8b5cf6; }
.tw-bg-green-500 { background-color: #10b981; }
.tw-bg-yellow-500 { background-color: #eab308; }
.tw-text-center { text-align: center; }
.tw-py-8 { padding-top: 2rem; padding-bottom: 2rem; }
.tw-py-12 { padding-top: 3rem; padding-bottom: 3rem; }
.tw-text-xl { font-size: 1.25rem; line-height: 1.75rem; }
.tw-text-2xl { font-size: 1.5rem; line-height: 2rem; }
.tw-text-indigo-600 { color: #4f46e5; }
.tw-bg-indigo-50 { background-color: #eef2ff; }
.tw-border-indigo-100 { border-color: #e0e7ff; }
.tw-flex-1 { flex: 1 1 0%; }
.tw-justify-between { justify-content: space-between; }
.tw-items-start { align-items: flex-start; }
.tw-ml-3 { margin-left: 0.75rem; }
.tw-whitespace-nowrap { white-space: nowrap; }
.tw-text-xs { font-size: 0.75rem; line-height: 1rem; }
.tw-leading-relaxed { line-height: 1.625; }

@media (max-width: 1024px) {
    .tw-grid-cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .tw-grid-cols-4, .tw-grid-cols-2 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
    .tw-container { padding: 0 0.5rem; }
}

/* Styles responsive pour le graphique */
#sales-chart-container {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    height: 250px;
    gap: 1rem;
    margin-bottom: 1.5rem;
    background-color: #fafafa;
    padding: 1rem;
    border-radius: 0.5rem;
}

@media (max-width: 1024px) {
    #sales-chart-container {
        height: 220px;
        gap: 0.75rem;
        padding: 0.75rem;
    }
    .chart-bar-label {
        font-size: 1rem !important;
    }
    .chart-month-label {
        font-size: 0.75rem !important;
    }
}

@media (max-width: 768px) {
    #sales-chart-container {
        height: 200px;
        gap: 0.5rem;
        padding: 0.5rem;
        overflow-x: auto;
        justify-content: flex-start;
    }
    .chart-bar-column {
        min-width: 50px !important;
        flex: 0 0 50px;
    }
    .chart-bar-label {
        font-size: 0.875rem !important;
    }
    .chart-month-label {
        font-size: 0.7rem !important;
    }
    .chart-stats-grid {
        grid-template-columns: repeat(1, 1fr) !important;
        gap: 0.75rem !important;
    }
}

@media (max-width: 480px) {
    #sales-chart-container {
        height: 180px;
        gap: 0.4rem;
        padding: 0.5rem;
    }
    .chart-bar-column {
        min-width: 40px !important;
        flex: 0 0 40px;
    }
    .chart-bar-label {
        font-size: 0.75rem !important;
    }
    .chart-month-label {
        font-size: 0.65rem !important;
    }
    .chart-percentage-label {
        font-size: 0.6rem !important;
    }
}
</style>

<div class="tw-container">
    <!-- Cards de statistiques -->
    <div class="tw-grid tw-grid-cols-4 tw-mb-8">
        <!-- Card Articles -->
        <div class="tw-card">
            <div class="tw-p-6 tw-flex tw-items-center tw-space-x-4">
                <div class="tw-w-12 tw-h-12 tw-bg-gradient-violet tw-rounded-full tw-flex tw-items-center" style="justify-content: center;">
                    <i class="fas fa-box tw-text-white" style="font-size: 1.25rem;"></i>
                </div>
                <div class="tw-flex-1">
                    <div class="tw-text-gray-600 tw-font-semibold">Articles</div>
                    <div class="tw-text-3xl tw-font-bold tw-text-gray-900"><?php echo e($stats['total_items'] ?? 0); ?></div>
                    <div class="tw-text-sm tw-text-gray-500">Actifs : <?php echo e($stats['active_items'] ?? 0); ?></div>
                </div>
            </div>
        </div>

        <!-- Card Ventes -->
        <div class="tw-card">
            <div class="tw-p-6 tw-flex tw-items-center tw-space-x-4">
                <div class="tw-w-12 tw-h-12 tw-bg-gradient-green tw-rounded-full tw-flex tw-items-center" style="justify-content: center;">
                    <i class="fas fa-shopping-cart tw-text-white" style="font-size: 1.25rem;"></i>
                </div>
                <div class="tw-flex-1">
                    <div class="tw-text-gray-600 tw-font-semibold">Ventes</div>
                    <div class="tw-text-3xl tw-font-bold tw-text-gray-900"><?php echo e($stats['total_sales'] ?? 0); ?></div>
                    <div class="tw-text-sm tw-text-gray-500">
                        <?php echo e(number_format($stats['total_revenue'] ?? 0, 2)); ?> USD
                        <span class="tw-text-gray-400">|</span>
                        <?php echo e(number_format(($stats['total_revenue'] ?? 0) * 2450, 0)); ?> CDF
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Messages -->
        <div class="tw-card">
            <div class="tw-p-6 tw-flex tw-items-center tw-space-x-4">
                <div class="tw-w-12 tw-h-12 tw-bg-gradient-blue tw-rounded-full tw-flex tw-items-center" style="justify-content: center;">
                    <i class="fas fa-envelope tw-text-white" style="font-size: 1.25rem;"></i>
                </div>
                <div class="tw-flex-1">
                    <div class="tw-text-gray-600 tw-font-semibold">Messages</div>
                    <div class="tw-text-3xl tw-font-bold tw-text-gray-900"><?php echo e($stats['unread_messages'] ?? 0); ?></div>
                    <div class="tw-text-sm tw-text-gray-500">Non lus</div>
                </div>
            </div>
        </div>

        <!-- Card Support -->
        <div class="tw-card">
            <div class="tw-p-6 tw-flex tw-items-center tw-space-x-4">
                <div class="tw-w-12 tw-h-12 tw-bg-gradient-orange tw-rounded-full tw-flex tw-items-center" style="justify-content: center;">
                    <i class="fas fa-headset tw-text-white" style="font-size: 1.25rem;"></i>
                </div>
                <div class="tw-flex-1">
                    <div class="tw-text-gray-600 tw-font-semibold">Support</div>
                    <div class="tw-text-3xl tw-font-bold tw-text-gray-900"><?php echo e($stats['pending_support_chats'] ?? 0); ?></div>
                    <div class="tw-text-sm tw-text-gray-500">En attente</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Support -->
    <div class="tw-mb-8">
        <div class="tw-card">
            <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">
                <div class="tw-flex tw-justify-between tw-items-center">
                    <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                        <i class="fas fa-headset" style="color: #f59e0b; margin-right: 0.75rem;"></i>
                        Support Client
                    </h3>
                    <?php if(Route::has('admin.support.index')): ?>
                        <a href="<?php echo e(route('admin.support.index')); ?>" 
                           class="tw-text-sm tw-text-blue-600 hover:tw-text-blue-800 tw-font-medium">
                            Voir tout
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('support.index')); ?>" 
                           class="tw-text-sm tw-text-blue-600 hover:tw-text-blue-800 tw-font-medium">
                            Mes demandes
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tw-p-6">
                <div class="tw-grid tw-grid-cols-4 tw-gap-4">
                    <div class="tw-text-center">
                        <div class="tw-text-2xl tw-font-bold tw-text-gray-900"><?php echo e($stats['total_support_chats'] ?? 0); ?></div>
                        <div class="tw-text-sm tw-text-gray-500">Total conversations</div>
                    </div>
                    <div class="tw-text-center">
                        <div class="tw-text-2xl tw-font-bold tw-text-red-600"><?php echo e($stats['open_support_chats'] ?? 0); ?></div>
                        <div class="tw-text-sm tw-text-gray-500">Nouvelles demandes</div>
                    </div>
                    <div class="tw-text-center">
                        <div class="tw-text-2xl tw-font-bold tw-text-yellow-600"><?php echo e($stats['pending_support_chats'] ?? 0); ?></div>
                        <div class="tw-text-sm tw-text-gray-500">En cours</div>
                    </div>
                    <div class="tw-text-center">
                        <div class="tw-text-2xl tw-font-bold tw-text-orange-600"><?php echo e($stats['unassigned_support_chats'] ?? 0); ?></div>
                        <div class="tw-text-sm tw-text-gray-500">Non assignées</div>
                    </div>
                </div>
                <?php if(($stats['unassigned_support_chats'] ?? 0) > 0): ?>
                    <div class="tw-mt-4 tw-p-3 tw-bg-orange-50 tw-border tw-border-orange-200 tw-rounded-lg">
                        <div class="tw-flex tw-items-center">
                            <i class="fas fa-exclamation-triangle tw-text-orange-600 tw-mr-2"></i>
                            <span class="tw-text-orange-800 tw-font-medium">
                                <?php echo e($stats['unassigned_support_chats'] ?? 0); ?> conversation(s) nécessitent votre attention
                            </span>
                            <?php if(Route::has('admin.support.index')): ?>
                                <a href="<?php echo e(route('admin.support.index', ['assigned_to' => 'unassigned'])); ?>" 
                                   class="tw-ml-auto tw-text-orange-600 hover:tw-text-orange-800 tw-font-medium">
                                    Voir →
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Section Articles et Commandes -->
    <div class="tw-grid tw-grid-cols-2 tw-mb-8">
        <!-- Articles récents -->
        <div class="tw-card">
            <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">
                <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                    <i class="fas fa-box" style="color: #8b5cf6; margin-right: 0.75rem;"></i>
                    Articles récents
                </h3>
            </div>
            <div class="tw-p-6">
                <?php if(isset($recentItems) && $recentItems->count() > 0): ?>
                    <div class="tw-space-y-3">
                        <?php $__currentLoopData = $recentItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="tw-flex tw-justify-between tw-items-center tw-p-4 tw-bg-gray-50 tw-rounded-lg tw-hover-violet" style="transition: background-color 0.15s;">
                                <div>
                                    <h6 class="tw-font-semibold tw-text-gray-900 tw-mb-4" style="margin-bottom: 0.25rem;"><?php echo e($item->name); ?></h6>
                                    <small class="tw-text-gray-500"><?php echo e($item->category->name ?? 'N/A'); ?></small>
                                </div>
                                <span class="tw-px-3 tw-py-1 tw-bg-violet-500 tw-text-white tw-text-sm tw-font-semibold" style="border-radius: 9999px;">
                                    <?php echo e($item->formatted_price); ?>

                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="tw-text-gray-500 tw-text-center tw-py-8">Aucun article récent</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Commandes récentes -->
        <div class="tw-card">
            <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">
                <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                    <i class="fas fa-shopping-cart" style="color: #10b981; margin-right: 0.75rem;"></i>
                    Commandes récentes
                </h3>
            </div>
            <div class="tw-p-6">
                <?php if(isset($recentOrders) && $recentOrders->count() > 0): ?>
                    <div class="tw-space-y-3">
                        <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="tw-flex tw-justify-between tw-items-center tw-p-4 tw-bg-gray-50 tw-rounded-lg tw-hover-green" style="transition: background-color 0.15s;">
                                <div>
                                    <h6 class="tw-font-semibold tw-text-gray-900" style="margin-bottom: 0.25rem;">Commande #<?php echo e($order->id); ?></h6>
                                    <small class="tw-text-gray-500"><?php echo e($order->item->name ?? 'N/A'); ?></small>
                                </div>
                                <span class="tw-px-3 tw-py-1 tw-text-sm tw-font-semibold <?php echo e($order->status === 'completed' ? 'tw-bg-green-500' : 'tw-bg-yellow-500'); ?> tw-text-white" style="border-radius: 9999px;">
                                    <?php echo e(ucfirst($order->status)); ?>

                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="tw-text-gray-500 tw-text-center tw-py-8">Aucune commande récente</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Section Messages et Notifications -->
    <div class="tw-grid tw-grid-cols-2 tw-mb-8">
        <!-- Messages récents -->
        <div class="tw-card">
            <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">
                <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                    <i class="fas fa-envelope" style="color: #06b6d4; margin-right: 0.75rem;"></i>
                    Messages récents
                </h3>
            </div>
            <div class="tw-p-6">
                <?php if(isset($recentMessages) && $recentMessages->count() > 0): ?>
                    <div class="tw-space-y-3">
                        <?php $__currentLoopData = $recentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="tw-p-4 tw-bg-gray-50 tw-rounded-lg tw-hover-blue" style="transition: background-color 0.15s;">
                                <div class="tw-flex tw-justify-between tw-items-start">
                                    <div class="tw-flex-1">
                                        <h6 class="tw-font-semibold tw-text-gray-900" style="margin-bottom: 0.25rem;"><?php echo e($msg->sender->name ?? 'N/A'); ?></h6>
                                        <p class="tw-text-gray-600 tw-text-sm tw-leading-relaxed"><?php echo e(Str::limit($msg->content, 50)); ?></p>
                                    </div>
                                    <small class="tw-text-gray-500 tw-text-xs tw-ml-3 tw-whitespace-nowrap"><?php echo e($msg->created_at->diffForHumans()); ?></small>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="tw-text-gray-500 tw-text-center tw-py-8">Aucun message récent</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notifications -->
        <div class="tw-card">
            <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">  
                <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                    <i class="fas fa-bell" style="color: #f59e0b; margin-right: 0.75rem;"></i>
                    Notifications
                </h3>
            </div>
            <div class="tw-p-6">
                <?php if(isset($notifications) && $notifications->count() > 0): ?>
                    <div class="tw-space-y-3">
                        <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="tw-p-4 tw-bg-gray-50 tw-rounded-lg tw-hover-orange" style="transition: background-color 0.15s;">
                                <div class="tw-flex tw-justify-between tw-items-start">
                                    <div class="tw-flex-1">
                                        <h6 class="tw-font-semibold tw-text-gray-900" style="margin-bottom: 0.25rem;"><?php echo e($notif->title); ?></h6>
                                        <p class="tw-text-gray-600 tw-text-sm tw-leading-relaxed"><?php echo e(Str::limit($notif->message, 50)); ?></p>
                                    </div>
                                    <small class="tw-text-gray-500 tw-text-xs tw-ml-3 tw-whitespace-nowrap"><?php echo e($notif->created_at->diffForHumans()); ?></small>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="tw-text-gray-500 tw-text-center tw-py-8">Aucune notification</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Section Graphique des ventes -->
    <div class="tw-card">
        <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">
            <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                <i class="fas fa-chart-line" style="color: #4f46e5; margin-right: 0.75rem;"></i>
                Évolution des ventes (6 derniers mois)
            </h3>
        </div>
        <div class="tw-p-6">
            <?php
                // Données de démonstration si $salesChart n'existe pas
                $chartData = $salesChart ?? [
                    'labels' => ['Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct'],
                    'data' => [12, 19, 15, 25, 22, 30]
                ];
                $maxValue = max($chartData['data']) ?: 1;
            ?>
            
            <!-- Graphique en barres visuelles -->
            <div style="padding: 1rem 0; background-color: white;">
                <!-- Message d'aide scroll mobile (visible uniquement sur mobile) -->
                <div id="chart-scroll-hint" style="display: none; text-align: center; color: #6b7280; font-size: 0.75rem; margin-bottom: 0.5rem; padding: 0.5rem; background-color: #f3f4f6; border-radius: 0.25rem;">
                    <i class="fas fa-hand-point-right"></i> Faites défiler horizontalement pour voir tous les mois
                </div>
                
                <div id="sales-chart-container">
                    <?php $__currentLoopData = $chartData['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $percentage = ($value / $maxValue) * 100;
                            $colors = ['#8b5cf6', '#10b981', '#06b6d4', '#f59e0b', '#ef4444', '#6366f1'];
                            $color = $colors[$index % count($colors)];
                        ?>
                        <div class="chart-bar-column" style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; min-width: 60px;">
                            <!-- Valeur au-dessus de la barre -->
                            <div class="chart-bar-label" style="font-size: 1.25rem; font-weight: 700; color: #111827; min-height: 30px;">
                                <?php echo e($value); ?>

                            </div>
                            <!-- Conteneur de barre avec fond visible -->
                            <div style="width: 100%; background-color: #e5e7eb; border-radius: 0.5rem; position: relative; overflow: hidden; flex-grow: 1; display: flex; align-items: flex-end; border: 1px solid #d1d5db;">
                                <div class="chart-bar" 
                                     style="width: 100%; 
                                            background: linear-gradient(180deg, <?php echo e($color); ?> 0%, <?php echo e($color); ?>cc 100%); 
                                            border-radius: 0.5rem 0.5rem 0 0;
                                            transition: height 1.2s cubic-bezier(0.4, 0, 0.2, 1);
                                            height: 0%;
                                            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
                                            position: relative;"
                                     data-height="<?php echo e($percentage); ?>">
                                     <!-- Mini étiquette sur la barre -->
                                     <div class="chart-percentage-label" style="position: absolute; top: 5px; left: 0; right: 0; text-align: center; color: white; font-size: 0.75rem; font-weight: 600; opacity: 0.9;">
                                         <?php echo e(number_format($percentage, 0)); ?>%
                                     </div>
                                </div>
                            </div>
                            <!-- Label du mois -->
                            <div class="chart-month-label" style="font-size: 0.875rem; font-weight: 600; color: #4b5563; margin-top: 0.5rem;">
                                <?php echo e($chartData['labels'][$index]); ?>

                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <!-- Légende et statistiques -->
                <div class="chart-stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid #f3f4f6;">
                    <div style="text-align: center; padding: 1rem; background-color: #f9fafb; border-radius: 0.5rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-bottom: 0.5rem;">TOTAL</div>
                        <div style="font-size: 1.875rem; font-weight: 700; color: #8b5cf6;">
                            <?php echo e(array_sum($chartData['data'])); ?>

                        </div>
                        <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">ventes</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background-color: #f9fafb; border-radius: 0.5rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-bottom: 0.5rem;">MOYENNE</div>
                        <div style="font-size: 1.875rem; font-weight: 700; color: #10b981;">
                            <?php echo e(round(array_sum($chartData['data']) / count($chartData['data']), 1)); ?>

                        </div>
                        <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">par mois</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background-color: #f9fafb; border-radius: 0.5rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-bottom: 0.5rem;">MEILLEUR</div>
                        <div style="font-size: 1.875rem; font-weight: 700; color: #f59e0b;">
                            <?php echo e(max($chartData['data'])); ?>

                        </div>
                        <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;"><?php echo e($chartData['labels'][array_search(max($chartData['data']), $chartData['data'])]); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Animation des barres du graphique avec fallback
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🎨 Initialisation du graphique...');
        
        const bars = document.querySelectorAll('.chart-bar');
        console.log('📊 Barres trouvées:', bars.length);
        
        if (bars.length === 0) {
            console.warn('⚠️ Aucune barre trouvée!');
            return;
        }
        
        // Animation immédiate avec délai progressif
        bars.forEach((bar, index) => {
            const targetHeight = bar.getAttribute('data-height');
            console.log(`Barre ${index}: hauteur cible = ${targetHeight}%`);
            
            setTimeout(() => {
                bar.style.height = targetHeight + '%';
                bar.style.opacity = '1';
            }, 200 + (index * 100)); // Délai progressif pour effet cascade
        });
        
        // Observer pour réanimer si nécessaire (scroll)
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && entry.target.style.height === '0%') {
                        const bar = entry.target;
                        const targetHeight = bar.getAttribute('data-height');
                        setTimeout(() => {
                            bar.style.height = targetHeight + '%';
                            bar.style.opacity = '1';
                        }, 100);
                    }
                });
            }, { threshold: 0.1 });
            
            bars.forEach(bar => observer.observe(bar));
        }
        
        console.log('✅ Graphique initialisé avec succès');
    });
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/dashboard/index.blade.php ENDPATH**/ ?>