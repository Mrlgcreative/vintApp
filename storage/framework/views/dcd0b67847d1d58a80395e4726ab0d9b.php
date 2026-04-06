<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- En-tête avec statistiques -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Support Client</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gérez les demandes d'assistance des utilisateurs</p>
            </div>
            <div class="flex gap-2">
                <button onclick="refreshStats()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Actualiser
                </button>
                <a href="<?php echo e(route('admin.support.stats')); ?>" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>Statistiques
                </a>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <!-- Total -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-blue-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total']); ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="fas fa-comments text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>

            <!-- Ouvert -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-red-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Ouvert</p>
                        <h3 class="text-2xl font-bold text-red-600"><?php echo e($stats['open']); ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
            </div>

            <!-- En cours -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-yellow-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">En cours</p>
                        <h3 class="text-2xl font-bold text-yellow-600"><?php echo e($stats['in_progress']); ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>

            <!-- En attente -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-violet-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">En attente</p>
                        <h3 class="text-2xl font-bold text-violet-600"><?php echo e($stats['waiting_user']); ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-violet-600 dark:text-violet-400"></i>
                    </div>
                </div>
            </div>

            <!-- Fermés aujourd'hui -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-green-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Fermés aujourd'hui</p>
                        <h3 class="text-2xl font-bold text-green-600"><?php echo e($stats['closed_today']); ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>

            <!-- Non assignés -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-amber-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Non assignés</p>
                        <h3 class="text-2xl font-bold text-amber-600"><?php echo e($stats['unassigned']); ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <i class="fas fa-user-times text-amber-600 dark:text-amber-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-6">
        <div class="p-4 sm:p-6">
            <form method="GET" action="<?php echo e(route('admin.support.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recherche</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                           placeholder="Référence, sujet..." 
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les statuts</option>
                        <option value="open" <?php echo e(request('status') === 'open' ? 'selected' : ''); ?>>Ouvert</option>
                        <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>En cours</option>
                        <option value="waiting_user" <?php echo e(request('status') === 'waiting_user' ? 'selected' : ''); ?>>En attente utilisateur</option>
                        <option value="closed" <?php echo e(request('status') === 'closed' ? 'selected' : ''); ?>>Fermé</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priorité</label>
                    <select name="priority" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Toutes les priorités</option>
                        <option value="low" <?php echo e(request('priority') === 'low' ? 'selected' : ''); ?>>Faible</option>
                        <option value="normal" <?php echo e(request('priority') === 'normal' ? 'selected' : ''); ?>>Normale</option>
                        <option value="high" <?php echo e(request('priority') === 'high' ? 'selected' : ''); ?>>Élevée</option>
                        <option value="urgent" <?php echo e(request('priority') === 'urgent' ? 'selected' : ''); ?>>Urgente</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catégorie</label>
                    <select name="category" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Toutes les catégories</option>
                        <option value="technical" <?php echo e(request('category') === 'technical' ? 'selected' : ''); ?>>Technique</option>
                        <option value="account" <?php echo e(request('category') === 'account' ? 'selected' : ''); ?>>Compte</option>
                        <option value="payment" <?php echo e(request('category') === 'payment' ? 'selected' : ''); ?>>Paiement</option>
                        <option value="order" <?php echo e(request('category') === 'order' ? 'selected' : ''); ?>>Commande</option>
                        <option value="general" <?php echo e(request('category') === 'general' ? 'selected' : ''); ?>>Général</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assigné à</label>
                    <select name="assigned_to" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="unassigned" <?php echo e(request('assigned_to') === 'unassigned' ? 'selected' : ''); ?>>Non assigné</option>
                        <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($admin->id); ?>" <?php echo e(request('assigned_to') == $admin->id ? 'selected' : ''); ?>>
                                <?php echo e($admin->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-search mr-1"></i>Filtrer
                    </button>
                    <a href="<?php echo e(route('admin.support.index')); ?>" class="inline-flex items-center justify-center px-3 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des conversations -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Référence</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Sujet</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Priorité</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Assigné à</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Dernier message</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $chats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-900 dark:text-white"><?php echo e($chat->reference); ?></span>
                                    <?php if($chat->unread_count_for_admin > 0): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                            <?php echo e($chat->unread_count_for_admin); ?> nouveau(x)
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <?php if($chat->user?->avatar): ?>
                                        <img class="w-8 h-8 rounded-full object-cover" src="<?php echo e($chat->user->avatar_url); ?>" alt="">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500 dark:text-gray-400 text-xs"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white text-sm"><?php echo e($chat->user?->name ?? 'Utilisateur supprimé'); ?></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($chat->user?->email ?? 'N/A'); ?></div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-900 dark:text-white text-sm">
                                    <?php echo e($chat->subject ?: 'Demande d\'assistance'); ?>

                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($chat->formatted_category); ?></div>
                            </td>
                            
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full
                                    <?php echo e($chat->status === 'open' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : ''); ?>

                                    <?php echo e($chat->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : ''); ?>

                                    <?php echo e($chat->status === 'waiting_user' ? 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-400' : ''); ?>

                                    <?php echo e($chat->status === 'closed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : ''); ?>">
                                    <?php echo e($chat->formatted_status); ?>

                                </span>
                            </td>
                            
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full
                                    <?php echo e($chat->priority === 'low' ? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300' : ''); ?>

                                    <?php echo e($chat->priority === 'normal' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : ''); ?>

                                    <?php echo e($chat->priority === 'high' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' : ''); ?>

                                    <?php echo e($chat->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : ''); ?>">
                                    <?php echo e($chat->formatted_priority); ?>

                                </span>
                            </td>
                            
                            <td class="px-4 py-4">
                                <?php if($chat->admin): ?>
                                    <div class="flex items-center gap-2">
                                        <?php if($chat->admin->avatar): ?>
                                            <img class="w-6 h-6 rounded-full object-cover" src="<?php echo e($chat->admin->avatar_url); ?>" alt="">
                                        <?php else: ?>
                                            <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600 dark:text-blue-400" style="font-size: 0.6rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="text-sm text-gray-700 dark:text-gray-300"><?php echo e($chat->admin->name); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-sm text-gray-400 dark:text-gray-500 italic">Non assigné</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <?php if($chat->last_message_at): ?>
                                    <?php echo e($chat->last_message_at->diffForHumans()); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('admin.support.show', $chat)); ?>" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($chat->status !== 'closed'): ?>
                                        <button onclick="assignChat(<?php echo e($chat->id); ?>)" 
                                                class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors" 
                                                title="Assigner">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <button onclick="closeChat(<?php echo e($chat->id); ?>)" 
                                                class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" 
                                                title="Fermer">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    <?php else: ?>
                                        <button onclick="reopenChat(<?php echo e($chat->id); ?>)" 
                                                class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors" 
                                                title="Rouvrir">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-comments text-5xl mb-4"></i>
                                    <p class="text-lg font-medium mb-1">Aucune conversation de support</p>
                                    <p class="text-sm">Les demandes d'assistance apparaîtront ici.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($chats->hasPages()): ?>
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                <?php echo e($chats->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal d'assignation -->
<div id="assignModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black/50 transition-opacity modal-overlay" onclick="closeAssignModal()"></div>
        
        <!-- Modal content -->
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl transform transition-all sm:my-8 sm:max-w-md w-full mx-4">
            <form id="assignForm">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assigner la conversation</h3>
                    <button type="button" onclick="closeAssignModal()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Body -->
                <div class="px-6 py-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sélectionner un administrateur</label>
                    <select id="adminSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Choisir un admin...</option>
                        <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($admin->id); ?>"><?php echo e($admin->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeAssignModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        Assigner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentChatId = null;

function assignChat(chatId) {
    currentChatId = chatId;
    document.getElementById('assignModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function closeChat(chatId) {
    if (confirm('Êtes-vous sûr de vouloir fermer cette conversation ?')) {
        fetch(`/admin/support/${chatId}/close`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la fermeture de la conversation.');
        });
    }
}

function reopenChat(chatId) {
    if (confirm('Êtes-vous sûr de vouloir rouvrir cette conversation ?')) {
        fetch(`/admin/support/${chatId}/reopen`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la réouverture de la conversation.');
        });
    }
}

function refreshStats() {
    location.reload();
}

// Gestion du formulaire d'assignation
document.getElementById('assignForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const adminId = document.getElementById('adminSelect').value;
    
    if (!adminId) {
        alert('Veuillez sélectionner un admin.');
        return;
    }
    
    fetch(`/admin/support/${currentChatId}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ admin_id: adminId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAssignModal();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de l\'assignation.');
    });
});

// Fermer le modal avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('assignModal').classList.contains('hidden')) {
        closeAssignModal();
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/admin/support/index.blade.php ENDPATH**/ ?>