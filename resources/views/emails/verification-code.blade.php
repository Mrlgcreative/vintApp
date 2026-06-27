<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de vérification VintApp</title>
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
        .code-container {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            border-radius: 12px;
            padding: 28px;
            text-align: center;
            margin: 24px 0;
        }
        .verification-code {
            font-size: 44px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 10px;
            margin: 0;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .code-label {
            color: rgba(255, 255, 255, 0.85);
            font-size: 12px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .warning {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 14px 18px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .warning-title {
            color: #92400e;
            font-weight: 700;
            margin: 0 0 4px;
            font-size: 14px;
        }
        .warning-text {
            color: #b45309;
            margin: 0;
            font-size: 14px;
        }
        .instructions {
            background: #fafaf9;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #e7e5e4;
        }
        .instructions h3 {
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
        .step-number {
            background: #d97706;
            color: white;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            margin-right: 12px;
            flex-shrink: 0;
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
        .security-notice {
            background: #fafaf9;
            border: 1px solid #e7e5e4;
            border-radius: 8px;
            padding: 14px;
            margin: 20px 0;
            text-align: center;
        }
        .security-notice p {
            color: #57534e;
            margin: 0;
            font-size: 13px;
        }
        .divider {
            border: none;
            border-top: 1px solid #e7e5e4;
            margin: 28px 0;
        }
        @media (max-width: 600px) {
            .container { margin: 12px; border-radius: 12px; }
            .header, .content, .footer { padding: 20px; }
            .verification-code { font-size: 34px; letter-spacing: 6px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Code de vérification</p>
        </div>
        <div class="content">
            <h2 style="color: #44403c; margin: 0 0 16px; font-size: 18px;">Bonjour {{ $user->name }},</h2>

            <p style="color: #57534e; margin: 0 0 8px;">
                Nous avons reçu une demande de vérification pour votre compte.
                Utilisez le code ci-dessous pour confirmer votre adresse email :
            </p>

            <div class="code-container">
                <div class="code-label">Votre code de vérification</div>
                <div class="verification-code">{{ $code }}</div>
            </div>

            <div class="warning">
                <div class="warning-title">Important</div>
                <div class="warning-text">
                    Ce code expire dans <strong>15 minutes</strong>.
                    Ne le partagez jamais avec qui que ce soit.
                </div>
            </div>

            <div class="instructions">
                <h3>Comment utiliser ce code :</h3>
                <div class="step">
                    <div class="step-number">1</div>
                    <span>Retournez sur {{ config('app.name') }} dans votre navigateur</span>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <span>Saisissez le code à 6 chiffres dans le formulaire</span>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <span>Cliquez sur "Vérifier"</span>
                </div>
            </div>

            <div class="security-notice">
                <p>Si vous n'avez pas demandé cette vérification, ignorez cet email et votre compte restera sécurisé.</p>
            </div>

            <hr class="divider">

            <p style="color: #57534e; margin: 0;">Merci de faire confiance à {{ config('app.name') }} !</p>
        </div>
        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong> &mdash; Marketplace de confiance</p>
            <p>Cet email a été envoyé à {{ $user->email }}</p>
            <p style="margin-top: 16px;">
                Des questions ?
                <a href="mailto:{{ config('mail.from.address') }}" style="color: #d97706; text-decoration: underline;">
                    {{ config('mail.from.address') }}
                </a>
            </p>
        </div>
    </div>
</body>
</html>
