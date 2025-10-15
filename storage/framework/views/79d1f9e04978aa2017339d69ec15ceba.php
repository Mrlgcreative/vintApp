<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus me-2"></i>
                        Vendre un article
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?php echo e(route('items.store')); ?>" enctype="multipart/form-data" id="createItemForm">
                        <?php echo csrf_field(); ?>

                        <!-- Informations de base -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Informations de base
                                </h5>
                            </div>

                            <!-- Nom de l'article -->
                            <div class="col-md-8">
                                <div class="form-floating mb-3">
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo e(old('name')); ?>" 
                                           placeholder="Nom de l'article" 
                                           required>
                                    <label for="name">
                                        <i class="fas fa-tag me-2"></i>
                                        Nom de l'article *
                                    </label>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <!-- Prix -->
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                   
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <div class="form-floating">
                                <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                          id="description" 
                                          name="description" 
                                          placeholder="Description détaillée" 
                                          style="height: 120px" 
                                          required><?php echo e(old('description')); ?></textarea>
                                <label for="description">
                                    <i class="fas fa-align-left me-2"></i>
                                    Description détaillée *
                                </label>
                                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Décrivez votre article en détail pour attirer les acheteurs
                                </small>
                            </div>
                        </div>

                        <!-- Catégorie et marque -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">Choisir une catégorie</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($category->id); ?>" 
                                                    <?php echo e(old('category_id') == $category->id ? 'selected' : ''); ?>>
                                                <?php echo e($category->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <label for="category_id">
                                        <i class="fas fa-folder me-2"></i>
                                        Catégorie *
                                    </label>
                                    <?php $__errorArgs = ['categorie_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['brand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="brand_id" 
                                            name="brand_id">
                                        <option value="">Choisir une marque (optionnel)</option>
                                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($brand->id); ?>" 
                                                    <?php echo e(old('brand_id') == $brand->id ? 'selected' : ''); ?>>
                                                <?php echo e($brand->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <label for="brand_id">
                                        <i class="fas fa-trademark me-2"></i>
                                        Marque
                                    </label>
                                    <?php $__errorArgs = ['brand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Prix et devise -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="number" 
                                           class="form-control <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="price" 
                                           name="price" 
                                           value="<?php echo e(old('price')); ?>" 
                                           placeholder="Prix" 
                                           step="0.01" 
                                           min="0" 
                                           required>
                                    <label for="price">
                                        <i class="fas fa-dollar-sign me-2"></i>
                                        Prix *
                                    </label>
                                    <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="currency" 
                                            name="currency" 
                                            required>
                                        <option value="">Choisir la devise</option>
                                        <option value="USD" <?php echo e(old('currency') == 'USD' ? 'selected' : ''); ?>>USD ($)</option>
                                        <option value="CDF" <?php echo e(old('currency') == 'CDF' ? 'selected' : ''); ?>>CDF (FC)</option>
                                    </select>
                                    <label for="currency">
                                        <i class="fas fa-money-bill me-2"></i>
                                        Devise *
                                    </label>
                                    <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Condition et quantité -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="condition" 
                                            name="condition" 
                                            required>
                                        <option value="">Choisir l'état</option>
                                        <option value="new" <?php echo e(old('condition') == 'new' ? 'selected' : ''); ?>>Neuf</option>
                                        <option value="like_new" <?php echo e(old('condition') == 'like_new' ? 'selected' : ''); ?>>Comme neuf</option>
                                        <option value="good" <?php echo e(old('condition') == 'good' ? 'selected' : ''); ?>>Bon état</option>
                                        <option value="fair" <?php echo e(old('condition') == 'fair' ? 'selected' : ''); ?>>État correct</option>
                                        <option value="poor" <?php echo e(old('condition') == 'poor' ? 'selected' : ''); ?>>Usé</option>
                                    </select>
                                    <label for="condition">
                                        <i class="fas fa-star me-2"></i>
                                        État de l'article *
                                    </label>
                                    <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="number" 
                                           class="form-control <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="quantity" 
                                           name="quantity" 
                                           value="<?php echo e(old('quantity', 1)); ?>" 
                                           placeholder="Quantité" 
                                           min="1" 
                                           required>
                                    <label for="quantity">
                                        <i class="fas fa-boxes me-2"></i>
                                        Quantité disponible *
                                    </label>
                                    <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-images me-2"></i>
                                Photos de l'article
                            </h5>
                            <div class="form-floating mb-3">
                                <input type="file" 
                                       class="form-control <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="images" 
                                       name="images[]" 
                                       multiple 
                                       accept="image/*">
                                <label for="images">
                                    <i class="fas fa-upload me-2"></i>
                                    Sélectionner des images
                                </label>
                                <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Vous pouvez sélectionner plusieurs images. Formats acceptés : JPG, PNG, GIF (max 2MB par image)
                                </small>
                            </div>
                            
                            <!-- Prévisualisation des images -->
                            <div id="imagePreview" class="row mt-3" style="display: none;">
                                <!-- Les prévisualisations seront ajoutées ici -->
                            </div>
                        </div>

                        <!-- Spécifications -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-cogs me-2"></i>
                                Spécifications (optionnel)
                            </h5>
                            <div id="specificationsContainer">
                                <div class="row mb-2 specification-row">
                                    <div class="col-md-5">
                                        <input type="text" 
                                               class="form-control" 
                                               name="specifications[key][]" 
                                               placeholder="Nom de la spécification">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" 
                                               class="form-control" 
                                               name="specifications[value][]" 
                                               placeholder="Valeur">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-specification">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addSpecification">
                                <i class="fas fa-plus me-2"></i>
                                Ajouter une spécification
                            </button>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('items.index')); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check me-2"></i>
                                Publier l'article
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prévisualisation des images
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        imagePreview.style.display = 'none';
        
        if (this.files.length > 0) {
            imagePreview.style.display = 'block';
            
            Array.from(this.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 mb-3';
                        col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" 
                                     class="img-thumbnail" 
                                     alt="Prévisualisation ${index + 1}"
                                     style="height: 150px; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-image" 
                                        data-index="${index}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        imagePreview.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // Supprimer une image de la prévisualisation
    imagePreview.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-image')) {
            const index = parseInt(e.target.dataset.index);
            const dt = new DataTransfer();
            const input = document.getElementById('images');
            const { files } = input;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }
            
            input.files = dt.files;
            e.target.closest('.col-md-3').remove();
            
            if (imagePreview.children.length === 0) {
                imagePreview.style.display = 'none';
            }
        }
    });

    // Gestion des spécifications
    const addSpecBtn = document.getElementById('addSpecification');
    const specContainer = document.getElementById('specificationsContainer');
    
    addSpecBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'row mb-2 specification-row';
        newRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" 
                       class="form-control" 
                       name="specifications[key][]" 
                       placeholder="Nom de la spécification">
            </div>
            <div class="col-md-5">
                <input type="text" 
                       class="form-control" 
                       name="specifications[value][]" 
                       placeholder="Valeur">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-specification">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        specContainer.appendChild(newRow);
    });

    // Supprimer une spécification
    specContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-specification') || 
            e.target.closest('.remove-specification')) {
            e.target.closest('.specification-row').remove();
        }
    });

    // Validation du formulaire
    const form = document.getElementById('createItemForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showNotification('Veuillez remplir tous les champs obligatoires', 'danger');
        }
    });

    // Validation du prix
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('input', function() {
        if (this.value < 0) {
            this.value = 0;
        }
    });

    // Validation de la quantité
    const quantityInput = document.getElementById('quantity');
    quantityInput.addEventListener('input', function() {
        if (this.value < 1) {
            this.value = 1;
        }
    });
});

// Fonction pour afficher les notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>

<style>
.form-floating > .form-control {
    border-radius: 0.5rem;
}

.btn-lg {
    border-radius: 0.5rem;
    padding: 0.75rem 2rem;
}

.card {
    border-radius: 1rem;
    border: none;
}

.card-header {
    border-radius: 1rem 1rem 0 0 !important;
}

/* Animation d'entrée */
.card {
    animation: slideInUp 0.5s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Styles pour les spécifications */
.specification-row {
    transition: all 0.3s ease;
}

.specification-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
    border-radius: 0.5rem;
    padding: 0.5rem;
    margin: -0.5rem;
}

/* Styles pour la prévisualisation des images */
.img-thumbnail {
    transition: transform 0.2s ease;
}

.img-thumbnail:hover {
    transform: scale(1.05);
}

/* Styles responsives */
@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem !important;
    }
    
    .btn-lg {
        padding: 0.5rem 1.5rem;
        font-size: 1rem;
    }
}
</style>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/items/create.blade.php ENDPATH**/ ?>