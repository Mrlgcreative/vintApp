

<?php $__env->startSection('title', 'Paiement AfribaPay - VintApp'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        
        <div class="flex items-center justify-between mb-6 pb-4 border-b">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Paiement Mobile Money</h1>
                <p class="text-sm text-gray-600 mt-1">Powered by AfribaPay</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-600">Montant à payer</div>
                <div class="text-3xl font-bold text-primary">
                    <?php echo e(number_format($totalAmount, 0, ',', ' ')); ?> 
                    <span class="text-lg"><?php echo e($currency); ?></span>
                </div>
            </div>
        </div>

        
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h2 class="font-semibold text-gray-900 mb-3">📦 Votre commande</h2>
            <div class="space-y-2">
                <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600"><?php echo e($item['name']); ?> × <?php echo e($item['quantity']); ?></span>
                    <span class="font-medium"><?php echo e(number_format($item['price'] * $item['quantity'], 0, ',', ' ')); ?> <?php echo e($currency); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between text-sm pt-2 border-t">
                    <span class="text-gray-900 font-semibold">Total</span>
                    <span class="text-lg font-bold text-primary"><?php echo e(number_format($totalAmount, 0, ',', ' ')); ?> <?php echo e($currency); ?></span>
                </div>
            </div>
        </div>

        
        <form action="<?php echo e(route('payments.afribapay.initiate')); ?>" method="POST" id="afribapay-form">
            <?php echo csrf_field(); ?>
            
            
            <?php if($errors->any()): ?>
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-red-900 text-sm">Erreurs de validation</h3>
                        <ul class="mt-2 text-xs text-red-700 list-disc list-inside">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            
            <input type="hidden" name="cart_items" value="<?php echo e(json_encode($cartItems)); ?>">
            <input type="hidden" name="total_amount" value="<?php echo e($totalAmount); ?>">
            <input type="hidden" name="delivery_address_id" value="<?php echo e($deliveryAddressId); ?>">
            <input type="hidden" name="currency" value="<?php echo e($currency); ?>">

            <div class="space-y-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        🌍 Pays
                    </label>
                    <div class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-700">
                        🇨🇩 République Démocratique du Congo (RDC)
                    </div>
                    <input type="hidden" name="country_code" value="CD">
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        📱 Opérateur Mobile Money
                    </label>
                    <div id="operator_display" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                        Saisissez votre numéro pour détecter l'opérateur
                    </div>
                    <input type="hidden" name="operator_code" id="operator_code">
                </div>

                
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                        📞 Numéro de téléphone Mobile Money
                    </label>
                    <input type="tel" name="phone_number" id="phone_number" required
                           placeholder="Ex: 0812345678 ou 243812345678"
                           pattern="[0-9]{9,12}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Entrez votre numéro (avec ou sans indicatif 243)</p>
                </div>

                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-blue-900 text-sm">Paiement sécurisé AfribaPay</h3>
                            <p class="text-xs text-blue-700 mt-1">
                                Vous recevrez une notification sur votre téléphone pour confirmer le paiement.
                                <?php if(config('services.afribapay.environment') === 'sandbox'): ?>
                                <br><span class="font-semibold">Mode TEST : Utilisez 243120000011 pour réussir</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                
                <button type="submit" id="submit-btn"
                        class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Payer <?php echo e(number_format($totalAmount, 0, ',', ' ')); ?> <?php echo e($currency); ?>

                    </span>
                </button>

                <div class="text-center">
                    <a href="<?php echo e(route('cart.checkout')); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                        ← Retour au checkout
                    </a>
                </div>
            </div>
        </form>
    </div>

    
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 mb-3">Opérateurs Mobile Money acceptés</p>
        <div class="flex justify-center items-center space-x-3 flex-wrap gap-2">
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 Airtel Money</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 Mpesa</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 Orange Money</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 Vodacom</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 MTN Money</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 Moov Money</div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Préfixes des opérateurs en RDC
const operatorPrefixes = {
    'vodacom': ['081', '082', '089', '24381', '24382', '24389'],
    'airtel': ['085', '089', '097', '098', '099', '24385', '24389', '24397', '24398', '24399'],
    'orange': ['084', '085', '089', '24384', '24385', '24389'],
    'afrimoney': ['090', '091', '24390', '24391']
};

const operatorNames = {
    'vodacom': 'Vodacom M-Pesa',
    'airtel': 'Airtel Money',
    'orange': 'Orange Money',
    'afrimoney': 'Africell Money'
};

function detectOperator(phoneNumber) {
    // Nettoyer le numéro (enlever espaces, tirets, etc.)
    const cleanPhone = phoneNumber.replace(/[\s\-+]/g, '');
    
    // Vérifier chaque opérateur
    for (const [operator, prefixes] of Object.entries(operatorPrefixes)) {
        for (const prefix of prefixes) {
            if (cleanPhone.startsWith(prefix)) {
                return operator;
            }
        }
    }
    
    return null;
}

function updateOperatorDisplay() {
    const phoneInput = document.getElementById('phone_number');
    const operatorDisplay = document.getElementById('operator_display');
    const operatorCodeInput = document.getElementById('operator_code');
    const phoneNumber = phoneInput.value;

    if (phoneNumber.length >= 3) {
        const detectedOperator = detectOperator(phoneNumber);
        
        if (detectedOperator) {
            operatorDisplay.textContent = operatorNames[detectedOperator];
            operatorDisplay.classList.remove('text-gray-500', 'text-red-500');
            operatorDisplay.classList.add('text-green-600', 'font-semibold');
            operatorCodeInput.value = detectedOperator;
        } else {
            operatorDisplay.textContent = 'Opérateur non reconnu - vérifiez le numéro';
            operatorDisplay.classList.remove('text-gray-500', 'text-green-600', 'font-semibold');
            operatorDisplay.classList.add('text-red-500');
            operatorCodeInput.value = '';
        }
    } else {
        operatorDisplay.textContent = 'Saisissez votre numéro pour détecter l\'opérateur';
        operatorDisplay.classList.remove('text-green-600', 'font-semibold', 'text-red-500');
        operatorDisplay.classList.add('text-gray-500');
        operatorCodeInput.value = '';
    }
}

// Détecter l'opérateur à chaque saisie
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone_number');
    phoneInput.addEventListener('input', updateOperatorDisplay);
    phoneInput.addEventListener('change', updateOperatorDisplay);
});

// Validation du formulaire
document.getElementById('afribapay-form').addEventListener('submit', function(e) {
    const phone = document.getElementById('phone_number').value;
    const operator = document.getElementById('operator_code').value;
    
    console.log('Form submit - Phone:', phone, 'Operator:', operator); // Debug
    
    if (!phone || phone.length < 9) {
        e.preventDefault();
        alert('Veuillez saisir un numéro de téléphone valide (minimum 9 chiffres)');
        return false;
    }
    
    if (!operator) {
        e.preventDefault();
        alert('Opérateur non détecté. Vérifiez que votre numéro commence par un préfixe valide (081, 082, 084, 085, 089, 090, 097, 098, 099)');
        return false;
    }
    
    // Tout est ok, afficher le loader
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Traitement en cours...</span>';
    
    return true;
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/payments/afribapay-form.blade.php ENDPATH**/ ?>