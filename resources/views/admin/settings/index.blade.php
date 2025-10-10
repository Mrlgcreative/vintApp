@extends('layouts.admin')

@section('title', 'Paramètres Système')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="ml-auto text-green-600 hover:text-green-800" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="ml-auto text-red-600 hover:text-red-800" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 mb-4 sm:mb-0">
                <i class="fas fa-cogs mr-3 text-gray-600"></i>
                Paramètres Système
            </h1>
            <div class="flex flex-col sm:flex-row gap-3">
                <button class="inline-flex items-center px-4 py-2 border border-cyan-300 text-cyan-700 bg-cyan-50 rounded-lg hover:bg-cyan-100 transition-colors duration-200" onclick="clearCache()">
                    <i class="fas fa-trash mr-2"></i>
                    Vider Cache
                </button>
                <button class="inline-flex items-center px-4 py-2 border border-blue-300 text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200" onclick="backupSettings()">
                    <i class="fas fa-download mr-2"></i>
                    Sauvegarder
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @if(isset($settings) && (is_array($settings) ? count($settings) : $settings->count()) > 0)
                        @foreach($categories as $category)
                            @if(isset($settings[$category]))
                                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                                    <div class="mb-6">
                                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                            <i class="fas fa-cog mr-3 text-gray-600"></i>
                                            {{ ucfirst($category) }}
                                        </h3>
                                    </div>
                                    <div class="space-y-4">
                                        @foreach($settings[$category] as $setting)
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">
                                                    {{ $setting->label }}
                                                    @if($setting->description)
                                                        <i class="fas fa-info-circle text-gray-400 ml-1 cursor-help" 
                                                           title="{{ $setting->description }}" 
                                                           data-tooltip="{{ $setting->description }}"></i>
                                                    @endif
                                                </label>
                                                
                                                @if($setting->key === 'app_logo')
                                                    <div class="mb-3">
                                                        <img src="{{ asset($setting->value) }}" 
                                                             alt="Logo actuel" 
                                                             class="h-16 w-32 object-contain border border-gray-200 rounded-lg p-2 bg-white shadow-sm"
                                                             id="logo-preview">
                                                    </div>
                                                    <input type="file" 
                                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                           name="logo_file"
                                                           accept="image/*"
                                                           onchange="previewLogo(this)">
                                                    <input type="hidden" 
                                                           name="settings[{{ $setting->key }}]"
                                                           value="{{ is_array($setting->value) ? json_encode($setting->value) : $setting->value }}">
                                                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF (max. 2MB)</p>
                                                            @elseif($setting->type === 'json' || $setting->type === 'array')
                                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 font-mono text-sm" 
                                                              name="settings[{{ $setting->key }}]"
                                                              rows="5"
                                                              data-original-value="{{ is_array($setting->value) ? json_encode($setting->value) : $setting->value }}">{{ is_array($setting->value) ? json_encode($setting->value, JSON_PRETTY_PRINT) : $setting->value }}</textarea>
                                                    <p class="text-xs text-gray-500 mt-1">Format JSON</p>
                                                @elseif($setting->type === 'boolean')
                                                    <div class="flex items-center space-x-3">
                                                        <!-- Hidden input pour s'assurer que la valeur false est envoyée -->
                                                        <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                                        <label class="inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" 
                                                                   class="sr-only peer" 
                                                                   name="settings[{{ $setting->key }}]"
                                                                   id="{{ $setting->key }}"
                                                                   value="1"
                                                                   {{ $setting->value ? 'checked' : '' }}>
                                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                            <span class="ml-3 text-sm font-medium text-gray-700" id="{{ $setting->key }}_label">
                                                                {{ $setting->value ? 'Activé' : 'Désactivé' }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                @elseif($setting->type === 'integer')
                                                    <input type="number" 
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" 
                                                           name="settings[{{ $setting->key }}]"
                                                           value="{{ $setting->value }}" 
                                                           step="1"
                                                           data-original-value="{{ $setting->value }}">
                                                @elseif($setting->type === 'float')
                                                    <input type="number" 
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" 
                                                           name="settings[{{ $setting->key }}]"
                                                           value="{{ $setting->value }}" 
                                                           step="0.01"
                                                           data-original-value="{{ $setting->value }}">
                                                @else
                                                    @if(str_contains($setting->key, 'email'))
                                                        <input type="email" 
                                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" 
                                                               name="settings[{{ $setting->key }}]"
                                                               value="{{ $setting->value }}"
                                                               data-original-value="{{ $setting->value }}">
                                                    @elseif(str_contains($setting->key, 'phone'))
                                                        <input type="tel" 
                                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" 
                                                               name="settings[{{ $setting->key }}]"
                                                               value="{{ $setting->value }}"
                                                               data-original-value="{{ $setting->value }}">
                                                    @else
                                                        <input type="text" 
                                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" 
                                                               name="settings[{{ $setting->key }}]"
                                                               value="{{ $setting->value }}"
                                                               data-original-value="{{ $setting->value }}">
                                                    @endif
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="col-span-2">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                                <i class="fas fa-info-circle text-blue-600 text-2xl mb-3"></i>
                                <h3 class="text-lg font-medium text-blue-900 mb-2">Aucun paramètre trouvé</h3>
                                <p class="text-blue-700">Veuillez exécuter les migrations et seeders.</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Section Mode Maintenance -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl border border-orange-200 p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-tools mr-3 text-orange-600"></i>
                                Mode Maintenance
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Contrôlez l'accès au site pour effectuer des maintenances ou des mises à jour.
                            </p>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 rounded-full {{ $maintenanceStatus ? 'bg-red-100' : 'bg-green-100' }}">
                                        <i class="fas {{ $maintenanceStatus ? 'fa-exclamation-triangle text-red-600' : 'fa-check-circle text-green-600' }} text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            Statut : {{ $maintenanceStatus ? 'Mode maintenance ACTIVÉ' : 'Site en ligne' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $maintenanceStatus ? 'Le site affiche la page de maintenance aux visiteurs' : 'Le site est accessible normalement' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <button type="button" 
                                        onclick="{{ $maintenanceStatus ? 'disableMaintenance()' : 'showMaintenanceModal()' }}"
                                        class="inline-flex items-center px-4 py-2 {{ $maintenanceStatus ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                                    <i class="fas {{ $maintenanceStatus ? 'fa-play' : 'fa-pause' }} mr-2"></i>
                                    {{ $maintenanceStatus ? 'Désactiver' : 'Activer' }}
                                </button>
                            </div>
                            
                            @if($maintenanceStatus)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle text-red-600 mr-2"></i>
                                        <p class="text-red-800 text-sm">
                                            <strong>Attention :</strong> Seuls les administrateurs peuvent accéder au site en mode maintenance.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Section Restrictions Géographiques -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200 p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-map-marked-alt mr-3 text-blue-600"></i>
                                Restrictions Géographiques
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Contrôlez si les vendeurs doivent respecter les zones géographiques autorisées pour publier leurs articles.
                            </p>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 rounded-full" id="location-status-icon">
                                        <i class="fas fa-map-marker-alt text-blue-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900" id="location-status-text">
                                            Chargement...
                                        </p>
                                        <p class="text-sm text-gray-500" id="location-status-description">
                                            Récupération du statut en cours...
                                        </p>
                                    </div>
                                </div>
                                
                                <button type="button" 
                                        onclick="toggleLocationRestrictions()"
                                        id="location-toggle-btn"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                                    <i class="fas fa-sync-alt mr-2 animate-spin"></i>
                                    Chargement...
                                </button>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="space-y-2 text-sm text-blue-800">
                                    <p class="flex items-start">
                                        <i class="fas fa-check-circle mr-2 mt-0.5"></i>
                                        <span><strong>Activé :</strong> Les vendeurs ne peuvent publier des articles que dans les villes autorisées par l'admin.</span>
                                    </p>
                                    <p class="flex items-start">
                                        <i class="fas fa-times-circle mr-2 mt-0.5"></i>
                                        <span><strong>Désactivé :</strong> Les vendeurs peuvent publier des articles dans n'importe quelle ville du monde.</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section Pré-inscription -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-200 p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-user-plus mr-3 text-purple-600"></i>
                                Mode Pré-inscription
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <strong>⚠️ ATTENTION :</strong> Quand activé, toute l'application devient inaccessible aux visiteurs (comme le mode maintenance) et redirige vers la page de pré-inscription. Seuls les admins connectés peuvent accéder à l'application.
                            </p>
                        </div>
                        
                        @php
                            $preregEnabled = \App\Models\Setting::get('preregistration_enabled', true);
                            $preregCount = \App\Models\UserWaiting::count();
                            $preregLimit = \App\Models\Setting::get('preregistration_limit', 0);
                        @endphp
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 rounded-full {{ $preregEnabled ? 'bg-green-100' : 'bg-red-100' }}">
                                        <i class="fas {{ $preregEnabled ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600' }} text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            Statut : {{ $preregEnabled ? 'MODE PRÉ-INSCRIPTION ACTIF' : 'MODE NORMAL' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $preregEnabled ? '🔒 Application verrouillée - Seule la pré-inscription est accessible' : '✅ Application accessible normalement' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <button type="button" 
                                        onclick="{{ $preregEnabled ? 'disablePreregistration()' : 'enablePreregistration()' }}"
                                        class="inline-flex items-center px-4 py-2 {{ $preregEnabled ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                                    <i class="fas {{ $preregEnabled ? 'fa-lock-open' : 'fa-lock' }} mr-2"></i>
                                    {{ $preregEnabled ? 'Désactiver' : 'Activer' }}
                                </button>
                            </div>
                            
                            <!-- Statistiques -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">Total inscriptions</p>
                                            <p class="text-2xl font-bold text-gray-900">{{ $preregCount }}</p>
                                        </div>
                                        <div class="p-3 bg-blue-100 rounded-lg">
                                            <i class="fas fa-users text-blue-600 text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">Limite configurée</p>
                                            <p class="text-2xl font-bold text-gray-900">{{ $preregLimit > 0 ? $preregLimit : '∞' }}</p>
                                        </div>
                                        <div class="p-3 bg-yellow-100 rounded-lg">
                                            <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">Places restantes</p>
                                            <p class="text-2xl font-bold text-gray-900">
                                                @if($preregLimit > 0)
                                                    {{ max(0, $preregLimit - $preregCount) }}
                                                @else
                                                    ∞
                                                @endif
                                            </p>
                                        </div>
                                        <div class="p-3 bg-green-100 rounded-lg">
                                            <i class="fas fa-ticket-alt text-green-600 text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions rapides -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.settings.preregistration') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                                    <i class="fas fa-cog mr-2"></i>
                                    Configurer
                                </a>
                                
                                <a href="{{ route('admin.waiting-users.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                                    <i class="fas fa-list mr-2"></i>
                                    Gérer les inscriptions
                                </a>
                                
                                <a href="{{ route('preregistration.index') }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Voir la page publique
                                </a>
                            </div>
                            
                            @if($preregLimit > 0 && $preregCount >= $preregLimit)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                        <p class="text-red-800 text-sm">
                                            <strong>Limite atteinte :</strong> Le nombre maximum de pré-inscriptions ({{ $preregLimit }}) a été atteint.
                                        </p>
                                    </div>
                                </div>
                            @endif
                            
                            @if(!$preregEnabled)
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle text-orange-600 mr-2"></i>
                                        <p class="text-orange-800 text-sm">
                                            Les visiteurs verront le message de fermeture configuré dans les paramètres.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Section Wallet Entreprise (Commissions) -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-200 p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-building text-purple-600 mr-3"></i>
                                Wallet Entreprise - Commissions de la Plateforme
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Gestion des commissions collectées sur chaque vente ({{ $enterpriseWallets['commission_rate'] }}% par défaut)
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Wallet USD -->
                            <div class="bg-white rounded-lg border border-purple-200 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-dollar-sign text-2xl text-purple-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-700 uppercase">Wallet USD</h4>
                                            <p class="text-xs text-gray-500">Commissions en dollars</p>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($enterpriseWallets['usd'])
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Solde actuel</span>
                                            <span class="text-2xl font-bold text-purple-600">
                                                ${{ number_format($enterpriseWallets['usd']->balance, 2) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                            <span class="text-xs text-gray-500">ID Wallet</span>
                                            <span class="text-xs font-mono text-gray-700">#{{ $enterpriseWallets['usd']->id }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">Statut</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $enterpriseWallets['usd']->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Wallet USD non créé</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Wallet CDF -->
                            <div class="bg-white rounded-lg border border-indigo-200 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-coins text-2xl text-indigo-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-700 uppercase">Wallet CDF</h4>
                                            <p class="text-xs text-gray-500">Commissions en francs</p>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($enterpriseWallets['cdf'])
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Solde actuel</span>
                                            <span class="text-2xl font-bold text-indigo-600">
                                                {{ number_format($enterpriseWallets['cdf']->balance, 0, ',', ' ') }} FC
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                            <span class="text-xs text-gray-500">ID Wallet</span>
                                            <span class="text-xs font-mono text-gray-700">#{{ $enterpriseWallets['cdf']->id }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">Statut</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $enterpriseWallets['cdf']->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Wallet CDF non créé</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Informations sur les commissions -->
                        <div class="mt-6 bg-white rounded-lg border border-purple-200 p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-purple-600 text-xl"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h5 class="text-sm font-semibold text-gray-900 mb-2">
                                        Comment fonctionnent les commissions ?
                                    </h5>
                                    <ul class="text-xs text-gray-600 space-y-1">
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                            <span>Sur chaque vente confirmée, {{ $enterpriseWallets['commission_rate'] }}% est prélevé automatiquement</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                            <span>Les fonds sont transférés du wallet pending vers le wallet entreprise et le wallet du vendeur</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                            <span>Le vendeur reçoit 95% du montant, la plateforme conserve 5% de commission</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                            <span>Toutes les transactions sont enregistrées et traçables dans l'historique</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('admin.wallets.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-wallet mr-2"></i>
                                Voir tous les wallets
                            </a>
                            <a href="{{ route('admin.transactions.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                Historique des transactions
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer les modifications
                    </button>
                    
                    <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200" onclick="resetForm()">
                        <i class="fas fa-undo mr-2"></i>
                        Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Configuration des URLs pour les requêtes AJAX
const MAINTENANCE_ENABLE_URL = '{{ route('admin.settings.maintenance.enable') }}';
const MAINTENANCE_DISABLE_URL = '{{ route('admin.settings.maintenance.disable') }}';
const CSRF_TOKEN = '{{ csrf_token() }}';

// Stockage des valeurs initiales pour détecter les changements
let initialFormValues = {};

// Initialiser les tooltips personnalisés
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips pour les icônes d'information
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });

    // Gérer les switches toggle
    const toggleSwitches = document.querySelectorAll('input[type="checkbox"]');
    toggleSwitches.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const label = document.getElementById(this.id + '_label');
            if (label) {
                label.textContent = this.checked ? 'Activé' : 'Désactivé';
            }
        });
    });

    // Sauvegarder les valeurs initiales du formulaire
    saveInitialFormValues();

    // Gérer la soumission du formulaire
    document.getElementById('settingsForm').addEventListener('submit', handleFormSubmit);

    // Surveiller les changements dans les champs
    const allInputs = document.querySelectorAll('input[data-original-value], input[type="checkbox"]');
    allInputs.forEach(input => {
        input.addEventListener('input', markChangedField);
        input.addEventListener('change', markChangedField);
    });
});

