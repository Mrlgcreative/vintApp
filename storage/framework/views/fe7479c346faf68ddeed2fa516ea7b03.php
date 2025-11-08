

<?php $__env->startSection('title', 'VintApp - Fashion Vintage'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap');
    
    * { 
        font-family: 'Inter', sans-serif; 
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    .font-display { font-family: 'Playfair Display', serif; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(60px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    
    .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
    .animate-slide-up { animation: slideUp 1s ease-out forwards; }
    .animate-scale-in { animation: scaleIn 0.6s ease-out forwards; }
    
    .stagger-1 { animation-delay: 0.1s; opacity: 0; }
    .stagger-2 { animation-delay: 0.2s; opacity: 0; }
    .stagger-3 { animation-delay: 0.3s; opacity: 0; }
    .stagger-4 { animation-delay: 0.4s; opacity: 0; }
    
    .hero-text {
        background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .card-hover {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
    }
    
    .image-wrapper {
        overflow: hidden;
        position: relative;
    }
    
    .image-zoom {
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover .image-zoom {
        transform: scale(1.08);
    }
    
    .category-item {
        transition: all 0.3s ease;
    }
    
    .category-item:hover {
        transform: translateX(8px);
    }
    
    .line-animate {
        position: relative;
    }
    
    .line-animate::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 0;
        height: 2px;
        background: #1a1a1a;
        transition: width 0.3s ease;
    }
    
    .line-animate:hover::after {
        width: 100%;
    }
    
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Hero Carousel Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
    
    @keyframes slideInFromLeft {
        0% { opacity: 0; transform: translateX(-100px); }
        100% { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes slideInFromRight {
        0% { opacity: 0; transform: translateX(100px); }
        100% { opacity: 1; transform: translateX(0); }
    }
    
    .carousel-dot {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .carousel-dot:hover {
        transform: scale(1.2);
    }
    
    /* Auto-play indicator */
    .carousel-progress {
        animation: carouselProgress 5s linear infinite;
    }
    
    @keyframes carouselProgress {
        0% { width: 0%; }
        100% { width: 100%; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<!-- Toast Minimal -->
<div id="toast" class="fixed top-8 right-8 z-50 bg-black text-white px-6 py-4 rounded-lg shadow-2xl transform translate-x-[500px] transition-all duration-500 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span id="toastMessage" class="text-sm font-medium">Success</span>
</div>

<div class="min-h-screen bg-white">

    <!-- Hero Section avec Carrousel Dynamique -->
    <section class="relative h-screen overflow-hidden">
        <?php if(isset($heroSlides) && $heroSlides->count() > 0): ?>
            <!-- Carrousel Container -->
            <div class="relative h-full">
                <!-- Slides -->
                <div id="carouselInner" class="flex h-full transition-transform duration-700 ease-in-out" style="width: <?php echo e($heroSlides->count() * 100); ?>%;">
                    <?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="relative w-full h-full flex-shrink-0" style="width: <?php echo e(100 / $heroSlides->count()); ?>%; background-color: <?php echo e($slide->background_color ?? '#6A0DAD'); ?>;">
                            <!-- Content Layout -->
                            <div class="relative z-10 h-full flex items-center">
                                <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center h-full min-h-[600px]">
                                        
                                        <?php if($slide->image_position === 'left'): ?>
                                            <!-- Image à gauche -->
                                            <div class="order-1 lg:order-1 flex justify-center items-center">
                                                <div class="w-full max-w-lg">
                                                    <img src="<?php echo e(Storage::url($slide->image_path)); ?>" 
                                                         alt="<?php echo e($slide->title ?? 'Hero Slide ' . ($index + 1)); ?>" 
                                                         class="w-full h-auto object-contain drop-shadow-2xl rounded-2xl"
                                                         style="max-height: <?php echo e($slide->image_size === 'small' ? '300px' : ($slide->image_size === 'medium' ? '400px' : ($slide->image_size === 'large' ? '500px' : '600px'))); ?>;" />
                                                </div>
                                            </div>
                                            
                                            <!-- Texte à droite -->
                                            <div class="order-2 lg:order-2 text-<?php echo e($slide->text_position ?? 'left'); ?>">
                                                <?php if($slide->subtitle): ?>
                                                    <p class="text-white/90 text-sm sm:text-base font-semibold tracking-wide uppercase mb-4 animate-fade-in">
                                                        <?php echo e($slide->subtitle); ?>

                                                    </p>
                                                <?php endif; ?>
                                                
                                                <h1 class="font-display text-3xl sm:text-5xl lg:text-6xl font-black text-white mb-6 leading-tight animate-slide-up">
                                                    <?php echo e($slide->title ?? 'Vintage Collection'); ?>

                                                </h1>
                                                
                                                <?php if($slide->subtitle): ?>
                                                    <p class="text-lg sm:text-xl text-white/80 mb-8 leading-relaxed animate-fade-in max-w-2xl <?php echo e($slide->text_position === 'center' ? 'mx-auto' : ($slide->text_position === 'right' ? 'ml-auto' : '')); ?>">
                                                        <?php echo e($slide->subtitle); ?>

                                                    </p>
                                                <?php endif; ?>
                                                
                                                <!-- CTA Buttons -->
                                                <div class="flex flex-col sm:flex-row gap-4 animate-scale-in <?php echo e($slide->text_position === 'center' ? 'justify-center' : ($slide->text_position === 'right' ? 'justify-end' : 'justify-start')); ?>">
                                                    <?php if($slide->button_primary_text): ?>
                                                        <a href="<?php echo e($slide->button_primary_url ?? route('items.index')); ?>" 
                                                           class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white text-gray-900 rounded-full font-bold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl">
                                                            <span><?php echo e($slide->button_primary_text); ?></span>
                                                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                            </svg>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <?php if($slide->button_secondary_text): ?>
                                                        <a href="<?php echo e($slide->button_secondary_url ?? '#'); ?>" 
                                                           class="inline-flex items-center justify-center gap-3 px-8 py-4 border-2 border-white text-white rounded-full font-semibold text-lg hover:bg-white hover:text-gray-900 transition-all">
                                                            <?php echo e($slide->button_secondary_text); ?>

                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- Texte à gauche -->
                                            <div class="order-2 lg:order-1 text-<?php echo e($slide->text_position ?? 'left'); ?>">
                                                <?php if($slide->subtitle): ?>
                                                    <p class="text-white/90 text-sm sm:text-base font-semibold tracking-wide uppercase mb-4 animate-fade-in">
                                                        <?php echo e($slide->subtitle); ?>

                                                    </p>
                                                <?php endif; ?>
                                                
                                                <h1 class="font-display text-3xl sm:text-5xl lg:text-6xl font-black text-white mb-6 leading-tight animate-slide-up">
                                                    <?php echo e($slide->title ?? 'Vintage Collection'); ?>

                                                </h1>
                                                
                                                <?php if($slide->subtitle): ?>
                                                    <p class="text-lg sm:text-xl text-white/80 mb-8 leading-relaxed animate-fade-in max-w-2xl <?php echo e($slide->text_position === 'center' ? 'mx-auto' : ($slide->text_position === 'right' ? 'ml-auto' : '')); ?>">
                                                        <?php echo e($slide->subtitle); ?>

                                                    </p>
                                                <?php endif; ?>
                                                
                                                <!-- CTA Buttons -->
                                                <div class="flex flex-col sm:flex-row gap-4 animate-scale-in <?php echo e($slide->text_position === 'center' ? 'justify-center' : ($slide->text_position === 'right' ? 'justify-end' : 'justify-start')); ?>">
                                                    <?php if($slide->button_primary_text): ?>
                                                        <a href="<?php echo e($slide->button_primary_url ?? route('items.index')); ?>" 
                                                           class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white text-gray-900 rounded-full font-bold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl">
                                                            <span><?php echo e($slide->button_primary_text); ?></span>
                                                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                            </svg>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <?php if($slide->button_secondary_text): ?>
                                                        <a href="<?php echo e($slide->button_secondary_url ?? '#'); ?>" 
                                                           class="inline-flex items-center justify-center gap-3 px-8 py-4 border-2 border-white text-white rounded-full font-semibold text-lg hover:bg-white hover:text-gray-900 transition-all">
                                                            <?php echo e($slide->button_secondary_text); ?>

                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <!-- Image à droite -->
                                            <div class="order-1 lg:order-2 flex justify-center items-center">
                                                <div class="w-full max-w-lg">
                                                    <img src="<?php echo e(Storage::url($slide->image_path)); ?>" 
                                                         alt="<?php echo e($slide->title ?? 'Hero Slide ' . ($index + 1)); ?>" 
                                                         class="w-full h-auto object-contain drop-shadow-2xl rounded-2xl"
                                                         style="max-height: <?php echo e($slide->image_size === 'small' ? '300px' : ($slide->image_size === 'medium' ? '400px' : ($slide->image_size === 'large' ? '500px' : '600px'))); ?>;" />
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <!-- Navigation Arrows -->
                <?php if($heroSlides->count() > 1): ?>
                    <button onclick="goToSlide(<?php echo e(($index ?? 0) > 0 ? ($index ?? 0) - 1 : $heroSlides->count() - 1); ?>)" 
                            class="absolute left-8 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-full flex items-center justify-center transition-all z-20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    
                    <button onclick="goToSlide(<?php echo e(($index ?? 0) < $heroSlides->count() - 1 ? ($index ?? 0) + 1 : 0); ?>)" 
                            class="absolute right-8 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-full flex items-center justify-center transition-all z-20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                <?php endif; ?>
                
                <!-- Dots Indicator -->
                <?php if($heroSlides->count() > 1): ?>
                    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-3 z-20">
                        <?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button onclick="goToSlide(<?php echo e($index); ?>)" 
                                    class="carousel-dot w-3 h-3 rounded-full transition-all <?php echo e($index === 0 ? 'bg-white' : 'bg-white/40 hover:bg-white/60'); ?>">
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Scroll Indicator -->
                <div class="absolute bottom-8 right-8 animate-bounce z-20">
                    <div class="w-6 h-10 border-2 border-white/60 rounded-full flex justify-center">
                        <div class="w-1 h-3 bg-white/80 rounded-full mt-2 animate-pulse"></div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Fallback Hero -->
            <div class="relative h-full bg-gradient-to-br from-purple-900 via-pink-800 to-blue-900 flex items-center justify-center">
                <!-- Animated Background -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, #8b5cf6 2px, transparent 0), radial-gradient(circle at 75% 75%, #ec4899 2px, transparent 0); background-size: 100px 100px; animation: float 20s ease-in-out infinite;"></div>
                </div>
                
                <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    <p class="text-purple-300 text-sm sm:text-base font-semibold tracking-wide uppercase mb-4 animate-fade-in">
                        Découvrez Notre
                    </p>
                    
                    <h1 class="font-display text-5xl sm:text-7xl lg:text-8xl font-black text-white mb-6 leading-tight animate-slide-up">
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-purple-300 via-pink-300 to-blue-300">
                            Vintage
                        </span>
                        <span class="block">
                            Collection
                        </span>
                    </h1>
                    
                    <p class="text-xl sm:text-2xl text-gray-200 mb-8 leading-relaxed animate-fade-in max-w-2xl mx-auto">
                        Pièces authentiques et uniques sélectionnées avec passion
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center animate-scale-in">
                        <a href="<?php echo e(route('items.index')); ?>" 
                           class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white text-gray-900 rounded-full font-bold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl">
                            <span>Explorer</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex justify-center gap-12 mt-12 animate-fade-in">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-white">2,5K+</div>
                            <div class="text-sm text-purple-200">Articles</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-white">1,2K+</div>
                            <div class="text-sm text-purple-200">Clients</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Barre de Recherche - Flottante sur Hero -->
    <section id="search-section" class="relative -mt-32 z-40 container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-3 hover:shadow-3xl transition-all duration-500 transform hover:scale-[1.02]">
            <form action="<?php echo e(route('items.index')); ?>" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="search" 
                           name="q" 
                           value="<?php echo e(request('q')); ?>" 
                           placeholder="Rechercher des pièces vintage..." 
                           class="w-full h-16 pl-14 pr-6 rounded-2xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-purple-100 focus:outline-none transition-all text-base font-medium placeholder:text-gray-400" />
                </div>
                
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="toggleFiltersModal()" 
                            class="h-16 px-6 rounded-2xl border-2 border-gray-200 hover:border-purple-300 hover:bg-purple-50 text-gray-700 hover:text-purple-700 transition-all flex items-center gap-2 font-medium group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        <span class="hidden sm:inline">Filtres</span>
                    </button>
                    
                    <button type="submit" 
                            class="h-16 px-8 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl font-bold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-xl hover:shadow-2xl hover:shadow-purple-500/30 transform hover:scale-105">
                        <span class="hidden sm:inline">Rechercher</span>
                        <svg class="w-5 h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Section Catégories - Optimisée -->
    <section class="py-20 lg:py-32 bg-gradient-to-b from-white to-gray-50/50">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête Section -->
            <div class="mb-12">
                <h2 class="font-display text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                    Catégories
                </h2>
            </div>

            <!-- Grille Catégories -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 lg:gap-6">
                <?php
                    $icons = ['👗', '👔', '👠', '👜', '⌚', '💍', '🕶️', '🎒', '👟', '🧥'];
                    $colors = [
                        'from-purple-100 to-purple-200 border-purple-200',
                        'from-pink-100 to-pink-200 border-pink-200',
                        'from-blue-100 to-blue-200 border-blue-200',
                        'from-green-100 to-green-200 border-green-200',
                        'from-yellow-100 to-yellow-200 border-yellow-200',
                        'from-red-100 to-red-200 border-red-200',
                        'from-indigo-100 to-indigo-200 border-indigo-200',
                        'from-teal-100 to-teal-200 border-teal-200',
                        'from-orange-100 to-orange-200 border-orange-200',
                        'from-cyan-100 to-cyan-200 border-cyan-200'
                    ];
                ?>
                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('items.index', ['category' => $category->id])); ?>" 
                       class="group relative bg-white hover:bg-gradient-to-br <?php echo e($colors[$index % count($colors)]); ?> border-2 border-transparent hover:border-opacity-50 rounded-2xl lg:rounded-3xl p-6 lg:p-8 text-center transition-all duration-300 hover:shadow-xl hover:shadow-black/5 hover:-translate-y-2">
                        
                        <!-- Icône -->
                        <div class="text-4xl lg:text-6xl mb-4 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 filter drop-shadow-sm">
                            <?php echo e($icons[$index % count($icons)]); ?>

                        </div>
                        
                        <!-- Texte -->
                        <div class="space-y-1">
                            <h3 class="font-bold text-sm lg:text-base text-gray-900 group-hover:text-gray-800">
                                <?php echo e($category->name); ?>

                            </h3>
                            <p class="text-xs lg:text-sm text-gray-500 group-hover:text-gray-600">
                                <?php echo e($category->items_count ?? rand(15, 150)); ?> articles
                            </p>
                        </div>
                        
                        <!-- Indicateur hover -->
                        <div class="absolute top-3 right-3 w-6 h-6 bg-white rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center transform scale-75 group-hover:scale-100">
                            <svg class="w-3 h-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center py-16">
                        <div class="text-8xl mb-6">📂</div>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Catégories à venir</h3>
                        <p class="text-gray-400">Nos catégories seront bientôt disponibles</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Section Produits Vedettes - Optimisée -->
    <section id="collection" class="py-20 lg:py-32 bg-white">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête Section -->
            <div class="mb-12">
                <h2 class="font-display text-3xl lg:text-4xl font-bold text-gray-900">
                    Articles Récents
                </h2>
            </div>

            <!-- Grille Produits -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
                <?php $__empty_1 = true; $__currentLoopData = $latestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $firstImage = is_string($item->images) ? json_decode($item->images, true)[0] ?? null : ($item->images[0] ?? null);
                        $isNew = $item->created_at->gt(now()->subDays(7));
                        $isFeatured = rand(0, 3) === 0;
                    ?>
                    <article class="group relative bg-white rounded-3xl overflow-hidden card-hover border-2 border-gray-100 hover:border-purple-200 transition-all duration-300">
                        <!-- Image Container -->
                        <div class="image-wrapper aspect-[3/4] bg-gradient-to-br from-gray-100 to-gray-200 relative overflow-hidden">
                            <?php if($firstImage && Storage::disk('public')->exists($firstImage)): ?>
                                <img src="<?php echo e(Storage::url($firstImage)); ?>" 
                                     alt="<?php echo e($item->name); ?>" 
                                     class="w-full h-full object-cover image-zoom" 
                                     loading="lazy" />
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-100 to-pink-100">
                                    <span class="text-6xl text-gray-400 filter drop-shadow-sm">📷</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Overlay Actions -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-3">
                                <button class="w-12 h-12 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all duration-200 hover:bg-white">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button class="w-12 h-12 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all duration-200 hover:bg-white">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                                <button onclick="addToCart(<?php echo e($item->id); ?>)" 
                                        class="w-12 h-12 bg-gray-900 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all duration-200 hover:bg-black">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <?php if($isNew): ?>
                                    <span class="px-3 py-1 bg-emerald-500 text-white text-xs font-bold rounded-full shadow-lg">
                                        NOUVEAU
                                    </span>
                                <?php endif; ?>
                                <?php if($isFeatured): ?>
                                    <span class="px-3 py-1 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-bold rounded-full shadow-lg">
                                        ⭐ VEDETTE
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Prix -->
                            <div class="absolute top-4 right-4">
                                <span class="px-4 py-2 bg-gray-900 text-white rounded-full text-sm font-bold shadow-lg">
                                    <?php echo e($item->formatted_price); ?>

                                </span>
                            </div>
                        </div>
                        
                        <!-- Contenu -->
                        <div class="p-5 lg:p-6">
                            <div class="space-y-3">
                                <h3 class="font-bold text-base lg:text-lg text-gray-900 line-clamp-2 min-h-[3rem] leading-tight">
                                    <?php echo e($item->name); ?>

                                </h3>
                                
                                <div class="flex items-center justify-between">
                                    <span class="px-3 py-1 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 rounded-full text-xs font-semibold">
                                        <?php echo e($item->condition ?? 'Excellent'); ?>

                                    </span>
                                    
                                    <div class="flex items-center gap-1 text-xs text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span><?php echo e($item->created_at->diffForHumans()); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Lien invisible -->
                        <a href="<?php echo e(route('items.show', $item)); ?>" class="absolute inset-0 z-10" aria-label="Voir <?php echo e($item->name); ?>"></a>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center py-20">
                        <div class="text-6xl mb-4">🛍️</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Aucun article</h3>
                        <a href="<?php echo e(route('items.create') ?? '#'); ?>" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 text-white rounded-full font-medium hover:bg-gray-800 transition-all">
                            <span>Ajouter</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if($latestItems && $latestItems->count() > 0): ?>
                <div class="text-center mt-12">
                    <a href="<?php echo e(route('items.index')); ?>" 
                       class="inline-flex items-center gap-2 px-8 py-4 bg-gray-900 text-white rounded-full font-medium hover:bg-gray-800 transition-all">
                        <span>Voir Plus</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Features - Minimal -->
    <section class="max-w-[1400px] mx-auto px-6 lg:px-12 py-24">
        <div class="grid md:grid-cols-4 gap-12">
            <div class="text-center">
                <div class="w-16 h-16 bg-black rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                </div>
                <h3 class="font-semibold mb-2">Free Shipping</h3>
                <p class="text-sm text-gray-500">On orders over €50</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-black rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold mb-2">Secure Payment</h3>
                <p class="text-sm text-gray-500">100% protected</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-black rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <h3 class="font-semibold mb-2">Easy Returns</h3>
                <p class="text-sm text-gray-500">14 days guarantee</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-black rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="font-semibold mb-2">Verified</h3>
                <p class="text-sm text-gray-500">100% authentic</p>
            </div>
        </div>
    </section>

    <!-- CTA Section - Bold & Minimal -->
    <section class="max-w-[1400px] mx-auto px-6 lg:px-12 py-24">
        <div class="bg-black text-white rounded-3xl p-12 lg:p-20 text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            </div>
            
            <div class="relative max-w-3xl mx-auto space-y-8">
                <h2 class="font-display text-4xl lg:text-6xl font-bold leading-tight">
                    Ready to Build Your
                    <span class="block italic text-gray-400">Vintage Collection?</span>
                </h2>
                <p class="text-lg text-gray-400 max-w-2xl mx-auto">
                    Join thousands of vintage lovers and discover unique pieces at unbeatable prices
                </p>
                <div class="flex flex-wrap gap-4 justify-center pt-4">
                    <a href="<?php echo e(route('items.index')); ?>" 
                       class="inline-flex items-center gap-3 px-8 py-4 bg-white text-black rounded-full font-medium hover:bg-gray-100 transition-all">
                        Start Shopping
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    <a href="<?php echo e(route('items.create') ?? '#'); ?>" 
                       class="inline-flex items-center gap-3 px-8 py-4 border-2 border-white text-white rounded-full font-medium hover:bg-white hover:text-black transition-all">
                        Sell Your Items
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- Filter Modal - Minimal -->
<div id="filterModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-8 py-6 flex items-center justify-between z-10 rounded-t-3xl">
            <h3 class="text-2xl font-bold">Filters</h3>
            <button onclick="toggleFiltersModal()" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form action="<?php echo e(route('items.index')); ?>" method="GET" id="filterForm" class="p-8 space-y-8">
            <div>
                <label class="block text-sm font-semibold mb-3">Category</label>
                <select name="category" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-black focus:outline-none transition-all">
                    <option value="">All Categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                            <?php echo e($category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold mb-3">Price Range</label>
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
                <label class="block text-sm font-semibold mb-3">Sort By</label>
                <select name="sort" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-black focus:outline-none transition-all">
                    <option value="">Relevance</option>
                    <option value="recent" <?php echo e(request('sort') === 'recent' ? 'selected' : ''); ?>>Newest</option>
                    <option value="popular" <?php echo e(request('sort') === 'popular' ? 'selected' : ''); ?>>Popular</option>
                    <option value="price_low" <?php echo e(request('sort') === 'price_low' ? 'selected' : ''); ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo e(request('sort') === 'price_high' ? 'selected' : ''); ?>>Price: High to Low</option>
                </select>
            </div>
            
            <div class="flex gap-4 pt-4 sticky bottom-0 bg-white pb-2">
                <button type="button" 
                        onclick="resetFilters()" 
                        class="flex-1 px-6 py-4 border-2 border-gray-200 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    Reset
                </button>
                <button type="submit" 
                        class="flex-1 px-6 py-4 bg-black text-white rounded-xl font-semibold hover:bg-gray-900 transition-all">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Hero Carousel Dynamique
<?php if(isset($heroSlides) && $heroSlides->count() > 1): ?>
let currentSlide = 0;
const totalSlides = <?php echo e($heroSlides->count()); ?>;
let autoPlayInterval;
let isTransitioning = false;

function goToSlide(index) {
    if (isTransitioning) return;
    
    isTransitioning = true;
    currentSlide = index;
    
    const inner = document.getElementById('carouselInner');
    if (inner) {
        inner.style.transform = `translateX(-${(currentSlide * 100) / totalSlides}%)`;
        
        // Update dots
        updateDots();
        
        // Reset transition lock
        setTimeout(() => {
            isTransitioning = false;
        }, 700);
    }
}

function updateDots() {
    document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
        if (i === currentSlide) {
            dot.classList.add('bg-white');
            dot.classList.remove('bg-white/40');
        } else {
            dot.classList.remove('bg-white');
            dot.classList.add('bg-white/40');
        }
    });
}

function nextSlide() {
    const next = (currentSlide + 1) % totalSlides;
    goToSlide(next);
}

function prevSlide() {
    const prev = (currentSlide - 1 + totalSlides) % totalSlides;
    goToSlide(prev);
}

// Auto-play functionality
function startAutoPlay() {
    autoPlayInterval = setInterval(nextSlide, 6000);
}

function stopAutoPlay() {
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
    }
}

// Pause on hover
const heroSection = document.querySelector('section');
if (heroSection) {
    heroSection.addEventListener('mouseenter', stopAutoPlay);
    heroSection.addEventListener('mouseleave', startAutoPlay);
}

// Keyboard navigation
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') {
        stopAutoPlay();
        prevSlide();
        setTimeout(startAutoPlay, 3000);
    } else if (e.key === 'ArrowRight') {
        stopAutoPlay();
        nextSlide();
        setTimeout(startAutoPlay, 3000);
    }
});

// Touch/Swipe support
let touchStartX = 0;
let touchEndX = 0;

heroSection.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
    stopAutoPlay();
});

heroSection.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
    setTimeout(startAutoPlay, 3000);
});

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            nextSlide(); // Swipe left = next slide
        } else {
            prevSlide(); // Swipe right = previous slide
        }
    }
}

