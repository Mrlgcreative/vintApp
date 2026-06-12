<?php $__env->startSection('title', 'Espace Vendeur'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 min-h-screen hidden lg:block flex-shrink-0">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-store text-primary"></i>
                    Espace Vendeur
                </h2>
            </div>
            <nav class="px-4 space-y-1">
                <a href="<?php echo e(route('seller.dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.dashboard') ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                    <i class="fas fa-chart-pie w-5 text-center"></i>
                    <span>Tableau de bord</span>
                </a>
                <a href="<?php echo e(route('seller.items')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.items') ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                    <i class="fas fa-box w-5 text-center"></i>
                    <span>Mes articles</span>
                </a>
                <a href="<?php echo e(route('seller.sales')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.sales') ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                    <i class="fas fa-shopping-cart w-5 text-center"></i>
                    <span>Mes ventes</span>
                </a>
                <a href="<?php echo e(route('seller.wallet')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.wallet') ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                    <i class="fas fa-wallet w-5 text-center"></i>
                    <span>Mon wallet</span>
                </a>
                <hr class="my-3 border-gray-200 dark:border-gray-700">
                <a href="<?php echo e(route('seller.categories')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.categories') ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                    <i class="fas fa-tags w-5 text-center"></i>
                    <span>Catégories</span>
                </a>
                <a href="<?php echo e(route('seller.brands')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.brands') ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                    <i class="fas fa-copyright w-5 text-center"></i>
                    <span>Marques</span>
                </a>
                <a href="<?php echo e(route('seller.reviews')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.reviews') ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                    <i class="fas fa-star w-5 text-center"></i>
                    <span>Mes notes</span>
                </a>
            </nav>
            <div class="p-4 mt-auto border-t border-gray-200 dark:border-gray-700">
                <a href="<?php echo e(route('items.create')); ?>" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-primary to-primary-600 text-white rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <i class="fas fa-plus"></i>
                    <span>Nouvel article</span>
                </a>
            </div>
        </aside>

        <!-- Mobile sidebar toggle -->
        <div class="lg:hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex gap-2 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-2">
            <a href="<?php echo e(route('seller.dashboard')); ?>" class="px-3 py-2 rounded-xl text-xs font-medium <?php echo e(request()->routeIs('seller.dashboard') ? 'bg-primary text-white' : 'text-gray-500'); ?>">
                <i class="fas fa-chart-pie block text-base text-center mb-0.5"></i>
                Dash
            </a>
            <a href="<?php echo e(route('seller.items')); ?>" class="px-3 py-2 rounded-xl text-xs font-medium <?php echo e(request()->routeIs('seller.items') ? 'bg-primary text-white' : 'text-gray-500'); ?>">
                <i class="fas fa-box block text-base text-center mb-0.5"></i>
                Arts
            </a>
            <a href="<?php echo e(route('seller.sales')); ?>" class="px-3 py-2 rounded-xl text-xs font-medium <?php echo e(request()->routeIs('seller.sales') ? 'bg-primary text-white' : 'text-gray-500'); ?>">
                <i class="fas fa-shopping-cart block text-base text-center mb-0.5"></i>
                Ventes
            </a>
            <a href="<?php echo e(route('seller.wallet')); ?>" class="px-3 py-2 rounded-xl text-xs font-medium <?php echo e(request()->routeIs('seller.wallet') ? 'bg-primary text-white' : 'text-gray-500'); ?>">
                <i class="fas fa-wallet block text-base text-center mb-0.5"></i>
                Wallet
            </a>
        </div>

        <!-- Main content -->
        <main class="flex-1 p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                            Tableau de bord
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Bienvenue dans votre espace vendeur</p>
                    </div>
                    <a href="<?php echo e(route('items.create')); ?>" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors">
                        <i class="fas fa-plus"></i>
                        <span>Publier un article</span>
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-box text-primary text-lg"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total_items']); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Articles <span class="hidden sm:inline">· <?php echo e($stats['active_items']); ?> actifs</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-emerald-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total_sales']); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Ventes totales</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-amber-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(number_format($stats['total_revenue'], 2)); ?> $</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Revenu total</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-star text-purple-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(number_format($stats['average_rating'], 1)); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($stats['total_reviews']); ?> avis</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Articles récents -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-box text-primary"></i>
                                Mes articles
                            </h3>
                            <a href="<?php echo e(route('seller.items')); ?>" class="text-sm text-primary hover:text-primary-600 font-medium">Voir tout</a>
                        </div>
                        <div class="p-4">
                            <?php if($items->count() > 0): ?>
                                <div class="space-y-2">
                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-colors">
                                            <div class="min-w-0 flex-1">
                                                <h6 class="font-semibold text-gray-900 dark:text-white text-sm truncate"><?php echo e($item->name); ?></h6>
                                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($item->category->name ?? 'N/A'); ?></p>
                                            </div>
                                            <span class="ml-3 px-2.5 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-semibold rounded-lg flex-shrink-0">
                                                <?php echo e($item->formatted_price); ?>

                                            </span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-10">
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-box text-gray-300 dark:text-gray-500"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun article pour le moment</p>
                                    <a href="<?php echo e(route('items.create')); ?>" class="mt-3 inline-flex items-center text-sm text-primary font-medium">Publier un article</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Ventes récentes -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-shopping-cart text-emerald-500"></i>
                                Dernières ventes
                            </h3>
                            <a href="<?php echo e(route('seller.sales')); ?>" class="text-sm text-primary hover:text-primary-600 font-medium">Voir tout</a>
                        </div>
                        <div class="p-4">
                            <?php if($sales->count() > 0): ?>
                                <div class="space-y-2">
                                    <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors">
                                            <div class="min-w-0 flex-1">
                                                <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Commande #<?php echo e($sale->id); ?></h6>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo e($sale->item->name ?? 'N/A'); ?></p>
                                            </div>
                                            <span class="ml-3 px-2.5 py-1 text-xs font-semibold rounded-lg flex-shrink-0 <?php echo e($sale->status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'); ?>">
                                                <?php echo e(ucfirst($sale->status)); ?>

                                            </span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-10">
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-shopping-cart text-gray-300 dark:text-gray-500"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucune vente pour le moment</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Derniers avis -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-star text-yellow-500"></i>
                            Derniers avis
                        </h3>
                        <a href="<?php echo e(route('seller.reviews')); ?>" class="text-sm text-primary hover:text-primary-600 font-medium">Voir tout</a>
                    </div>
                    <div class="p-4">
                        <?php if($reviews->count() > 0): ?>
                            <div class="space-y-3">
                                <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                                <div class="w-9 h-9 bg-gradient-to-br from-primary to-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                                    <?php echo e(strtoupper(substr($review->reviewer->name ?? '?', 0, 1))); ?>

                                                </div>
                                                <div class="min-w-0">
                                                    <h6 class="font-semibold text-gray-900 dark:text-white text-sm"><?php echo e($review->reviewer->name ?? 'Anonyme'); ?></h6>
                                                    <div class="flex items-center gap-1">
                                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star <?php echo e($i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'); ?> text-xs"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0"><?php echo e($review->created_at->diffForHumans()); ?></span>
                                        </div>
                                        <?php if($review->comment): ?>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2"><?php echo e($review->comment); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-10">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-star text-gray-300 dark:text-gray-500"></i>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Aucun avis pour le moment</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/seller/dashboard.blade.php ENDPATH**/ ?>