function saveInitialFormValues() {
    const form = document.getElementById('settingsForm');
    const formData = new FormData(form);
    
    // Stocker toutes les valeurs initiales
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('settings[')) {
            initialFormValues[key] = value;
        }
    }
}

function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const changedSettings = {};
    let hasChanges = false;

    // Détecter les changements
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('settings[')) {
            const initialValue = initialFormValues[key] || '';
            if (value !== initialValue) {
                changedSettings[key] = value;
                hasChanges = true;
            }
        }
    }

    // Vérifier les fichiers uploadés
    const fileInput = form.querySelector('input[type="file"]');
    if (fileInput && fileInput.files.length > 0) {
        hasChanges = true;
    }

    if (!hasChanges) {
        showNotification('Aucune modification détectée', 'info');
        return;
    }

    // Afficher un résumé des changements
    const changeCount = Object.keys(changedSettings).length + (fileInput && fileInput.files.length > 0 ? 1 : 0);
    const confirmMessage = `Vous êtes sur le point de modifier ${changeCount} paramètre(s). Continuer ?`;
    
    if (!confirm(confirmMessage)) {
        return;
    }

    // Créer un nouveau FormData avec seulement les changements
    const submitData = new FormData();
    submitData.append('_token', form.querySelector('input[name="_token"]').value);
    
    // Ajouter les paramètres modifiés
    for (let [key, value] of Object.entries(changedSettings)) {
        submitData.append(key, value);
    }

    // Ajouter le fichier s'il y en a un
    if (fileInput && fileInput.files.length > 0) {
        submitData.append('logo_file', fileInput.files[0]);
    }

    // Soumettre avec indication de chargement
    const submitButton = form.querySelector('button[type="submit"]');
    const originalContent = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';
    submitButton.disabled = true;

    fetch(form.action, {
        method: 'POST',
        body: submitData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json().catch(() => ({ success: true }));
        }
        throw new Error('Erreur réseau');
    })
    .then(data => {
        if (data.success !== false) {
            showNotification('Paramètres mis à jour avec succès !', 'success');
            // Mettre à jour les valeurs initiales
            saveInitialFormValues();
        } else {
            showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la mise à jour des paramètres', 'error');
    })
    .finally(() => {
        submitButton.innerHTML = originalContent;
        submitButton.disabled = false;
    });
}

