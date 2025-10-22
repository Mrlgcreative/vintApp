@extends('app')

@section('content')
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

    <!-- Hero Carrousel -->
    @if(isset($heroSlides) && $heroSlides->count() > 0)
        <div id="heroCarousel" class="carousel slide mx-2 mx-md-4 my-4 my-md-6" data-bs-ride="carousel" data-bs-interval="5000">
            <!-- Indicateurs -->
            <div class="carousel-indicators">
                @foreach($heroSlides as $index => $slide)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" 
                        class="{{ $index === 0 ? 'active' : '' }}" 
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                        aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>

            <!-- Slides -->
            <div class="carousel-inner rounded-3 overflow-hidden shadow-lg">
                @foreach($heroSlides as $index => $slide)
                    @php
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
                    @endphp
                    
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }} hero-slide-item" 
                         style="background-color: {{ $bgColor }};">
                        <div class="container-fluid px-3 px-md-5 py-4 py-md-5">
                            <div class="row align-items-center g-3 g-md-4">
                                @if($imgPos === 'left')
                                    <!-- Image à gauche -->
                                    <div class="col-12 col-md-6 text-center order-1 order-md-1">
                                        <div class="hero-image-wrapper">
                                            <img src="{{ Storage::url($slide->image_path) }}" 
                                                 alt="{{ $slide->title }}"
                                                 class="img-fluid hero-image hero-image-{{ $imgSize }}"
                                                 data-size="{{ $imgSize }}">
                                        </div>
                                    </div>
                                    <!-- Texte à droite -->
                                    <div class="col-12 col-md-6 text-center text-md-{{ $textPos }} order-2 order-md-2">
                                        <h1 class="text-white fw-bold mb-2 mb-md-3 hero-title">
                                            {{ $slide->title }}
                                        </h1>
                                        @if($slide->subtitle)
                                            <p class="text-white mb-3 mb-md-4 hero-subtitle">
                                                {{ $slide->subtitle }}
                                            </p>
                                        @endif
                                        <div class="d-flex gap-2 gap-md-3 flex-wrap justify-content-center justify-content-md-{{ $textPos === 'center' ? 'center' : ($textPos === 'right' ? 'end' : 'start') }}">
                                            @if($slide->button_primary_text && $slide->button_primary_url)
                                                <a href="{{ $slide->button_primary_url }}" 
                                                   class="btn btn-light btn-hero px-4 px-md-5 py-2 py-md-3 rounded-pill fw-semibold shadow-lg hover-lift">
                                                    {{ $slide->button_primary_text }}
                                                </a>
                                            @endif
                                            @if($slide->button_secondary_text && $slide->button_secondary_url)
                                                <a href="{{ $slide->button_secondary_url }}" 
                                                   class="btn btn-outline-light btn-hero px-4 px-md-5 py-2 py-md-3 rounded-pill fw-semibold">
                                                    {{ $slide->button_secondary_text }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <!-- Texte à gauche -->
                                    <div class="col-12 col-md-6 text-center text-md-{{ $textPos }} order-2 order-md-1">
                                        <h1 class="text-white fw-bold mb-2 mb-md-3 hero-title">
                                            {{ $slide->title }}
                                        </h1>
                                        @if($slide->subtitle)
                                            <p class="text-white mb-3 mb-md-4 hero-subtitle">
                                                {{ $slide->subtitle }}
                                            </p>
                                        @endif
                                        <div class="d-flex gap-2 gap-md-3 flex-wrap justify-content-center justify-content-md-{{ $textPos === 'center' ? 'center' : ($textPos === 'right' ? 'end' : 'start') }}">
                                            @if($slide->button_primary_text && $slide->button_primary_url)
                                                <a href="{{ $slide->button_primary_url }}" 
                                                   class="btn btn-light btn-hero px-4 px-md-5 py-2 py-md-3 rounded-pill fw-semibold shadow-lg hover-lift">
                                                    {{ $slide->button_primary_text }}
                                                </a>
                                            @endif
                                            @if($slide->button_secondary_text && $slide->button_secondary_url)
                                                <a href="{{ $slide->button_secondary_url }}" 
                                                   class="btn btn-outline-light btn-hero px-4 px-md-5 py-2 py-md-3 rounded-pill fw-semibold">
                                                    {{ $slide->button_secondary_text }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Image à droite -->
                                    <div class="col-12 col-md-6 text-center order-1 order-md-2">
                                        <div class="hero-image-wrapper">
                                            <img src="{{ Storage::url($slide->image_path) }}" 
                                                 alt="{{ $slide->title }}"
                                                 class="img-fluid hero-image hero-image-{{ $imgSize }}"
                                                 data-size="{{ $imgSize }}">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <style>
            /* Styles responsive optimisés pour le carrousel hero */
            .hero-slide-item {
                min-height: 450px;
                transition: background-color 0.3s ease;
            }
            
            .hero-title {
                font-size: 1.75rem;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
                line-height: 1.2;
            }
            
            .hero-subtitle {
                font-size: 1rem;
                opacity: 0.95;
                line-height: 1.4;
            }
            
            .hero-image-wrapper {
                padding: 1rem 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .hero-image {
                max-width: 100%;
                height: auto;
                object-fit: contain;
                transition: transform 0.3s ease;
            }
            
            /* Tailles d'images dynamiques */
            .hero-image-small {
                max-height: 180px;
            }
            
            .hero-image-medium {
                max-height: 250px;
            }
            
            .hero-image-large {
                max-height: 300px;
            }
            
            .hero-image-full {
                max-height: 350px;
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
                    padding: 1.5rem 0 !important;
                }
                
                .hero-title {
                    font-size: 1.5rem !important;
                }
                
                .hero-subtitle {
                    font-size: 0.95rem !important;
                }
                
                .hero-image-small {
                    max-height: 180px !important;
                }
                
                .hero-image-medium {
                    max-height: 250px !important;
                }
                
                .hero-image-large {
                    max-height: 300px !important;
                }
                
                .hero-image-full {
                    max-height: 350px !important;
                }
                
                .hero-image-wrapper {
                    padding: 0.5rem 0;
                }
                
                /* Boutons plus petits sur mobile */
                .btn-hero {
                    font-size: 0.9rem;
                    padding: 0.5rem 1.5rem !important;
                }
            }
            
            /* Media queries pour tablette */
            @media (min-width: 768px) and (max-width: 991.98px) {
                .hero-slide-item {
                    min-height: 400px;
                }
                
                .hero-title {
                    font-size: 2rem !important;
                }
                
                .hero-subtitle {
                    font-size: 1.1rem !important;
                }
                
                .hero-image-small {
                    max-height: 220px !important;
                }
                
                .hero-image-medium {
                    max-height: 300px !important;
                }
                
                .hero-image-large {
                    max-height: 380px !important;
                }
                
                .hero-image-full {
                    max-height: 450px !important;
                }
            }
            
            /* Media queries pour desktop */
            @media (min-width: 992px) {
                .hero-slide-item {
                    min-height: 500px;
                }
                
                .hero-title {
                    font-size: 2.5rem !important;
                }
                
                .hero-subtitle {
                    font-size: 1.2rem !important;
                }
                
                .hero-image-small {
                    max-height: 250px !important;
                }
                
                .hero-image-medium {
                    max-height: 350px !important;
                }
                
                .hero-image-large {
                    max-height: 450px !important;
                }
                
                .hero-image-full {
                    max-height: 500px !important;
                }
                
                .hero-image:hover {
                    transform: scale(1.02);
                }
            }
        </style>
    @else
        <!-- Fallback Hero Banner -->
        <div class="mx-2 mx-md-4 my-4 my-md-6 rounded-3 overflow-hidden shadow-lg">
            <div class="position-relative" style="height: 300px; background: linear-gradient(135deg, #6A0DAD 0%, #6ECEDA 100%);">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-8 col-lg-6">
                                <div class="text-white">
                                    <h1 class="display-4 fw-bold mb-3">
                                        {{ $heroSettings['title'] ?? 'Bienvenue sur VintApp' }}
                                    </h1>
                                    <p class="lead mb-4 opacity-90">
                                        {{ $heroSettings['subtitle'] ?? 'La marketplace de confiance pour acheter et vendre' }}
                                    </p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        @auth
                                            <a href="{{ route('items.create') }}" 
                                               class="btn btn-light btn-lg px-5 rounded-pill fw-semibold shadow-lg">
                                                <i class="fas fa-plus me-2"></i>{{ $heroSettings['button_primary'] ?? 'Vendre' }}
                                            </a>
                                        @else
                                            <a href="{{ route('register') }}" 
                                               class="btn btn-light btn-lg px-5 rounded-pill fw-semibold shadow-lg">
                                                <i class="fas fa-user-plus me-2"></i>Commencer
                                            </a>
                                        @endauth
                                        <a href="{{ route('items.index') }}" 
                                           class="btn btn-outline-light btn-lg px-5 rounded-pill fw-semibold">
                                            <i class="fas fa-search me-2"></i>{{ $heroSettings['button_secondary'] ?? 'Explorer' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Catégories populaires -->
    <section class="px-3 px-md-4 py-4 py-md-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold text-gray-800 mb-0">
                <i class="fas fa-layer-group text-purple-600 me-2"></i>
                Catégories populaires
            </h2>
        </div>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
            @foreach($categories as $category)
                <div class="col">
                    <a href="{{ route('items.index', ['category' => $category->id]) }}" 
                       class="card text-decoration-none h-100 border-0 shadow-sm hover-card"
                       style="background: linear-gradient(135deg, #f3e8ff 0%, #e0f2fe 100%);">
                        <div class="card-body text-center p-3">
                            <div class="fs-2 mb-2">
                                <i class="fas fa-{{ $category->icon ?? 'tag' }} text-purple-600"></i>
                            </div>
                            <h6 class="card-title mb-1 text-gray-800 fw-semibold" style="font-size: 0.9rem;">
                                {{ $category->name }}
                            </h6>
                            <small class="text-muted">{{ $category->items_count ?? 0 }} articles</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Filtres -->
    <section class="px-3 px-md-4 py-2">
        <div class="d-flex gap-2 overflow-auto pb-2 flex-nowrap" style="scrollbar-width: none; -ms-overflow-style: none;">
            <button class="btn btn-purple btn-sm rounded-pill px-4 fw-medium flex-shrink-0">
                <i class="fas fa-star me-1"></i>Tous
            </button>
            <button class="btn btn-outline-purple btn-sm rounded-pill px-4 fw-medium flex-shrink-0">
                <i class="fas fa-sparkles me-1"></i>Nouveautés
            </button>
            <button class="btn btn-outline-purple btn-sm rounded-pill px-4 fw-medium flex-shrink-0">
                <i class="fas fa-fire me-1"></i>Populaires
            </button>
            <button class="btn btn-outline-purple btn-sm rounded-pill px-4 fw-medium flex-shrink-0">
                <i class="fas fa-tag me-1"></i>Promotions
            </button>
        </div>
    </section>

    <!-- Articles populaires -->
    <section class="px-3 px-md-4 py-4 py-md-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold text-gray-800 mb-0">
                <i class="fas fa-box text-purple-600 me-2"></i>
                Derniers articles
            </h2>
            <a href="{{ route('items.index') }}" 
               class="btn btn-sm btn-outline-purple rounded-pill px-4 fw-semibold">
                Voir tout <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
            @forelse($latestItems as $item)
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-card-item position-relative">
                        <!-- Image produit -->
                        <div class="position-relative" style="padding-top: 100%; overflow: hidden;">
                            @php
                                $imgPath = ($item->images && count($item->images) > 0) ? $item->images[0] : null;
                            @endphp
                            @if($imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath))
                                <img src="{{ Storage::url($imgPath) }}" 
                                     alt="{{ $item->name }}" 
                                     class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
                            @else
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-center bg-light">
                                    <i class="fas fa-image fs-1 text-gray-400"></i>
                                </div>
                            @endif
                            <!-- Badge prix -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-purple-600 text-white rounded-pill px-3 py-2 shadow-sm fw-bold">
                                    {{ number_format($item->price) }} {{ $item->currency }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Info produit -->
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2 fw-semibold text-gray-800" 
                                style="font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                {{ $item->name }}
                            </h6>
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <span class="badge bg-purple-100 text-purple-600 rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                    {{ $item->condition }}
                                </span>
                                <button class="btn btn-sm btn-cyan rounded-circle p-0 d-flex align-items-center justify-content-center" 
                                        style="width: 28px; height: 28px;">
                                    <i class="fas fa-plus" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                                <i class="far fa-clock me-1"></i>{{ $item->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <a href="{{ route('items.show', $item) }}" class="stretched-link"></a>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-box-open text-gray-300" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="h5 fw-semibold text-gray-600 mb-2">Aucun article disponible</h4>
                        <p class="text-gray-500 mb-4">Soyez le premier à publier un article !</p>
                        @auth
                            <a href="{{ route('items.create') }}" 
                               class="btn btn-purple btn-lg rounded-pill px-5 fw-semibold shadow">
                                <i class="fas fa-plus me-2"></i>Publier un article
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="btn btn-purple btn-lg rounded-pill px-5 fw-semibold shadow">
                                <i class="fas fa-user-plus me-2"></i>Rejoindre VintApp
                            </a>
                        @endauth
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</div>

<style>
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
</style>
@endsection
