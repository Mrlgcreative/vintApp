

<?php $__env->startPush('styles'); ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
    
    #home-content * { font-family: 'Inter', sans-serif !important; }
    
    .hero-gradient { background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%) !important; }
    .category-gradient { background: linear-gradient(135deg, #f5f3ff 0%, #ddd6fe 100%) !important; }
    
    .animate-slide-in { 
        animation: slideIn 0.6s ease-out !important; 
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    .hover-lift { 
        transition: transform 0.3s ease !important; 
    }
    .hover-lift:hover { 
        transform: translateY(-4px) !important; 
    }
    
    .card-hover { 
        transition: all 0.3s ease !important; 
    }
    .card-hover:hover { 
        transform: translateY(-6px) !important; 
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15) !important; 
    }
    
    .image-zoom { 
        transition: transform 0.4s ease !important; 
    }
    .card-hover:hover .image-zoom { 
        transform: scale(1.08) !important; 
    }
    
    .badge-shadow { 
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3) !important; 
    }
    
    .carousel-indicator { 
        transition: all 0.3s ease !important; 
    }
    .carousel-indicator.active { 
        background-color: white !important; 
        width: 2rem !important; 
    }
</style>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 z-50 bg-violet-600 text-white px-5 py-3 rounded-xl shadow-lg transform translate-x-[400px] transition-transform duration-300 flex items-center gap-3">
    <span class="text-xl">✅</span>
    <span id="toastMessage">Notification</span>
    <button onclick="closeToast()" class="ml-2 hover:opacity-70">
        <span class="text-lg">✖</span>
    </button>
</div>

<div id="home-content">

<!-- Container Principal -->
<div class="min-h-screen pb-20">

    <!-- Barre de Recherche -->
    <div class="max-w-7xl mx-auto px-4 pt-6 pb-4 animate-slide-in">
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200/60 p-2 flex gap-2 hover:shadow-md transition-shadow duration-300">
            <form action="<?php echo e(route('items.index')); ?>" method="GET" class="flex-1 flex gap-2">
                <div class="flex-1 relative">
                    <input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Rechercher des articles..." class="w-full h-12 pl-4 pr-28 rounded-xl bg-neutral-50 border-2 border-transparent focus:border-violet-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-100 transition-all duration-300 text-sm font-medium" />
                    <button type="submit" class="absolute right-1.5 top-1.5 h-9 px-5 bg-gradient-to-r from-violet-600 to-violet-700 text-white rounded-lg text-sm font-semibold hover:from-violet-700 hover:to-violet-800 transition-all duration-300 shadow-sm hover:shadow-md flex items-center gap-2">
                        <span>🔍</span>
                    </button>
                </div>
            </form>
            <button onclick="toggleFiltersModal()" class="h-12 px-5 bg-neutral-50 hover:bg-white border-2 border-transparent hover:border-violet-600 rounded-xl text-violet-600 font-semibold text-sm transition-all duration-300 flex items-center gap-2">
                <span>⚙️</span>
                <span class="hidden sm:inline">Filtres</span>
            </button>
        </div>
    </div>

    <!-- Hero Carousel -->
    <?php if(isset($heroSlides) && $heroSlides->count() > 0): ?>
        <div class="max-w-7xl mx-auto px-4 py-6 animate-slide-in">
            <div class="relative overflow-hidden rounded-3xl shadow-lg hero-gradient" id="heroCarousel">
                <div class="flex transition-transform duration-700 ease-in-out" id="carouselInner">
                    <?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $imgPos = $slide->image_position ?? 'left';
                        ?>
                        <div class="min-w-full px-8 py-12 md:px-16 md:py-20">
                            <div class="grid md:grid-cols-2 gap-8 items-center">
                                <?php if($imgPos === 'left'): ?>
                                    <div class="flex justify-center md:justify-start">
                                        <img src="<?php echo e(Storage::url($slide->image_path)); ?>" alt="<?php echo e($slide->title); ?>" class="w-full max-w-sm h-80 object-cover rounded-2xl shadow-2xl" />
                                    </div>
                                    <div class="text-white space-y-6">
                                        <h1 class="text-4xl md:text-5xl font-bold leading-tight"><?php echo e($slide->title); ?></h1>
                                        <p class="text-lg text-violet-100 leading-relaxed"><?php echo e($slide->subtitle); ?></p>
                                        <div class="flex flex-wrap gap-4">
                                            <?php if($slide->button_primary_text && $slide->button_primary_url): ?>
                                                <a href="<?php echo e($slide->button_primary_url); ?>" class="px-6 py-3 bg-white text-violet-600 rounded-full font-semibold hover-lift inline-flex items-center gap-2">
                                                    <?php echo e($slide->button_primary_text); ?>

                                                    <span></span>
                                                </a>
                                            <?php endif; ?>
                                            <?php if($slide->button_secondary_text && $slide->button_secondary_url): ?>
                                                <a href="<?php echo e($slide->button_secondary_url); ?>" class="px-6 py-3 bg-violet-700/30 border-2 border-white/30 text-white rounded-full font-semibold hover:bg-white/10 transition-all duration-300">
                                                    <?php echo e($slide->button_secondary_text); ?>

                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-white space-y-6 order-2 md:order-1">
                                        <h1 class="text-4xl md:text-5xl font-bold leading-tight"><?php echo e($slide->title); ?></h1>
                                        <p class="text-lg text-violet-100 leading-relaxed"><?php echo e($slide->subtitle); ?></p>
                                        <div class="flex flex-wrap gap-4">
                                            <?php if($slide->button_primary_text && $slide->button_primary_url): ?>
                                                <a href="<?php echo e($slide->button_primary_url); ?>" class="px-6 py-3 bg-white text-violet-600 rounded-full font-semibold hover-lift inline-flex items-center gap-2">
                                                    <?php echo e($slide->button_primary_text); ?>

                                                    <span></span>
                                                </a>
                                            <?php endif; ?>
                                            <?php if($slide->button_secondary_text && $slide->button_secondary_url): ?>
                                                <a href="<?php echo e($slide->button_secondary_url); ?>" class="px-6 py-3 bg-violet-700/30 border-2 border-white/30 text-white rounded-full font-semibold hover:bg-white/10 transition-all duration-300">
                                                    <?php echo e($slide->button_secondary_text); ?>

                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex justify-center md:justify-end order-1 md:order-2">
                                        <img src="<?php echo e(Storage::url($slide->image_path)); ?>" alt="<?php echo e($slide->title); ?>" class="w-full max-w-sm h-80 object-cover rounded-2xl shadow-2xl" />
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <div class="absolute top-4 left-4 flex gap-2">
                    <?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button onclick="goToSlide(<?php echo e($index); ?>)" class="w-8 h-1 rounded-full carousel-indicator <?php echo e($index === 0 ? 'active bg-white' : 'bg-white/50'); ?>"></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="max-w-7xl mx-auto px-4 py-6 animate-slide-in">
            <div class="relative overflow-hidden rounded-3xl shadow-lg hero-gradient px-8 py-12 md:px-16 md:py-20">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div class="text-white space-y-6">
                        <h1 class="text-4xl md:text-5xl font-bold leading-tight">Bienvenue sur VintApp</h1>
                        <p class="text-lg text-violet-100 leading-relaxed">Découvrez des articles d'occasion de qualité</p>
                        <a href="<?php echo e(route('items.index')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-violet-600 rounded-full font-semibold hover-lift">
                            Explorer <span>→</span>
                        </a>
                    </div>
                    <div class="flex justify-center">
                        <div class="w-80 h-80 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <span class="text-8xl text-white/30">📷</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Catégories -->
    <section class="max-w-7xl mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold text-neutral-800 mb-6">Catégories populaires</h2>
        
        <div class="md:hidden flex gap-3 overflow-x-auto scrollbar-hide pb-2">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('items.index', ['category' => $category->id])); ?>" class="flex-shrink-0 w-20 aspect-square category-gradient rounded-2xl p-3 flex flex-col items-center justify-center gap-2 text-center hover:-translate-y-1 transition-transform duration-300">
                    <span class="text-2xl"></span>
                    <span class="text-xs font-semibold text-violet-800 line-clamp-1"><?php echo e($category->name); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class="hidden md:grid grid-cols-3 lg:grid-cols-6 gap-4">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('items.index', ['category' => $category->id])); ?>" class="category-gradient rounded-2xl p-5 flex flex-col items-center gap-3 text-center hover:-translate-y-1 transition-all duration-300 shadow-sm hover:shadow-md">
                    <span class="text-4xl"></span>
                    <div>
                        <p class="font-semibold text-neutral-800 mb-1"><?php echo e($category->name); ?></p>
                        <p class="text-xs text-neutral-500"><?php echo e($category->items_count ?? 0); ?> articles</p>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <!-- Filtres rapides -->
    <section class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-2">
            <a href="<?php echo e(route('items.index')); ?>" class="flex-shrink-0 px-5 py-2 <?php echo e(!request('sort') ? 'bg-violet-600 text-white' : 'bg-white border-2 border-neutral-200 text-neutral-700 hover:border-violet-600 hover:text-violet-600'); ?> rounded-full font-medium text-sm transition-all duration-300 inline-flex items-center gap-2">
                <span>⭐</span>
                Tous
            </a>
            <a href="<?php echo e(route('items.index', ['sort' => 'recent'])); ?>" class="flex-shrink-0 px-5 py-2 <?php echo e(request('sort') === 'recent' ? 'bg-violet-600 text-white' : 'bg-white border-2 border-neutral-200 text-neutral-700 hover:border-violet-600 hover:text-violet-600'); ?> rounded-full font-medium text-sm transition-all duration-300 inline-flex items-center gap-2">
                <span></span>
                Nouveautés
            </a>
            <a href="<?php echo e(route('items.index', ['sort' => 'popular'])); ?>" class="flex-shrink-0 px-5 py-2 <?php echo e(request('sort') === 'popular' ? 'bg-violet-600 text-white' : 'bg-white border-2 border-neutral-200 text-neutral-700 hover:border-violet-600 hover:text-violet-600'); ?> rounded-full font-medium text-sm transition-all duration-300 inline-flex items-center gap-2">
                <span></span>
                Populaires
            </a>
            <a href="<?php echo e(route('items.index', ['sort' => 'price_low'])); ?>" class="flex-shrink-0 px-5 py-2 <?php echo e(request('sort') === 'price_low' ? 'bg-violet-600 text-white' : 'bg-white border-2 border-neutral-200 text-neutral-700 hover:border-violet-600 hover:text-violet-600'); ?> rounded-full font-medium text-sm transition-all duration-300 inline-flex items-center gap-2">
                <span>🏷️</span>
                Prix croissant
            </a>
        </div>
    </section>

    <!-- Articles Grid -->
    <section class="max-w-7xl mx-auto px-4 py-6 pb-20">
        <h2 class="text-2xl font-bold text-neutral-800 mb-6">Articles récents</h2>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <?php $__empty_1 = true; $__currentLoopData = $latestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $firstImage = is_string($item->images) ? json_decode($item->images, true)[0] ?? null : ($item->images[0] ?? null);
                    $imgPath = $firstImage;
                ?>
                <div class="bg-white rounded-2xl overflow-hidden card-hover relative shadow-sm border border-neutral-100">
                    <div class="aspect-square bg-neutral-100 overflow-hidden relative">
                        <?php if($imgPath && Storage::disk('public')->exists($imgPath)): ?>
                            <img src="<?php echo e(Storage::url($imgPath)); ?>" alt="<?php echo e($item->name); ?>" class="w-full h-full object-cover image-zoom" />
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-6xl text-neutral-300"></span>
                            </div>
                        <?php endif; ?>
                        
                        <span class="absolute top-2 right-2 px-3 py-1.5 bg-violet-600 text-white rounded-full text-xs font-bold badge-shadow">
                            <?php echo e(number_format($item->price, 0, ',', ' ')); ?> <?php echo e($item->currency); ?>

                        </span>
                    </div>
                    
                    <div class="p-3">
                        <h3 class="font-semibold text-sm text-neutral-800 mb-2 line-clamp-2 min-h-[2.5rem]"><?php echo e($item->name); ?></h3>
                        
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 bg-violet-100 text-violet-600 rounded-full text-xs font-medium">
                                <?php echo e($item->condition); ?>

                            </span>
                            
                            <button onclick="addToCart(<?php echo e($item->id); ?>)" class="w-7 h-7 bg-cyan-500 hover:bg-cyan-600 text-white rounded-full flex items-center justify-center transition-colors duration-200">
                                <span>+</span>
                            </button>
                        </div>
                        
                        <p class="text-xs text-neutral-500 flex items-center gap-1">
                            <span>🕒</span>
                            <?php echo e($item->created_at->diffForHumans()); ?>

                        </p>
                    </div>
                    
                    <a href="<?php echo e(route('items.show', $item)); ?>" class="absolute inset-0"></a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full text-center py-12">
                    <span class="text-8xl text-neutral-300">📦</span>
                    <h3 class="text-lg font-semibold text-neutral-600 mb-2 mt-4">Aucun article disponible</h3>
                    <p class="text-neutral-500">Revenez plus tard pour découvrir de nouveaux articles</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

