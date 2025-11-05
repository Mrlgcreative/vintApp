@extends('app')

@section('title', 'Conditions d\'Utilisation - VintApp')
@section('meta_description', 'Conditions générales d\'utilisation de VintApp - Règles et conditions pour utiliser notre marketplace de vente d\'articles d\'occasion')
@section('meta_keywords', 'CGU, conditions, utilisation, règles, VintApp, marketplace, légal')

@section('content')
<div class="container-fluid">
    <!-- Header des CGU -->
    <div class="terms-header bg-gradient-primary text-white py-5 mb-4">
        <div class="container text-center">
            <h1 class="display-5 mb-3">
                <i class="fas fa-file-contract me-3"></i>
                Conditions Générales d'Utilisation
            </h1>
            <p class="lead mb-3">Dernière mise à jour : {{ date('d/m/Y') }}</p>
            <p>En utilisant VintApp, vous acceptez ces conditions d'utilisation</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 mb-4">
                <div class="terms-sidebar sticky-top">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-list-ul"></i> Sommaire</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="#article-1" class="list-group-item list-group-item-action">
                                <i class="fas fa-info-circle text-primary me-2"></i> 1. Objet
                            </a>
                            <a href="#article-2" class="list-group-item list-group-item-action">
                                <i class="fas fa-user text-success me-2"></i> 2. Acceptation
                            </a>
                            <a href="#article-3" class="list-group-item list-group-item-action">
                                <i class="fas fa-store text-warning me-2"></i> 3. Services
                            </a>
                            <a href="#article-4" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-plus text-info me-2"></i> 4. Inscription
                            </a>
                            <a href="#article-5" class="list-group-item list-group-item-action">
                                <i class="fas fa-tags text-danger me-2"></i> 5. Vente
                            </a>
                            <a href="#article-6" class="list-group-item list-group-item-action">
                                <i class="fas fa-shopping-cart text-purple me-2"></i> 6. Achat
                            </a>
                            <a href="#article-7" class="list-group-item list-group-item-action">
                                <i class="fas fa-credit-card text-success me-2"></i> 7. Paiements
                            </a>
                            <a href="#article-8" class="list-group-item list-group-item-action">
                                <i class="fas fa-truck text-primary me-2"></i> 8. Livraison
                            </a>
                            <a href="#article-9" class="list-group-item list-group-item-action">
                                <i class="fas fa-shield-alt text-danger me-2"></i> 9. Responsabilités
                            </a>
                            <a href="#article-10" class="list-group-item list-group-item-action">
                                <i class="fas fa-ban text-warning me-2"></i> 10. Sanctions
                            </a>
                            <a href="#article-11" class="list-group-item list-group-item-action">
                                <i class="fas fa-balance-scale text-info me-2"></i> 11. Litiges
                            </a>
                            <a href="#article-12" class="list-group-item list-group-item-action">
                                <i class="fas fa-edit text-secondary me-2"></i> 12. Modifications
                            </a>
                        </div>
                    </div>

                    <!-- Contact légal -->
                    <div class="card mt-4">
                        <div class="card-body text-center">
                            <h6>Questions juridiques ?</h6>
                            <p class="small text-muted">Contactez notre service juridique</p>
                            <a href="mailto:legal@vintapp.com" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-envelope"></i> legal@vintapp.com
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="col-lg-9">
                <!-- Article 1 : Objet -->
                <div id="article-1" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i> Article 1 - Objet et définitions</h4>
                        </div>
                        <div class="card-body">
                            <h5>1.1. Objet</h5>
                            <p>Les présentes Conditions Générales d'Utilisation (ci-après « CGU ») régissent l'utilisation de la plateforme VintApp, marketplace dédiée à l'achat et à la vente d'articles d'occasion entre particuliers.</p>
                            
                            <h5>1.2. Définitions</h5>
                            <ul>
                                <li><strong>VintApp :</strong> La plateforme accessible via le site web et l'application mobile</li>
                                <li><strong>Utilisateur :</strong> Toute personne utilisant VintApp, qu'elle soit acheteur ou vendeur</li>
                                <li><strong>Vendeur :</strong> Utilisateur proposant un article à la vente</li>
                                <li><strong>Acheteur :</strong> Utilisateur achetant un article</li>
                                <li><strong>Article :</strong> Bien d'occasion mis en vente sur VintApp</li>
                                <li><strong>Transaction :</strong> Achat d'un article via la plateforme</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Article 2 : Acceptation -->
                <div id="article-2" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0"><i class="fas fa-user me-2"></i> Article 2 - Acceptation des CGU</h4>
                        </div>
                        <div class="card-body">
                            <p>L'utilisation de VintApp implique l'acceptation pleine et entière des présentes CGU. Si vous n'acceptez pas ces conditions, vous ne devez pas utiliser VintApp.</p>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Important :</strong> Nous vous recommandons de lire attentivement ces conditions et de les conserver.
                            </div>
                            
                            <h5>2.1. Capacité juridique</h5>
                            <p>Pour utiliser VintApp, vous devez :</p>
                            <ul>
                                <li>Être âgé de 16 ans minimum (accord parental requis pour les mineurs)</li>
                                <li>Avoir la capacité juridique pour contracter</li>
                                <li>Fournir des informations exactes et complètes</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Article 3 : Services -->
                <div id="article-3" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0"><i class="fas fa-store me-2"></i> Article 3 - Description des services</h4>
                        </div>
                        <div class="card-body">
                            <h5>3.1. Services proposés</h5>
                            <p>VintApp propose une plateforme permettant :</p>
                            <ul>
                                <li>La mise en relation d'acheteurs et de vendeurs</li>
                                <li>La publication d'annonces de vente</li>
                                <li>La recherche et navigation dans le catalogue</li>
                                <li>La messagerie intégrée</li>
                                <li>Le système de paiement sécurisé</li>
                                <li>Le programme d'affiliation et de points</li>
                            </ul>
                            
                            <h5>3.2. Nature du service</h5>
                            <p>VintApp agit uniquement en tant qu'intermédiaire technique. Nous ne sommes pas parties aux contrats de vente conclus entre utilisateurs.</p>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Important :</strong> VintApp ne garantit pas la conformité, la qualité ou la légalité des articles vendus.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Article 4 : Inscription -->
                <div id="article-4" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i> Article 4 - Inscription et compte</h4>
                        </div>
                        <div class="card-body">
                            <h5>4.1. Création du compte</h5>
                            <p>L'inscription est gratuite et requiert :</p>
                            <ul>
                                <li>Une adresse email valide</li>
                                <li>Un nom et prénom</li>
                                <li>Un mot de passe sécurisé</li>
                                <li>La vérification de l'email</li>
                            </ul>
                            
                            <h5>4.2. Responsabilités du compte</h5>
                            <p>Vous vous engagez à :</p>
                            <ul>
                                <li>Fournir des informations exactes et à jour</li>
                                <li>Maintenir la confidentialité de vos identifiants</li>
                                <li>Notifier immédiatement tout usage non autorisé</li>
                                <li>Respecter les présentes CGU</li>
                            </ul>
                            
                            <h5>4.3. Suspension/suppression</h5>
                            <p>VintApp se réserve le droit de suspendre ou supprimer tout compte en cas de :</p>
                            <ul>
                                <li>Non-respect des CGU</li>
                                <li>Informations fausses ou trompeuses</li>
                                <li>Activité frauduleuse</li>
                                <li>Inactivité prolongée (plus de 2 ans)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Article 5 : Vente -->
                <div id="article-5" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h4 class="mb-0"><i class="fas fa-tags me-2"></i> Article 5 - Règles de vente</h4>
                        </div>
                        <div class="card-body">
                            <h5>5.1. Articles autorisés</h5>
                            <p>Seuls les articles d'occasion personnels sont autorisés. Il est interdit de vendre :</p>
                            <ul>
                                <li>Des articles neufs ou professionnels</li>
                                <li>Des produits contrefaits ou illégaux</li>
                                <li>Des articles dangereux ou réglementés</li>
                                <li>Des services ou prestations</li>
                                <li>Des animaux vivants</li>
                                <li>Des médicaments ou produits de santé</li>
                            </ul>
                            
                            <h5>5.2. Publication d'annonces</h5>
                            <p>Chaque annonce doit comporter :</p>
                            <ul>
                                <li>Des photos claires et représentatives</li>
                                <li>Une description honnête et complète</li>
                                <li>L'état réel de l'article</li>
                                <li>Un prix fixe en euros</li>
                                <li>La catégorie appropriée</li>
                            </ul>
                            
                            <h5>5.3. Obligations du vendeur</h5>
                            <p>Le vendeur s'engage à :</p>
                            <ul>
                                <li>Être propriétaire légitime de l'article</li>
                                <li>Décrire fidèlement l'état de l'article</li>
                                <li>Répondre aux questions des acheteurs</li>
                                <li>Expédier l'article dans les délais convenus</li>
                                <li>Emballer soigneusement l'article</li>
                            </ul>
                            
                            <div class="alert alert-danger">
                                <i class="fas fa-ban me-2"></i>
                                <strong>Interdit :</strong> Vente en dehors de VintApp après contact sur la plateforme.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Article 6 : Achat -->
                <div id="article-6" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white;">
                            <h4 class="mb-0"><i class="fas fa-shopping-cart me-2"></i> Article 6 - Règles d'achat</h4>
                        </div>
                        <div class="card-body">
                            <h5>6.1. Processus d'achat</h5>
                            <p>L'achat d'un article implique :</p>
                            <ol>
                                <li>Sélection de l'article et validation</li>
                                <li>Paiement sécurisé via VintApp</li>
                                <li>Confirmation de la commande</li>
                                <li>Expédition par le vendeur</li>
                                <li>Réception et vérification</li>
                            </ol>
                            
                            <h5>6.2. Droits de l'acheteur</h5>
                            <p>L'acheteur bénéficie :</p>
                            <ul>
                                <li>De la protection acheteur VintApp</li>
                                <li>Du remboursement en cas de non-conformité</li>
                                <li>De l'assistance de notre support</li>
                                <li>Du suivi de commande</li>
                            </ul>
                            
                            <h5>6.3. Obligations de l'acheteur</h5>
                            <p>L'acheteur s'engage à :</p>
                            <ul>
                                <li>Payer le prix convenu</li>
                                <li>Fournir une adresse de livraison correcte</li>
                                <li>Vérifier l'article à réception</li>
                                <li>Signaler tout problème dans les 48h</li>
                                <li>Laisser une évaluation honnête</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Article 7 : Paiements -->
                <div id="article-7" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i> Article 7 - Paiements et commissions</h4>
                        </div>
                        <div class="card-body">
                            <h5>7.1. Méthodes de paiement</h5>
                            <p>Les paiements s'effectuent via :</p>
                            <ul>
                                <li>Cartes bancaires (Visa, Mastercard)</li>
                                <li>Portefeuilles électroniques (M-Pesa, Orange Money, Airtel Money)</li>
                                <li>Virement bancaire</li>
                            </ul>
                            
                            <h5>7.2. Sécurité des paiements</h5>
                            <p>VintApp utilise des prestataires certifiés PCI-DSS pour sécuriser les transactions. Nous ne stockons jamais vos données bancaires.</p>
                            
                            <h5>7.3. Commission VintApp</h5>
                            <p>VintApp prélève une commission sur chaque vente réussie :</p>
                            <ul>
                                <li><strong>Articles < 50€ :</strong> 5% + 0,50€ de frais fixes</li>
                                <li><strong>Articles ≥ 50€ :</strong> 8% du prix de vente</li>
                                <li><strong>Maximum :</strong> 15€ par article</li>
                            </ul>
                            
                            <h5>7.4. Remboursements</h5>
                            <p>Les remboursements sont traités sous 5-7 jours ouvrés sur le même moyen de paiement utilisé pour l'achat.</p>
                        </div>
                    </div>
                </div>

                <!-- Article 8 : Livraison -->
                <div id="article-8" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-truck me-2"></i> Article 8 - Livraison et expédition</h4>
                        </div>
                        <div class="card-body">
                            <h5>8.1. Modes de livraison</h5>
                            <p>Plusieurs options sont disponibles :</p>
                            <ul>
                                <li><strong>Remise en main propre :</strong> Rencontre directe vendeur/acheteur</li>
                                <li><strong>Envoi postal :</strong> Via La Poste ou transporteurs privés</li>
                                <li><strong>Point relais :</strong> Récupération en point de vente partenaire</li>
                            </ul>
                            
                            <h5>8.2. Frais de port</h5>
                            <p>Les frais de livraison sont :</p>
                            <ul>
                                <li>Clairement indiqués avant l'achat</li>
                                <li>À la charge de l'acheteur sauf mention contraire</li>
                                <li>Non remboursables en cas de retour</li>
                            </ul>
                            
                            <h5>8.3. Délais de livraison</h5>
                            <p>Les délais indicatifs sont :</p>
                            <ul>
                                <li><strong>Remise en main propre :</strong> À convenir</li>
                                <li><strong>Courrier suivi :</strong> 2-3 jours ouvrés</li>
                                <li><strong>Colissimo :</strong> 1-2 jours ouvrés</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Article 9 : Responsabilités -->
                <div id="article-9" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h4 class="mb-0"><i class="fas fa-shield-alt me-2"></i> Article 9 - Responsabilités</h4>
                        </div>
                        <div class="card-body">
                            <h5>9.1. Responsabilité de VintApp</h5>
                            <p>VintApp s'engage à :</p>
                            <ul>
                                <li>Fournir un service de qualité</li>
                                <li>Maintenir la sécurité de la plateforme</li>
                                <li>Protéger les données personnelles</li>
                                <li>Modérer les contenus signalés</li>
                            </ul>
                            
                            <h5>9.2. Limitations de responsabilité</h5>
                            <p>VintApp ne peut être tenu responsable :</p>
                            <ul>
                                <li>De la qualité, conformité ou légalité des articles</li>
                                <li>Du comportement des utilisateurs</li>
                                <li>Des dommages indirects ou immatériels</li>
                                <li>Des interruptions temporaires du service</li>
                                <li>De la perte de données due à un dysfonctionnement</li>
                            </ul>
                            
                            <h5>9.3. Responsabilité des utilisateurs</h5>
                            <p>Chaque utilisateur est responsable :</p>
                            <ul>
                                <li>De ses publications et transactions</li>
                                <li>De la véracité des informations fournies</li>
                                <li>Du respect des lois en vigueur</li>
                                <li>Des dommages causés à des tiers</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Article 10 : Sanctions -->
                <div id="article-10" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0"><i class="fas fa-ban me-2"></i> Article 10 - Sanctions et exclusions</h4>
                        </div>
                        <div class="card-body">
                            <h5>10.1. Comportements sanctionnés</h5>
                            <p>Sont passibles de sanctions :</p>
                            <ul>
                                <li>Non-respect des CGU</li>
                                <li>Tentative de fraude ou d'escroquerie</li>
                                <li>Harcèlement ou insultes</li>
                                <li>Vente d'articles interdits</li>
                                <li>Manipulation des évaluations</li>
                                <li>Usurpation d'identité</li>
                            </ul>
                            
                            <h5>10.2. Sanctions appliquées</h5>
                            <p>Selon la gravité, VintApp peut :</p>
                            <ul>
                                <li><strong>Avertissement :</strong> Rappel des règles</li>
                                <li><strong>Limitation :</strong> Restriction de fonctionnalités</li>
                                <li><strong>Suspension :</strong> Blocage temporaire du compte</li>
                                <li><strong>Exclusion :</strong> Suppression définitive du compte</li>
                            </ul>
                            
                            <h5>10.3. Procédure de recours</h5>
                            <p>En cas de sanction, l'utilisateur peut :</p>
                            <ul>
                                <li>Contacter le support dans les 15 jours</li>
                                <li>Expliquer sa situation</li>
                                <li>Demander la révision de la décision</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Article 11 : Litiges -->
                <div id="article-11" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0"><i class="fas fa-balance-scale me-2"></i> Article 11 - Gestion des litiges</h4>
                        </div>
                        <div class="card-body">
                            <h5>11.1. Résolution amiable</h5>
                            <p>En cas de litige entre utilisateurs :</p>
                            <ol>
                                <li>Tentative de résolution directe via la messagerie</li>
                                <li>Médiation par le support VintApp si nécessaire</li>
                                <li>Décision finale de VintApp en cas d'échec</li>
                            </ol>
                            
                            <h5>11.2. Protection acheteur</h5>
                            <p>L'acheteur est protégé en cas de :</p>
                            <ul>
                                <li>Non-réception de l'article</li>
                                <li>Article non-conforme à la description</li>
                                <li>Article défectueux non signalé</li>
                            </ul>
                            
                            <h5>11.3. Procédure de réclamation</h5>
                            <p>Pour signaler un problème :</p>
                            <ul>
                                <li>Contactez le vendeur dans les 48h</li>
                                <li>Documentez le problème (photos, messages)</li>
                                <li>Contactez le support si aucune solution</li>
                                <li>Fournissez tous les éléments de preuve</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Article 12 : Modifications -->
                <div id="article-12" class="terms-section mb-5">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Article 12 - Modifications et dispositions finales</h4>
                        </div>
                        <div class="card-body">
                            <h5>12.1. Modification des CGU</h5>
                            <p>VintApp se réserve le droit de modifier les présentes CGU à tout moment. Les modifications prendront effet dès leur publication sur la plateforme.</p>
                            
                            <h5>12.2. Notification des changements</h5>
                            <p>Les utilisateurs seront informés des modifications importantes par :</p>
                            <ul>
                                <li>Email à l'adresse du compte</li>
                                <li>Notification in-app</li>
                                <li>Bannière sur le site</li>
                            </ul>
                            
                            <h5>12.3. Droit applicable et juridiction</h5>
                            <p>Les présentes CGU sont régies par le droit français. En cas de litige, les tribunaux français sont seuls compétents.</p>
                            
                            <h5>12.4. Contact et signalement</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Support utilisateurs :</strong><br>
                                    Email : support@vintapp.com<br>
                                    Téléphone : +33 1 23 45 67 89
                                </div>
                                <div class="col-md-6">
                                    <strong>Service juridique :</strong><br>
                                    Email : legal@vintapp.com<br>
                                    Adresse : VintApp Legal Dept.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Résumé et acceptation -->
                <div class="card border-success mb-5">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i> Résumé des points clés</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>✅ Vos droits :</h6>
                                <ul class="small">
                                    <li>Utilisation gratuite de VintApp</li>
                                    <li>Protection acheteur</li>
                                    <li>Support client 7j/7</li>
                                    <li>Données sécurisées</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>⚠️ Vos obligations :</h6>
                                <ul class="small">
                                    <li>Respecter les règles d'usage</li>
                                    <li>Être honnête dans vos annonces</li>
                                    <li>Payer les commissions</li>
                                    <li>Traiter les autres avec respect</li>
                                </ul>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <p class="mb-0 text-center">
                            <strong>En continuant à utiliser VintApp, vous acceptez ces conditions.</strong><br>
                            <small class="text-muted">Dernière mise à jour : {{ date('d/m/Y') }}</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.terms-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.terms-sidebar {
    top: 20px;
}

.terms-section {
    scroll-margin-top: 100px;
}

.list-group-item {
    border: none;
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
}

.list-group-item:hover,
.list-group-item.active {
    border-left-color: #0d6efd;
    background-color: #f8f9ff;
    transform: translateX(5px);
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
    margin-bottom: 2rem;
}

.card-header {
    border: none;
    font-weight: 600;
}

.text-purple {
    color: #6f42c1 !important;
}

.terms-section h5 {
    color: #495057;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
    margin-top: 1.5rem;
}

.terms-section h5:first-child {
    margin-top: 0;
}

.alert {
    border: none;
    border-left: 4px solid;
}

.alert-info {
    background-color: #e7f3ff;
    border-left-color: #0dcaf0;
}

.alert-warning {
    background-color: #fff8e1;
    border-left-color: #ffc107;
}

.alert-danger {
    background-color: #ffebee;
    border-left-color: #dc3545;
}

.alert-success {
    background-color: #e8f5e8;
    border-left-color: #28a745;
}

@media (max-width: 768px) {
    .terms-header {
        padding: 2rem 0 !important;
    }
    
    .display-5 {
        font-size: 2rem;
    }
    
    .terms-sidebar {
        position: relative !important;
        top: 0 !important;
    }
    
    .terms-section {
        scroll-margin-top: 80px;
    }
}

