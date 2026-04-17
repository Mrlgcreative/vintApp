

<?php $__env->startSection('title', 'Tickets non assignés'); ?>

<?php $__env->startSection('content'); ?>
<div>
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Tickets non assignés</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Prenez en charge les tickets en attente</p>
        </div>
    </div>

    
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="<?php echo e(route('agent.unassigned')); ?>" class="flex flex-wrap gap-3">
            <select name="priority" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Toutes priorités</option>
                <option value="urgent" <?php echo e(request('priority') === 'urgent' ? 'selected' : ''); ?>>Urgent</option>
                <option value="high" <?php echo e(request('priority') === 'high' ? 'selected' : ''); ?>>Haute</option>
                <option value="normal" <?php echo e(request('priority') === 'normal' ? 'selected' : ''); ?>>Normale</option>
                <option value="low" <?php echo e(request('priority') === 'low' ? 'selected' : ''); ?>>Basse</option>
            </select>
            <select name="category" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Toutes catégories</option>
                <option value="technical" <?php echo e(request('category') === 'technical' ? 'selected' : ''); ?>>Technique</option>
                <option value="account" <?php echo e(request('category') === 'account' ? 'selected' : ''); ?>>Compte</option>
                <option value="payment" <?php echo e(request('category') === 'payment' ? 'selected' : ''); ?>>Paiement</option>
                <option value="order" <?php echo e(request('category') === 'order' ? 'selected' : ''); ?>>Commande</option>
                <option value="general" <?php echo e(request('category') === 'general' ? 'selected' : ''); ?>>Général</option>
            </select>
            <button type="submit" class="px-3 py-2 text-sm bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-filter mr-1"></i>Filtrer
            </button>
            <a href="<?php echo e(route('agent.unassigned')); ?>" class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                <i class="fas fa-times mr-1"></i>Reset
            </a>
        </form>
    </div>

    <?php if($chats->isEmpty()): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
            <div class="w-16 h-16 mx-auto bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-check text-2xl text-green-500"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Tout est pris en charge !</h3>
            <p class="text-gray-500 dark:text-gray-400">Il n'y a aucun ticket non assigné pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php $__currentLoopData = $chats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $prioConfig = [
                        'urgent' => ['color' => 'border-red-500 bg-red-50 dark:bg-red-900/10', 'badge' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'icon' => 'fa-exclamation-triangle text-red-500'],
                        'high' => ['color' => 'border-orange-500 bg-orange-50 dark:bg-orange-900/10', 'badge' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400', 'icon' => 'fa-arrow-up text-orange-500'],
                        'normal' => ['color' => 'border-blue-500 bg-white dark:bg-gray-800', 'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'icon' => 'fa-minus text-blue-500'],
                        'low' => ['color' => 'border-gray-300 bg-white dark:bg-gray-800', 'badge' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'icon' => 'fa-arrow-down text-gray-400'],
                    ];
                    $cfg = $prioConfig[$chat->priority] ?? $prioConfig['normal'];
                    $catLabels = ['technical' => 'Technique', 'account' => 'Compte', 'payment' => 'Paiement', 'order' => 'Commande', 'general' => 'Général'];
                ?>
                <div class="rounded-xl shadow-sm border-l-4 <?php echo e($cfg['color']); ?> p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas <?php echo e($cfg['icon']); ?> text-xs"></i>
                                <span class="text-xs font-mono text-gray-500 dark:text-gray-400"><?php echo e($chat->reference); ?></span>
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full <?php echo e($cfg['badge']); ?>"><?php echo e(ucfirst($chat->priority)); ?></span>
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400"><?php echo e($catLabels[$chat->category] ?? $chat->category); ?></span>
                            </div>
                            <h3 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white"><?php echo e($chat->subject); ?></h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                <i class="fas fa-user mr-1"></i><?php echo e($chat->user->name ?? '?'); ?> · 
                                <i class="fas fa-clock mr-1"></i><?php echo e($chat->created_at->diffForHumans()); ?>

                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="<?php echo e(route('agent.show', $chat)); ?>" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                <i class="fas fa-eye mr-1.5"></i>Voir
                            </a>
                            <button onclick="claimTicket(<?php echo e($chat->id); ?>)" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-hand-paper mr-1.5"></i>Prendre en charge
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-4">
            <?php echo e($chats->links()); ?>

        </div>
    <?php endif; ?>
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

<?php echo $__env->make('layouts.agent', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mes projets\vintApp\resources\views/agent/unassigned.blade.php ENDPATH**/ ?>