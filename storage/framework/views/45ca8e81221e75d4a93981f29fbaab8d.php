

<?php $__env->startSection('title', 'Gestion des Experts'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <!-- Total Experts -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] sm:text-xs font-semibold text-blue-600 uppercase tracking-wide">
                        Total Experts
                    </p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total_experts']); ?></p>
                </div>
                <div class="p-2 sm:p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-base sm:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Experts Actifs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] sm:text-xs font-semibold text-green-600 uppercase tracking-wide">
                        Experts Actifs
                    </p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['active_experts']); ?></p>
                </div>
                <div class="p-2 sm:p-3 bg-green-100 rounded-full">
                    <i class="fas fa-user-check text-green-600 text-base sm:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Vérifications Totales -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] sm:text-xs font-semibold text-primary-600 uppercase tracking-wide">
                        Vérifications
                    </p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total_verifications']); ?></p>
                </div>
                <div class="p-2 sm:p-3 bg-primary-100 rounded-full">
                    <i class="fas fa-certificate text-primary-600 text-base sm:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- En Attente -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] sm:text-xs font-semibold text-orange-600 uppercase tracking-wide">
                        En Attente
                    </p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['pending_verifications']); ?></p>
                </div>
                <div class="p-2 sm:p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-clock text-orange-600 text-base sm:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2 sm:gap-3">
                <i class="fas fa-user-graduate text-blue-600"></i>
                Gestion des Experts
            </h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mt-1">Gérer les experts en vérification d'authenticité</p>
        </div>
        <div class="flex gap-2 sm:gap-3">
            <a href="<?php echo e(route('admin.experts.candidates')); ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-colors flex items-center gap-2 flex-1 sm:flex-none justify-center">
                <i class="fas fa-user-plus"></i>
                <span class="hidden xs:inline">Désigner un</span> Expert
            </a>
            <button onclick="toggleStats()" 
                    class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-colors flex items-center gap-2 justify-center">
                <i class="fas fa-chart-bar"></i>
                <span class="hidden sm:inline">Statistiques</span>
            </button>
        </div>
    </div>

    <!-- Messages d'alerte -->
    <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 relative">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span class="text-green-800"><?php echo e(session('success')); ?></span>
            </div>
            <button type="button" class="absolute top-3 right-3 text-green-500 hover:text-green-700" 
                    onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 relative">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                <span class="text-red-800"><?php echo e(session('error')); ?></span>
            </div>
            <button type="button" class="absolute top-3 right-3 text-red-500 hover:text-red-700" 
                    onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if(session('warning')): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 relative">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-yellow-500 mr-3"></i>
                <span class="text-yellow-800"><?php echo e(session('warning')); ?></span>
            </div>
            <button type="button" class="absolute top-3 right-3 text-yellow-500 hover:text-yellow-700" 
                    onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- Filtres et recherche -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filtres de recherche</h3>
            <button onclick="toggleFilters()" 
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-200 transition-colors">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <div id="filtersPanel" class="hidden p-4 sm:p-6">
            <form method="GET" action="<?php echo e(route('admin.experts.index')); ?>">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Rechercher</label>
                        <input type="text" id="search" name="search" 
                               value="<?php echo e(request('search')); ?>" 
                               placeholder="Nom ou email..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Statut</label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous</option>
                            <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Actif</option>
                            <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactif</option>
                            <option value="suspended" <?php echo e(request('status') === 'suspended' ? 'selected' : ''); ?>>Suspendu</option>
                        </select>
                    </div>
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Spécialisation</label>
                        <select id="specialization" name="specialization" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes</option>
                            <option value="luxury" <?php echo e(request('specialization') === 'luxury' ? 'selected' : ''); ?>>Luxe</option>
                            <option value="sneakers" <?php echo e(request('specialization') === 'sneakers' ? 'selected' : ''); ?>>Sneakers</option>
                            <option value="watches" <?php echo e(request('specialization') === 'watches' ? 'selected' : ''); ?>>Montres</option>
                            <option value="handbags" <?php echo e(request('specialization') === 'handbags' ? 'selected' : ''); ?>>Sacs</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Trier par</label>
                        <select id="sort" name="sort" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="name" <?php echo e(request('sort') === 'name' ? 'selected' : ''); ?>>Nom</option>
                            <option value="created_at" <?php echo e(request('sort') === 'created_at' ? 'selected' : ''); ?>>Date d'ajout</option>
                            <option value="verifications_count" <?php echo e(request('sort') === 'verifications_count' ? 'selected' : ''); ?>>Nb vérifications</option>
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">&nbsp;</label>
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-colors flex items-center gap-2 flex-1 sm:flex-none justify-center">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                            <a href="<?php echo e(route('admin.experts.index')); ?>" 
                               class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-colors flex items-center gap-2 flex-1 sm:flex-none justify-center">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des experts -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-list text-blue-600"></i>
                Liste des Experts (<?php echo e($experts->total()); ?>)
            </h3>
        </div>
        <div class="p-3 sm:p-6">
            <?php if($experts->count() > 0): ?>
                <!-- Vue Table Desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Expert</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Spécialisations</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Niveau</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Vérifications</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Taux d'approbation</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Statut</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $experts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50 dark:bg-gray-900">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <?php if($expert->user): ?>
                                                <div class="w-10 h-10 flex-shrink-0">
                                                    <?php if($expert->user->avatar): ?>
                                                        <img src="<?php echo e($expert->user->avatar_url); ?>" 
                                                             class="w-10 h-10 rounded-full object-cover" 
                                                             alt="<?php echo e($expert->user->name); ?>"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="w-10 h-10 bg-blue-600 rounded-full hidden items-center justify-center">
                                                            <span class="text-white font-bold text-sm">
                                                                <?php echo e(strtoupper(substr($expert->user->name, 0, 1))); ?>

                                                            </span>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                                            <span class="text-white font-bold text-sm">
                                                                <?php echo e(strtoupper(substr($expert->user->name, 0, 1))); ?>

                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($expert->user->name); ?></div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($expert->user->email); ?></div>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-10 h-10 flex-shrink-0">
                                                    <div class="w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-bold text-sm">?</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900 dark:text-white">Utilisateur supprimé</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">N/A</div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <?php if($expert->specialties && count($expert->specialties) > 0): ?>
                                            <div class="flex flex-wrap gap-1">
                                                <?php $__currentLoopData = $expert->specialties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                        <?php echo e(ucfirst(str_replace('_', ' ', $specialty))); ?>

                                                    </span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-sm">Aucune spécialisation</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            <?php if($expert->certification_level === 'master'): ?> bg-green-100 text-green-800
                                            <?php elseif($expert->certification_level === 'senior'): ?> bg-blue-100 text-blue-800
                                            <?php else: ?> bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                            <?php endif; ?>">
                                            <?php echo e(ucfirst($expert->certification_level)); ?>

                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <div class="font-bold text-gray-900 dark:text-white"><?php echo e($expert->verification_count); ?></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">vérifications</div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <?php if($expert->approval_rate > 0): ?>
                                            <div class="font-bold text-green-600"><?php echo e(number_format($expert->approval_rate, 1)); ?>%</div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 mt-1">
                                                <div class="bg-green-600 h-1 rounded-full" 
                                                     style="width: <?php echo e($expert->approval_rate); ?>%"></div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   class="sr-only peer"
                                                   data-expert-id="<?php echo e($expert->id); ?>"
                                                   <?php echo e($expert->is_active ? 'checked' : ''); ?>

                                                   onchange="toggleExpertStatus(<?php echo e($expert->id); ?>)">
                                            <div class="relative w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 dark:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">
                                                <?php echo e($expert->is_active ? 'Actif' : 'Inactif'); ?>

                                            </span>
                                        </label>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="<?php echo e(route('admin.experts.show', $expert)); ?>" 
                                               class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.experts.edit', $expert)); ?>" 
                                               class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" 
                                                    title="Révoquer le statut d'expert"
                                                    onclick="revokeExpert(<?php echo e($expert->id); ?>, '<?php echo e($expert->user?->name ?? 'Utilisateur supprimé'); ?>')">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vue Cards Mobile -->
                <div class="lg:hidden space-y-3">
                    <?php $__currentLoopData = $experts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-3">
                            <!-- En-tête : Avatar + Nom + Statut -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <?php if($expert->user): ?>
                                        <div class="w-10 h-10 flex-shrink-0">
                                            <?php if($expert->user->avatar): ?>
                                                <img src="<?php echo e($expert->user->avatar_url); ?>" 
                                                     class="w-10 h-10 rounded-full object-cover" 
                                                     alt="<?php echo e($expert->user->name); ?>"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-10 h-10 bg-blue-600 rounded-full hidden items-center justify-center">
                                                    <span class="text-white font-bold text-sm"><?php echo e(strtoupper(substr($expert->user->name, 0, 1))); ?></span>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm"><?php echo e(strtoupper(substr($expert->user->name, 0, 1))); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-gray-900 dark:text-white truncate"><?php echo e($expert->user->name); ?></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo e($expert->user->email); ?></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-bold text-sm">?</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white">Utilisateur supprimé</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <label class="inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="checkbox" class="sr-only peer" data-expert-id="<?php echo e($expert->id); ?>"
                                           <?php echo e($expert->is_active ? 'checked' : ''); ?>

                                           onchange="toggleExpertStatus(<?php echo e($expert->id); ?>)">
                                    <div class="relative w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 dark:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Infos : Niveau + Spécialisations -->
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                    <?php if($expert->certification_level === 'master'): ?> bg-green-100 text-green-800
                                    <?php elseif($expert->certification_level === 'senior'): ?> bg-blue-100 text-blue-800
                                    <?php else: ?> bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100
                                    <?php endif; ?>">
                                    <?php echo e(ucfirst($expert->certification_level)); ?>

                                </span>
                                <?php if($expert->specialties && count($expert->specialties) > 0): ?>
                                    <?php $__currentLoopData = $expert->specialties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-full">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $specialty))); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>

                            <!-- Stats + Actions -->
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-4 text-sm">
                                    <div class="text-center">
                                        <span class="font-bold text-gray-900 dark:text-white"><?php echo e($expert->verification_count); ?></span>
                                        <span class="text-gray-500 dark:text-gray-400 text-xs ml-0.5">vérif.</span>
                                    </div>
                                    <?php if($expert->approval_rate > 0): ?>
                                        <div class="flex items-center gap-1">
                                            <span class="font-bold text-green-600"><?php echo e(number_format($expert->approval_rate, 0)); ?>%</span>
                                            <div class="w-12 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                <div class="bg-green-600 h-1.5 rounded-full" style="width: <?php echo e($expert->approval_rate); ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center gap-1">
                                    <a href="<?php echo e(route('admin.experts.show', $expert)); ?>" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.experts.edit', $expert)); ?>" 
                                       class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                            title="Révoquer"
                                            onclick="revokeExpert(<?php echo e($expert->id); ?>, '<?php echo e($expert->user?->name ?? 'Utilisateur supprimé'); ?>')">
                                        <i class="fas fa-user-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row justify-between items-center mt-4 sm:mt-6 gap-3 sm:gap-4">
                    <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 order-2 sm:order-1">
                        <?php echo e($experts->firstItem() ?? 0); ?>-<?php echo e($experts->lastItem() ?? 0); ?> sur <?php echo e($experts->total()); ?>

                    </div>
                    <div class="order-1 sm:order-2 w-full sm:w-auto overflow-x-auto">
                        <?php echo e($experts->links()); ?>

                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-8 sm:py-12 px-4">
                    <i class="fas fa-user-graduate text-gray-400 text-4xl sm:text-6xl mb-3 sm:mb-4"></i>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun expert désigné</h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6">Commencez par désigner des utilisateurs comme experts.</p>
                    <a href="<?php echo e(route('admin.experts.candidates')); ?>" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg text-sm sm:text-base font-medium transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-user-plus"></i>
                        Désigner un Expert
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript pour les interactions -->
<script>
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.classList.toggle('hidden');
}

function toggleStats() {
    // Logique pour afficher/masquer les statistiques
    alert('Statistiques détaillées - À implémenter');
}

function toggleExpertStatus(expertId) {
    fetch(`/admin/experts/${expertId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors du changement de statut');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du changement de statut');
    });
}

function revokeExpert(expertId, expertName) {
    if (confirm(`Êtes-vous sûr de vouloir révoquer le statut d'expert de ${expertName} ?`)) {
        // Afficher un indicateur de chargement
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        fetch(`/admin/experts/${expertId}/revoke`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La réponse n\'est pas au format JSON');
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                // Afficher message de succès
                if (data.message) {
                    alert(data.message);
                }
                // Recharger la page ou rediriger
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    location.reload();
                }
            } else {
                // Afficher le message d'erreur spécifique
                alert(data.message || 'Erreur lors de la révocation du statut');
                // Restaurer le bouton
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erreur détaillée:', error);
            alert(`Erreur lors de la révocation: ${error.message}`);
            // Restaurer le bouton
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mes projets\vintApp\resources\views/admin/experts/index.blade.php ENDPATH**/ ?>