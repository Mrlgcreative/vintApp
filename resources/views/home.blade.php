@extends('app')

@section('content')
<div class="home-container">
  

    <!-- Banner d'image -->
    <div class="hero-banner">
        <div class="banner-overlay">
            <div class="container">
                <div class="row align-items-center min-vh-50">
                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold text-white mb-4">
                            Découvrez des articles uniques
                        </h1>
                        <p class="lead text-white mb-4">
                            La marketplace moderne pour acheter et vendre en toute sécurité. 
                            Rejoignez notre communauté et trouvez des produits exceptionnels.
                        </p>
                        <div class="d-flex gap-3">
                            @auth
                                <a href="{{ route('items.create') }}" class="btn btn-light btn-lg">
                                    <i class="fas fa-plus me-2"></i>Vendre un article
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Commencer
                                </a>
                            @endauth
                            <a href="{{ route('items.index') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-search me-2"></i>Parcourir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Catégories d'articles -->
    <section class="categories-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">
                <i class="fas fa-th-large me-2 text-violet"></i>
                Catégories populaires
            </h2>
            <div class="row g-4">
                @foreach($categories as $category)
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route('items.index', ['category' => $category->id]) }}" class="text-decoration-none">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-{{ $category->icon ?? 'tag' }} fa-2x text-violet"></i>
                            </div>
                            <h5 class="category-title">{{ $category->name }}</h5>
                            <p class="category-count">{{ $category->items_count ?? 0 }} articles</p>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Fil d'actualité des articles -->
    <section class="articles-section py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-newspaper me-2 text-violet"></i>
                    Derniers articles
                </h2>
                <a href="{{ route('items.index') }}" class="btn btn-outline-violet">
                    Voir tout <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            
            <div class="row g-4">
                @forelse($latestItems as $item)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="article-card">
                        <div class="article-image">
                            @if($item->images && count($item->images) > 0)
                                <img src="{{ Storage::url($item->images[0]) }}" alt="{{ $item->name }}" class="img-fluid">
                            @else
                                <div class="no-image">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                            @endif
                            <div class="article-price">
                                <span class="price-tag">{{ number_format($item->price) }} {{ $item->currency }}</span>
                            </div>
                        </div>
                        <div class="article-content">
                            <h6 class="article-title">{{ Str::limit($item->name, 50) }}</h6>
                            <p class="article-description">{{ Str::limit($item->description, 80) }}</p>
                            <h6 class="article-title">{{ Str::limit($item->price, 50) }} {{ $item->currency }}</h6>
                            <div class="article-meta">
                                <span class="badge bg-violet-light">{{ $item->condition }}</span>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <a href="{{ route('items.show', $item) }}" class="stretched-link"></a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun article disponible</h4>
                    <p class="text-muted">Soyez le premier à publier un article !</p>
                    @auth
                        <a href="{{ route('items.create') }}" class="btn btn-violet">
                            <i class="fas fa-plus me-2"></i>Publier un article
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-violet">
                            <i class="fas fa-user-plus me-2"></i>Rejoindre VintApp
                        </a>
                    @endauth
                </div>
                @endforelse
            </div>
        </div>
    </section>

  
</div>

<style>
.home-container {
    min-height: 100vh;
}

.hero-banner {
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    position: relative;
}

.banner-overlay {
    background: rgba(0,0,0,0.3);
    min-height: 50vh;
    display: flex;
    align-items: center;
}

.min-vh-50 {
    min-height: 50vh;
}

.category-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.category-card:hover {
    transform: translateY(-5px);
    border-color: var(--violet-color);
    box-shadow: 0 8px 25px rgba(124, 58, 237, 0.15);
}

.category-icon {
    margin-bottom: 1rem;
}

.category-title {
    color: var(--violet-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.category-count {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0;
}

.article-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    height: 100%;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.article-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.article-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.article-price {
    position: absolute;
    top: 10px;
    right: 10px;
}

.price-tag {
    background: var(--violet-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.article-content {
    padding: 1rem;
}

.article-title {
    color: var(--violet-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.article-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.4;
}

.article-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-item {
    padding: 2rem 1rem;
}

.stat-item i {
    color: var(--violet-color);
}

.stat-item h3 {
    color: var(--violet-color);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-banner {
        background-attachment: scroll;
    }
    
    .banner-overlay {
        min-height: 40vh;
    }
    
    .category-card {
        padding: 1.5rem;
    }
    
    .article-image {
        height: 150px;
    }
}
</style>
@endsection 