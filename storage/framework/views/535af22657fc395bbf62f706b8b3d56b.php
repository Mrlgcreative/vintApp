

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Mon panier</h2>
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if(empty($cart)): ?>
        <div class="alert alert-info">Votre panier est vide.</div>
    <?php else: ?>
    <form method="POST" action="<?php echo e(route('cart.clear')); ?>" class="mb-3">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i> Vider le panier</button>
    </form>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $total += $item['price'] * $item['quantity']; ?>
                    <tr>
                        <td>
                            <?php if($item['image']): ?>
                                <img src="<?php echo e(asset('storage/' . $item['image'])); ?>" alt="<?php echo e($item['name']); ?>" width="50" class="me-2 rounded">
                            <?php endif; ?>
                            <div>
                                <?php echo e($item['name']); ?>

                                <?php if(isset($item['has_discount']) && $item['has_discount']): ?>
                                    <br><small class="badge bg-success">
                                        <i class="fas fa-tag me-1"></i>
                                        Réduction <?php echo e($item['discount_percentage']); ?>%
                                    </small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if(isset($item['has_discount']) && $item['has_discount']): ?>
                                <div>
                                    <span class="text-decoration-line-through text-muted small">
                                        <?php echo e($item['original_price']); ?> <?php echo e($item['currency']); ?>

                                    </span>
                                    <br>
                                    <span class="text-success fw-bold">
                                        <?php echo e($item['price']); ?> <?php echo e($item['currency']); ?>

                                    </span>
                                </div>
                            <?php else: ?>
                                <?php echo e($item['price']); ?> <?php echo e($item['currency']); ?>

                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="<?php echo e(route('cart.update', $item['id'])); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <input type="number" name="quantity" value="<?php echo e($item['quantity']); ?>" min="1" style="width:60px;">
                                <button type="submit" class="btn btn-outline-primary btn-sm ms-1"><i class="fas fa-sync"></i></button>
                            </form>
                        </td>
                        <td><b><?php echo e(number_format($item['price'] * $item['quantity'], 2)); ?> <?php echo e($item['currency']); ?></b></td>
                        <td>
                            <form method="POST" action="<?php echo e(route('cart.remove', $item['id'])); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-times"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total :</th>
                    <th colspan="2"><?php echo e(number_format($total, 2)); ?> <?php echo e($item['currency'] ?? ''); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="d-flex justify-content-end">
        <a href="<?php echo e(route('cart.checkout')); ?>" class="btn btn-primary btn-lg">
            <i class="fas fa-credit-card me-2"></i>Passer à la caisse
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/cart.blade.php ENDPATH**/ ?>