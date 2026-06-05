              @extends('layouts.admin')

@section('title', 'Gestion des marques')
@section('page-title', 'Gestion des marques')

@section('page-actions')
<div class="flex flex-wrap gap-3">
    <div class="relative">
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-200" 
                type="button" onclick="toggleDropdown('filter-dropdown')">
            <i class="fas fa-filter mr-2"></i>Filtrer
            <i class="fas fa-chevron-down ml-1"></i>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 hidden z-10" 
             id="filter-dropdown">
            <div class="py-1">
                <a href="{{ route('admin.brands.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">Toutes</a>
                <a href="{{ route('admin.brands.index', ['status' => 'active']) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">Actives</a>
                <a href="{{ route('admin.brands.index', ['status' => 'inactive']) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">Inactives</a>
                <a href="{{ route('admin.brands.index', ['featured' => '1']) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">En vedette</a>
            </div>
        </div>
    </div>
    <div class="relative">
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-200" 
                type="button" onclick="toggleDropdown('export-dropdown')">
            <i class="fas fa-download mr-2"></i>Export
            <i class="fas fa-chevron-down ml-1"></i>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 hidden z-10" 
             id="export-dropdown">
            <div class="py-1">
                <a href="#" onclick="exportBrands('csv')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">CSV</a>
                <a href="#" onclick="exportBrands('excel')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">Excel</a>
                <a href="#" onclick="exportBrands('pdf')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">PDF</a>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.brands.create') }}" 
       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
        <i class="fas fa-plus mr-2"></i>Ajouter une marque
    </a>
</div>
@endsection

@section('content')
<!-- Messages de succès/erreur -->
@if(session('success'))
    <div class="flex items-center rounded-xl bg-green-50 p-4 text-green-800 animate-fade-in mb-6" role="alert">
        <i class="fas fa-check-circle mr-3 text-green-500"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" class="ml-4 text-green-500 hover:text-green-700" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="flex items-center rounded-xl bg-red-50 p-4 text-red-800 animate-fade-in mb-6" role="alert">
        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" class="ml-4 text-red-500 hover:text-red-700" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<!-- Barre de recherche et filtres -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="p-6">
        <form method="GET" action="{{ route('admin.brands.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                           name="search" 
                           placeholder="Rechercher une marque..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <select name="country" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous les pays</option>
                    <option value="FR" {{ request('country') === 'FR' ? 'selected' : '' }}>France</option>
                    <option value="IT" {{ request('country') === 'IT' ? 'selected' : '' }}>Italie</option>
                    <option value="US" {{ request('country') === 'US' ? 'selected' : '' }}>États-Unis</option>
                    <option value="GB" {{ request('country') === 'GB' ? 'selected' : '' }}>Royaume-Uni</option>
                </select>
            </div>
            <div>
                <select name="sort" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom A-Z</option>
                    <option value="-name" {{ request('sort') === '-name' ? 'selected' : '' }}>Nom Z-A</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Plus ancien</option>
                    <option value="-created_at" {{ request('sort', '-created_at') === '-created_at' ? 'selected' : '' }}>Plus récent</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Total marques -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-primary-600 uppercase tracking-wider mb-2">Total marques</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $brands->total() ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tags text-2xl text-primary-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-primary-500 to-primary-600"></div>
    </div>

    <!-- Marques actives -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Marques actives</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $brands->where('is_active', 1)->count() ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
    </div>

    <!-- En vedette -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-yellow-600 uppercase tracking-wider mb-2">En vedette</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $brands->where('is_featured', 1)->count() ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-yellow-500 to-yellow-600"></div>
    </div>

    <!-- Articles total -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">Articles total</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $brands->sum('items_count') ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
    </div>
</div>

