@extends('app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header avec titre et bouton d'ajout -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-tags text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Marques</h1>
                    <p class="text-gray-600 text-sm mt-1">Gérez les marques de vos produits</p>
                </div>
            </div>
            <a href="{{ route('brands.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:from-indigo-600 hover:to-indigo-700 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <i class="fas fa-plus mr-2"></i>
                Ajouter une marque
            </a>
        </div>

        <!-- Tableau des marques -->
        <div class="bg-white rounded-2xl shadow-xl shadow-indigo-600/10 border border-gray-100/50 overflow-hidden">
            <!-- Header du tableau -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-indigo-500 mr-2"></i>
                    Liste des marques
                </h3>
            </div>

            <!-- Version Desktop : Tableau -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Logo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Site web</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($brands as $brand)
                            <tr class="hover:bg-indigo-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($brand->logo)
                                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center overflow-hidden">
                                            <img src="{{ asset('storage/' . $brand->logo) }}" 
                                                 alt="Logo {{ $brand->name }}" 
                                                 class="w-full h-full object-contain">
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                                            <span class="text-gray-400 text-xs">-</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900">{{ $brand->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-600 text-sm max-w-xs">{{ Str::limit($brand->description, 40) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($brand->website)
                                        <a href="{{ $brand->website }}" 
                                           target="_blank" 
                                           class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                            {{ Str::limit($brand->website, 30) }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($brand->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('brands.edit', $brand) }}" 
                                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 hover:text-indigo-700 transition-colors duration-200">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('brands.destroy', $brand) }}" 
                                              method="POST" 
                                              class="inline-block" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette marque ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-tags text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune marque trouvée</h3>
                                        <p class="text-gray-600 text-sm mb-6">Commencez par ajouter votre première marque</p>
                                        <a href="{{ route('brands.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                            <i class="fas fa-plus mr-2"></i>
                                            Ajouter une marque
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Version Mobile : Cartes -->
            <div class="lg:hidden">
                @forelse($brands as $brand)
                    <div class="border-b border-gray-200 last:border-b-0 p-6 hover:bg-indigo-50 transition-colors duration-200">
                        <div class="flex items-start space-x-4">
                            <!-- Logo -->
                            <div class="flex-shrink-0">
                                @if($brand->logo)
                                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center overflow-hidden">
                                        <img src="{{ asset('storage/' . $brand->logo) }}" 
                                             alt="Logo {{ $brand->name }}" 
                                             class="w-full h-full object-contain">
                                    </div>
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-tags text-gray-400 text-xl"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Contenu -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $brand->name }}</h3>
                                        @if($brand->description)
                                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($brand->description, 80) }}</p>
                                        @endif
                                        @if($brand->website)
                                            <a href="{{ $brand->website }}" 
                                               target="_blank" 
                                               class="text-indigo-600 hover:text-indigo-800 font-medium text-sm mb-2 block">
                                                {{ Str::limit($brand->website, 40) }}
                                            </a>
                                        @endif
                                        <div class="flex items-center space-x-3">
                                            @if($brand->is_active)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                    <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2 mt-4">
                                    <a href="{{ route('brands.edit', $brand) }}" 
                                       class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                                        <i class="fas fa-edit mr-2"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('brands.destroy', $brand) }}" 
                                          method="POST" 
                                          class="flex-1" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette marque ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                            <i class="fas fa-trash mr-2"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-tags text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Aucune marque trouvée</h3>
                            <p class="text-gray-600 mb-8 max-w-sm">Commencez par ajouter votre première marque pour organiser vos produits</p>
                            <a href="{{ route('brands.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:bg-indigo-700 hover:shadow-xl transition-all duration-300">
                                <i class="fas fa-plus mr-2"></i>
                                Ajouter une marque
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection 