

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-edit me-2"></i>Modifier la marque</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('brands.update', $brand)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo e(old('name', $brand->name)); ?>" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="2" maxlength="255"><?php echo e(old('description', $brand->description)); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="website" class="form-label">Site web</label>
                            <input type="url" name="website" id="website" class="form-control" value="<?php echo e(old('website', $brand->website)); ?>" maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            <?php if($brand->logo): ?>
                                <div class="mb-2">
                                    <img src="<?php echo e(asset('storage/' . $brand->logo)); ?>" alt="Logo actuel" style="width:60px;height:60px;object-fit:contain;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo e(old('is_active', $brand->is_active) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="<?php echo e(route('brands.index')); ?>" class="btn btn-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/brands/edit.blade.php ENDPATH**/ ?>