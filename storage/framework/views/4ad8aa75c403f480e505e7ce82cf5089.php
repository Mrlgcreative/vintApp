

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fas fa-tags me-2"></i> Marques</h2>
        <a href="<?php echo e(route('brands.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Ajouter une marque
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Logo</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Site web</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <?php if($brand->logo): ?>
                                        <img src="<?php echo e(asset('storage/' . $brand->logo)); ?>" alt="Logo" style="width:40px;height:40px;object-fit:contain;">
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-semibold"><?php echo e($brand->name); ?></td>
                                <td><?php echo e(Str::limit($brand->description, 40)); ?></td>
                                <td>
                                    <?php if($brand->website): ?>
                                        <a href="<?php echo e($brand->website); ?>" target="_blank"><?php echo e($brand->website); ?></a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($brand->is_active): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('brands.edit', $brand)); ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                                    <form action="<?php echo e(route('brands.destroy', $brand)); ?>" method="POST" class="d-inline-block" onsubmit="return confirm('Supprimer cette marque ?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aucune marque trouvée.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/brands/index.blade.php ENDPATH**/ ?>