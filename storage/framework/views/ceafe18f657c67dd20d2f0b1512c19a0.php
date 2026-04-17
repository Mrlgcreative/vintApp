

<?php $__env->startSection('title', 'Tableau de bord'); ?>
<?php $__env->startSection('page-title', 'Tableau de bord'); ?>

<?php $__env->startSection('content'); ?>


<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-5 mb-6">

    
    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-primary-600">Utilisateurs</span>
            <div class="w-9 h-9 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(number_format($stats['total_users'])); ?></div>
        <div class="flex items-center gap-1.5 mt-1">
            <span class="inline-flex items-center text-[11px] font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-1.5 py-0.5 rounded-md">+<?php echo e($stats['new_users_today']); ?></span>
            <span class="text-[11px] text-gray-400">aujourd'hui</span>
        </div>
    </div>

    
    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-green-600">Revenus USD</span>
            <div class="w-9 h-9 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">$<?php echo e(number_format($stats['total_revenue_usd'], 2)); ?></div>
        <p class="text-[11px] text-gray-400 mt-1"><?php echo e($stats['transactions_today']); ?> transactions aujourd'hui</p>
    </div>

    
    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-600">Revenus CDF</span>
            <div class="w-9 h-9 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(number_format($stats['total_revenue_cdf'], 0, ',', ' ')); ?> <span class="text-base font-semibold text-gray-400">FC</span></div>
        <p class="text-[11px] text-gray-400 mt-1">Franc Congolais</p>
    </div>

    
    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-yellow-200 dark:border-yellow-800/30 p-5 transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-yellow-600">Wallets Pending</span>
            <div class="w-9 h-9 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['pending_wallets']); ?></div>
        <div class="flex items-center gap-2 mt-1.5 text-[11px] text-gray-500 dark:text-gray-400">
            <span>$<?php echo e(number_format($stats['pending_wallets_usd'], 2)); ?></span>
            <span class="w-px h-3 bg-gray-200 dark:bg-gray-600"></span>
            <span><?php echo e(number_format($stats['pending_wallets_cdf'], 0, ',', ' ')); ?> FC</span>
        </div>
        <a href="<?php echo e(route('admin.wallets.pending')); ?>" class="inline-flex items-center gap-1 text-[11px] font-medium text-yellow-600 hover:text-yellow-700 mt-2 transition-colors">
            Voir détails
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    
    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-600">Articles actifs</span>
            <div class="w-9 h-9 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(number_format($stats['active_items'])); ?></div>
        <p class="text-[11px] text-gray-400 mt-1"><?php echo e(number_format($stats['total_items'])); ?> au total</p>
    </div>

    
    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-orange-600">Vérifications</span>
            <div class="w-9 h-9 bg-orange-50 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total_verifications'] ?? 0); ?></div>
        <div class="flex items-center gap-1.5 mt-1">
            <?php if(($stats['pending_verifications'] ?? 0) > 0): ?>
                <span class="inline-flex items-center text-[11px] font-medium text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-1.5 py-0.5 rounded-md"><?php echo e($stats['pending_verifications'] ?? 0); ?></span>
                <span class="text-[11px] text-gray-400">en attente</span>
            <?php else: ?>
                <span class="text-[11px] text-gray-400">Aucune en attente</span>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-primary-600">Commissions</span>
            <div class="w-9 h-9 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">$<?php echo e(number_format($stats['enterprise_commission_usd'] ?? 0, 2)); ?></div>
        <p class="text-[11px] text-gray-400 mt-1">Sous-wallet commission</p>
    </div>

    
    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-violet-600">Commandes</span>
            <div class="w-9 h-9 bg-violet-50 dark:bg-violet-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['pending_orders']); ?></div>
        <p class="text-[11px] text-gray-400 mt-1">en attente de traitement</p>
    </div>
</div>


