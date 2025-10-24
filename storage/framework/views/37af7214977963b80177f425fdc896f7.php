

<?php $__env->startPush('scripts'); ?>
<!-- jsQR Library pour scanner QR codes -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
// Vérifier que jsQR est chargé
console.log('jsQR chargé ?', typeof jsQR !== 'undefined' ? '✅ OUI' : '❌ NON');
if (typeof jsQR === 'undefined') {
    console.error('❌ jsQR n\'a pas pu être chargé depuis le CDN');
    console.log('Tentative de chargement depuis un CDN alternatif...');
    
    // Fallback vers unpkg
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/jsqr@1.4.0/dist/jsQR.js';
    script.onload = () => console.log('✅ jsQR chargé depuis unpkg');
    script.onerror = () => console.error('❌ Échec chargement jsQR depuis unpkg aussi');
    document.head.appendChild(script);
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Mes Commandes
                </h1>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success" onclick="showScanInstructions()">
                        <i class="fas fa-qrcode me-2"></i>
                        Scanner QR Code
                    </button>
                    <a href="<?php echo e(route('orders.my-sales')); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-store me-2"></i>
                        Mes Ventes
                    </a>
                </div>
            </div>

            <?php if($orders->count() > 0): ?>
                <div class="row g-4">
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 order-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><?php echo e($order->order_number); ?></h6>
                                    <span class="badge <?php echo e($order->status_badge_class); ?>">
                                        <?php echo e($order->status_text); ?>

                                    </span>
                                </div>

                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <?php if($order->item->images && count($order->item->images) > 0): ?>
                                                <img src="<?php echo e(asset('storage/' . $order->item->images[0])); ?>" 
                                                     class="img-thumbnail" 
                                                     alt="<?php echo e($order->item->name); ?>"
                                                     style="height: 80px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="height: 80px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-8">
                                            <h6 class="card-title"><?php echo e(Str::limit($order->item->name, 40)); ?></h6>
                                            <p class="text-muted mb-1">
                                                <small>Quantité: <?php echo e($order->quantity); ?></small>
                                            </p>
                                            <p class="text-primary fw-bold mb-0">
                                                <?php echo e($order->formatted_total_price); ?>

                                            </p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Vendeur</small>
                                            <div class="fw-bold"><?php echo e($order->item->user->name); ?></div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Date</small>
                                            <div class="fw-bold"><?php echo e($order->created_at->format('d/m/Y')); ?></div>
                                        </div>
                                    </div>

                                    <?php if($order->deliveryAddress): ?>
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                Livraison
                                            </small>
                                            <div class="fw-bold"><?php echo e($order->deliveryAddress->city); ?></div>
                                            <small class="text-muted"><?php echo e(Str::limit($order->deliveryAddress->address, 30)); ?></small>
                                        </div>
                                    <?php elseif($order->shipping_city && $order->shipping_city !== 'À définir'): ?>
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                Livraison
                                            </small>
                                            <div class="fw-bold"><?php echo e($order->shipping_city); ?></div>
                                            <?php if($order->shipping_address && $order->shipping_address !== 'À définir'): ?>
                                                <small class="text-muted"><?php echo e(Str::limit($order->shipping_address, 30)); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-grid gap-2">
                                        <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-2"></i>
                                            Voir détails
                                        </a>
                                        
                                        <?php if($order->scan_token): ?>
                                            <a href="<?php echo e(route('orders.scan', $order->scan_token)); ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-qrcode me-2"></i>
                                                Scanner / Confirmer réception
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if($order->status === 'pending'): ?>
                                            <button class="btn btn-danger btn-sm" 
                                                    data-order-id="<?php echo e($order->id); ?>"
                                                    onclick="cancelOrder(this.dataset.orderId)">
                                                <i class="fas fa-times me-2"></i>
                                                Annuler
                                            </button>
                                        <?php endif; ?>

                                        <?php if(in_array($order->status, ['shipped', 'delivered']) && !$order->confirmed_by_buyer_at && !$order->scan_token): ?>
                                            <button class="btn btn-success btn-sm" 
                                                    data-order-id="<?php echo e($order->id); ?>"
                                                    onclick="confirmDelivery(this.dataset.orderId)">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Commande Reçue
                                            </button>
                                        <?php endif; ?>

                                        <?php if($order->confirmed_by_buyer_at): ?>
                                            <div class="alert alert-success mb-0 py-2" role="alert">
                                                <i class="fas fa-check-circle me-1"></i>
                                                <small>Réception confirmée le <?php echo e($order->confirmed_by_buyer_at->format('d/m/Y')); ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <?php echo e($orders->links()); ?>

                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucune commande</h4>
                    <p class="text-muted">Vous n'avez pas encore passé de commande.</p>
                    <a href="<?php echo e(route('items.index')); ?>" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>
                        Découvrir des articles
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function cancelOrder(orderId) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
        fetch(`/orders/${orderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Erreur lors de l\'annulation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }
}

function confirmDelivery(orderId) {
    // Demander une confirmation avec possibilité d'ajouter un commentaire
    const note = prompt('Confirmez-vous avoir reçu votre commande ?\n\nVous pouvez ajouter un commentaire (optionnel) :');
    
    if (note !== null) { // L'utilisateur n'a pas cliqué sur Annuler
        fetch(`/orders/${orderId}/confirm-delivery`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                note: note || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.error || 'Erreur lors de la confirmation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la confirmation');
        });
    }
}

async function checkCameraPermission() {
    try {
        // Vérifier si l'API est disponible
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            return {
                granted: false,
                error: 'NotSupportedError',
                message: 'Votre navigateur ne supporte pas l\'accès à la caméra. Utilisez Chrome, Firefox ou Safari récent.'
            };
        }
        
        // Essayer d'accéder à la caméra
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        
        // Si succès, arrêter immédiatement le stream
        stream.getTracks().forEach(track => track.stop());
        
        return { granted: true };
    } catch (err) {
        return {
            granted: false,
            error: err.name,
            message: err.message
        };
    }
}

function showScanInstructions() {
    // Créer le modal avec scanner intégré
    const modalHtml = `
        <div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="scanModalLabel">
                            <i class="fas fa-qrcode me-2"></i>
                            Scanner un QR Code
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="stopScanner()"></button>
                    </div>
                    <div class="modal-body">
                        <div id="scanner-container" class="text-center">
                            <div id="scanner-status" class="mb-3">
                                <i class="fas fa-spinner fa-spin fa-2x text-success"></i>
                                <p class="mt-2">Initialisation de la caméra...</p>
                            </div>
                            <video id="qr-video" style="width: 100%; max-width: 500px; border-radius: 10px; display: none;"></video>
                            <canvas id="qr-canvas" style="display: none;"></canvas>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Instructions :</strong>
                            <ul class="mb-0 mt-2">
                                <li>Autorisez l'accès à la caméra quand le navigateur vous le demande</li>
                                <li>Pointez la caméra vers le QR code sur la facture</li>
                                <li>La détection se fait automatiquement</li>
                            </ul>
                        </div>
                        
                        <div id="scan-result" class="alert alert-success mt-3" style="display: none;">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>QR Code détecté !</strong> Redirection en cours...
                        </div>
                        
                        <div id="scan-error" class="alert alert-danger mt-3" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Erreur :</strong> <span id="error-message"></span>
                            
                            <div class="d-grid gap-2 mt-3">
                                <button type="button" class="btn btn-warning" onclick="retryScanner()">
                                    <i class="fas fa-redo me-2"></i>
                                    Réessayer après avoir autorisé
                                </button>
                            </div>
                        </div>
                        
                        <div id="alternative-options" style="display: none;">
                            <hr>
                            <h6 class="text-center mb-3">Autres options de confirmation</h6>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Astuce :</strong> Cliquez sur le bouton "Scanner / Confirmer réception" sur votre commande ci-dessous.
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary" onclick="scrollToOrders()">
                                    <i class="fas fa-list me-2"></i>
                                    Voir mes commandes
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="stopScanner()">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter le modal au DOM s'il n'existe pas déjà
    if (!document.getElementById('scanModal')) {
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    // Afficher le modal
    const modal = new bootstrap.Modal(document.getElementById('scanModal'));
    modal.show();
    
    // Démarrer le scanner après l'ouverture du modal
    document.getElementById('scanModal').addEventListener('shown.bs.modal', function() {
        startQRScanner();
    }, { once: true });
}

let videoStream = null;
let scanningInterval = null;

function startQRScanner() {
    const video = document.getElementById('qr-video');
    const canvas = document.getElementById('qr-canvas');
    const context = canvas.getContext('2d');
    const statusDiv = document.getElementById('scanner-status');
    const resultDiv = document.getElementById('scan-result');
    const errorDiv = document.getElementById('scan-error');
    
    // VÉRIFICATION CRITIQUE : jsQR est-il chargé ?
    if (typeof jsQR === 'undefined') {
        console.error('❌ ERREUR CRITIQUE : jsQR n\'est pas chargé !');
        statusDiv.style.display = 'none';
        errorDiv.style.display = 'block';
        document.getElementById('error-message').innerHTML = `
            <strong>Erreur de chargement de la bibliothèque jsQR</strong>
            <div class="mt-2">
                <p>La bibliothèque de scan de QR code n'a pas pu être chargée.</p>
                <ol>
                    <li>Vérifiez votre connexion internet</li>
                    <li>Rechargez la page (F5)</li>
                    <li>Si le problème persiste, utilisez le bouton "Scanner / Confirmer réception" sur vos commandes</li>
                </ol>
            </div>
        `;
        document.getElementById('alternative-options').style.display = 'block';
        return;
    }
    
    console.log('✅ jsQR est chargé et prêt');
    
    // Vérifier l'API getUserMedia
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        console.error('❌ getUserMedia n\'est pas supporté');
        statusDiv.style.display = 'none';
        errorDiv.style.display = 'block';
        document.getElementById('error-message').innerHTML = `
            <strong>Navigateur non compatible</strong>
            <div class="mt-2">
                <p>Votre navigateur ne supporte pas l'accès à la caméra.</p>
                <p>Utilisez Chrome, Firefox, Safari ou Edge récent.</p>
            </div>
        `;
        document.getElementById('alternative-options').style.display = 'block';
        return;
    }
    
    console.log('✅ getUserMedia est supporté');
    console.log('🎥 Demande d\'accès à la caméra...');
    
    // Demander l'accès à la caméra
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            facingMode: 'environment' // Utiliser la caméra arrière sur mobile
        } 
    })
    .then(function(stream) {
        videoStream = stream;
        video.srcObject = stream;
        video.setAttribute('playsinline', true); // iOS
        video.play();
        
        statusDiv.style.display = 'none';
        video.style.display = 'block';
        
        console.log('✅ Caméra activée, démarrage du scan...');
        console.log('📹 Résolution vidéo:', video.videoWidth + 'x' + video.videoHeight);
        
        let scanAttempts = 0;
        
        // Démarrer la détection de QR code
        scanningInterval = setInterval(() => {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.height = video.videoHeight;
                canvas.width = video.videoWidth;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                
                // Vérifier que jsQR est toujours disponible
                if (typeof jsQR === 'undefined') {
                    console.error('❌ jsQR n\'est plus disponible pendant le scan');
                    stopScanner();
                    return;
                }
                
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                scanAttempts++;
                if (scanAttempts % 10 === 0) {
                    console.log('🔍 Scan en cours... tentative', scanAttempts);
                }
                
                if (code) {
                    // QR Code détecté
                    console.log('✅ QR CODE DÉTECTÉ !');
                    console.log('📝 Données:', code.data);
                    console.log('📍 Position:', code.location);
                    
                    stopScanner();
                    resultDiv.style.display = 'block';
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i><strong>QR Code détecté !</strong><br><small>' + code.data + '</small>';
                    
                    // Rediriger vers l'URL du QR code
                    setTimeout(() => {
                        console.log('🔄 Redirection vers:', code.data);
                        window.location.href = code.data;
                    }, 1500);
                }
            }
        }, 300);
    })
    .catch(function(err) {
        console.error('=== ERREUR CAMÉRA ===');
        console.error('Type:', err.name);
        console.error('Message:', err.message);
        console.error('Objet complet:', err);
        console.error('URL actuelle:', window.location.href);
        console.error('Protocol:', window.location.protocol);
        console.error('Hostname:', window.location.hostname);
        
        statusDiv.style.display = 'none';
        errorDiv.style.display = 'block';
        
        let errorMessage = '';
        let detailedInfo = '';
        
        if (err.name === 'NotAllowedError') {
            errorMessage = 'L\'accès à la caméra a été refusé.';
            detailedInfo = `
                <div class="mt-3 p-3 bg-light rounded">
                    <strong>Solutions possibles :</strong>
                    <ol class="mb-0 mt-2">
                        <li>Cliquez sur l'icône 🔒 ou ⓘ à gauche de l'URL dans la barre d'adresse</li>
                        <li>Cherchez "Caméra" dans les permissions</li>
                        <li>Changez en "Autoriser"</li>
                        <li>Rechargez la page (F5)</li>
                        <li>Cliquez à nouveau sur "Réessayer"</li>
                    </ol>
                    <div class="alert alert-info mt-2 mb-0">
                        <small><strong>Info de débogage :</strong><br>
                        URL: ${window.location.href}<br>
                        Protocol: ${window.location.protocol}<br>
                        Hostname: ${window.location.hostname}</small>
                    </div>
                </div>
            `;
        } else if (err.name === 'NotFoundError') {
            errorMessage = 'Aucune caméra détectée sur votre appareil.';
        } else if (err.name === 'NotSupportedError' || err.name === 'TypeError') {
            errorMessage = 'Votre navigateur ne supporte pas l\'accès à la caméra.';
            detailedInfo = '<div class="mt-2"><small>Utilisez Chrome, Firefox ou Safari récent.</small></div>';
        } else if (err.name === 'NotReadableError' || err.name === 'AbortError') {
            errorMessage = 'La caméra est déjà utilisée par une autre application.';
            detailedInfo = '<div class="mt-2"><small>Fermez les autres onglets/applications qui utilisent la caméra.</small></div>';
        } else {
            errorMessage = 'Erreur inattendue: ' + err.message;
            detailedInfo = '<div class="mt-2"><small>Type d\'erreur: ' + err.name + '</small></div>';
        }
        
        document.getElementById('error-message').innerHTML = errorMessage + detailedInfo;
        document.getElementById('alternative-options').style.display = 'block';
    });
}

function stopScanner() {
    // Arrêter le flux vidéo
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }
    
    // Arrêter l'intervalle de scan
    if (scanningInterval) {
        clearInterval(scanningInterval);
        scanningInterval = null;
    }
}

function retryScanner() {
    // Réinitialiser l'affichage
    document.getElementById('scan-error').style.display = 'none';
    document.getElementById('alternative-options').style.display = 'none';
    document.getElementById('scanner-status').style.display = 'block';
    document.getElementById('scanner-status').innerHTML = '<i class="fas fa-spinner fa-spin fa-2x text-success"></i><p class="mt-2">Initialisation de la caméra...</p>';
    
    // Réessayer après un court délai
    setTimeout(() => {
        startQRScanner();
    }, 500);
}

function scrollToOrders() {
    // Fermer le modal s'il existe
    const modal = bootstrap.Modal.getInstance(document.getElementById('qrInfoModal'));
    if (modal) {
        modal.hide();
    }
    
    // Scroller vers le haut des commandes
    setTimeout(() => {
        window.scrollTo({
            top: document.querySelector('.container-fluid').offsetTop,
            behavior: 'smooth'
        });
    }, 300);
}
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/orders/index.blade.php ENDPATH**/ ?>