

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fas fa-layer-group me-2"></i> Catégories</h2>
        <a href="<?php echo e(route('categories.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Ajouter une catégorie
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Icône</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Articles</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <?php if($category->icon): ?>
                                        <i class="<?php echo e($category->icon); ?> text-primary fs-4"></i>
                                    <?php else: ?>
                                        <i class="fas fa-folder text-muted"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-semibold"><?php echo e($category->name); ?></td>
                                <td><?php echo e(Str::limit($category->description, 50) ?: '-'); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo e($category->items_count ?? 0); ?></span>
                                </td>
                                <td>
                                    <?php if($category->is_active): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('categories.show', $category)); ?>" 
                                       class="btn btn-sm btn-outline-info me-1" 
                                       title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('categories.edit', $category)); ?>" 
                                       class="btn btn-sm btn-outline-primary me-1" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('categories.destroy', $category)); ?>" 
                                          method="POST" 
                                          class="d-inline-block" 
                                          onsubmit="return confirm('Supprimer cette catégorie ? Tous les articles associés seront également supprimés.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-layer-group fs-1 mb-2 d-block"></i>
                                    Aucune catégorie trouvée.
                                    <br>
                                    <a href="<?php echo e(route('categories.create')); ?>" class="btn btn-primary btn-sm mt-2">
                                        Créer la première catégorie
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if(isset($categories) && method_exists($categories, 'links')): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($categories->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/categories/index.blade.php ENDPATH**/ ?>