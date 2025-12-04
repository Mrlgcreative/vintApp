<section class="max-w-[1400px] mx-auto px-6 lg:px-12 py-24">
    <div class="bg-black text-white rounded-3xl p-12 lg:p-20 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        
        <div class="relative max-w-3xl mx-auto space-y-8">
            <h2 class="font-display text-4xl lg:text-6xl font-bold leading-tight">
                Prêt à Construire Votre
                <span class="block italic text-gray-400">Collection Vintage ?</span>
            </h2>
            <p class="text-lg text-gray-400 max-w-2xl mx-auto">
                Rejoignez des milliers d'amateurs de vintage et découvrez des pièces uniques à prix imbattables
            </p>
            <div class="flex flex-wrap gap-4 justify-center pt-4">
                <a href="{{ route('items.index') }}" 
                   class="inline-flex items-center gap-3 px-8 py-4 bg-white text-black rounded-full font-medium hover:bg-gray-100 transition-all">
                    Commencer à Acheter
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="{{ route('items.create') ?? '#' }}" 
                   class="inline-flex items-center gap-3 px-8 py-4 border-2 border-white text-white rounded-full font-medium hover:bg-white hover:text-black transition-all">
                    Vendre vos Articles
                </a>
            </div>
        </div>
    </div>
</section>
