

<?php $__env->startSection('title', 'Proposer une livraison locale'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-truck mr-2 text-blue-500"></i>Proposer une livraison locale
            </h1>
            <p class="text-gray-600">
                Proposez une livraison locale pour connecter directement avec votre acheteur et offrir une expérience personnalisée.
            </p>
        </div>

        <!-- Formulaire de proposition -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="<?php echo e(route('local-delivery.propose')); ?>" method="POST" id="deliveryForm">
                <?php echo csrf_field(); ?>
                
                <!-- Sélection de la commande -->
                <div class="mb-6">
                    <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-shopping-bag mr-2"></i>Commande
                    </label>
                    <select name="order_id" id="order_id" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Sélectionnez une commande...</option>
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($order->id); ?>" 
                                    data-buyer-id="<?php echo e($order->buyer_id); ?>"
                                    data-buyer-name="<?php echo e($order->buyer->name); ?>"
                                    data-buyer-email="<?php echo e($order->buyer->email); ?>"
                                    data-buyer-address="<?php echo e($order->buyer_address_data['address']); ?>"
                                    data-buyer-phone="<?php echo e($order->buyer_address_data['phone']); ?>"
                                    data-buyer-latitude="<?php echo e($order->buyer_address_data['latitude']); ?>"
                                    data-buyer-longitude="<?php echo e($order->buyer_address_data['longitude']); ?>"
                                    data-has-coordinates="<?php echo e($order->buyer_address_data['has_coordinates'] ? 'true' : 'false'); ?>"
                                    data-source="<?php echo e($order->buyer_address_data['source']); ?>"
                                    <?php echo e(request('order_id') == $order->id ? 'selected' : ''); ?>>
                                Commande #<?php echo e($order->order_number); ?> - <?php echo e($order->buyer->name); ?>

                                <?php if($order->buyer_address_data['has_coordinates']): ?>
                                    <small class="text-green-600">(📍 GPS disponible)</small>
                                <?php else: ?>
                                    <small class="text-orange-600">(📍 GPS requis)</small>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Type de livraison -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        <i class="fas fa-route mr-2"></i>Type de livraison
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative">
                            <input type="radio" name="delivery_type" value="pickup" class="sr-only peer" required>
                            <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                                <div class="text-center">
                                    <i class="fas fa-store text-3xl text-gray-600 mb-2 peer-checked:text-blue-500"></i>
                                    <h3 class="font-medium text-gray-900">Récupération</h3>
                                    <p class="text-sm text-gray-600 mt-1">L'acheteur vient récupérer chez vous</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative">
                            <input type="radio" name="delivery_type" value="meetup" class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                                <div class="text-center">
                                    <i class="fas fa-handshake text-3xl text-gray-600 mb-2 peer-checked:text-blue-500"></i>
                                    <h3 class="font-medium text-gray-900">Point de rencontre</h3>
                                    <p class="text-sm text-gray-600 mt-1">Rendez-vous à mi-chemin</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative">
                            <input type="radio" name="delivery_type" value="hand_delivery" class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                                <div class="text-center">
                                    <i class="fas fa-home text-3xl text-gray-600 mb-2 peer-checked:text-blue-500"></i>
                                    <h3 class="font-medium text-gray-900">Livraison à domicile</h3>
                                    <p class="text-sm text-gray-600 mt-1">Vous livrez chez l'acheteur</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Informations vendeur -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-user-tie mr-2"></i>Vos informations
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="seller_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Votre adresse
                            </label>
                            <textarea name="seller_address" id="seller_address" rows="3" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Votre adresse complète..."></textarea>
                        </div>
                        <div>
                            <label for="seller_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Votre téléphone
                            </label>
                            <input type="tel" name="seller_phone" id="seller_phone" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="+243999123456">
                        </div>
                    </div>
                    
                    <!-- Coordonnées GPS vendeur -->
                    <div class="mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="seller_latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                    Latitude
                                </label>
                                <input type="number" name="seller_latitude" id="seller_latitude" step="any" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="-4.441931">
                            </div>
                            <div>
                                <label for="seller_longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                    Longitude
                                </label>
                                <input type="number" name="seller_longitude" id="seller_longitude" step="any" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="15.266293">
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" onclick="getCurrentLocation('seller')" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Utiliser ma position actuelle
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Informations acheteur -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-user mr-2"></i>Informations de l'acheteur
                        <span id="auto-fill-indicator" class="hidden ml-2 text-sm text-green-600 bg-green-100 px-2 py-1 rounded-full">
                            <i class="fas fa-check mr-1"></i><span id="auto-fill-source">Remplies automatiquement</span>
                        </span>
                    </h3>
                    <div id="buyer-info-note" class="hidden mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span id="buyer-info-message">Ces informations sont récupérées automatiquement depuis l'adresse de livraison enregistrée lors de la commande.</span>
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="buyer_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Adresse de l'acheteur
                            </label>
                            <textarea name="buyer_address" id="buyer_address" rows="3" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Adresse de livraison..."></textarea>
                        </div>
                        <div>
                            <label for="buyer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone de l'acheteur
                            </label>
                            <input type="tel" name="buyer_phone" id="buyer_phone" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="+243999654321">
                        </div>
                    </div>
                    
                    <!-- Coordonnées GPS acheteur -->
                    <div class="mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="buyer_latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                    Latitude
                                </label>
                                <input type="number" name="buyer_latitude" id="buyer_latitude" step="any" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="-4.432774">
                            </div>
                            <div>
                                <label for="buyer_longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                    Longitude
                                </label>
                                <input type="number" name="buyer_longitude" id="buyer_longitude" step="any" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="15.251050">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Point de rencontre (visible seulement si meetup est sélectionné) -->
                <div id="meetup_section" class="mb-6 hidden">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-map-pin mr-2"></i>Point de rencontre
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="meetup_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Adresse du point de rencontre
                            </label>
                            <textarea name="meetup_address" id="meetup_address" rows="2"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Lieu de rendez-vous convenu..."></textarea>
                        </div>
                        <div>
                            <label for="meetup_latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                Latitude
                            </label>
                            <input type="number" name="meetup_latitude" id="meetup_latitude" step="any"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="meetup_longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                Longitude
                            </label>
                            <input type="number" name="meetup_longitude" id="meetup_longitude" step="any"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Informations calculées -->
                <div id="delivery_info" class="mb-6 p-4 bg-gray-50 rounded-lg hidden">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        <i class="fas fa-calculator mr-2"></i>Informations de livraison
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Distance:</span>
                            <span id="calculated_distance" class="text-blue-600 font-medium">-- km</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Frais estimés:</span>
                            <span id="calculated_fee" class="text-green-600 font-medium">-- CDF</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Temps estimé:</span>
                            <span id="estimated_time" class="text-orange-600 font-medium">-- min</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between">
                    <a href="<?php echo e(route('orders.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Proposer la livraison
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gestion de la sélection de commande pour remplir automatiquement les informations de l'acheteur
document.getElementById('order_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    
    if (selectedOption.value) {
        // Récupérer les données de l'option sélectionnée
        const buyerAddress = selectedOption.getAttribute('data-buyer-address');
        const buyerPhone = selectedOption.getAttribute('data-buyer-phone');
        const buyerLatitude = selectedOption.getAttribute('data-buyer-latitude');
        const buyerLongitude = selectedOption.getAttribute('data-buyer-longitude');
        const hasCoordinates = selectedOption.getAttribute('data-has-coordinates') === 'true';
        const source = selectedOption.getAttribute('data-source');
        
        // Remplir les champs disponibles
        if (buyerAddress) {
            document.getElementById('buyer_address').value = buyerAddress;
        }
        if (buyerPhone) {
            document.getElementById('buyer_phone').value = buyerPhone;
        }
        if (buyerLatitude && buyerLatitude !== 'null') {
            document.getElementById('buyer_latitude').value = buyerLatitude;
        }
        if (buyerLongitude && buyerLongitude !== 'null') {
            document.getElementById('buyer_longitude').value = buyerLongitude;
        }
        
        // Afficher l'indicateur et le message informatif
        document.getElementById('auto-fill-indicator').classList.remove('hidden');
        document.getElementById('buyer-info-note').classList.remove('hidden');
        
        // Déterminer la source et le message selon les données disponibles
        if (hasCoordinates) {
            document.getElementById('auto-fill-source').textContent = 
                source === 'order' ? 'Adresse de livraison' : 'Adresse par défaut';
            document.getElementById('buyer-info-message').innerHTML = 
                '<i class="fas fa-check-circle text-green-500 mr-2"></i>Coordonnées GPS récupérées automatiquement depuis l\'adresse de l\'acheteur.';
        } else {
            document.getElementById('auto-fill-source').textContent = 'GPS manquant';
            document.getElementById('buyer-info-message').innerHTML = 
                '<i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>Les coordonnées GPS ne sont pas disponibles. Utilisez le bouton "Obtenir ma position" ou saisissez les coordonnées manuellement.';
            
            // Ajouter un bouton d'aide pour obtenir les coordonnées
            const helpButton = document.createElement('button');
            helpButton.type = 'button';
            helpButton.className = 'mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm';
            helpButton.innerHTML = '<i class="fas fa-location-arrow mr-2"></i>Obtenir position acheteur';
            helpButton.onclick = () => getCurrentLocation('buyer');
            
            const messageDiv = document.getElementById('buyer-info-message').parentElement;
            if (!messageDiv.querySelector('button')) {
                messageDiv.appendChild(helpButton);
            }
        }
        
        // Calculer automatiquement la distance si les coordonnées du vendeur sont déjà renseignées
        calculateDistance();
    } else {
        // Vider les champs si aucune commande n'est sélectionnée
        document.getElementById('buyer_address').value = '';
        document.getElementById('buyer_phone').value = '';
        document.getElementById('buyer_latitude').value = '';
        document.getElementById('buyer_longitude').value = '';
        document.getElementById('delivery_info').classList.add('hidden');
        document.getElementById('auto-fill-indicator').classList.add('hidden');
        document.getElementById('buyer-info-note').classList.add('hidden');
    }
});

