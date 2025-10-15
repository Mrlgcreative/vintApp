

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-cogs text-violet me-2"></i>
                        Personnalisation de mes articles
                    </h1>
                    <p class="text-muted mb-0">Modifiez la catégorie, la taille et la marque de vos articles</p>
                </div>
                <a href="<?php echo e(route('items.create')); ?>" class="btn btn-violet">
                    <i class="fas fa-plus me-2"></i>Ajouter un article
                </a>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if($userItems->count() > 0): ?>
                <div class="row g-4">
                    <?php $__currentLoopData = $userItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card personalization-card">
                            <div class="card-header bg-light">
                                <div class="d-flex align-items-center">
                                    <?php if($item->images && count($item->images) > 0): ?>
                                        <img src="<?php echo e(Storage::url($item->images[0])); ?>" 
                                             alt="<?php echo e($item->name); ?>" 
                                             class="item-thumbnail me-3">
                                    <?php else: ?>
                                        <div class="item-thumbnail-placeholder me-3">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo e(Str::limit($item->name, 40)); ?></h6>
                                        <small class="text-muted"><?php echo e(number_format($item->price)); ?> <?php echo e($item->currency); ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <form class="personalization-form" data-item-id="<?php echo e($item->id); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    
                                    <!-- Catégorie -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-tag me-1"></i>Catégorie
                                        </label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">Sélectionner une catégorie</option>
                                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($category->id); ?>" 
                                                        <?php echo e($item->category_id == $category->id ? 'selected' : ''); ?>>
                                                    <?php echo e($category->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <!-- Marque -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-copyright me-1"></i>Marque
                                        </label>
                                        <select name="brand_id" class="form-select">
                                            <option value="">Aucune marque</option>
                                            <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($brand->id); ?>" 
                                                        <?php echo e($item->brand_id == $brand->id ? 'selected' : ''); ?>>
                                                    <?php echo e($brand->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <!-- Taille -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-ruler me-1"></i>Taille
                                        </label>
                                        <input type="text" 
                                               name="size" 
                                               class="form-control" 
                                               value="<?php echo e($item->size ?? ''); ?>"
                                               placeholder="Ex: M, L, XL, 42, 43...">
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-violet btn-sm flex-grow-1">
                                            <i class="fas fa-save me-1"></i>Sauvegarder
                                        </button>
                                        <a href="<?php echo e(route('items.edit', $item)); ?>" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">Aucun article à personnaliser</h4>
                        <p class="text-muted mb-4">Vous n'avez pas encore publié d'articles. Commencez par créer votre premier article !</p>
                        <a href="<?php echo e(route('items.create')); ?>" class="btn btn-violet btn-lg">
                            <i class="fas fa-plus me-2"></i>Créer mon premier article
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.personalization-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.personalization-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.item-thumbnail {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.item-thumbnail-placeholder {
    width: 60px;
    height: 60px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.personalization-form {
    position: relative;
}

.personalization-form.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.personalization-form.loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    border: 2px solid var(--violet-color);
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    z-index: 1;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

.empty-state {
    max-width: 400px;
    margin: 0 auto;
}

.form-label {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.form-select, .form-control {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.form-select:focus, .form-control:focus {
    border-color: var(--violet-color);
    box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 6px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.personalization-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const itemId = this.dataset.itemId;
            const formData = new FormData(this);
            
            // Ajouter la classe loading
            this.classList.add('loading');
            
            fetch(`/items/${itemId}/personalization`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    category_id: formData.get('category_id'),
                    brand_id: formData.get('brand_id'),
                    size: formData.get('size'),
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher un message de succès
                    showAlert('success', data.message);
                } else {
                    showAlert('error', 'Erreur lors de la mise à jour');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Erreur lors de la mise à jour');
            })
            .finally(() => {
                // Retirer la classe loading
                this.classList.remove('loading');
            });
        });
    });
    
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/items/personalization.blade.php ENDPATH**/ ?>