

<?php $__env->startSection('title', 'Tableau de bord'); ?>

<?php $__env->startSection('content'); ?>
<div>
    
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
            Bonjour, <?php echo e(auth()->user()->name); ?> 👋
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Voici un aperçu de votre activité support</p>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-emerald-500 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Mes tickets actifs</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['active']); ?></h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">sur <?php echo e($stats['max_chats']); ?> max</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-inbox text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
            <?php $pct = $stats['max_chats'] > 0 ? min(100, ($stats['active'] / $stats['max_chats']) * 100) : 0; ?>
            <div class="mt-2 w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full">
                <div class="<?php echo e($pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-emerald-500')); ?> h-full rounded-full transition-all" style="width: <?php echo e($pct); ?>%"></div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-orange-500 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Attendent ma réponse</p>
                    <h3 class="text-2xl font-bold <?php echo e($stats['waiting_reply'] > 0 ? 'text-orange-600' : 'text-gray-900 dark:text-white'); ?>"><?php echo e($stats['waiting_reply']); ?></h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-reply text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-blue-500 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Résolus aujourd'hui</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['closed_today']); ?></h3>
                    <p class="text-[10px] text-gray-400 mt-0.5"><?php echo e($stats['closed_week']); ?> cette semaine</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-check-circle text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        
        <a href="<?php echo e(route('agent.unassigned')); ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-red-500 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Non assignés</p>
                    <h3 class="text-2xl font-bold <?php echo e($stats['unassigned'] > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white'); ?>"><?php echo e($stats['unassigned']); ?></h3>
                    <p class="text-[10px] text-emerald-600 mt-0.5">Cliquer pour voir →</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-inbox mr-2 text-emerald-500"></i>Tickets récents
                </h2>
                <a href="<?php echo e(route('agent.tickets')); ?>" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Voir tous →
                </a>
            </div>
            <?php if($recentTickets->isEmpty()): ?>
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-check-circle text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm">Aucun ticket actif</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__currentLoopData = $recentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('agent.show', $ticket)); ?>" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                    <?php echo e(strtoupper(substr($ticket->user->name ?? '?', 0, 2))); ?>

                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?php echo e($ticket->subject); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo e($ticket->reference); ?> · <?php echo e($ticket->user->name ?? 'Utilisateur'); ?>

                                </p>
                            </div>
                            <div class="flex-shrink-0 flex flex-col items-end gap-1">
                                <?php
                                    $statusColors = [
                                        'open' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'waiting_user' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'closed' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400',
                                    ];
                                    $statusLabels = ['open' => 'Ouvert', 'in_progress' => 'En cours', 'waiting_user' => 'Attente', 'closed' => 'Fermé'];
                                ?>
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full <?php echo e($statusColors[$ticket->status] ?? ''); ?>">
                                    <?php echo e($statusLabels[$ticket->status] ?? $ticket->status); ?>

                                </span>
                                <span class="text-[10px] text-gray-400"><?php echo e($ticket->last_message_at?->diffForHumans()); ?></span>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-fire mr-2 text-red-500"></i>Urgents non assignés
                </h2>
                <a href="<?php echo e(route('agent.unassigned')); ?>" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Voir tous →
                </a>
            </div>
            <?php if($urgentUnassigned->isEmpty()): ?>
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-shield-alt text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm">Aucun ticket urgent</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__currentLoopData = $urgentUnassigned; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center gap-3 px-5 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?php echo e($ticket->subject); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo e($ticket->reference); ?> · <?php echo e($ticket->user->name ?? '?'); ?> · <?php echo e($ticket->created_at->diffForHumans()); ?>

                                </p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <?php
                                    $prioColors = [
                                        'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                    ];
                                    $prioLabels = ['urgent' => 'Urgent', 'high' => 'Haute'];
                                ?>
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full <?php echo e($prioColors[$ticket->priority] ?? ''); ?>">
                                    <?php echo e($prioLabels[$ticket->priority] ?? $ticket->priority); ?>

                                </span>
                                <button onclick="claimTicket(<?php echo e($ticket->id); ?>)" class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-hand-paper mr-1"></i>Prendre
                                </button>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function claimTicket(chatId) {
    fetch(`/agent/ticket/${chatId}/claim`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(() => alert('Erreur réseau.'));
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.agent', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mes projets\vintApp\resources\views/agent/dashboard.blade.php ENDPATH**/ ?>