<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Promotion Exclusive VintApp' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .email-container {
            max-width: 650px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }
        .header {
            background: linear-gradient(135deg, #4f00ce 0%, #6a1b9a 50%, #8f5cff 100%);
            color: white;
            padding: 50px 30px;
            text-align: center;
            position: relative;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        .header .subtitle {
            margin: 10px 0 0 0;
            font-size: 18px;
            font-weight: 300;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        .content {
            padding: 45px 35px;
            color: #2c3e50;
            line-height: 1.7;
        }
        .content h2 {
            color: #4f00ce;
            font-size: 24px;
            margin: 30px 0 20px 0;
            font-weight: 600;
        }
        .content p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .content .greeting {
            font-size: 18px;
            color: #34495e;
            margin-bottom: 25px;
        }
        .highlight-box {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
            border-left: 4px solid #4f00ce;
            padding: 25px;
            margin: 30px 0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(79, 0, 206, 0.1);
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4f00ce 0%, #6a1b9a 50%, #8f5cff 100%);
            color: white;
            padding: 18px 50px;
            text-decoration: none;
            border-radius: 30px;
            margin: 25px 0;
            font-weight: 600;
            font-size: 17px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(79, 0, 206, 0.3);
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 0, 206, 0.4);
        }
        .features {
            display: table;
            width: 100%;
            margin: 30px 0;
        }
        .feature {
            display: table-cell;
            text-align: center;
            vertical-align: top;
            padding: 20px 15px;
        }
        .feature-icon {
            font-size: 36px;
            color: #4f00ce;
            margin-bottom: 15px;
        }
        .feature h3 {
            color: #2c3e50;
            font-size: 16px;
            margin: 10px 0 8px 0;
            font-weight: 600;
        }
        .feature p {
            font-size: 14px;
            color: #7f8c8d;
            margin: 0;
        }
        .footer {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 35px 25px;
            text-align: center;
            font-size: 13px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        .footer a {
            color: #4f00ce;
            text-decoration: none;
            font-weight: 500;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .footer .brand {
            font-weight: 600;
            color: #4f00ce;
            font-size: 14px;
        }
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #dee2e6 50%, transparent 100%);
            margin: 30px 0;
        }
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            .header {
                padding: 35px 20px;
            }
            .header h1 {
                font-size: 26px;
            }
            .content {
                padding: 30px 25px;
            }
            .features {
                display: block;
            }
            .feature {
                display: block;
                margin-bottom: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>✨ Offre Exclusive VintApp</h1>
            <p class="subtitle">Découvrez des trésors uniques à prix exceptionnels</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Bonjour {{ $subscriber->name ?? 'cher membre de notre communauté' }},</p>
            
            <p>Nous sommes ravis de vous présenter une <strong>offre exceptionnelle</strong> spécialement sélectionnée pour vous sur VintApp, votre marketplace de référence pour les objets d'occasion de qualité.</p>

            <div class="highlight-box">
                {!! $emailContent !!}
            </div>

            <h2>🌟 Pourquoi choisir VintApp ?</h2>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🔍</div>
                    <h3>Sélection Rigoureuse</h3>
                    <p>Articles vérifiés et authentifiés par notre équipe</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🚚</div>
                    <h3>Livraison Sécurisée</h3>
                    <p>Suivi GPS en temps réel de vos commandes</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">💰</div>
                    <h3>Prix Compétitifs</h3>
                    <p>Les meilleures offres du marché congolais</p>
                </div>
            </div>

            <div class="divider"></div>

            <p>Ne laissez pas passer cette opportunité unique ! Cette offre est <strong>limitée dans le temps</strong> et nos stocks s'épuisent rapidement.</p>

            <center>
                <a href="{{ route('newsletter.track.click', ['token' => $subscriber->unsubscribe_token, 'url' => route('items.index')]) }}" class="cta-button">
                    🛍️ Découvrir l'offre maintenant
                </a>
            </center>

            <p style="margin-top: 35px; font-style: italic; color: #7f8c8d;">
                "Chez VintApp, chaque objet a une histoire, chaque achat est une découverte."
            </p>
            
            <div class="divider"></div>

            <p style="margin-bottom: 5px;">Cordialement,</p>
            <p style="margin-top: 5px;"><strong class="brand">L'équipe VintApp</strong></p>
            <p style="font-size: 14px; color: #7f8c8d; margin-top: 0;">Votre partenaire de confiance pour l'achat-vente d'occasion</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin-bottom: 15px;">
                <strong>Vous recevez cet email car vous faites partie de notre communauté privilégiée.</strong>
            </p>
            
            <p style="margin-bottom: 20px;">
                <a href="{{ route('newsletter.preferences', $subscriber->unsubscribe_token) }}">⚙️ Gérer mes préférences</a> | 
                <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}">📧 Se désabonner</a> |
                <a href="{{ route('items.index') }}">🏪 Visiter VintApp</a>
            </p>
            
            <div class="divider" style="margin: 20px 0;"></div>
            
            <p style="margin-bottom: 10px; color: #6c757d;">
                <strong class="brand">VintApp</strong> - La marketplace de l'occasion de qualité en RDC
            </p>
            <p style="margin-bottom: 15px; font-size: 12px;">
                📍 Kinshasa, Lubumbashi, Kolwezi et bientôt dans toute la RDC<br>
                📧 <a href="mailto:support@vintapp.cd">support@vintapp.cd</a> | 
                📞 <a href="tel:+243800000000">+243 800 000 000</a>
            </p>
            
            <p style="margin-bottom: 0; font-size: 11px; color: #adb5bd;">
                &copy; {{ date('Y') }} VintApp SARL. Tous droits réservés.<br>
                Entreprise congolaise dédiée à l'économie circulaire et au développement durable.
            </p>
            
            <!-- Pixel de tracking invisible -->
            <img src="{{ route('newsletter.track.open', $subscriber->unsubscribe_token) }}" alt="" width="1" height="1" style="display:none;" loading="lazy">
        </div>
    </div>
</body>
</html>
