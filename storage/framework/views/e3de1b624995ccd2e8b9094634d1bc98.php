<aside class="w-64 lg:w-72 flex-shrink-0 hidden lg:block min-h-screen bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 sticky top-0">
    <div class="p-6">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold shadow-lg shadow-primary-500/20">
                <i class="fas fa-store"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">Espace vendeur</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e(Auth::user()->name); ?></p>
            </div>
        </div>

        <nav class="space-y-1">
            <a href="<?php echo e(route('seller.dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200'); ?>">
                <i class="fas fa-chart-pie w-5 text-center"></i> Tableau de bord
            </a>
            <a href="<?php echo e(route('seller.items')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.items') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200'); ?>">
                <i class="fas fa-box w-5 text-center"></i> Mes articles
            </a>
            <a href="<?php echo e(route('seller.sales')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.sales') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200'); ?>">
                <i class="fas fa-shopping-cart w-5 text-center"></i> Mes ventes
            </a>
            <a href="<?php echo e(route('seller.wallet')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.wallet') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200'); ?>">
                <i class="fas fa-wallet w-5 text-center"></i> Mon wallet
            </a>
            <a href="<?php echo e(route('messages.index')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('messages.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200'); ?>">
                <i class="fas fa-envelope w-5 text-center"></i> Messagerie
            </a>
            <a href="<?php echo e(route('seller.categories')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.categories') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200'); ?>">
                <i class="fas fa-tags w-5 text-center"></i> Catégories
            </a>
            <a href="<?php echo e(route('seller.brands')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.brands') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200'); ?>">
                <i class="fas fa-building w-5 text-center"></i> Marques
            </a>
            <a href="<?php echo e(route('seller.reviews')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('seller.reviews') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200'); ?>">
                <i class="fas fa-star w-5 text-center"></i> Mes notes
            </a>
        </nav>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
            <a href="<?php echo e(route('items.create')); ?>" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors shadow-lg shadow-primary-500/20">
                <i class="fas fa-plus"></i> Nouvel article
            </a>
            <a href="<?php echo e(url('/')); ?>" class="flex items-center justify-center gap-2 w-full px-4 py-3 mt-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-sm">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</aside><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/seller/partials/sidebar.blade.php ENDPATH**/ ?>