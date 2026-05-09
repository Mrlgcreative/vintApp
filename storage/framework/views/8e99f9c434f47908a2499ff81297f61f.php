<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de vérification VintApp</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .code-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .verification-code {
            font-size: 48px;
            font-weight: bold;
            color: white;
            letter-spacing: 8px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        .code-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .warning {
            background: #fef3cd;
            border-left: 4px solid #fbbf24;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .warning-title {
            color: #92400e;
            font-weight: bold;
            margin: 0 0 5px;
        }
        .warning-text {
            color: #b45309;
            margin: 0;
            font-size: 14px;
        }
        .instructions {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .instructions h3 {
            color: #374151;
            margin: 0 0 15px;
            font-size: 18px;
        }
        .step {
            display: flex;
            align-items: center;
            margin: 10px 0;
            color: #6b7280;
        }
        .step-number {
            background: #667eea;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            color: #6b7280;
            margin: 5px 0;
            font-size: 14px;
        }
        .security-notice {
            background: #ecfdf5;
            border: 1px solid #d1fae5;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .security-notice p {
            color: #065f46;
            margin: 0;
            font-size: 14px;
        }
        .timer {
            color: #dc2626;
            font-weight: bold;
            font-size: 16px;
            margin: 15px 0;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .verification-code {
                font-size: 36px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 VintApp</h1>
            <p>Code de vérification de votre compte</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2 style="color: #374151; margin: 0 0 20px;">Bonjour <?php echo e($user->name); ?>,</h2>
            
            <p style="color: #6b7280; margin: 0 0 20px;">
                Nous avons reçu une demande de vérification pour votre compte VintApp. 
                Utilisez le code ci-dessous pour confirmer votre adresse email :
            </p>

            <!-- Code de vérification -->
            <div class="code-container">
                <div class="code-label">Votre code de vérification</div>
                <div class="verification-code"><?php echo e($code); ?></div>
            </div>

            <!-- Avertissement de sécurité -->
            <div class="warning">
                <div class="warning-title">⚠️ Important</div>
                <div class="warning-text">
                    Ce code expire dans <strong>15 minutes</strong>. 
                    Ne le partagez jamais avec qui que ce soit.
                </div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>Comment utiliser ce code :</h3>
                <div class="step">
                    <div class="step-number">1</div>
                    <span>Retournez sur VintApp dans votre navigateur</span>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <span>Saisissez le code à 6 chiffres dans le formulaire</span>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <span>Cliquez sur "Vérifier le code"</span>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <span>Profitez de VintApp !</span>
                </div>
            </div>

            <!-- Notice de sécurité -->
            <div class="security-notice">
                <p>
                    🔒 Si vous n'avez pas demandé cette vérification, 
                    ignorez cet email et votre compte restera sécurisé.
                </p>
            </div>

            <p style="color: #6b7280; margin: 30px 0 0;">
                Merci de faire confiance à VintApp pour vos achats et ventes !
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>VintApp</strong> - Votre marketplace de confiance</p>
            <p>Cet email a été envoyé à <?php echo e($user->email); ?></p>
            <p style="margin-top: 20px;">
                Des questions ? Contactez notre support : 
                <a href="mailto:<?php echo e(config('mail.from.address')); ?>" style="color: #667eea;">
                    <?php echo e(config('mail.from.address')); ?>

                </a>
            </p>
        </div>
    </div>
</body>
</html><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/emails/verification-code.blade.php ENDPATH**/ ?>