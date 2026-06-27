<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification email - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #57534e;
            background-color: #f5f5f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #d97706 100%);
            padding: 36px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1px;
        }
        .header p {
            margin: 6px 0 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
        }
        .content {
            padding: 36px 32px 28px;
        }
        .content h2 {
            color: #44403c;
            margin: 28px 0 16px;
            font-size: 18px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            margin: 24px 0;
            text-align: center;
        }
        .btn:hover {
            background: linear-gradient(135deg, #b45309 0%, #d97706 100%);
        }
        .info-box {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 14px 18px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .info-box p {
            color: #92400e;
            margin: 0;
            font-size: 14px;
        }
        .steps {
            background: #fafaf9;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #e7e5e4;
        }
        .steps h3 {
            color: #44403c;
            margin: 0 0 16px;
            font-size: 16px;
        }
        .step {
            display: flex;
            align-items: center;
            margin: 10px 0;
            color: #57534e;
            font-size: 14px;
        }
        .step-check {
            color: #d97706;
            font-weight: 700;
            margin-right: 10px;
            font-size: 16px;
        }
        .security-box {
            background: #fafaf9;
            border: 1px solid #e7e5e4;
            border-radius: 8px;
            padding: 14px;
            margin: 20px 0;
            text-align: center;
        }
        .security-box p {
            color: #57534e;
            margin: 0;
            font-size: 13px;
        }
        .fallback-link {
            background: #fafaf9;
            border-radius: 8px;
            padding: 14px;
            margin: 20px 0;
            word-break: break-all;
            font-size: 13px;
        }
        .fallback-link p {
            color: #57534e;
            margin: 0 0 6px;
        }
        .fallback-link a {
            color: #d97706;
        }
        .divider {
            border: none;
            border-top: 1px solid #e7e5e4;
            margin: 28px 0;
        }
        .footer {
            background: #fafaf9;
            padding: 28px 30px;
            text-align: center;
            border-top: 1px solid #e7e5e4;
        }
        .footer p {
            color: #a8a29e;
            margin: 4px 0;
            font-size: 13px;
        }
        .footer a {
            color: #d97706;
        }
        @media (max-width: 600px) {
            .container { margin: 12px; border-radius: 12px; }
            .header, .content, .footer { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Vérification d'adresse email</p>
        </div>
        <div class="content">
            <h2 style="margin-top: 0;">Bienvenue sur {{ config('app.name') }} !</h2>

            <p style="color: #57534e; margin: 0 0 8px;">
                Bonjour <strong style="color: #44403c;">{{ $user->name }}</strong>,
            </p>

            <p style="color: #57534e; margin: 0 0 8px;">
                Merci de vous être inscrit sur <strong>{{ config('app.name') }}</strong>, votre marketplace de confiance pour acheter et vendre des articles uniques !
            </p>

            <p style="color: #57534e; margin: 0 0 8px;">
                Pour activer votre compte et commencer à explorer des milliers d'articles, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous :
            </p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="btn">Vérifier mon email</a>
            </div>

            <div class="info-box">
                <p>Ce lien expirera dans <strong>60 minutes</strong>.</p>
            </div>

            <hr class="divider">

            <h2>Prochaines étapes</h2>

            <div class="steps">
                <h3>Une fois votre email vérifié, vous pourrez :</h3>
                <div class="step">
                    <span class="step-check">✓</span>
                    <span><strong>Parcourir</strong> des milliers d'articles</span>
                </div>
                <div class="step">
                    <span class="step-check">✓</span>
                    <span><strong>Vendre</strong> vos propres articles</span>
                </div>
                <div class="step">
                    <span class="step-check">✓</span>
                    <span><strong>Ajouter</strong> des favoris</span>
                </div>
                <div class="step">
                    <span class="step-check">✓</span>
                    <span><strong>Échanger</strong> avec d'autres utilisateurs</span>
                </div>
            </div>

            <h2>Sécurité</h2>

            <div class="security-box">
                <p>Si vous n'avez pas créé de compte sur {{ config('app.name') }}, ignorez simplement cet email. Aucune action supplémentaire n'est requise.</p>
            </div>

            <div class="fallback-link">
                <p style="font-weight: 600; color: #44403c;">Lien de vérification manuel :</p>
                <p>Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :</p>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>

            <hr class="divider">

            <p style="color: #57534e; margin: 0;">Merci de faire confiance à {{ config('app.name') }} !</p>
            <p style="color: #44403c; font-weight: 600; margin: 4px 0 0;">L'équipe {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong> &mdash; Marketplace de confiance</p>
            <p>Cet email a été envoyé à {{ $user->email }}</p>
            <p style="margin-top: 16px;">
                Des questions ?
                <a href="mailto:{{ config('mail.from.address') }}">
                    {{ config('mail.from.address') }}
                </a>
            </p>
        </div>
    </div>
</body>
</html>
