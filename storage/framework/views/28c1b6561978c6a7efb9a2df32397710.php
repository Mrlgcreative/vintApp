

<?php $__env->startSection('title', 'Paramètres des Couleurs'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-4 px-3 sm:py-6 sm:px-6 lg:px-8">
    <?php if(session('success')): ?>
        <div class="mb-4 sm:mb-6 bg-green-50 border border-green-200 text-green-800 px-3 py-2 sm:px-4 sm:py-3 rounded-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-sm sm:text-base"></i>
                <span class="text-sm sm:text-base flex-1"><?php echo e(session('success')); ?></span>
                <button type="button" class="ml-2 text-green-600 hover:text-green-800" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-4 sm:mb-6 bg-red-50 border border-red-200 text-red-800 px-3 py-2 sm:px-4 sm:py-3 rounded-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-sm sm:text-base"></i>
                <span class="text-sm sm:text-base flex-1"><?php echo e(session('error')); ?></span>
                <button type="button" class="ml-2 text-red-600 hover:text-red-800" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col gap-3 sm:gap-0 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-palette mr-2 sm:mr-3 text-purple-600 text-base sm:text-xl"></i>
                <span class="hidden sm:inline">Paramètres des Couleurs</span>
                <span class="sm:hidden">Couleurs</span>
            </h1>
            <div class="flex flex-col xs:flex-row gap-2 sm:gap-3">
                <button onclick="openImportModal()" class="inline-flex items-center justify-center px-3 py-2 sm:px-4 text-sm border border-green-300 text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                    <i class="fas fa-upload mr-2"></i>
                    <span class="hidden xs:inline">Importer</span>
                    <span class="xs:hidden">Import</span>
                </button>
                <a href="<?php echo e(route('admin.settings.colors.export')); ?>" class="inline-flex items-center justify-center px-3 py-2 sm:px-4 text-sm border border-blue-300 text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>
                    <span class="hidden xs:inline">Exporter</span>
                    <span class="xs:hidden">Export</span>
                </a>
            </div>
        </div>

        <!-- Navigation par onglets -->
        <div class="border-b border-gray-200 bg-gray-50 dark:bg-gray-900">
            <nav class="flex space-x-4 px-4 sm:px-6" aria-label="Paramètres">
                <a href="<?php echo e(route('admin.settings.index')); ?>" 
                   class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-200 hover:border-gray-300 dark:border-gray-600 whitespace-nowrap">
                    <i class="fas fa-sliders-h mr-2"></i>
                    Général
                </a>
                <a href="<?php echo e(route('admin.settings.colors')); ?>" 
                   class="inline-flex items-center px-3 py-2 border-b-2 border-purple-500 text-sm font-medium text-purple-600 whitespace-nowrap">
                    <i class="fas fa-palette mr-2"></i>
                    Couleurs
                </a>
                <a href="<?php echo e(route('admin.settings.preregistration')); ?>" 
                   class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-200 hover:border-gray-300 dark:border-gray-600 whitespace-nowrap">
                    <i class="fas fa-user-clock mr-2"></i>
                    Préinscription
                </a>
            </nav>
        </div>

        <div class="space-y-6 p-4 sm:p-6">
            <!-- Aperçu en direct -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-eye mr-2 text-blue-600"></i>
                    Aperçu en Direct
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
                    <?php $__currentLoopData = $currentColors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colorName => $colorValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($colorName !== 'name'): ?>
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mx-auto mb-2"
                                     style="background-color: <?php echo e($colorValue); ?>"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-300 capitalize"><?php echo e($colorName); ?></span>
                                <div class="text-xs text-gray-400 font-mono"><?php echo e($colorValue); ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Sélecteur de palette -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-swatchbook text-blue-600"></i>
                    Choisir une Palette
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="paletteGrid">
                        <?php $__currentLoopData = config('colors.palettes'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paletteKey => $palette): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="palette-card cursor-pointer group border-2 rounded-lg p-4 transition-all hover:shadow-md <?php echo e(($activePaletteName ?? 'default') === $paletteKey ? 'border-blue-500 bg-blue-50' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:border-gray-600'); ?>"
                                 data-palette="<?php echo e($paletteKey); ?>"
                                 onclick="changePalette('<?php echo e($paletteKey); ?>')">
                                <!-- Nom de la palette -->
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3"><?php echo e($palette['name']); ?></h4>
                                
                                <!-- Aperçu des couleurs -->
                                <div class="flex gap-1 mb-3">
                                    <div class="w-6 h-6 rounded-sm border border-gray-200 dark:border-gray-700" style="background-color: <?php echo e($palette['primary']); ?>"></div>
                                    <div class="w-6 h-6 rounded-sm border border-gray-200 dark:border-gray-700" style="background-color: <?php echo e($palette['secondary']); ?>"></div>
                                    <div class="w-6 h-6 rounded-sm border border-gray-200 dark:border-gray-700" style="background-color: <?php echo e($palette['success']); ?>"></div>
                                    <div class="w-6 h-6 rounded-sm border border-gray-200 dark:border-gray-700" style="background-color: <?php echo e($palette['danger']); ?>"></div>
                                    <div class="w-6 h-6 rounded-sm border border-gray-200 dark:border-gray-700" style="background-color: <?php echo e($palette['accent']); ?>"></div>
                                </div>
                                
                                <!-- Badge active -->
                                <div class="active-badge <?php echo e(($activePaletteName ?? 'default') === $paletteKey ? '' : 'hidden'); ?> flex items-center gap-1 text-xs text-blue-600">
                                    <i class="fas fa-check-circle"></i>
                                    Active
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <div id="processingMessage" class="hidden text-sm text-gray-600 dark:text-gray-300 flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Application de la palette en cours...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Palette personnalisée -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-brush text-purple-600"></i>
                        Créer une Palette Personnalisée
                    </h3>
                    <button onclick="toggleCustomPalette()" 
                            class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        <i class="fas fa-plus"></i> 
                        <span>Nouveau</span>
                    </button>
                </div>

                <div id="customPaletteForm" class="hidden">
                    <form action="<?php echo e(route('admin.settings.colors.custom')); ?>" method="POST" class="space-y-4" onsubmit="return validateCustomPalette()">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="palette_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                    Nom de la palette <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="palette_name" 
                                       placeholder="Ex: Ma palette"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-9 gap-4">
                            <?php $__currentLoopData = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark', 'accent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colorName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="text-center">
                                    <label for="color_<?php echo e($colorName); ?>" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 capitalize">
                                        <?php echo e($colorName); ?> <span class="text-red-500">*</span>
                                    </label>
                                    <input type="color" 
                                           name="<?php echo e($colorName); ?>" 
                                           id="color_<?php echo e($colorName); ?>"
                                           value="<?php echo e($currentColors[$colorName] ?? '#000000'); ?>"
                                           required
                                           class="w-full h-12 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                                    <small class="text-xs text-gray-500 font-mono" id="value_<?php echo e($colorName); ?>"><?php echo e($currentColors[$colorName] ?? '#000000'); ?></small>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="toggleCustomPalette()"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-save mr-1"></i>
                                Créer la Palette
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Palettes personnalisées existantes -->
                <div class="mt-6">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">Palettes Personnalisées</h4>
                    <div id="customPalettesList" class="space-y-2">
                        <!-- Les palettes personnalisées seront chargées ici via JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Paramètres avancés -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <button onclick="toggleAdvancedSettings()" 
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-cog text-gray-600 dark:text-gray-300"></i>
                        Paramètres Avancés
                    </h3>
                    <i class="fas fa-chevron-down transition-transform duration-300" id="advancedSettingsChevron"></i>
                </button>
                
                <div id="advancedSettingsContent" class="hidden border-t border-gray-200 dark:border-gray-700 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Mode Sombre -->
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <i class="fas fa-moon text-indigo-600"></i>
                                Mode Sombre
                            </h4>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer">
                                    <input type="checkbox" id="darkModeToggle" 
                                           onchange="toggleDarkMode()"
                                           class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Activer le mode sombre
                                    </span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer">
                                    <input type="checkbox" id="autoDarkMode" 
                                           onchange="toggleAutoDarkMode()"
                                           class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Basculement automatique (19h-7h)
                                    </span>
                                </label>
                                <div id="darkModeStatus" class="mt-2 text-xs text-gray-500 dark:text-gray-400 pl-3">
                                    <!-- Status sera affiché ici -->
                                </div>
                            </div>
                        </div>

                        <!-- Mode Jour/Nuit Automatique Multi-Palettes -->
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <i class="fas fa-sun text-yellow-500"></i>
                                <i class="fas fa-moon text-indigo-400 -ml-1"></i>
                                Mode Jour / Nuit Automatique
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                Change automatiquement les couleurs selon l'heure. Choisissez une palette pour le jour et une pour la nuit.
                            </p>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 bg-gradient-to-r from-yellow-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700 rounded-lg hover:from-yellow-100 hover:to-indigo-100 dark:hover:from-gray-600 dark:hover:to-gray-600 transition-colors cursor-pointer">
                                    <input type="checkbox" id="dayNightToggle" 
                                           onchange="toggleDayNightMode()"
                                           <?php echo e(config('colors.day_night.enabled', false) ? 'checked' : ''); ?>

                                           class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Activer le mode jour/nuit automatique
                                    </span>
                                </label>

                                <div id="dayNightSettings" class="<?php echo e(config('colors.day_night.enabled', false) ? '' : 'hidden'); ?>">
                                    <!-- Aperçu mode actuel -->
                                    <div id="dayNightPreview" class="p-4 rounded-lg border-2 mb-3 transition-all duration-300">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span id="dayNightCurrentIcon" class="text-2xl"></span>
                                                <span id="dayNightCurrentLabel" class="ml-2 font-semibold text-sm"></span>
                                            </div>
                                            <span id="dayNightNextSwitch" class="text-xs text-gray-500 dark:text-gray-400"></span>
                                        </div>
                                    </div>

                                    <!-- Horaires -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-3 bg-yellow-50 dark:bg-gray-700 rounded-lg">
                                            <label class="block text-xs font-medium text-yellow-700 dark:text-yellow-300 mb-1">
                                                <i class="fas fa-sun mr-1"></i> Début du jour
                                            </label>
                                            <select id="dayStartHour" onchange="updateDayNightSchedule()" 
                                                    class="w-full text-sm border-yellow-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-md">
                                                <?php for($h = 4; $h <= 10; $h++): ?>
                                                    <option value="<?php echo e($h); ?>" <?php echo e(config('colors.day_night.day_start', 7) == $h ? 'selected' : ''); ?>>
                                                        <?php echo e(str_pad($h, 2, '0', STR_PAD_LEFT)); ?>:00
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="p-3 bg-indigo-50 dark:bg-gray-700 rounded-lg">
                                            <label class="block text-xs font-medium text-indigo-700 dark:text-indigo-300 mb-1">
                                                <i class="fas fa-moon mr-1"></i> Début de la nuit
                                            </label>
                                            <select id="nightStartHour" onchange="updateDayNightSchedule()" 
                                                    class="w-full text-sm border-indigo-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-md">
                                                <?php for($h = 17; $h <= 22; $h++): ?>
                                                    <option value="<?php echo e($h); ?>" <?php echo e(config('colors.day_night.night_start', 19) == $h ? 'selected' : ''); ?>>
                                                        <?php echo e(str_pad($h, 2, '0', STR_PAD_LEFT)); ?>:00
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- ============================================ -->
                                    <!-- SÉLECTEUR DE PALETTES JOUR                   -->
                                    <!-- ============================================ -->
                                    <?php
                                        // Fallback si les variables n'ont pas été passées par le contrôleur
                                        if (!isset($dayPalettes)) {
                                            $dayNightSvc = app(\App\Services\DayNightService::class);
                                            $dayPalettes = config('colors.day_night.day_palettes', []);
                                            $nightPalettes = config('colors.day_night.night_palettes', []);
                                            $activeDayKey = $dayNightSvc->getActiveDayKey();
                                            $activeNightKey = $dayNightSvc->getActiveNightKey();
                                        }
                                    ?>
                                    
                                    <div class="mt-4">
                                        <h5 class="text-sm font-semibold text-yellow-700 dark:text-yellow-300 mb-2 flex items-center gap-1">
                                            <i class="fas fa-sun"></i> Palettes de Jour
                                        </h5>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="dayPaletteGrid">
                                            <?php $__currentLoopData = $dayPalettes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $palette): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <button type="button" 
                                                        onclick="selectDayPalette('<?php echo e($key); ?>')" 
                                                        id="dayPalette_<?php echo e($key); ?>"
                                                        class="palette-card p-3 rounded-lg border-2 text-left transition-all duration-200 hover:shadow-md <?php echo e($activeDayKey === $key ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 ring-2 ring-yellow-300' : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-yellow-300'); ?>">
                                                    <div class="text-xs font-semibold mb-1.5 truncate <?php echo e($activeDayKey === $key ? 'text-yellow-700 dark:text-yellow-300' : 'text-gray-700 dark:text-gray-300'); ?>">
                                                        <?php echo e($palette['name'] ?? $key); ?>

                                                    </div>
                                                    <div class="flex flex-wrap gap-0.5">
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['primary']); ?>" title="Primary"></div>
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['accent']); ?>" title="Accent"></div>
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['success']); ?>" title="Success"></div>
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['danger']); ?>" title="Danger"></div>
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['warning']); ?>" title="Warning"></div>
                                                        <div class="w-5 h-5 rounded-sm border border-gray-200" style="background-color: <?php echo e($palette['background']); ?>" title="Background"></div>
                                                    </div>
                                                    <?php if($activeDayKey === $key): ?>
                                                        <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1 font-medium">
                                                            <i class="fas fa-check-circle"></i> Active
                                                        </div>
                                                    <?php endif; ?>
                                                </button>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>

                                    <!-- ============================================ -->
                                    <!-- SÉLECTEUR DE PALETTES NUIT                   -->
                                    <!-- ============================================ -->
                                    <div class="mt-4">
                                        <h5 class="text-sm font-semibold text-indigo-700 dark:text-indigo-300 mb-2 flex items-center gap-1">
                                            <i class="fas fa-moon"></i> Palettes de Nuit
                                        </h5>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="nightPaletteGrid">
                                            <?php $__currentLoopData = $nightPalettes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $palette): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <button type="button" 
                                                        onclick="selectNightPalette('<?php echo e($key); ?>')" 
                                                        id="nightPalette_<?php echo e($key); ?>"
                                                        class="palette-card p-3 rounded-lg border-2 text-left transition-all duration-200 hover:shadow-md <?php echo e($activeNightKey === $key ? 'border-indigo-500 bg-indigo-900/20 ring-2 ring-indigo-400' : 'border-gray-600 bg-gray-800 hover:border-indigo-400'); ?>">
                                                    <div class="text-xs font-semibold mb-1.5 truncate <?php echo e($activeNightKey === $key ? 'text-indigo-300' : 'text-gray-300'); ?>">
                                                        <?php echo e($palette['name'] ?? $key); ?>

                                                    </div>
                                                    <div class="flex flex-wrap gap-0.5">
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['primary']); ?>" title="Primary"></div>
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['accent']); ?>" title="Accent"></div>
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['success']); ?>" title="Success"></div>
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['danger']); ?>" title="Danger"></div>
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['warning']); ?>" title="Warning"></div>
                                                        <div class="w-5 h-5 rounded-sm" style="background-color: <?php echo e($palette['background']); ?>" title="Background"></div>
                                                    </div>
                                                    <?php if($activeNightKey === $key): ?>
                                                        <div class="text-xs text-indigo-400 mt-1 font-medium">
                                                            <i class="fas fa-check-circle"></i> Active
                                                        </div>
                                                    <?php endif; ?>
                                                </button>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>

                                    <!-- Boutons de test -->
                                    <div class="flex gap-2 mt-3">
                                        <button onclick="testDayMode()" class="flex-1 px-3 py-2 text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 rounded-lg transition-colors">
                                            <i class="fas fa-sun mr-1"></i> Tester Jour
                                        </button>
                                        <button onclick="testNightMode()" class="flex-1 px-3 py-2 text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 rounded-lg transition-colors">
                                            <i class="fas fa-moon mr-1"></i> Tester Nuit
                                        </button>
                                        <button onclick="resetDayNightAuto()" class="flex-1 px-3 py-2 text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg transition-colors">
                                            <i class="fas fa-sync mr-1"></i> Auto
                                        </button>
                                    </div>
                                </div>

                                <div id="dayNightStatus" class="text-xs text-gray-500 dark:text-gray-400 pl-3">
                                    <!-- Status sera affiché dynamiquement -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Couleurs par Rôle -->
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <i class="fas fa-users-cog text-purple-600"></i>
                                Couleurs par Rôle
                            </h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                                     onclick="showRoleColorPicker('admin', '#DC2626')">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full border-2 border-gray-300 dark:border-gray-600 shadow-sm" 
                                             style="background-color: #DC2626" id="roleColor_admin"></div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Admin</span>
                                    </div>
                                    <span class="text-xs text-gray-400 font-mono">#DC2626</span>
                                </div>
                                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                                     onclick="showRoleColorPicker('expert', '#7C3AED')">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full border-2 border-gray-300 dark:border-gray-600 shadow-sm" 
                                             style="background-color: #7C3AED" id="roleColor_expert"></div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Expert</span>
                                    </div>
                                    <span class="text-xs text-gray-400 font-mono">#7C3AED</span>
                                </div>
                                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                                     onclick="showRoleColorPicker('seller', '#059669')">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full border-2 border-gray-300 dark:border-gray-600 shadow-sm" 
                                             style="background-color: #059669" id="roleColor_seller"></div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Vendeur</span>
                                    </div>
                                    <span class="text-xs text-gray-400 font-mono">#059669</span>
                                </div>
                                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                                     onclick="showRoleColorPicker('buyer', '#2563EB')">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full border-2 border-gray-300 dark:border-gray-600 shadow-sm" 
                                             style="background-color: #2563EB" id="roleColor_buyer"></div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Acheteur</span>
                                    </div>
                                    <span class="text-xs text-gray-400 font-mono">#2563EB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                        <button onclick="resetAdvancedSettings()" 
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-undo mr-1"></i>
                            Réinitialiser
                        </button>
                        <button onclick="saveAdvancedSettings()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-1"></i>
                            Enregistrer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'import -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border max-w-md shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Importer Configuration</h3>
            
            <form action="<?php echo e(route('admin.settings.colors.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label for="config_file" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Fichier de configuration (JSON)
                    </label>
                    <input type="file" name="config_file" id="config_file" accept=".json"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeImportModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-upload mr-1"></i>
                        Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleCustomPalette() {
    const form = document.getElementById('customPaletteForm');
    form.classList.toggle('hidden');
}

