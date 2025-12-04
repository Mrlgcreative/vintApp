@props(['categories'])

<div id="filterModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-8 py-6 flex items-center justify-between z-10 rounded-t-3xl">
            <h3 class="text-2xl font-bold">Filtres</h3>
            <button onclick="toggleFiltersModal()" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form action="{{ route('items.index') }}" method="GET" id="filterForm" class="p-8 space-y-8">
            <div>
                <label class="block text-sm font-semibold mb-3">Catégorie</label>
                <select name="category" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-black focus:outline-none transition-all">
                    <option value="">Toutes les Catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold mb-3">Plage de Prix</label>
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" 
                           name="price_min" 
                           placeholder="Min" 
                           value="{{ request('price_min') }}" 
                           class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-black focus:outline-none transition-all" />
                    <input type="number" 
                           name="price_max" 
                           placeholder="Max" 
                           value="{{ request('price_max') }}" 
                           class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-black focus:outline-none transition-all" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-3">Trier par</label>
                <select name="sort" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-black focus:outline-none transition-all">
                    <option value="">Pertinence</option>
                    <option value="recent" {{ request('sort') === 'recent' ? 'selected' : '' }}>Plus Récent</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Populaire</option>
                    <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Prix: Croissant</option>
                    <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Prix: Décroissant</option>
                </select>
            </div>
            
            <div class="flex gap-4 pt-4 sticky bottom-0 bg-white pb-2">
                <button type="button" 
                        onclick="resetFilters()" 
                        class="flex-1 px-6 py-4 border-2 border-gray-200 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    Réinitialiser
                </button>
                <button type="submit" 
                        class="flex-1 px-6 py-4 bg-black text-white rounded-xl font-semibold hover:bg-gray-900 transition-all">
                    Appliquer
                </button>
            </div>
        </form>
    </div>
</div>
