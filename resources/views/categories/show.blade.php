@extends('app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de la catégorie -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-primary-600/10 border border-gray-100/50 overflow-hidden mb-8">
            <div class="p-6 lg:p-8">
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-6">
                    <!-- Informations de la catégorie -->
                    <div class="flex items-start space-x-4">
                        <!-- Icône -->
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 lg:w-20 lg:h-20 bg-gradient-to-r from-primary-100 to-primary-200 rounded-2xl flex items-center justify-center">
                                @if($category->icon)
                                    <i class="{{ $category->icon }} text-primary-600 text-3xl lg:text-4xl"></i>
                                @else
                                    <i class="fas fa-folder text-primary-600 text-3xl lg:text-4xl"></i>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Détails -->
                        <div class="flex-1 min-w-0">
                            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $category->name }}</h1>
                            @if($category->description)
                                <p class="text-gray-600 dark:text-gray-300 mb-4 leading-relaxed">{{ $category->description }}</p>
                            @endif
                            
                            <!-- Badges et informations -->
                            <div class="flex flex-wrap items-center gap-3">
                                <!-- Statut -->
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                        Inactive
                                    </span>
                                @endif
                                
                                <!-- Nombre d'articles -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                    <i class="fas fa-box mr-2"></i>
                                    {{ $category->items_count ?? 0 }} article{{ ($category->items_count ?? 0) > 1 ? 's' : '' }}
                                </span>
                                
                                <!-- Catégorie parente -->
                                @if($category->parent)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">
                                        <i class="fas fa-level-up-alt mr-2"></i>
                                        Sous-catégorie de 
                                        <a href="{{ route('categories.show', $category->parent) }}" 
                                           class="ml-1 underline hover:no-underline">
                                            {{ $category->parent->name }}
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menu d'actions -->
                    <div class="flex-shrink-0">
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                                <i class="fas fa-cog mr-2"></i>
                                Actions
                                <i class="fas fa-chevron-down ml-2 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                <div class="py-2">
                                    <a href="{{ route('categories.edit', $category) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-primary-50 hover:text-primary-600 transition-colors duration-200">
                                        <i class="fas fa-edit mr-3"></i>
                                        Modifier la catégorie
                                    </a>
                                    <a href="{{ route('items.index', ['category' => $category->id]) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200">
                                        <i class="fas fa-list mr-3"></i>
                                        Voir tous les articles
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Tous les articles associés seront également supprimés.')">
                                            <i class="fas fa-trash mr-3"></i>
                                            Supprimer la catégorie
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sous-catégories -->
        @if(isset($subcategories) && $subcategories->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-primary-600/10 border border-gray-100/50 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-sitemap text-primary-500 mr-2"></i>
                    Sous-catégories
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($subcategories as $subcategory)
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6 text-center hover:shadow-lg hover:bg-primary-50 transition-all duration-300">
                        <!-- Icône -->
                        <div class="w-12 h-12 bg-gradient-to-r from-primary-100 to-primary-200 rounded-xl flex items-center justify-center mx-auto mb-4">
                            @if($subcategory->icon)
                                <i class="{{ $subcategory->icon }} text-primary-600 text-xl"></i>
                            @else
                                <i class="fas fa-folder text-primary-600 text-xl"></i>
                            @endif
                        </div>
                        
                        <!-- Nom -->
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $subcategory->name }}</h4>
                        
                        <!-- Description -->
                        @if($subcategory->description)
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 leading-relaxed">{{ Str::limit($subcategory->description, 60) }}</p>
                        @endif
                        
                        <!-- Badge articles -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 mb-4">
                            {{ $subcategory->items_count ?? 0 }} article{{ ($subcategory->items_count ?? 0) > 1 ? 's' : '' }}
                        </span>
                        
                        <!-- Bouton -->
                        <div>
                            <a href="{{ route('categories.show', $subcategory) }}" 
                               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors duration-200">
                                Voir la catégorie
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Articles de cette catégorie -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-primary-600/10 border border-gray-100/50 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-box text-primary-500 mr-2"></i>
                    Articles de cette catégorie
                </h3>
                <a href="{{ route('items.create', ['category' => $category->id]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Ajouter un article
                </a>
            </div>
            
            @if(isset($items) && $items->count() > 0)
                <!-- Version Desktop : Tableau -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Prix</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">État</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Vendeur</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($items as $item)
                            <tr class="hover:bg-primary-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center overflow-hidden">
                                        @if($item->images)
                                            @php
                                                $images = is_array($item->images) ? $item->images : json_decode($item->images, true);
                                            @endphp
                                            @if($images && is_array($images) && count($images) > 0)
                                                <img src="{{ asset('storage/' . $images[0]) }}" 
                                                     alt="{{ $item->name }}" 
                                                     class="w-full h-full object-cover"
                                                     loading="lazy">
                                            @else
                                                <i class="fas fa-image text-gray-400"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-image text-gray-400"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $item->name }}</div>
                                    <div class="text-gray-600 dark:text-gray-300 text-sm">{{ Str::limit($item->description, 40) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $item->formatted_price ?? $item->price . ' FC' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                        @if(isset($item->condition_color))
                                            @if($item->condition_color == 'success') bg-emerald-100 text-emerald-800
                                            @elseif($item->condition_color == 'warning') bg-amber-100 text-amber-800
                                            @elseif($item->condition_color == 'danger') bg-red-100 text-red-800
                                            @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                            @endif
                                        @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                        @endif">
                                        {{ ucfirst($item->condition) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                        @if(isset($item->status_color))
                                            @if($item->status_color == 'success') bg-emerald-100 text-emerald-800
                                            @elseif($item->status_color == 'warning') bg-amber-100 text-amber-800
                                            @elseif($item->status_color == 'danger') bg-red-100 text-red-800
                                            @elseif($item->status_color == 'info') bg-blue-100 text-blue-800
                                            @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                            @endif
                                        @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                        @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-gray-900 dark:text-white">{{ $item->user->name ?? 'Inconnu' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('items.show', $item) }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 hover:text-primary-700 transition-colors duration-200"
                                       title="Voir l'article">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Version Mobile : Cartes -->
                <div class="lg:hidden">
                    @foreach($items as $item)
                    <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0 p-6 hover:bg-primary-50 transition-colors duration-200">
                        <div class="flex items-start space-x-4">
                            <!-- Image -->
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center overflow-hidden">
                                    @if($item->images)
                                        @php
                                            $images = is_array($item->images) ? $item->images : json_decode($item->images, true);
                                        @endphp
                                        @if($images && is_array($images) && count($images) > 0)
                                            <img src="{{ asset('storage/' . $images[0]) }}" 
                                                 alt="{{ $item->name }}" 
                                                 class="w-full h-full object-cover"
                                                 loading="lazy">
                                        @else
                                            <i class="fas fa-image text-gray-400 text-xl"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-image text-gray-400 text-xl"></i>
                                    @endif
                                </div>
                            </div>

                            <!-- Contenu -->
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $item->name }}</h4>
                                <p class="text-gray-600 dark:text-gray-300 text-sm mb-2">{{ Str::limit($item->description, 60) }}</p>
                                <div class="font-bold text-gray-900 dark:text-white mb-2">{{ $item->formatted_price ?? $item->price . ' FC' }}</div>
                                
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold 
                                        @if(isset($item->condition_color))
                                            @if($item->condition_color == 'success') bg-emerald-100 text-emerald-800
                                            @elseif($item->condition_color == 'warning') bg-amber-100 text-amber-800
                                            @elseif($item->condition_color == 'danger') bg-red-100 text-red-800
                                            @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                            @endif
                                        @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                        @endif">
                                        {{ ucfirst($item->condition) }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold 
                                        @if(isset($item->status_color))
                                            @if($item->status_color == 'success') bg-emerald-100 text-emerald-800
                                            @elseif($item->status_color == 'warning') bg-amber-100 text-amber-800
                                            @elseif($item->status_color == 'danger') bg-red-100 text-red-800
                                            @elseif($item->status_color == 'info') bg-blue-100 text-blue-800
                                            @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                            @endif
                                        @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                        @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                                
                                <div class="text-gray-600 dark:text-gray-300 text-sm mb-3">Vendeur : {{ $item->user->name ?? 'Inconnu' }}</div>
                                
                                <a href="{{ route('items.show', $item) }}" 
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors duration-200">
                                    <i class="fas fa-eye mr-2"></i>
                                    Voir l'article
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if(method_exists($items, 'links'))
                <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                    {{ $items->links() }}
                </div>
                @endif
            @else
                <!-- État vide -->
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Aucun article dans cette catégorie</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-sm">Commencez par ajouter votre premier article dans cette catégorie</p>
                        <a href="{{ route('items.create', ['category' => $category->id]) }}" 
                           class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl shadow-lg hover:bg-primary-700 hover:shadow-xl transition-all duration-300">
                            <i class="fas fa-plus mr-2"></i>
                            Ajouter le premier article
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Bouton retour -->
        <div class="flex justify-start">
            <a href="{{ route('categories.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 dark:bg-gray-900 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour aux catégories
            </a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection