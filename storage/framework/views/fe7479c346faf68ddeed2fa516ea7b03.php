

<?php $__env->startSection('content'); ?>
<!-- Toast notification Bootstrap -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11000">
    <div id="mainToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mainToastBody">
                Notification
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Container principal avec Tailwind -->
<div class="min-h-screen bg-white pb-20">

    <!-- Barre de recherche -->
   

    <!-- Hero Carrousel -->
    <?php if(isset($heroSlides) && $heroSlides->count() > 0): ?>
        <div id="heroCarousel" class="carousel slide mx-2 mx-md-4 my-4 my-md-6 position-relative hero-section-shadow" data-bs-ride="carousel" data-bs-interval="5000">
            <!-- Indicateurs en haut à gauche (traits) -->
            <div class="carousel-indicators-custom">
                <?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo e($index); ?>" 
                        class="indicator-line <?php echo e($index === 0 ? 'active' : ''); ?>" 
                        aria-current="<?php echo e($index === 0 ? 'true' : 'false'); ?>" 
                        aria-label="Slide <?php echo e($index + 1); ?>"></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Slides -->
            <div class="carousel-inner rounded-3 overflow-hidden">
                <?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $bgColor = $slide->background_color ?? '#6A0DAD';
                        $textPos = $slide->text_position ?? 'left';
                        $imgPos = $slide->image_position ?? 'right';
                        $imgSize = $slide->image_size ?? 'medium';
                        
                        // Mapping des tailles
                        $sizeMapping = [
                            'small' => ['mobile' => '180px', 'desktop' => '250px'],
                            'medium' => ['mobile' => '250px', 'desktop' => '350px'],
                            'large' => ['mobile' => '300px', 'desktop' => '450px'],
                            'full' => ['mobile' => '350px', 'desktop' => '500px']
                        ];
                        
                        $heights = $sizeMapping[$imgSize] ?? $sizeMapping['medium'];
                    ?>
                    
                    <div class="carousel-item <?php echo e($index === 0 ? 'active' : ''); ?> hero-slide-item" 
                         style="background-color: <?php echo e($bgColor); ?>;">
                        <div class="container-fluid px-3 px-md-5 py-4 py-md-5">
                            <div class="row align-items-center g-3 g-md-4">
                                <?php if($imgPos === 'left'): ?>
                                    <!-- Image à gauche (toujours) -->
                                    <div class="col-6 col-md-6 text-center order-1">
                                        <div class="hero-image-wrapper">
                                            <img src="<?php echo e(Storage::url($slide->image_path)); ?>" 
                                                 alt="<?php echo e($slide->title); ?>"
                                                 class="img-fluid hero-image hero-image-<?php echo e($imgSize); ?>"
                                                 data-size="<?php echo e($imgSize); ?>">
                                        </div>
                                    </div>
                                    <!-- Texte à droite (toujours) -->
                                    <div class="col-6 col-md-6 text-center text-md-<?php echo e($textPos); ?> order-2">
                                        <h1 class="text-white fw-bold mb-2 mb-md-3 hero-title">
                                            <?php echo e($slide->title); ?>

                                        </h1>
                                        <?php if($slide->subtitle): ?>
                                            <p class="text-white mb-3 mb-md-4 hero-subtitle">
                                                <?php echo e($slide->subtitle); ?>

                                            </p>
                                        <?php endif; ?>
                                        <div class="d-flex gap-2 gap-md-3 flex-wrap justify-content-center justify-content-md-<?php echo e($textPos === 'center' ? 'center' : ($textPos === 'right' ? 'end' : 'start')); ?>">
                                            <?php if($slide->button_primary_text && $slide->button_primary_url): ?>
                                                <a href="<?php echo e($slide->button_primary_url); ?>" 
                                                   class="btn btn-light btn-hero px-4 px-md-5 py-2 py-md-3 rounded-pill fw-semibold shadow-lg hover-lift">
                                                    <?php echo e($slide->button_primary_text); ?>

                                                </a>
                                            <?php endif; ?>
                                            <?php if($slide->button_secondary_text && $slide->button_secondary_url): ?>
                                                <a href="<?php echo e($slide->button_secondary_url); ?>" 
                                                   class="btn btn-outline-light btn-hero px-4 px-md-5 py-2 py-md-3 rounded-pill fw-semibold">
                                                    <?php echo e($slide->button_secondary_text); ?>

                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Texte à gauche (toujours) -->
                                    <div class="col-6 col-md-6 text-center text-md-<?php echo e($textPos); ?> order-1">
                                        <h1 class="text-white fw-bold mb-2 mb-md-3 hero-title">
                                            <?php echo e($slide->title); ?>

                                        </h1>
                                        <?php if($slide->subtitle): ?>
                                            <p class="text-white mb-3 mb-md-4 hero-subtitle">
                                                <?php echo e($slide->subtitle); ?>

                                            </p>
                                        <?php endif; ?>
                                        <div class="d-flex gap-2 gap-md-3 flex-wrap justify-content-center justify-content-md-<?php echo e($textPos === 'center' ? 'center' : ($textPos === 'right' ? 'end' : 'start')); ?>">
                                            <?php if($slide->button_primary_text && $slide->button_primary_url): ?>
                                                <a href="<?php echo e($slide->button_primary_url); ?>" 
                                                   class="btn btn-light btn-hero px-4 px-md-5 py-2 py-md-3 rounded-pill fw-semibold shadow-lg hover-lift">
                                                    <?php echo e($slide->button_primary_text); ?>

                                                </a>
                                            <?php endif; ?>
                                            <?php if($slide->button_secondary_text && $slide->button_secondary_url): ?>
                                                <a href="<?php echo e($slide->button_secondary_url); ?>" 
                                                   class="btn btn-outline-light btn-hero px-4 px-md-5 py-2 py-md-3 rounded-pill fw-semibold">
                                                    <?php echo e($slide->button_secondary_text); ?>

                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Image à droite (toujours) -->
                                    <div class="col-6 col-md-6 text-center order-2">
                                        <div class="hero-image-wrapper">
                                            <img src="<?php echo e(Storage::url($slide->image_path)); ?>" 
                                                 alt="<?php echo e($slide->title); ?>"
                                                 class="img-fluid hero-image hero-image-<?php echo e($imgSize); ?>"
                                                 data-size="<?php echo e($imgSize); ?>">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <style>
            /* Ombre pour la section hero */
            .hero-section-shadow {
                box-shadow: 0 8px 32px rgba(106, 13, 173, 0.15), 
                            0 4px 12px rgba(0, 0, 0, 0.08);
                border-radius: 16px;
                transition: box-shadow 0.3s ease;
            }
            
            .hero-section-shadow:hover {
                box-shadow: 0 12px 40px rgba(106, 13, 173, 0.2), 
                            0 6px 16px rgba(0, 0, 0, 0.1);
            }
            
            /* Indicateurs carrousel en haut à gauche (traits) */
            .carousel-indicators-custom {
                position: absolute;
                top: 1rem;
                left: 1.5rem;
                z-index: 15;
                display: flex;
                flex-direction: row;
                gap: 0.5rem;
                margin: 0;
                padding: 0;
                list-style: none;
            }
            
            .indicator-line {
                width: 40px;
                height: 3px;
                padding: 0;
                margin: 0;
                background-color: rgba(255, 255, 255, 0.4);
                border: none;
                border-radius: 2px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .indicator-line:hover {
                background-color: rgba(255, 255, 255, 0.7);
            }
            
            .indicator-line.active {
                background-color: rgba(255, 255, 255, 1);
                width: 50px;
            }
            
            /* Responsive pour mobile */
            @media (max-width: 767.98px) {
                .carousel-indicators-custom {
                    top: 0.75rem;
                    left: 1rem;
                    gap: 0.4rem;
                }
                
                .indicator-line {
                    width: 30px;
                    height: 2.5px;
                }
                
                .indicator-line.active {
                    width: 40px;
                }
            }
            
            /* Styles responsive optimisés pour le carrousel hero */
            .hero-slide-item {
                min-height: 200px;
                transition: background-color 0.3s ease;
            }
            
            .hero-title {
                font-size: 1.25rem;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
                line-height: 1.2;
            }
            
            .hero-subtitle {
                font-size: 0.85rem;
                opacity: 0.95;
                line-height: 1.3;
            }
            
            .hero-image-wrapper {
                padding: 0.5rem 0;
                display: flex;
                align-items: flex-end;
                justify-content: center;
            }
            
            .hero-image {
                max-width: 100%;
                height: auto;
                object-fit: contain;
                transition: transform 0.3s ease;
                margin-top: 2rem;
            }
            
            /* Tailles d'images dynamiques */
            .hero-image-small {
                max-height: 120px;
            }
            
            .hero-image-medium {
                max-height: 160px;
            }
            
            .hero-image-large {
                max-height: 200px;
            }
            
            .hero-image-full {
                max-height: 220px;
            }
            
            .btn-hero {
                transition: all 0.3s ease;
            }
            
            .hover-lift {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .hover-lift:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 16px rgba(0,0,0,0.3) !important;
            }
            
            /* Indicateurs carrousel */
            #heroCarousel .carousel-indicators button {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                margin: 0 4px;
                opacity: 0.5;
                transition: all 0.3s ease;
            }

            #heroCarousel .carousel-indicators button.active {
                background-color: white;
                opacity: 1;
                transform: scale(1.3);
            }
            
            /* Media queries pour mobile */
            @media (max-width: 767.98px) {
                .hero-slide-item {
                    min-height: auto;
                    padding: 0.75rem 0 !important;
                }
                
                .hero-title {
                    font-size: 1.1rem !important;
                }
                
                .hero-subtitle {
                    font-size: 0.8rem !important;
                }
                
                .hero-image-small {
                    max-height: 100px !important;
                }
                
                .hero-image-medium {
                    max-height: 140px !important;
                }
                
                .hero-image-large {
                    max-height: 170px !important;
                }
                
                .hero-image-full {
                    max-height: 100px !important;
                }
                
                .hero-image-wrapper {
                    padding: 0 0 0.25rem 0;
                    align-items: flex-end;
                }
                
                .hero-image {
                    margin-top: 1rem !important;
                }
                
                /* Boutons plus petits sur mobile */
                .btn-hero {
                    font-size: 0.8rem;
                    padding: 0.4rem 1rem !important;
                }
            }
            
            /* Media queries pour tablette */
            @media (min-width: 768px) and (max-width: 991.98px) {
                .hero-slide-item {
                    min-height: 220px;
                }
                
                .hero-title {
                    font-size: 1.5rem !important;
                }
                
                .hero-subtitle {
                    font-size: 0.9rem !important;
                }
                
                .hero-image-small {
                    max-height: 140px !important;
                }
                
                .hero-image-medium {
                    max-height: 180px !important;
                }
                
                .hero-image-large {
                    max-height: 220px !important;
                }
                
                .hero-image-full {
                    max-height: 250px !important;
                }
            }
            
            /* Media queries pour desktop */
            @media (min-width: 992px) {
                .hero-slide-item {
                    min-height: 260px;
                }
                
                .hero-title {
                    font-size: 1.75rem !important;
                }
                
                .hero-subtitle {
                    font-size: 1rem !important;
                }
                
                .hero-image-small {
                    max-height: 160px !important;
                }
                
                .hero-image-medium {
                    max-height: 220px !important;
                }
                
                .hero-image-large {
                    max-height: 280px !important;
                }
                
                .hero-image-full {
                    max-height: 320px !important;
                }
                
                .hero-image:hover {
                    transform: scale(1.02);
                }
            }
        </style>
    <?php else: ?>
        <!-- Fallback Hero Banner -->
        <div class="mx-2 mx-md-4 my-4 my-md-6 rounded-3 overflow-hidden shadow-lg">
            <div class="position-relative" style="height: 300px; background: linear-gradient(135deg, #6A0DAD 0%, #6ECEDA 100%);">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-8 col-lg-6">
                                <div class="text-white">
                                    <h1 class="display-4 fw-bold mb-3">
                                        <?php echo e($heroSettings['title'] ?? 'Bienvenue sur VintApp'); ?>

                                    </h1>
                                    <p class="lead mb-4 opacity-90">
                                        <?php echo e($heroSettings['subtitle'] ?? 'La marketplace de confiance pour acheter et vendre'); ?>

                                    </p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <?php if(auth()->guard()->check()): ?>
                                            <a href="<?php echo e(route('items.create')); ?>" 
                                               class="btn btn-light btn-lg px-5 rounded-pill fw-semibold shadow-lg">
                                                <i class="fas fa-plus me-2"></i><?php echo e($heroSettings['button_primary'] ?? 'Vendre'); ?>

                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('register')); ?>" 
                                               class="btn btn-light btn-lg px-5 rounded-pill fw-semibold shadow-lg">
                                                <i class="fas fa-user-plus me-2"></i>Commencer
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('items.index')); ?>" 
                                           class="btn btn-outline-light btn-lg px-5 rounded-pill fw-semibold">
                                            <i class="fas fa-search me-2"></i><?php echo e($heroSettings['button_secondary'] ?? 'Explorer'); ?>

                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Catégories populaires -->
    <section class="px-3 px-md-4 py-3 py-md-6">
        <div class="d-flex justify-content-between align-items-center mb-2 mb-md-4">
            <h2 class="h6 h4-md fw-bold text-gray-800 mb-0">
                <i class="fas fa-layer-group text-purple-600 me-2"></i>
                Catégories
            </h2>
        </div>
        <!-- Mobile: Défilement horizontal -->
        <div class="d-md-none">
            <div class="d-flex gap-2 overflow-auto pb-2 categories-scroll">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('items.index', ['category' => $category->id])); ?>" 
                       class="card text-decoration-none border-0 shadow-sm category-card-mobile flex-shrink-0"
                       style="background: linear-gradient(135deg, #f3e8ff 0%, #e0f2fe 100%);">
                        <div class="card-body text-center p-2">
                            <div class="category-icon-mobile mb-1">
                                <i class="fas fa-<?php echo e($category->icon ?? 'tag'); ?> text-purple-600"></i>
                            </div>
                            <h6 class="category-title-mobile text-gray-800 fw-semibold mb-0">
                                <?php echo e($category->name); ?>

                            </h6>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <!-- Desktop: Grille -->
        <div class="d-none d-md-block">
            <div class="row row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col">
                        <a href="<?php echo e(route('items.index', ['category' => $category->id])); ?>" 
                           class="card text-decoration-none h-100 border-0 shadow-sm hover-card category-card"
                           style="background: linear-gradient(135deg, #f3e8ff 0%, #e0f2fe 100%);">
                            <div class="card-body text-center p-3">
                                <div class="category-icon-wrapper mb-2">
                                    <i class="fas fa-<?php echo e($category->icon ?? 'tag'); ?> text-purple-600"></i>
                                </div>
                                <h6 class="card-title mb-1 text-gray-800 fw-semibold category-title">
                                    <?php echo e($category->name); ?>

                                </h6>
                                <small class="text-muted category-count"><?php echo e($category->items_count ?? 0); ?> articles</small>
                            </div>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    <!-- Filtres -->
    <section class="px-3 px-md-4 py-2 mb-4">
        <div class="d-flex gap-2 overflow-auto pb-2 flex-nowrap filter-section-wrapper" 
             style="scrollbar-width: none; -ms-overflow-style: none;">
            <a href="<?php echo e(route('items.index')); ?>" 
               class="btn <?php echo e(!request('filter') ? 'btn-purple' : 'btn-outline-purple'); ?> btn-sm rounded-pill px-4 fw-medium flex-shrink-0 text-decoration-none">
                <i class="fas fa-star me-1"></i>Tous
            </a>
            <a href="<?php echo e(route('items.index', ['sort' => 'recent'])); ?>" 
               class="btn <?php echo e(request('sort') == 'recent' ? 'btn-purple' : 'btn-outline-purple'); ?> btn-sm rounded-pill px-4 fw-medium flex-shrink-0 text-decoration-none">
                <i class="fas fa-sparkles me-1"></i>Nouveautés
            </a>
            <a href="<?php echo e(route('items.index', ['sort' => 'popular'])); ?>" 
               class="btn <?php echo e(request('sort') == 'popular' ? 'btn-purple' : 'btn-outline-purple'); ?> btn-sm rounded-pill px-4 fw-medium flex-shrink-0 text-decoration-none">
                <i class="fas fa-fire me-1"></i>Populaires
            </a>
            <a href="<?php echo e(route('items.index', ['condition' => 'new'])); ?>" 
               class="btn <?php echo e(request('condition') == 'new' ? 'btn-purple' : 'btn-outline-purple'); ?> btn-sm rounded-pill px-4 fw-medium flex-shrink-0 text-decoration-none">
                <i class="fas fa-tag me-1"></i>Neuf
            </a>
        </div>
    </section>

    <!-- Articles populaires -->
    <section class="px-3 px-md-4 py-4 py-md-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold text-gray-800 mb-0">
                <i class="fas fa-box text-purple-600 me-2"></i>
                Derniers articles
            </h2>
            <a href="<?php echo e(route('items.index')); ?>" 
               class="btn btn-sm btn-outline-purple rounded-pill px-4 fw-semibold">
                Voir tout <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
            <?php $__empty_1 = true; $__currentLoopData = $latestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col">
                    <div class="card h-100 border-0 item-card-shadow hover-card-item position-relative">
                        <!-- Image produit -->
                        <div class="position-relative item-image-wrapper" style="padding-top: 100%; overflow: hidden;">
                            <?php
                                $imgPath = ($item->images && count($item->images) > 0) ? $item->images[0] : null;
                            ?>
                            <?php if($imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)): ?>
                                <img src="<?php echo e(Storage::url($imgPath)); ?>" 
                                     alt="<?php echo e($item->name); ?>" 
                                     class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
                            <?php else: ?>
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-center bg-light">
                                    <i class="fas fa-image fs-1 text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            <!-- Badge prix -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-purple-600 text-white rounded-pill px-3 py-2 shadow-sm fw-bold">
                                    <?php echo e(number_format($item->price)); ?> <?php echo e($item->currency); ?>

                                </span>
                            </div>
                        </div>
                        
                        <!-- Info produit -->
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2 fw-semibold text-gray-800" 
                                style="font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                <?php echo e($item->name); ?>

                            </h6>
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <span class="badge bg-purple-100 text-purple-600 rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                    <?php echo e($item->condition); ?>

                                </span>
                                <button class="btn btn-sm btn-cyan rounded-circle p-0 d-flex align-items-center justify-content-center" 
                                        style="width: 28px; height: 28px;">
                                    <i class="fas fa-plus" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                                <i class="far fa-clock me-1"></i><?php echo e($item->created_at->diffForHumans()); ?>

                            </small>
                        </div>
                        <a href="<?php echo e(route('items.show', $item)); ?>" class="stretched-link"></a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-box-open text-gray-300" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="h5 fw-semibold text-gray-600 mb-2">Aucun article disponible</h4>
                        <p class="text-gray-500 mb-4">Soyez le premier à publier un article !</p>
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('items.create')); ?>" 
                               class="btn btn-purple btn-lg rounded-pill px-5 fw-semibold shadow">
                                <i class="fas fa-plus me-2"></i>Publier un article
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('register')); ?>" 
                               class="btn btn-purple btn-lg rounded-pill px-5 fw-semibold shadow">
                                <i class="fas fa-user-plus me-2"></i>Rejoindre VintApp
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
/* ===== BARRE DE RECHERCHE & FILTRES - UI MODERNE ===== */

