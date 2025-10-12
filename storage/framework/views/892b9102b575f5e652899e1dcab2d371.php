<?php $__env->startPush('styles'); ?>
<style>
@media (max-width: 768px) {
    .row.g-4 > [class^="col-"],
    .row.g-4 > [class*=" col-"] {
        flex: 0 0 33.3333%;
        max-width: 33.3333%;
        padding-left: 3px;
        padding-right: 3px;
        margin-bottom: 8px;
    }
    .item-card {
        margin-bottom: 1rem;
        min-width: 0;
        max-width: 100%;
        height: 120px;
        padding: 0.25rem 0.25rem 0.1rem 0.25rem;
        border-radius: 8px;
    }
    .card-img-top, .item-card img {
        height: 50px !important;
        border-radius: 6px 6px 0 0;
    }
    .card-title {
        font-size: 9px;
        line-height: 1.1;
        margin-bottom: 0.1rem;
    }
    .card-text {
        font-size: 8px;
        line-height: 1.1;
        display: none; /* Masquer la description sur mobile */
    }
    .card-body {
        padding: 0.3rem;
    }
    .badge {
        font-size: 6px;
        padding: 0.08em 0.3em;
    }
    .condition-badge {
        font-size: 6px;
        padding: 0.15em 0.4em;
        border-radius: 6px;
    }
    .favorite-btn {
        width: 20px;
        height: 20px;
        padding: 0;
        font-size: 9px;
        margin: 2px;
    }
    .btn-primary.w-100 {
        font-size: 8px;
        padding: 0.2rem 0.25rem;
        margin-top: 0.2rem !important;
    }
    .btn-primary.w-100 i {
        display: none; /* Masquer l'icône sur mobile */
    }
    .text-primary.fw-bold {
        font-size: 9px;
    }
    .text-muted small, small.text-muted {
        font-size: 7px;
    }
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1px;
    }
    .d-flex.justify-content-between.align-items-center.mb-2 {
        margin-bottom: 0.15rem !important;
    }
}
</style>
<?php $__env->stopPush(); ?>
<!-- Toast notification Bootstrap -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11000">
    <div id="mainToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mainToastBody">
                Notification
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script>
function showNotification(message, type = 'primary') {
        var toastEl = document.getElementById('mainToast');
        var toastBody = document.getElementById('mainToastBody');
        if (!toastEl || !toastBody) return;
        toastBody.textContent = message;
        toastEl.className = 'toast align-items-center text-bg-' + type + ' border-0';
        var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
        toast.show();
}
</script>


