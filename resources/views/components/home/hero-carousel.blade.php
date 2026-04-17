@props(['slides'])

<section class="relative h-[90vh] min-h-[600px] overflow-hidden">
    @if($slides && $slides->count() > 0)
        <!-- Carrousel Container -->
        <div class="relative h-full">
            <!-- Slides -->
            <div id="carouselInner" class="flex h-full transition-transform duration-700 ease-in-out" 
                 style="width: {{ $slides->count() * 100 }}%;">
                @foreach($slides as $index => $slide)
                    <div class="relative w-full h-full flex-shrink-0" 
                         style="width: {{ 100 / $slides->count() }}%;">
                        
                        <!-- Image de fond plein écran -->
                        @if($slide->image_url)
                            <div class="absolute inset-0">
                                <img src="/storage/{{ $slide->image_url }}" 
                                     alt="{{ $slide->title }}" 
                                     class="w-full h-full object-cover" />
                            </div>
                        @endif
                        
                        <!-- Overlay gradient -->
                        <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/20"></div>
                        
                        <div class="relative z-10 h-full flex items-center">
                            <div class="container max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
                                <div class="max-w-2xl space-y-6">
                                    @if($slide->subtitle)
                                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-full">
                                            <span class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></span>
                                            <span class="text-sm font-semibold text-white/90 tracking-wide uppercase">{{ $slide->subtitle }}</span>
                                        </div>
                                    @endif
                                    
                                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-[1.1] tracking-tight">
                                        {{ $slide->title }}
                                    </h1>
                                    
                                    @if($slide->description)
                                        <p class="text-lg sm:text-xl text-white/80 leading-relaxed max-w-lg">
                                            {{ $slide->description }}
                                        </p>
                                    @endif
                                    
                                    @if($slide->cta_text && $slide->cta_link)
                                        <div class="flex flex-wrap items-center gap-4 pt-2">
                                            <a href="{{ $slide->cta_link }}" 
                                               class="group inline-flex items-center gap-3 px-7 py-3.5 bg-white text-gray-900 rounded-full font-bold text-base hover:bg-purple-50 transition-all duration-300 shadow-xl shadow-black/20">
                                                <span>{{ $slide->cta_text }}</span>
                                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('items.index') }}" 
                                               class="inline-flex items-center gap-2 px-7 py-3.5 border-2 border-white/30 text-white rounded-full font-semibold text-base hover:bg-white/10 backdrop-blur-sm transition-all duration-300">
                                                Explorer
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Navigation Arrows -->
            @if($slides->count() > 1)
                <button onclick="prevSlide()" 
                        class="absolute left-6 top-1/2 -translate-y-1/2 w-11 h-11 bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 rounded-full flex items-center justify-center transition-all duration-300 z-20 group">
                    <svg class="w-5 h-5 text-white group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                
                <button onclick="nextSlide()" 
                        class="absolute right-6 top-1/2 -translate-y-1/2 w-11 h-11 bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 rounded-full flex items-center justify-center transition-all duration-300 z-20 group">
                    <svg class="w-5 h-5 text-white group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @endif
            
            <!-- Bottom Bar: Dots + Slide Counter -->
            @if($slides->count() > 1)
                <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-4 z-20">
                    <div class="flex items-center gap-2 px-4 py-2.5 bg-white/10 backdrop-blur-md border border-white/20 rounded-full">
                        @foreach($slides as $index => $slide)
                            <button onclick="goToSlide({{ $index }})" 
                                    class="carousel-dot rounded-full transition-all duration-300 {{ $index === 0 ? 'w-8 h-2.5 bg-white' : 'w-2.5 h-2.5 bg-white/40 hover:bg-white/60' }}">
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Fallback Hero -->
        <div class="relative h-full bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 flex items-center justify-center overflow-hidden">
            <!-- Motif décoratif -->
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-1/4 -left-20 w-96 h-96 bg-purple-600/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-1/4 -right-20 w-80 h-80 bg-pink-600/20 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-500/10 rounded-full blur-3xl"></div>
            </div>
            
            <!-- Grille subtile -->
            <div class="absolute inset-0 opacity-5 bg-[linear-gradient(rgba(255,255,255,.1)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.1)_1px,transparent_1px)] bg-[size:60px_60px]"></div>
            
            <div class="container max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 text-center relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-full mb-8">
                    <span class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></span>
                    <span class="text-sm font-semibold text-white/80 tracking-wide uppercase">Bienvenue sur VintApp</span>
                </div>
                
                <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl font-bold text-white mb-6 leading-[1.1] tracking-tight">
                    Trouvez des Pièces
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-purple-400">
                        Vintage Uniques
                    </span>
                </h1>
                
                <p class="text-lg sm:text-xl text-white/60 mb-10 leading-relaxed max-w-2xl mx-auto">
                    Des articles authentiques sélectionnés avec soin, pour un style qui vous ressemble
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('items.index') }}" 
                       class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white text-gray-900 rounded-full font-bold text-base hover:bg-purple-50 transition-all duration-300 shadow-xl shadow-black/20">
                        <span>Explorer la Collection</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('items.create') ?? '#' }}" 
                       class="inline-flex items-center justify-center gap-2 px-8 py-4 border-2 border-white/20 text-white rounded-full font-semibold text-base hover:bg-white/10 backdrop-blur-sm transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Vendre un Article
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="flex justify-center gap-10 mt-14">
                    <div class="text-center px-6">
                        <div class="text-3xl font-bold text-white mb-1">2,5K+</div>
                        <div class="text-sm text-white/40 font-medium">Articles</div>
                    </div>
                    <div class="w-px h-12 bg-white/10"></div>
                    <div class="text-center px-6">
                        <div class="text-3xl font-bold text-white mb-1">1,2K+</div>
                        <div class="text-sm text-white/40 font-medium">Clients</div>
                    </div>
                    <div class="w-px h-12 bg-white/10"></div>
                    <div class="text-center px-6">
                        <div class="text-3xl font-bold text-white mb-1">98%</div>
                        <div class="text-sm text-white/40 font-medium">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