/* Container principal */
.search-wrapper-home {
    max-width: 720px;
    margin: 0 auto;
    padding: 0.5rem;
    display: flex;
    gap: 0.875rem;
    align-items: stretch;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 
                0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(106, 13, 173, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.search-wrapper-home:hover {
    box-shadow: 0 6px 28px rgba(106, 13, 173, 0.12), 
                0 2px 6px rgba(0, 0, 0, 0.06);
    border-color: rgba(106, 13, 173, 0.15);
}

/* Formulaire de recherche */
.search-form-home {
    flex: 1;
    position: relative;
}

/* Champ de recherche */
.search-input-home {
    width: 100%;
    padding: 0.875rem 130px 0.875rem 1.25rem;
    border-radius: 12px;
    border: 2px solid transparent;
    background: #f8f9fa;
    font-size: 0.9375rem;
    font-weight: 500;
    color: #1f2937;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.01em;
}

.search-input-home::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

.search-input-home:hover {
    background: #ffffff;
    border-color: rgba(106, 13, 173, 0.1);
}

.search-input-home:focus {
    background: #ffffff;
    border-color: #6A0DAD;
    box-shadow: 0 0 0 4px rgba(106, 13, 173, 0.08);
    outline: none;
}

/* Bouton de recherche */
.search-btn-home {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    padding: 0.625rem 1.375rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.02em;
    background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
    border: none;
    color: white;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(106, 13, 173, 0.25);
}

.search-btn-home:hover {
    background: linear-gradient(135deg, #5a0b92 0%, #7209a8 100%);
    transform: translateY(-50%) translateY(-1px);
    box-shadow: 0 4px 14px rgba(106, 13, 173, 0.35);
}

.search-btn-home:active {
    transform: translateY(-50%) translateY(0);
    box-shadow: 0 2px 6px rgba(106, 13, 173, 0.3);
}

/* Bouton de filtre */
.filter-btn-home {
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9375rem;
    white-space: nowrap;
    letter-spacing: 0.02em;
    background: #f8f9fa;
    border: 2px solid transparent;
    color: #6A0DAD;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
}

.filter-btn-home::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(106, 13, 173, 0.1), rgba(139, 13, 199, 0.1));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.filter-btn-home:hover {
    background: #ffffff;
    border-color: #6A0DAD;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(106, 13, 173, 0.15);
}

.filter-btn-home:hover::before {
    opacity: 1;
}

.filter-btn-home:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(106, 13, 173, 0.1);
}

.filter-btn-home i {
    font-size: 1rem;
    position: relative;
    z-index: 1;
}

.filter-btn-home span {
    position: relative;
    z-index: 1;
}

/* Responsive - Tablette */
@media (max-width: 991.98px) {
    .search-wrapper-home {
        max-width: 100%;
        margin: 0 1rem;
    }
}

/* Responsive - Mobile */
@media (max-width: 767.98px) {
    .search-wrapper-home {
        padding: 0.4rem;
        gap: 0.5rem;
        margin: 0 0.75rem;
        border-radius: 14px;
    }
    
    .search-input-home {
        padding: 0.75rem 70px 0.75rem 1rem;
        font-size: 0.875rem;
        border-radius: 10px;
    }
    
    .search-btn-home {
        padding: 0.5rem 0.875rem;
        right: 5px;
        font-size: 0.8125rem;
        border-radius: 8px;
    }
    
    .filter-btn-home {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        border-radius: 10px;
        min-width: 48px;
        justify-content: center;
    }
    
    .filter-btn-home i {
        font-size: 0.9375rem;
    }
}

/* Responsive - Très petit mobile */
@media (max-width: 374.98px) {
    .search-wrapper-home {
        gap: 0.375rem;
        padding: 0.35rem;
    }
    
    .search-input-home {
        padding: 0.625rem 65px 0.625rem 0.875rem;
        font-size: 0.8125rem;
    }
    
    .search-btn-home {
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
    }
    
    .filter-btn-home {
        padding: 0.625rem 0.875rem;
        min-width: 44px;
    }
}

/* ===== MODAL DE FILTRES - UI MODERNE ===== */

/* Modal backdrop personnalisé */
.modal.fade .modal-dialog {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal.show .modal-dialog {
    transform: none;
}

/* Modal content */
#filtersModal .modal-content {
    border-radius: 20px;
    border: 1px solid rgba(106, 13, 173, 0.1);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 
                0 4px 16px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

/* Modal header */
#filtersModal .modal-header {
    background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
    color: white;
    padding: 1.5rem 1.75rem;
    border-bottom: none;
}

#filtersModal .modal-title {
    font-weight: 700;
    font-size: 1.25rem;
    letter-spacing: 0.02em;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

#filtersModal .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

#filtersModal .btn-close:hover {
    opacity: 1;
}