// Start auto-play on page load
document.addEventListener('DOMContentLoaded', () => {
    updateDots();
    startAutoPlay();
});

<?php else: ?>
// Single slide or no slides
document.addEventListener('DOMContentLoaded', () => {
    console.log('VintApp Hero: Single slide mode or no slides available');
});
<?php endif; ?>

// Toast Notification
function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    if (toast && toastMessage) {
        toastMessage.textContent = message;
        toast.style.transform = 'translateX(0)';
        setTimeout(() => {
            toast.style.transform = 'translateX(500px)';
        }, 3000);
    }
}

function closeToast() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.style.transform = 'translateX(500px)';
    }
}

// Filter Modal
function toggleFiltersModal() {
    const modal = document.getElementById('filterModal');
    if (modal) {
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
        
        // Prevent body scroll when modal is open
        if (!modal.classList.contains('hidden')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('filterModal');
        if (modal && !modal.classList.contains('hidden')) {
            toggleFiltersModal();
        }
    }
});

// Close modal on outside click
document.getElementById('filterModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        toggleFiltersModal();
    }
});

function resetFilters() {
    document.getElementById('filterForm').reset();
}

// Add to Cart
function addToCart(itemId) {
    showToast('Added to cart successfully!');
    console.log('Item added to cart:', itemId);
    
    // Animate button
    const button = event.target.closest('button');
    if (button) {
        button.classList.add('scale-90');
        setTimeout(() => {
            button.classList.remove('scale-90');
        }, 200);
    }
}

