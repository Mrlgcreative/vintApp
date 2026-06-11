<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Offre VintApp' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #4a4a5a;
            background-color: #f4f0f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(124, 58, 237, 0.08);
        }
        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #9333ea 50%, #6d28d9 100%);
            padding: 36px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
        }
        .header .subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 15px;
            margin-top: 8px;
        }
        .content {
            padding: 36px 32px 28px;
        }
        .highlight-box {
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
            border-radius: 12px;
            padding: 24px;
            margin: 20px 0;
            border: 1px solid #ddd6fe;
            font-size: 15px;
            line-height: 1.7;
        }
        .features {
            display: flex;
            gap: 12px;
            margin: 24px 0;
        }
        .feature {
            flex: 1;
            background: #fafafa;
            padding: 20px 16px;
            border-radius: 12px;
            text-align: center;
        }
        .feature-icon {
            font-size: 28px;
            margin-bottom: 8px;
        }
        .feature h3 {
            color: #1f1f2e;
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 4px;
        }
        .feature p {
            font-size: 13px;
            color: #6b6b80;
            margin: 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: #ffffff !important;
            padding: 16px 40px;
            text-decoration: none !important;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }
        .divider {
            border: none;
            border-top: 1px solid #eeeaf5;
            margin: 28px 0;
        }
        .footer {
            background: #f8f6fc;
            padding: 28px 30px;
            text-align: center;
            border-top: 1px solid #eeeaf5;
        }
        .footer p {
            color: #9a9aae;
            margin: 4px 0;
            font-size: 13px;
        }
        .footer a {
            color: #7c3aed;
        }
        .testimonial {
            font-style: italic;
            color: #6b6b80;
            text-align: center;
            margin: 24px 0;
            font-size: 15px;
        }
        @media (max-width: 600px) {
            .email-container { margin: 12px; border-radius: 12px; }
            .header, .content, .footer { padding: 20px; }
            .features { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $subject ?? 'Offre exclusive' }}</h1>
            <div class="subtitle">Des trésors uniques à prix exceptionnels</div>
        </div>
        <div class="content">
            <p class="greeting" style="font-size: 16px; color: #1f1f2e;">
                Bonjour {{ $subscriber->name ?? 'cher membre de notre communauté' }},
            </p>

            <p>Nous sommes ravis de vous présenter une <strong>offre exceptionnelle</strong> spécialement sélectionnée pour vous :</p>

            <div class="highlight-box">
                {!! $emailContent !!}
            </div>

            <h2 style="color: #1f1f2e; font-size: 18px; margin-top: 28px;">Pourquoi choisir {{ config('app.name') }} ?</h2>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🔍</div>
                    <h3>Sélection Rigoureuse</h3>
                    <p>Articles vérifiés et authentifiés par notre équipe</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🚚</div>
                    <h3>Livraison Sécurisée</h3>
                    <p>Suivi en temps réel de vos commandes</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">💰</div>
                    <h3>Prix Compétitifs</h3>
                    <p>Les meilleures offres du marché</p>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('newsletter.track.click', ['token' => $subscriber->unsubscribe_token, 'url' => route('items.index')]) }}" class="cta-button">
                    Découvrir l'offre maintenant
                </a>
            </div>

            <div class="testimonial">
                "Chez {{ config('app.name') }}, chaque objet a une histoire, chaque achat est une découverte."
            </div>
        </div>
        <div class="footer">
            <p><strong>Vous recevez cet email car vous faites partie de notre communauté privilégiée.</strong></p>
            <p>
                <a href="{{ route('newsletter.preferences', $subscriber->unsubscribe_token) }}">Gérer mes préférences</a>
                &mdash;
                <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}">Se désabonner</a>
                &mdash;
                <a href="{{ route('items.index') }}">Visiter {{ config('app.name') }}</a>
            </p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <img src="{{ route('newsletter.track.open', $subscriber->unsubscribe_token) }}" alt="" width="1" height="1" style="display:none;">
        </div>
    </div>
</body>
</html>
