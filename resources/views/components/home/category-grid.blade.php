@props(['categories'])

<section class="py-14 lg:py-20 bg-gradient-to-b from-violet-50/60 to-white dark:from-gray-800/60 dark:to-gray-900">
    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600 dark:text-violet-400">Parcourir</span>
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    Catégories
                </h2>
            </div>
            <a href="{{ route('items.index') }}"
               class="hidden lg:inline-flex items-center gap-1.5 text-sm font-medium text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300 transition-colors">
                Tout voir
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2.5 lg:gap-3">
            @php
                $icons = [
                    'fas fa-tshirt', 'fas fa-female', 'fas fa-shoe-prints', 'fas fa-shopping-bag',
                    'far fa-clock', 'fas fa-glasses', 'fas fa-vest', 'fas fa-crown',
                    'fas fa-suitcase', 'fas fa-gem', 'fas fa-headphones', 'fas fa-couch',
                ];
                $lightBgs = [
                    'bg-violet-50 text-violet-600 dark:bg-violet-900/40 dark:text-violet-300',
                    'bg-pink-50 text-pink-600 dark:bg-pink-900/40 dark:text-pink-300',
                    'bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300',
                    'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300',
                    'bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300',
                    'bg-rose-50 text-rose-600 dark:bg-rose-900/40 dark:text-rose-300',
                    'bg-teal-50 text-teal-600 dark:bg-teal-900/40 dark:text-teal-300',
                    'bg-orange-50 text-orange-600 dark:bg-orange-900/40 dark:text-orange-300',
                    'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300',
                    'bg-cyan-50 text-cyan-600 dark:bg-cyan-900/40 dark:text-cyan-300',
                    'bg-violet-50 text-violet-600 dark:bg-violet-900/40 dark:text-violet-300',
                    'bg-pink-50 text-pink-600 dark:bg-pink-900/40 dark:text-pink-300',
                ];
            @endphp

            @forelse($categories as $index => $category)
                @php $i = $index % count($lightBgs); @endphp
                <a href="{{ route('items.index', ['category' => $category->id]) }}"
                   class="group flex flex-col items-center gap-1.5 p-3 lg:p-4 rounded-xl bg-white/70 hover:bg-white dark:bg-gray-800/50 dark:hover:bg-gray-800 border border-transparent hover:border-violet-100 dark:hover:border-violet-800 transition-all duration-200 hover:shadow-sm hover:shadow-violet-200/40 dark:hover:shadow-violet-900/20">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg {{ $lightBgs[$i] }} flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <i class="{{ $category->icon ?? $icons[$index % count($icons)] }} text-sm lg:text-base"></i>
                    </div>
                    <h3 class="font-medium text-[11px] lg:text-xs text-gray-800 dark:text-gray-200 text-center leading-tight">
                        {{ $category->name }}
                    </h3>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-sm text-gray-400 dark:text-gray-500">Catégories à venir</p>
                </div>
            @endforelse
        </div>

        @if($categories && $categories->count() > 6)
            <div class="mt-6 text-center lg:hidden">
                <a href="{{ route('items.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300 transition-colors">
                    Voir toutes les catégories
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</section>
