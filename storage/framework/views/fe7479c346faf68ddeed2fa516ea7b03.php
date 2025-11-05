

<?php
use Illuminate\Support\Facades\Storage;
?>

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

    /* Categories styles */
    .category-card {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        min-height: 100px;
        min-width: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: linear-gradient(180deg, #fff 0%, #fbf8ff 100%);
        flex-shrink: 0;
    }

    .category-img {
        width: 60px;
        height: 60px;
        border-radius: 0.5rem;
        object-fit: cover;
        box-shadow: 0 6px 15px rgba(16,24,40,0.06);
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #7c3aed;
    }

    /* Responsive categories */
    @media (min-width: 640px) {
        .category-card {
            min-height: 120px;
            min-width: 140px;
            gap: 0.75rem;
        }
        
        .category-img {
            width: 80px;
            height: 80px;
            border-radius: 0.75rem;
            font-size: 2rem;
        }
    }

    .category-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(124,58,237,0.06) 0%, rgba(167,139,250,0.03) 100%);
        pointer-events: none;
    }

    .category-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(255,255,255,0.9);
        padding: 0.125rem 0.375rem;
        border-radius: 999px;
        font-size: 0.625rem;
        color: #374151;
        box-shadow: 0 4px 12px rgba(15,23,42,0.06);
    }

    .category-name { 
        font-weight: 600; 
        color: #111827; 
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100px;
    }
    
    .category-sub { 
        font-size: 0.625rem; 
        color: #6b7280; 
        white-space: nowrap;
    }

    /* Responsive adjustments */
    @media (min-width: 640px) {
        .category-badge {
            top: 10px;
            right: 10px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .category-name { 
            font-weight: 700;
            max-width: 120px;
        }
        
        .category-sub { 
            font-size: 0.75rem;
        }
    }

    /* Quick filters styles */
    .filter-chip {
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.625rem 1.25rem;
        transition: all 0.3s ease;
        white-space: nowrap;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        border: 2px solid transparent;
    }

    .filter-chip.active {
        background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        transform: translateY(-1px);
    }

    .filter-chip:not(.active) {
        background: white;
        color: #374151;
        border-color: #e5e7eb;
    }

    .filter-chip:not(.active):hover {
        border-color: #7c3aed;
        color: #7c3aed;
        background: #faf5ff;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(124, 58, 237, 0.15);
    }

    /* Mobile responsive */
    @media (max-width: 640px) {
        .filter-chip {
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
        }
    }

    /* Animations supplémentaires */
    .fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }
    
    @keyframes fadeInUp {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
    
    .pulse-glow {
        animation: pulseGlow 2s infinite;
    }
    
    @keyframes pulseGlow {
        0%, 100% { 
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.3); 
        }
        50% { 
            box-shadow: 0 0 30px rgba(124, 58, 237, 0.6); 
        }
    }
    
    .float {
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    /* Améliorations des cards articles */
    .product-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .product-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* Stats counter animation */
    .stats-number {
        background: linear-gradient(45deg, #fff, #e0e7ff);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        font-weight: 900;
    }
</style>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<?php $__env->stopPush(); ?>


<?php $__env->startSection('title', 'Accueil'); ?>
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
                    <div class="text-white space-y-8">
                        <div class="space-y-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium">
                                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                Nouvelle collection disponible
                            </div>
                            <h1 class="text-4xl md:text-6xl font-bold leading-tight bg-gradient-to-r from-white to-violet-200 bg-clip-text text-transparent">
                                Découvrez le vintage
                                <span class="block text-white">d'exception</span>
                            </h1>
                            <p class="text-lg text-violet-100 leading-relaxed max-w-md">
                                Des pièces uniques sélectionnées avec soin. Qualité garantie et authenticité vérifiée.
                            </p>
                        </div>
                        
                        <div class="flex flex-wrap gap-4">
                            <a href="<?php echo e(route('items.index')); ?>" class="group px-8 py-4 bg-white text-violet-600 rounded-full font-semibold hover-lift inline-flex items-center gap-2 shadow-lg hover:shadow-xl transition-all duration-300">
                                Explorer maintenant
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                            <a href="#categories" class="px-8 py-4 bg-violet-700/30 border-2 border-white/30 text-white rounded-full font-semibold hover:bg-white/10 transition-all duration-300 backdrop-blur-sm">
                                Voir les catégories
                            </a>
                        </div>
                        
                        <div class="flex items-center gap-8 pt-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold">1000+</div>
                                <div class="text-sm text-violet-200">Articles</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">500+</div>
                                <div class="text-sm text-violet-200">Clients satisfaits</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">24/7</div>
                                <div class="text-sm text-violet-200">Support</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <div class="relative">
                            <div class="w-80 h-80 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                                <span class="text-8xl text-white/30">�️</span>
                            </div>
                            <div class="absolute -top-4 -right-4 w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center text-2xl animate-bounce">
                                🎉
                            </div>
                            <div class="absolute -bottom-4 -left-4 w-12 h-12 bg-green-400 rounded-full flex items-center justify-center text-lg">
                                ✨
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Catégories améliorées -->
    <section id="categories" class="max-w-7xl mx-auto px-4 py-6 fade-in-up">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-neutral-800 mb-2">Explorez nos catégories</h2>
            <p class="text-neutral-600">Trouvez exactement ce que vous cherchez</p>
        </div>

        <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $catImg = $category->image ?? null;
                ?>
                <a href="<?php echo e(route('items.index', ['category' => $category->id])); ?>" class="category-card p-3 sm:p-4 hover:shadow-lg transition-all duration-300">
                    <?php if($catImg && Storage::disk('public')->exists($catImg)): ?>
                        <img src="<?php echo e(Storage::url($catImg)); ?>" alt="<?php echo e($category->name); ?>" class="category-img" />
                    <?php else: ?>
                        <div class="category-img">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    <?php endif; ?>

                    <div class="text-center">
                        <p class="category-name text-xs sm:text-sm"><?php echo e($category->name); ?></p>
                        <p class="category-sub text-xs"><?php echo e($category->items_count ?? 0); ?> articles</p>
                    </div>

                    <div class="category-badge text-xs">Voir</div>
                    <div class="category-overlay" aria-hidden="true"></div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <!-- Section de confiance -->
    <section class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center p-6 bg-white rounded-2xl shadow-sm border border-neutral-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">🚚</span>
                </div>
                <h3 class="font-semibold text-neutral-800 mb-2">Livraison rapide</h3>
                <p class="text-sm text-neutral-600">Expédition sous 24h</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-2xl shadow-sm border border-neutral-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">🔒</span>
                </div>
                <h3 class="font-semibold text-neutral-800 mb-2">Paiement sécurisé</h3>
                <p class="text-sm text-neutral-600">Transactions protégées</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-2xl shadow-sm border border-neutral-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">↩️</span>
                </div>
                <h3 class="font-semibold text-neutral-800 mb-2">Retour gratuit</h3>
                <p class="text-sm text-neutral-600">14 jours pour changer d'avis</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-2xl shadow-sm border border-neutral-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">⭐</span>
                </div>
                <h3 class="font-semibold text-neutral-800 mb-2">Qualité vérifiée</h3>
                <p class="text-sm text-neutral-600">Articles authentifiés</p>
            </div>
        </div>
    </section>

    <!-- Filtres rapides -->
    <section class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2">
            <a href="<?php echo e(route('items.index')); ?>" class="filter-chip <?php echo e(!request('sort') ? 'active' : ''); ?> inline-flex items-center gap-2">
                <span class="text-base">⭐</span>
                Tous
            </a>
            <a href="<?php echo e(route('items.index', ['sort' => 'recent'])); ?>" class="filter-chip <?php echo e(request('sort') === 'recent' ? 'active' : ''); ?> inline-flex items-center gap-2">
                <span class="text-base">🆕</span>
                Nouveautés
            </a>
            <a href="<?php echo e(route('items.index', ['sort' => 'popular'])); ?>" class="filter-chip <?php echo e(request('sort') === 'popular' ? 'active' : ''); ?> inline-flex items-center gap-2">
                <span class="text-base">🔥</span>
                Populaires
            </a>
            <a href="<?php echo e(route('items.index', ['sort' => 'price_low'])); ?>" class="filter-chip <?php echo e(request('sort') === 'price_low' ? 'active' : ''); ?> inline-flex items-center gap-2">
                <span class="text-base">🏷️</span>
                Prix croissant
            </a>
            <a href="<?php echo e(route('items.index', ['sort' => 'price_high'])); ?>" class="filter-chip <?php echo e(request('sort') === 'price_high' ? 'active' : ''); ?> inline-flex items-center gap-2">
                <span class="text-base">💎</span>
                Prix décroissant
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
                    $isNew = $item->created_at->gt(now()->subDays(7));
                    $isFeatured = rand(0, 4) === 0; // 20% chance d'être featured
                ?>
                <div class="group bg-white rounded-2xl overflow-hidden card-hover relative shadow-sm border border-neutral-100 hover:border-violet-200">
                    <div class="aspect-square bg-neutral-100 overflow-hidden relative">
                        <?php if($imgPath && Storage::disk('public')->exists($imgPath)): ?>
                            <img src="<?php echo e(Storage::url($imgPath)); ?>" alt="<?php echo e($item->name); ?>" class="w-full h-full object-cover image-zoom" />
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-6xl text-neutral-300">📷</span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Overlay avec actions rapides -->
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-2">
                            <button class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-colors duration-200 hover:scale-110 transform">
                                <span class="text-lg">👁️</span>
                            </button>
                            <button class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-colors duration-200 hover:scale-110 transform">
                                <span class="text-lg">❤️</span>
                            </button>
                        </div>
                        
                        <!-- Badges -->
                        <div class="absolute top-2 left-2 flex flex-col gap-1">
                            <?php if($isNew): ?>
                                <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                                    NOUVEAU
                                </span>
                            <?php endif; ?>
                            <?php if($isFeatured): ?>
                                <span class="px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full">
                                    ⭐ FEATURED
                                </span>
                            <?php endif; ?>
                        </div>
                        
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

    <!-- Section CTA -->
    <section class="max-w-7xl mx-auto px-4 py-12">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-violet-600 via-purple-600 to-blue-600 p-8 md:p-12 text-center">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative z-10 max-w-3xl mx-auto text-white">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Prêt à découvrir votre prochain coup de cœur ?
                </h2>
                <p class="text-lg text-white/90 mb-8">
                    Rejoignez des milliers d'acheteurs satisfaits et trouvez des pièces uniques à prix imbattables.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="<?php echo e(route('items.index')); ?>" class="px-8 py-4 bg-white text-violet-600 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 hover:scale-105 shadow-lg">
                        Commencer à acheter
                    </a>
                    <a href="<?php echo e(route('items.create') ?? '#'); ?>" class="px-8 py-4 bg-transparent border-2 border-white text-white rounded-full font-semibold hover:bg-white hover:text-violet-600 transition-all duration-300">
                        Vendre mes articles
                    </a>
                </div>
            </div>
            <!-- Éléments décoratifs -->
            <div class="absolute top-4 right-4 w-16 h-16 bg-white/10 rounded-full float"></div>
            <div class="absolute bottom-4 left-4 w-12 h-12 bg-white/10 rounded-full float" style="animation-delay: 2s;"></div>
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

// Lazy load category images and set fallback placeholders
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.category-img').forEach(el => {
        if (el.tagName === 'IMG') {
            el.loading = 'lazy';
        }
    });
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/home.blade.php ENDPATH**/ ?>