<div class="mb-6">
    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3 px-1">Sous-wallets Entreprise</h3>
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-5">

        
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Transport</span>
            </div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">$<?php echo e(number_format($stats['enterprise_transport_usd'] ?? 0, 2)); ?></div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-accent-50 dark:bg-accent-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Boost</span>
            </div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">$<?php echo e(number_format($stats['enterprise_boost_usd'] ?? 0, 2)); ?></div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-amber-50 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Vérifications</span>
            </div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">$<?php echo e(number_format($stats['verification_revenue_usd'] ?? 0, 2)); ?></div>
            <p class="text-[11px] text-gray-400 mt-1"><?php echo e($stats['completed_verifications'] ?? 0); ?> payées</p>
        </div>

        
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 dark:from-gray-700 dark:to-gray-800 rounded-2xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-300">Total Entreprise</span>
            </div>
            <div class="text-xl font-bold text-white">$<?php echo e(number_format(($stats['enterprise_commission_usd'] ?? 0) + ($stats['enterprise_transport_usd'] ?? 0) + ($stats['enterprise_boost_usd'] ?? 0), 2)); ?></div>
            <p class="text-[11px] text-gray-400 mt-1">Tous sous-wallets USD</p>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

    
    <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Évolution — 30 derniers jours</h3>
            <div class="flex items-center gap-4 text-[11px]">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Utilisateurs</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Transactions</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Revenus</span>
            </div>
        </div>
        <div class="p-5">
            <canvas id="dailyStatsChart" height="320"></canvas>
        </div>
    </div>

    
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Aperçu rapide</h3>
        </div>
        <div class="p-5 flex-1 flex flex-col justify-between">
            <div class="space-y-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Actifs (7j)</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo e($stats['active_users']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Commandes en attente</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo e($stats['pending_orders']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Fonds en wallets</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo e(number_format($stats['total_wallet_balance'], 2)); ?> <span class="text-xs font-normal text-gray-400">USD</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-2.5 mt-6">
                <a href="<?php echo e(route('admin.users.index')); ?>" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Gérer les utilisateurs
                </a>
                <a href="<?php echo e(route('admin.wallets.pending')); ?>" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Wallets en attente
                </a>
                <a href="<?php echo e(route('admin.transactions.index')); ?>" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Voir les transactions
                </a>
            </div>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Dernières transactions</h3>
            <a href="<?php echo e(route('admin.transactions.index')); ?>" class="text-[11px] font-medium text-primary-600 hover:text-primary-700 transition-colors">Tout voir</a>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-700/30">
            <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center gap-3.5 px-6 py-3.5 hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                    <?php if($transaction->status === 'completed'): ?>
                        <div class="w-8 h-8 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    <?php elseif($transaction->status === 'pending'): ?>
                        <div class="w-8 h-8 bg-yellow-50 dark:bg-yellow-900/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    <?php else: ?>
                        <div class="w-8 h-8 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                    <?php endif; ?>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?php echo e($transaction->user?->name ?? 'Utilisateur supprimé'); ?></p>
                        <p class="text-[11px] text-gray-400 truncate"><?php echo e($transaction->description); ?></p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e(number_format($transaction->amount, 2)); ?> <?php echo e($transaction->currency); ?></p>
                        <p class="text-[11px] text-gray-400"><?php echo e($transaction->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-10">
                    <svg class="w-10 h-10 text-gray-200 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    <p class="text-sm text-gray-400">Aucune transaction récente</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Nouveaux utilisateurs</h3>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="text-[11px] font-medium text-primary-600 hover:text-primary-700 transition-colors">Tout voir</a>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-700/30">
            <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php if($user): ?>
                <div class="flex items-center gap-3.5 px-6 py-3.5 hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                    <div class="flex-shrink-0">
                        <?php if($user->avatar): ?>
                            <img src="<?php echo e($user->avatar_url); ?>" class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700" alt="">
                        <?php else: ?>
                            <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-semibold text-xs ring-2 ring-primary-100 dark:ring-primary-900/30">
                                <?php echo e($user->initial ?? substr($user->name ?? 'U', 0, 1)); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?php echo e($user->name ?? 'Utilisateur'); ?></p>
                            <?php if(method_exists($user, 'isOnline') && $user->isOnline()): ?>
                                <span class="w-2 h-2 bg-green-500 rounded-full flex-shrink-0" title="En ligne"></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-[11px] text-gray-400 truncate"><?php echo e($user->email ?? 'N/A'); ?></p>
                    </div>
                    <span class="text-[11px] text-gray-400 flex-shrink-0"><?php echo e($user->created_at?->diffForHumans() ?? 'N/A'); ?></span>
                </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-10">
                    <svg class="w-10 h-10 text-gray-200 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-sm text-gray-400">Aucun nouvel utilisateur</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('dailyStatsChart').getContext('2d');
    const dailyStats = <?php echo json_encode($dailyStats, 15, 512) ?>;
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';
    const textColor = isDark ? '#9ca3af' : '#6b7280';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyStats.map(s => {
                const d = new Date(s.date);
                return d.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Utilisateurs',
                data: dailyStats.map(s => s.users),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.35,
                pointRadius: 0,
                pointHitRadius: 20
            }, {
                label: 'Transactions',
                data: dailyStats.map(s => s.transactions),
                borderColor: '#f43f5e',
                backgroundColor: 'rgba(244,63,94,0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.35,
                pointRadius: 0,
                pointHitRadius: 20
            }, {
                label: 'Revenus (USD)',
                data: dailyStats.map(s => s.revenue),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.35,
                pointRadius: 0,
                pointHitRadius: 20,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1f2937' : '#fff',
                    titleColor: isDark ? '#f9fafb' : '#111827',
                    bodyColor: isDark ? '#d1d5db' : '#6b7280',
                    borderColor: isDark ? '#374151' : '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 10,
                    titleFont: { weight: '600', size: 12 },
                    bodyFont: { size: 11 },
                    boxPadding: 4
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: textColor, font: { size: 10 }, maxRotation: 0, autoSkipPadding: 20 }
                },
                y: {
                    position: 'left',
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 10 }, padding: 8 }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false },
                    ticks: { color: textColor, font: { size: 10 }, padding: 8, callback: v => '$' + v }
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mes projets\vintApp\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>