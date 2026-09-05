@props(['slides'])

@php
    $imageSizeMap = [
        'small' => '220px',
        'medium' => '320px',
        'large' => '440px',
        'full' => '100%',
    ];
    $totalSlides = $slides ? $slides->count() : 0;
@endphp

<section class="relative isolate h-[90vh] min-h-[560px] sm:min-h-[620px] overflow-hidden bg-gray-950"
         style="background-image: url('https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?q=80&w=2000&auto=format&fit=crop'); background-size: cover; background-position: center;">
    <!-- Voiles : scrim radial pour la lisibilité -->
    <div class="absolute inset-0 bg-black/60 z-[1]"></div>
    <div class="absolute inset-0 z-[1] bg-gradient-to-b from-black/50 via-black/20 to-gray-950/95"></div>
    <!-- Halo violet accent, style shadcn glow -->
    <div class="pointer-events-none absolute -top-40 right-1/4 h-[36rem] w-[36rem] -z-[1] rounded-full bg-vinted-primary/25 blur-[140px]"></div>
    <div class="pointer-events-none absolute -bottom-52 -left-20 h-[30rem] w-[30rem] -z-[1] rounded-full bg-vinted-accent/20 blur-[120px]"></div>

    @if($slides && $totalSlides > 0)
        <!-- Carrousel Container -->
        <div class="relative h-full">
            <!-- Slides -->
            <div id="carouselInner" class="relative h-full">
                @foreach($slides as $index => $slide)
                    <div class="carousel-slide absolute inset-0 flex items-center transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                         data-slide-index="{{ $index }}"
                         data-duration="{{ $slide->display_duration ?? 6 }}">
                        <!-- Image du slide en fond -->
                        @if($slide->image_url)
                            <div class="absolute inset-0">
                                <img src="/storage/{{ $slide->image_url }}"
                                     alt="{{ $slide->title }}"
                                     class="w-full h-full object-cover" />
                            </div>
                            <div class="absolute inset-0 bg-black/60 z-[1]"></div>
                            <div class="absolute inset-0 z-[2] bg-gradient-to-b from-black/50 via-transparent to-gray-950/95"></div>
                        @endif

                        <div class="relative z-[5] container max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 w-full">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-center gap-10 lg:gap-14 {{ ($slide->image_position ?? 'right') === 'left' ? 'lg:[direction:rtl]' : '' }}">
                                <!-- Texte -->
                                <div class="lg:col-span-7 space-y-7 {{ $slide->image_url ? '' : 'lg:col-span-12 text-center mx-auto max-w-3xl' }} {{ ($slide->text_position ?? 'left') === 'center' ? 'text-center' : (($slide->text_position ?? 'left') === 'right' ? 'text-right' : 'text-left') }}">
                                    @if($slide->subtitle)
                                        <div class="inline-flex items-center gap-2.5 px-4 py-1.5 bg-white/5 backdrop-blur-md border border-white/10 rounded-full shadow-lg shadow-black/10">
                                            <span class="w-1.5 h-1.5 bg-vinted-primary rounded-full animate-pulse"></span>
                                            <span class="text-[11px] sm:text-xs font-semibold text-white/80 tracking-[0.2em] uppercase">{{ $slide->subtitle }}</span>
                                        </div>
                                    @endif

                                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-[1.1] tracking-tight drop-shadow-lg">
                                        {{ $slide->title }}
                                    </h1>

                                    <div class="flex flex-wrap gap-4 pt-1 {{ ($slide->text_position ?? 'left') === 'center' ? 'justify-center' : (($slide->text_position ?? 'left') === 'right' ? 'justify-end' : 'justify-start') }}">
                                        @if($slide->button_primary_text)
                                            <a href="{{ $slide->button_primary_url ?? '#' }}"
                                               class="group inline-flex items-center gap-3 px-7 py-3.5 bg-white text-gray-900 rounded-full font-semibold text-base hover:bg-gray-100 transition-all duration-300 shadow-xl shadow-black/30">
                                                <span>{{ $slide->button_primary_text }}</span>
                                                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                                </svg>
                                            </a>
                                        @endif
                                        @if($slide->button_secondary_text)
                                            <a href="{{ $slide->button_secondary_url ?? '#' }}"
                                               class="inline-flex items-center gap-2 px-7 py-3.5 border border-white/20 bg-white/5 backdrop-blur-md text-white rounded-full font-semibold text-base hover:bg-white/10 transition-all duration-300">
                                                {{ $slide->button_secondary_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Image produit (carte glassmorphisme) -->
                                @if($slide->image_url && ($slide->image_size ?? 'medium') !== 'full')
                                    <div class="lg:col-span-5 flex justify-center">
                                        <div class="relative">
                                            <div class="absolute -inset-6 rounded-[2rem] bg-gradient-to-tr from-vinted-primary/40 via-white/10 to-transparent blur-2xl"></div>
                                            <div class="relative rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl p-4 shadow-2xl shadow-black/40">
                                                <img src="/storage/{{ $slide->image_url }}"
                                                     alt="{{ $slide->title }}"
                                                     class="rounded-2xl object-cover"
                                                     style="max-height: {{ $imageSizeMap[$slide->image_size ?? 'medium'] ?? '320px' }}; aspect-ratio: 4/5;" />
                                                <div class="absolute bottom-7 left-7 right-7 flex items-center justify-between rounded-2xl bg-black/45 backdrop-blur-md border border-white/10 px-4 py-3">
                                                    <span class="text-sm font-medium text-white/90 truncate">{{ $slide->title }}</span>
                                                    <span class="shrink-0 inline-flex items-center gap-1.5 text-xs font-semibold text-vinted-300">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                        Authentique
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Flèches -->
            @if($totalSlides > 1)
                <button onclick="prevSlide()"
                        class="absolute left-6 top-1/2 -translate-y-1/2 w-11 h-11 bg-black/25 backdrop-blur-md border border-white/10 hover:bg-black/50 rounded-full flex items-center justify-center transition-all duration-300 z-20 group">
                    <svg class="w-5 h-5 text-white group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button onclick="nextSlide()"
                        class="absolute right-6 top-1/2 -translate-y-1/2 w-11 h-11 bg-black/25 backdrop-blur-md border border-white/10 hover:bg-black/50 rounded-full flex items-center justify-center transition-all duration-300 z-20 group">
                    <svg class="w-5 h-5 text-white group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @endif

            <!-- Dots -->
            @if($totalSlides > 1)
                <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-3 z-20">
                    <div class="flex items-center gap-2.5 px-4 py-3 bg-black/25 backdrop-blur-md border border-white/10 rounded-full">
                        @foreach($slides as $index => $slide)
<button onclick="goToSlide({{ $index }})"
                                            class="carousel-dot rounded-full transition-all duration-300 {{ $index === 0 ? 'w-8 bg-white' : 'w-2.5 h-2.5 bg-white/40 hover:bg-white/60' }}">
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Fallback Hero centré -->
        <div class="relative h-full flex items-center justify-center">
            <div class="container max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 text-center relative z-10">
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 bg-white/5 backdrop-blur-md border border-white/10 rounded-full mb-8">
                    <span class="w-1.5 h-1.5 bg-vinted-primary rounded-full animate-pulse"></span>
                    <span class="text-[11px] sm:text-xs font-semibold text-white/80 tracking-[0.2em] uppercase">Bienvenue sur VintApp</span>
                </div>

                <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-bold text-white mb-6 leading-[1.1] tracking-tight drop-shadow-xl">
                    Trouvez des Pièces
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-white via-white to-vinted-300">
                        Vintage Uniques
                    </span>
                </h1>

                <p class="text-base sm:text-xl text-white/70 mb-9 sm:mb-10 leading-relaxed max-w-2xl mx-auto">
                    Des articles authentiques sélectionnés avec soin, pour un style qui vous ressemble
                </p>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                    <a href="{{ route('items.index') }}"
                       class="group inline-flex items-center justify-center gap-3 px-6 sm:px-8 py-3.5 sm:py-4 bg-white text-gray-900 rounded-full font-semibold text-sm sm:text-base hover:bg-gray-100 transition-all duration-300 shadow-xl shadow-black/30">
                        <span>Explorer la Collection</span>
                        <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('items.create') ?? '#' }}"
                       class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3.5 sm:py-4 border border-white/20 bg-white/5 backdrop-blur-md text-white rounded-full font-medium text-sm sm:text-base hover:bg-white/10 transition-all duration-300">
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