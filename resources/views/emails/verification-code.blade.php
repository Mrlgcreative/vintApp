<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de vérification - {{ config('app.name') }}</title>
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
        .code-box {
            background: #1a1a1a;
            border-radius: 10px;
            padding: 24px;
            text-align: center;
            margin: 20px 0;
        }
        .code-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .code-value {
            font-size: 40px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 8px;
            margin: 0;
        }
        .alert {
            background: #f5f5f5;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            color: #4a4a4a;
            margin: 20px 0;
        }
        .alert strong {
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
            .code-value { font-size: 32px; letter-spacing: 6px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <p style="margin: 0 0 12px;">Bonjour {{ $user->name }},</p>
            <p style="margin: 0 0 8px;">
                Nous avons reçu une demande de vérification pour votre compte.
                Utilisez le code ci-dessous pour confirmer votre adresse email :
            </p>

            <div class="code-box">
                <div class="code-label">Code de vérification</div>
                <div class="code-value">{{ $code }}</div>
            </div>

            <div class="alert">
                Ce code expire dans <strong>15 minutes</strong>. Ne le partagez jamais.
            </div>

            <p style="margin: 0;">Merci de faire confiance à {{ config('app.name') }} !</p>
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