// Gestion du type de livraison
document.querySelectorAll('input[name="delivery_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const meetupSection = document.getElementById('meetup_section');
        if (this.value === 'meetup') {
            meetupSection.classList.remove('hidden');
            document.getElementById('meetup_address').required = true;
        } else {
            meetupSection.classList.add('hidden');
            document.getElementById('meetup_address').required = false;
        }
    });
});

// Fonction pour obtenir la position GPS actuelle
function getCurrentLocation(type) {
    if (!navigator.geolocation) {
        alert('La géolocalisation n\'est pas supportée par votre navigateur');
        return;
    }

    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Localisation...';
    button.disabled = true;

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            document.getElementById(type + '_latitude').value = lat.toFixed(8);
            document.getElementById(type + '_longitude').value = lng.toFixed(8);
            
            button.innerHTML = originalText;
            button.disabled = false;
            
            // Calculer automatiquement la distance si les deux positions sont renseignées
            calculateDistance();
        },
        function(error) {
            alert('Impossible d\'obtenir votre position : ' + error.message);
            button.innerHTML = originalText;
            button.disabled = false;
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 60000
        }
    );
}

// Fonction pour calculer la distance et les frais
function calculateDistance() {
    const sellerLat = parseFloat(document.getElementById('seller_latitude').value);
    const sellerLng = parseFloat(document.getElementById('seller_longitude').value);
    const buyerLat = parseFloat(document.getElementById('buyer_latitude').value);
    const buyerLng = parseFloat(document.getElementById('buyer_longitude').value);
    
    if (sellerLat && sellerLng && buyerLat && buyerLng) {
        // Formule Haversine pour calculer la distance
        const R = 6371; // Rayon de la Terre en km
        const dLat = (buyerLat - sellerLat) * Math.PI / 180;
        const dLng = (buyerLng - sellerLng) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(sellerLat * Math.PI / 180) * Math.cos(buyerLat * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c;
        
        // Calcul des frais (exemple: 5 CDF/km avec minimum de 5 CDF)
        const fee = Math.max(5, distance * 5);
        
        // Temps estimé (exemple: 30 min + 10 min par km)
        const time = Math.round(30 + (distance * 10));
        
        // Afficher les résultats
        document.getElementById('calculated_distance').textContent = distance.toFixed(1) + ' km';
        document.getElementById('calculated_fee').textContent = Math.round(fee) + ' CDF';
        document.getElementById('estimated_time').textContent = time + ' min';
        document.getElementById('delivery_info').classList.remove('hidden');
    }
}

// Écouter les changements de coordonnées pour recalculer automatiquement
['seller_latitude', 'seller_longitude', 'buyer_latitude', 'buyer_longitude'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateDistance);
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/local-delivery/create.blade.php ENDPATH**/ ?>