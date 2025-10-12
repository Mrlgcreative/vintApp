

<?php $__env->startSection('page-title', 'Newsletter'); ?>

<?php $__env->startSection('content'); ?>
<!-- En-tête -->
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-gray-900">
        <i class="fas fa-envelope mr-3 text-primary-600"></i>
        Gestion de la Newsletter
    </h2>
    <div class="flex gap-3">
        <a href="<?php echo e(route('admin.settings.newsletter.send')); ?>" 
           class="inline-flex items-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-md transition-all hover:from-primary-700 hover:to-primary-800 hover:shadow-lg">
            <i class="fas fa-paper-plane mr-2"></i>
            Envoyer une Newsletter
        </a>
        <a href="<?php echo e(route('admin.settings.newsletter.export')); ?>" 
           class="inline-flex items-center rounded-lg bg-gradient-to-r from-green-600 to-green-700 px-4 py-2.5 text-sm font-medium text-white shadow-md transition-all hover:from-green-700 hover:to-green-800 hover:shadow-lg">
            <i class="fas fa-download mr-2"></i>
            Exporter CSV
        </a>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="mb-6 flex items-center rounded-xl bg-green-50 p-4 text-green-800 animate-fade-in">
        <i class="fas fa-check-circle mr-3 text-green-600"></i>
        <span class="flex-1"><?php echo e(session('success')); ?></span>
        <button type="button" class="text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="mb-6 flex items-center rounded-xl bg-red-50 p-4 text-red-800 animate-fade-in">
        <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
        <span class="flex-1"><?php echo e(session('error')); ?></span>
        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
<?php endif; ?>

<!-- Statistiques -->
<div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
    <!-- Total -->
    <div class="group overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900"><?php echo e($stats['total']); ?></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                <i class="fas fa-users text-xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Actifs -->
    <div class="group overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Actifs</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900"><?php echo e($stats['active']); ?></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                <i class="fas fa-user-check text-xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Vérifiés -->
    <div class="group overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Vérifiés</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900"><?php echo e($stats['verified']); ?></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-cyan-100">
                <i class="fas fa-envelope-circle-check text-xl text-cyan-600"></i>
            </div>
        </div>
    </div>

    <!-- Emails envoyés -->
    <div class="group overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Envoyés</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900"><?php echo e(number_format($stats['total_emails_sent'])); ?></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100">
                <i class="fas fa-paper-plane text-xl text-orange-600"></i>
            </div>
        </div>
    </div>

    <!-- Ouverts -->
    <div class="group overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Ouverts</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900"><?php echo e(number_format($stats['total_emails_opened'])); ?></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100">
                <i class="fas fa-envelope-open text-xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <!-- Clics -->
    <div class="group overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Clics</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900"><?php echo e(number_format($stats['total_clicks'])); ?></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                <i class="fas fa-mouse-pointer text-xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Liste des abonnés -->
<div class="overflow-hidden rounded-xl bg-white shadow-md">
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
        <h3 class="text-lg font-semibold text-white">
            <i class="fas fa-list mr-2"></i>
            Liste des abonnés (<?php echo e($subscribers->total()); ?>)
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Nom</th>
                            <th>Statut</th>
                            <th>Vérifié</th>
                            <th>Préférences</th>
                            <th>Statistiques</th>
                            <th>Inscription</th>
                            <th>Actions</th>
                        </tr>
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Vérifié</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Préférences</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Statistiques</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Inscription</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <?php $__empty_1 = true; $__currentLoopData = $subscribers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscriber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900"><?php echo e($subscriber->id); ?></td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>
                            <?php echo e($subscriber->email); ?>

                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700"><?php echo e($subscriber->name ?? '-'); ?></td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <?php if($subscriber->is_active): ?>
                                <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Actif</span>
                            <?php else: ?>
                                <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <?php if($subscriber->email_verified): ?>
                                <i class="fas fa-check-circle text-lg text-green-600"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle text-lg text-red-600"></i>
                            <?php endif; ?>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <div class="flex flex-wrap gap-1">
                                <?php if($subscriber->receive_new_items): ?>
                                    <span class="inline-flex rounded-full bg-cyan-100 px-2 py-1 text-xs font-medium text-cyan-800">Articles</span>
                                <?php endif; ?>
                                <?php if($subscriber->receive_promotions): ?>
                                    <span class="inline-flex rounded-full bg-orange-100 px-2 py-1 text-xs font-medium text-orange-800">Promos</span>
                                <?php endif; ?>
                                <?php if($subscriber->receive_newsletters): ?>
                                    <span class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">News</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-xs text-gray-600">
                            <div class="space-y-1">
                                <div><span class="font-medium">Envoyés:</span> <?php echo e($subscriber->emails_sent); ?></div>
                                <div><span class="font-medium">Ouverts:</span> <?php echo e($subscriber->emails_opened); ?></div>
                                <div><span class="font-medium">Clics:</span> <?php echo e($subscriber->emails_clicked); ?></div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                            <?php echo e($subscriber->created_at->format('d/m/Y')); ?>

                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <button class="toggle-subscriber inline-flex items-center rounded-lg bg-yellow-500 px-3 py-1.5 text-xs font-medium text-white transition-all hover:bg-yellow-600" 
                                        data-id="<?php echo e($subscriber->id); ?>"
                                        title="<?php echo e($subscriber->is_active ? 'Désactiver' : 'Activer'); ?>">
                                    <i class="fas fa-<?php echo e($subscriber->is_active ? 'toggle-on' : 'toggle-off'); ?>"></i>
                                </button>
                                <button class="delete-subscriber inline-flex items-center rounded-lg bg-red-500 px-3 py-1.5 text-xs font-medium text-white transition-all hover:bg-red-600" 
                                        data-id="<?php echo e($subscriber->id); ?>"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="py-12 text-center">
                            <i class="fas fa-inbox mb-4 text-5xl text-gray-300"></i>
                            <p class="text-gray-500">Aucun abonné pour le moment</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
        <?php echo e($subscribers->links()); ?>

    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle actif/inactif
    document.querySelectorAll('.toggle-subscriber').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            
            fetch(`/admin/settings/newsletter/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Erreur lors de la modification', 'error');
            });
        });
    });

    // Supprimer un abonné
    document.querySelectorAll('.delete-subscriber').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet abonné ?')) return;
            
            const id = this.dataset.id;
            
            fetch(`/admin/settings/newsletter/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Erreur lors de la suppression', 'error');
            });
        });
    });
});

function showNotification(message, type) {
    const bgColors = {
        success: 'bg-green-50 text-green-800',
        error: 'bg-red-50 text-red-800',
        warning: 'bg-yellow-50 text-yellow-800'
    };
    
    const icons = {
        success: 'fa-check-circle text-green-600',
        error: 'fa-exclamation-circle text-red-600',
        warning: 'fa-exclamation-triangle text-yellow-600'
    };
    
    const alert = document.createElement('div');
    alert.className = `fixed top-20 right-4 z-50 flex items-center rounded-xl px-6 py-4 shadow-lg ${bgColors[type]} animate-fade-in`;
    alert.style.minWidth = '300px';
    alert.innerHTML = `
        <i class="fas ${icons[type]} mr-3"></i>
        <span class="flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-4 ${type === 'success' ? 'text-green-600 hover:text-green-800' : type === 'error' ? 'text-red-600 hover:text-red-800' : 'text-yellow-600 hover:text-yellow-800'}">
            <i class="fas fa-times"></i>
        </button>
    `;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 5000);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/newsletter/subscribers.blade.php ENDPATH**/ ?>