@extends('layouts.admin')

@section('title', 'Zones Autorisées')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                <i class="fas fa-map-marked-alt text-primary-600 mr-2"></i>
                Zones Autorisées
            </h1>
            <p class="text-gray-600 mt-1">
                Gérez les villes et régions ayant accès à VintApp
            </p>
        </div>
        
        <div class="flex gap-3">
            <button onclick="openModal('addCityModal')" 
                    class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Ajouter une ville
            </button>
            <button onclick="openModal('addRegionModal')" 
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Ajouter une région
            </button>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-500 text-xl mt-0.5"></i>
            <div class="flex-1">
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-red-800 font-semibold mb-2">Erreur(s) :</p>
                    <ul class="list-disc list-inside space-y-1 text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">Villes Totales</p>
                    <p class="text-3xl font-bold text-blue-900 mt-1">{{ $stats['total_cities'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-city text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600">Villes Actives</p>
                    <p class="text-3xl font-bold text-green-900 mt-1">{{ $stats['active_cities'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600">Régions Totales</p>
                    <p class="text-3xl font-bold text-purple-900 mt-1">{{ $stats['total_regions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-6 border border-pink-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-pink-600">Régions Actives</p>
                    <p class="text-3xl font-bold text-pink-900 mt-1">{{ $stats['active_regions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-pink-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-double text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Action rapide -->
    @if($stats['total_cities'] == 0)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
            <i class="fas fa-lightbulb text-blue-500 text-4xl mb-3"></i>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Démarrage rapide</h3>
            <p class="text-blue-700 mb-4">Ajoutez les principales villes de RDC en un clic</p>
            <form action="{{ route('admin.locations.seed') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-magic mr-2"></i>
                    Initialiser les villes par défaut
                </button>
            </form>
        </div>
    @endif

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="switchTab('cities')" 
                        id="tab-cities"
                        class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-primary-600 text-primary-600">
                    <i class="fas fa-city mr-2"></i>
                    Villes ({{ $cities->total() }})
                </button>
                <button onclick="switchTab('regions')" 
                        id="tab-regions"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-map-marked mr-2"></i>
                    Régions ({{ $regions->total() }})
                </button>
            </nav>
        </div>

        <!-- Tab Content: Villes -->
        <div id="content-cities" class="tab-content p-6">
            @if($cities->count() > 0)
                <!-- Vue Desktop (Table) -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Région</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pays</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cities as $city)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900">{{ $city->name }}</div>
                                    @if($city->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($city->description, 40) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $city->region ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $city->country }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($city->city_code)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $city->city_code }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="toggleCityStatus({{ $city->id }})" 
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $city->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ $city->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="editCity({{ $city->id }})" 
                                            class="text-primary-600 hover:text-primary-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteCity({{ $city->id }}, '{{ $city->name }}')" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Vue Mobile (Cards) -->
                <div class="lg:hidden space-y-4">
                    @foreach($cities as $city)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $city->name }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $city->region ?? 'Région non spécifiée' }}</p>
                            </div>
                            <button onclick="toggleCityStatus({{ $city->id }})" 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $city->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $city->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                            <div>
                                <span class="text-gray-500">Pays:</span>
                                <span class="text-gray-900 ml-1">{{ $city->country }}</span>
                            </div>
                            @if($city->city_code)
                            <div>
                                <span class="text-gray-500">Code:</span>
                                <span class="text-gray-900 ml-1">{{ $city->city_code }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex gap-2 pt-3 border-t border-gray-300">
                            <button onclick="editCity({{ $city->id }})" 
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-colors">
                                <i class="fas fa-edit mr-2"></i>
                                Modifier
                            </button>
                            <button onclick="deleteCity({{ $city->id }}, '{{ $city->name }}')" 
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $cities->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-city text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune ville enregistrée</h3>
                    <p class="text-gray-600 mb-4">Commencez par ajouter les villes autorisées</p>
                    <button onclick="openModal('addCityModal')" 
                            class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Ajouter une ville
                    </button>
                </div>
            @endif
        </div>

        <!-- Tab Content: Régions -->
        <div id="content-regions" class="tab-content hidden p-6">
            @if($regions->count() > 0)
                <!-- Vue Desktop (Table) -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Région</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pays</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($regions as $region)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900">{{ $region->name }}</div>
                                    @if($region->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($region->description, 40) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $region->country }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($region->region_code)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $region->region_code }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="toggleRegionStatus({{ $region->id }})" 
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $region->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ $region->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="editRegion({{ $region->id }})" 
                                            class="text-primary-600 hover:text-primary-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteRegion({{ $region->id }}, '{{ $region->name }}')" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Vue Mobile (Cards) -->
                <div class="lg:hidden space-y-4">
                    @foreach($regions as $region)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $region->name }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $region->country }}</p>
                            </div>
                            <button onclick="toggleRegionStatus({{ $region->id }})" 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $region->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $region->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </div>
                        
                        @if($region->region_code)
                        <div class="text-sm mb-3">
                            <span class="text-gray-500">Code:</span>
                            <span class="text-gray-900 ml-1">{{ $region->region_code }}</span>
                        </div>
                        @endif
                        
                        <div class="flex gap-2 pt-3 border-t border-gray-300">
                            <button onclick="editRegion({{ $region->id }})" 
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-colors">
                                <i class="fas fa-edit mr-2"></i>
                                Modifier
                            </button>
                            <button onclick="deleteRegion({{ $region->id }}, '{{ $region->name }}')" 
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $regions->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-map text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune région enregistrée</h3>
                    <p class="text-gray-600 mb-4">Commencez par ajouter les régions autorisées</p>
                    <button onclick="openModal('addRegionModal')" 
                            class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Ajouter une région
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Ajouter une ville -->
<div id="addCityModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeModal('addCityModal')"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-city text-primary-600 mr-2"></i>
                    Ajouter une nouvelle ville
                </h3>
            </div>
            
            <form action="{{ route('admin.locations.cities.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la ville <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Ex: Kinshasa">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Région/Province</label>
                    <input type="text" name="region" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Ex: Kinshasa">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pays <span class="text-red-500">*</span></label>
                    <select name="country" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="Congo (RDC)" selected>Congo (RDC)</option>
                        <option value="Congo (Brazzaville)">Congo (Brazzaville)</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code unique</label>
                    <input type="text" name="city_code" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Ex: KIN-01">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Informations complémentaires..."></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked 
                           class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label class="ml-2 text-sm text-gray-700">Activer immédiatement</label>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('addCityModal')" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Ajouter une région -->
<div id="addRegionModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeModal('addRegionModal')"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-map text-purple-600 mr-2"></i>
                    Ajouter une nouvelle région
                </h3>
            </div>
            
            <form action="{{ route('admin.locations.regions.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la région <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Ex: Haut-Katanga">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pays <span class="text-red-500">*</span></label>
                    <select name="country" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="Congo (RDC)" selected>Congo (RDC)</option>
                        <option value="Congo (Brazzaville)">Congo (Brazzaville)</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code unique</label>
                    <input type="text" name="region_code" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Ex: HK-01">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Informations complémentaires..."></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked 
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label class="ml-2 text-sm text-gray-700">Activer immédiatement</label>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('addRegionModal')" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Gestion des tabs
function switchTab(tab) {
    // Masquer tous les contenus
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Réinitialiser tous les boutons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-primary-600', 'text-primary-600', 'active');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Afficher le contenu actif
    document.getElementById(`content-${tab}`).classList.remove('hidden');
    
    // Activer le bouton
    const activeButton = document.getElementById(`tab-${tab}`);
    activeButton.classList.add('border-primary-600', 'text-primary-600', 'active');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}

// Gestion des modals
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Toggle statut ville
function toggleCityStatus(cityId) {
    if (!confirm('Changer le statut de cette ville ?')) return;
    
    fetch(`/admin/settings/locations/cities/${cityId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Erreur:', error));
}

// Toggle statut région
function toggleRegionStatus(regionId) {
    if (!confirm('Changer le statut de cette région ?')) return;
    
    fetch(`/admin/settings/locations/regions/${regionId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Erreur:', error));
}

// Supprimer ville
function deleteCity(cityId, cityName) {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer la ville "${cityName}" ?`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/settings/locations/cities/${cityId}`;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    
    form.appendChild(csrfInput);
    form.appendChild(methodInput);
    document.body.appendChild(form);
    form.submit();
}

// Supprimer région
function deleteRegion(regionId, regionName) {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer la région "${regionName}" ?`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/settings/locations/regions/${regionId}`;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    
    form.appendChild(csrfInput);
    form.appendChild(methodInput);
    document.body.appendChild(form);
    form.submit();
}

// Edit functions (à implémenter)
function editCity(cityId) {
    alert('Fonctionnalité d\'édition à implémenter');
}

function editRegion(regionId) {
    alert('Fonctionnalité d\'édition à implémenter');
}
</script>
@endpush

@endsection
