<?php $__env->startSection('content'); ?>
<div class="min-h-[80vh] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8 text-center animate-slide-up relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none shimmer"></div>

            <div class="mb-5 animate-shake">
                <div class="w-16 h-16 mx-auto rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center animate-pulse-error">
                    <div class="animate-bounce-error">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>

            <h2 class="text-lg font-bold text-red-600 dark:text-red-400 mb-2">Paiement Échoué</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Votre transaction n'a pas pu être traitée</p>

            <?php if(isset($error) && $error): ?>
                <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/20 rounded-xl p-4 mb-5 text-sm text-red-700 dark:text-red-300">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span><?php echo e($error); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 mb-5 text-sm text-left space-y-2">
                <h5 class="font-semibold text-red-600 dark:text-red-400 pb-2 border-b border-gray-200 dark:border-gray-700 mb-2">
                    Détails de la tentative
                </h5>

                <?php if(isset($amount) && $amount > 0): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Montant tenté</span>
                        <span class="font-semibold text-gray-900 dark:text-white text-right">
                            <?php echo e(number_format($amount, 2)); ?> <?php echo e($currency ?? 'USD'); ?>

                            <?php if(isset($currency)): ?>
                                <?php if($currency === 'USD'): ?>
                                    <span class="text-gray-400 font-normal text-xs block">(<?php echo e(number_format($amount * 2650, 0)); ?> CDF)</span>
                                <?php elseif($currency === 'CDF'): ?>
                                    <span class="text-gray-400 font-normal text-xs block">(<?php echo e(number_format($amount / 2650, 2)); ?> USD)</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-gray-400 font-normal text-xs block">(<?php echo e(number_format($amount * 2650, 0)); ?> CDF)</span>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if(isset($provider) && $provider): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Opérateur</span>
                        <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($provider); ?></span>
                    </div>
                <?php endif; ?>

                <div class="flex justify-between">
                    <span class="text-gray-500">Date</span>
                    <span class="font-semibold text-gray-900 dark:text-white"><?php echo e(now()->format('d/m/Y à H:i')); ?></span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Statut</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Échec
                    </span>
                </div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-900/20 rounded-xl p-4 mb-5 text-sm text-yellow-800 dark:text-yellow-200 text-left">
                <h6 class="font-semibold mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Causes possibles :
                </h6>
                <ul class="space-y-1 text-yellow-700 dark:text-yellow-300 list-disc list-inside text-xs">
                    <li>Solde insuffisant sur votre compte Mobile Money</li>
                    <li>Numéro de téléphone invalide ou inactif</li>
                    <li>Délai d'attente de l'opérateur dépassé</li>
                    <li>Transaction refusée par l'opérateur</li>
                    <li>Problème de connexion réseau</li>
                </ul>
            </div>

            <div class="flex flex-col gap-2.5 mt-5">
                <a href="<?php echo e(route('cart.pay')); ?>" class="w-full px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors text-center">
                    Réessayer le Paiement
                </a>
                <a href="<?php echo e(route('support.index')); ?>" class="w-full px-5 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors text-center">
                    Contacter le Support
                </a>
                <a href="<?php echo e(route('dashboard')); ?>" class="w-full px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-center">
                    Retour au Dashboard
                </a>
            </div>

            <p class="mt-5 text-xs text-gray-400 dark:text-gray-500">
                Besoin d'aide ? Notre équipe support est disponible 24/7
            </p>
        </div>
    </div>
</div>

<style>
.animate-slide-up {
    animation: slideUp 0.6s ease-out;
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

.animate-pulse-error {
    animation: pulse-error 1.5s ease-in-out infinite;
}

.animate-bounce-error {
    animation: bounce-error 0.8s ease-in-out;
}

.shimmer::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(220, 38, 38, 0.03), transparent);
    animation: shimmer 3s infinite;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(50px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

@keyframes pulse-error {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.9; }
}

@keyframes bounce-error {
    0% { transform: scale(0) rotate(-180deg); opacity: 0; }
    50% { transform: scale(1.2) rotate(0deg); }
    100% { transform: scale(1) rotate(0deg); opacity: 1; }
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/payments/error.blade.php ENDPATH**/ ?>