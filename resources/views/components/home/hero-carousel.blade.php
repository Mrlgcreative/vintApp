@props(['slides'])

<section class="relative h-screen overflow-hidden">
    @if($slides && $slides->count() > 0)
        <!-- Carrousel Container -->
        <div class="relative h-full">
            <!-- Slides -->
            <div id="carouselInner" class="flex h-full transition-transform duration-700 ease-in-out" 
                 style="width: {{ $slides->count() * 100 }}%;">
                @foreach($slides as $index => $slide)
                    <div class="relative w-full h-full flex-shrink-0" 
                         style="width: {{ 100 / $slides->count() }}%; background-color: {{ $slide->background_color ?? '#6A0DAD' }};">
                        
                        <!-- Image de fond avec opacité 20% -->
                        @if($slide->image_url)
                            <div class="absolute inset-0 opacity-20">
                                <img src="/storage/{{ $slide->image_url }}" 
                                     alt="{{ $slide->title }}" 
                                     class="w-full h-full object-cover" />
                            </div>
                        @endif
                        
                        <div class="relative z-10 h-full flex items-center">
                            <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center h-full min-h-[600px]">
                                    
                                    <!-- Left Content -->
                                    <div class="text-left space-y-6 lg:space-y-8">
                                        @if($slide->subtitle)
                                            <p class="text-white/90 text-sm sm:text-base font-semibold tracking-wide uppercase animate-fade-in">
                                                {{ $slide->subtitle }}
                                            </p>
                                        @endif
                                        
                                        <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black text-white leading-tight animate-slide-up">
                                            {{ $slide->title }}
                                        </h1>
                                        
                                        @if($slide->description)
                                            <p class="text-lg sm:text-xl text-white/80 leading-relaxed animate-fade-in max-w-xl">
                                                {{ $slide->description }}
                                            </p>
                                        @endif
                                        
                                        @if($slide->cta_text && $slide->cta_link)
                                            <div class="animate-scale-in">
                                                <a href="{{ $slide->cta_link }}" 
                                                   class="inline-flex items-center gap-3 px-8 py-4 bg-white text-gray-900 rounded-full font-bold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl">
                                                    <span>{{ $slide->cta_text }}</span>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Right Content - Image -->
                                    <div class="relative h-full flex items-center justify-center">
                                        @if($slide->image_url)
                                            <div class="relative w-full max-w-md lg:max-w-lg aspect-square group">
                                                <!-- Cadre décoratif extérieur avec effet vintage -->
                                                <div class="absolute -inset-4 bg-gradient-to-br from-white/20 via-white/10 to-transparent rounded-3xl backdrop-blur-sm transform rotate-3 group-hover:rotate-6 transition-transform duration-500"></div>
                                                
                                                <!-- Cadre décoratif intérieur -->
                                                <div class="absolute -inset-2 bg-white/30 rounded-2xl backdrop-blur-sm transform -rotate-2 group-hover:-rotate-3 transition-transform duration-500"></div>
                                                
                                                <!-- Container principal de l'image -->
                                                <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden border border-white/50 backdrop-blur-md transform group-hover:scale-105 transition-all duration-500">
                                                    <!-- Effet de brillance vintage -->
                                                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                                    
                                                    <!-- Image -->
                                                    <img src="/storage/{{ $slide->image_url }}" 
                                                         alt="{{ $slide->title }}" 
                                                         class="w-full h-full object-cover animate-scale-in" />
                                                    
                                                    <!-- Overlay avec effet vignette -->
                                                    <div class="absolute inset-0 shadow-[inset_0_0_60px_rgba(0,0,0,0.1)] pointer-events-none"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Navigation Arrows -->
            @if($slides->count() > 1)
                <button onclick="prevSlide()" 
                        class="absolute left-8 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-full flex items-center justify-center transition-all z-20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                
                <button onclick="nextSlide()" 
                        class="absolute right-8 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-full flex items-center justify-center transition-all z-20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @endif
            
            <!-- Dots Indicator -->
            @if($slides->count() > 1)
                <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-3 z-20">
                    @foreach($slides as $index => $slide)
                        <button onclick="goToSlide({{ $index }})" 
                                class="carousel-dot w-3 h-3 rounded-full transition-all {{ $index === 0 ? 'bg-white' : 'bg-white/40 hover:bg-white/60' }}">
                        </button>
                    @endforeach
                </div>
            @endif
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 right-8 animate-bounce z-20">
                <div class="w-6 h-10 border-2 border-white/60 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-white/80 rounded-full mt-2 animate-pulse"></div>
                </div>
            </div>
        </div>
    @else
        <!-- Fallback Hero -->
        <div class="relative h-full bg-gradient-to-br from-purple-900 via-pink-800 to-indigo-900 flex items-center justify-center">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_25%_25%,_rgba(120,119,198,0.3)_2px,_transparent_0),radial-gradient(circle_at_75%_75%,_rgba(245,158,11,0.3)_2px,_transparent_0)] bg-[length:100px_100px]"></div>
            </div>
            
            <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <p class="text-purple-300 text-sm sm:text-base font-semibold tracking-wide uppercase mb-4">
                    Découvrez Notre
                </p>
                
                <h1 class="font-display text-5xl sm:text-7xl lg:text-8xl font-black text-white mb-6 leading-tight">
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-purple-300 via-pink-300 to-indigo-300">
                        Vintage
                    </span>
                    <span class="block">Collection</span>
                </h1>
                
                <p class="text-xl sm:text-2xl text-gray-200 mb-8 leading-relaxed max-w-2xl mx-auto">
                    Pièces authentiques et uniques sélectionnées avec passion
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('items.index') }}" 
                       class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-white text-gray-900 rounded-full font-bold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl">
                        <span>Explorer</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="flex justify-center gap-12 mt-12">
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
    @endif
</section>
