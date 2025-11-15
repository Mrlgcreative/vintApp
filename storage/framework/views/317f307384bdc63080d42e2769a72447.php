

<?php $__env->startSection('title', 'Gestion des utilisateurs'); ?>
<?php $__env->startSection('page-title', 'Gestion des utilisateurs'); ?>

<?php $__env->startSection('page-actions'); ?>
<div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
    <a href="<?php echo e(route('admin.users.create')); ?>" 
       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
        <i class="fas fa-plus mr-2"></i>
        <span class="hidden sm:inline">Nouvel utilisateur</span>
        <span class="sm:hidden">Nouveau</span>
    </a>
    <button class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200" 
            onclick="toggleBulkActions()">
        <i class="fas fa-tasks mr-2"></i>
        <span class="hidden sm:inline">Actions groupées</span>
        <span class="sm:hidden">Actions</span>
    </button>
    <a href="<?php echo e(route('admin.users.index', ['export' => 'csv'])); ?>" 
       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <i class="fas fa-download mr-2"></i>
        <span class="hidden sm:inline">Exporter CSV</span>
        <span class="sm:hidden">Export</span>
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Filtres -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-4 sm:mb-6">
    <div class="p-4 sm:p-6">
        <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="space-y-4">
            <!-- Ligne 1: Recherche -->
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Rechercher</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm sm:text-base" 
                           id="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nom ou email...">
                </div>
            </div>
            
            <!-- Ligne 2: Filtres -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Rôle</label>
                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm sm:text-base" 
                            id="role" name="role">
                        <option value="">Tous les rôles</option>
                        <option value="admin" <?php echo e(request('role') === 'admin' ? 'selected' : ''); ?>>Admin</option>
                        <option value="user" <?php echo e(request('role') === 'user' ? 'selected' : ''); ?>>Utilisateur</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Statut</label>
                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm sm:text-base" 
                            id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Actif</option>
                        <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactif</option>
                    </select>
                </div>
                
                <!-- Boutons -->
                <div class="flex items-end">
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full">
                        <button type="submit" 
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        <a href="<?php echo e(route('admin.users.index')); ?>" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tableau des utilisateurs -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
            Utilisateurs (<?php echo e($users->total()); ?>)
        </h3>
    </div>
    <div>
        <?php if($users->count() > 0): ?>
            <!-- Vue Desktop (Table) - Cachée sur mobile et tablet -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rôles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Wallets</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dernière connexion</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if($user->avatar): ?>
                                            <img src="<?php echo e($user->avatar_url); ?>" class="w-10 h-10 rounded-full mr-4" alt="Avatar">
                                        <?php else: ?>
                                            <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-4">
                                                <?php echo e($user->initial); ?>

                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($user->name); ?></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">ID: <?php echo e($user->id); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-900 dark:text-white"><?php echo e($user->email); ?></span>
                                        <?php if($user->email_verified_at): ?>
                                            <i class="fas fa-check-circle text-green-500 ml-2" title="Email vérifié"></i>
                                        <?php else: ?>
                                            <i class="fas fa-exclamation-circle text-yellow-500 ml-2" title="Email non vérifié"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($role->slug === 'admin' ? 'bg-red-100 text-red-800' : 'bg-primary-100 text-primary-800'); ?>">
                                                <?php echo e($role->name); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <div class="space-y-1">
                                        <?php if($user->usdWallet()): ?>
                                            <div>USD: <?php echo e(number_format($user->usdWallet()->balance, 2)); ?></div>
                                        <?php endif; ?>
                                        <?php if($user->cdfWallet()): ?>
                                            <div>CDF: <?php echo e(number_format($user->cdfWallet()->balance, 0)); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?php if($user->last_seen): ?>
                                        <div class="text-sm text-gray-900 dark:text-white"><?php echo e($user->last_seen->diffForHumans()); ?></div>
                                        <?php if($user->isOnline()): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                En ligne
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-500 dark:text-gray-400">Jamais connecté</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        <?php if($user->is_active ?? true): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Actif
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Inactif
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if($user->is_suspended ?? false): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Suspendu
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="relative">
                                        <button class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500" 
                                                type="button" onclick="toggleDropdown('user-desktop-<?php echo e($user->id); ?>-dropdown')">
                                            Actions
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 hidden z-10" 
                                             id="user-desktop-<?php echo e($user->id); ?>-dropdown">
                                            <div class="py-1">
                                                <a href="<?php echo e(route('admin.users.show', $user)); ?>" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                                                    <i class="fas fa-eye mr-3 w-4"></i>Voir détails
                                                </a>
                                                <a href="<?php echo e(route('admin.users.edit', $user)); ?>" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                                                    <i class="fas fa-edit mr-3 w-4"></i>Modifier
                                                </a>
                                                <div class="border-t border-gray-100"></div>
                                                <?php if($user->is_active ?? true): ?>
                                                    <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <input type="hidden" name="action" value="deactivate">
                                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50" 
                                                                onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?')">
                                                            <i class="fas fa-pause mr-3 w-4"></i>Désactiver
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <input type="hidden" name="action" value="activate">
                                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                                            <i class="fas fa-play mr-3 w-4"></i>Activer
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if(!($user->is_suspended ?? false)): ?>
                                                    <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <input type="hidden" name="action" value="suspend">
                                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50" 
                                                                onclick="return confirm('Êtes-vous sûr de vouloir suspendre cet utilisateur ?')">
                                                            <i class="fas fa-ban mr-3 w-4"></i>Suspendre
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <div class="border-t border-gray-100"></div>
                                                <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                                                        <i class="fas fa-trash mr-3 w-4"></i>Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Vue Mobile/Tablet (Cards) - Cachée sur desktop -->
            <div class="lg:hidden divide-y divide-gray-200">
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-150">
                        <!-- En-tête utilisateur -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <?php if($user->avatar): ?>
                                    <img src="<?php echo e($user->avatar_url); ?>" class="w-12 h-12 rounded-full flex-shrink-0" alt="Avatar">
                                <?php else: ?>
                                    <div class="w-12 h-12 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                        <?php echo e($user->initial); ?>

                                    </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate"><?php echo e($user->name); ?></h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo e($user->email); ?></p>
                                    <p class="text-xs text-gray-400">ID: <?php echo e($user->id); ?></p>
                                </div>
                            </div>
                            
                            <!-- Menu actions mobile -->
                            <div class="relative ml-2 flex-shrink-0">
                                <button onclick="toggleMobileDropdown(<?php echo e($user->id); ?>)" 
                                        class="p-2 rounded-lg hover:bg-gray-100 dark:bg-gray-800 transition-colors">
                                    <i class="fas fa-ellipsis-v text-gray-500 dark:text-gray-400"></i>
                                </button>
                                <div id="mobile-dropdown-<?php echo e($user->id); ?>" 
                                     class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20">
                                    <div class="py-1">
                                        <a href="<?php echo e(route('admin.users.show', $user)); ?>" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                                            <i class="fas fa-eye mr-3 w-4"></i>Voir détails
                                        </a>
                                        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                                            <i class="fas fa-edit mr-3 w-4"></i>Modifier
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <?php if($user->is_active ?? true): ?>
                                            <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <input type="hidden" name="action" value="deactivate">
                                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50" 
                                                        onclick="return confirm('Désactiver cet utilisateur ?')">
                                                    <i class="fas fa-pause mr-3 w-4"></i>Désactiver
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <input type="hidden" name="action" value="activate">
                                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                                    <i class="fas fa-play mr-3 w-4"></i>Activer
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if(!($user->is_suspended ?? false)): ?>
                                            <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <input type="hidden" name="action" value="suspend">
                                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50" 
                                                        onclick="return confirm('Suspendre cet utilisateur ?')">
                                                    <i class="fas fa-ban mr-3 w-4"></i>Suspendre
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <div class="border-t border-gray-100"></div>
                                        <form action="<?php echo e(route('admin.users.update-status', $user)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50" 
                                                    onclick="return confirm('Supprimer cet utilisateur ? Action irréversible.')">
                                                <i class="fas fa-trash mr-3 w-4"></i>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Badges et informations -->
                        <div class="space-y-2">
                            <!-- Rôles -->
                            <div class="flex flex-wrap gap-1">
                                <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($role->slug === 'admin' ? 'bg-red-100 text-red-800' : 'bg-primary-100 text-primary-800'); ?>">
                                        <?php echo e($role->name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                
                                <?php if($user->is_active ?? true): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Actif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactif
                                    </span>
                                <?php endif; ?>
                                
                                <?php if($user->is_suspended ?? false): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Suspendu
                                    </span>
                                <?php endif; ?>
                                
                                <?php if($user->isOnline()): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1 animate-pulse"></span>
                                        En ligne
                                    </span>
                                <?php endif; ?>
                                
                                <?php if($user->email_verified_at): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-check-circle mr-1"></i>Email vérifié
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Wallets -->
                            <?php if($user->usdWallet() || $user->cdfWallet()): ?>
                                <div class="flex gap-3 text-xs">
                                    <?php if($user->usdWallet()): ?>
                                        <div class="flex items-center">
                                            <i class="fas fa-wallet text-green-600 mr-1"></i>
                                            <span class="font-medium">USD:</span>
                                            <span class="ml-1"><?php echo e(number_format($user->usdWallet()->balance, 2)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($user->cdfWallet()): ?>
                                        <div class="flex items-center">
                                            <i class="fas fa-wallet text-blue-600 mr-1"></i>
                                            <span class="font-medium">CDF:</span>
                                            <span class="ml-1"><?php echo e(number_format($user->cdfWallet()->balance, 0)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Dernière connexion -->
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <?php if($user->last_seen): ?>
                                    <i class="fas fa-clock mr-1"></i>
                                    <?php echo e($user->last_seen->diffForHumans()); ?>

                                <?php else: ?>
                                    <i class="fas fa-clock mr-1"></i>
                                    Jamais connecté
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun utilisateur trouvé</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucun utilisateur ne correspond aux critères de recherche.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if($users->hasPages()): ?>
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 bg-gray-50 dark:bg-gray-900">
            <?php echo e($users->appends(request()->query())->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on select change
    document.querySelectorAll('select[name="role"], select[name="status"]').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});

// Dropdown toggle function pour desktop
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id$="-dropdown"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(el => {
        if (el.id !== dropdownId) {
            el.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Toggle dropdown mobile pour les actions
function toggleMobileDropdown(userId) {
    const dropdownId = 'mobile-dropdown-' + userId;
    const dropdown = document.getElementById(dropdownId);
    const allMobileDropdowns = document.querySelectorAll('[id^="mobile-dropdown-"]');
    
    // Fermer tous les autres dropdowns mobiles
    allMobileDropdowns.forEach(el => {
        if (el.id !== dropdownId) {
            el.classList.add('hidden');
        }
    });
    
    // Toggle le dropdown actuel
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleDropdown"]') && 
        !event.target.closest('[onclick*="toggleMobileDropdown"]')) {
        document.querySelectorAll('[id$="-dropdown"], [id^="mobile-dropdown-"]').forEach(el => {
            el.classList.add('hidden');
        });
    }
});

// Bulk actions toggle
function toggleBulkActions() {
    // Implementation for bulk actions modal/panel
    alert('Fonctionnalité des actions groupées à implémenter');
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/users/index.blade.php ENDPATH**/ ?>