<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur VintApp</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #4a4a5a;
            background-color: #f4f0f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(124, 58, 237, 0.08);
        }
        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #6d28d9 100%);
            padding: 36px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
        }
        .header p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 15px;
        }
        .content {
            padding: 36px 32px 28px;
        }
        .features {
            display: flex;
            gap: 12px;
            margin: 24px 0;
        }
        .feature {
            flex: 1;
            background: #f5f3ff;
            padding: 20px 16px;
            border-radius: 12px;
            text-align: center;
        }
        .feature-icon {
            font-size: 32px;
            margin-bottom: 8px;
        }
        .feature strong {
            display: block;
            color: #5b21b6;
            font-size: 14px;
            margin-bottom: 4px;
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
        @media (max-width: 600px) {
            .container { margin: 12px; border-radius: 12px; }
            .header, .content, .footer { padding: 20px; }
            .features { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue sur {{ config('app.name') }} !</h1>
            <p>Merci de nous avoir rejoints</p>
        </div>
        <div class="content">
            <h2 style="color: #1f1f2e; margin: 0 0 16px;">Bonjour {{ $subscriber->name ?? 'cher(e) abonné(e)' }},</h2>

            <p>Merci de vous être inscrit(e) à notre newsletter ! Vous recevrez désormais nos meilleures trouvailles et offres exclusives.</p>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">📦</div>
                    <strong>Nouveaux articles</strong>
                    <p>Soyez le premier à découvrir nos dernières trouvailles</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎁</div>
                    <strong>Promotions exclusives</strong>
                    <p>Accédez à des offres réservées aux abonnés</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">💰</div>
                    <strong>Conseils et astuces</strong>
                    <p>Maximisez vos achats et ventes</p>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('items.index') }}" class="cta-button">
                    Découvrir nos articles
                </a>
            </div>

            <p style="margin-top: 24px;">À bientôt sur {{ config('app.name') }} !</p>
            <p><strong>L'équipe {{ config('app.name') }}</strong></p>
        </div>
        <div class="footer">
            <p>Vous recevez cet email car vous êtes inscrit(e) à notre newsletter.</p>
            <p>
                <a href="{{ route('newsletter.preferences', $subscriber->unsubscribe_token) }}">Gérer mes préférences</a>
                &mdash;
                <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}">Se désabonner</a>
            </p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <img src="{{ route('newsletter.track.open', $subscriber->unsubscribe_token) }}" alt="" width="1" height="1" style="display:none;">
        </div>
    </div>
</body>
</html>