/* Smooth scrolling pour les ancres */
html {
    scroll-behavior: smooth;
}

/* Animation pour les sections */
.terms-section {
    animation: fadeIn 0.6s ease-in-out;
}

@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(20px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navigation active dans la sidebar
    const sidebarLinks = document.querySelectorAll('.terms-sidebar .list-group-item');
    const sections = document.querySelectorAll('.terms-section');
    
    // Gestion du clic sur les liens de navigation
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                // Mise à jour de l'état actif
                sidebarLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                // Scroll vers la section
                targetSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Effet de surbrillance
                targetSection.style.animation = 'none';
                setTimeout(() => {
                    targetSection.style.animation = 'highlight 2s ease-in-out';
                }, 10);
            }
        });
    });
    
    // Détection de la section visible pour mise à jour de la navigation
    const observerOptions = {
        root: null,
        rootMargin: '-20% 0px -70% 0px',
        threshold: 0
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const sectionId = entry.target.id;
                const correspondingLink = document.querySelector(`a[href="#${sectionId}"]`);
                
                if (correspondingLink) {
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    correspondingLink.classList.add('active');
                }
            }
        });
    }, observerOptions);
    
    sections.forEach(section => {
        observer.observe(section);
    });
    
    // Animation d'entrée pour les cartes
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1
    });
    
    document.querySelectorAll('.card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        cardObserver.observe(card);
    });
    
    // Gérer les ancres dans l'URL
    if (window.location.hash) {
        const targetLink = document.querySelector(`a[href="${window.location.hash}"]`);
        if (targetLink) {
            setTimeout(() => {
                targetLink.click();
            }, 100);
        }
    }
});
</script>

<style>
/* Animation de surbrillance */
@keyframes highlight {
    0% { 
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4);
        transform: scale(1);
    }
    50% { 
        box-shadow: 0 0 20px 10px rgba(13, 110, 253, 0.2);
        transform: scale(1.01);
    }
    100% { 
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
        transform: scale(1);
    }
}
</style>
@endpush