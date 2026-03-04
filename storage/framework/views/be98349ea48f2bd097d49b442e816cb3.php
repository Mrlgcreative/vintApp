<section class="relative -mt-32 z-40 container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-100 p-3 hover:shadow-3xl transition-all duration-500 transform hover:scale-[1.02]">
        <form action="<?php echo e(route('items.index')); ?>" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="search" 
                       name="q" 
                       value="<?php echo e(request('q')); ?>" 
                       placeholder="Rechercher des pièces vintage..." 
                       class="w-full h-16 pl-14 pr-6 rounded-2xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-purple-100 focus:outline-none transition-all text-base font-medium placeholder:text-gray-400" />
            </div>
            
            <div class="flex gap-3">
                <button type="button" 
                        onclick="toggleFiltersModal()" 
                        class="h-16 px-6 rounded-2xl border-2 border-gray-200 hover:border-purple-300 hover:bg-purple-50 text-gray-700 hover:text-purple-700 transition-all flex items-center gap-2 font-medium group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span class="hidden sm:inline">Filtres</span>
                </button>
                
                <button type="submit" 
                        class="h-16 px-8 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl font-bold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-xl hover:shadow-2xl hover:shadow-purple-500/30 transform hover:scale-105">
                    <span class="hidden sm:inline">Rechercher</span>
                    <svg class="w-5 h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</section>
<?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/components/home/search-bar.blade.php ENDPATH**/ ?>