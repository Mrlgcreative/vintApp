

<?php $__env->startSection('title', 'Centre d\'aide - VintApp'); ?>
<?php $__env->startSection('meta_description', 'Centre d\'aide VintApp - FAQ, guides d\'utilisation et support pour acheter et vendre en toute sécurité'); ?>
<?php $__env->startSection('meta_keywords', 'aide, FAQ, support, guide, tutoriel, VintApp, assistance'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header du centre d'aide -->
    <div class="help-header bg-gradient-primary text-white py-5 mb-4">
        <div class="container text-center">
            <h1 class="display-4 mb-3">
                <i class="fas fa-question-circle me-3"></i>
                Centre d'aide VintApp
            </h1>
            <p class="lead mb-4">Trouvez rapidement les réponses à vos questions</p>
            
            <!-- Barre de recherche -->
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Rechercher dans l'aide..." autocomplete="off">
                        <button class="btn btn-light" type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div id="searchResults" class="mt-3 d-none">
                        <!-- Résultats de recherche -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 mb-4">
                <div class="help-sidebar sticky-top">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-list-ul"></i> Catégories</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="#getting-started" class="list-group-item list-group-item-action active" data-category="getting-started">
                                <i class="fas fa-play-circle text-success"></i> Premiers pas
                            </a>
                            <a href="#buying" class="list-group-item list-group-item-action" data-category="buying">
                                <i class="fas fa-shopping-cart text-primary"></i> Acheter
                            </a>
                            <a href="#selling" class="list-group-item list-group-item-action" data-category="selling">
                                <i class="fas fa-store text-warning"></i> Vendre
                            </a>
                            <a href="#payments" class="list-group-item list-group-item-action" data-category="payments">
                                <i class="fas fa-credit-card text-info"></i> Paiements
                            </a>
                            <a href="#account" class="list-group-item list-group-item-action" data-category="account">
                                <i class="fas fa-user-cog text-secondary"></i> Compte
                            </a>
                            <a href="#affiliate" class="list-group-item list-group-item-action" data-category="affiliate">
                                <i class="fas fa-users text-purple"></i> Affiliation
                            </a>
                            <a href="#safety" class="list-group-item list-group-item-action" data-category="safety">
                                <i class="fas fa-shield-alt text-danger"></i> Sécurité
                            </a>
                        </div>
                    </div>

                    <!-- Contact rapide -->
                    <div class="card mt-4">
                        <div class="card-body text-center">
                            <h6>Besoin d'aide personnalisée ?</h6>
                            <p class="small text-muted">Notre équipe est là pour vous aider</p>
                            <a href="#contact" class="btn btn-primary btn-sm">
                                <i class="fas fa-headset"></i> Nous contacter
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="col-lg-9">
                <!-- Section Premiers pas -->
                <div id="getting-started" class="help-section">
                    <div class="section-header mb-4">
                        <h2><i class="fas fa-play-circle text-success"></i> Premiers pas avec VintApp</h2>
                        <p class="text-muted">Découvrez comment utiliser VintApp pour acheter et vendre en toute sécurité</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 guide-card">
                                <div class="card-body">
                                    <div class="guide-icon mb-3">
                                        <i class="fas fa-user-plus fa-2x text-success"></i>
                                    </div>
                                    <h5>Créer un compte</h5>
                                    <p>Inscrivez-vous gratuitement et configurez votre profil pour commencer à acheter et vendre.</p>
                                    <a href="#guide-signup" class="btn btn-outline-success btn-sm">Voir le guide</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 guide-card">
                                <div class="card-body">
                                    <div class="guide-icon mb-3">
                                        <i class="fas fa-camera fa-2x text-primary"></i>
                                    </div>
                                    <h5>Première annonce</h5>
                                    <p>Apprenez à créer votre première annonce avec de belles photos et une description attractive.</p>
                                    <a href="#guide-first-listing" class="btn btn-outline-primary btn-sm">Voir le guide</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Premiers pas -->
                    <div class="faq-section">
                        <h4 class="mb-3">Questions fréquentes - Premiers pas</h4>
                        <div class="accordion" id="faqGettingStarted">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Comment créer un compte sur VintApp ?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqGettingStarted">
                                    <div class="accordion-body">
                                        <p>Vous pouvez créer un compte de plusieurs façons :</p>
                                        <ul>
                                            <li><strong>Email :</strong> Cliquez sur "S'inscrire" et remplissez le formulaire</li>
                                            <li><strong>Google :</strong> Utilisez votre compte Google pour une inscription rapide</li>
                                            <li><strong>Facebook :</strong> Connectez-vous avec votre compte Facebook</li>
                                        </ul>
                                        <div class="alert alert-info">
                                            <i class="fas fa-lightbulb"></i> <strong>Astuce :</strong> Utilisez un code de parrainage lors de l'inscription pour gagner des points bonus !
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        VintApp est-il gratuit ?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqGettingStarted">
                                    <div class="accordion-body">
                                        <p><strong>Oui, VintApp est entièrement gratuit !</strong></p>
                                        <ul>
                                            <li>Inscription gratuite</li>
                                            <li>Publication d'annonces gratuite</li>
                                            <li>Navigation et recherche gratuites</li>
                                            <li>Messagerie intégrée gratuite</li>
                                        </ul>
                                        <p>Nous prenons uniquement une petite commission sur les ventes réussies pour maintenir la plateforme.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Acheter -->
                <div id="buying" class="help-section d-none">
                    <div class="section-header mb-4">
                        <h2><i class="fas fa-shopping-cart text-primary"></i> Guide d'achat</h2>
                        <p class="text-muted">Tout ce que vous devez savoir pour acheter en toute sécurité</p>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card guide-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-search fa-3x text-primary mb-3"></i>
                                    <h5>Rechercher</h5>
                                    <p class="small">Utilisez les filtres pour trouver exactement ce que vous cherchez</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card guide-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-comments fa-3x text-success mb-3"></i>
                                    <h5>Discuter</h5>
                                    <p class="small">Posez vos questions au vendeur via la messagerie intégrée</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card guide-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-credit-card fa-3x text-warning mb-3"></i>
                                    <h5>Payer</h5>
                                    <p class="small">Payez en sécurité avec nos méthodes de paiement protégées</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Achats -->
                    <div class="faq-section">
                        <h4 class="mb-3">Questions fréquentes - Achats</h4>
                        <div class="accordion" id="faqBuying">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-buy-1">
                                        Comment puis-je être sûr que l'article correspond à la description ?
                                    </button>
                                </h2>
                                <div id="faq-buy-1" class="accordion-collapse collapse" data-bs-parent="#faqBuying">
                                    <div class="accordion-body">
                                        <p>Pour éviter les mauvaises surprises :</p>
                                        <ul>
                                            <li>Examinez attentivement toutes les photos</li>
                                            <li>Lisez la description complète</li>
                                            <li>Posez des questions spécifiques au vendeur</li>
                                            <li>Demandez des photos supplémentaires si nécessaire</li>
                                            <li>Vérifiez les avis du vendeur</li>
                                        </ul>
                                        <div class="alert alert-success">
                                            <i class="fas fa-shield-alt"></i> Notre garantie acheteur vous protège en cas de problème !
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-buy-2">
                                        Que faire si l'article ne me convient pas ?
                                    </button>
                                </h2>
                                <div id="faq-buy-2" class="accordion-collapse collapse" data-bs-parent="#faqBuying">
                                    <div class="accordion-body">
                                        <p>Si l'article ne correspond pas à la description :</p>
                                        <ol>
                                            <li>Contactez d'abord le vendeur pour trouver une solution</li>
                                            <li>Documentez le problème avec des photos</li>
                                            <li>Si aucun accord n'est trouvé, contactez notre support</li>
                                            <li>Nous interviendrons pour résoudre le litige</li>
                                        </ol>
                                        <p class="text-muted">Vous avez 48h après réception pour signaler un problème.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Vendre -->
                <div id="selling" class="help-section d-none">
                    <div class="section-header mb-4">
                        <h2><i class="fas fa-store text-warning"></i> Guide de vente</h2>
                        <p class="text-muted">Maximisez vos ventes avec nos conseils d'experts</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h5><i class="fas fa-star"></i> Conseils pour une vente réussie</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-camera text-primary"></i> Photos de qualité</h6>
                                            <ul class="small">
                                                <li>Éclairage naturel optimal</li>
                                                <li>Plusieurs angles de vue</li>
                                                <li>Zoom sur les détails importants</li>
                                                <li>Fond neutre et propre</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-edit text-success"></i> Description attractive</h6>
                                            <ul class="small">
                                                <li>Titre accrocheur et précis</li>
                                                <li>État détaillé de l'article</li>
                                                <li>Marque, taille, matière</li>
                                                <li>Prix juste et compétitif</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                                    <h6>Vendeurs actifs vendent</h6>
                                    <h4 class="text-success">3x plus vite</h4>
                                    <p class="small text-muted">Répondez rapidement aux messages et mettez à jour vos annonces</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Ventes -->
                    <div class="faq-section">
                        <h4 class="mb-3">Questions fréquentes - Ventes</h4>
                        <div class="accordion" id="faqSelling">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-sell-1">
                                        Comment fixer le bon prix pour mon article ?
                                    </button>
                                </h2>
                                <div id="faq-sell-1" class="accordion-collapse collapse" data-bs-parent="#faqSelling">
                                    <div class="accordion-body">
                                        <p>Pour fixer un prix attractif :</p>
                                        <ul>
                                            <li><strong>Recherchez :</strong> Consultez les prix d'articles similaires</li>
                                            <li><strong>État :</strong> Ajustez selon l'état de votre article</li>
                                            <li><strong>Marque :</strong> Les marques populaires se vendent mieux</li>
                                            <li><strong>Saisonnalité :</strong> Adaptez aux tendances actuelles</li>
                                        </ul>
                                        <div class="alert alert-info">
                                            <i class="fas fa-calculator"></i> Notre outil d'estimation vous aide à trouver le prix optimal !
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Paiements -->
                <div id="payments" class="help-section d-none">
                    <div class="section-header mb-4">
                        <h2><i class="fas fa-credit-card text-info"></i> Paiements sécurisés</h2>
                        <p class="text-muted">Tout sur nos méthodes de paiement et la sécurité</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3 text-center mb-3">
                            <div class="payment-method">
                                <div class="payment-icon bg-success text-white rounded mb-2 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <h6>M-Pesa</h6>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="payment-method">
                                <div class="payment-icon bg-warning text-white rounded mb-2 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <h6>Orange Money</h6>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="payment-method">
                                <div class="payment-icon bg-danger text-white rounded mb-2 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                    <i class="fas fa-sim-card"></i>
                                </div>
                                <h6>Airtel Money</h6>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="payment-method">
                                <div class="payment-icon bg-primary text-white rounded mb-2 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <h6>Cartes bancaires</h6>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Paiements -->
                    <div class="faq-section">
                        <h4 class="mb-3">Questions fréquentes - Paiements</h4>
                        <div class="accordion" id="faqPayments">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-pay-1">
                                        Mes informations de paiement sont-elles sécurisées ?
                                    </button>
                                </h2>
                                <div id="faq-pay-1" class="accordion-collapse collapse" data-bs-parent="#faqPayments">
                                    <div class="accordion-body">
                                        <p><strong>Absolument !</strong> Nous utilisons les plus hauts standards de sécurité :</p>
                                        <ul>
                                            <li>Chiffrement SSL 256 bits</li>
                                            <li>Tokenisation des données bancaires</li>
                                            <li>Conformité PCI DSS</li>
                                            <li>Authentification à deux facteurs</li>
                                        </ul>
                                        <div class="alert alert-success">
                                            <i class="fas fa-lock"></i> Vos données ne sont jamais stockées sur nos serveurs !
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Affiliation -->
                <div id="affiliate" class="help-section d-none">
                    <div class="section-header mb-4">
                        <h2><i class="fas fa-users text-purple"></i> Programme d'affiliation</h2>
                        <p class="text-muted">Gagnez des points en parrainant vos amis</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5><i class="fas fa-gift"></i> Comment ça marche ?</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center mb-3">
                                            <div class="step-icon bg-primary text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <span class="h4 mb-0">1</span>
                                            </div>
                                            <h6>Obtenez votre code</h6>
                                            <p class="small">Créez votre code de parrainage unique dans votre profil</p>
                                        </div>
                                        <div class="col-md-4 text-center mb-3">
                                            <div class="step-icon bg-success text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <span class="h4 mb-0">2</span>
                                            </div>
                                            <h6>Partagez</h6>
                                            <p class="small">Envoyez votre code à vos amis via WhatsApp, Facebook, etc.</p>
                                        </div>
                                        <div class="col-md-4 text-center mb-3">
                                            <div class="step-icon bg-warning text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <span class="h4 mb-0">3</span>
                                            </div>
                                            <h6>Gagnez</h6>
                                            <p class="small">Recevez des points à chaque inscription avec votre code</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card bg-gradient-success text-white">
                                <div class="card-body text-center">
                                    <h2 class="display-4">500</h2>
                                    <h5>Points par parrainage</h5>
                                    <p class="small">+ 100 points bonus pour votre filleul</p>
                                    <a href="<?php echo e(route('affiliate.dashboard')); ?>" class="btn btn-light btn-sm">
                                        <i class="fas fa-arrow-right"></i> Commencer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Sécurité -->
                <div id="safety" class="help-section d-none">
                    <div class="section-header mb-4">
                        <h2><i class="fas fa-shield-alt text-danger"></i> Sécurité et Confidentialité</h2>
                        <p class="text-muted">Votre sécurité et confidentialité sont nos priorités</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5><i class="fas fa-lock"></i> Nos engagements sécurité</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Chiffrement SSL 256 bits pour toutes les données</li>
                                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Authentification à deux facteurs disponible</li>
                                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Vos données personnelles ne sont jamais partagées</li>
                                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Paiements sécurisés avec nos partenaires certifiés</li>
                                        <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Modération active contre les arnaques</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-certificate fa-3x text-primary mb-3"></i>
                                    <h6>Certification</h6>
                                    <p class="small">Conforme aux standards de sécurité internationaux RGPD</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CGU et Politique de confidentialité -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-file-contract"></i> Conditions Générales</h5>
                                </div>
                                <div class="card-body">
                                    <p>Nos conditions d'utilisation définissent les règles d'usage de VintApp :</p>
                                    <ul class="small">
                                        <li>Utilisation responsable de la plateforme</li>
                                        <li>Règles de publication d'annonces</li>
                                        <li>Politique de remboursement</li>
                                        <li>Gestion des litiges</li>
                                    </ul>
                                    <button class="btn btn-outline-primary btn-sm" onclick="alert('Les CGU complètes seront bientôt disponibles')">
                                        <i class="fas fa-download"></i> Télécharger les CGU
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-user-shield"></i> Politique de Confidentialité</h5>
                                </div>
                                <div class="card-body">
                                    <p>Nous protégeons vos données personnelles :</p>
                                    <ul class="small">
                                        <li>Collecte minimale de données nécessaires</li>
                                        <li>Pas de vente de données à des tiers</li>
                                        <li>Droit à l'oubli et suppression de compte</li>
                                        <li>Transparence sur l'usage de vos données</li>
                                    </ul>
                                    <button class="btn btn-outline-info btn-sm" onclick="alert('La politique complète sera bientôt disponible')">
                                        <i class="fas fa-eye"></i> Voir la politique
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Contact -->
                <div id="contact" class="help-section d-none">
                    <div class="section-header mb-4">
                        <h2><i class="fas fa-headset text-info"></i> Nous contacter</h2>
                        <p class="text-muted">Notre équipe support est là pour vous aider</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                                    <h5>Email</h5>
                                    <p>support@vintapp.com</p>
                                    <p class="small text-muted">Réponse sous 24h</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-comments fa-3x text-success mb-3"></i>
                                    <h5>Chat en direct</h5>
                                    <p>Lun-Ven, 9h-18h</p>
                                    <button class="btn btn-success btn-sm" id="openChat">
                                        <i class="fas fa-comment"></i> Ouvrir le chat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de contact -->
                    <div class="row mt-4">
                        <div class="col-lg-8 mx-auto">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-paper-plane"></i> Formulaire de contact</h5>
                                </div>
                                <div class="card-body">
                                    <form id="contactForm">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nom complet *</label>
                                                <input type="text" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email *</label>
                                                <input type="email" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Sujet *</label>
                                            <select class="form-control" required>
                                                <option>Question générale</option>
                                                <option>Problème technique</option>
                                                <option>Problème de paiement</option>
                                                <option>Signaler un utilisateur</option>
                                                <option>Suggestion d'amélioration</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Message *</label>
                                            <textarea class="form-control" rows="5" required 
                                                      placeholder="Décrivez votre demande en détail..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-send"></i> Envoyer le message
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.help-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.help-sidebar {
    top: 20px;
}

.help-section {
    min-height: 400px;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.guide-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.guide-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.guide-icon {
    text-align: center;
}

.section-header {
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 1rem;
}

.faq-section {
    margin-top: 2rem;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9ff;
    color: #0d6efd;
}

.payment-method {
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    background: white;
    transition: all 0.3s ease;
}

.payment-method:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
}

.step-icon {
    font-weight: bold;
}

.text-purple {
    color: #6f42c1 !important;
}

.list-group-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

#searchResults {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.search-result-item {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-item:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .help-header {
        padding: 2rem 0 !important;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .help-sidebar {
        position: relative !important;
        top: 0 !important;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navigation entre les sections
    const categoryLinks = document.querySelectorAll('[data-category]');
    const sections = document.querySelectorAll('.help-section');
    
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const categoryId = this.getAttribute('data-category');
            
            // Mise à jour de la navigation active
            categoryLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Affichage de la section correspondante
            sections.forEach(section => {
                section.classList.add('d-none');
            });
            
            const targetSection = document.getElementById(categoryId);
            if (targetSection) {
                targetSection.classList.remove('d-none');
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // Fonction de recherche
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const searchResults = document.getElementById('searchResults');
    
    const searchData = {
        'inscription': {
            title: 'Comment créer un compte ?',
            section: 'getting-started',
            content: 'Créez un compte gratuitement avec email, Google ou Facebook'
        },
        'vendre': {
            title: 'Comment vendre un article ?',
            section: 'selling',
            content: 'Publiez votre annonce avec photos et description détaillée'
        },
        'acheter': {
            title: 'Comment acheter en sécurité ?',
            section: 'buying', 
            content: 'Recherchez, discutez avec le vendeur et payez en sécurité'
        },
        'paiement': {
            title: 'Méthodes de paiement acceptées',
            section: 'payments',
            content: 'M-Pesa, Orange Money, Airtel Money et cartes bancaires'
        },
        'parrainage': {
            title: 'Programme d\'affiliation',
            section: 'affiliate',
            content: 'Gagnez 500 points en parrainant vos amis'
        },
        'points': {
            title: 'Comment utiliser mes points ?',
            section: 'affiliate',
            content: 'Convertissez vos points en argent ou codes de réduction'
        }
    };
    
    function performSearch() {
        const query = searchInput.value.toLowerCase().trim();
        
        if (query.length < 2) {
            searchResults.classList.add('d-none');
            return;
        }
        
        const results = [];
        
        Object.keys(searchData).forEach(key => {
            const item = searchData[key];
            if (key.includes(query) || 
                item.title.toLowerCase().includes(query) || 
                item.content.toLowerCase().includes(query)) {
                results.push(item);
            }
        });
        
        if (results.length > 0) {
            searchResults.innerHTML = results.map(item => `
                <div class="search-result-item" data-section="${item.section}">
                    <h6 class="mb-1">${item.title}</h6>
                    <p class="mb-0 small text-muted">${item.content}</p>
                </div>
            `).join('');
            
            // Ajouter les événements de clic sur les résultats
            searchResults.querySelectorAll('.search-result-item').forEach(item => {
                item.addEventListener('click', function() {
                    const sectionId = this.getAttribute('data-section');
                    const targetLink = document.querySelector(`[data-category="${sectionId}"]`);
                    if (targetLink) {
                        targetLink.click();
                        searchResults.classList.add('d-none');
                        searchInput.value = '';
                    }
                });
            });
            
            searchResults.classList.remove('d-none');
        } else {
            searchResults.innerHTML = '<div class="search-result-item"><p class="mb-0">Aucun résultat trouvé</p></div>';
            searchResults.classList.remove('d-none');
        }
    }
    
    searchInput.addEventListener('input', performSearch);
    searchBtn.addEventListener('click', performSearch);
    
    // Masquer les résultats si on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!searchResults.contains(e.target) && 
            !searchInput.contains(e.target) && 
            !searchBtn.contains(e.target)) {
            searchResults.classList.add('d-none');
        }
    });
    
    // Chat en direct (simulation)
    document.getElementById('openChat')?.addEventListener('click', function() {
        alert('Le chat en direct sera bientôt disponible ! En attendant, contactez-nous par email : support@vintapp.com');
    });
    
    // Formulaire de contact
    document.getElementById('contactForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Simulation d'envoi
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            alert('Message envoyé avec succès ! Notre équipe vous répondra dans les 24h.');
            this.reset();
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 2000);
    });
    
    // Animation d'entrée pour les cardes
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1
    });
    
    document.querySelectorAll('.guide-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/help/index.blade.php ENDPATH**/ ?>