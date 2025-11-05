

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- En-tête de la catégorie -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <?php if($category->icon): ?>
                                <i class="<?php echo e($category->icon); ?> text-primary fs-1 me-3"></i>
                            <?php else: ?>
                                <i class="fas fa-folder text-muted fs-1 me-3"></i>
                            <?php endif; ?>
                            <div>
                                <h1 class="h2 mb-1"><?php echo e($category->name); ?></h1>
                                <?php if($category->description): ?>
                                    <p class="text-muted mb-2"><?php echo e($category->description); ?></p>
                                <?php endif; ?>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge <?php echo e($category->is_active ? 'bg-success' : 'bg-secondary'); ?>">
                                        <?php echo e($category->is_active ? 'Active' : 'Inactive'); ?>

                                    </span>
                                    <small class="text-muted">
                                        <i class="fas fa-box me-1"></i>
                                        <?php echo e($category->items_count ?? 0); ?> article(s)
                                    </small>
                                    <?php if($category->parent): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-level-up-alt me-1"></i>
                                            Sous-catégorie de 
                                            <a href="<?php echo e(route('categories.show', $category->parent)); ?>" class="text-decoration-none">
                                                <?php echo e($category->parent->name); ?>

                                            </a>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i> Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('categories.edit', $category)); ?>">
                                        <i class="fas fa-edit me-2"></i> Modifier
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('items.index', ['category' => $category->id])); ?>">
                                        <i class="fas fa-list me-2"></i> Voir tous les articles
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="<?php echo e(route('categories.destroy', $category)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="dropdown-item text-danger"
                                                onclick="return confirm('Supprimer cette catégorie ? Tous les articles associés seront également supprimés.')">
                                            <i class="fas fa-trash me-2"></i> Supprimer
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sous-catégories -->
    <?php if(isset($subcategories) && $subcategories->count() > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i> Sous-catégories</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-center">
                                    <?php if($subcategory->icon): ?>
                                        <i class="<?php echo e($subcategory->icon); ?> text-primary fs-3 mb-2"></i>
                                    <?php else: ?>
                                        <i class="fas fa-folder text-muted fs-3 mb-2"></i>
                                    <?php endif; ?>
                                    <h6 class="card-title"><?php echo e($subcategory->name); ?></h6>
                                    <p class="card-text small text-muted"><?php echo e(Str::limit($subcategory->description, 60)); ?></p>
                                    <span class="badge bg-info"><?php echo e($subcategory->items_count ?? 0); ?> articles</span>
                                    <div class="mt-2">
                                        <a href="<?php echo e(route('categories.show', $subcategory)); ?>" class="btn btn-sm btn-outline-primary">
                                            Voir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Articles de cette catégorie -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i> Articles de cette catégorie</h5>
                    <a href="<?php echo e(route('items.create', ['category' => $category->id])); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Ajouter un article
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if(isset($items) && $items->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Prix</th>
                                    <th>État</th>
                                    <th>Statut</th>
                                    <th>Vendeur</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <?php if($item->images): ?>
                                            <?php 
                                                // Vérifier si $item->images est déjà un array ou une string JSON
                                                $images = is_array($item->images) ? $item->images : json_decode($item->images, true);
                                            ?>
                                            <?php if($images && is_array($images) && count($images) > 0): ?>
                                                <img src="<?php echo e(asset('storage/' . $images[0])); ?>" 
                                                     alt="<?php echo e($item->name); ?>" 
                                                     style="width:50px;height:50px;object-fit:cover;" 
                                                     class="rounded">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                     style="width:50px;height:50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                 style="width:50px;height:50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?php echo e($item->name); ?></div>
                                        <small class="text-muted"><?php echo e(Str::limit($item->description, 40)); ?></small>
                                    </td>
                                    <td class="fw-bold"><?php echo e($item->formatted_price ?? $item->price . ' FC'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($item->condition_color ?? 'secondary'); ?>">
                                            <?php echo e(ucfirst($item->condition)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($item->status_color ?? 'secondary'); ?>">
                                            <?php echo e(ucfirst($item->status)); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($item->user->name ?? 'Inconnu'); ?></td>
                                    <td class="text-end">
                                        <a href="<?php echo e(route('items.show', $item)); ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Voir l'article">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if(method_exists($items, 'links')): ?>
                    <div class="card-footer bg-white">
                        <?php echo e($items->links()); ?>

                    </div>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fs-1 text-muted mb-3"></i>
                        <h6 class="text-muted">Aucun article dans cette catégorie</h6>
                        <a href="<?php echo e(route('items.create', ['category' => $category->id])); ?>" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-plus me-1"></i> Ajouter le premier article
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton retour -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="<?php echo e(route('categories.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour aux catégories
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/categories/show.blade.php ENDPATH**/ ?>