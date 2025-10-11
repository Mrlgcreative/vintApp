@extends('app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="fas fa-headset me-3"></i>Support Client
                    </h2>
                    <p class="mb-0 mt-2">Nous sommes là pour vous aider 24/7</p>
                </div>
                <div class="card-body p-5">
                    <!-- Informations de contact -->
                    <div class="row g-4 mb-5">
                        <div class="col-md-4 text-center">
                            <div class="support-card p-4 rounded-3 bg-light h-100">
                                <div class="icon-wrapper mb-3">
                                    <i class="fas fa-envelope fa-3x text-primary"></i>
                                </div>
                                <h5 class="fw-bold">Email</h5>
                                <p class="text-muted mb-2">Réponse sous 24h</p>
                                <a href="mailto:support@vintapp.com" class="text-primary fw-bold">
                                    support@vintapp.com
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <div class="support-card p-4 rounded-3 bg-light h-100">
                                <div class="icon-wrapper mb-3">
                                    <i class="fas fa-phone fa-3x text-success"></i>
                                </div>
                                <h5 class="fw-bold">Téléphone</h5>
                                <p class="text-muted mb-2">Lun-Ven 8h-18h</p>
                                <a href="tel:+243000000000" class="text-success fw-bold">
                                    +243 (0) 00 000 0000
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <div class="support-card p-4 rounded-3 bg-light h-100">
                                <div class="icon-wrapper mb-3">
                                    <i class="fab fa-whatsapp fa-3x text-success"></i>
                                </div>
                                <h5 class="fw-bold">WhatsApp</h5>
                                <p class="text-muted mb-2">Réponse rapide</p>
                                <a href="https://wa.me/243000000000" target="_blank" class="text-success fw-bold">
                                    Nous contacter
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formulaire de contact -->
                    <div class="card border-0 bg-light">
                        <div class="card-body p-4">
                            <h4 class="mb-4">
                                <i class="fas fa-paper-plane me-2"></i>Envoyer un message
                            </h4>
                            
                            <form id="support-form" action="{{ route('support') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nom complet</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ Auth::check() ? Auth::user()->name : '' }}" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ Auth::check() ? Auth::user()->email : '' }}" required>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="subject" class="form-label">Sujet</label>
                                        <select class="form-select" id="subject" name="subject" required>
                                            <option value="">Sélectionnez un sujet</option>
                                            <option value="payment">Problème de paiement</option>
                                            <option value="order">Question sur une commande</option>
                                            <option value="account">Problème de compte</option>
                                            <option value="technical">Problème technique</option>
                                            <option value="suggestion">Suggestion</option>
                                            <option value="other">Autre</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" name="message" 
                                                  rows="6" required placeholder="Décrivez votre problème ou question..."></textarea>
                                    </div>
                                    
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <div id="form-response" class="mt-3" style="display: none;"></div>
                        </div>
                    </div>
                    
                    <!-- FAQ -->
                    <div class="mt-5">
                        <h4 class="mb-4">
                            <i class="fas fa-question-circle me-2"></i>Questions Fréquentes
                        </h4>
                        
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Comment effectuer un paiement Mobile Money ?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <ol>
                                            <li>Accédez à la page de paiement</li>
                                            <li>Entrez votre numéro Mobile Money (Orange, M-Pesa, Airtel, Africell)</li>
                                            <li>Saisissez le montant à payer</li>
                                            <li>Cliquez sur "Payer maintenant"</li>
                                            <li>Attendez la confirmation (3-5 secondes)</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Que faire si mon paiement échoue ?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>Si votre paiement échoue, vérifiez :</p>
                                        <ul>
                                            <li>Votre solde Mobile Money est suffisant</li>
                                            <li>Votre numéro est correct (9 chiffres après +243)</li>
                                            <li>Vous avez une bonne connexion internet</li>
                                            <li>Votre compte Mobile Money est actif</li>
                                        </ul>
                                        <p>Si le problème persiste, contactez-nous via ce formulaire.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Combien de temps pour recevoir ma commande ?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>Les délais de livraison dépendent de votre localisation :</p>
                                        <ul>
                                            <li><strong>Kinshasa :</strong> 1-3 jours ouvrables</li>
                                            <li><strong>Autres villes :</strong> 3-7 jours ouvrables</li>
                                            <li><strong>Zones reculées :</strong> 7-14 jours ouvrables</li>
                                        </ul>
                                        <p>Vous recevrez une notification à chaque étape de la livraison.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        Comment suivre ma commande ?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <ol>
                                            <li>Connectez-vous à votre compte</li>
                                            <li>Accédez à "Mes Commandes"</li>
                                            <li>Cliquez sur la commande que vous souhaitez suivre</li>
                                            <li>Vous verrez le statut actuel et l'historique complet</li>
                                        </ol>
                                        <p>Vous recevez également des notifications par email et dans l'application.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bouton retour -->
                    <div class="text-center mt-5">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Retour au Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('support-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formResponse = document.getElementById('form-response');
    const submitButton = this.querySelector('button[type="submit"]');
    
    // Simulation d'envoi (à remplacer par un vrai endpoint)
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    
    // Simuler un délai d'envoi
    setTimeout(() => {
        formResponse.innerHTML = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Message envoyé avec succès !</strong>
                <p class="mb-0 mt-2">Notre équipe vous répondra dans les plus brefs délais.</p>
            </div>
        `;
        formResponse.style.display = 'block';
        
        // Réinitialiser le formulaire
        this.reset();
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Envoyer le message';
        
        // Masquer le message après 5 secondes
        setTimeout(() => {
            formResponse.style.display = 'none';
        }, 5000);
    }, 1500);
});
</script>

<style>
.support-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.support-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.icon-wrapper {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}
</style>
@endsection
