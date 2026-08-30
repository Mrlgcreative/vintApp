@extends('layouts.admin')

@section('title', 'Zones Autorisées')

@section('page-title', 'Zones Autorisées')
@section('page-subtitle', 'Gérez les villes et régions ayant accès à VintApp')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <button type="button" onclick="openModal('addCityModal')"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-plus"></i>Ajouter une ville
    </button>
    <button type="button" onclick="openModal('addRegionModal')"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-accent-600 hover:bg-accent-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-plus"></i>Ajouter une région
    </button>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Alerts -->
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20 px-4 py-3">
            <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
            <p class="flex-1 text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
            <button type="button" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 px-4 py-3">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm text-red-800 dark:text-red-200 font-semibold mb-2">Erreur(s) :</p>
                    <ul class="list-disc list-inside space-y-1 text-sm text-red-700 dark:text-red-300">
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
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total Villes</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-blue-600">{{ $stats['total_cities'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-400">
                    <i class="fas fa-city text-[10px]"></i>
                    Villes
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-city text-xs text-blue-500"></i>
                    Toutes les villes
                </div>
                <div class="text-xs text-slate-400">Villes enregistrées et géolocalisées</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Villes Actives</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-green-600">{{ $stats['active_cities'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-green-200 bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-400">
                    <i class="fas fa-check-circle text-[10px]"></i>
                    Actives
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-check-circle text-xs text-green-500"></i>
                    Actuellement autorisées
                </div>
                <div class="text-xs text-slate-400">Statu actif sur la plateforme</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total Régions</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-primary-600">{{ $stats['total_regions'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-primary-200 bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700 dark:border-primary-500/30 dark:bg-primary-500/10 dark:text-primary-400">
                    <i class="fas fa-map text-[10px]"></i>
                    Régions
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-map text-xs text-primary-500"></i>
                    Toutes les régions
                </div>
                <div class="text-xs text-slate-400">Zones régionales autorisées</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Pays Couverts</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-orange-600">{{ $stats['countries_count'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-orange-200 bg-orange-50 px-2 py-0.5 text-xs font-medium text-orange-700 dark:border-orange-500/30 dark:bg-orange-500/10 dark:text-orange-400">
                    <i class="fas fa-globe-africa text-[10px]"></i>
                    Pays
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-globe-africa text-xs text-orange-500"></i>
                    Pays distincts
                </div>
                <div class="text-xs text-slate-400">Pays avec villes enregistrées</div>
            </div>
        </div>
    </div>

    <!-- 🗺️ Carte GPS Interactive -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-primary-600"></i>
                    Carte GPS Interactive
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    <span id="map-city-count">0</span> villes avec coordonnées GPS
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="fitAllMarkers()" 
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i class="fas fa-compress-arrows-alt"></i>
                    Tout afficher
                </button>
                <button onclick="enableMapMarkerMode()" 
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-3 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    <i class="fas fa-map-pin"></i>
                    Marquer
                </button>
                <button onclick="refreshMapData()" 
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-green-700">
                    <i class="fas fa-sync-alt"></i>
                    Actualiser
                </button>
            </div>
        </div>
        
        <!-- Carte -->
        <div class="relative">
            <div id="map" class="w-full h-96 rounded-lg border border-slate-300 dark:border-slate-600"></div>
            <div id="map-loading" class="absolute inset-0 bg-white/90 dark:bg-slate-800/90 rounded-lg hidden">
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-4xl text-primary-600 mb-3"></i>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Chargement de la carte...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mode marquage indicateur -->
        <div id="mapModeIndicator" class="hidden mt-3 flex items-center justify-between gap-3 rounded-xl border border-sky-200 bg-sky-50 dark:border-sky-800 dark:bg-sky-900/20 px-4 py-3">
            <div class="flex items-center gap-2">
                <i class="fas fa-info-circle text-sky-600 dark:text-sky-400"></i>
                <span class="text-sm text-sky-800 dark:text-sky-200 font-medium">
                    Mode marquage activé - Cliquez sur la carte pour placer une ville
                </span>
            </div>
            <button onclick="disableMapMarkerMode()"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-sky-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-sky-700">
                <i class="fas fa-times"></i> Annuler
            </button>
        </div>

        <!-- Légende -->
        <div class="mt-4 flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-green-500 text-lg"></i>
                <span class="text-slate-700 dark:text-slate-300">Villes actives</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-slate-400 text-lg"></i>
                <span class="text-slate-700 dark:text-slate-300">Villes inactives</span>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="border-b border-slate-200 dark:border-slate-700">
            <nav class="flex -mb-px">
                <button id="tab-cities" onclick="switchTab('cities')" 
                        class="tab-button active px-6 py-3 text-sm font-medium border-b-2 border-primary-600 text-primary-600">
                    <i class="fas fa-city mr-2"></i>
                    Villes ({{ $cities->total() }})
                </button>
                <button id="tab-regions" onclick="switchTab('regions')" 
                        class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300">
                    <i class="fas fa-map mr-2"></i>
                    Régions ({{ $regions->total() }})
                </button>
            </nav>
        </div>

        <!-- Tab Content: Villes -->
        <div id="content-cities" class="tab-content p-6">
            @if($cities->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ville</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Région</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pays</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">GPS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($cities as $city)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $city->name }}</div>
                                    @if($city->description)
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ Str::limit($city->description, 40) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                    {{ $city->region ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                    {{ $city->country }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($city->latitude && $city->longitude)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                            <i class="fas fa-check mr-1"></i> Oui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                            <i class="fas fa-times mr-1"></i> Non
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="toggleCityStatus({{ $city->id }})" 
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors {{ $city->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 hover:bg-green-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 hover:bg-slate-200' }}">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ $city->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="deleteCity({{ $city->id }}, '{{ $city->name }}')" 
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $cities->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-city text-slate-400 text-5xl mb-4"></i>
                    <p class="text-slate-500 dark:text-slate-400">Aucune ville trouvée</p>
                    <button onclick="openModal('addCityModal')" 
                            class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Ajouter votre première ville
                    </button>
                </div>
            @endif
        </div>

        <!-- Tab Content: Régions -->
        <div id="content-regions" class="tab-content hidden p-6">
            @if($regions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Région</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pays</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($regions as $region)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $region->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                    {{ $region->country }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="toggleRegionStatus({{ $region->id }})" 
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors {{ $region->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ $region->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="deleteRegion({{ $region->id }}, '{{ $region->name }}')" 
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $regions->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-map text-slate-400 text-5xl mb-4"></i>
                    <p class="text-slate-500 dark:text-slate-400">Aucune région trouvée</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Ajouter une ville -->
<div id="addCityModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-4" onclick="if (event.target === this) closeModal('addCityModal')">
    <div class="relative w-full max-w-2xl rounded-lg border border-slate-200 bg-white p-5 shadow-2xl sm:p-6 my-8 dark:border-slate-700 dark:bg-slate-800">
        <div class="mb-5 flex items-center justify-between">
            <h3 class="flex items-center text-base font-semibold text-slate-900 sm:text-lg dark:text-white">
                <span class="mr-3 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900">
                    <i class="fas fa-city text-sm text-primary-600 dark:text-primary-400"></i>
                </span>
                Ajouter une ville
            </h3>
            <button type="button" onclick="closeModal('addCityModal')" class="inline-flex h-8 w-8 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
            
            <form action="{{ route('admin.locations.cities.store') }}" method="POST" id="cityForm" onsubmit="return handleCityFormSubmit(event)">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Pays <span class="text-red-500">*</span>
                        </label>
                        <select name="country_code" id="worldCountrySelect" required onchange="onCountryChange()"
                                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">-- Sélectionnez d'abord un pays --</option>
                            <option value="CD">🇨🇩 Congo (RDC)</option>
                            <option value="US">🇺🇸 États-Unis</option>
                            <option value="FR">🇫🇷 France</option>
                            <option value="BE">🇧🇪 Belgique</option>
                            <option value="CA">🇨🇦 Canada</option>
                        </select>
                        <input type="hidden" name="country" id="countryName">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            🔍 Rechercher une ville dans ce pays
                        </label>
                        <input type="text" id="citySearchInput" placeholder="Sélectionnez d'abord un pays, puis tapez 3 lettres..."
                               oninput="searchCityNominatim(this.value)" disabled
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed">
                        <div id="searchResults" class="mt-2 max-h-48 overflow-y-auto hidden"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Nom de la ville <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="cityNameInput" required
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Région
                        </label>
                        <input type="text" name="region" id="cityRegionInput" list="regionsList"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <datalist id="regionsList"></datalist>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Latitude <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="latitude" id="cityLatitudeInput" step="0.000001" required
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Longitude <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="longitude" id="cityLongitudeInput" step="0.000001" required
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Code ville
                        </label>
                        <input type="text" name="city_code" id="cityCityCodeInput" list="cityCodesList"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <datalist id="cityCodesList">
                            <option value="KIN">Kinshasa</option>
                            <option value="FIH">Kinshasa (Aéroport)</option>
                            <option value="LBB">Lubumbashi</option>
                            <option value="KWZ">Kolwezi</option>
                            <option value="GOM">Goma</option>
                            <option value="NYC">New York City</option>
                            <option value="PAR">Paris</option>
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Population
                        </label>
                        <input type="number" name="population" id="cityPopulationInput" list="populationHints"
                               placeholder="Ex: 15000000"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <datalist id="populationHints">
                            <option value="50000">Petite ville (50k)</option>
                            <option value="100000">Ville moyenne (100k)</option>
                            <option value="500000">Grande ville (500k)</option>
                            <option value="1000000">Métropole (1M)</option>
                            <option value="5000000">Mégapole (5M)</option>
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Fuseau horaire
                        </label>
                        <input type="text" name="timezone" id="cityTimezoneInput" placeholder="Africa/Kinshasa"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="cityDescriptionInput" rows="3"
                                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked class="mr-2 w-4 h-4 text-primary-600 focus:ring-primary-500 rounded">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Ville active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeModal('addCityModal')" 
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Annuler
                    </button>
                    <button type="submit" id="submitCityBtn"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
</div>

<!-- Modal: Ajouter une région -->
<div id="addRegionModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-4" onclick="if (event.target === this) closeModal('addRegionModal')">
    <div class="relative w-full max-w-lg rounded-lg border border-slate-200 bg-white p-5 shadow-2xl sm:p-6 my-8 dark:border-slate-700 dark:bg-slate-800">
        <div class="mb-5 flex items-center justify-between">
            <h3 class="flex items-center text-base font-semibold text-slate-900 sm:text-lg dark:text-white">
                <span class="mr-3 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-accent-100 dark:bg-accent-900">
                    <i class="fas fa-map text-sm text-accent-600 dark:text-accent-400"></i>
                </span>
                Ajouter une région
            </h3>
            <button type="button" onclick="closeModal('addRegionModal')" class="inline-flex h-8 w-8 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
            
            <form action="{{ route('admin.locations.regions.store') }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Nom de la région <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Pays <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="country" required
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Description
                        </label>
                        <textarea name="description" rows="3"
                                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-accent-500 focus:border-transparent"></textarea>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked class="mr-2 w-4 h-4 text-accent-600 focus:ring-accent-500 rounded">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Région active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeModal('addRegionModal')" 
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Annuler
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-accent-700">
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
</div>

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

<style>
    #map {
        z-index: 1;
    }
    
    .leaflet-popup-content {
        margin: 0;
        min-width: 150px;
    }
    
    .leaflet-popup-content h4 {
        margin: 0;
    }
</style>
@endpush

@push('scripts')
<!-- Leaflet JavaScript - Charger dans le bon ordre -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
// Variables globales
let map, markers, allCitiesData = [], mapMarkerMode = false, tempMarker = null;

// Initialiser la carte Leaflet
function initMap() {
    map = L.map('map').setView([-4.0383, 21.7587], 5);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
    
    markers = L.markerClusterGroup({
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true,
        maxClusterRadius: 50
    });
    
    map.addLayer(markers);
    map.on('click', onMapClick);
    loadCitiesOnMap();
}

// Charger les villes
function loadCitiesOnMap() {
    showMapLoading(true);
    
    fetch('/admin/settings/locations/api/cities/map')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                allCitiesData = data.cities;
                displayCitiesOnMap(data.cities);
                document.getElementById('map-city-count').textContent = data.total;
            }
            showMapLoading(false);
        })
        .catch(error => {
            console.error('❌ Erreur:', error);
            showMapLoading(false);
        });
}

// Afficher les villes sur la carte
function displayCitiesOnMap(cities) {
    markers.clearLayers();
    
    cities.forEach(city => {
        if (city.latitude && city.longitude) addCityMarkerToMap(city);
    });
    
    if (cities.length > 0 && markers.getLayers().length > 0) {
        map.fitBounds(markers.getBounds(), { padding: [50, 50] });
    }
}

// Ajouter un marqueur
function addCityMarkerToMap(city) {
    const lat = parseFloat(city.latitude);
    const lng = parseFloat(city.longitude);
    
    if (isNaN(lat) || isNaN(lng)) return;
    
    const iconColor = city.is_active ? '#10b981' : '#94a3b8';
    
    const customIcon = L.icon({
        iconUrl: `data:image/svg+xml;base64,${btoa(`
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                <path fill="${iconColor}" stroke="white" stroke-width="2" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
        `)}`,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });
    
    const marker = L.marker([lat, lng], { icon: customIcon });
    
    marker.bindPopup(`
        <div class="p-2">
            <h4 class="font-bold text-lg mb-1">${city.name}</h4>
            <p class="text-sm text-slate-600">${city.country}</p>
            <p class="text-xs text-slate-500 mt-1">📍 ${lat.toFixed(4)}°, ${lng.toFixed(4)}°</p>
        </div>
    `);
    
    markers.addLayer(marker);
}

// Mode marquage
function enableMapMarkerMode() {
    mapMarkerMode = true;
    document.getElementById('mapModeIndicator').classList.remove('hidden');
    document.getElementById('map').style.cursor = 'crosshair';
}

function disableMapMarkerMode() {
    mapMarkerMode = false;
    document.getElementById('mapModeIndicator').classList.add('hidden');
    document.getElementById('map').style.cursor = '';
    if (tempMarker) {
        map.removeLayer(tempMarker);
        tempMarker = null;
    }
}

function onMapClick(e) {
    if (!mapMarkerMode) return;
    
    if (tempMarker) map.removeLayer(tempMarker);
    
    tempMarker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
    tempMarker.bindPopup(`
        <div class="p-2">
            <h4 class="font-bold mb-2">Nouvelle ville</h4>
            <p class="text-sm text-slate-600 mb-2">📍 ${e.latlng.lat.toFixed(6)}°, ${e.latlng.lng.toFixed(6)}°</p>
            <button onclick="openAddCityWithCoords(${e.latlng.lat}, ${e.latlng.lng})" 
                    class="px-3 py-1 bg-primary-600 text-white text-sm rounded hover:bg-primary-700">
                Ajouter ville
            </button>
        </div>
    `).openPopup();
}

function openAddCityWithCoords(lat, lng) {
    disableMapMarkerMode();
    document.getElementById('cityLatitudeInput').value = parseFloat(lat).toFixed(6);
    document.getElementById('cityLongitudeInput').value = parseFloat(lng).toFixed(6);
    
    // Géocodage inversé pour pré-remplir les données
    reverseGeocode(lat, lng);
    
    openModal('addCityModal');
}

// Utilitaires
function fitAllMarkers() {
    if (allCitiesData.length > 0) displayCitiesOnMap(allCitiesData);
}

function refreshMapData() {
    loadCitiesOnMap();
}

function showMapLoading(show) {
    const loadingEl = document.getElementById('map-loading');
    if (loadingEl) {
        show ? loadingEl.classList.remove('hidden') : loadingEl.classList.add('hidden');
    }
}

// Gestion des modals
function openModal(modalId) {
    const el = document.getElementById(modalId);
    el.classList.remove('hidden');
    el.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeModal(modalId) {
    const el = document.getElementById(modalId);
    el.classList.add('hidden');
    el.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

// Gestion des tabs
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(b => {
        b.classList.remove('border-primary-600', 'text-primary-600');
        b.classList.add('border-transparent', 'text-slate-500');
    });
    
    document.getElementById(`content-${tab}`).classList.remove('hidden');
    const activeBtn = document.getElementById(`tab-${tab}`);
    activeBtn.classList.add('border-primary-600', 'text-primary-600');
    activeBtn.classList.remove('border-transparent', 'text-slate-500');
}

// Actions CRUD
function toggleCityStatus(cityId) {
    if (!confirm('Changer le statut de cette ville ?')) return;
    
    fetch(`/admin/settings/locations/cities/${cityId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
    })
    .then(data => {
        if (data.success) location.reload();
    })
    .catch(error => {
        console.error('❌ Erreur toggle city:', error);
        alert('Erreur lors du changement de statut');
    });
}

function deleteCity(cityId, cityName) {
    if (!confirm(`Supprimer "${cityName}" ?`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/settings/locations/cities/${cityId}`;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    
    form.appendChild(csrf);
    form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
}

function toggleRegionStatus(regionId) {
    if (!confirm('Changer le statut de cette région ?')) return;
    
    fetch(`/admin/settings/locations/regions/${regionId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
    })
    .then(data => {
        if (data.success) location.reload();
    })
    .catch(error => {
        console.error('❌ Erreur toggle region:', error);
        alert('Erreur lors du changement de statut');
    });
}

function deleteRegion(regionId, regionName) {
    if (!confirm(`Supprimer "${regionName}" ?`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/settings/locations/regions/${regionId}`;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    
    form.appendChild(csrf);
    form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
}

// Soumission formulaire ville
function handleCityFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitCityBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Ajout...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeModal('addCityModal');
            location.reload();
        }
    })
    .catch(error => {
        console.error('❌ Erreur:', error);
        alert('Erreur lors de l\'ajout de la ville. Vérifiez la console pour plus de détails.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
    
    return false;
}

function onCountryChange() {
    const select = document.getElementById('worldCountrySelect');
    const option = select.options[select.selectedIndex];
    const searchInput = document.getElementById('citySearchInput');
    
    document.getElementById('countryName').value = option.text.replace(/🇨🇩|🇺🇸|🇫🇷|🇧🇪|🇨🇦/g, '').trim();
    
    // Activer le champ de recherche si un pays est sélectionné
    if (select.value) {
        searchInput.disabled = false;
        searchInput.placeholder = `Rechercher une ville en ${option.text}...`;
        
        // Charger les régions du pays sélectionné
        loadRegionsByCountry(select.value);
    } else {
        searchInput.disabled = true;
        searchInput.placeholder = "Sélectionnez d'abord un pays...";
        searchInput.value = '';
        document.getElementById('searchResults').classList.add('hidden');
    }
}

// Charger les régions pour autocomplétion
function loadRegionsByCountry(countryCode) {
    const regionsList = document.getElementById('regionsList');
    
    // Régions par pays (données statiques - peut être remplacé par API)
    const regions = {
        'CD': ['Kinshasa', 'Kongo Central', 'Kwango', 'Kwilu', 'Mai-Ndombe', 'Kasaï', 'Kasaï-Central', 'Kasaï-Oriental', 'Lomami', 'Sankuru', 'Maniema', 'Sud-Kivu', 'Nord-Kivu', 'Ituri', 'Haut-Uele', 'Tshopo', 'Bas-Uele', 'Nord-Ubangi', 'Mongala', 'Sud-Ubangi', 'Équateur', 'Tshuapa', 'Tanganyika', 'Haut-Lomami', 'Lualaba', 'Haut-Katanga'],
        'US': ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'],
        'FR': ['Île-de-France', 'Auvergne-Rhône-Alpes', 'Bourgogne-Franche-Comté', 'Bretagne', 'Centre-Val de Loire', 'Corse', 'Grand Est', 'Hauts-de-France', 'Normandie', 'Nouvelle-Aquitaine', 'Occitanie', 'Pays de la Loire', "Provence-Alpes-Côte d'Azur"],
        'BE': ['Bruxelles-Capitale', 'Flandre', 'Wallonie'],
        'CA': ['Alberta', 'Colombie-Britannique', 'Manitoba', 'Nouveau-Brunswick', 'Terre-Neuve-et-Labrador', 'Nouvelle-Écosse', 'Ontario', 'Île-du-Prince-Édouard', 'Québec', 'Saskatchewan']
    };
    
    regionsList.innerHTML = '';
    
    if (regions[countryCode]) {
        regions[countryCode].forEach(region => {
            const option = document.createElement('option');
            option.value = region;
            regionsList.appendChild(option);
        });
    }
}

// Recherche de ville via Nominatim
let searchTimeout;
function searchCityNominatim(query) {
    clearTimeout(searchTimeout);
    
    const resultsDiv = document.getElementById('searchResults');
    const countrySelect = document.getElementById('worldCountrySelect');
    const selectedCountry = countrySelect.value;
    
    if (!selectedCountry) {
        resultsDiv.innerHTML = '<div class="p-3 text-sm text-orange-600 bg-orange-50 dark:bg-orange-900/20 rounded-lg">⚠️ Veuillez d\'abord sélectionner un pays</div>';
        resultsDiv.classList.remove('hidden');
        return;
    }
    
    if (query.length < 3) {
        resultsDiv.classList.add('hidden');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        resultsDiv.innerHTML = '<div class="p-3 text-center"><i class="fas fa-spinner fa-spin text-primary-600"></i> Recherche...</div>';
        resultsDiv.classList.remove('hidden');
        
        // Recherche limitée au pays sélectionné
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=${selectedCountry.toLowerCase()}&limit=10&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="p-3 text-sm text-slate-500 dark:text-slate-400">Aucune ville trouvée dans ce pays</div>';
                    return;
                }
                
                let html = '<div class="bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg divide-y divide-slate-200 dark:divide-slate-600">';
                
                data.forEach(place => {
                    const displayName = place.display_name;
                    const city = place.address?.city || place.address?.town || place.address?.village || place.name;
                    const country = place.address?.country || '';
                    const countryCode = place.address?.country_code?.toUpperCase() || '';
                    const region = place.address?.state || place.address?.region || '';
                    const lat = parseFloat(place.lat);
                    const lon = parseFloat(place.lon);
                    
                    html += `
                        <div class="p-3 hover:bg-slate-50 dark:hover:bg-slate-600 cursor-pointer transition-colors" 
                             onclick='fillCityData(${JSON.stringify({
                                 name: city,
                                 country: country,
                                 countryCode: countryCode,
                                 region: region,
                                 latitude: lat,
                                 longitude: lon,
                                 displayName: displayName
                             })})'>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-map-marker-alt text-primary-600 mt-1"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">${city}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">${region ? region + ', ' : ''}${country}</p>
                                    <p class="text-xs text-slate-400 mt-1">📍 ${lat.toFixed(4)}°, ${lon.toFixed(4)}°</p>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                resultsDiv.innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur recherche:', error);
                resultsDiv.innerHTML = '<div class="p-3 text-sm text-red-500">Erreur de recherche</div>';
            });
    }, 500);
}

// Remplir le formulaire avec les données de la ville sélectionnée
function fillCityData(data) {
    document.getElementById('cityNameInput').value = data.name;
    document.getElementById('cityLatitudeInput').value = data.latitude;
    document.getElementById('cityLongitudeInput').value = data.longitude;
    
    // Remplir la région si disponible
    if (data.region) {
        document.getElementById('cityRegionInput').value = data.region;
    }
    
    // Cacher les résultats
    document.getElementById('searchResults').classList.add('hidden');
    document.getElementById('citySearchInput').value = '';
    
    // Feedback visuel
    const nameInput = document.getElementById('cityNameInput');
    nameInput.classList.add('ring-2', 'ring-green-500');
    setTimeout(() => {
        nameInput.classList.remove('ring-2', 'ring-green-500');
    }, 1500);
}

// Géocodage inversé au clic sur la carte
function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
        .then(response => response.json())
        .then(data => {
            if (data.address) {
                const city = data.address.city || data.address.town || data.address.village || '';
                const country = data.address.country || '';
                const countryCode = data.address.country_code?.toUpperCase() || '';
                const region = data.address.state || data.address.region || '';
                
                if (city) {
                    document.getElementById('cityNameInput').value = city;
                }
                if (region) {
                    document.getElementById('cityRegionInput').value = region;
                }
                if (countryCode) {
                    const countrySelect = document.getElementById('worldCountrySelect');
                    const option = Array.from(countrySelect.options).find(opt => opt.value === countryCode);
                    if (option) {
                        countrySelect.value = countryCode;
                        onCountryChange();
                    } else {
                        document.getElementById('countryName').value = country;
                    }
                }
            }
        })
        .catch(error => console.error('Erreur géocodage inversé:', error));
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    
    // Cacher les résultats de recherche si clic en dehors
    document.addEventListener('click', function(e) {
        const searchInput = document.getElementById('citySearchInput');
        const searchResults = document.getElementById('searchResults');
        if (e.target !== searchInput && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
});
</script>
@endpush

@endsection
