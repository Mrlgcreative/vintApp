

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">Mes demandes de support</h1>
                <p class="text-gray-600">Gérez vos conversations avec notre équipe d'assistance</p>
            </div>
            <a href="<?php echo e(route('support.create')); ?>" 
               class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold hover:from-purple-700 hover:to-blue-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle demande
            </a>
        </div>

        <!-- Statistiques rapides -->
        <?php if($chats->count() > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-lg border-l-4 border-purple-500 p-6 transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo e($chats->count()); ?></p>
                        </div>
                        <div class="p-4 rounded-xl bg-purple-100">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border-l-4 border-amber-500 p-6 transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">En cours</p>
                            <p class="text-3xl font-bold text-amber-600"><?php echo e($chats->whereIn('status', ['open', 'in_progress'])->count()); ?></p>
                        </div>
                        <div class="p-4 rounded-xl bg-amber-100">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border-l-4 border-blue-500 p-6 transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">En attente</p>
                            <p class="text-3xl font-bold text-blue-600"><?php echo e($chats->where('status', 'waiting_user')->count()); ?></p>
                        </div>
                        <div class="p-4 rounded-xl bg-blue-100">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 p-6 transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Résolues</p>
                            <p class="text-3xl font-bold text-green-600"><?php echo e($chats->where('status', 'closed')->count()); ?></p>
                        </div>
                        <div class="p-4 rounded-xl bg-green-100">
                            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Liste des conversations -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <?php if($chats->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Référence</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sujet</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Priorité</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Agent</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dernière activité</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $chats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-purple-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-900"><?php echo e($chat->reference); ?></span>
                                            <?php if($chat->unread_count_for_user > 0): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-600 text-white animate-pulse">
                                                    <?php echo e($chat->unread_count_for_user); ?> nouveau(x)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($chat->subject ?: 'Demande d\'assistance'); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($chat->formatted_category); ?></div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                            <?php echo e($chat->status === 'open' ? 'bg-red-100 text-red-800' : ''); ?>

                                            <?php echo e($chat->status === 'in_progress' ? 'bg-amber-100 text-amber-800' : ''); ?>

                                            <?php echo e($chat->status === 'waiting_user' ? 'bg-blue-100 text-blue-800' : ''); ?>

                                            <?php echo e($chat->status === 'closed' ? 'bg-green-100 text-green-800' : ''); ?>">
                                            <?php echo e($chat->formatted_status); ?>

                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                            <?php echo e($chat->priority === 'low' ? 'bg-gray-100 text-gray-800' : ''); ?>

                                            <?php echo e($chat->priority === 'normal' ? 'bg-blue-100 text-blue-800' : ''); ?>

                                            <?php echo e($chat->priority === 'high' ? 'bg-orange-100 text-orange-800' : ''); ?>

                                            <?php echo e($chat->priority === 'urgent' ? 'bg-red-100 text-red-800' : ''); ?>">
                                            <?php echo e($chat->formatted_priority); ?>

                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($chat->admin): ?>
                                            <div class="flex items-center gap-2">
                                                <?php if($chat->admin->avatar): ?>
                                                    <img class="w-8 h-8 rounded-full object-cover ring-2 ring-purple-200" 
                                                         src="<?php echo e(asset('storage/' . $chat->admin->avatar)); ?>" 
                                                         alt="<?php echo e($chat->admin->name); ?>">
                                                <?php else: ?>
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center ring-2 ring-purple-200">
                                                        <span class="text-xs font-bold text-white"><?php echo e(substr($chat->admin->name, 0, 1)); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="text-sm font-medium text-gray-900"><?php echo e($chat->admin->name); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-sm italic text-gray-400">En attente d'assignation</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if($chat->last_message_at): ?>
                                            <?php echo e($chat->last_message_at->diffForHumans()); ?>

                                        <?php else: ?>
                                            <?php echo e($chat->created_at->diffForHumans()); ?>

                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <a href="<?php echo e(route('support.show', $chat)); ?>" 
                                               class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-purple-100 text-purple-700 hover:bg-purple-200 transition-all duration-200"
                                               title="Voir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <?php if($chat->status !== 'closed'): ?>
                                                <button onclick="closeChat('<?php echo e($chat->id); ?>')" 
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200" 
                                                        title="Fermer">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
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
                <div class="text-center py-16 px-4">
                    <svg class="mx-auto w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune demande de support</h3>
                    <p class="text-gray-600 mb-6">Vous n'avez pas encore créé de demande d'assistance.</p>
                    <a href="<?php echo e(route('support.create')); ?>" 
                       class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold hover:from-purple-700 hover:to-blue-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Créer ma première demande
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Aide et informations -->
        <div class="mt-8 p-6 rounded-2xl bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl bg-blue-100 flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h5 class="text-lg font-bold text-gray-900 mb-3">Comment fonctionne le support ?</h5>
                    <ul class="space-y-2 mb-4">
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-600 text-white text-xs font-bold flex-shrink-0 mt-0.5">1</span>
                            <span class="text-gray-700"><strong>Créez votre demande</strong> - Décrivez votre problème en détail</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-600 text-white text-xs font-bold flex-shrink-0 mt-0.5">2</span>
                            <span class="text-gray-700"><strong>Assignation automatique</strong> - Un agent vous sera assigné selon la priorité</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-600 text-white text-xs font-bold flex-shrink-0 mt-0.5">3</span>
                            <span class="text-gray-700"><strong>Échange en temps réel</strong> - Communiquez directement avec votre agent</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-600 text-white text-xs font-bold flex-shrink-0 mt-0.5">4</span>
                            <span class="text-gray-700"><strong>Résolution</strong> - Une fois votre problème résolu, la conversation sera fermée</span>
                        </li>
                    </ul>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-500"></span>
                            <span class="text-sm text-gray-600">Ouvert</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="text-sm text-gray-600">En cours</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <span class="text-sm text-gray-600">En attente de votre réponse</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-sm text-gray-600">Résolu</span>
                        </div>
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

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/support/index.blade.php ENDPATH**/ ?>