// Smooth Scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Intersection Observer for scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe sections on page load
document.addEventListener('DOMContentLoaded', () => {
    // Observe all sections except the first one (hero)
    const sections = document.querySelectorAll('section:not(:first-child)');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        observer.observe(section);
    });
    
    // Stagger animation for category items
    const categoryItems = document.querySelectorAll('.category-item');
    categoryItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.05}s`;
    });
    
    // Stagger animation for product cards
    const productCards = document.querySelectorAll('.card-hover');
    productCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.05}s`;
    });
});

// Lazy load images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                imageObserver.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Parallax effect for hero section (optional)
let ticking = false;
function updateParallax() {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.parallax');
    
    parallaxElements.forEach(element => {
        const speed = element.dataset.speed || 0.5;
        element.style.transform = `translateY(${scrolled * speed}px)`;
    });
    
    ticking = false;
}

window.addEventListener('scroll', function() {
    if (!ticking) {
        window.requestAnimationFrame(updateParallax);
        ticking = true;
    }
});

// Page load animations
window.addEventListener('load', () => {
    document.body.classList.add('loaded');
});

// Add hover effect to product cards
document.querySelectorAll('.card-hover').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.zIndex = '10';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.zIndex = '1';
    });
});

// Shopping cart animation (if you have a cart icon in nav)
function animateCartIcon() {
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.classList.add('animate-bounce');
        setTimeout(() => {
            cartIcon.classList.remove('animate-bounce');
        }, 600);
    }
}

