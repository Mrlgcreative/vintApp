@extends('app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Finaliser votre commande</h2>
        </div>
    </div>

    @if(empty($cart))
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Votre panier est vide.
        </div>
    @else
    <div class="row">
        <!-- Colonne gauche : Formulaire de livraison -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Informations de livraison</h5>
                </div>
                <div class="card-body p-4">
                    <form id="deliveryForm">
                        @csrf
                        <div class="row">
                            <!-- Nom complet -->
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nom complet <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="{{ Auth::user()->name ?? '' }}" required>
                                <div class="invalid-feedback">Veuillez entrer votre nom complet.</div>
                            </div>

                            <!-- Téléphone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Téléphone <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="+243 800 000 000" required>
                                <div class="invalid-feedback">Veuillez entrer un numéro de téléphone valide.</div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ Auth::user()->email ?? '' }}" required>
                                <div class="invalid-feedback">Veuillez entrer une adresse email valide.</div>
                            </div>

                            <!-- Ville -->
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">
                                    <i class="fas fa-city me-1"></i>Ville <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="city" name="city" required>
                                    <option value="">Sélectionnez une ville</option>
                                    <option value="Kinshasa">Kinshasa</option>
                                    <option value="Lubumbashi">Lubumbashi</option>
                                    <option value="Goma">Goma</option>
                                    <option value="Bukavu">Bukavu</option>
                                    <option value="Matadi">Matadi</option>
                                    <option value="Kolwezi">Kolwezi</option>
                                    <option value="Kisangani">Kisangani</option>
                                    <option value="Autre">Autre ville</option>
                                </select>
                                <div class="invalid-feedback">Veuillez sélectionner une ville.</div>
                            </div>

                            <!-- Commune/Quartier -->
                            <div class="col-md-6 mb-3">
                                <label for="commune" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>Commune/Quartier <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="commune" name="commune" 
                                       placeholder="Ex: Gombe, Lemba, etc." required>
                                <div class="invalid-feedback">Veuillez entrer votre commune ou quartier.</div>
                            </div>

                            <!-- Adresse complète -->
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">
                                    <i class="fas fa-home me-1"></i>Adresse complète <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       placeholder="Avenue, numéro, bâtiment..." required>
                                <div class="invalid-feedback">Veuillez entrer votre adresse complète.</div>
                            </div>

                            <!-- Notes de livraison -->
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">
                                    <i class="fas fa-sticky-note me-1"></i>Instructions de livraison (optionnel)
                                </label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Point de repère, instructions particulières..."></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-check-circle me-2"></i>Confirmer les informations de livraison
                        </button>
                    </form>

                    <!-- Zone d'affichage des informations confirmées -->
                    <div id="deliveryInfoConfirmed" class="mt-4 p-3 bg-light rounded" style="display: none;">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-check-circle me-2"></i>Informations de livraison confirmées
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong><i class="fas fa-user me-2"></i>Nom :</strong> <span id="confirmed_name"></span></p>
                                <p class="mb-2"><strong><i class="fas fa-phone me-2"></i>Téléphone :</strong> <span id="confirmed_phone"></span></p>
                                <p class="mb-2"><strong><i class="fas fa-envelope me-2"></i>Email :</strong> <span id="confirmed_email"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong><i class="fas fa-city me-2"></i>Ville :</strong> <span id="confirmed_city"></span></p>
                                <p class="mb-2"><strong><i class="fas fa-map-marker-alt me-2"></i>Commune :</strong> <span id="confirmed_commune"></span></p>
                                <p class="mb-2"><strong><i class="fas fa-home me-2"></i>Adresse :</strong> <span id="confirmed_address"></span></p>
                            </div>
                            <div class="col-12" id="confirmed_notes_container" style="display: none;">
                                <p class="mb-0"><strong><i class="fas fa-sticky-note me-2"></i>Instructions :</strong> <span id="confirmed_notes"></span></p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" id="editDeliveryBtn">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Récapitulatif de la commande -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Récapitulatif</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <tbody>
                                @foreach($cart as $item)
                                    <tr>
                                        <td class="align-middle" style="width: 60px;">
                                            @if($item['image'])
                                                <img src="{{ asset('storage/' . $item['image']) }}" 
                                                     alt="{{ $item['name'] }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <div class="fw-semibold">{{ $item['name'] }}</div>
                                            <small class="text-muted">Qté: {{ $item['quantity'] }}</small>
                                        </td>
                                        <td class="align-middle text-end fw-bold">
                                            {{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Sous-total :</span>
                        <span class="fw-semibold">{{ number_format($subtotal, 2) }} {{ $item['currency'] ?? '' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>
                            Frais de livraison :
                            <small class="text-muted">({{ $transportFeePercentage }}%)</small>
                        </span>
                        <span class="text-primary fw-semibold">+{{ number_format($transportFee, 2) }} {{ $item['currency'] ?? '' }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="h5 mb-0">Total :</span>
                        <span class="h5 mb-0 text-primary fw-bold">{{ number_format($total, 2) }} {{ $item['currency'] ?? '' }}</span>
                    </div>

                    <!-- Bouton de paiement (masqué par défaut) -->
                    <div id="paymentButtonContainer" style="display: none;">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Vous serez redirigé vers la page de paiement sécurisé Mobile Money.</small>
                        </div>
                        <a href="{{ route('cart.pay') }}" class="btn btn-success btn-lg w-100" id="paymentButton">
                            <i class="fas fa-mobile-alt me-2"></i>Payer par Mobile Money
                        </a>
                    </div>

                    <!-- Message d'instruction (affiché par défaut) -->
                    <div id="deliveryInstructionMessage" class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Veuillez d'abord remplir vos informations de livraison ci-contre.
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .card {
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
    }
    .btn-primary {
        background-color: #6f42c1;
        border-color: #6f42c1;
    }
    .btn-primary:hover {
        background-color: #5a32a3;
        border-color: #5a32a3;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliveryForm = document.getElementById('deliveryForm');
    const deliveryInfoConfirmed = document.getElementById('deliveryInfoConfirmed');
    const paymentButtonContainer = document.getElementById('paymentButtonContainer');
    const deliveryInstructionMessage = document.getElementById('deliveryInstructionMessage');
    const editDeliveryBtn = document.getElementById('editDeliveryBtn');
    let savedAddressId = null;

    // Charger l'adresse par défaut au chargement de la page
    loadDefaultAddress();

    // Fonction pour charger l'adresse par défaut
    function loadDefaultAddress() {
        fetch('/delivery-address/default', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const address = data.data;
                savedAddressId = address.id;
                
                // Pré-remplir le formulaire
                document.getElementById('full_name').value = address.full_name;
                document.getElementById('phone').value = address.phone;
                document.getElementById('email').value = address.email;
                document.getElementById('city').value = address.city;
                document.getElementById('commune').value = address.commune;
                document.getElementById('address').value = address.address;
                document.getElementById('notes').value = address.notes || '';

                // Afficher les informations confirmées
                showConfirmedInfo(address);
            }
        })
        .catch(error => console.error('Erreur lors du chargement de l\'adresse:', error));
    }

    // Fonction pour afficher les informations confirmées
    function showConfirmedInfo(data) {
        document.getElementById('confirmed_name').textContent = data.full_name;
        document.getElementById('confirmed_phone').textContent = data.phone;
        document.getElementById('confirmed_email').textContent = data.email;
        document.getElementById('confirmed_city').textContent = data.city;
        document.getElementById('confirmed_commune').textContent = data.commune;
        document.getElementById('confirmed_address').textContent = data.address;
        
        if (data.notes) {
            document.getElementById('confirmed_notes').textContent = data.notes;
            document.getElementById('confirmed_notes_container').style.display = 'block';
        } else {
            document.getElementById('confirmed_notes_container').style.display = 'none';
        }

        // Masquer le formulaire et afficher les infos confirmées
        deliveryForm.style.display = 'none';
        deliveryInfoConfirmed.style.display = 'block';

        // Afficher le bouton de paiement
        paymentButtonContainer.style.display = 'block';
        deliveryInstructionMessage.style.display = 'none';
    }

    // Gestion de la soumission du formulaire
    deliveryForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validation du formulaire
        if (!deliveryForm.checkValidity()) {
            e.stopPropagation();
            deliveryForm.classList.add('was-validated');
            return;
        }

        // Récupérer les données du formulaire
        const formData = {
            full_name: document.getElementById('full_name').value,
            phone: document.getElementById('phone').value,
            email: document.getElementById('email').value,
            city: document.getElementById('city').value,
            commune: document.getElementById('commune').value,
            address: document.getElementById('address').value,
            notes: document.getElementById('notes').value,
            is_default: true
        };

        // Afficher un loader
        const submitBtn = deliveryForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';

        // Déterminer si c'est une création ou une mise à jour
        const url = savedAddressId ? `/delivery-address/${savedAddressId}` : '/delivery-address';
        const method = savedAddressId ? 'PUT' : 'POST';

        // Envoyer les données au serveur
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                savedAddressId = data.data.id;
                
                // Afficher les informations confirmées
                showConfirmedInfo(data.data);

                // Afficher un message de succès
                showToast('Adresse de livraison enregistrée avec succès', 'success');

                // Scroll vers le récapitulatif sur mobile
                if (window.innerWidth < 992) {
                    document.getElementById('paymentButtonContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                showToast('Erreur: ' + (data.message || 'Une erreur est survenue'), 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de l\'enregistrement de l\'adresse', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });

    // Bouton pour modifier les informations
    editDeliveryBtn.addEventListener('click', function() {
        deliveryForm.style.display = 'block';
        deliveryInfoConfirmed.style.display = 'none';
        paymentButtonContainer.style.display = 'none';
        deliveryInstructionMessage.style.display = 'block';
        
        // Scroll vers le formulaire
        deliveryForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Fonction pour afficher un toast
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Ajout d'un listener sur le bouton de paiement pour inclure l'ID de l'adresse
    const paymentButton = document.getElementById('paymentButton');
    if (paymentButton) {
        paymentButton.addEventListener('click', function(e) {
            if (savedAddressId) {
                // Ajouter l'ID de l'adresse à l'URL
                const url = new URL(this.href);
                url.searchParams.append('delivery_address_id', savedAddressId);
                this.href = url.toString();
            }
        });
    }
});
</script>
@endpush
@endsection 