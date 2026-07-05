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
        .btn {
            display: inline-block;
            background: #1a1a1a;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            margin: 20px 0;
        }
        .alert {
            background: #f5f5f5;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin: 20px 0;
        }
        .alert p {
            margin: 0;
        }
        .divider {
            border: none;
            border-top: 1px solid #e5e5e5;
            margin: 28px 0;
        }
        .steps {
            margin: 20px 0;
        }
        .step {
            margin: 8px 0;
            font-size: 14px;
        }
        .step strong {
            color: #1a1a1a;
        }
        .fallback {
            font-size: 13px;
            word-break: break-all;
            margin: 20px 0;
        }
        .fallback a {
            color: #1a1a1a;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2 style="margin: 0 0 16px;">Bienvenue !</h2>

            <p>Bonjour <strong>{{ $user->name }}</strong>,</p>

            <p>Merci de vous être inscrit sur {{ config('app.name') }}. Pour activer votre compte, veuillez confirmer votre adresse email :</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="btn">Vérifier mon email</a>
            </div>

            <div class="alert">
                <p>Ce lien expirera dans <strong>60 minutes</strong>.</p>
            </div>

            <hr class="divider">

            <h3>Une fois votre email vérifié, vous pourrez :</h3>

            <div class="steps">
                <div class="step">✓ <strong>Parcourir</strong> des milliers d'articles</div>
                <div class="step">✓ <strong>Vendre</strong> vos propres articles</div>
                <div class="step">✓ <strong>Ajouter</strong> des favoris</div>
                <div class="step">✓ <strong>Échanger</strong> avec d'autres utilisateurs</div>
            </div>

            <div class="alert">
                <p>Si vous n'avez pas créé de compte, ignorez simplement cet email.</p>
            </div>

            <div class="fallback">
                <p style="font-weight: 600;">Lien de vérification :</p>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>

            <hr class="divider">

            <p>Merci de faire confiance à {{ config('app.name') }} !</p>
            <p><strong>L'équipe {{ config('app.name') }}</strong></p>
        </div>
        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>Cet email a été envoyé à {{ $user->email }}</p>
            <p style="margin-top: 12px;">
                <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
