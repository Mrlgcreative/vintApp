

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
                    <form action="<?php echo e(route('admin.settings.colors.custom')); ?>" method="POST" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="palette_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                    Nom de la palette
                                </label>
                                <input type="text" name="name" id="palette_name" 
                                       placeholder="Ex: Ma palette"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-9 gap-4">
                            <?php $__currentLoopData = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark', 'accent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colorName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="text-center">
                                    <label for="color_<?php echo e($colorName); ?>" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 capitalize">
                                        <?php echo e($colorName); ?>

                                    </label>
                                    <input type="color" 
                                           name="<?php echo e($colorName); ?>" 
                                           id="color_<?php echo e($colorName); ?>"
                                           value="<?php echo e($currentColors[$colorName] ?? '#000000'); ?>"
                                           class="w-full h-12 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-cog text-gray-600 dark:text-gray-300"></i>
                    Paramètres Avancés
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3">Mode Sombre</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" id="darkModeToggle" class="w-4 h-4 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-200">Activer le mode sombre</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" id="autoDarkMode" class="w-4 h-4 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-200">Basculement automatique (19h-7h)</span>
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3">Couleurs par Rôle</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-200">Admin</span>
                                <div class="w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: #DC2626"></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-200">Expert</span>
                                <div class="w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: #7C3AED"></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-200">Vendeur</span>
                                <div class="w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: #059669"></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-200">Acheteur</span>
                                <div class="w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: #2563EB"></div>
                            </div>
                        </div>
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
});

// Fermer les modals en cliquant à l'extérieur
document.addEventListener('click', function(e) {
    const importModal = document.getElementById('importModal');
    if (e.target === importModal) {
        closeImportModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/settings/colors.blade.php ENDPATH**/ ?>