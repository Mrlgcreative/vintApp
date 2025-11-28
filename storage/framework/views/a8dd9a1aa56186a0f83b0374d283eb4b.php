<?php $__env->startSection('title', 'Paiement - ' . (isset($order) ? "Commande #{$order->order_number}" : 'Rechargement wallet')); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        
        <div class="flex items-center justify-between mb-6 pb-4 border-b">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <?php if(isset($order)): ?>
                        Paiement commande
                    <?php else: ?>
                        Rechargement wallet
                    <?php endif; ?>
                </h1>
                <?php if(isset($order)): ?>
                    <p class="text-sm text-gray-600 mt-1">Commande #<?php echo e($order->order_number); ?></p>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-600">Montant à payer</div>
                <div class="text-3xl font-bold text-primary">
                    <?php echo e(number_format($payment->amount, 0, ',', ' ')); ?> 
                    <span class="text-lg"><?php echo e($payment->currency); ?></span>
                </div>
            </div>
        </div>

        
        <?php if(isset($order)): ?>
        <div class="mb-6 space-y-3">
            <h2 class="font-semibold text-gray-900 mb-3">Détails de la commande</h2>
            
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Vendeur</span>
                <span class="font-medium"><?php echo e($order->seller->name); ?></span>
            </div>
            
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Article</span>
                <span class="font-medium"><?php echo e($order->item->name); ?></span>
            </div>
            
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Prix unitaire</span>
                <span class="font-medium"><?php echo e(number_format($order->item_price, 0, ',', ' ')); ?> XOF</span>
            </div>
            
            <?php if($order->quantity > 1): ?>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Quantité</span>
                <span class="font-medium"><?php echo e($order->quantity); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="flex justify-between text-sm pt-3 border-t">
                <span class="text-gray-900 font-semibold">Total</span>
                <span class="text-lg font-bold text-primary"><?php echo e(number_format($order->total_amount, 0, ',', ' ')); ?> XOF</span>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-blue-900 text-sm">Paiement sécurisé</h3>
                    <p class="text-xs text-blue-700 mt-1">
                        Votre paiement est sécurisé par CinetPay. Vous pouvez payer par carte bancaire ou Mobile Money.
                    </p>
                </div>
            </div>
        </div>

        
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-gray-600">ID Transaction</div>
                    <div class="font-mono text-xs mt-1"><?php echo e($payment->transaction_id); ?></div>
                </div>
                <div>
                    <div class="text-gray-600">Date</div>
                    <div class="font-medium mt-1"><?php echo e($payment->created_at->format('d/m/Y H:i')); ?></div>
                </div>
            </div>
        </div>

        
        <div class="space-y-4">
            <div id="cinetpay-payment-form">
                
            </div>

            <div class="text-center">
                <?php if(isset($order)): ?>
                    <a href="<?php echo e(route('orders.show', $order)); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                        Retour à la commande
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('wallet.index')); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                        Retour au wallet
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 mb-3">Moyens de paiement acceptés</p>
        <div class="flex justify-center items-center space-x-4 flex-wrap">
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">💳 Visa</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">💳 Mastercard</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 Orange Money</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 MTN Mobile Money</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 Moov Money</div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    /* Personnaliser le formulaire CinetPay */
    #cinetpay-payment-form {
        min-height: 400px;
    }
    
    /* Style du SDK CinetPay */
    .cp-seamless-container {
        border-radius: 0.5rem;
        overflow: hidden;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.cinetpay.com/seamless/main.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration du paiement CinetPay Seamless
        CinetPay.setConfig({
            apikey: '<?php echo e(config("services.cinetpay.api_key")); ?>',
            site_id: '<?php echo e(config("services.cinetpay.site_id")); ?>',
            notify_url: '<?php echo e(route("payments.cinetpay.notify")); ?>',
            mode: '<?php echo e(config("services.cinetpay.platform")); ?>'
        });

        // Démarrer le paiement
        CinetPay.getCheckout({
            transaction_id: '<?php echo e($payment->transaction_id); ?>',
            amount: <?php echo e($payment->amount); ?>,
            currency: '<?php echo e($payment->currency); ?>',
            channels: 'ALL',
            description: '<?php echo e($payment->designation); ?>',
            customer_name: '<?php echo e(auth()->user()->name); ?>',
            customer_email: '<?php echo e(auth()->user()->email); ?>',
            <?php if(auth()->user()->phone): ?>
            customer_phone_number: '<?php echo e(auth()->user()->phone); ?>',
            <?php endif; ?>
            customer_country: 'CD',
            customer_city: '<?php echo e(auth()->user()->city ?? "Kinshasa"); ?>',
            customer_zip_code: '00000',
            
            // Callbacks
            onComplete: function(data) {
                console.log('Paiement complété:', data);
                
                // Rediriger vers la page de retour
                window.location.href = '<?php echo e(route("payments.cinetpay.return")); ?>?transaction_id=<?php echo e($payment->transaction_id); ?>';
            },
            
            onError: function(error) {
                console.error('Erreur de paiement:', error);
                
                // Afficher un message d'erreur
                alert('Une erreur est survenue lors du paiement. Veuillez réessayer.');
            }
        });

        // Personnaliser l'affichage
        CinetPay.waitResponse(function(data) {
            if (data.status == "REFUSED") {
                alert('Paiement refusé');
                <?php if(isset($order)): ?>
                window.location.href = '<?php echo e(route("orders.show", $order ?? 0)); ?>';
                <?php else: ?>
                window.location.href = '<?php echo e(route("wallet.index")); ?>';
                <?php endif; ?>
            } else if (data.status == "ACCEPTED") {
                alert('Paiement accepté avec succès !');
                window.location.href = '<?php echo e(route("payments.cinetpay.return")); ?>?transaction_id=<?php echo e($payment->transaction_id); ?>';
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/payments/checkout.blade.php ENDPATH**/ ?>