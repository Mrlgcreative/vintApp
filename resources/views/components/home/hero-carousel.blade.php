@props(['slides'])

@php
    $imageSizeMap = [
        'small' => '250px',
        'medium' => '350px',
        'large' => '450px',
        'full' => '100%',
    ];
    $slideDurations = $slides ? $slides->pluck('display_duration')->toArray() : [];
@endphp

<section class="relative h-[90vh] min-h-[500px] sm:min-h-[600px] overflow-hidden">
    @if($slides && $slides->count() > 0)
        <!-- Carrousel Container -->
        <div class="relative h-full">
            <!-- Slides -->
            <div id="carouselInner" class="relative h-full">
                @foreach($slides as $index => $slide)
                    <div class="carousel-slide absolute inset-0 flex items-center transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                         data-slide-index="{{ $index }}"
                         data-duration="{{ $slide->display_duration ?? 6 }}"
                          style="background-color: {{ $slide->background_color ?? '#1a1a1a' }};">
                        
                        <div class="container max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 w-full">
                            <div class="flex flex-col md:flex-row items-center gap-6 md:gap-10 {{ ($slide->image_position ?? 'right') === 'left' ? 'md:flex-row-reverse' : '' }}">
                                
                                <!-- Texte -->
                                <div class="flex-1 space-y-6 {{ ($slide->text_position ?? 'left') === 'center' ? 'text-center' : (($slide->text_position ?? 'left') === 'right' ? 'text-right' : 'text-left') }}">
                                    @if($slide->subtitle)
                                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-full">
                                            <span class="w-2 h-2 bg-white/60 rounded-full animate-pulse"></span>
                                            <span class="text-sm font-semibold text-white/90 tracking-wide uppercase">{{ $slide->subtitle }}</span>
                                        </div>
                                    @endif
                                    
                                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-[1.1] tracking-tight">
                                        {{ $slide->title }}
                                    </h1>
                                    
                                    <div class="flex flex-wrap gap-4 pt-2 {{ ($slide->text_position ?? 'left') === 'center' ? 'justify-center' : (($slide->text_position ?? 'left') === 'right' ? 'justify-end' : 'justify-start') }}">
                                        @if($slide->button_primary_text)
                                            <a href="{{ $slide->button_primary_url ?? '#' }}" 
                                               class="inline-flex items-center gap-3 px-7 py-3.5 bg-white text-gray-900 rounded-full font-semibold text-base hover:bg-gray-100 transition-all duration-300 shadow-lg shadow-black/20">
                                                <span>{{ $slide->button_primary_text }}</span>
                                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                                </svg>
                                            </a>
                                        @endif
                                        @if($slide->button_secondary_text)
                                            <a href="{{ $slide->button_secondary_url ?? '#' }}" 
                                               class="inline-flex items-center gap-2 px-7 py-3.5 border border-white/30 text-white rounded-full font-medium text-base hover:bg-white/10 transition-all duration-300">
                                                {{ $slide->button_secondary_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Image -->
                                @if($slide->image_url)
                                    <div class="flex-1 flex justify-center">
                                        <img src="/storage/{{ $slide->image_url }}" 
                                             alt="{{ $slide->title }}"
                                             class="object-contain drop-shadow-2xl rounded-lg"
                                             style="max-height: {{ $imageSizeMap[$slide->image_size ?? 'medium'] ?? '350px' }};" />
                                    </div>
                                @endif
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
            
            <!-- Bottom Bar: Dots -->
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
        <div class="relative h-full bg-gray-900 flex items-center justify-center overflow-hidden">
            
            <div class="container max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 text-center relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-full mb-8">
                    <span class="w-2 h-2 bg-white/60 rounded-full animate-pulse"></span>
                    <span class="text-sm font-semibold text-white/80 tracking-wide uppercase">Bienvenue sur VintApp</span>
                </div>
                
                <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-bold text-white mb-6 leading-[1.1] tracking-tight">
                    Trouvez des Pieces
                    <span class="block text-white/90">
                        Vintage Uniques
                    </span>
                </h1>
                
                <p class="text-base sm:text-xl text-white/60 mb-8 sm:mb-10 leading-relaxed max-w-2xl mx-auto">
                    Des articles authentiques selectionnes avec soin, pour un style qui vous ressemble
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                    <a href="{{ route('items.index') }}" 
                       class="inline-flex items-center justify-center gap-3 px-6 sm:px-8 py-3.5 sm:py-4 bg-white text-gray-900 rounded-full font-semibold text-sm sm:text-base hover:bg-gray-100 transition-all duration-300 shadow-lg shadow-black/20">
                        <span>Explorer la Collection</span>
                        <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('items.create') ?? '#' }}" 
                       class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3.5 sm:py-4 border border-white/20 text-white rounded-full font-medium text-sm sm:text-base hover:bg-white/10 transition-all duration-300">
                        <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Vendre un Article
                    </a>
                </div>
                
            </div>
        </div>
    @endif
</section>
