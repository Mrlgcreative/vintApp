<section class="relative -mt-16 sm:-mt-28 z-40 container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-2">
        <form action="{{ route('items.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="search" 
                       name="q" 
                       value="{{ request('q') }}" 
                       placeholder="Rechercher des pieces vintage..." 
                       class="w-full h-12 pl-12 pr-4 rounded-xl bg-gray-50 dark:bg-gray-700 border-0 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 focus:outline-none transition-all text-sm font-medium text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-400" />
            </div>
            
            <div class="flex gap-2">
                <button type="button" 
                        onclick="toggleFiltersModal()" 
                        class="h-12 px-4 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-all flex items-center gap-2 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span class="hidden sm:inline">Filtres</span>
                </button>
                
                <button type="submit" 
                        class="h-12 px-6 bg-gray-900 text-white rounded-xl font-semibold text-sm hover:bg-gray-800 transition-all">
                    <span class="hidden sm:inline">Rechercher</span>
                    <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</section>
