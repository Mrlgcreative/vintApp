@props(['categories'])

<section class="py-20 lg:py-32 bg-gradient-to-b from-white to-gray-50/50">
    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête Section -->
        <div class="mb-12">
            <h2 class="font-display text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                Catégories
            </h2>
        </div>

        <!-- Conteneur Catégories -->
        <div class="relative">
            <div class="flex overflow-x-auto scrollbar-hide gap-4 lg:gap-6 pb-4 -mx-4 px-4" style="scroll-snap-type: x mandatory;">
                @php
                    $icons = ['👔', '👗', '👟', '👜', '⌚', '🕶️', '🧥', '👠', '🎒', '💍'];
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
                @endphp
                
                @forelse($categories as $index => $category)
                    <a href="{{ route('items.index', ['category' => $category->id]) }}" 
                       class="group relative bg-white hover:bg-gradient-to-br {{ $colors[$index % count($colors)] }} border-2 border-transparent hover:border-opacity-50 rounded-2xl lg:rounded-3xl p-4 lg:p-8 text-center transition-all duration-300 hover:shadow-xl hover:shadow-black/5 hover:-translate-y-2 flex-shrink-0 w-32 lg:w-40"
                       style="scroll-snap-align: start;">
                        
                        <!-- Image de catégorie -->
                        <div class="mb-3 lg:mb-4 overflow-hidden rounded-xl aspect-square group-hover:scale-110 transition-all duration-300">
                            @if($category->image_url)
                                <img src="/storage/{{ $category->image_url }}" 
                                     alt="{{ $category->name }}" 
                                     class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-4xl">
                                    {{ $icons[$index % count($icons)] }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Texte -->
                        <div class="space-y-1">
                            <h3 class="font-bold text-xs lg:text-sm xl:text-base text-gray-900 group-hover:text-gray-800 leading-tight">
                                {{ $category->name }}
                            </h3>
                            <p class="text-xs lg:text-sm text-gray-500 group-hover:text-gray-600">
                                {{ $category->items_count ?? 0 }} articles
                            </p>
                        </div>
                        
                        <!-- Indicateur hover -->
                        <div class="absolute top-2 lg:top-3 right-2 lg:right-3 w-5 lg:w-6 h-5 lg:h-6 bg-white rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center transform scale-75 group-hover:scale-100">
                            <svg class="w-2 lg:w-3 h-2 lg:h-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-16 w-full">
                        <div class="text-6xl lg:text-8xl mb-4 lg:mb-6">📦</div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-600 mb-2">Catégories à venir</h3>
                        <p class="text-gray-400">Nos catégories seront bientôt disponibles</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Navigation Arrows - Desktop uniquement -->
            <div class="hidden lg:block">
                <button id="categoriesPrev" 
                        class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-2 w-10 h-10 bg-white shadow-xl rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-gray-800 transition-all duration-300 z-10 opacity-80 hover:opacity-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                
                <button id="categoriesNext" 
                        class="absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-2 w-10 h-10 bg-white shadow-xl rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-gray-800 transition-all duration-300 z-10 opacity-80 hover:opacity-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>