</div><!-- Fin #home-content -->

<!-- Modal Filtres -->
<div id="filterModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg">
        <div class="bg-gradient-to-r from-violet-600 to-violet-700 text-white px-6 py-5 rounded-t-3xl flex items-center justify-between">
            <h3 class="text-xl font-bold flex items-center gap-2">
                <span></span>
                Filtres
            </h3>
            <button onclick="toggleFiltersModal()" class="hover:bg-white/10 rounded-full p-2 transition-colors">
                <span></span>
            </button>
        </div>
        
        <form action="<?php echo e(route('items.index')); ?>" method="GET" id="filterForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-2">Catégorie</label>
                <select name="category" class="w-full px-4 py-2.5 border-2 border-neutral-200 rounded-xl focus:border-violet-600 focus:outline-none focus:ring-4 focus:ring-violet-100 transition-all">
                    <option value="">Toutes les catégories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                            <?php echo e($category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-2">Prix</label>
                <div class="grid grid-cols-2 gap-3">
                    <input type="number" name="price_min" placeholder="Prix min" value="<?php echo e(request('price_min')); ?>" class="px-4 py-2.5 border-2 border-neutral-200 rounded-xl focus:border-violet-600 focus:outline-none focus:ring-4 focus:ring-violet-100 transition-all" />
                    <input type="number" name="price_max" placeholder="Prix max" value="<?php echo e(request('price_max')); ?>" class="px-4 py-2.5 border-2 border-neutral-200 rounded-xl focus:border-violet-600 focus:outline-none focus:ring-4 focus:ring-violet-100 transition-all" />
                </div>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="resetFilters()" class="flex-1 px-4 py-3 border-2 border-neutral-300 text-neutral-700 rounded-xl font-semibold hover:bg-neutral-50 transition-colors">
                    Réinitialiser
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-violet-600 to-violet-700 text-white rounded-xl font-semibold hover:from-violet-700 hover:to-violet-800 transition-all shadow-lg">
                    Appliquer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
<?php if(isset($heroSlides) && $heroSlides->count() > 0): ?>
let currentSlide = 0;
const totalSlides = <?php echo e($heroSlides->count()); ?>;

function goToSlide(index) {
    currentSlide = index;
    const inner = document.getElementById('carouselInner');
    if (inner) {
        inner.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        document.querySelectorAll('.carousel-indicator').forEach((ind, i) => {
            if (i === index) {
                ind.classList.add('active', 'bg-white');
                ind.classList.remove('bg-white/50');
            } else {
                ind.classList.remove('active', 'bg-white');
                ind.classList.add('bg-white/50');
            }
        });
    }
}

setInterval(() => {
    goToSlide((currentSlide + 1) % totalSlides);
}, 5000);
<?php endif; ?>

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

function toggleFiltersModal() {
    const modal = document.getElementById('filterModal');
    if (modal) {
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }
}

function resetFilters() {
    document.getElementById('filterForm').reset();
}

function addToCart(itemId) {
    showToast('Article ajouté au panier !');
    console.log('Item added:', itemId);
}
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/home.blade.php ENDPATH**/ ?>