<!-- Liste des marques -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h5 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-1">Liste des marques</h5>
                @if(isset($brands))
                    <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">{{ $brands->total() }} marque(s) trouvée(s)</p>
                @endif
            </div>
            <div class="flex gap-2 items-center">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showInactive" onchange="toggleInactive()">
                    <label class="form-check-label text-xs md:text-sm" for="showInactive">
                        Voir inactives
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="p-0">
        @if(isset($brands) && $brands->count() > 0)
            <!-- Version Desktop - Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Marque</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pays</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Articles</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Créée le</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($brands as $brand)
                        <tr data-brand-id="{{ $brand->id }}" class="hover:bg-gray-50 dark:bg-gray-900 transition-colors {{ !$brand->is_active ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4">
                                <div class="form-check">
                                    <input class="form-check-input brand-checkbox" type="checkbox" value="{{ $brand->id }}">
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($brand->logo)
                                        <img src="{{ $brand->logo_url }}" 
                                             class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-700 shadow-sm"
                                             alt="Logo {{ $brand->name }}">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-lg flex items-center justify-center border border-primary-300 shadow-sm">
                                            <span class="font-bold text-sm">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $brand->name }}</div>
                                        @if($brand->slug)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $brand->slug }}</div>
                                        @endif
                                        @if($brand->description)
                                            <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($brand->description, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $brand->is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $brand->is_active ? 'bg-green-500' : 'bg-gray-500' }} mr-1.5"></span>
                                        {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($brand->is_featured)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <i class="fas fa-star text-yellow-500 mr-1"></i>Vedette
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($brand->country)
                                    <div class="flex items-center gap-2">
                                        <span class="fi fi-{{ strtolower($brand->country) }}"></span>
                                        <span class="text-sm text-gray-700 dark:text-gray-200">{{ $brand->country }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Non spécifié</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <strong class="text-primary-600 text-lg">{{ $brand->items_count ?? 0 }}</strong>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">articles</span>
                                </div>
                                @if($brand->items_count > 0)
                                    <div class="text-xs text-gray-400 mt-1">
                                        Dernier: {{ $brand->latest_item_date ? $brand->latest_item_date->format('d/m/Y') : 'N/A' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $brand->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $brand->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-1 justify-end items-center">
                                    <a href="{{ route('admin.brands.show', $brand) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors" 
                                       data-bs-toggle="tooltip" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.brands.edit', $brand) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary-600 hover:bg-primary-50 transition-colors"
                                       data-bs-toggle="tooltip" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="relative">
                                        <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 hover:bg-gray-50 dark:bg-gray-900 transition-colors" 
                                                type="button" 
                                                onclick="toggleActionDropdown({{ $brand->id }})">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="action-dropdown-{{ $brand->id }}" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20">
                                            <div class="py-1">
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="toggleBrandStatus({{ $brand->id }})">
                                                    <i class="fas fa-{{ $brand->is_active ? 'pause' : 'play' }} mr-2 text-{{ $brand->is_active ? 'yellow' : 'green' }}-500"></i>
                                                    {{ $brand->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="toggleBrandFeatured({{ $brand->id }})">
                                                    <i class="fas fa-star mr-2 text-yellow-500"></i>
                                                    {{ $brand->is_featured ? 'Retirer de la vedette' : 'Mettre en vedette' }}
                                                </button>
                                                <hr class="my-1 border-gray-200 dark:border-gray-600">
                                                <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" onclick="deleteBrand({{ $brand->id }}, '{{ $brand->name }}')">
                                                    <i class="fas fa-trash mr-2"></i>Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Version Mobile/Tablet - Cartes -->
            <div class="lg:hidden">
                @foreach($brands as $brand)
                <div data-brand-id="{{ $brand->id }}" class="border-b border-gray-200 p-4 hover:bg-gray-50 dark:bg-gray-900 transition-colors {{ !$brand->is_active ? 'opacity-60' : '' }}">
                    <div class="flex gap-3">
                        <!-- Checkbox et Logo -->
                        <div class="flex flex-col items-center gap-2">
                            <div class="form-check">
                                <input class="form-check-input brand-checkbox" type="checkbox" value="{{ $brand->id }}">
                            </div>
                            @if($brand->logo)
                                <img src="{{ $brand->logo_url }}" 
                                     class="w-16 h-16 rounded-lg object-cover border border-gray-200 dark:border-gray-700 shadow-sm"
                                     alt="Logo {{ $brand->name }}">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-lg flex items-center justify-center border border-primary-300 shadow-sm">
                                    <span class="font-bold">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Contenu principal -->
                        <div class="flex-1 min-w-0">
                            <!-- En-tête avec nom et actions -->
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1 min-w-0">
                                    <h6 class="font-semibold text-gray-900 dark:text-white truncate">{{ $brand->name }}</h6>
                                    @if($brand->slug)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $brand->slug }}</p>
                                    @endif
                                </div>
                                
                                <!-- Menu dropdown actions mobile -->
                                <div class="relative">
                                    <button class="text-gray-400 hover:text-gray-600 dark:text-gray-300 p-1" onclick="toggleMobileDropdown({{ $brand->id }})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 hidden z-10" 
                                         id="mobile-dropdown-{{ $brand->id }}">
                                        <div class="py-1">
                                            <a href="{{ route('admin.brands.show', $brand) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                                                <i class="fas fa-eye w-4"></i> Voir
                                            </a>
                                            <a href="{{ route('admin.brands.edit', $brand) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                                                <i class="fas fa-edit w-4"></i> Modifier
                                            </a>
                                            <button class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800" onclick="toggleBrandStatus({{ $brand->id }})">
                                                <i class="fas {{ $brand->is_active ? 'fa-pause' : 'fa-play' }} w-4"></i>
                                                {{ $brand->is_active ? 'Désactiver' : 'Activer' }}
                                            </button>
                                            <button class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800" onclick="toggleBrandFeatured({{ $brand->id }})">
                                                <i class="fas fa-star w-4"></i>
                                                {{ $brand->is_featured ? 'Retirer vedette' : 'Mettre en vedette' }}
                                            </button>
                                            <hr class="my-1">
                                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50" onclick="deleteBrand({{ $brand->id }}, '{{ $brand->name }}')">
                                                <i class="fas fa-trash w-4"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            @if($brand->description)
                                <p class="text-xs text-gray-400 mb-2 line-clamp-2">{{ $brand->description }}</p>
                            @endif
                            
                            <!-- Badges et infos -->
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $brand->is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $brand->is_active ? 'bg-green-500' : 'bg-gray-500' }} mr-1"></span>
                                    {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                
                                @if($brand->is_featured)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-star text-yellow-500 mr-1"></i>Vedette
                                    </span>
                                @endif
                                
                                @if($brand->country)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                        <span class="fi fi-{{ strtolower($brand->country) }} mr-1"></span>
                                        {{ $brand->country }}
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Statistiques -->
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span>
                                    <i class="fas fa-box text-gray-400"></i>
                                    {{ $brand->items_count ?? 0 }} articles
                                </span>
                                <span>
                                    <i class="fas fa-calendar text-gray-400"></i>
                                    {{ $brand->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Actions en lot -->
            <div class="p-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700" id="bulkActions" style="display: none;">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="text-xs sm:text-sm">
                        <span class="font-semibold text-gray-900 dark:text-white" id="selectedCount">0</span>
                        <span class="text-gray-600 dark:text-gray-300"> marque(s) sélectionnée(s)</span>
                    </div>
                    <div class="flex flex-wrap gap-2 justify-center sm:justify-end">
                        <button class="inline-flex items-center px-3 py-1.5 text-xs sm:text-sm font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200 transition-colors" onclick="bulkAction('activate')">
                            <i class="fas fa-check me-1"></i>
                            <span class="hidden sm:inline">Activer</span>
                            <span class="sm:hidden"><i class="fas fa-check"></i></span>
                        </button>
                        <button class="inline-flex items-center px-3 py-1.5 text-xs sm:text-sm font-medium text-yellow-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition-colors" onclick="bulkAction('deactivate')">
                            <i class="fas fa-pause me-1"></i>
                            <span class="hidden sm:inline">Désactiver</span>
                            <span class="sm:hidden"><i class="fas fa-pause"></i></span>
                        </button>
                        <button class="inline-flex items-center px-3 py-1.5 text-xs sm:text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors" onclick="bulkAction('delete')">
                            <i class="fas fa-trash me-1"></i>
                            <span class="hidden sm:inline">Supprimer</span>
                            <span class="sm:hidden"><i class="fas fa-trash"></i></span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            @if($brands->hasPages())
                <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 text-center sm:text-left">
                            Affichage de <span class="font-medium text-gray-900 dark:text-white">{{ $brands->firstItem() }}</span> à <span class="font-medium text-gray-900 dark:text-white">{{ $brands->lastItem() }}</span> 
                            sur <span class="font-medium text-gray-900 dark:text-white">{{ $brands->total() }}</span> résultats
                        </div>
                        <div class="w-full sm:w-auto overflow-x-auto">
                            {{ $brands->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                @if(request()->has('search') || request()->has('status') || request()->has('country'))
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        <i class="fas fa-search text-3xl text-gray-400"></i>
                    </div>
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucune marque trouvée</h5>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Aucune marque ne correspond à vos critères de recherche.</p>
                    <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:bg-gray-900 transition-colors">
                        <i class="fas fa-times me-2"></i>Effacer les filtres
                    </a>
                @else
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 mb-4">
                        <i class="fas fa-tags text-3xl text-primary-600"></i>
                    </div>
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucune marque enregistrée</h5>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Commencez par ajouter des marques pour organiser vos articles.</p>
                    <a href="{{ route('admin.brands.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-plus me-2"></i>Ajouter une marque
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>
    
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Center trick -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Header -->
            <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-red-800 flex items-center" id="modal-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Confirmer la suppression
                    </h3>
                    <button type="button" onclick="closeDeleteModal()" class="text-red-400 hover:text-red-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="bg-white dark:bg-gray-800 px-6 py-4">
                <p class="text-gray-700 dark:text-gray-200 mb-4">
                    Êtes-vous sûr de vouloir supprimer la marque 
                    <strong class="text-gray-900 dark:text-white" id="brandNameToDelete"></strong> ?
                </p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 font-medium">
                                Cette action est <strong>irréversible</strong> et supprimera :
                            </p>
                            <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1">
                                <li>Toutes les informations de la marque</li>
                                <li>Les associations avec les articles</li>
                                <li>L'historique et les statistiques</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors w-full sm:w-auto">
                    <i class="fas fa-times mr-2"></i>Annuler
                </button>
                <form id="deleteForm" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex justify-center items-center w-full px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Gestion des dropdowns
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id$="-dropdown"]');
    
    // Fermer tous les autres dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle le dropdown actuel
    dropdown.classList.toggle('hidden');
}

// Gestion des dropdowns d'action dans le tableau
function toggleActionDropdown(brandId) {
    const dropdown = document.getElementById(`action-dropdown-${brandId}`);
    const allDropdowns = document.querySelectorAll('[id^="action-dropdown-"]');
    
    // Fermer tous les autres dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== `action-dropdown-${brandId}`) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle le dropdown actuel
    dropdown.classList.toggle('hidden');
}

// Fermer les dropdowns en cliquant en dehors
document.addEventListener('click', function(event) {
    if (!event.target.closest('button[onclick*="toggleDropdown"]') && !event.target.closest('button[onclick*="toggleActionDropdown"]')) {
        const allDropdowns = document.querySelectorAll('[id$="-dropdown"], [id^="action-dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
    }
});

// Initialiser les tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Gestion de la sélection multiple
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.brand-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.brand-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkboxes.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Écouteurs pour les checkboxes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('brand-checkbox')) {
        updateBulkActions();
    }
});

// Changer le statut d'une marque
function toggleBrandStatus(brandId) {
    const row = document.querySelector(`tr[data-brand-id="${brandId}"]`) || document.querySelector(`div[data-brand-id="${brandId}"]`);
    const isCurrentlyInactive = row ? row.classList.contains('opacity-60') : false;
    
    fetch(`/admin/brands/${brandId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ is_active: isCurrentlyInactive })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification du statut');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification du statut');
    });
}

// Mettre en vedette / retirer de la vedette
function toggleBrandFeatured(brandId) {
    // Logique similaire à toggleBrandStatus mais pour is_featured
    alert('Fonctionnalité à implémenter : toggle featured');
}

// Supprimer une marque
function deleteBrand(brandId, brandName) {
    document.getElementById('brandNameToDelete').textContent = brandName;
    document.getElementById('deleteForm').action = `/admin/brands/${brandId}`;
    
    // Ouvrir le modal
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Empêcher le scroll
}

// Fermer le modal de suppression
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    document.body.style.overflow = ''; // Réactiver le scroll
}

// Fermer le modal avec la touche Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});

// Actions en lot
function bulkAction(action) {
    const selectedBrands = Array.from(document.querySelectorAll('.brand-checkbox:checked')).map(cb => cb.value);
    
    if (selectedBrands.length === 0) {
        alert('Veuillez sélectionner au moins une marque.');
        return;
    }
    
    let confirmMessage = '';
    switch(action) {
        case 'activate':
            confirmMessage = `Activer ${selectedBrands.length} marque(s) ?`;
            break;
        case 'deactivate':
            confirmMessage = `Désactiver ${selectedBrands.length} marque(s) ?`;
            break;
        case 'delete':
            confirmMessage = `Supprimer définitivement ${selectedBrands.length} marque(s) ?`;
            break;
    }
    
    if (confirm(confirmMessage)) {
        // Implémentation des actions en lot
        console.log(`${action} brands:`, selectedBrands);
        alert(`Fonctionnalité à implémenter : ${action}`);
    }
}

// Afficher/masquer les marques inactives
function toggleInactive() {
    const showInactive = document.getElementById('showInactive').checked;
    const inactiveRows = document.querySelectorAll('[data-brand-id].opacity-60');
    
    inactiveRows.forEach(row => {
        row.style.display = showInactive ? '' : 'none';
    });
}

// Export des données
function exportBrands(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    
    window.location.href = `{{ route('admin.brands.index') }}?${params.toString()}`;
}

// Gestion des dropdowns mobile
function toggleMobileDropdown(brandId) {
    const dropdown = document.getElementById(`mobile-dropdown-${brandId}`);
    const allDropdowns = document.querySelectorAll('[id^="mobile-dropdown-"]');
    
    // Fermer tous les autres dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== `mobile-dropdown-${brandId}`) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle le dropdown actuel
    dropdown.classList.toggle('hidden');
}

// Fermer les dropdowns en cliquant en dehors
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleMobileDropdown"]') && !event.target.closest('[id^="mobile-dropdown-"]')) {
        const allDropdowns = document.querySelectorAll('[id^="mobile-dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
    }
});

// Améliorer la gestion des checkboxes sur mobile
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.brand-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});
</script>
@endpush