/* Modal body */
#filtersModal .modal-body {
    padding: 2rem 1.75rem;
    background: #fafbfc;
}

/* Groupe de filtres */
#filtersModal .mb-4 {
    margin-bottom: 1.75rem !important;
}

#filtersModal label {
    font-weight: 600;
    font-size: 0.9375rem;
    color: #1f2937;
    margin-bottom: 0.625rem;
    letter-spacing: 0.01em;
    display: block;
}

/* Inputs et selects */
#filtersModal .form-control,
#filtersModal .form-select {
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    padding: 0.75rem 1rem;
    font-size: 0.9375rem;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
}

#filtersModal .form-control:hover,
#filtersModal .form-select:hover {
    border-color: rgba(106, 13, 173, 0.3);
}

#filtersModal .form-control:focus,
#filtersModal .form-select:focus {
    border-color: #6A0DAD;
    box-shadow: 0 0 0 4px rgba(106, 13, 173, 0.08);
    outline: none;
}

/* Grid pour prix min/max */
#filtersModal .row.g-3 {
    margin: 0 -0.5rem;
}

#filtersModal .row.g-3 > div {
    padding: 0 0.5rem;
}

/* Modal footer */
#filtersModal .modal-footer {
    padding: 1.25rem 1.75rem;
    background: white;
    border-top: 1px solid #e5e7eb;
    gap: 0.75rem;
}