// Call this when adding to cart
function addToCartWithAnimation(itemId) {
    addToCart(itemId);
    animateCartIcon();
}

// Handle favorite/wishlist toggle
function toggleFavorite(itemId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const button = event.currentTarget;
    const icon = button.querySelector('svg');
    
    // Toggle filled state
    if (icon.classList.contains('fill-current')) {
        icon.classList.remove('fill-current', 'text-red-500');
        showToast('Removed from favorites');
    } else {
        icon.classList.add('fill-current', 'text-red-500');
        showToast('Added to favorites');
    }
    
    console.log('Toggle favorite for item:', itemId);
}

// Search suggestions (optional enhancement)
const searchInput = document.querySelector('input[name="q"]');
if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value;
        
        if (query.length > 2) {
            searchTimeout = setTimeout(() => {
                // Here you could fetch search suggestions via AJAX
                console.log('Searching for:', query);
            }, 300);
        }
    });
}

// Add to cart with quantity (optional)
function addToCartWithQuantity(itemId, quantity = 1) {
    showToast(`${quantity} item(s) added to cart`);
    console.log(`Added ${quantity} of item ${itemId} to cart`);
}

// Quick view modal (optional feature)
function quickView(itemId) {
    console.log('Quick view for item:', itemId);
    // You can implement a quick view modal here
}

// Back to top button (optional)
const backToTopButton = document.createElement('button');
backToTopButton.innerHTML = `
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
    </svg>
`;
backToTopButton.className = 'fixed bottom-8 right-8 w-12 h-12 bg-black text-white rounded-full shadow-lg opacity-0 transition-opacity hover:bg-gray-900 z-40';
backToTopButton.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
document.body.appendChild(backToTopButton);

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 500) {
        backToTopButton.style.opacity = '1';
    } else {
        backToTopButton.style.opacity = '0';
    }
});

// Log page view analytics (optional)
console.log('VintApp Home Page Loaded', {
    timestamp: new Date().toISOString(),
    items_count: <?php echo e($latestItems->count() ?? 0); ?>,
    categories_count: <?php echo e($categories->count() ?? 0); ?>

});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/home.blade.php ENDPATH**/ ?>