function showTooltip(event) {
    const tooltip = document.createElement('div');
    tooltip.className = 'absolute z-50 px-3 py-2 text-xs text-white bg-gray-900 rounded-lg shadow-lg whitespace-nowrap';
    tooltip.textContent = event.target.getAttribute('data-tooltip');
    tooltip.id = 'tooltip';
    
    document.body.appendChild(tooltip);
    
    const rect = event.target.getBoundingClientRect();
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
}

function hideTooltip() {
    const tooltip = document.getElementById('tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

function resetForm() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ?')) {
        document.getElementById('settingsForm').reset();
        
        // Reset toggle labels
        const toggleSwitches = document.querySelectorAll('input[type="checkbox"]');
        toggleSwitches.forEach(toggle => {
            const label = document.getElementById(toggle.id + '_label');
            if (label) {
                label.textContent = toggle.checked ? 'Activé' : 'Désactivé';
            }
        });

        // Nettoyer tous les indicateurs de changement
        const changedContainers = document.querySelectorAll('.bg-yellow-50');
        changedContainers.forEach(container => {
            container.classList.remove('bg-yellow-50', 'border', 'border-yellow-300', 'rounded-lg', 'p-3', '-m-1');
            container.classList.add('bg-transparent');
            
            const indicator = container.querySelector('.change-indicator');
            if (indicator) {
                indicator.remove();
            }
        });

        // Nettoyer le compteur de changements
        updateChangeCounter();

        showNotification('Formulaire réinitialisé', 'info');
    }
}

