

<?php $__env->startSection('title', 'Logs système'); ?>

<?php $__env->startSection('content'); ?>
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Logs système</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-1">Consultez et gérez les logs de l'application</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <button onclick="clearLogs()" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors shadow-sm">
            <i class="fas fa-broom mr-2"></i>
            <span>Vider les logs</span>
        </button>
        <button onclick="downloadLogs()" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm">
            <i class="fas fa-download mr-2"></i>
            <span>Télécharger</span>
        </button>
    </div>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Erreurs aujourd'hui</p>
                <p class="text-3xl font-bold text-red-600 mt-2"><?php echo e($stats['error'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Avertissements</p>
                <p class="text-3xl font-bold text-yellow-600 mt-2"><?php echo e($stats['warning'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Informations</p>
                <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo e($stats['info'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Taille du fichier</p>
                <p class="text-3xl font-bold text-primary-600 mt-2"><?php echo e(number_format(($fileSize ?? 0) / 1024, 0)); ?> KB</p>
            </div>
            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-alt text-primary-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filtres</h2>
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Niveau -->
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    <i class="fas fa-layer-group text-gray-400 mr-1"></i>
                    Niveau
                </label>
                <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors bg-white dark:bg-gray-800" 
                        id="level" 
                        name="level">
                    <option value="">Tous les niveaux</option>
                    <option value="emergency" <?php echo e(request('level') === 'emergency' ? 'selected' : ''); ?>>🚨 Emergency</option>
                    <option value="alert" <?php echo e(request('level') === 'alert' ? 'selected' : ''); ?>>🔴 Alert</option>
                    <option value="critical" <?php echo e(request('level') === 'critical' ? 'selected' : ''); ?>>❌ Critical</option>
                    <option value="error" <?php echo e(request('level') === 'error' ? 'selected' : ''); ?>>❗ Error</option>
                    <option value="warning" <?php echo e(request('level') === 'warning' ? 'selected' : ''); ?>>⚠️ Warning</option>
                    <option value="notice" <?php echo e(request('level') === 'notice' ? 'selected' : ''); ?>>📢 Notice</option>
                    <option value="info" <?php echo e(request('level') === 'info' ? 'selected' : ''); ?>>ℹ️ Info</option>
                    <option value="debug" <?php echo e(request('level') === 'debug' ? 'selected' : ''); ?>>🐛 Debug</option>
                </select>
            </div>
            
            <!-- Date -->
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    <i class="fas fa-calendar text-gray-400 mr-1"></i>
                    Date
                </label>
                <input type="date" 
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" 
                       id="date" 
                       name="date" 
                       value="<?php echo e(request('date')); ?>">
            </div>
            
            <!-- Recherche -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    <i class="fas fa-search text-gray-400 mr-1"></i>
                    Recherche
                </label>
                <input type="text" 
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" 
                       id="search" 
                       name="search" 
                       placeholder="Rechercher..." 
                       value="<?php echo e(request('search')); ?>">
            </div>
            
            <!-- Bouton -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">&nbsp;</label>
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Logs - Vue Desktop -->
<div class="hidden lg:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-900">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            <i class="fas fa-list mr-2 text-gray-600 dark:text-gray-300"></i>
            Entrées des logs
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Niveau
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Message
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Contexte
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Date/Heure
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 dark:bg-gray-900 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                                $levelLower = strtolower($log['level']);
                                $badgeClasses = match($levelLower) {
                                    'emergency', 'alert', 'critical', 'error' => 'bg-red-100 text-red-800',
                                    'warning' => 'bg-yellow-100 text-yellow-800',
                                    'notice', 'info' => 'bg-blue-100 text-blue-800',
                                    'debug' => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100',
                                    default => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100',
                                };
                                $icon = match($levelLower) {
                                    'emergency', 'alert', 'critical', 'error' => 'fa-times-circle',
                                    'warning' => 'fa-exclamation-triangle',
                                    'notice', 'info' => 'fa-info-circle',
                                    'debug' => 'fa-bug',
                                    default => 'fa-circle',
                                };
                            ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($badgeClasses); ?>">
                                <i class="fas <?php echo e($icon); ?> mr-1"></i>
                                <?php echo e(strtoupper($log['level'])); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-white max-w-md">
                                <?php echo e(\Illuminate\Support\Str::limit($log['message'], 100)); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                <div><span class="font-medium">Env:</span> <?php echo e($log['env']); ?></div>
                                <?php if(!empty(trim($log['context']))): ?>
                                    <div class="text-xs max-w-xs overflow-hidden">
                                        <span class="font-medium">Context:</span>
                                        <?php echo e(\Illuminate\Support\Str::limit($log['context'], 50)); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white"><?php echo e(\Carbon\Carbon::parse($log['datetime'])->format('d/m/Y H:i:s')); ?></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo e(\Carbon\Carbon::parse($log['datetime'])->diffForHumans()); ?></div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-lg font-medium">Aucun log trouvé</p>
                                <p class="text-sm">Les logs s'afficheront ici lorsqu'ils seront générés</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Logs - Vue Mobile (Cards) -->
<div class="lg:hidden space-y-4">
    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $levelLower = strtolower($log['level']);
            $bgClass = match($levelLower) {
                'emergency', 'alert', 'critical', 'error' => 'bg-red-50 border-red-100',
                'warning' => 'bg-yellow-50 border-yellow-100',
                'notice', 'info' => 'bg-blue-50 border-blue-100',
                'debug' => 'bg-gray-50 dark:bg-gray-900 border-gray-100',
                default => 'bg-gray-50 dark:bg-gray-900 border-gray-100',
            };
            $badgeClass = match($levelLower) {
                'emergency', 'alert', 'critical', 'error' => 'bg-red-100 text-red-800',
                'warning' => 'bg-yellow-100 text-yellow-800',
                'notice', 'info' => 'bg-blue-100 text-blue-800',
                'debug' => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100',
                default => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100',
            };
            $icon = match($levelLower) {
                'emergency', 'alert', 'critical', 'error' => 'fa-times-circle',
                'warning' => 'fa-exclamation-triangle',
                'notice', 'info' => 'fa-info-circle',
                'debug' => 'fa-bug',
                default => 'fa-circle',
            };
        ?>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="<?php echo e($bgClass); ?> px-4 py-3 border-b">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($badgeClass); ?>">
                        <i class="fas <?php echo e($icon); ?> mr-1"></i>
                        <?php echo e(strtoupper($log['level'])); ?>

                    </span>
                    <span class="text-xs text-gray-600 dark:text-gray-300"><?php echo e(\Carbon\Carbon::parse($log['datetime'])->diffForHumans()); ?></span>
                </div>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Message:</p>
                    <p class="text-sm text-gray-900 dark:text-white"><?php echo e($log['message']); ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Contexte:</p>
                    <div class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                        <div><span class="font-medium">Env:</span> <?php echo e($log['env']); ?></div>
                        <?php if(!empty(trim($log['context']))): ?>
                            <div class="text-xs bg-gray-50 dark:bg-gray-900 p-2 rounded max-h-24 overflow-auto">
                                <pre class="whitespace-pre-wrap text-xs"><?php echo e(\Illuminate\Support\Str::limit($log['context'], 200)); ?></pre>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="pt-2 border-t border-gray-100">
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e(\Carbon\Carbon::parse($log['datetime'])->format('d/m/Y H:i:s')); ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12">
            <div class="flex flex-col items-center justify-center text-gray-400">
                <i class="fas fa-inbox text-5xl mb-4"></i>
                <p class="text-lg font-medium text-center">Aucun log trouvé</p>
                <p class="text-sm text-center mt-2">Les logs s'afficheront ici lorsqu'ils seront générés</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function clearLogs() {
    if (confirm('Êtes-vous sûr de vouloir vider tous les logs ? Cette action est irréversible.')) {
        // AJAX call to clear logs
        fetch('/admin/logs/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Logs vidés avec succès !');
            location.reload();
        })
        .catch(error => {
            alert('Erreur lors de la suppression des logs');
            console.error(error);
        });
    }
}

function downloadLogs() {
    window.location.href = '/admin/logs/download';
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/logs/index.blade.php ENDPATH**/ ?>