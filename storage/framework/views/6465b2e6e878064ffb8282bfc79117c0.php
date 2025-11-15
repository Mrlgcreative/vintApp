

<?php $__env->startSection('title', 'Mon Profil - ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Profile Header -->
        <div class="relative mb-8 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 opacity-90"></div>
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            
            <div class="relative px-6 py-12 sm:px-8 sm:py-16">
                <div class="flex flex-col lg:flex-row items-center gap-8">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <?php
                            $avatarUrl = null;
                            if ($user->avatar) {
                                if (filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                                    $avatarUrl = $user->avatar;
                                } else {
                                    $avatarUrl = Storage::url($user->avatar);
                                }
                            }
                        ?>
                        
                        <?php if($avatarUrl): ?>
                            <img src="<?php echo e($avatarUrl); ?>" 
                                 alt="<?php echo e($user->name); ?>" 
                                 class="w-32 h-32 sm:w-40 sm:h-40 rounded-full object-cover ring-8 ring-white/30 shadow-2xl backdrop-blur-sm"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-gradient-to-br from-yellow-400 via-pink-500 to-purple-600 flex items-center justify-center text-white text-5xl font-black ring-8 ring-white/30 shadow-2xl hidden">
                                <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

                            </div>
                        <?php else: ?>
                            <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-gradient-to-br from-yellow-400 via-pink-500 to-purple-600 flex items-center justify-center text-white text-5xl font-black ring-8 ring-white/30 shadow-2xl">
                                <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- User Info -->
                    <div class="flex-1 text-center lg:text-left">
                        <h1 class="text-4xl sm:text-5xl font-black text-white mb-4 tracking-tight">
                            <?php echo e($user->name); ?>

                        </h1>
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 text-white/90">
                            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                                <span class="font-medium"><?php echo e($user->email); ?></span>
                            </div>
                            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">Membre depuis <?php echo e($user->created_at->translatedFormat('F Y')); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex-shrink-0 flex gap-3">
                        <a href="<?php echo e(route('profile.edit')); ?>" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-900 rounded-xl hover:bg-gray-50 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <!-- Stats Grid with Currency Support -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Items -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-bl-full transform translate-x-8 -translate-y-8"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-4xl font-black text-gray-900 dark:text-white"><?php echo e($stats['total_items']); ?></p>
                        </div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Articles Publiés</h3>
                    <div class="mt-3 flex items-center gap-2 text-sm">
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 font-medium">
                            <?php echo e($stats['active_items']); ?> actifs
                        </span>
                        <span class="text-gray-400">•</span>
                        <span class="text-gray-500 dark:text-gray-400"><?php echo e($stats['sold_items']); ?> vendus</span>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-500/10 to-pink-500/10 rounded-bl-full transform translate-x-8 -translate-y-8"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-4xl font-black text-gray-900 dark:text-white"><?php echo e($stats['total_orders']); ?></p>
                        </div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Commandes</h3>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 font-medium text-sm">
                            <?php echo e($stats['completed_orders']); ?> complétées
                        </span>
                    </div>
                </div>
            </div>

            <!-- Total Revenue with Multi-Currency -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 rounded-bl-full transform translate-x-8 -translate-y-8"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-black text-gray-900 dark:text-white">
                                <?php echo e(number_format($stats['total_revenue'], 2)); ?>

                            </p>
                        </div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Revenus Totaux</h3>
                    <div class="mt-3 space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">USD</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">$<?php echo e(number_format($stats['total_revenue'], 2)); ?></span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">CDF</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300"><?php echo e(number_format($stats['total_revenue'] * 2500, 0)); ?> FC</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Rating -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-bl-full transform translate-x-8 -translate-y-8"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-4xl font-black text-gray-900 dark:text-white"><?php echo e(number_format($stats['average_rating'], 1)); ?></p>
                        </div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Note Moyenne</h3>
                    <div class="mt-3 flex items-center gap-1">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <?php if($i <= floor($stats['average_rating'])): ?>
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <?php else: ?>
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>


        <!-- Quick Actions Dashboard -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </div>
                    <span>Tableau de Bord</span>
                </h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Mes Articles -->
                    <a href="<?php echo e(route('items.my-items')); ?>" class="group relative overflow-hidden bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-2xl border-2 border-blue-200 dark:border-blue-800 p-6 hover:shadow-2xl hover:scale-105 hover:border-blue-400 dark:hover:border-blue-600 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/10 rounded-bl-full transform translate-x-8 -translate-y-8"></div>
                        <div class="relative">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Mes Articles</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    <?php echo e($stats['active_items']); ?> actifs
                                </span>
                                <span class="mx-2 text-gray-400">•</span>
                                <span class="text-gray-500 dark:text-gray-400"><?php echo e($stats['sold_items']); ?> vendus</span>
                            </p>
                            <div class="flex items-center text-blue-600 dark:text-blue-400 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                                <span>Gérer mes articles</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- Mes Commandes -->
                    <a href="<?php echo e(route('orders.index')); ?>" class="group relative overflow-hidden bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl border-2 border-emerald-200 dark:border-emerald-800 p-6 hover:shadow-2xl hover:scale-105 hover:border-emerald-400 dark:hover:border-emerald-600 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/10 rounded-bl-full transform translate-x-8 -translate-y-8"></div>
                        <div class="relative">
                            <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Mes Commandes</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                <?php echo e($stats['total_orders']); ?> commande<?php echo e($stats['total_orders'] > 1 ? 's' : ''); ?> passée<?php echo e($stats['total_orders'] > 1 ? 's' : ''); ?>

                            </p>
                            <div class="flex items-center text-emerald-600 dark:text-emerald-400 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                                <span>Voir mes commandes</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- Messages -->
                    <a href="<?php echo e(route('messages.index')); ?>" class="group relative overflow-hidden bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl border-2 border-purple-200 dark:border-purple-800 p-6 hover:shadow-2xl hover:scale-105 hover:border-purple-400 dark:hover:border-purple-600 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/10 rounded-bl-full transform translate-x-8 -translate-y-8"></div>
                        <div class="relative">
                            <?php if($stats['unread_messages'] > 0): ?>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-lg animate-pulse">
                                    <?php echo e($stats['unread_messages']); ?>

                                </div>
                            <?php endif; ?>
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Messages</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                <?php if($stats['unread_messages'] > 0): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                        <?php echo e($stats['unread_messages']); ?> non lu<?php echo e($stats['unread_messages'] > 1 ? 's' : ''); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-500 dark:text-gray-400">Aucun nouveau message</span>
                                <?php endif; ?>
                            </p>
                            <div class="flex items-center text-purple-600 dark:text-purple-400 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                                <span>Voir mes messages</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- Wallet -->
                    <a href="<?php echo e(route('wallet.index')); ?>" class="group relative overflow-hidden bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-2xl border-2 border-amber-200 dark:border-amber-800 p-6 hover:shadow-2xl hover:scale-105 hover:border-amber-400 dark:hover:border-amber-600 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/10 rounded-bl-full transform translate-x-8 -translate-y-8"></div>
                        <div class="relative">
                            <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Wallet</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                Gérez vos paiements et transactions
                            </p>
                            <div class="flex items-center text-amber-600 dark:text-amber-400 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                                <span>Accéder au wallet</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- Paramètres -->
                    <a href="<?php echo e(route('profile.edit')); ?>" class="group relative overflow-hidden bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-900/20 dark:to-slate-900/20 rounded-2xl border-2 border-gray-200 dark:border-gray-700 p-6 hover:shadow-2xl hover:scale-105 hover:border-gray-400 dark:hover:border-gray-600 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gray-500/10 rounded-bl-full transform translate-x-8 -translate-y-8"></div>
                        <div class="relative">
                            <div class="w-16 h-16 bg-gradient-to-br from-gray-600 to-slate-700 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Paramètres</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                Sécurité et préférences du compte
                            </p>
                            <div class="flex items-center text-gray-600 dark:text-gray-400 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                                <span>Configurer</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- Statistiques -->
                    <a href="<?php echo e(route('profile.stats')); ?>" class="group relative overflow-hidden bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20 rounded-2xl border-2 border-indigo-200 dark:border-indigo-800 p-6 hover:shadow-2xl hover:scale-105 hover:border-indigo-400 dark:hover:border-indigo-600 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/10 rounded-bl-full transform translate-x-8 -translate-y-8"></div>
                        <div class="relative">
                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Statistiques</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                Analyse détaillée de vos performances
                            </p>
                            <div class="flex items-center text-indigo-600 dark:text-indigo-400 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                                <span>Voir les stats</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>


        <!-- Activity Feed -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-rose-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span>Activité Récente</span>
                </h2>
            </div>
            
            <div class="p-6 space-y-4">
                <?php if($stats['unread_messages'] > 0): ?>
                    <div class="flex items-start gap-4 p-5 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-l-4 border-blue-500 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 dark:text-white font-semibold mb-1">
                                Vous avez <span class="text-blue-600 dark:text-blue-400 font-black"><?php echo e($stats['unread_messages']); ?></span> message<?php echo e($stats['unread_messages'] > 1 ? 's' : ''); ?> non lu<?php echo e($stats['unread_messages'] > 1 ? 's' : ''); ?>

                            </p>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                Consultez vos messages pour rester en contact avec vos acheteurs et vendeurs.
                            </p>
                            <a href="<?php echo e(route('messages.index')); ?>" 
                               class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold text-sm group">
                                <span>Les consulter maintenant</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($stats['active_items'] > 0): ?>
                    <div class="flex items-start gap-4 p-5 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border-l-4 border-emerald-500 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 dark:text-white font-semibold mb-1">
                                Vous avez <span class="text-emerald-600 dark:text-emerald-400 font-black"><?php echo e($stats['active_items']); ?></span> article<?php echo e($stats['active_items'] > 1 ? 's' : ''); ?> en ligne
                            </p>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                Vos articles sont visibles par des milliers d'acheteurs potentiels.
                            </p>
                            <a href="<?php echo e(route('items.my-items')); ?>" 
                               class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-semibold text-sm group">
                                <span>Les gérer</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($stats['total_items'] === 0): ?>
                    <div class="flex items-start gap-4 p-5 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-l-4 border-amber-500 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 dark:text-white font-semibold mb-1">
                                Commencez à vendre dès maintenant
                            </p>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                Vous n'avez encore publié aucun article. Publiez votre premier article et commencez à gagner de l'argent.
                            </p>
                            <a href="<?php echo e(route('items.create')); ?>" 
                               class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-semibold text-sm group">
                                <span>Publier votre premier article</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($stats['active_items'] === 0 && $stats['total_items'] > 0): ?>
                    <div class="flex items-start gap-4 p-5 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border-l-4 border-purple-500 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 dark:text-white font-semibold mb-1">
                                Tous vos articles ont été vendus !
                            </p>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                Félicitations ! Continuez sur votre lancée en publiant de nouveaux articles.
                            </p>
                            <a href="<?php echo e(route('items.create')); ?>" 
                               class="inline-flex items-center gap-2 text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-semibold text-sm group">
                                <span>Publier un nouvel article</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/profile/index.blade.php ENDPATH**/ ?>