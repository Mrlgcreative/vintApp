@props(['item'])

@php
    $images = $item->images ?? [];
    $firstImage = count($images) > 0 ? $images[0] : null;
    $isNew = $item->created_at->gt(now()->subDays(7));
    $activeBoost = $item->activeBoosts->first();
    $isBoosted = $activeBoost !== null;
    $hasOffer = $item->has_offer;
    $offer = $item->offer;
    $salePrice = $item->sale_price;
    $symbol = $item->currency === 'USD' ? '$' : 'FC';
@endphp

<article class="group relative bg-white dark:bg-gray-800 rounded-3xl overflow-hidden border-2 {{ $isBoosted ? 'border-gray-300 dark:border-gray-600 ring-2 ring-gray-100 dark:ring-gray-700 shadow-lg shadow-gray-500/10 dark:shadow-black/30' : 'border-gray-100 dark:border-gray-700 hover:border-gray-300' }} transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
    
    <!-- Image Container -->
    <div class="aspect-[3/4] relative overflow-hidden bg-gray-100 dark:bg-gray-800">
        <x-skeleton class="absolute inset-0 h-full w-full" />

        @if($firstImage && Storage::disk('public')->exists($firstImage))
            <img src="{{ asset('storage/' . $firstImage) }}" 
                 alt="{{ $item->name }}" 
                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                 loading="lazy" />
        @else
            <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-700/50">
                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        @endif
        
        <!-- Boost Glow Effect -->
        @if($isBoosted)
            <div class="absolute inset-0 bg-gradient-to-tr from-gray-400/10 via-transparent to-transparent pointer-events-none"></div>
        @endif
        
        <!-- Overlay Actions -->
        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-3">
            <button class="w-12 h-12 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all duration-200">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
            <button class="w-12 h-12 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all duration-200">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
            <button onclick="addToCart({{ $item->id }})" 
                    class="w-12 h-12 bg-gray-900 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all duration-200 hover:bg-black">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </button>
        </div>
        
        <!-- Badges -->
        <div class="absolute top-4 left-4 flex flex-col gap-2">
            @if($hasOffer)
                <span class="px-3 py-1 {{ $offer->is_flash_sale ? 'bg-red-500' : 'bg-emerald-500' }} text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 3.293a1 1 0 011.414 0l1.5 1.5a1 1 0 010 1.414L8 18.414a1 1 0 01-.707.293H3a1 1 0 01-1-1v-4.293a1 1 0 01.293-.707L17.293 3.293zM12 6l2 2-7 7a1 1 0 01-1.414 0l-1.5-1.5a1 1 0 010-1.414L12 6z"/></svg>
                    {{ $offer->discountLabel() }}
                </span>
            @endif
            @if($isBoosted)
                <div class="relative">
                    <span class="px-3 py-1 bg-gray-900 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Spotlight
                    </span>
                </div>
            @endif
            @if($isNew)
                <span class="px-3 py-1 bg-gray-700 text-white text-xs font-bold rounded-full shadow-lg">
                    Nouveau
                </span>
            @endif
        </div>
        
        <!-- Prix -->
        <div class="absolute top-4 right-4 flex flex-col items-end gap-1">
            @if($hasOffer)
                <span class="px-2 py-0.5 text-xs text-white/80 line-through bg-gray-950/60 rounded-full">
                    {{ $symbol }} {{ number_format((float) $item->price, 2) }}
                </span>
                <span class="px-4 py-2 {{ $isBoosted ? 'bg-gray-800 shadow-lg' : 'bg-gray-900' }} text-white rounded-full text-sm font-bold shadow-lg">
                    {{ $symbol }} {{ number_format((float) $salePrice, 2) }}
                </span>
            @else
                <span class="px-4 py-2 {{ $isBoosted ? 'bg-gray-800 shadow-lg' : 'bg-gray-900' }} text-white rounded-full text-sm font-bold shadow-lg">
                    {{ $item->formatted_price }}
                </span>
            @endif
        </div>
    </div>
    
    <!-- Contenu -->
    <div class="p-5 lg:p-6 {{ $isBoosted ? 'bg-gray-50/50 dark:bg-gray-700/30' : '' }}">
        <div class="space-y-3">
            <h3 class="font-bold text-base lg:text-lg text-gray-900 dark:text-white line-clamp-2 min-h-[3rem] leading-tight">
                {{ $item->name }}
            </h3>
            
            <div class="flex items-center justify-between">
                <span class="px-3 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 rounded-full text-xs font-semibold">
                    {{ $item->category->name ?? 'Vintage' }}
                </span>
                
                @if($item->condition)
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ ucfirst($item->condition) }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Lien invisible -->
    <a href="{{ route('items.show', $item) }}" class="absolute inset-0 z-10" aria-label="Voir {{ $item->name }}"></a>
</article>
