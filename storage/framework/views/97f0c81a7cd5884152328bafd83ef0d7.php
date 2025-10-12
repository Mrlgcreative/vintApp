
<div class="app-brand d-flex align-items-center <?php echo e($class ?? ''); ?>">
    <?php if(($showLogo ?? true) && file_exists(public_path($appLogo ?? ''))): ?>
        <img src="<?php echo e(asset($appLogo)); ?>" 
             alt="<?php echo e($appName); ?>" 
             class="app-logo me-2" 
             style="height: <?php echo e($logoHeight ?? '40px'); ?>; max-width: <?php echo e($logoWidth ?? '120px'); ?>; object-fit: contain;">
    <?php endif; ?>
    
    <?php if($showName ?? true): ?>
        <span class="app-name fw-bold <?php echo e($nameClass ?? 'text-dark'); ?>" 
              style="font-size: <?php echo e($nameSize ?? '1.5rem'); ?>;">
            <?php echo e($appName ?? 'VintApp'); ?>

        </span>
    <?php endif; ?>
</div><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/components/app-brand.blade.php ENDPATH**/ ?>