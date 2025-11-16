

<?php $__env->startSection('title', 'Gestion du Carrousel Hero'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Styles pour le slider de durée */
.slider {
    -webkit-appearance: none;
    appearance: none;
    background: #e5e7eb;
    outline: none;
    border-radius: 0.5rem;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #8b5cf6;
    cursor: pointer;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: #8b5cf6;
    cursor: pointer;
    border-radius: 50%;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.slider::-webkit-slider-track {
    background: linear-gradient(to right, #8b5cf6 0%, #8b5cf6 calc((6 - 3) / (15 - 3) * 100%), #e5e7eb calc((6 - 3) / (15 - 3) * 100%), #e5e7eb 100%);
}
</style>
<?php $__env->stopPush(); ?>

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
                <i class="fas fa-images mr-2 sm:mr-3 text-gray-600 dark:text-gray-300 text-base sm:text-xl"></i>
                Carrousel Hero - Page d'Accueil
            </h1>
            <button onclick="showAddModal()" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Ajouter une Slide
            </button>
        </div>
        
        <div class="p-4 sm:p-6">
            <!-- Aperçu du carrousel -->
            <div class="mb-6 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-eye mr-2 text-primary-600"></i>
                    Aperçu du Carrousel
                </h3>
                <?php if($slides->where('is_active', true)->count() > 0): ?>
                    <div id="heroCarouselPreview" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php $__currentLoopData = $slides->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" data-bs-target="#heroCarouselPreview" data-bs-slide-to="<?php echo e($index); ?>" class="<?php echo e($index === 0 ? 'active' : ''); ?>"></button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="carousel-inner rounded-lg overflow-hidden" style="max-height: 500px;">
                            <?php $__currentLoopData = $slides->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="carousel-item <?php echo e($index === 0 ? 'active' : ''); ?>" style="background-color: <?php echo e($slide->background_color ?? '#6A0DAD'); ?>;">
                                    <div class="d-flex align-items-center" style="min-height: 500px; padding: 40px;">
                                        <div class="container">
                                            <div class="row align-items-center">
                                                <?php if($slide->image_position === 'left'): ?>
                                                    <!-- Image à gauche -->
                                                    <div class="col-md-6 text-center">
                                                        <img src="<?php echo e(Storage::url($slide->image_path)); ?>" 
                                                             class="img-fluid" 
                                                             style="max-height: 400px; object-fit: contain;"
                                                             alt="<?php echo e($slide->title); ?>">
                                                    </div>
                                                    <!-- Texte à droite -->
                                                    <div class="col-md-6 text-<?php echo e($slide->text_position ?? 'left'); ?>">
                                                        <h2 class="text-white fw-bold mb-3" style="font-size: 2.5rem;"><?php echo e($slide->title); ?></h2>
                                                        <?php if($slide->subtitle): ?>
                                                            <p class="text-white mb-4" style="font-size: 1.2rem;"><?php echo e($slide->subtitle); ?></p>
                                                        <?php endif; ?>
                                                        <div class="d-flex gap-3 <?php echo e($slide->text_position === 'center' ? 'justify-content-center' : ($slide->text_position === 'right' ? 'justify-content-end' : '')); ?>">
                                                            <?php if($slide->button_primary_text): ?>
                                                                <a href="<?php echo e($slide->button_primary_url ?? '#'); ?>" class="btn btn-light btn-lg px-4 py-2"><?php echo e($slide->button_primary_text); ?></a>
                                                            <?php endif; ?>
                                                            <?php if($slide->button_secondary_text): ?>
                                                                <a href="<?php echo e($slide->button_secondary_url ?? '#'); ?>" class="btn btn-outline-light btn-lg px-4 py-2"><?php echo e($slide->button_secondary_text); ?></a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Texte à gauche (ou position choisie) -->
                                                    <div class="col-md-6 text-<?php echo e($slide->text_position ?? 'left'); ?>">
                                                        <h2 class="text-white fw-bold mb-3" style="font-size: 2.5rem;"><?php echo e($slide->title); ?></h2>
                                                        <?php if($slide->subtitle): ?>
                                                            <p class="text-white mb-4" style="font-size: 1.2rem;"><?php echo e($slide->subtitle); ?></p>
                                                        <?php endif; ?>
                                                        <div class="d-flex gap-3 <?php echo e($slide->text_position === 'center' ? 'justify-content-center' : ($slide->text_position === 'right' ? 'justify-content-end' : '')); ?>">
                                                            <?php if($slide->button_primary_text): ?>
                                                                <a href="<?php echo e($slide->button_primary_url ?? '#'); ?>" class="btn btn-light btn-lg px-4 py-2"><?php echo e($slide->button_primary_text); ?></a>
                                                            <?php endif; ?>
                                                            <?php if($slide->button_secondary_text): ?>
                                                                <a href="<?php echo e($slide->button_secondary_url ?? '#'); ?>" class="btn btn-outline-light btn-lg px-4 py-2"><?php echo e($slide->button_secondary_text); ?></a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <!-- Image à droite -->
                                                    <div class="col-md-6 text-center">
                                                        <img src="<?php echo e(Storage::url($slide->image_path)); ?>" 
                                                             class="img-fluid" 
                                                             style="max-height: <?php echo e($slide->image_size === 'small' ? '250px' : ($slide->image_size === 'medium' ? '350px' : ($slide->image_size === 'large' ? '450px' : '100%'))); ?>; object-fit: contain;"
                                                             alt="<?php echo e($slide->title); ?>">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarouselPreview" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#heroCarouselPreview" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-image fa-3x mb-3 opacity-50"></i>
                        <p>Aucune slide active. Ajoutez et activez des slides pour voir l'aperçu.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Liste des slides -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-list mr-2 text-primary-600"></i>
                    Toutes les Slides (<?php echo e($slides->count()); ?>)
                </h3>

                <?php if($slides->count() > 0): ?>
                    <div id="slidesList" class="space-y-3">
                        <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow duration-200 slide-item" data-slide-id="<?php echo e($slide->id); ?>">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <!-- Image -->
                                    <div class="flex-shrink-0">
                                        <img src="<?php echo e(Storage::url($slide->image_path)); ?>" alt="<?php echo e($slide->title); ?>" class="w-full md:w-48 h-32 object-cover rounded-lg">
                                    </div>
                                    
                                    <!-- Contenu -->
                                    <div class="flex-grow">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($slide->title); ?></h4>
                                                <?php if($slide->subtitle): ?>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1"><?php echo e($slide->subtitle); ?></p>
                                                <?php endif; ?>
                                                <!-- Affichage de la durée -->
                                                <div class="flex items-center gap-4 mt-2">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-clock mr-1"></i><?php echo e($slide->display_duration ?? 6); ?>s
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                                        <i class="fas fa-expand-arrows-alt mr-1"></i><?php echo e(ucfirst($slide->image_size ?? 'medium')); ?>

                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <!-- Handle de glisser-déposer -->
                                                <button class="drag-handle cursor-move p-2 text-gray-400 hover:text-gray-600 dark:text-gray-300">
                                                    <i class="fas fa-grip-vertical"></i>
                                                </button>
                                                
                                                <!-- Badge statut -->
                                                <?php if($slide->is_active): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>Active
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
                                                        <i class="fas fa-eye-slash mr-1"></i>Inactive
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Boutons -->
                                        <?php if($slide->button_primary_text || $slide->button_secondary_text): ?>
                                            <div class="flex flex-wrap gap-2 mb-3">
                                                <?php if($slide->button_primary_text): ?>
                                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-primary-100 text-primary-800">
                                                        <i class="fas fa-link mr-1"></i><?php echo e($slide->button_primary_text); ?>

                                                    </span>
                                                <?php endif; ?>
                                                <?php if($slide->button_secondary_text): ?>
                                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-link mr-1"></i><?php echo e($slide->button_secondary_text); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Actions -->
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <button onclick="editSlide(<?php echo e($slide->id); ?>)" class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-md transition-colors">
                                                <i class="fas fa-edit mr-1.5"></i>Modifier
                                            </button>
                                            
                                            <form action="<?php echo e(route('admin.settings.hero-slides.toggle', $slide)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 <?php echo e($slide->is_active ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700' : 'bg-green-50 hover:bg-green-100 text-green-700'); ?> text-sm font-medium rounded-md transition-colors">
                                                    <i class="fas <?php echo e($slide->is_active ? 'fa-eye-slash' : 'fa-eye'); ?> mr-1.5"></i>
                                                    <?php echo e($slide->is_active ? 'Désactiver' : 'Activer'); ?>

                                                </button>
                                            </form>
                                            
                                            <form action="<?php echo e(route('admin.settings.hero-slides.destroy', $slide)); ?>" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette slide ?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-md transition-colors">
                                                    <i class="fas fa-trash mr-1.5"></i>Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                        <i class="fas fa-images fa-4x text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune slide</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Commencez par ajouter votre première slide pour le carrousel hero.</p>
                        <button onclick="showAddModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Ajouter une Slide
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier Slide -->
<div id="slideModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 px-4">
    <div class="relative top-10 sm:top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-xl bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-image mr-2 text-primary-600"></i>
                    <span id="modalTitle">Ajouter une Slide</span>
                </h3>
                <button onclick="hideModal()" class="text-gray-400 hover:text-gray-600 dark:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="slideForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" value="POST" id="formMethod">
                
                <!-- Titre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="slideTitle" required 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Ex: Bienvenue sur VintApp">
                </div>
                
                <!-- Sous-titre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Sous-titre
                    </label>
                    <textarea name="subtitle" id="slideSubtitle" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                              placeholder="Description courte..."></textarea>
                </div>
                
                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Image <span class="text-red-500" id="imageRequired">*</span>
                    </label>
                    <input type="file" name="image" id="slideImage" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           onchange="previewImage(this)">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formats: JPG, PNG, GIF - Max 5MB - Recommandé: 1920x1080px (PNG haute résolution)</p>
                    <div id="imagePreview" class="mt-3 hidden">
                        <img id="previewImg" src="" alt="Aperçu" class="w-full h-48 object-contain rounded-lg bg-gray-50 dark:bg-gray-900">
                    </div>
                </div>
                
                <!-- Couleur de fond personnalisable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Couleur de fond <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="background_color" id="backgroundColor" value="#6A0DAD"
                               class="h-10 w-20 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                        <input type="text" id="backgroundColorHex" value="#6A0DAD" 
                               pattern="^#[0-9A-Fa-f]{6}$"
                               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="#6A0DAD">
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Choisissez la couleur de fond du carrousel</p>
                </div>
                
                <!-- Position du texte et de l'image -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Position du texte <span class="text-red-500">*</span>
                        </label>
                        <select name="text_position" id="textPosition" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="left">À gauche</option>
                            <option value="center">Au centre</option>
                            <option value="right">À droite</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Position de l'image <span class="text-red-500">*</span>
                        </label>
                        <select name="image_position" id="imagePosition" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="right">À droite</option>
                            <option value="left">À gauche</option>
                        </select>
                    </div>
                </div>
                
                <!-- Taille de l'image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Taille de l'image <span class="text-red-500">*</span>
                    </label>
                    <select name="image_size" id="imageSize" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="small">Petite (250px)</option>
                        <option value="medium" selected>Moyenne (350px)</option>
                        <option value="large">Grande (450px)</option>
                        <option value="full">Pleine hauteur (100%)</option>
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Définit la hauteur maximale de l'image dans le carrousel</p>
                </div>
                
                <!-- Durée d'affichage -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Durée d'affichage <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="display_duration" id="displayDuration" 
                               min="3" max="15" step="1" value="6" required
                               class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-center">
                        <span class="text-sm text-gray-600 dark:text-gray-300 font-medium">secondes</span>
                        <input type="range" id="durationRange" 
                               min="3" max="15" step="1" value="6"
                               class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer slider">
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Temps d'affichage de cette slide avant passage automatique à la suivante (3-15 secondes)</p>
                </div>
                
                <!-- Bouton Principal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Texte Bouton Principal
                        </label>
                        <input type="text" name="button_primary_text" id="buttonPrimaryText"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="Ex: Commencer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            URL Bouton Principal
                        </label>
                        <input type="text" name="button_primary_url" id="buttonPrimaryUrl"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="/register">
                    </div>
                </div>
                
                <!-- Bouton Secondaire -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Texte Bouton Secondaire
                        </label>
                        <input type="text" name="button_secondary_text" id="buttonSecondaryText"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="Ex: Explorer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            URL Bouton Secondaire
                        </label>
                        <input type="text" name="button_secondary_url" id="buttonSecondaryUrl"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="/items">
                    </div>
                </div>
                
                <!-- Statut -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="slideIsActive" value="1" checked
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <label for="slideIsActive" class="ml-2 block text-sm text-gray-700 dark:text-gray-200">
                        Activer cette slide immédiatement
                    </label>
                </div>
                
                <!-- Boutons -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="hideModal()" 
                            class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitButtonText">Ajouter</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
// Variables globales
let currentSlideId = null;
let slidesData = <?php echo json_encode($slides, 15, 512) ?>;

// Initialiser Sortable.js pour le drag & drop
document.addEventListener('DOMContentLoaded', function() {
    const slidesList = document.getElementById('slidesList');
    if (slidesList) {
        new Sortable(slidesList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                updateSlidesOrder();
            }
        });
    }
});

// Afficher le modal d'ajout
function showAddModal() {
    currentSlideId = null;
    document.getElementById('modalTitle').textContent = 'Ajouter une Slide';
    document.getElementById('submitButtonText').textContent = 'Ajouter';
    document.getElementById('slideForm').action = '<?php echo e(route("admin.settings.hero-slides.store")); ?>';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('imageRequired').classList.remove('hidden');
    document.getElementById('slideImage').required = true;
    
    // Réinitialiser le formulaire
    document.getElementById('slideForm').reset();
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('backgroundColor').value = '#6A0DAD';
    document.getElementById('backgroundColorHex').value = '#6A0DAD';
    document.getElementById('textPosition').value = 'left';
    document.getElementById('imagePosition').value = 'right';
    document.getElementById('imageSize').value = 'medium';
    document.getElementById('displayDuration').value = 6;
    document.getElementById('durationRange').value = 6;
    
    document.getElementById('slideModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Afficher le modal d'édition
function editSlide(slideId) {
    currentSlideId = slideId;
    const slide = slidesData.find(s => s.id === slideId);
    
    if (!slide) return;
    
    document.getElementById('modalTitle').textContent = 'Modifier la Slide';
    document.getElementById('submitButtonText').textContent = 'Mettre à jour';
    document.getElementById('slideForm').action = `/admin/settings/hero-slides/${slideId}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('imageRequired').classList.add('hidden');
    document.getElementById('slideImage').required = false;
    
    // Remplir le formulaire
    document.getElementById('slideTitle').value = slide.title || '';
    document.getElementById('slideSubtitle').value = slide.subtitle || '';
    document.getElementById('backgroundColor').value = slide.background_color || '#6A0DAD';
    document.getElementById('backgroundColorHex').value = slide.background_color || '#6A0DAD';
    document.getElementById('textPosition').value = slide.text_position || 'left';
    document.getElementById('imagePosition').value = slide.image_position || 'right';
    document.getElementById('imageSize').value = slide.image_size || 'medium';
    document.getElementById('displayDuration').value = slide.display_duration || 6;
    document.getElementById('durationRange').value = slide.display_duration || 6;
    document.getElementById('buttonPrimaryText').value = slide.button_primary_text || '';
    document.getElementById('buttonPrimaryUrl').value = slide.button_primary_url || '';
    document.getElementById('buttonSecondaryText').value = slide.button_secondary_text || '';
    document.getElementById('buttonSecondaryUrl').value = slide.button_secondary_url || '';
    document.getElementById('slideIsActive').checked = slide.is_active;
    
    // Afficher l'aperçu de l'image existante
    document.getElementById('imagePreview').classList.remove('hidden');
    document.getElementById('previewImg').src = `/storage/${slide.image_path}`;
    
    document.getElementById('slideModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Cacher le modal
function hideModal() {
    document.getElementById('slideModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Prévisualiser l'image
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').classList.remove('hidden');
            document.getElementById('previewImg').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Synchroniser le color picker avec le champ texte
document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.getElementById('backgroundColor');
    const colorHex = document.getElementById('backgroundColorHex');
    
    if (colorPicker && colorHex) {
        colorPicker.addEventListener('input', function() {
            colorHex.value = this.value.toUpperCase();
        });
        
        colorHex.addEventListener('input', function() {
            const hex = this.value;
            if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
                colorPicker.value = hex;
            }
        });
    }
    
    // Synchroniser le champ durée avec le slider
    const durationInput = document.getElementById('displayDuration');
    const durationRange = document.getElementById('durationRange');
    
    if (durationInput && durationRange) {
        durationInput.addEventListener('input', function() {
            const value = Math.max(3, Math.min(15, parseInt(this.value) || 6));
            this.value = value;
            durationRange.value = value;
        });
        
        durationRange.addEventListener('input', function() {
            durationInput.value = this.value;
        });
    }
});

// Mettre à jour l'ordre des slides
function updateSlidesOrder() {
    const slideItems = document.querySelectorAll('.slide-item');
    const order = Array.from(slideItems).map(item => parseInt(item.dataset.slideId));
    
    fetch('<?php echo e(route("admin.settings.hero-slides.reorder")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Ordre mis à jour avec succès !', 'success');
        } else {
            showNotification('Erreur lors de la mise à jour de l\'ordre', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la mise à jour de l\'ordre', 'error');
    });
}

// Afficher une notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300`;
    
    switch(type) {
        case 'success':
            notification.className += ' bg-green-500 text-white';
            break;
        case 'error':
            notification.className += ' bg-red-500 text-white';
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
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
        notification.classList.add('translate-x-0', 'opacity-100');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/settings/hero-slides.blade.php ENDPATH**/ ?>