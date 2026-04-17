<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'default', // 'default', 'admin', 'minimal'
    'showNewsletter' => true,
    'showSocial' => true,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'variant' => 'default', // 'default', 'admin', 'minimal'
    'showNewsletter' => true,
    'showSocial' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isAdmin = $variant === 'admin';
    $isMinimal = $variant === 'minimal';
?>

<footer class="<?php echo e($isAdmin ? 'bg-gray-100 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700' : 'bg-gray-800 dark:bg-gray-900'); ?> text-gray-<?php echo e($isAdmin ? '600 dark:text-gray-400' : '300'); ?> py-<?php echo e($isMinimal ? '6' : '12'); ?> mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if(!$isMinimal): ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <!-- À propos -->
                <div class="col-span-2 md:col-span-1">
                    <h5 class="font-semibold <?php echo e($isAdmin ? 'text-gray-900 dark:text-white' : 'text-white'); ?> mb-4">
                        <?php if (isset($component)) { $__componentOriginalac37604bae5cded3771d6931140b8398 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalac37604bae5cded3771d6931140b8398 = $attributes; } ?>
<?php $component = App\View\Components\AppBrand::resolve(['showLogo' => true,'showName' => true,'logoHeight' => '24px','logoWidth' => '80px','nameSize' => '1.25rem','nameClass' => $isAdmin ? 'text-gray-900 dark:text-white' : 'text-white'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-brand'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppBrand::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $attributes = $__attributesOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__attributesOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $component = $__componentOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__componentOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>
                    </h5>
                    <p class="text-sm <?php echo e($isAdmin ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400'); ?>">
                        <?php echo e($appDescription ?? 'La marketplace de confiance pour acheter et vendre des articles d\'occasion.'); ?>

                    </p>
                </div>
                
                <!-- Navigation -->
                <div>
                    <h6 class="font-semibold <?php echo e($isAdmin ? 'text-gray-900 dark:text-white' : 'text-white'); ?> mb-4">Navigation</h6>
                    <ul class="space-y-2 text-sm">
                        <?php if($isAdmin): ?>
                            <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-gray-900 dark:hover:text-white transition-colors">Dashboard</a></li>
                            <li><a href="<?php echo e(route('admin.users.index')); ?>" class="hover:text-gray-900 dark:hover:text-white transition-colors">Utilisateurs</a></li>
                            <li><a href="<?php echo e(route('admin.items.index')); ?>" class="hover:text-gray-900 dark:hover:text-white transition-colors">Articles</a></li>
                            <li><a href="<?php echo e(route('admin.orders.index')); ?>" class="hover:text-gray-900 dark:hover:text-white transition-colors">Commandes</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo e(route('items.index')); ?>" class="hover:text-white transition-colors">Articles</a></li>
                            <li><a href="<?php echo e(route('categories.index')); ?>" class="hover:text-white transition-colors">Catégories</a></li>
                            <li><a href="<?php echo e(route('brands.index')); ?>" class="hover:text-white transition-colors">Marques</a></li>
                            <?php if(auth()->guard()->check()): ?>
                                <li><a href="<?php echo e(route('items.my-items')); ?>" class="hover:text-white transition-colors">Mes articles</a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Support -->
                <div>
                    <h6 class="font-semibold <?php echo e($isAdmin ? 'text-gray-900 dark:text-white' : 'text-white'); ?> mb-4">Support</h6>
                    <ul class="space-y-2 text-sm">
                        <?php if($isAdmin): ?>
                            <li><a href="<?php echo e(route('admin.support.index')); ?>" class="hover:text-gray-900 dark:hover:text-white transition-colors">Tickets Support</a></li>
                            <li><a href="<?php echo e(route('admin.settings.index')); ?>" class="hover:text-gray-900 dark:hover:text-white transition-colors">Paramètres</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo e(route('support.index')); ?>" class="hover:text-white transition-colors">Support Client</a></li>
                            <li><a href="<?php echo e(route('help.index')); ?>" class="hover:text-white transition-colors">Centre d'aide</a></li>
                            <li><a href="<?php echo e(route('help.index')); ?>#contact" class="hover:text-white transition-colors">Contact</a></li>
                            <li><a href="<?php echo e(route('terms')); ?>" class="hover:text-white transition-colors">CGU</a></li>
                            <li><a href="<?php echo e(route('privacy')); ?>" class="hover:text-white transition-colors">Confidentialité</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Réseaux sociaux -->
                <?php if($showSocial): ?>
                    <div>
                        <h6 class="font-semibold <?php echo e($isAdmin ? 'text-gray-900 dark:text-white' : 'text-white'); ?> mb-4">Suivez-nous</h6>
                        <div class="flex space-x-3">
                            <a href="https://facebook.com/vintapp" target="_blank" class="<?php echo e($isAdmin ? 'text-gray-400 hover:text-blue-600' : 'text-gray-400 hover:text-white'); ?> transition-colors">
                                <i class="fab fa-facebook-f text-lg"></i>
                            </a>
                            <a href="https://twitter.com/vintapp" target="_blank" class="<?php echo e($isAdmin ? 'text-gray-400 hover:text-blue-400' : 'text-gray-400 hover:text-white'); ?> transition-colors">
                                <i class="fab fa-twitter text-lg"></i>
                            </a>
                            <a href="https://instagram.com/vintapp" target="_blank" class="<?php echo e($isAdmin ? 'text-gray-400 hover:text-pink-600' : 'text-gray-400 hover:text-white'); ?> transition-colors">
                                <i class="fab fa-instagram text-lg"></i>
                            </a>
                            <?php if($isAdmin): ?>
                                <a href="https://linkedin.com/company/vintapp" target="_blank" class="text-gray-400 hover:text-blue-700 transition-colors">
                                    <i class="fab fa-linkedin-in text-lg"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($isAdmin): ?>
                            <!-- Version info pour admin -->
                            <div class="mt-4 text-xs text-gray-400 dark:text-gray-500">
                                <p>Version: <?php echo e(config('app.version', '1.0.0')); ?></p>
                                <p>Laravel: <?php echo e(app()->version()); ?></p>
                                <p>PHP: <?php echo e(phpversion()); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Newsletter -->
            <?php if($showNewsletter && !$isAdmin): ?>
                <div class="border-t border-gray-700 dark:border-gray-800 mt-8 pt-8">
                    <div class="text-center max-w-md mx-auto">
                        <h5 class="font-semibold text-white mb-3">📧 Newsletter</h5>
                        <p class="text-sm text-gray-400 mb-4">Recevez nos dernières offres et nouveautés.</p>
                        <form id="newsletterForm" class="flex gap-2">
                            <?php echo csrf_field(); ?>
                            <input type="email" id="newsletterEmail" 
                                   class="flex-1 px-3 py-2 bg-gray-700 dark:bg-gray-800 text-white rounded-md border border-gray-600 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                                   placeholder="Votre email" required>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        <div id="newsletterMessage" class="mt-2 text-sm"></div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Copyright -->
        <div class="<?php echo e(!$isMinimal ? 'border-t border-gray-'.($isAdmin ? '200 dark:border-gray-700' : '700 dark:border-gray-800').' mt-8 pt-8' : ''); ?> text-center">
            <p class="text-sm <?php echo e($isAdmin ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400'); ?>">
                © <?php echo e(date('Y')); ?> <?php echo e($appName ?? config('app.name', 'VintApp')); ?>. Tous droits réservés.
                <?php if($isAdmin): ?>
                    <span class="mx-2">|</span>
                    <a href="<?php echo e(url('/')); ?>" target="_blank" class="hover:text-gray-900 dark:hover:text-white transition-colors">
                        <i class="fas fa-external-link-alt mr-1 text-xs"></i>Voir le site
                    </a>
                <?php endif; ?>
            </p>
        </div>
    </div>
</footer>
<?php /**PATH D:\Mes projets\vintApp\resources\views/components/footer.blade.php ENDPATH**/ ?>