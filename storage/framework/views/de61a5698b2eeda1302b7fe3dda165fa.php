<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['categories']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['categories']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div id="filterModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-8 py-6 flex items-center justify-between z-10 rounded-t-3xl">
            <h3 class="text-2xl font-bold">Filtres</h3>
            <button onclick="toggleFiltersModal()" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form action="<?php echo e(route('items.index')); ?>" method="GET" id="filterForm" class="p-8 space-y-8">
            <div>
                <label class="block text-sm font-semibold mb-3">Catégorie</label>
                <select name="category" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-black focus:outline-none transition-all">
                    <option value="">Toutes les Catégories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                            <?php echo e($category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold mb-3">Plage de Prix</label>
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" 
                           name="price_min" 
                           placeholder="Min" 
                           value="<?php echo e(request('price_min')); ?>" 
                           class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-black focus:outline-none transition-all" />
                    <input type="number" 
                           name="price_max" 
                           placeholder="Max" 
                           value="<?php echo e(request('price_max')); ?>" 
                           class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-black focus:outline-none transition-all" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-3">Trier par</label>
                <select name="sort" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-black focus:outline-none transition-all">
                    <option value="">Pertinence</option>
                    <option value="recent" <?php echo e(request('sort') === 'recent' ? 'selected' : ''); ?>>Plus Récent</option>
                    <option value="popular" <?php echo e(request('sort') === 'popular' ? 'selected' : ''); ?>>Populaire</option>
                    <option value="price_low" <?php echo e(request('sort') === 'price_low' ? 'selected' : ''); ?>>Prix: Croissant</option>
                    <option value="price_high" <?php echo e(request('sort') === 'price_high' ? 'selected' : ''); ?>>Prix: Décroissant</option>
                </select>
            </div>
            
            <div class="flex gap-4 pt-4 sticky bottom-0 bg-white pb-2">
                <button type="button" 
                        onclick="resetFilters()" 
                        class="flex-1 px-6 py-4 border-2 border-gray-200 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    Réinitialiser
                </button>
                <button type="submit" 
                        class="flex-1 px-6 py-4 bg-black text-white rounded-xl font-semibold hover:bg-gray-900 transition-all">
                    Appliquer
                </button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/components/home/filter-modal.blade.php ENDPATH**/ ?>