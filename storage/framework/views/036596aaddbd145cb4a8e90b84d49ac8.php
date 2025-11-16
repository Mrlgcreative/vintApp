

<?php $__env->startSection('title', 'Zones Autorisées'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4 sm:space-y-6 px-3 sm:px-0">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-map-marked-alt text-primary-600 mr-2 text-lg sm:text-xl"></i>
                <span class="hidden sm:inline">Zones Autorisées</span>
                <span class="sm:hidden">Zones</span>
            </h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mt-1">
                <span class="hidden sm:inline">Gérez les villes et régions ayant accès à VintApp</span>
                <span class="sm:hidden">Gérez les villes et régions</span>
            </p>
        </div>
        
        <div class="flex flex-col xs:flex-row gap-2 sm:gap-3">
            <button onclick="openModal('addCityModal')" 
                    class="inline-flex items-center justify-center px-3 sm:px-4 py-2 text-sm bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                <span class="hidden xs:inline">Ajouter une ville</span>
                <span class="xs:hidden">Ville</span>
            </button>
            <button onclick="openModal('addRegionModal')" 
                    class="inline-flex items-center justify-center px-3 sm:px-4 py-2 text-sm bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                <span class="hidden xs:inline">Ajouter une région</span>
                <span class="xs:hidden">Région</span>
            </button>
        </div>
    </div>

    <!-- Alerts -->
    <?php if(session('success')): ?>
        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-3 sm:p-4 flex items-start gap-2 sm:gap-3">
            <i class="fas fa-check-circle text-green-500 text-lg sm:text-xl mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm sm:text-base text-green-800 font-medium"><?php echo e(session('success')); ?></p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-3 sm:p-4">
            <div class="flex items-start gap-2 sm:gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-lg sm:text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm sm:text-base text-red-800 font-semibold mb-2">Erreur(s) :</p>
                    <ul class="list-disc list-inside space-y-1 text-sm sm:text-base text-red-700">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- 🧪 SECTION: Testeur de Restrictions Géographiques -->
    <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-orange-300 rounded-xl shadow-sm p-4 sm:p-6">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center shrink-0">
                <i class="fas fa-flask text-white text-lg sm:text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-1">
                    Testeur de Restrictions Géographiques
                </h3>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                    Simulez des villes pour vérifier si le système de blocage fonctionne correctement
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Test avec ville autorisée -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-green-200">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-check-circle text-green-600"></i>
                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">Test Ville Autorisée</h4>
                </div>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mb-3">
                    Simuler une ville de la liste des villes actives
                </p>
                <div class="flex flex-col xs:flex-row gap-2">
                    <select id="allowedCitySelect" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">-- Choisir une ville --</option>
                        <?php $__currentLoopData = \App\Models\AllowedCity::where('is_active', true)->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($city->name); ?>"><?php echo e($city->name); ?> (<?php echo e($city->country); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button onclick="testAllowedCity()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium whitespace-nowrap">
                        <i class="fas fa-play mr-1"></i> Tester
                    </button>
                </div>
            </div>

            <!-- Test avec ville NON autorisée -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-red-200">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-times-circle text-red-600"></i>
                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">Test Ville Bloquée</h4>
                </div>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mb-3">
                    Simuler une ville NON présente dans la liste
                </p>
                <div class="flex flex-col xs:flex-row gap-2">
                    <input type="text" id="blockedCityInput" placeholder="Ex: Bukavu, Goma, Lubumbashi..." class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <button onclick="testBlockedCity()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium whitespace-nowrap">
                        <i class="fas fa-ban mr-1"></i> Tester
                    </button>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <div class="flex items-start gap-2">
                <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                <div class="flex-1 text-xs sm:text-sm text-blue-800">
                    <p class="font-semibold mb-1">Comment ça marche :</p>
                    <ul class="list-disc list-inside space-y-1 text-blue-700">
                        <li><strong>Ville autorisée :</strong> Vous devriez voir la page normalement</li>
                        <li><strong>Ville bloquée :</strong> Vous devriez voir la page "Zone non disponible"</li>
                        <li>Pour activer le mode test, ajoutez <code class="bg-blue-100 px-1 rounded">ENABLE_GEO_TESTING=true</code> dans .env</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statut du mode test -->
        <div class="mt-3 text-center">
            <?php if(env('ENABLE_GEO_TESTING')): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-300">
                    <i class="fas fa-check-circle mr-1"></i> Mode Test ACTIVÉ
                </span>
            <?php else: ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
                    <i class="fas fa-times-circle mr-1"></i> Mode Test DÉSACTIVÉ
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- 🗺️ SECTION: Carte GPS Interactive OpenStreetMap -->
    <div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-3 sm:mb-4">
            <div>
                <h2 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-globe-africa text-green-600 mr-2"></i>
                    <span class="hidden sm:inline">Carte des Villes Autorisées (OpenStreetMap)</span>
                    <span class="sm:hidden">Carte des Villes</span>
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                    <span id="map-city-count" class="font-semibold text-primary-600"><?php echo e($stats['total_cities']); ?></span> villes dans 
                    <span id="map-country-count" class="font-semibold text-primary-600"><?php echo e($stats['countries_count']); ?></span> pays
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="centerMapOnCountry('COD')" 
                        class="px-2 sm:px-3 py-1.5 sm:py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-xs sm:text-sm font-medium">
                    <span class="country-flag">🇨🇩</span> RDC
                </button>
                <button onclick="fitAllMarkers()" 
                        class="px-2 sm:px-3 py-1.5 sm:py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 rounded-lg hover:bg-gray-200 dark:bg-gray-700 transition-colors text-xs sm:text-sm font-medium">
                    <i class="fas fa-compress-arrows-alt"></i> <span class="hidden xs:inline">Tout</span>
                </button>
                <button onclick="refreshMapData()" 
                        class="px-2 sm:px-3 py-1.5 sm:py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-xs sm:text-sm font-medium">
                    <i class="fas fa-sync-alt"></i> <span class="hidden xs:inline">Actualiser</span>
                </button>
                <button onclick="showMapHelp()" 
                        class="px-2 sm:px-3 py-1.5 sm:py-2 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-colors text-xs sm:text-sm font-medium"
                        title="Aide & Raccourcis">
                    <i class="fas fa-question-circle"></i>
                </button>
            </div>
        </div>
        
        <!-- Carte OpenStreetMap (Leaflet) -->
        <div style="position: relative;">
            <div id="map"></div>
            <div id="map-loading" class="map-loading hidden">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-primary-600 mb-2"></i>
                    <p class="text-gray-700 dark:text-gray-200 font-medium">Chargement de la carte...</p>
                </div>
            </div>
        </div>
        
        <!-- Légende -->
        <!-- Mode marquage -->
        <div id="mapModeIndicator" class="hidden mt-3 mb-2 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-mouse-pointer text-blue-600 text-lg"></i>
                    <div>
                        <p class="font-semibold text-blue-900 text-sm">Mode Marquage Activé</p>
                        <p class="text-xs text-blue-700">Cliquez sur la carte pour placer un marqueur de nouvelle ville</p>
                    </div>
                </div>
                <button onclick="disableMapMarkerMode()" 
                        class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fas fa-times mr-1"></i> Désactiver
                </button>
            </div>
        </div>
        
        <div class="mt-4 flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-500 rounded-full border-2 border-white shadow"></div>
                <span class="text-gray-700 dark:text-gray-200">Ville active</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-red-500 rounded-full border-2 border-white shadow"></div>
                <span class="text-gray-700 dark:text-gray-200">Ville inactive</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-500 rounded-full border-2 border-white shadow"></div>
                <span class="text-gray-700 dark:text-gray-200">Nouveau marqueur</span>
            </div>
            <button onclick="enableMapMarkerMode()" 
                    class="flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors font-medium">
                <i class="fas fa-map-marker-alt"></i>
                <span class="hidden sm:inline">Ajouter ville par clic</span>
                <span class="sm:hidden">Marquer</span>
            </button>
            <div class="flex items-center gap-2 ml-auto">
                <i class="fas fa-map text-gray-500 dark:text-gray-400"></i>
                <span class="text-xs text-gray-500 dark:text-gray-400">Propulsé par OpenStreetMap</span>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-2 sm:gap-3 lg:gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg sm:rounded-xl p-3 sm:p-4 lg:p-6 border border-blue-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-medium text-blue-600">Villes Totales</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900 mt-0.5 sm:mt-1"><?php echo e($stats['total_cities']); ?></p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-blue-500 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas fa-city text-white text-sm sm:text-base lg:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg sm:rounded-xl p-3 sm:p-4 lg:p-6 border border-green-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-medium text-green-600">Villes Actives</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-green-900 mt-0.5 sm:mt-1"><?php echo e($stats['active_cities']); ?></p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-green-500 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle text-white text-sm sm:text-base lg:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-lg sm:rounded-xl p-3 sm:p-4 lg:p-6 border border-primary-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-medium text-primary-600">Régions Totales</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-primary-900 mt-0.5 sm:mt-1"><?php echo e($stats['total_regions']); ?></p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-primary-500 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas fa-map text-white text-sm sm:text-base lg:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-accent-50 to-accent-100 rounded-xl p-6 border border-accent-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-accent-600">Régions Actives</p>
                    <p class="text-3xl font-bold text-accent-900 mt-1"><?php echo e($stats['active_regions']); ?></p>
                </div>
                <div class="w-12 h-12 bg-accent-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-double text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-600">Pays</p>
                    <p class="text-3xl font-bold text-orange-900 mt-1"><?php echo e($stats['countries_count']); ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-flag text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Action rapide -->
    <?php if($stats['total_cities'] == 0): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
            <i class="fas fa-lightbulb text-blue-500 text-4xl mb-3"></i>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Démarrage rapide</h3>
            <p class="text-blue-700 mb-4">Ajoutez les principales villes de RDC en un clic</p>
            <form action="<?php echo e(route('admin.locations.seed')); ?>" method="POST" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-magic mr-2"></i>
                    Initialiser les villes par défaut
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
            <nav class="flex -mb-px">
                <button onclick="switchTab('cities')" 
                        id="tab-cities"
                        class="tab-button active px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium border-b-2 border-primary-600 text-primary-600 whitespace-nowrap">
                    <i class="fas fa-city mr-1 sm:mr-2"></i>
                    <span class="hidden xs:inline">Villes (<?php echo e($cities->total()); ?>)</span>
                    <span class="xs:hidden">Villes</span>
                </button>
                <button onclick="switchTab('regions')" 
                        id="tab-regions"
                        class="tab-button px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-200 hover:border-gray-300 dark:border-gray-600 whitespace-nowrap">
                    <i class="fas fa-map-marked mr-1 sm:mr-2"></i>
                    <span class="hidden xs:inline">Régions (<?php echo e($regions->total()); ?>)</span>
                    <span class="xs:hidden">Régions</span>
                </button>
            </nav>
        </div>

        <!-- Tab Content: Villes -->
        <div id="content-cities" class="tab-content p-3 sm:p-4 lg:p-6">
            <?php if($cities->count() > 0): ?>
                <!-- Vue Desktop (Table) -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ville</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Région</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pays</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Code</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                                <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                            <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 dark:bg-gray-900">
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($city->name); ?></div>
                                    <?php if($city->description): ?>
                                        <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e(Str::limit($city->description, 40)); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                    <?php echo e($city->region ?? '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                    <?php echo e($city->country); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($city->city_code): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                            <?php echo e($city->city_code); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="toggleCityStatus(<?php echo e($city->id); ?>)" 
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo e($city->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100'); ?>">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        <?php echo e($city->is_active ? 'Active' : 'Inactive'); ?>

                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="editCity(<?php echo e($city->id); ?>)" 
                                            class="text-primary-600 hover:text-primary-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteCity(<?php echo e($city->id); ?>, '<?php echo e($city->name); ?>')" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vue Mobile (Cards) -->
                <div class="lg:hidden space-y-4">
                    <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white"><?php echo e($city->name); ?></h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1"><?php echo e($city->region ?? 'Région non spécifiée'); ?></p>
                            </div>
                            <button onclick="toggleCityStatus(<?php echo e($city->id); ?>)" 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($city->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100'); ?>">
                                <?php echo e($city->is_active ? 'Active' : 'Inactive'); ?>

                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Pays:</span>
                                <span class="text-gray-900 dark:text-white ml-1"><?php echo e($city->country); ?></span>
                            </div>
                            <?php if($city->city_code): ?>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Code:</span>
                                <span class="text-gray-900 dark:text-white ml-1"><?php echo e($city->city_code); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex gap-2 pt-3 border-t border-gray-300 dark:border-gray-600">
                            <button onclick="editCity(<?php echo e($city->id); ?>)" 
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-colors">
                                <i class="fas fa-edit mr-2"></i>
                                Modifier
                            </button>
                            <button onclick="deleteCity(<?php echo e($city->id); ?>, '<?php echo e($city->name); ?>')" 
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer
                            </button>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    <?php echo e($cities->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-city text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucune ville enregistrée</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">Commencez par ajouter les villes autorisées</p>
                    <button onclick="openModal('addCityModal')" 
                            class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Ajouter une ville
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab Content: Régions -->
        <div id="content-regions" class="tab-content hidden p-6">
            <?php if($regions->count() > 0): ?>
                <!-- Vue Desktop (Table) -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Région</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pays</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                            <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 dark:bg-gray-900">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($region->name); ?></div>
                                    <?php if($region->description): ?>
                                        <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e(Str::limit($region->description, 40)); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                    <?php echo e($region->country); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($region->region_code): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                            <?php echo e($region->region_code); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="toggleRegionStatus(<?php echo e($region->id); ?>)" 
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo e($region->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100'); ?>">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        <?php echo e($region->is_active ? 'Active' : 'Inactive'); ?>

                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="editRegion(<?php echo e($region->id); ?>)" 
                                            class="text-primary-600 hover:text-primary-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteRegion(<?php echo e($region->id); ?>, '<?php echo e($region->name); ?>')" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vue Mobile (Cards) -->
                <div class="lg:hidden space-y-4">
                    <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white"><?php echo e($region->name); ?></h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1"><?php echo e($region->country); ?></p>
                            </div>
                            <button onclick="toggleRegionStatus(<?php echo e($region->id); ?>)" 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($region->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100'); ?>">
                                <?php echo e($region->is_active ? 'Active' : 'Inactive'); ?>

                            </button>
                        </div>
                        
                        <?php if($region->region_code): ?>
                        <div class="text-sm mb-3">
                            <span class="text-gray-500 dark:text-gray-400">Code:</span>
                            <span class="text-gray-900 dark:text-white ml-1"><?php echo e($region->region_code); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex gap-2 pt-3 border-t border-gray-300 dark:border-gray-600">
                            <button onclick="editRegion(<?php echo e($region->id); ?>)" 
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-colors">
                                <i class="fas fa-edit mr-2"></i>
                                Modifier
                            </button>
                            <button onclick="deleteRegion(<?php echo e($region->id); ?>, '<?php echo e($region->name); ?>')" 
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer
                            </button>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    <?php echo e($regions->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-map text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucune région enregistrée</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">Commencez par ajouter les régions autorisées</p>
                    <button onclick="openModal('addRegionModal')" 
                            class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Ajouter une région
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Ajouter une ville (🌍 Version Mondiale) -->
<div id="addCityModal" class="fixed inset-0 z-50 hidden overflow-y-auto px-3 sm:px-4">
    <div class="flex items-center justify-center min-h-screen pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeModal('addCityModal')"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-4 pb-4 sm:px-6 sm:pt-5 sm:pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full sm:max-w-2xl">
            <div class="mb-3 sm:mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-globe text-primary-600 mr-2 text-sm sm:text-base"></i>
                            <span class="hidden sm:inline">Ajouter une ville (monde entier)</span>
                            <span class="sm:hidden">Ajouter une ville</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">Recherchez n'importe quelle ville dans le monde</p>
                    </div>
                    <button onclick="closeModal('addCityModal')" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 ml-2">
                        <i class="fas fa-times text-lg sm:text-xl"></i>
                    </button>
                </div>
            </div>
            
            <form action="<?php echo e(route('admin.locations.cities.store')); ?>" method="POST" id="cityForm" class="space-y-3 sm:space-y-4" onsubmit="return handleCityFormSubmit(event)">
                <?php echo csrf_field(); ?>
                
                <!-- Sélection du pays -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        <i class="fas fa-flag mr-1"></i>
                        Pays <span class="text-red-500">*</span>
                    </label>
                    <select id="worldCountrySelect" name="country_code" required 
                            onchange="onCountryChange()"
                            class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Sélectionnez un pays --</option>
                        <option value="CD" selected>🇨🇩 Congo (RDC)</option>
                        <option value="CG">🇨🇬 Congo-Brazzaville</option>
                        <option value="FR">🇫🇷 France</option>
                        <option value="BE">🇧🇪 Belgique</option>
                        <option value="CA">🇨🇦 Canada</option>
                        <option value="US">🇺🇸 États-Unis</option>
                        <!-- Les autres pays seront chargés dynamiquement -->
                    </select>
                    <input type="hidden" name="country" id="countryName" value="">
                </div>

                <!-- Recherche de ville avec autocomplete -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        <i class="fas fa-search mr-1"></i>
                        Rechercher une ville <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="citySearchInput" 
                               autocomplete="off"
                               placeholder="Tapez au moins 3 caractères..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <div id="citySearchResults" class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg mt-1 shadow-lg max-h-60 overflow-y-auto hidden"></div>
                        <div id="citySearchLoading" class="hidden absolute right-3 top-3">
                            <i class="fas fa-spinner fa-spin text-primary-600"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Exemples: Paris, Tokyo, New York, Kinshasa...</p>
                    <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700">
                        <i class="fas fa-lightbulb mr-1"></i>
                        <strong>Astuce :</strong> Vous pouvez aussi cliquer sur la carte ci-dessous pour placer un marqueur !
                    </div>
                </div>

                <!-- Nom de la ville (rempli auto) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nom de la ville <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="cityNameInput" required readonly
                           class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg cursor-not-allowed"
                           placeholder="Sélectionnez une ville ci-dessus">
                </div>

                <!-- Région/Province (rempli auto) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Région/Province/État</label>
                    <input type="text" name="region" id="cityRegionInput"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Auto-rempli ou modifiable">
                </div>

                <!-- Coordonnées GPS (rempli auto) -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            <i class="fas fa-map-pin mr-1"></i>
                            Latitude <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="latitude" id="cityLatitudeInput" step="0.000001" required readonly
                               class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg cursor-not-allowed"
                               placeholder="Auto">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            <i class="fas fa-map-pin mr-1"></i>
                            Longitude <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="longitude" id="cityLongitudeInput" step="0.000001" required readonly
                               class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg cursor-not-allowed"
                               placeholder="Auto">
                    </div>
                </div>

                <!-- Aperçu sur la carte (mini) -->
                <div id="cityPreview" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center text-white text-2xl flex-shrink-0">
                            <span id="cityPreviewFlag">🏙️</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 dark:text-white" id="cityPreviewName">-</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300" id="cityPreviewLocation">-</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="cityPreviewCoords">-</p>
                        </div>
                    </div>
                </div>
                
                <!-- Options avancées (collapsible) -->
                <details class="border border-gray-200 dark:border-gray-700 rounded-lg">
                    <summary class="px-4 py-2 bg-gray-50 dark:bg-gray-900 cursor-pointer hover:bg-gray-100 dark:bg-gray-800 transition-colors font-medium text-sm text-gray-700 dark:text-gray-200">
                        <i class="fas fa-cog mr-2"></i>
                        Options avancées (optionnel)
                    </summary>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Code unique</label>
                            <input type="text" name="city_code" 
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Ex: PAR-01, TOK-01">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Description</label>
                            <textarea name="description" rows="2" 
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="Informations complémentaires..."></textarea>
                        </div>
                    </div>
                </details>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked 
                           class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500">
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-200">Activer immédiatement cette ville</label>
                </div>
                
                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeModal('addCityModal')" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:bg-gray-900 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Annuler
                    </button>
                    <button type="submit" id="submitCityBtn" disabled
                            class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
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
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-map text-primary-600 mr-2"></i>
                    Ajouter une nouvelle région
                </h3>
            </div>
            
            <form action="<?php echo e(route('admin.locations.regions.store')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nom de la région <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Ex: Haut-Katanga">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Pays <span class="text-red-500">*</span></label>
                    <select name="country" required 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="Congo (RDC)" selected>Congo (RDC)</option>
                        <option value="Congo (Brazzaville)">Congo (Brazzaville)</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Code unique</label>
                    <input type="text" name="region_code" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Ex: HK-01">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Informations complémentaires..."></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked 
                           class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500">
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-200">Activer immédiatement</label>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('addRegionModal')" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:bg-gray-900 transition-colors">
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

<?php $__env->startPush('scripts'); ?>
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
        button.classList.add('border-transparent', 'text-gray-500 dark:text-gray-400');
    });
    
    // Afficher le contenu actif
    document.getElementById(`content-${tab}`).classList.remove('hidden');
    
    // Activer le bouton
    const activeButton = document.getElementById(`tab-${tab}`);
    activeButton.classList.add('border-primary-600', 'text-primary-600', 'active');
    activeButton.classList.remove('border-transparent', 'text-gray-500 dark:text-gray-400');
}

// Gestion des modals
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// ========================================
// 🧪 FONCTIONS DE TEST GÉOGRAPHIQUE
// ========================================

// Tester une ville autorisée
function testAllowedCity() {
    const select = document.getElementById('allowedCitySelect');
    const cityName = select.value;
    
    if (!cityName) {
        alert('⚠️ Veuillez sélectionner une ville !');
        return;
    }
    
    // Ouvrir dans un nouvel onglet avec paramètre test_city
    const testUrl = `<?php echo e(url('/')); ?>?test_city=${encodeURIComponent(cityName)}`;
    
    // Afficher un message de confirmation
    if (confirm(`🧪 Test en cours...\n\n✅ Ville simulée : ${cityName}\n\nLe site devrait s'afficher normalement.\n\nVoulez-vous ouvrir dans un nouvel onglet ?`)) {
        window.open(testUrl, '_blank');
    }
}

// Tester une ville bloquée
function testBlockedCity() {
    const input = document.getElementById('blockedCityInput');
    const cityName = input.value.trim();
    
    if (!cityName) {
        alert('⚠️ Veuillez entrer un nom de ville !');
        return;
    }
    
    // Vérifier si la ville n'est PAS dans la liste des villes autorisées
    const allowedSelect = document.getElementById('allowedCitySelect');
    const allowedCities = Array.from(allowedSelect.options).map(opt => opt.value.toLowerCase());
    
    if (allowedCities.includes(cityName.toLowerCase())) {
        alert(`⚠️ ERREUR : "${cityName}" est dans la liste des villes autorisées !\n\nPour tester le blocage, entrez une ville qui n'est PAS dans la liste.`);
        return;
    }
    
    // Ouvrir dans un nouvel onglet avec paramètre test_city
    const testUrl = `<?php echo e(url('/')); ?>?test_city=${encodeURIComponent(cityName)}`;
    
    // Afficher un message de confirmation
    if (confirm(`🧪 Test en cours...\n\n❌ Ville simulée : ${cityName}\n\nVous devriez voir la page "Zone non disponible".\n\nVoulez-vous ouvrir dans un nouvel onglet ?`)) {
        window.open(testUrl, '_blank');
    }
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
            // Mettre à jour le marqueur sur la carte
            updateCityMarkerStatus(cityId, data.city);
            showToast('Statut de la ville mis à jour', 'success');
            // Recharger pour mettre à jour l'interface
            setTimeout(() => location.reload(), 1000);
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
    
    // Supprimer le marqueur de la carte avant de supprimer de la DB
    removeCityMarkerFromMap(cityId);
    
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

// ========================================
// 🗺️ LEAFLET MAP INITIALIZATION
// ========================================

let map;
let markers;
let allCitiesData = [];
let mapMarkerMode = false;
let tempMarker = null;
let cityMarkersMap = new Map(); // Map pour stocker les marqueurs par cityId

// Initialiser la carte Leaflet
function initMap() {
    // Créer la carte centrée sur l'Afrique centrale
    map = L.map('map').setView([-1.95, 24.77], 5);
    
    // Ajouter le layer OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
    
    // Créer le groupe de clusters
    markers = L.markerClusterGroup({
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true,
        maxClusterRadius: 50
    });
    
    map.addLayer(markers);
    
    // Ajouter l'événement de clic sur la carte pour le mode marquage
    map.on('click', onMapClick);
    
    // Charger les villes
    loadCitiesOnMap();
}

// Charger toutes les villes avec coordonnées GPS
function loadCitiesOnMap() {
    showMapLoading(true);
    
    fetch('/admin/settings/locations/api/cities/map')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allCitiesData = data.cities;
                displayCitiesOnMap(allCitiesData);
                
                // Mettre à jour le compteur
                document.getElementById('map-city-count').textContent = data.total;
            }
            showMapLoading(false);
        })
        .catch(error => {
            console.error('Erreur lors du chargement des villes:', error);
            showMapLoading(false);
        });
}

// Afficher les villes sur la carte
function displayCitiesOnMap(cities) {
    // Vider les markers existants
    markers.clearLayers();
    cityMarkersMap.clear();
    
    cities.forEach(city => {
        if (city.latitude && city.longitude) {
            addCityMarkerToMap(city);
        }
    });
    
    // Ajuster la vue pour afficher tous les markers
    if (cities.length > 0 && markers.getLayers().length > 0) {
        map.fitBounds(markers.getBounds(), { padding: [50, 50] });
    }
}

// Ajouter un marqueur de ville sur la carte
function addCityMarkerToMap(city) {
    // Couleur selon le statut
    const iconColor = city.is_active ? '#10b981' : '#ef4444'; // green-500 : red-500
    
    // Créer un marqueur personnalisé avec logo de localisation
    const customIcon = L.divIcon({
        className: 'custom-location-icon',
        html: `<div style="
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        ">
            <i class="fas fa-map-marker-alt" style="
                font-size: 32px;
                color: ${iconColor};
                filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));
                -webkit-filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));
            "></i>
            <div style="
                position: absolute;
                top: 8px;
                left: 50%;
                transform: translateX(-50%);
                width: 12px;
                height: 12px;
                background-color: white;
                border-radius: 50%;
                border: 2px solid ${iconColor};
            "></div>
        </div>`,
        iconSize: [32, 40],
        iconAnchor: [16, 40],
        popupAnchor: [0, -40]
    });
    
    const marker = L.marker([city.latitude, city.longitude], { icon: customIcon });
    
    // Contenu du popup
    const popupContent = `
        <div class="marker-popup" style="min-width: 220px;">
            <h4>
                <span style="font-size: 1.3rem; margin-right: 6px;">${getCountryFlag(city.country_code)}</span>
                ${city.name}
            </h4>
            <p><strong>Pays:</strong> ${city.country}</p>
            ${city.population ? `<p><strong>Population:</strong> ${formatNumber(city.population)}</p>` : ''}
            <p>
                <strong>Statut:</strong> 
                <span class="status-badge ${city.is_active ? 'status-active' : 'status-inactive'}">
                    ${city.is_active ? '✓ Active' : '✗ Inactive'}
                </span>
            </p>
            <p style="font-size: 11px; color: #6b7280; margin-top: 8px; border-top: 1px solid #e5e7eb; padding-top: 8px;">
                📍 ${city.latitude.toFixed(4)}°, ${city.longitude.toFixed(4)}°
            </p>
        </div>
    `;
    
    marker.bindPopup(popupContent);
    markers.addLayer(marker);
    
    // Stocker le marqueur avec l'ID de la ville
    cityMarkersMap.set(city.id, marker);
    
    // Ajouter la ville aux données si elle n'existe pas déjà
    if (!allCitiesData.find(c => c.id === city.id)) {
        allCitiesData.push(city);
    }
}

// Supprimer un marqueur de ville de la carte
function removeCityMarkerFromMap(cityId) {
    const marker = cityMarkersMap.get(cityId);
    if (marker) {
        markers.removeLayer(marker);
        cityMarkersMap.delete(cityId);
        
        // Supprimer des données
        allCitiesData = allCitiesData.filter(c => c.id !== cityId);
        
        showToast('Marqueur supprimé de la carte', 'info');
    }
}

// Mettre à jour le statut d'un marqueur de ville
function updateCityMarkerStatus(cityId, cityData) {
    // Supprimer l'ancien marqueur
    removeCityMarkerFromMap(cityId);
    
    // Ajouter le nouveau marqueur avec le statut mis à jour
    if (cityData && cityData.latitude && cityData.longitude) {
        addCityMarkerToMap(cityData);
        showToast(`Marqueur de ${cityData.name} mis à jour`, 'success');
    }
}

// Centrer la carte sur un pays
function centerMapOnCountry(countryCode) {
    const countryCenters = {
        'COD': { lat: -4.0383, lng: 21.7587, zoom: 6 },  // RDC
        'COG': { lat: -0.228, lng: 15.8277, zoom: 6 },    // Congo
        'RWA': { lat: -1.9403, lng: 29.8739, zoom: 8 },   // Rwanda
        'BDI': { lat: -3.3731, lng: 29.9189, zoom: 8 },   // Burundi
        'UGA': { lat: 1.3733, lng: 32.2903, zoom: 7 },    // Ouganda
        'TZA': { lat: -6.3690, lng: 34.8888, zoom: 6 },   // Tanzanie
        'KEN': { lat: -0.0236, lng: 37.9062, zoom: 6 },   // Kenya
        'ZMB': { lat: -13.1339, lng: 27.8493, zoom: 6 },  // Zambie
        'AGO': { lat: -11.2027, lng: 17.8739, zoom: 6 },  // Angola
        'ZAF': { lat: -30.5595, lng: 22.9375, zoom: 6 },  // Afrique du Sud
        'CMR': { lat: 7.3697, lng: 12.3547, zoom: 6 },    // Cameroun
        'GAB': { lat: -0.8037, lng: 11.6094, zoom: 7 },   // Gabon
        'CAF': { lat: 6.6111, lng: 20.9394, zoom: 6 }     // RCA
    };
    
    if (countryCenters[countryCode]) {
        const center = countryCenters[countryCode];
        map.setView([center.lat, center.lng], center.zoom);
        
        // Filtrer les villes de ce pays
        const countryCities = allCitiesData.filter(city => city.country_code === countryCode);
        if (countryCities.length > 0) {
            displayCitiesOnMap(countryCities);
        }
    }
}

// Ajuster la vue pour afficher tous les markers
function fitAllMarkers() {
    if (allCitiesData.length > 0) {
        displayCitiesOnMap(allCitiesData);
    }
}

// Actualiser les données de la carte
function refreshMapData() {
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    icon.classList.add('fa-spin');
    
    loadCitiesOnMap();
    
    setTimeout(() => {
        icon.classList.remove('fa-spin');
    }, 1000);
}

// Afficher/masquer le loading
function showMapLoading(show) {
    const loadingEl = document.getElementById('map-loading');
    if (loadingEl) {
        if (show) {
            loadingEl.classList.remove('hidden');
        } else {
            loadingEl.classList.add('hidden');
        }
    }
}

// Obtenir le drapeau emoji d'un pays
function getCountryFlag(countryCode) {
    const flags = {
        'COD': '🇨🇩', 'COG': '🇨🇬', 'RWA': '🇷🇼', 'BDI': '🇧🇮',
        'UGA': '🇺🇬', 'TZA': '🇹🇿', 'KEN': '🇰🇪', 'ZMB': '🇿🇲',
        'AGO': '🇦🇴', 'ZAF': '🇿🇦', 'CMR': '🇨🇲', 'GAB': '🇬🇦',
        'CAF': '🇨🇫'
    };
    return flags[countryCode] || '🌍';
}

// Formater un nombre avec des séparateurs
function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

// ========================================
// 🎯 MODE MARQUAGE SUR CARTE
// ========================================

// Activer le mode marquage
function enableMapMarkerMode() {
    mapMarkerMode = true;
    document.getElementById('mapModeIndicator').classList.remove('hidden');
    document.getElementById('map').style.cursor = 'crosshair';
    
    // Message de confirmation
    showToast('Mode marquage activé ! Cliquez sur la carte pour placer une ville.', 'info');
}

// Désactiver le mode marquage
function disableMapMarkerMode() {
    mapMarkerMode = false;
    document.getElementById('mapModeIndicator').classList.add('hidden');
    document.getElementById('map').style.cursor = '';
    
    // Supprimer le marqueur temporaire si existant
    if (tempMarker) {
        map.removeLayer(tempMarker);
        tempMarker = null;
    }
    
    showToast('Mode marquage désactivé', 'info');
}

// Gestion du clic sur la carte
function onMapClick(e) {
    if (!mapMarkerMode) return;
    
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;
    
    // Supprimer l'ancien marqueur temporaire
    if (tempMarker) {
        map.removeLayer(tempMarker);
    }
    
    // Créer un nouveau marqueur temporaire bleu avec logo de localisation
    const blueIcon = L.divIcon({
        className: 'custom-location-icon temp-marker',
        html: `<div style="
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        ">
            <i class="fas fa-map-marker-alt" style="
                font-size: 36px;
                color: #3b82f6;
                filter: drop-shadow(0 3px 6px rgba(0,0,0,0.5));
                -webkit-filter: drop-shadow(0 3px 6px rgba(0,0,0,0.5));
                animation: pulse 1.5s infinite;
            "></i>
            <div style="
                position: absolute;
                top: 9px;
                left: 50%;
                transform: translateX(-50%);
                width: 14px;
                height: 14px;
                background-color: white;
                border-radius: 50%;
                border: 2px solid #3b82f6;
                animation: pulse 1.5s infinite;
            "></div>
        </div>`,
        iconSize: [36, 44],
        iconAnchor: [18, 44],
        popupAnchor: [0, -44]
    });
    
    tempMarker = L.marker([lat, lng], { icon: blueIcon }).addTo(map);
    
    // Popup avec formulaire rapide
    const popupContent = `
        <div style="min-width: 280px;">
            <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #1f2937;">
                <i class="fas fa-map-marker-alt text-blue-600"></i>
                Nouvelle ville
            </h4>
            <div style="background: #f3f4f6; padding: 8px; border-radius: 6px; margin-bottom: 12px; font-size: 12px;">
                <strong>📍 Coordonnées :</strong><br>
                Latitude: ${lat.toFixed(6)}°<br>
                Longitude: ${lng.toFixed(6)}°
            </div>
            <div style="display: flex; gap: 8px;">
                <button onclick="openAddCityWithCoords(${lat}, ${lng})" 
                        style="flex: 1; padding: 8px 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">
                    <i class="fas fa-plus-circle"></i> Ajouter ville
                </button>
                <button onclick="cancelTempMarker()" 
                        style="padding: 8px 12px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p style="margin: 10px 0 0 0; font-size: 11px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 8px;">
                💡 Le système va rechercher le nom de la ville via géocodage inversé
            </p>
        </div>
    `;
    
    tempMarker.bindPopup(popupContent).openPopup();
    
    // Centrer légèrement au-dessus du marqueur pour voir le popup
    map.panTo([lat, lng]);
    
    showToast('Marqueur placé ! Cliquez sur "Ajouter ville" pour continuer.', 'success');
}

// Ouvrir le modal d'ajout avec coordonnées pré-remplies
function openAddCityWithCoords(lat, lng) {
    // Désactiver le mode marquage
    disableMapMarkerMode();
    
    // Pré-remplir les coordonnées
    document.getElementById('cityLatitudeInput').value = lat.toFixed(6);
    document.getElementById('cityLongitudeInput').value = lng.toFixed(6);
    
    // Effectuer un géocodage inversé pour obtenir le nom de la ville
    reverseGeocode(lat, lng);
    
    // Ouvrir le modal
    openModal('addCityModal');
    
    showToast('Recherche du nom de la ville en cours...', 'info');
}

// Annuler le marqueur temporaire
function cancelTempMarker() {
    if (tempMarker) {
        map.removeLayer(tempMarker);
        tempMarker = null;
    }
    showToast('Marqueur supprimé', 'info');
}

// Géocodage inversé (coordonnées → nom de ville)
function reverseGeocode(lat, lng) {
    const loadingMsg = showToast('🔍 Recherche du nom de la ville...', 'info', 0);
    
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&addressdetails=1`, {
        headers: {
            'Accept-Language': 'fr'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Supprimer le message de loading
        if (loadingMsg) loadingMsg.remove();
        
        if (data && data.address) {
            const address = data.address;
            
            // Extraire le nom de la ville (plusieurs possibilités)
            const cityName = address.city || address.town || address.village || address.municipality || address.county || 'Ville inconnue';
            const region = address.state || address.region || address.province || '';
            const country = address.country || '';
            const countryCode = address.country_code ? address.country_code.toUpperCase() : 'CD';
            
            // Remplir le formulaire
            document.getElementById('cityNameInput').value = cityName;
            document.getElementById('cityRegionInput').value = region;
            document.getElementById('worldCountrySelect').value = countryCode;
            document.getElementById('countryName').value = country;
            
            // Activer le bouton submit
            document.getElementById('submitCityBtn').disabled = false;
            
            // Afficher l'aperçu
            document.getElementById('cityPreviewFlag').textContent = getCountryFlag(countryCode);
            document.getElementById('cityPreviewName').textContent = cityName;
            document.getElementById('cityPreviewLocation').textContent = `${region ? region + ', ' : ''}${country}`;
            document.getElementById('cityPreviewCoords').textContent = `📍 ${lat.toFixed(6)}°, ${lng.toFixed(6)}°`;
            document.getElementById('cityPreview').classList.remove('hidden');
            
            showToast(`✅ Ville trouvée : ${cityName}`, 'success');
        } else {
            showToast('⚠️ Lieu trouvé mais pas de ville identifiée. Entrez le nom manuellement.', 'warning');
            document.getElementById('cityNameInput').value = '';
            document.getElementById('cityNameInput').readOnly = false;
            document.getElementById('cityNameInput').focus();
        }
    })
    .catch(error => {
        console.error('Erreur géocodage inversé:', error);
        if (loadingMsg) loadingMsg.remove();
        showToast('❌ Erreur lors de la recherche. Entrez le nom manuellement.', 'error');
        document.getElementById('cityNameInput').readOnly = false;
        document.getElementById('cityNameInput').focus();
    });
}

// Fonction helper pour afficher des toasts
function showToast(message, type = 'info', duration = 3000) {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-orange-500',
        info: 'bg-blue-500'
    };
    
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3 animate-slide-up`;
    toast.style.animation = 'slideUp 0.3s ease-out';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    if (duration > 0) {
        setTimeout(() => {
            toast.style.animation = 'slideDown 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
    
    return toast;
}

// Visualiser la ville sur la carte principale
function viewOnMap() {
    const lat = parseFloat(document.getElementById('cityLatitudeInput').value);
    const lng = parseFloat(document.getElementById('cityLongitudeInput').value);
    
    if (lat && lng) {
        // Fermer le modal
        closeModal('addCityModal');
        
        // Centrer la carte sur les coordonnées
        map.setView([lat, lng], 12);
        
        // Ajouter un marqueur temporaire si pas déjà présent
        if (!tempMarker) {
            const blueIcon = L.divIcon({
                className: 'custom-location-icon temp-marker',
                html: `<div style="
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">
                    <i class="fas fa-map-marker-alt" style="
                        font-size: 36px;
                        color: #3b82f6;
                        filter: drop-shadow(0 3px 6px rgba(0,0,0,0.5));
                        -webkit-filter: drop-shadow(0 3px 6px rgba(0,0,0,0.5));
                        animation: pulse 1.5s infinite;
                    "></i>
                    <div style="
                        position: absolute;
                        top: 9px;
                        left: 50%;
                        transform: translateX(-50%);
                        width: 14px;
                        height: 14px;
                        background-color: white;
                        border-radius: 50%;
                        border: 2px solid #3b82f6;
                        animation: pulse 1.5s infinite;
                    "></div>
                </div>`,
                iconSize: [36, 44],
                iconAnchor: [18, 44],
                popupAnchor: [0, -44]
            });
            
            tempMarker = L.marker([lat, lng], { icon: blueIcon }).addTo(map);
        }
        
        showToast('📍 Position affichée sur la carte', 'info');
    }
}

// Afficher l'aide de la carte
function showMapHelp() {
    const helpHTML = `
        <div style="max-width: 500px;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #1f2937;">
                <i class="fas fa-info-circle text-primary-600"></i>
                Guide d'utilisation de la carte
            </h3>
            
            <div style="background: #f9fafb; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                    🎯 Mode Marquage
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 13px; color: #6b7280;">
                    <li style="margin-bottom: 6px;">
                        <strong>1.</strong> Cliquez sur "Ajouter ville par clic" ou appuyez sur <kbd style="background: #e5e7eb; padding: 2px 6px; border-radius: 4px; font-family: monospace;">M</kbd>
                    </li>
                    <li style="margin-bottom: 6px;">
                        <strong>2.</strong> Cliquez sur la carte à l'emplacement souhaité
                    </li>
                    <li style="margin-bottom: 6px;">
                        <strong>3.</strong> Le système recherche automatiquement le nom de la ville
                    </li>
                    <li>
                        <strong>4.</strong> Validez et sauvegardez la nouvelle ville
                    </li>
                </ul>
            </div>
            
            <div style="background: #eff6ff; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #1e40af;">
                    ⌨️ Raccourcis clavier
                </h4>
                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Activer/Désactiver mode marquage</span>
                        <kbd style="background: #e5e7eb; padding: 2px 8px; border-radius: 4px; font-family: monospace;">M</kbd>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Annuler le mode marquage</span>
                        <kbd style="background: #e5e7eb; padding: 2px 8px; border-radius: 4px; font-family: monospace;">Escape</kbd>
                    </div>
                </div>
            </div>
            
            <div style="background: #fef3c7; padding: 12px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <p style="margin: 0; font-size: 12px; color: #92400e;">
                    <i class="fas fa-lightbulb" style="color: #f59e0b;"></i>
                    <strong>Astuce :</strong> Vous pouvez zoomer sur la carte (molette ou +/-) pour placer le marqueur avec plus de précision !
                </p>
            </div>
            
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb; text-align: center;">
                <button onclick="this.closest('.leaflet-popup').remove(); enableMapMarkerMode();" 
                        style="padding: 8px 16px; background: #8b5cf6; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-play"></i> Essayer maintenant
                </button>
            </div>
        </div>
    `;
    
    // Créer un popup au centre de la carte
    const center = map.getCenter();
    L.popup({
        maxWidth: 550,
        closeButton: true
    })
    .setLatLng(center)
    .setContent(helpHTML)
    .openOn(map);
}

// ========================================
// 🌍 GESTION DU FORMULAIRE MULTI-PAYS
// ========================================

// Charger les villes majeures d'un pays
function loadMajorCities(countryCode) {
    if (!countryCode) {
        const container = document.getElementById('majorCitiesContainer');
        if (container) container.classList.add('hidden');
        return;
    }
    
    // Mettre à jour le nom du pays
    const select = document.getElementById('countrySelect');
    const nameInput = document.getElementById('countryName');
    if (select && nameInput) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption.dataset.name) {
            nameInput.value = selectedOption.dataset.name;
        }
    }
    
    // Charger les villes majeures
    fetch(`/admin/settings/locations/api/countries/${countryCode}/major-cities`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('majorCitiesContainer');
            if (data.success && data.cities.length > 0 && container) {
                displayMajorCities(data.cities);
                container.classList.remove('hidden');
            } else if (container) {
                container.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            const container = document.getElementById('majorCitiesContainer');
            if (container) container.classList.add('hidden');
        });
}

// Afficher les villes majeures
function displayMajorCities(cities) {
    const container = document.getElementById('majorCitiesList');
    if (!container) return;
    
    container.innerHTML = '';
    
    cities.slice(0, 6).forEach(city => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'px-3 py-2 text-sm bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-lg transition-colors text-left';
        button.innerHTML = `
            <div class="font-medium text-gray-900 dark:text-white">${city.name}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">${city.population ? formatNumber(city.population) + ' hab.' : ''}</div>
        `;
        button.onclick = () => fillCityData(city);
        container.appendChild(button);
    });
}

// Remplir les données de la ville
function fillCityData(city) {
    const fields = {
        'cityName': city.name,
        'cityLatitude': city.latitude,
        'cityLongitude': city.longitude,
        'cityPopulation': city.population,
        'cityTimezone': city.timezone
    };
    
    Object.keys(fields).forEach(id => {
        const element = document.getElementById(id);
        if (element && fields[id]) {
            element.value = fields[id];
        }
    });
    
    // Valider les coordonnées
    if (city.latitude && city.longitude) {
        validateCoordinates(city.latitude, city.longitude);
    }
}

// Valider les coordonnées GPS
function validateCoordinates(lat, lng) {
    const countrySelect = document.getElementById('countrySelect');
    const validationDiv = document.getElementById('gpsValidation');
    
    if (!countrySelect || !validationDiv || !lat || !lng) return;
    
    const countryCode = countrySelect.value;
    if (!countryCode) return;
    
    fetch('/admin/settings/locations/api/validate-coordinates', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            country_code: countryCode,
            latitude: parseFloat(lat),
            longitude: parseFloat(lng)
        })
    })
    .then(response => response.json())
    .then(data => {
        validationDiv.classList.remove('hidden');
        
        if (data.is_valid) {
            validationDiv.innerHTML = `
                <div class="flex items-center gap-2 text-green-700">
                    <i class="fas fa-check-circle"></i>
                    <span>${data.message} (${data.distance_km} km du centre)</span>
                </div>
            `;
        } else {
            validationDiv.innerHTML = `
                <div class="flex items-center gap-2 text-orange-600">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>${data.message} (${data.distance_km} km du centre)</span>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Erreur validation:', error);
    });
}

// Ouvrir le picker GPS (carte modale)
function openGPSPicker() {
    alert('Fonctionnalité GPS Picker à venir!\n\nVous pourrez cliquer sur la carte pour sélectionner les coordonnées.');
    // TODO: Implémenter une modale avec carte Leaflet interactive
}

// ========================================
// 🌍 GESTION MODAL VILLE MONDIALE
// ========================================

let worldCountries = [];
let selectedCityData = null;
let searchTimeout = null;

// Charger tous les pays du monde au chargement
function loadWorldCountries() {
    fetch('/admin/settings/locations/api/world/countries')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                worldCountries = data.countries;
                populateCountrySelect();
            }
        })
        .catch(error => console.error('Erreur chargement pays:', error));
}

// Remplir le select des pays
function populateCountrySelect() {
    const select = document.getElementById('worldCountrySelect');
    if (!select) return;
    
    // Garder les options par défaut et ajouter tous les pays
    const defaultOptions = select.innerHTML;
    
    let optionsHTML = '<option value="">-- Sélectionnez un pays (195 disponibles) --</option>';
    
    worldCountries.forEach(country => {
        optionsHTML += `<option value="${country.code}" data-name="${country.name}" data-flag="${country.flag}">
            ${country.flag} ${country.name}
        </option>`;
    });
    
    select.innerHTML = optionsHTML;
}

// Quand le pays change
function onCountryChange() {
    const select = document.getElementById('worldCountrySelect');
    const selectedOption = select.options[select.selectedIndex];
    const countryName = selectedOption.dataset.name || selectedOption.text.replace(/[^\w\s\(\)]/g, '').trim();
    
    document.getElementById('countryName').value = countryName;
    
    // Réinitialiser la recherche de ville
    document.getElementById('citySearchInput').value = '';
    document.getElementById('citySearchResults').classList.add('hidden');
    resetCityForm();
}

// Recherche de ville avec autocomplete (debounced)
function setupCitySearch() {
    const searchInput = document.getElementById('citySearchInput');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        const countryCode = document.getElementById('worldCountrySelect').value;
        
        if (query.length < 3) {
            document.getElementById('citySearchResults').classList.add('hidden');
            return;
        }
        
        if (!countryCode) {
            alert('Sélectionnez d\'abord un pays');
            return;
        }
        
        // Debounce
        clearTimeout(searchTimeout);
        document.getElementById('citySearchLoading').classList.remove('hidden');
        
        searchTimeout = setTimeout(() => {
            searchCities(query, countryCode);
        }, 500);
    });
}

// Appel API pour chercher les villes
function searchCities(query, countryCode) {
    fetch(`/admin/settings/locations/api/world/cities/search?query=${encodeURIComponent(query)}&country_code=${countryCode}&limit=10`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('citySearchLoading').classList.add('hidden');
            
            if (data.success && data.cities.length > 0) {
                displayCityResults(data.cities);
            } else {
                displayNoResults();
            }
        })
        .catch(error => {
            console.error('Erreur recherche:', error);
            document.getElementById('citySearchLoading').classList.add('hidden');
            displayNoResults();
        });
}

// Afficher les résultats de recherche
function displayCityResults(cities) {
    const resultsDiv = document.getElementById('citySearchResults');
    
    let html = '';
    cities.forEach(city => {
        html += `
            <div class="px-4 py-3 hover:bg-primary-50 cursor-pointer border-b border-gray-100 last:border-0" 
                 onclick='selectCity(${JSON.stringify(city)})'>
                <div class="font-semibold text-gray-900 dark:text-white">${city.city || city.name}</div>
                <div class="text-sm text-gray-600 dark:text-gray-300">${city.state ? city.state + ', ' : ''}${city.country}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    <i class="fas fa-map-pin mr-1"></i>
                    ${city.latitude.toFixed(4)}°, ${city.longitude.toFixed(4)}°
                </div>
            </div>
        `;
    });
    
    resultsDiv.innerHTML = html;
    resultsDiv.classList.remove('hidden');
}

// Aucun résultat
function displayNoResults() {
    const resultsDiv = document.getElementById('citySearchResults');
    resultsDiv.innerHTML = `
        <div class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
            <i class="fas fa-search text-2xl mb-2"></i>
            <p>Aucune ville trouvée</p>
            <p class="text-xs mt-1">Essayez une autre recherche</p>
        </div>
    `;
    resultsDiv.classList.remove('hidden');
}

// Sélectionner une ville
function selectCity(cityData) {
    selectedCityData = cityData;
    
    // Remplir le formulaire
    document.getElementById('cityNameInput').value = cityData.city || cityData.name;
    document.getElementById('cityRegionInput').value = cityData.state || '';
    document.getElementById('cityLatitudeInput').value = cityData.latitude;
    document.getElementById('cityLongitudeInput').value = cityData.longitude;
    
    // Mettre à jour l'aperçu
    const countryCode = document.getElementById('worldCountrySelect').value;
    const country = worldCountries.find(c => c.code === countryCode);
    
    document.getElementById('cityPreviewFlag').textContent = country?.flag || '🏙️';
    document.getElementById('cityPreviewName').textContent = cityData.city || cityData.name;
    document.getElementById('cityPreviewLocation').textContent = `${cityData.state ? cityData.state + ', ' : ''}${cityData.country}`;
    document.getElementById('cityPreviewCoords').textContent = `📍 ${cityData.latitude.toFixed(4)}°, ${cityData.longitude.toFixed(4)}°`;
    document.getElementById('cityPreview').classList.remove('hidden');
    
    // Cacher les résultats
    document.getElementById('citySearchResults').classList.add('hidden');
    document.getElementById('citySearchInput').value = cityData.city || cityData.name;
    
    // Activer le bouton submit
    document.getElementById('submitCityBtn').disabled = false;
}

// Réinitialiser le formulaire
function resetCityForm() {
    selectedCityData = null;
    document.getElementById('cityNameInput').value = '';
    document.getElementById('cityNameInput').readOnly = true;
    document.getElementById('cityRegionInput').value = '';
    document.getElementById('cityLatitudeInput').value = '';
    document.getElementById('cityLongitudeInput').value = '';
    document.getElementById('cityPreview').classList.add('hidden');
    document.getElementById('submitCityBtn').disabled = true;
}

// Fermer les résultats si clic dehors
document.addEventListener('click', function(e) {
    const searchInput = document.getElementById('citySearchInput');
    const resultsDiv = document.getElementById('citySearchResults');
    
    if (searchInput && resultsDiv && !searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
        resultsDiv.classList.add('hidden');
    }
});

// ========================================
// 📝 GESTION SOUMISSION FORMULAIRE VILLE
// ========================================

// Gérer la soumission du formulaire de ville via AJAX
function handleCityFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitCityBtn');
    const originalBtnText = submitBtn.innerHTML;
    
    // Désactiver le bouton et afficher un loader
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Ajout en cours...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ajouter la ville sur la carte immédiatement
            if (data.city && data.city.latitude && data.city.longitude) {
                addCityMarkerToMap(data.city);
                
                // Centrer la carte sur la nouvelle ville
                map.setView([data.city.latitude, data.city.longitude], 10);
                
                showToast(`✅ Ville "${data.city.name}" ajoutée avec succès !`, 'success', 4000);
            }
            
            // Fermer le modal
            closeModal('addCityModal');
            
            // Réinitialiser le formulaire
            form.reset();
            resetCityForm();
            
            // Mettre à jour le compteur de villes
            const cityCount = document.getElementById('map-city-count');
            if (cityCount) {
                cityCount.textContent = parseInt(cityCount.textContent) + 1;
            }
            
            // Recharger la page après 2 secondes pour mettre à jour la liste
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showToast('❌ Erreur: ' + (data.message || 'Impossible d\'ajouter la ville'), 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('❌ Erreur lors de l\'ajout de la ville', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
    
    return false;
}

// Initialiser la carte au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la carte Leaflet
    initMap();
    
    // 🌍 Charger les pays du monde
    loadWorldCountries();
    
    // 🌍 Setup autocomplete ville
    setupCitySearch();
    
    // 🎯 Raccourcis clavier pour le mode marquage
    document.addEventListener('keydown', function(e) {
        // Touche "M" pour activer/désactiver le mode marquage
        if (e.key === 'm' || e.key === 'M') {
            // Vérifier qu'on n'est pas dans un input
            if (document.activeElement.tagName !== 'INPUT' && 
                document.activeElement.tagName !== 'TEXTAREA') {
                e.preventDefault();
                if (mapMarkerMode) {
                    disableMapMarkerMode();
                } else {
                    enableMapMarkerMode();
                }
            }
        }
        
        // Touche "Escape" pour désactiver le mode marquage
        if (e.key === 'Escape' && mapMarkerMode) {
            disableMapMarkerMode();
        }
    });
    
    // Surveiller les changements de coordonnées
    const latInput = document.getElementById('cityLatitude');
    const lngInput = document.getElementById('cityLongitude');
    
    if (latInput && lngInput) {
        latInput.addEventListener('change', function() {
            const lat = this.value;
            const lng = lngInput.value;
            if (lat && lng) {
                validateCoordinates(lat, lng);
            }
        });
        
        lngInput.addEventListener('change', function() {
            const lat = latInput.value;
            const lng = this.value;
            if (lat && lng) {
                validateCoordinates(lat, lng);
            }
        });
    }
    
    // Charger les villes majeures au chargement si pays sélectionné
    const countrySelect = document.getElementById('countrySelect');
    if (countrySelect && countrySelect.value) {
        loadMajorCities(countrySelect.value);
    }
});
</script>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

<style>
    #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        position: relative;
    }
    
    @media (min-width: 640px) {
        #map {
            height: 500px;
            border-radius: 12px;
        }
    }
    
    .country-flag {
        font-size: 1.25rem;
        line-height: 1;
    }
    
    @media (min-width: 640px) {
        .country-flag {
            font-size: 1.5rem;
        }
    }
    
    /* Style pour les popups Leaflet */
    .leaflet-popup-content {
        margin: 8px;
        line-height: 1.4;
    }
    
    @media (min-width: 640px) {
        .leaflet-popup-content {
            margin: 12px;
            line-height: 1.5;
        }
    }
    
    .leaflet-popup-content h4 {
        margin: 0 0 6px 0;
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
    }
    
    @media (min-width: 640px) {
        .leaflet-popup-content h4 {
            margin: 0 0 8px 0;
            font-size: 16px;
        }
    }
    
    .leaflet-popup-content p {
        margin: 3px 0;
        font-size: 12px;
        color: #4b5563;
    }
    
    @media (min-width: 640px) {
        .leaflet-popup-content p {
            margin: 4px 0;
            font-size: 13px;
        }
    }
    
    .marker-popup .status-badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
    
    @media (min-width: 640px) {
        .marker-popup .status-badge {
            padding: 2px 8px;
            font-size: 12px;
        }
    }
    
    .marker-popup .status-active {
        background-color: #d1fae5;
        color: #065f46;
    }
    .marker-popup .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    /* Loading overlay */
    .map-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        z-index: 1000;
    }
    
    /* Style pour les marqueurs Leaflet personnalisés */
    .custom-marker {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .marker-active {
        background-color: #10b981;
    }
    .marker-inactive {
        background-color: #ef4444;
    }
    
    /* Style pour les icônes de localisation personnalisées */
    .custom-location-icon {
        background: transparent !important;
        border: none !important;
    }
    
    /* Animation pour les icônes de localisation */
    .custom-location-icon i {
        transition: transform 0.2s ease;
    }
    
    .custom-location-icon:hover i {
        transform: scale(1.1);
    }
    
    /* Style pour le marqueur temporaire */
    .temp-marker i {
        animation: bounce 1.5s infinite;
    }
    
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }
    
    /* Animations pour les toasts */
    @keyframes slideUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideDown {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(100%);
            opacity: 0;
        }
    }
    
    /* Animation pulse pour le marqueur temporaire */
    @keyframes pulse {
        0% {
            box-shadow: 0 3px 6px rgba(0,0,0,0.4), 0 0 0 0 rgba(59, 130, 246, 0.7);
        }
        50% {
            box-shadow: 0 3px 6px rgba(0,0,0,0.4), 0 0 0 8px rgba(59, 130, 246, 0);
        }
        100% {
            box-shadow: 0 3px 6px rgba(0,0,0,0.4), 0 0 0 0 rgba(59, 130, 246, 0);
        }
    }
    
    /* Curseur crosshair pour le mode marquage */
    #map.marker-mode {
        cursor: crosshair !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/locations/index.blade.php ENDPATH**/ ?>