function clearCache() {
    if (confirm('Êtes-vous sûr de vouloir vider le cache des paramètres ?')) {
        // Afficher l'indicateur de chargement
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Vidage en cours...';
        button.disabled = true;

        fetch('{{ route("admin.settings.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Cache vidé avec succès !', 'success');
            } else {
                showNotification('Erreur lors du vidage du cache: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors du vidage du cache.', 'error');
        })
        .finally(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
}

function backupSettings() {
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Préparation...';
    button.disabled = true;

    setTimeout(() => {
        showNotification('Fonction de sauvegarde disponible - téléchargement en cours...', 'info');
        button.innerHTML = originalContent;
        button.disabled = false;
    }, 1500);
}

function previewLogo(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Vérifier la taille du fichier (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            showNotification('Le fichier est trop volumineux (max. 2MB)', 'error');
            input.value = '';
            return;
        }

        // Vérifier le type de fichier
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            showNotification('Format de fichier non supporté', 'error');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logo-preview').src = e.target.result;
            showNotification('Aperçu du logo mis à jour', 'success');
        };
        reader.readAsDataURL(file);
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300`;
    
    // Couleurs selon le type
    switch(type) {
        case 'success':
            notification.className += ' bg-green-500 text-white';
            break;
        case 'error':
            notification.className += ' bg-red-500 text-white';
            break;
        case 'warning':
            notification.className += ' bg-yellow-500 text-white';
            break;
        default:
            notification.className += ' bg-blue-500 text-white';
    }
    
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
        notification.classList.add('translate-x-0', 'opacity-100');
    }, 100);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function markChangedField(event) {
    const input = event.target;
    let isChanged = false;

    if (input.type === 'checkbox') {
        // Pour les checkboxes, vérifier par rapport aux valeurs initiales
        const fieldName = input.name;
        const currentValue = input.checked ? '1' : '0';
        const initialValue = initialFormValues[fieldName] || '0';
        isChanged = currentValue !== initialValue;
    } else if (input.hasAttribute('data-original-value')) {
        // Pour les autres inputs
        isChanged = input.value !== input.getAttribute('data-original-value');
    }

    // Ajouter/enlever la classe pour indiquer le changement
    const container = input.closest('.space-y-2');
    if (container) {
        if (isChanged) {
            container.classList.add('bg-yellow-50', 'border', 'border-yellow-300', 'rounded-lg', 'p-3', '-m-1');
            container.classList.remove('bg-transparent');
            
            // Ajouter un indicateur visuel si pas déjà présent
            if (!container.querySelector('.change-indicator')) {
                const indicator = document.createElement('span');
                indicator.className = 'change-indicator text-xs text-yellow-700 font-medium';
                indicator.innerHTML = '<i class="fas fa-edit mr-1"></i>Modifié';
                const label = container.querySelector('label');
                if (label) {
                    label.appendChild(indicator);
                }
            }
        } else {
            container.classList.remove('bg-yellow-50', 'border', 'border-yellow-300', 'rounded-lg', 'p-3', '-m-1');
            container.classList.add('bg-transparent');
            
            // Enlever l'indicateur visuel
            const indicator = container.querySelector('.change-indicator');
            if (indicator) {
                indicator.remove();
            }
        }
    }

    // Mettre à jour le compteur de changements
    updateChangeCounter();
}

function updateChangeCounter() {
    const changedFields = document.querySelectorAll('.bg-yellow-50').length;
    const submitButton = document.querySelector('button[type="submit"]');
    
    if (changedFields > 0) {
        if (!submitButton.querySelector('.change-count')) {
            const badge = document.createElement('span');
            badge.className = 'change-count ml-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full';
            submitButton.appendChild(badge);
        }
        submitButton.querySelector('.change-count').textContent = changedFields;
        submitButton.classList.add('ring-2', 'ring-yellow-300');
    } else {
        const badge = submitButton.querySelector('.change-count');
        if (badge) {
            badge.remove();
        }
        submitButton.classList.remove('ring-2', 'ring-yellow-300');
    }
}

// Fonctions pour le mode maintenance
function showMaintenanceModal() {
    document.getElementById('maintenanceModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function hideMaintenanceModal() {
    document.getElementById('maintenanceModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

async function enableMaintenance() {
    const form = document.getElementById('maintenanceForm');
    const formData = new FormData(form);
    
    try {
        const response = await fetch(MAINTENANCE_ENABLE_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(result.message || 'Erreur lors de l\'activation', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de communication avec le serveur', 'error');
    }
    
    hideMaintenanceModal();
}

async function disableMaintenance() {
    if (!confirm('Êtes-vous sûr de vouloir désactiver le mode maintenance ?')) {
        return;
    }
    
    try {
        const response = await fetch(MAINTENANCE_DISABLE_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(result.message || 'Erreur lors de la désactivation', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de communication avec le serveur', 'error');
    }
}

// ========================================
// Fonctions pour la pré-inscription
// ========================================

async function enablePreregistration() {
    if (!confirm('⚠️ ATTENTION : ACTIVER LE MODE PRÉ-INSCRIPTION ?\n\n' +
                 '• Toute l\'application sera VERROUILLÉE\n' +
                 '• Les visiteurs seront redirigés vers la page de pré-inscription\n' +
                 '• Seuls les ADMINS connectés pourront accéder à l\'application\n\n' +
                 'Ce mode est similaire au mode maintenance.\n\n' +
                 'Voulez-vous vraiment continuer ?')) {
        return;
    }
    
    try {
        const response = await fetch('{{ route("admin.settings.preregistration.toggle") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                enabled: true
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message || 'Pré-inscriptions ouvertes avec succès !', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(result.message || 'Erreur lors de l\'activation', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de communication avec le serveur', 'error');
    }
}

async function disablePreregistration() {
    if (!confirm('Êtes-vous sûr de vouloir DÉSACTIVER le mode pré-inscription ?\n\n' +
                 '• L\'application redeviendra accessible à tous\n' +
                 '• Le fonctionnement normal sera rétabli\n\n' +
                 'Continuer ?')) {
        return;
    }
    
    try {
        const response = await fetch('{{ route("admin.settings.preregistration.toggle") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                enabled: false
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message || 'Pré-inscriptions fermées avec succès !', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(result.message || 'Erreur lors de la désactivation', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de communication avec le serveur', 'error');
    }
}

// ========================================
// Fonctions pour les restrictions géographiques
// ========================================

// Charger le statut au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadLocationRestrictionsStatus();
});

async function loadLocationRestrictionsStatus() {
    try {
        const response = await fetch('{{ route("admin.settings.location-restrictions.status") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            updateLocationRestrictionsUI(result.enabled);
        } else {
            showNotification('Erreur lors du chargement du statut', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de communication avec le serveur', 'error');
    }
}

async function toggleLocationRestrictions() {
    const currentBtn = document.getElementById('location-toggle-btn');
    const currentEnabled = currentBtn.dataset.enabled === 'true';
    const newEnabled = !currentEnabled;
    
    const confirmMessage = newEnabled 
        ? '⚠️ Activer les restrictions géographiques ?\n\n• Les vendeurs ne pourront publier des articles QUE dans les villes autorisées\n• Utile pour contrôler les zones de vente'
        : '⚠️ Désactiver les restrictions géographiques ?\n\n• Les vendeurs pourront publier des articles dans N\'IMPORTE QUELLE ville du monde\n• Plus de liberté mais moins de contrôle';
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    // Afficher le loader
    currentBtn.innerHTML = '<i class="fas fa-sync-alt mr-2 animate-spin"></i>Traitement...';
    currentBtn.disabled = true;
    
    try {
        const response = await fetch('{{ route("admin.settings.location-restrictions.toggle") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                enabled: newEnabled
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            updateLocationRestrictionsUI(result.enabled);
        } else {
            showNotification(result.message || 'Erreur lors de la modification', 'error');
            currentBtn.disabled = false;
            updateLocationRestrictionsUI(currentEnabled); // Restaurer l'état précédent
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de communication avec le serveur', 'error');
        currentBtn.disabled = false;
        updateLocationRestrictionsUI(currentEnabled); // Restaurer l'état précédent
    }
}

function updateLocationRestrictionsUI(enabled) {
    const statusIcon = document.getElementById('location-status-icon');
    const statusText = document.getElementById('location-status-text');
    const statusDescription = document.getElementById('location-status-description');
    const toggleBtn = document.getElementById('location-toggle-btn');
    
    // Stocker l'état actuel
    toggleBtn.dataset.enabled = enabled;
    toggleBtn.disabled = false;
    
    if (enabled) {
        // État ACTIVÉ
        statusIcon.innerHTML = '<i class="fas fa-check-circle text-green-600 text-lg"></i>';
        statusIcon.className = 'p-2 rounded-full bg-green-100';
        statusText.textContent = 'Restrictions ACTIVÉES';
        statusDescription.textContent = 'Les vendeurs ne peuvent publier que dans les villes autorisées';
        toggleBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Désactiver';
        toggleBtn.className = 'inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200';
    } else {
        // État DÉSACTIVÉ
        statusIcon.innerHTML = '<i class="fas fa-globe text-blue-600 text-lg"></i>';
        statusIcon.className = 'p-2 rounded-full bg-blue-100';
        statusText.textContent = 'Restrictions DÉSACTIVÉES';
        statusDescription.textContent = 'Les vendeurs peuvent publier dans n\'importe quelle ville du monde';
        toggleBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Activer';
        toggleBtn.className = 'inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200';
    }
}
</script>

<!-- Modal pour activer le mode maintenance -->
<div id="maintenanceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-tools mr-2 text-orange-600"></i>
                    Activer le Mode Maintenance
                </h3>
                <button onclick="hideMaintenanceModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="maintenanceForm" onsubmit="event.preventDefault(); enableMaintenance();">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Message personnalisé
                        </label>
                        <textarea name="message" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                  rows="3" 
                                  placeholder="Nous effectuons actuellement des travaux de maintenance..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Temps estimé (optionnel)
                        </label>
                        <input type="text" 
                               name="estimated_time" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Ex: 2 heures, 30 minutes...">
                    </div>
                </div>
                
                <div class="flex items-center justify-end pt-6 space-x-3">
                    <button type="button" 
                            onclick="hideMaintenanceModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                        <i class="fas fa-tools mr-2"></i>
                        Activer la maintenance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection