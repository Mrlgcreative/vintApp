<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #4a4a4a;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: #1a1a1a;
            padding: 32px 28px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
        }
        .content {
            padding: 32px 28px;
        }
        .features {
            display: flex;
            gap: 12px;
            margin: 20px 0;
        }
        .feature {
            flex: 1;
            background: #f5f5f5;
            padding: 16px;
            border-radius: 8px;
            text-align: center;
        }
        .feature-icon {
            font-size: 18px;
            margin-bottom: 6px;
            color: #9a9a9a;
        }
        .feature strong {
            display: block;
            font-size: 13px;
            margin-bottom: 4px;
        }
        .feature p {
            font-size: 12px;
            color: #9a9a9a;
            margin: 0;
        }
        .btn {
            display: inline-block;
            background: #1a1a1a;
            color: #ffffff !important;
            padding: 14px 36px;
            text-decoration: none !important;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            margin: 16px 0;
        }
        .footer {
            padding: 24px 28px;
            text-align: center;
            border-top: 1px solid #e5e5e5;
        }
        .footer p {
            color: #9a9a9a;
            margin: 4px 0;
            font-size: 13px;
        }
        .footer a {
            color: #1a1a1a;
        }
        @media (max-width: 600px) {
            .container { margin: 12px; border-radius: 10px; }
            .header, .content, .footer { padding: 20px; }
            .features { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue !</h1>
        </div>
        <div class="content">
            <p>Bonjour {{ $subscriber->name ?? 'cher abonné' }},</p>
            <p>Merci de vous être inscrit à notre newsletter ! Vous recevrez désormais nos meilleures trouvailles et offres exclusives.</p>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">✦</div>
                    <strong>Nouveaux articles</strong>
                    <p>Soyez le premier informé</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">✦</div>
                    <strong>Promotions</strong>
                    <p>Offres réservées aux abonnés</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">✦</div>
                    <strong>Conseils</strong>
                    <p>Astuces et bons plans</p>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('items.index') }}" class="btn">Découvrir nos articles</a>
            </div>

            <p>À bientôt sur {{ config('app.name') }} !</p>
            <p><strong>L'équipe {{ config('app.name') }}</strong></p>
        </div>
        <div class="footer">
            <p>Vous recevez cet email car vous êtes abonné à notre newsletter.</p>
            <p>
                <a href="{{ route('newsletter.preferences', $subscriber->unsubscribe_token) }}">Préférences</a>
                &mdash;
                <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}">Se désabonner</a>
            </p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
            <img src="{{ route('newsletter.track.open', $subscriber->unsubscribe_token) }}" alt="" width="1" height="1" style="display:none;">
        </div>
    </div>
</body>
</html>
