

<?php $__env->startSection('title', 'Statistiques Support'); ?>

<?php $__env->startSection('content'); ?>
<div>
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Statistiques Support</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Analyse des performances du support client</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.support.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour
            </a>
        </div>
    </div>

    <!-- Filtre par période -->
    <div class="mb-6">
        <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-1 shadow-sm">
            <?php $__currentLoopData = [7 => '7 jours', 14 => '14 jours', 30 => '30 jours', 60 => '60 jours', 90 => '90 jours']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.support.stats', ['period' => $value])); ?>"
                   class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-md transition-colors <?php echo e((int)$period === $value ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Vue d'ensemble -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-6">
        <!-- Total -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-blue-500 p-3 sm:p-4">
            <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Total Chats</p>
            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['overview']['total_chats']); ?></h3>
        </div>
        <!-- Ouverts -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-red-500 p-3 sm:p-4">
            <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Ouverts</p>
            <h3 class="text-xl sm:text-2xl font-bold text-red-600 dark:text-red-400"><?php echo e($stats['overview']['open_chats']); ?></h3>
        </div>
        <!-- En cours -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-yellow-500 p-3 sm:p-4">
            <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">En Cours</p>
            <h3 class="text-xl sm:text-2xl font-bold text-yellow-600 dark:text-yellow-400"><?php echo e($stats['overview']['in_progress_chats']); ?></h3>
        </div>
        <!-- Fermés -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-green-500 p-3 sm:p-4">
            <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Fermés</p>
            <h3 class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400"><?php echo e($stats['overview']['closed_chats']); ?></h3>
        </div>
        <!-- Temps réponse moyen -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-purple-500 p-3 sm:p-4">
            <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Rép. Moy.</p>
            <h3 class="text-xl sm:text-2xl font-bold text-purple-600 dark:text-purple-400"><?php echo e($stats['overview']['avg_response_time']); ?><span class="text-xs sm:text-sm font-normal ml-1">min</span></h3>
        </div>
        <!-- Temps résolution moyen -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-indigo-500 p-3 sm:p-4">
            <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Résol. Moy.</p>
            <h3 class="text-xl sm:text-2xl font-bold text-indigo-600 dark:text-indigo-400"><?php echo e($stats['overview']['avg_resolution_time']); ?><span class="text-xs sm:text-sm font-normal ml-1">h</span></h3>
        </div>
    </div>

    <!-- Graphique activité quotidienne -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Activité Quotidienne
            </h2>
        </div>
        <div class="p-4 sm:p-6">
            <canvas id="dailyChart" height="300"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Par catégorie -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Par Catégorie
                </h2>
            </div>
            <div class="p-4 sm:p-6">
                <?php if($stats['by_category']->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $stats['by_category']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $total = $stats['by_category']->sum('count');
                                $percent = $total > 0 ? round(($cat->count / $total) * 100) : 0;
                            ?>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize"><?php echo e($cat->category ?? 'Non classé'); ?></span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($cat->count); ?> <span class="text-xs text-gray-400">(<?php echo e($percent); ?>%)</span></span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: <?php echo e($percent); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">Aucune donnée pour cette période</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Par priorité -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Par Priorité
                </h2>
            </div>
            <div class="p-4 sm:p-6">
                <?php if($stats['by_priority']->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $stats['by_priority']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $total = $stats['by_priority']->sum('count');
                                $percent = $total > 0 ? round(($prio->count / $total) * 100) : 0;
                                $colors = [
                                    'low' => 'bg-blue-500',
                                    'medium' => 'bg-yellow-500',
                                    'high' => 'bg-orange-500',
                                    'urgent' => 'bg-red-500',
                                ];
                                $labels = [
                                    'low' => 'Basse',
                                    'medium' => 'Moyenne',
                                    'high' => 'Haute',
                                    'urgent' => 'Urgente',
                                ];
                                $color = $colors[$prio->priority] ?? 'bg-gray-500';
                                $label = $labels[$prio->priority] ?? ucfirst($prio->priority ?? 'Non définie');
                            ?>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e($label); ?></span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($prio->count); ?> <span class="text-xs text-gray-400">(<?php echo e($percent); ?>%)</span></span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="<?php echo e($color); ?> h-2.5 rounded-full transition-all duration-500" style="width: <?php echo e($percent); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">Aucune donnée pour cette période</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Performance des admins -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Performance des Agents
            </h2>
        </div>
        <div class="p-4 sm:p-6">
            <?php if($stats['admin_performance']->count() > 0): ?>
                <!-- Mobile: cards -->
                <div class="space-y-3 lg:hidden">
                    <?php $__currentLoopData = $stats['admin_performance']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $rate = $admin->total_assigned > 0 ? round(($admin->closed_chats / $admin->total_assigned) * 100) : 0; ?>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold text-xs">
                                        <?php echo e(strtoupper(substr($admin->name, 0, 2))); ?>

                                    </div>
                                    <span class="font-medium text-sm text-gray-900 dark:text-white"><?php echo e($admin->name); ?></span>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($rate >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : ($rate >= 50 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400')); ?>">
                                    <?php echo e($rate); ?>%
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Assignés</span>
                                    <span class="ml-1 font-semibold text-gray-900 dark:text-white"><?php echo e($admin->total_assigned); ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Résolus</span>
                                    <span class="ml-1 font-semibold text-gray-900 dark:text-white"><?php echo e($admin->closed_chats); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Desktop: table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Agent</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Chats Assignés</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Chats Résolus</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Taux de Résolution</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php $__currentLoopData = $stats['admin_performance']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $rate = $admin->total_assigned > 0 ? round(($admin->closed_chats / $admin->total_assigned) * 100) : 0; ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold text-sm">
                                                <?php echo e(strtoupper(substr($admin->name, 0, 2))); ?>

                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white text-sm"><?php echo e($admin->name); ?></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($admin->email); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white"><?php echo e($admin->total_assigned); ?></td>
                                    <td class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white"><?php echo e($admin->closed_chats); ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="<?php echo e($rate >= 80 ? 'bg-green-500' : ($rate >= 50 ? 'bg-yellow-500' : 'bg-red-500')); ?> h-2 rounded-full transition-all duration-500" style="width: <?php echo e($rate); ?>%"></div>
                                            </div>
                                            <span class="text-sm font-semibold <?php echo e($rate >= 80 ? 'text-green-600 dark:text-green-400' : ($rate >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400')); ?>"><?php echo e($rate); ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">Aucun agent trouvé</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyStats = <?php echo json_encode($stats['daily_stats'], 15, 512) ?>;
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#9ca3af' : '#6b7280';
    const gridColor = isDark ? 'rgba(75,85,99,0.3)' : 'rgba(209,213,219,0.5)';

    const labels = dailyStats.map(s => {
        const d = new Date(s.date);
        return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
    });

    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Nouveaux chats',
                    data: dailyStats.map(s => s.new_chats),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                },
                {
                    label: 'Chats fermés',
                    data: dailyStats.map(s => s.closed_chats),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                },
                {
                    label: 'Messages',
                    data: dailyStats.map(s => s.messages),
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    labels: { color: textColor, usePointStyle: true, padding: 16, font: { size: 12 } }
                },
                tooltip: {
                    backgroundColor: isDark ? '#1f2937' : '#fff',
                    titleColor: isDark ? '#f9fafb' : '#111827',
                    bodyColor: isDark ? '#d1d5db' : '#4b5563',
                    borderColor: isDark ? '#374151' : '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    ticks: { color: textColor, maxRotation: 45, font: { size: 10 } },
                    grid: { color: gridColor }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor, stepSize: 1, font: { size: 11 } },
                    grid: { color: gridColor }
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.support', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mes projets\vintApp\resources\views/admin/support/stats.blade.php ENDPATH**/ ?>