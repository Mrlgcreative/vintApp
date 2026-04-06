<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                <?php if($query): ?>
                    Résultats pour "<?php echo e($query); ?>"
                <?php else: ?>
                    Rechercher des articles
                <?php endif; ?>
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Trouvez des pièces uniques parmi notre collection</p>
        </div>

        <!-- Filtres -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8">
            <form method="GET" action="<?php echo e(route('items.search')); ?>" class="space-y-4">
                <!-- Barre de recherche principale -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           name="q" 
                           value="<?php echo e($query); ?>"
                           placeholder="Rechercher un article, une marque..."
                           class="w-full pl-12 pr-4 py-3.5 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <!-- Filtres rapides -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtres :</span>
                    
                    <select name="category" class="px-4 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-full text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <option value="">Catégorie</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <select name="condition" class="px-4 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-full text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <option value="">État</option>
                        <option value="new" <?php echo e(request('condition') == 'new' ? 'selected' : ''); ?>>Neuf</option>
                        <option value="like_new" <?php echo e(request('condition') == 'like_new' ? 'selected' : ''); ?>>Comme neuf</option>
                        <option value="good" <?php echo e(request('condition') == 'good' ? 'selected' : ''); ?>>Bon état</option>
                        <option value="fair" <?php echo e(request('condition') == 'fair' ? 'selected' : ''); ?>>État correct</option>
                    </select>

                    <div class="flex items-center gap-2">
                        <input type="number" 
                               name="min_price" 
                               placeholder="Min"
                               value="<?php echo e(request('min_price')); ?>"
                               class="w-24 px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-full text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <span class="text-gray-400">-</span>
                        <input type="number" 
                               name="max_price" 
                               placeholder="Max"
                               value="<?php echo e(request('max_price')); ?>"
                               class="w-24 px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-full text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <span class="text-xs text-gray-400">FCFA</span>
                    </div>

                    <div class="flex-1"></div>

                    <a href="<?php echo e(route('items.index')); ?>" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        Réinitialiser
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-medium rounded-full hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">
                        Rechercher
                    </button>
                </div>
            </form>
        </div>

        <?php if($items->count() > 0): ?>
            <!-- Barre de résultats -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <p class="text-gray-600 dark:text-gray-400">
                    <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($items->total()); ?></span> article(s) trouvé(s)
                </p>
                
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Trier par</span>
                    <select id="sortSelect" class="px-4 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                        <option value="created_at-desc" <?php echo e(request('sort') == 'created_at' && request('order') == 'desc' ? 'selected' : ''); ?>>Plus récents</option>
                        <option value="created_at-asc" <?php echo e(request('sort') == 'created_at' && request('order') == 'asc' ? 'selected' : ''); ?>>Plus anciens</option>
                        <option value="price-asc" <?php echo e(request('sort') == 'price' && request('order') == 'asc' ? 'selected' : ''); ?>>Prix croissant</option>
                        <option value="price-desc" <?php echo e(request('sort') == 'price' && request('order') == 'desc' ? 'selected' : ''); ?>>Prix décroissant</option>
                        <option value="views-desc" <?php echo e(request('sort') == 'views' && request('order') == 'desc' ? 'selected' : ''); ?>>Popularité</option>
                    </select>
                </div>
            </div>

            <!-- Grille de produits -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 lg:gap-6">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="group bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <a href="<?php echo e(route('items.show', $item)); ?>" class="block">
                            <!-- Image -->
                            <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <?php if($item->images && count($item->images) > 0): ?>
                                    <img src="<?php echo e(Storage::url($item->images[0])); ?>" 
                                         alt="<?php echo e($item->name); ?>" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>

                                <!-- Badge état -->
                                <span class="absolute top-3 left-3 px-2.5 py-1 text-xs font-medium rounded-full
                                    <?php if($item->condition == 'new'): ?> bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300
                                    <?php elseif($item->condition == 'like_new'): ?> bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300
                                    <?php elseif($item->condition == 'good'): ?> bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300
                                    <?php else: ?> bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300
                                    <?php endif; ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $item->condition))); ?>

                                </span>

                                <!-- Bouton favori -->
                                <?php if(auth()->guard()->check()): ?>
                                    <button type="button" 
                                            class="favorite-btn absolute top-3 right-3 w-9 h-9 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-sm hover:bg-white dark:hover:bg-gray-700 hover:scale-110 transition-all"
                                            data-item-id="<?php echo e($item->id); ?>">
                                        <svg class="w-5 h-5 text-gray-400 hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Infos -->
                            <div class="p-4">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1"><?php echo e($item->brand->name ?? $item->category->name ?? 'Vintage'); ?></p>
                                <h3 class="font-medium text-gray-900 dark:text-white text-sm line-clamp-2 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    <?php echo e($item->name); ?>

                                </h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white"><?php echo e($item->formatted_price); ?></span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <?php echo e($item->views); ?>

                                    </span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <div class="mt-10 flex justify-center">
                <?php echo e($items->appends(request()->query())->links()); ?>

            </div>

        <?php else: ?>
            <!-- État vide -->
            <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                <div class="w-20 h-20 mx-auto mb-6 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aucun résultat</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                    <?php if($query): ?>
                        Aucun article ne correspond à "<?php echo e($query); ?>" avec les filtres sélectionnés.
                    <?php else: ?>
                        Essayez de modifier vos critères de recherche.
                    <?php endif; ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="<?php echo e(route('items.index')); ?>" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-full hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Voir tous les articles
                    </a>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('items.create')); ?>" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-full hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Vendre un article
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-6 right-6 z-50 transform translate-x-[400px] transition-transform duration-300">
    <div class="flex items-center gap-3 px-5 py-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <span id="toastMessage" class="text-sm font-medium text-gray-900 dark:text-white">Action réussie</span>
        <button onclick="closeToast()" class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du tri
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const [sort, order] = this.value.split('-');
            const url = new URL(window.location);
            url.searchParams.set('sort', sort);
            url.searchParams.set('order', order);
            window.location.href = url.toString();
        });
    }

    // Gestion des favoris
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const itemId = this.dataset.itemId;
            const icon = this.querySelector('svg');
            
            fetch(`/items/${itemId}/favorite`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_favorite) {
                        icon.classList.add('fill-red-500', 'text-red-500');
                        icon.classList.remove('text-gray-400');
                    } else {
                        icon.classList.remove('fill-red-500', 'text-red-500');
                        icon.classList.add('text-gray-400');
                    }
                    showToast(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Une erreur est survenue', 'error');
            });
        });
    });
});

function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    if (toast && toastMessage) {
        toastMessage.textContent = message;
        toast.style.transform = 'translateX(0)';
        setTimeout(() => {
            toast.style.transform = 'translateX(400px)';
        }, 3000);
    }
}

function closeToast() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.style.transform = 'translateX(400px)';
    }
}
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/items/search.blade.php ENDPATH**/ ?>