#filtersModal .modal-footer .btn {
    padding: 0.75rem 1.75rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9375rem;
    letter-spacing: 0.02em;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-width: 120px;
}

#filtersModal .modal-footer .btn-secondary {
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    color: #6b7280;
}

#filtersModal .modal-footer .btn-secondary:hover {
    background: #e5e7eb;
    border-color: #d1d5db;
    color: #4b5563;
    transform: translateY(-1px);
}

#filtersModal .modal-footer .btn-purple {
    background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
    border: none;
    box-shadow: 0 4px 12px rgba(106, 13, 173, 0.25);
}

#filtersModal .modal-footer .btn-purple:hover {
    background: linear-gradient(135deg, #5a0b92 0%, #7209a8 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(106, 13, 173, 0.35);
}

#filtersModal .modal-footer .btn-purple:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(106, 13, 173, 0.3);
}

/* Responsive modal */
@media (max-width: 767.98px) {
    #filtersModal .modal-dialog {
        margin: 0.5rem;
    }
    
    #filtersModal .modal-content {
        border-radius: 16px;
    }
    
    #filtersModal .modal-header,
    #filtersModal .modal-body,
    #filtersModal .modal-footer {
        padding-left: 1.25rem;
        padding-right: 1.25rem;
    }
    
    #filtersModal .modal-title {
        font-size: 1.125rem;
    }
    
    #filtersModal .modal-footer .btn {
        min-width: 100px;
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
    }
}

