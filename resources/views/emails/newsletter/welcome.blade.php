<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur VintApp</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4f00ce 0%, #8f5cff 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
        }
        .content p {
            color: #333;
            line-height: 1.6;
            font-size: 16px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4f00ce 0%, #8f5cff 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: bold;
        }
        .features {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
        }
        .feature {
            text-align: center;
            flex: 1;
            padding: 15px;
        }
        .feature-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .footer {
            background-color: #f8f8f8;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer a {
            color: #4f00ce;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Bienvenue sur VintApp !</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Bonjour {{ $subscriber->name ?? 'cher(e) abonné(e)' }},</p>
            
            <p>Merci de vous être inscrit(e) à notre newsletter ! Nous sommes ravis de vous compter parmi notre communauté.</p>
            
            <p>En tant qu'abonné(e), vous bénéficierez de :</p>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">📦</div>
                    <strong>Nouveaux articles</strong>
                    <p style="font-size: 14px;">Soyez le premier à découvrir nos dernières trouvailles</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎁</div>
                    <strong>Promotions exclusives</strong>
                    <p style="font-size: 14px;">Accédez à des offres réservées aux abonnés</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">💰</div>
                    <strong>Conseils et astuces</strong>
                    <p style="font-size: 14px;">Maximisez vos achats et ventes</p>
                </div>
            </div>

            <center>
                <a href="{{ route('items.index') }}?utm_source=newsletter&utm_medium=email&utm_campaign=welcome" class="cta-button">
                    🛍️ Découvrir nos articles
                </a>
            </center>

            <p style="margin-top: 30px;">À bientôt sur VintApp !</p>
            <p><strong>L'équipe VintApp</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Vous recevez cet email car vous vous êtes inscrit(e) à notre newsletter.</p>
            <p>
                <a href="{{ route('newsletter.preferences', $subscriber->unsubscribe_token) }}">Gérer mes préférences</a> | 
                <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}">Se désabonner</a>
            </p>
            <p>&copy; {{ date('Y') }} VintApp. Tous droits réservés.</p>
            
            <!-- Pixel de tracking -->
            <img src="{{ route('newsletter.track.open', $subscriber->unsubscribe_token) }}" alt="" width="1" height="1" style="display:none;">
        </div>
    </div>
</body>
</html>
