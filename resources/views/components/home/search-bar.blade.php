<section class="relative -mt-14 sm:-mt-20 z-40 container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="relative" x-data="searchAutoComplete()">
        <div class="rounded-2xl bg-white dark:bg-gray-900 shadow-2xl shadow-black/20 ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
            <form action="{{ route('items.index') }}" method="GET" x-on:submit="submitSearch($event)" class="flex flex-col sm:flex-row items-stretch sm:items-center">
                {{-- Champ recherche principal --}}
                <div class="flex-1 flex items-center gap-2 sm:gap-3 px-4 py-3 sm:px-6 sm:py-4 relative">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search"
                           name="q"
                           x-ref="input"
                           x-model="query"
                           x-on:input="onInput()"
                           x-on:focus="open=true"
                           x-on:keydown.down.prevent="move(1)"
                           x-on:keydown.up.prevent="move(-1)"
                           x-on:keydown.enter.prevent="submitSearch($event)"
                           x-on:click.away="close()"
                           value="{{ request('q') }}"
                           autocomplete="off"
                           placeholder="Rechercher une pièce vintage..."
                           class="w-full bg-transparent border-0 p-0 text-sm sm:text-base font-medium text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-0 focus:border-0" />
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center">
                        <i x-show="loading" x-cloak class="fas fa-spinner fa-spin text-sm text-gray-400"></i>
                        <button type="button" x-show="query" x-on:click="clear()" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 transition-colors" aria-label="Effacer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                </div>

                {{-- Séparateur --}}
                <div class="hidden sm:block w-px self-stretch my-4 bg-gray-200 dark:bg-gray-700"></div>

                {{-- Boutons --}}
                <div class="flex items-center gap-2 px-4 py-3 sm:px-4 sm:py-4 sm:border-l border-t sm:border-t-0 border-gray-200 dark:border-gray-700">
                    <button type="button"
                            onclick="toggleFiltersModal()"
                            class="h-9 sm:h-11 px-3 sm:px-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition-all flex items-center gap-1.5 sm:gap-2 text-sm font-medium">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        <span class="hidden sm:inline">Filtres</span>
                    </button>

                    {{-- Bouton Rechercher --}}
                    <button type="submit"
                            class="h-9 sm:h-11 flex-1 sm:flex-none px-4 sm:px-7 inline-flex items-center justify-center gap-2 bg-vinted-primary text-white rounded-xl font-semibold text-sm hover:bg-vinted-primary-700 transition-all shadow-lg shadow-vinted-primary/30 hover:shadow-vinted-primary/40">
                        <span class="hidden sm:inline">Rechercher</span>
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Suggestions --}}
        <div x-show="open && (loading || hasResults || error || query.length > 0)" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute left-0 right-0 mt-2 bg-white dark:bg-gray-900 rounded-xl shadow-2xl shadow-black/20 ring-1 ring-black/5 dark:ring-white/10 z-50 overflow-hidden">
            <!-- État chargement -->
            <div x-show="loading" class="p-4 flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-spinner fa-spin text-gray-400 dark:text-gray-500"></i>
                Recherche en cours...
            </div>

            <!-- État erreur -->
            <div x-show="!loading && error" class="p-4 text-sm text-red-500 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                <span x-text="error"></span>
            </div>

            <!-- État vide -->
            <div x-show="!loading && !error && query && hasResults === false" class="p-4 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                Aucun résultat pour <strong class="truncate" x-text="query"></strong>
            </div>

            <!-- Résultats -->
            <template x-if="hasResults">
                <div>
                    <div class="px-4 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 border-b border-gray-100 dark:border-gray-700">
                        Résultats
                    </div>
                    <ul class="max-h-80 overflow-y-auto py-1">
                        <template x-for="(item, i) in results" :key="item.id">
                            <li>
                                <a :href="item.url || '/items/search?q=' + encodeURIComponent(query)" x-on:click="close()"
                                   class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                                   :class="i === activeIndex ? 'bg-gray-100 dark:bg-gray-800' : ''">
                                    <img :src="item.first_image_url || '/images/placeholder.png'"
                                         :alt="item.name"
                                         class="w-10 h-10 rounded-lg object-cover flex-shrink-0 bg-gray-100 dark:bg-gray-700"
                                         x-on:error="$el.src='/images/placeholder.png'">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="item.name"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                            <i class="fas fa-store text-gray-400"></i>
                                            <span class="truncate" x-text="item.user?.name || 'Vendeur'"></span>
                                        </p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white flex-shrink-0" x-text="item.formatted_price"></span>
                                </a>
                            </li>
                        </template>
                    </ul>
                    <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700">
                        <a :href="'/items/search?q=' + encodeURIComponent(query)" x-on:click="close()"
                           class="flex items-center justify-center gap-2 text-sm text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 font-medium">
                            Voir tous les résultats pour <span class="truncate block max-w-[50%]" x-text="query"></span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>
</section>