function validateCustomPalette() {
    const paletteName = document.getElementById('palette_name').value.trim();
    
    if (!paletteName) {
        showToast('error', 'Veuillez saisir un nom pour la palette');
        return false;
    }
    
    // Vérifier que toutes les couleurs sont définies
    const colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark', 'accent'];
    for (const color of colors) {
        const input = document.getElementById(`color_${color}`);
        if (!input || !input.value) {
            showToast('error', `La couleur "${color}" est requise`);
            return false;
        }
    }
    
    return true;
}

// Mettre à jour l'affichage de la valeur hex quand on change la couleur
document.addEventListener('DOMContentLoaded', function() {
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        input.addEventListener('input', function() {
            const colorName = this.id.replace('color_', '');
            const valueDisplay = document.getElementById(`value_${colorName}`);
            if (valueDisplay) {
                valueDisplay.textContent = this.value.toUpperCase();
            }
        });
    });
});

function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}

// Charger les palettes personnalisées
function loadCustomPalettes() {
    const container = document.getElementById('customPalettesList');
    container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">Chargement...</p>';
    
    fetch('<?php echo e(route("admin.settings.colors.custom.list")); ?>', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.palettes && Object.keys(data.palettes).length > 0) {
            let html = '';
            Object.entries(data.palettes).forEach(([key, palette]) => {
                html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="flex gap-1">
                                ${['primary', 'secondary', 'accent'].map(color => 
                                    `<div class="w-6 h-6 rounded" style="background-color: ${palette[color]}"></div>`
                                ).join('')}
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">${palette.name}</span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="changePalette('${key}')" 
                                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                Appliquer
                            </button>
                            <button onclick="deleteCustomPalette('${key}')" 
                                    class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">Aucune palette personnalisée trouvée.</p>';
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        container.innerHTML = '<p class="text-sm text-red-500">Erreur lors du chargement des palettes.</p>';
    });
}

// Supprimer une palette personnalisée
function deleteCustomPalette(paletteKey) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette palette ?')) {
        return;
    }
    
    fetch(`/admin/settings/colors/custom/${paletteKey}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            loadCustomPalettes();
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('error', 'Erreur lors de la suppression.');
    });
}

// Fonction pour changer de palette avec AJAX
function changePalette(paletteName) {
    // Afficher le message de traitement
    const processingMsg = document.getElementById('processingMessage');
    if (processingMsg) {
        processingMsg.classList.remove('hidden');
    }

    // Désactiver tous les clics pendant le traitement
    const paletteCards = document.querySelectorAll('.palette-card');
    paletteCards.forEach(card => card.style.pointerEvents = 'none');

    // Envoyer la requête AJAX
    fetch('<?php echo e(route("admin.settings.colors.update")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: new URLSearchParams({
            palette: paletteName
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Masquer le message de traitement
        if (processingMsg) {
            processingMsg.classList.add('hidden');
        }

        // Réactiver les clics
        paletteCards.forEach(card => card.style.pointerEvents = '');

        if (data.success || data.message) {
            // Mettre à jour l'interface
            updateActivePalette(paletteName);
            
            // Afficher un message de succès
            showToast('success', data.message || 'Palette changée avec succès ! Actualisation en cours...');
            
            // Recharger la page après un court délai pour voir les nouvelles couleurs
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('error', data.error || 'Erreur lors du changement de palette');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        
        // Masquer le message de traitement
        if (processingMsg) {
            processingMsg.classList.add('hidden');
        }

        // Réactiver les clics
        paletteCards.forEach(card => card.style.pointerEvents = '');
        
        showToast('error', 'Erreur de connexion. Veuillez réessayer.');
    });
}

// Mettre à jour visuellement la palette active
function updateActivePalette(newPalette) {
    // Retirer les styles actifs de toutes les cartes
    document.querySelectorAll('.palette-card').forEach(card => {
        card.classList.remove('border-blue-500', 'bg-blue-50');
        card.classList.add('border-gray-200');
        card.classList.add('dark:border-gray-700');
        card.querySelector('.active-badge').classList.add('hidden');
    });

    // Ajouter les styles actifs à la carte sélectionnée
    const activeCard = document.querySelector(`[data-palette="${newPalette}"]`);
    if (activeCard) {
        activeCard.classList.remove('border-gray-200');
        activeCard.classList.remove('dark:border-gray-700');
        activeCard.classList.add('border-blue-500', 'bg-blue-50');
        activeCard.querySelector('.active-badge').classList.remove('hidden');
    }
}

// Afficher un toast de notification
function showToast(type, message) {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas ${icon} text-xl"></i>
            <span class="font-medium">${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Animation de sortie et suppression
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Initialiser la page
document.addEventListener('DOMContentLoaded', function() {
    loadCustomPalettes();
    loadAdvancedSettings();
});

// Toggle des paramètres avancés
function toggleAdvancedSettings() {
    const content = document.getElementById('advancedSettingsContent');
    const chevron = document.getElementById('advancedSettingsChevron');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
    }
}

// Charger les paramètres avancés
function loadAdvancedSettings() {
    // Charger le mode sombre depuis localStorage
    const darkMode = localStorage.getItem('vintapp_dark_mode') === 'true';
    const autoDarkMode = localStorage.getItem('vintapp_auto_dark_mode') === 'true';
    
    document.getElementById('darkModeToggle').checked = darkMode;
    document.getElementById('autoDarkMode').checked = autoDarkMode;
    
    updateDarkModeStatus();
}

// Toggle du mode sombre
function toggleDarkMode() {
    const isEnabled = document.getElementById('darkModeToggle').checked;
    localStorage.setItem('vintapp_dark_mode', isEnabled);
    
    if (isEnabled) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    
    updateDarkModeStatus();
    showToast('success', 'Mode sombre ' + (isEnabled ? 'activé' : 'désactivé'));
}

// Toggle du mode sombre automatique
function toggleAutoDarkMode() {
    const isEnabled = document.getElementById('autoDarkMode').checked;
    localStorage.setItem('vintapp_auto_dark_mode', isEnabled);
    
    if (isEnabled) {
        applyAutoDarkMode();
    }
    
    updateDarkModeStatus();
    showToast('success', 'Basculement automatique ' + (isEnabled ? 'activé' : 'désactivé'));
}

// Appliquer le mode sombre automatique
function applyAutoDarkMode() {
    const hour = new Date().getHours();
    const shouldBeDark = hour >= 19 || hour < 7;
    
    if (shouldBeDark) {
        document.documentElement.classList.add('dark');
        document.getElementById('darkModeToggle').checked = true;
    } else {
        document.documentElement.classList.remove('dark');
        document.getElementById('darkModeToggle').checked = false;
    }
}

// Mettre à jour le statut du mode sombre
function updateDarkModeStatus() {
    const statusDiv = document.getElementById('darkModeStatus');
    const darkMode = document.getElementById('darkModeToggle').checked;
    const autoDarkMode = document.getElementById('autoDarkMode').checked;
    
    let statusText = '';
    if (autoDarkMode) {
        const hour = new Date().getHours();
        const shouldBeDark = hour >= 19 || hour < 7;
        statusText = `<i class="fas fa-info-circle mr-1"></i> Mode automatique actif ${shouldBeDark ? '(actuellement sombre)' : '(actuellement clair)'}`;
    } else if (darkMode) {
        statusText = '<i class="fas fa-moon mr-1"></i> Mode sombre activé manuellement';
    } else {
        statusText = '<i class="fas fa-sun mr-1"></i> Mode clair activé';
    }
    
    statusDiv.innerHTML = statusText;
}

// Afficher le sélecteur de couleur pour un rôle
function showRoleColorPicker(role, currentColor) {
    const newColor = prompt(`Choisir une couleur pour le rôle "${role}" (format hex):`, currentColor);
    
    if (newColor && /^#[0-9A-Fa-f]{6}$/.test(newColor)) {
        const roleElement = document.getElementById(`roleColor_${role}`);
        if (roleElement) {
            roleElement.style.backgroundColor = newColor;
            roleElement.nextElementSibling.nextElementSibling.textContent = newColor.toUpperCase();
            showToast('success', `Couleur du rôle "${role}" mise à jour`);
        }
    } else if (newColor) {
        showToast('error', 'Format de couleur invalide. Utilisez le format #RRGGBB');
    }
}

// Sauvegarder les paramètres avancés
function saveAdvancedSettings() {
    const darkMode = document.getElementById('darkModeToggle').checked;
    const autoDarkMode = document.getElementById('autoDarkMode').checked;
    
    // Récupérer les couleurs des rôles
    const roleColors = {
        admin: document.getElementById('roleColor_admin').style.backgroundColor,
        expert: document.getElementById('roleColor_expert').style.backgroundColor,
        seller: document.getElementById('roleColor_seller').style.backgroundColor,
        buyer: document.getElementById('roleColor_buyer').style.backgroundColor
    };
    
    // Sauvegarder dans localStorage
    localStorage.setItem('vintapp_dark_mode', darkMode);
    localStorage.setItem('vintapp_auto_dark_mode', autoDarkMode);
    localStorage.setItem('vintapp_role_colors', JSON.stringify(roleColors));
    
    // Envoyer au serveur via AJAX
    fetch('/admin/settings/advanced', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            dark_mode: darkMode,
            auto_dark_mode: autoDarkMode,
            role_colors: roleColors
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Paramètres avancés enregistrés avec succès !');
        } else {
            showToast('error', 'Erreur lors de l\'enregistrement');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('success', 'Paramètres enregistrés localement');
    });
}

// Réinitialiser les paramètres avancés
function resetAdvancedSettings() {
    if (!confirm('Voulez-vous vraiment réinitialiser tous les paramètres avancés ?')) {
        return;
    }
    
    // Réinitialiser les valeurs
    document.getElementById('darkModeToggle').checked = false;
    document.getElementById('autoDarkMode').checked = true;
    
    // Réinitialiser les couleurs des rôles
    document.getElementById('roleColor_admin').style.backgroundColor = '#DC2626';
    document.getElementById('roleColor_expert').style.backgroundColor = '#7C3AED';
    document.getElementById('roleColor_seller').style.backgroundColor = '#059669';
    document.getElementById('roleColor_buyer').style.backgroundColor = '#2563EB';
    
    // Supprimer du localStorage
    localStorage.removeItem('vintapp_dark_mode');
    localStorage.removeItem('vintapp_auto_dark_mode');
    localStorage.removeItem('vintapp_role_colors');
    
    // Appliquer
    document.documentElement.classList.remove('dark');
    updateDarkModeStatus();
    
    showToast('success', 'Paramètres réinitialisés aux valeurs par défaut');
}

// Fermer les modals en cliquant à l'extérieur
document.addEventListener('click', function(e) {
    const importModal = document.getElementById('importModal');
    if (e.target === importModal) {
        closeImportModal();
    }
});

// ============================================
// MODE JOUR / NUIT MULTI-PALETTES
// ============================================

// État local des palettes sélectionnées
let selectedDayPalette = '<?php echo e($activeDayKey ?? "ciel"); ?>';
let selectedNightPalette = '<?php echo e($activeNightKey ?? "indigo"); ?>';

// Toggle du mode jour/nuit
function toggleDayNightMode() {
    const isEnabled = document.getElementById('dayNightToggle').checked;
    const settingsDiv = document.getElementById('dayNightSettings');
    
    if (isEnabled) {
        settingsDiv.classList.remove('hidden');
        if (window.VintAppDayNight) {
            window.VintAppDayNight.enabled = true;
        }
        updateDayNightPreview();
    } else {
        settingsDiv.classList.add('hidden');
        if (window.VintAppDayNight) {
            window.VintAppDayNight.enabled = false;
        }
        document.documentElement.setAttribute('data-theme', 'day');
        document.documentElement.classList.remove('dark');
    }
    
    saveDayNightSettings(isEnabled);
    updateDayNightStatus();
    showToast('success', 'Mode jour/nuit ' + (isEnabled ? 'activé' : 'désactivé'));
}

// Sélectionner une palette de jour
function selectDayPalette(key) {
    selectedDayPalette = key;
    
    // Mettre à jour le visuel des cartes
    document.querySelectorAll('#dayPaletteGrid button').forEach(function(btn) {
        btn.classList.remove('border-yellow-500', 'bg-yellow-50', 'dark:bg-yellow-900/20', 'ring-2', 'ring-yellow-300');
        btn.classList.add('border-gray-200', 'dark:border-gray-600', 'bg-white', 'dark:bg-gray-800');
        
        // Retirer l'indicateur "Active"
        const activeIndicator = btn.querySelector('.active-indicator');
        if (activeIndicator) activeIndicator.remove();
        
        // Remettre la couleur de titre par défaut
        const title = btn.querySelector('div:first-child');
        if (title) {
            title.classList.remove('text-yellow-700', 'dark:text-yellow-300');
            title.classList.add('text-gray-700', 'dark:text-gray-300');
        }
    });
    
    // Activer la carte sélectionnée
    const selectedCard = document.getElementById('dayPalette_' + key);
    if (selectedCard) {
        selectedCard.classList.remove('border-gray-200', 'dark:border-gray-600', 'bg-white', 'dark:bg-gray-800');
        selectedCard.classList.add('border-yellow-500', 'bg-yellow-50', 'dark:bg-yellow-900/20', 'ring-2', 'ring-yellow-300');
        
        const title = selectedCard.querySelector('div:first-child');
        if (title) {
            title.classList.remove('text-gray-700', 'dark:text-gray-300');
            title.classList.add('text-yellow-700', 'dark:text-yellow-300');
        }
        
        // Ajouter indicateur
        if (!selectedCard.querySelector('.active-indicator')) {
            const indicator = document.createElement('div');
            indicator.className = 'active-indicator text-xs text-yellow-600 dark:text-yellow-400 mt-1 font-medium';
            indicator.innerHTML = '<i class="fas fa-check-circle"></i> Active';
            selectedCard.appendChild(indicator);
        }
    }
    
    // Appliquer la palette via le JS dynamique
    if (window.VintAppDayNight && window.VintAppDayNight.setDayPalette) {
        window.VintAppDayNight.setDayPalette(key);
    }
    
    // Sauvegarder côté serveur
    saveDayNightSettings(document.getElementById('dayNightToggle').checked);
    showToast('success', '☀️ Palette jour changée : ' + key);
}

// Sélectionner une palette de nuit
function selectNightPalette(key) {
    selectedNightPalette = key;
    
    // Mettre à jour le visuel des cartes
    document.querySelectorAll('#nightPaletteGrid button').forEach(function(btn) {
        btn.classList.remove('border-indigo-500', 'bg-indigo-900/20', 'ring-2', 'ring-indigo-400');
        btn.classList.add('border-gray-600', 'bg-gray-800');
        
        const activeIndicator = btn.querySelector('.active-indicator');
        if (activeIndicator) activeIndicator.remove();
        
        const title = btn.querySelector('div:first-child');
        if (title) {
            title.classList.remove('text-indigo-300');
            title.classList.add('text-gray-300');
        }
    });
    
    const selectedCard = document.getElementById('nightPalette_' + key);
    if (selectedCard) {
        selectedCard.classList.remove('border-gray-600', 'bg-gray-800');
        selectedCard.classList.add('border-indigo-500', 'bg-indigo-900/20', 'ring-2', 'ring-indigo-400');
        
        const title = selectedCard.querySelector('div:first-child');
        if (title) {
            title.classList.remove('text-gray-300');
            title.classList.add('text-indigo-300');
        }
        
        if (!selectedCard.querySelector('.active-indicator')) {
            const indicator = document.createElement('div');
            indicator.className = 'active-indicator text-xs text-indigo-400 mt-1 font-medium';
            indicator.innerHTML = '<i class="fas fa-check-circle"></i> Active';
            selectedCard.appendChild(indicator);
        }
    }
    
    if (window.VintAppDayNight && window.VintAppDayNight.setNightPalette) {
        window.VintAppDayNight.setNightPalette(key);
    }
    
    saveDayNightSettings(document.getElementById('dayNightToggle').checked);
    showToast('success', '🌙 Palette nuit changée : ' + key);
}

// Mettre à jour l'aperçu du mode actuel
function updateDayNightPreview() {
    const preview = document.getElementById('dayNightPreview');
    const iconEl = document.getElementById('dayNightCurrentIcon');
    const labelEl = document.getElementById('dayNightCurrentLabel');
    const nextEl = document.getElementById('dayNightNextSwitch');
    
    if (!preview) return;
    
    const hour = new Date().getHours();
    const dayStart = parseInt(document.getElementById('dayStartHour')?.value || 7);
    const nightStart = parseInt(document.getElementById('nightStartHour')?.value || 19);
    const isDay = hour >= dayStart && hour < nightStart;
    
    if (isDay) {
        preview.className = 'p-4 rounded-lg border-2 mb-3 transition-all duration-300 bg-gradient-to-r from-yellow-50 to-orange-50 border-yellow-200';
        iconEl.textContent = '☀️';
        labelEl.textContent = 'Mode Jour — ' + selectedDayPalette;
        labelEl.className = 'ml-2 font-semibold text-sm text-yellow-800';
        const nextSwitch = nightStart > hour ? nightStart + ':00' : 'demain';
        nextEl.textContent = 'Nuit à ' + nextSwitch;
    } else {
        preview.className = 'p-4 rounded-lg border-2 mb-3 transition-all duration-300 bg-gradient-to-r from-indigo-900 to-purple-900 border-indigo-500/30';
        iconEl.textContent = '🌙';
        labelEl.textContent = 'Mode Nuit — ' + selectedNightPalette;
        labelEl.className = 'ml-2 font-semibold text-sm text-indigo-200';
        const nextSwitch = dayStart > hour ? dayStart + ':00' : 'demain ' + dayStart + ':00';
        nextEl.textContent = 'Jour à ' + nextSwitch;
        nextEl.className = 'text-xs text-indigo-300';
    }
}

// Mettre à jour les horaires
function updateDayNightSchedule() {
    const dayStart = parseInt(document.getElementById('dayStartHour').value);
    const nightStart = parseInt(document.getElementById('nightStartHour').value);
    
    if (window.VintAppDayNight) {
        window.VintAppDayNight.setDayStart(dayStart);
        window.VintAppDayNight.setNightStart(nightStart);
    }

    localStorage.setItem('vintapp_day_start', dayStart);
    localStorage.setItem('vintapp_night_start', nightStart);
    
    updateDayNightPreview();
    updateDayNightStatus();
    saveDayNightSettings(document.getElementById('dayNightToggle').checked);
    showToast('info', 'Horaires mis à jour : jour à ' + dayStart + 'h, nuit à ' + nightStart + 'h');
}

// Tester le mode jour
function testDayMode() {
    if (window.VintAppDayNight && window.VintAppDayNight.setDayPalette) {
        window.VintAppDayNight.setDayPalette(selectedDayPalette);
    }
    document.documentElement.setAttribute('data-theme', 'day');
    document.documentElement.classList.remove('dark');
    showToast('info', '☀️ Aperçu du mode jour — ' + selectedDayPalette);
}

// Tester le mode nuit
function testNightMode() {
    if (window.VintAppDayNight && window.VintAppDayNight.setNightPalette) {
        window.VintAppDayNight.setNightPalette(selectedNightPalette);
    }
    document.documentElement.setAttribute('data-theme', 'night');
    document.documentElement.classList.add('dark');
    showToast('info', '🌙 Aperçu du mode nuit — ' + selectedNightPalette);
}

// Réinitialiser au mode automatique
function resetDayNightAuto() {
    localStorage.removeItem('vintapp_day_night_manual');
    if (window.VintAppDayNight && window.VintAppDayNight.resetAuto) {
        window.VintAppDayNight.resetAuto();
    } else {
        const hour = new Date().getHours();
        const dayStart = parseInt(document.getElementById('dayStartHour')?.value || 7);
        const nightStart = parseInt(document.getElementById('nightStartHour')?.value || 19);
        const isDay = hour >= dayStart && hour < nightStart;
        
        document.documentElement.setAttribute('data-theme', isDay ? 'day' : 'night');
        if (!isDay) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
    updateDayNightPreview();
    showToast('success', '🔄 Mode automatique réactivé');
}

// Mettre à jour le statut jour/nuit
function updateDayNightStatus() {
    const statusDiv = document.getElementById('dayNightStatus');
    if (!statusDiv) return;
    
    const isEnabled = document.getElementById('dayNightToggle')?.checked;
    
    if (!isEnabled) {
        statusDiv.innerHTML = '<i class="fas fa-info-circle mr-1"></i> Mode jour/nuit désactivé';
        return;
    }
    
    const hour = new Date().getHours();
    const dayStart = parseInt(document.getElementById('dayStartHour')?.value || 7);
    const nightStart = parseInt(document.getElementById('nightStartHour')?.value || 19);
    const isDay = hour >= dayStart && hour < nightStart;
    const manual = localStorage.getItem('vintapp_day_night_manual');
    
    let statusText = '';
    if (manual) {
        statusText = '<i class="fas fa-hand-pointer mr-1"></i> Mode ' + (manual === 'day' ? 'jour (' + selectedDayPalette + ')' : 'nuit (' + selectedNightPalette + ')') + ' forcé manuellement';
    } else if (isDay) {
        statusText = '<i class="fas fa-sun mr-1 text-yellow-500"></i> Jour actif — palette: <strong>' + selectedDayPalette + '</strong> (' + hour + 'h) — nuit à ' + nightStart + 'h';
    } else {
        statusText = '<i class="fas fa-moon mr-1 text-indigo-400"></i> Nuit active — palette: <strong>' + selectedNightPalette + '</strong> (' + hour + 'h) — jour à ' + dayStart + 'h';
    }
    
    statusDiv.innerHTML = statusText;
}

// Sauvegarder les paramètres jour/nuit côté serveur (avec palettes)
function saveDayNightSettings(enabled) {
    const dayStart = parseInt(document.getElementById('dayStartHour')?.value || 7);
    const nightStart = parseInt(document.getElementById('nightStartHour')?.value || 19);
    
    fetch('/admin/settings/colors/day-night', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            enabled: enabled,
            day_start: dayStart,
            night_start: nightStart,
            active_day_palette: selectedDayPalette,
            active_night_palette: selectedNightPalette
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('[VintApp] Paramètres jour/nuit + palettes sauvegardés');
        }
    })
    .catch(error => {
        console.error('Erreur sauvegarde jour/nuit:', error);
    });
}

// Initialiser l'aperçu au chargement
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('dayNightToggle')?.checked) {
        updateDayNightPreview();
    }
    updateDayNightStatus();
    
    // Rafraîchir toutes les minutes
    setInterval(function() {
        if (document.getElementById('dayNightToggle')?.checked) {
            updateDayNightPreview();
            updateDayNightStatus();
        }
    }, 60000);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Mes projets\vintApp\resources\views/admin/settings/colors.blade.php ENDPATH**/ ?>