<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-box me-2"></i>
                    Articles disponibles
                </h1>
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('items.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Vendre un article
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('items.index')); ?>" class="row g-3">
                        <!-- Recherche -->
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Rechercher un article..."
                                       value="<?php echo e(request('search')); ?>">
                            </div>
                        </div>

                        <!-- Catégorie -->
                        <div class="col-md-2">
                            <select class="form-select" name="category">
                                <option value="">Toutes les catégories</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" 
                                            <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                                        <?php echo e($category->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Marque -->
                        <div class="col-md-2">
                            <select class="form-select" name="brand">
                                <option value="">Toutes les marques</option>
                                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($brand->id); ?>" 
                                            <?php echo e(request('brand') == $brand->id ? 'selected' : ''); ?>>
                                        <?php echo e($brand->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Condition -->
                        <div class="col-md-2">
                            <select class="form-select" name="condition">
                                <option value="">Toutes les conditions</option>
                                <option value="new" <?php echo e(request('condition') == 'new' ? 'selected' : ''); ?>>Neuf</option>
                                <option value="like_new" <?php echo e(request('condition') == 'like_new' ? 'selected' : ''); ?>>Comme neuf</option>
                                <option value="good" <?php echo e(request('condition') == 'good' ? 'selected' : ''); ?>>Bon état</option>
                                <option value="fair" <?php echo e(request('condition') == 'fair' ? 'selected' : ''); ?>>État correct</option>
                                <option value="poor" <?php echo e(request('condition') == 'poor' ? 'selected' : ''); ?>>Usé</option>
                            </select>
                        </div>

                        <!-- Prix min -->
                        <div class="col-md-1">
                            <input type="number" 
                                   class="form-control" 
                                   name="min_price" 
                                   placeholder="Min"
                                   value="<?php echo e(request('min_price')); ?>">
                        </div>

                        <!-- Prix max -->
                        <div class="col-md-1">
                            <input type="number" 
                                   class="form-control" 
                                   name="max_price" 
                                   placeholder="Max"
                                   value="<?php echo e(request('max_price')); ?>">
                        </div>

                        <!-- Boutons -->
                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    Filtrer
                                </button>
                                <a href="<?php echo e(route('items.index')); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultats -->
    <div class="row">
        <div class="col-12">
            <?php if($items->count() > 0): ?>
                <div class="row g-4">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card h-100 item-card">
                                <!-- Image -->
                                <div class="position-relative">
                                    <?php if($item->images && count($item->images) > 0): ?>
                                        <img src="<?php echo e(Storage::url($item->images[0])); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo e($item->name); ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Badge condition -->
                                    <span class="position-absolute top-0 start-0 m-2 badge condition-badge condition-<?php echo e($item->condition); ?>">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $item->condition))); ?>

                                    </span>

                                    <!-- Bouton favori -->
                                    <?php if(auth()->guard()->check()): ?>
                                        <button class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 favorite-btn"
                                                data-item-id="<?php echo e($item->id); ?>">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Contenu -->
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo e(Str::limit($item->name, 50)); ?></h5>
                                    
                                    <div class="mb-2">
                                        <span class="badge bg-primary"><?php echo e($item->category->name); ?></span>
                                        <?php if($item->brand): ?>
                                            <span class="badge bg-secondary"><?php echo e($item->brand->name); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <p class="card-text text-muted">
                                        <?php echo e(Str::limit($item->description, 100)); ?>

                                    </p>

                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <p class="text-primary fw-bold"><?php echo e($item->formatted_price); ?></p>
                                            <small class="text-muted">
                                                <i class="fas fa-eye me-1"></i>
                                                <?php echo e($item->views); ?> vues
                                            </small>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo e($item->user->name); ?>

                                            </small>
                                            <small class="text-muted">
                                                <?php echo e($item->created_at->diffForHumans()); ?>

                                            </small>
                                        </div>

                                        <a href="<?php echo e(route('items.show', $item)); ?>" 
                                           class="btn btn-primary w-100 mt-2">
                                            <i class="fas fa-eye me-2"></i>
                                            Voir détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <?php echo e($items->appends(request()->query())->links()); ?>

                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun article trouvé</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('items.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Vendre votre premier article
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des favoris
    const favoriteBtns = document.querySelectorAll('.favorite-btn');
    favoriteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.dataset.itemId;
            
            fetch(`/items/${itemId}/favorite`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = this.querySelector('i');
                    if (data.is_favorite) {
                        icon.classList.add('text-danger');
                    } else {
                        icon.classList.remove('text-danger');
                    }
                    
                    // Afficher une notification
                    showNotification(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Une erreur est survenue', 'danger');
            });
        });
    });
});
</script>
<?php $__env->stopSection(); ?> 

<?php $__env->startPush('styles'); ?>
<style>
.item-card {
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(79,0,206,0.08), 0 1.5px 4px rgba(0,0,0,0.04);
    transition: transform 0.15s, box-shadow 0.15s;
    border: none;
}
.item-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 8px 32px rgba(79,0,206,0.16), 0 2px 8px rgba(0,0,0,0.08);
    border: 1.5px solid #4f00ce22;
}
.card-img-top, .item-card img {
    border-radius: 18px 18px 0 0;
}
.condition-badge {
    border-radius: 12px;
    font-size: 0.85rem;
    padding: 0.4em 0.9em;
    background: #f5f5f5;
    color: #4f00ce;
    font-weight: 600;
    box-shadow: 0 1px 4px rgba(79,0,206,0.08);
}
.favorite-btn {
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(79,0,206,0.08);
    transition: background 0.2s, color 0.2s;
}
.favorite-btn i {
    color: #bbb;
    transition: color 0.2s;
}
.favorite-btn:hover i, .favorite-btn.active i, .favorite-btn i.text-danger {
    color: #e74c3c !important;
}
.card-title {
    font-weight: 700;
    color: #2d176a;
}
.card-text {
    font-size: 0.97rem;
}
.btn-primary, .btn-primary:focus {
    background: linear-gradient(90deg, #4f00ce 60%, #8f5cff 100%);
    border: none;
    font-weight: 600;
    letter-spacing: 0.02em;
}
.btn-primary:hover {
    background: linear-gradient(90deg, #8f5cff 0%, #4f00ce 100%);
}
.badge.bg-primary {
    background: #4f00ce !important;
}
.badge.bg-secondary {
    background: #8f5cff !important;
}
@media (max-width: 768px) {
    .item-card {
        margin-bottom: 0.8rem;
    }
    .card-img-top, .item-card img {
        height: 50px !important;
    }
    .card-body {
        padding: 0.3rem;
    }
}
</style>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/items/index.blade.php ENDPATH**/ ?>