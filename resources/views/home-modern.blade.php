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

<script>
function showNotification(message, type = 'primary') {
    var toastEl = document.getElementById('mainToast');
    var toastBody = document.getElementById('mainToastBody');
    if (!toastEl || !toastBody) return;
    toastBody.textContent = message;
    toastEl.className = 'toast align-items-center text-bg-' + type + ' border-0';
    var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.show();
}
</script>

<!-- Container principal avec Tailwind -->
<div class="min-h-screen bg-white dark:bg-gray-800 pb-20">
  
    <!-- Barre de recherche avec filtre -->
    <div class="sticky top-0 z-40 bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center gap-3">
                <input 
                    type="text" 
                    placeholder="Que recherchez-vous ?" 
                    class="flex-1 px-4 py-3 rounded-full border border-gray-200 dark:border-gray-700 focus:border-primary-600 focus:ring-2 focus:ring-primary-200 outline-none transition-all"
                />
                <button class="p-3 rounded-full bg-primary-100 text-primary-600 hover:bg-primary-200 transition-colors">
                    <i class="fas fa-sliders-h"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Hero Carrousel -->
    @if(isset($heroSlides) && $heroSlides->count() > 0)
        <div id="heroCarousel" class="carousel slide carousel-fade mx-4 my-6" data-bs-ride="carousel" data-bs-interval="5000">
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
            <div class="carousel-inner rounded-2xl overflow-hidden shadow-lg">
                @foreach($heroSlides as $index => $slide)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="relative h-48 bg-gradient-to-r from-primary-600 to-cyan-400" 
                             style="background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('{{ Storage::url($slide->image_path) }}'); background-size: cover; background-position: center;">
                            <div class="absolute inset-0 flex items-center px-6">
                                <div class="text-white max-w-md">
                                    <h1 class="text-3xl md:text-4xl font-bold mb-2">
                                        {{ $slide->title }}
                                    </h1>
                                    @if($slide->subtitle)
                                        <p class="text-lg mb-4 opacity-90">
                                            {{ $slide->subtitle }}
                                        </p>
                                    @endif
                                    <div class="flex gap-3 flex-wrap">
                                        @if($slide->button_primary_text && $slide->button_primary_url)
                                            <a href="{{ $slide->button_primary_url }}" 
                                               class="px-6 py-2.5 bg-white dark:bg-gray-800 text-primary-600 rounded-full font-semibold hover:bg-gray-100 dark:bg-gray-800 transition-colors shadow-md">
                                                {{ $slide->button_primary_text }}
                                            </a>
                                        @endif
                                        @if($slide->button_secondary_text && $slide->button_secondary_url)
                                            <a href="{{ $slide->button_secondary_url }}" 
                                               class="px-6 py-2.5 border-2 border-white text-white rounded-full font-semibold hover:bg-white dark:bg-gray-800 hover:text-primary-600 transition-all">
                                                {{ $slide->button_secondary_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Fallback Hero Banner -->
        <div class="mx-4 my-6 rounded-2xl overflow-hidden shadow-lg">
            <div class="relative h-48 bg-gradient-to-r from-primary-600 to-cyan-400" 
                 style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('{{ $heroSettings['image'] ?? '' }}'); background-size: cover; background-position: center;">
                <div class="absolute inset-0 flex items-center px-6">
                    <div class="text-white max-w-md">
                        <h1 class="text-3xl md:text-4xl font-bold mb-2">
                            {{ $heroSettings['title'] ?? 'Bienvenue sur VintApp' }}
                        </h1>
                        <p class="text-lg mb-4 opacity-90">
                            {{ $heroSettings['subtitle'] ?? 'La marketplace de confiance' }}
                        </p>
                        <div class="flex gap-3">
                            @auth
                                <a href="{{ route('items.create') }}" 
                                   class="px-6 py-2.5 bg-white dark:bg-gray-800 text-primary-600 rounded-full font-semibold hover:bg-gray-100 dark:bg-gray-800 transition-colors">
                                    <i class="fas fa-plus me-1"></i>{{ $heroSettings['button_primary'] ?? 'Vendre' }}
                                </a>
                            @else
                                <a href="{{ route('register') }}" 
                                   class="px-6 py-2.5 bg-white dark:bg-gray-800 text-primary-600 rounded-full font-semibold hover:bg-gray-100 dark:bg-gray-800 transition-colors">
                                    <i class="fas fa-user-plus me-1"></i>Commencer
                                </a>
                            @endauth
                            <a href="{{ route('items.index') }}" 
                               class="px-6 py-2.5 border-2 border-white text-white rounded-full font-semibold hover:bg-white dark:bg-gray-800 hover:text-primary-600 transition-all">
                                <i class="fas fa-search me-1"></i>{{ $heroSettings['button_secondary'] ?? 'Explorer' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Catégories populaires -->
    <section class="px-4 py-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">
            Catégories populaires
        </h2>
        <div class="grid grid-cols-4 gap-3">
            @foreach($categories as $category)
                <a href="{{ route('items.index', ['category' => $category->id]) }}" 
                   class="flex flex-col items-center p-3 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors">
                    <div class="text-2xl mb-2">
                        <i class="fas fa-{{ $category->icon ?? 'tag' }} text-primary-600"></i>
                    </div>
                    <span class="text-xs text-center font-medium text-gray-700 dark:text-gray-200">{{ $category->name }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $category->items_count ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Filtres -->
    <section class="px-4 py-2">
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
            <button class="px-4 py-2 bg-primary-600 text-white rounded-full text-sm font-medium whitespace-nowrap">
                Tous
            </button>
            <button class="px-4 py-2 border border-primary-600 text-primary-600 rounded-full text-sm font-medium hover:bg-primary-50 whitespace-nowrap">
                Nouveautés
            </button>
            <button class="px-4 py-2 border border-primary-600 text-primary-600 rounded-full text-sm font-medium hover:bg-primary-50 whitespace-nowrap">
                Populaires
            </button>
            <button class="px-4 py-2 border border-primary-600 text-primary-600 rounded-full text-sm font-medium hover:bg-primary-50 whitespace-nowrap">
                Promotions
            </button>
        </div>
    </section>

    <!-- Articles populaires -->
    <section class="px-4 py-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Derniers articles
            </h2>
            <a href="{{ route('items.index') }}" 
               class="text-sm text-primary-600 font-semibold hover:text-primary-700">
                Voir tout <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            @forelse($latestItems as $item)
                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                    <!-- Image produit -->
                    <div class="relative h-32 bg-gradient-to-br from-primary-200 to-cyan-200">
                        @php
                            $imgPath = ($item->images && count($item->images) > 0) ? $item->images[0] : null;
                        @endphp
                        @if($imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath))
                            <img src="{{ Storage::url($imgPath) }}" 
                                 alt="{{ $item->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-3xl text-gray-400"></i>
                            </div>
                        @endif
                        <!-- Badge prix -->
                        <div class="absolute top-2 right-2 px-3 py-1 bg-primary-600 text-white rounded-full text-sm font-bold shadow-md">
                            {{ number_format($item->price) }} {{ $item->currency }}
                        </div>
                    </div>
                    
                    <!-- Info produit -->
                    <div class="p-3">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-1 line-clamp-2">
                            {{ Str::limit($item->name, 40) }}
                        </h3>
                        <div class="flex items-center justify-between mt-2">
                            <span class="px-2 py-1 bg-primary-100 text-primary-600 text-xs rounded-full">
                                {{ $item->condition }}
                            </span>
                            <button class="w-7 h-7 bg-cyan-400 text-white rounded-full flex items-center justify-center hover:bg-cyan-500 transition-colors">
                                <i class="fas fa-plus text-sm"></i>
                            </button>
                        </div>
                        <small class="text-xs text-gray-500 dark:text-gray-400 block mt-2">
                            {{ $item->created_at->diffForHumans() }}
                        </small>
                    </div>
                    <a href="{{ route('items.show', $item) }}" class="absolute inset-0"></a>
                </div>
            @empty
                <div class="col-span-2 text-center py-12">
                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                    <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-300 mb-2">Aucun article disponible</h4>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Soyez le premier à publier un article !</p>
                    @auth
                        <a href="{{ route('items.create') }}" 
                           class="inline-block px-6 py-3 bg-primary-600 text-white rounded-full font-semibold hover:bg-primary-700 transition-colors">
                            <i class="fas fa-plus me-2"></i>Publier un article
                        </a>
                    @else
                        <a href="{{ route('register') }}" 
                           class="inline-block px-6 py-3 bg-primary-600 text-white rounded-full font-semibold hover:bg-primary-700 transition-colors">
                            <i class="fas fa-user-plus me-2"></i>Rejoindre VintApp
                        </a>
                    @endauth
                </div>
            @endforelse
        </div>
    </section>
</div>

<style>
/* Classes utilitaires personnalisées */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

/* Indicateurs carrousel Tailwind-friendly */
#heroCarousel .carousel-indicators button {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin: 0 4px;
}

#heroCarousel .carousel-indicators button.active {
    background-color: white;
    transform: scale(1.3);
}
</style>
@endsection