/* Classes utilitaires personnalisées */
.btn-purple {
    background-color: #6A0DAD;
    border-color: #6A0DAD;
    color: white;
}

.btn-purple:hover {
    background-color: #5a0b92;
    border-color: #5a0b92;
    color: white;
}

/* Styles responsive pour les catégories */
.category-card {
    min-height: 110px;
}

.category-icon-wrapper {
    font-size: 1.5rem;
}

.category-title {
    font-size: 0.85rem;
    line-height: 1.2;
}

.category-count {
    font-size: 0.7rem;
}

/* Mobile: catégories en défilement horizontal (petites) */
.categories-scroll {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.categories-scroll::-webkit-scrollbar {
    display: none;
}

.category-card-mobile {
    width: 70px;
    min-width: 70px;
    border-radius: 12px !important;
}

.category-card-mobile .card-body {
    padding: 0.5rem 0.3rem !important;
}

.category-icon-mobile {
    font-size: 1.1rem;
    margin-bottom: 0.25rem;
}

.category-title-mobile {
    font-size: 0.65rem;
    line-height: 1.1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

/* Tablette et Desktop */
@media (min-width: 768px) {
    .category-card {
        aspect-ratio: 1 / 1;
    }
}

/* Styles pour la section des filtres */
.filter-section-wrapper {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 0.75rem 1rem;
    border-radius: 14px;
    box-shadow: 0 4px 16px rgba(106, 13, 173, 0.12), 
                0 2px 6px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(106, 13, 173, 0.08);
    border-bottom: none;
    margin-bottom: 0;
}

.filter-section-wrapper:hover {
    box-shadow: 0 6px 20px rgba(106, 13, 173, 0.15), 
                0 3px 8px rgba(0, 0, 0, 0.08);
}

/* Tablette */
@media (min-width: 768px) and (max-width: 991.98px) {
    .category-card {
        min-height: 100px;
    }
    
    .category-icon {
        font-size: 1.3rem;
    }
    
    .category-title {
        font-size: 0.8rem;
    }
}

/* Desktop */
@media (min-width: 992px) {
    .category-card {
        min-height: 120px;
    }
    
    .category-icon {
        font-size: 1.75rem;
    }
    
    .category-title {
        font-size: 0.9rem;
    }
    
    .category-count {
        font-size: 0.75rem;
    }
}

.btn-outline-purple {
    border-color: #6A0DAD;
    color: #6A0DAD;
}

.btn-outline-purple:hover {
    background-color: #6A0DAD;
    border-color: #6A0DAD;
    color: white;
}

.btn-cyan {
    background-color: #6ECEDA;
    border-color: #6ECEDA;
    color: white;
}

.btn-cyan:hover {
    background-color: #5ebcc8;
    color: white;
}

.text-purple-600 {
    color: #6A0DAD !important;
}

.bg-purple-600 {
    background-color: #6A0DAD !important;
}

.bg-purple-100 {
    background-color: #f3e8ff !important;
}

.text-gray-800 {
    color: #1f2937 !important;
}

.text-gray-400 {
    color: #9ca3af !important;
}

.hover-card {
    transition: all 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(106, 13, 173, 0.15) !important;
}

.hover-card-item {
    transition: all 0.3s ease;
}

.hover-card-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.object-fit-cover {
    object-fit: cover;
}

/* Masquer scrollbar pour les filtres */
section .overflow-auto::-webkit-scrollbar {
    display: none;
}

/* ===== ANIMATIONS & MICRO-INTERACTIONS ===== */

/* Animation du wrapper au chargement */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.search-wrapper-home {
    animation: slideInDown 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Effet de focus amélioré */
.search-input-home:focus,
.form-control:focus,
.form-select:focus {
    animation: inputFocus 0.3s ease;
}

@keyframes inputFocus {
    0% { transform: scale(1); }
    50% { transform: scale(1.01); }
    100% { transform: scale(1); }
}

/* Indicateur de chargement pour le bouton de recherche */
.search-btn-home.loading {
    pointer-events: none;
    opacity: 0.7;
}

.search-btn-home.loading::after {
    content: '';
    position: absolute;
    width: 14px;
    height: 14px;
    top: 50%;
    left: 50%;
    margin: -7px 0 0 -7px;
    border: 2px solid transparent;
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Badge de compteur de filtres actifs */
.filter-btn-home .filter-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    font-size: 0.625rem;
    font-weight: 700;
    padding: 0.125rem 0.375rem;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
    animation: badgePulse 2s infinite;
}

@keyframes badgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Smooth scroll */
html {
    scroll-behavior: smooth;
}

/* ===== OMBRES POUR LES CARTES D'ARTICLES ===== */

/* Carte d'article avec ombre élégante */
.item-card-shadow {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(106, 13, 173, 0.08), 
                0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.item-card-shadow:hover {
    box-shadow: 0 12px 32px rgba(106, 13, 173, 0.15), 
                0 4px 16px rgba(0, 0, 0, 0.08);
    transform: translateY(-8px);
}

/* Image wrapper avec effet */
.item-image-wrapper {
    border-radius: 16px 16px 0 0;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
}

.item-image-wrapper img {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.item-card-shadow:hover .item-image-wrapper img {
    transform: scale(1.08);
}

/* Badge prix avec ombre */
.item-card-shadow .badge {
    box-shadow: 0 4px 12px rgba(106, 13, 173, 0.25);
    transition: all 0.3s ease;
}

.item-card-shadow:hover .badge {
    box-shadow: 0 6px 16px rgba(106, 13, 173, 0.35);
    transform: scale(1.05);
}

/* Corps de la carte */
.item-card-shadow .card-body {
    background: white;
    position: relative;
    z-index: 1;
}

/* Badge condition avec ombre subtile */
.item-card-shadow .badge.bg-purple-100 {
    box-shadow: 0 2px 6px rgba(106, 13, 173, 0.12);
}

/* Bouton d'ajout avec ombre */
.item-card-shadow .btn-cyan {
    box-shadow: 0 3px 10px rgba(110, 206, 218, 0.3);
    transition: all 0.3s ease;
}

.item-card-shadow:hover .btn-cyan {
    box-shadow: 0 5px 14px rgba(110, 206, 218, 0.4);
    transform: scale(1.1);
}

/* Effet de brillance au survol */
.item-card-shadow::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 50%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
    z-index: 2;
    pointer-events: none;
}

.item-card-shadow:hover::before {
    left: 100%;
}

/* Responsive - Optimisation des ombres sur mobile */
@media (max-width: 767.98px) {
    .item-card-shadow {
        box-shadow: 0 2px 10px rgba(106, 13, 173, 0.06), 
                    0 1px 4px rgba(0, 0, 0, 0.03);
    }
    
    .item-card-shadow:hover {
        box-shadow: 0 8px 20px rgba(106, 13, 173, 0.12), 
                    0 3px 10px rgba(0, 0, 0, 0.06);
        transform: translateY(-4px);
    }
}

/* Amélioration visuelle du lien stretched */
.item-card-shadow .stretched-link::before {
    border-radius: 16px;
}

</style>

<script>
    // ===== INITIALISATION =====
    document.addEventListener('DOMContentLoaded', function() {
        initSearchFeatures();
        initFilterModal();
    });

    // ===== FONCTIONS DE RECHERCHE =====
    function initSearchFeatures() {
        const searchForm = document.querySelector('.search-form-home');
        const searchInput = document.querySelector('.search-input-home');
        const searchBtn = document.querySelector('.search-btn-home');

        // Animation de chargement lors de la soumission
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                if (searchInput.value.trim() === '') {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.classList.add('border-danger');
                    setTimeout(() => {
                        searchInput.classList.remove('border-danger');
                    }, 1000);
                    return;
                }
                
                searchBtn.classList.add('loading');
            });
        }

        // Nettoyer la bordure d'erreur lors de la saisie
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                this.classList.remove('border-danger');
            });
        }
    }

    // ===== FONCTIONS DU MODAL DE FILTRES =====
    function initFilterModal() {
        const modal = document.getElementById('filtersModal');
        
        if (modal) {
            // Ajouter un effet sonore visuel à l'ouverture (vibration légère)
            modal.addEventListener('shown.bs.modal', function() {
                const modalDialog = this.querySelector('.modal-dialog');
                modalDialog.style.animation = 'modalBounce 0.3s ease';
            });
            
            // Compteur de filtres actifs
            updateFilterCount();
        }
    }

    // Ouvrir le modal de filtres
    function toggleFiltersModal() {
        const modal = new bootstrap.Modal(document.getElementById('filtersModal'));
        modal.show();
    }

    // Réinitialiser tous les filtres
    function resetFilters() {
        const form = document.getElementById('filterForm');
        form.reset();
        
        // Supprimer les valeurs des champs pour un reset complet
        form.querySelectorAll('input, select').forEach(field => {
            if (field.type === 'text' || field.type === 'search' || field.type === 'number') {
                field.value = '';
            } else if (field.tagName === 'SELECT') {
                field.selectedIndex = 0;
            }
        });
        
        // Animation de confirmation
        const resetBtn = event.target;
        const originalText = resetBtn.innerHTML;
        resetBtn.innerHTML = '<i class="fas fa-check me-2"></i>Réinitialisé!';
        resetBtn.classList.add('btn-success');
        
        setTimeout(() => {
            resetBtn.innerHTML = originalText;
            resetBtn.classList.remove('btn-success');
        }, 1500);
    }

    // Appliquer les filtres (soumettre le formulaire)
    function applyFilters() {
        const form = document.getElementById('filterForm');
        const applyBtn = event.target;
        
        // Animation de chargement
        applyBtn.classList.add('loading');
        applyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Application...';
        
        form.submit();
    }

    // Compter les filtres actifs
    function updateFilterCount() {
        const params = new URLSearchParams(window.location.search);
        let activeFilters = 0;
        
        // Compter les paramètres (exclure 'q' qui est la recherche principale)
        ['category', 'price_min', 'price_max', 'condition', 'sort'].forEach(param => {
            if (params.get(param)) activeFilters++;
        });
        
        // Afficher le badge si des filtres sont actifs
        const filterBtn = document.querySelector('.filter-btn-home');
        if (filterBtn && activeFilters > 0) {
            let badge = filterBtn.querySelector('.filter-badge');
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'filter-badge';
                filterBtn.style.position = 'relative';
                filterBtn.appendChild(badge);
            }
            badge.textContent = activeFilters;
        }
    }

    // Animation du modal
    const style = document.createElement('style');
    style.textContent = `
        @keyframes modalBounce {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/home.blade.php ENDPATH**/ ?>