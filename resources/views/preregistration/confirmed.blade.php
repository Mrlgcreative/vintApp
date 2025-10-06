<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email confirmé ! - VintApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .confirmed-container {
            max-width: 600px;
            width: 100%;
            margin: 2rem;
        }

        .confirmed-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            text-align: center;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .confirmed-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .confirmed-icon::before {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            border: 3px solid #10b981;
            border-radius: 50%;
            opacity: 0.3;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.3;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.1;
            }
        }

        .confirmed-icon i {
            font-size: 3.5rem;
            color: white;
        }

        h1 {
            color: #1f2937;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .user-info {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dcfce7;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #065f46;
        }

        .info-value {
            color: #047857;
        }

        .status-badge {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: 600;
            margin: 1rem 0;
        }

        .btn-home {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            padding: 1rem 2rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="confirmed-container">
        <div class="confirmed-card">
            <div class="confirmed-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h1>Email confirmé avec succès !</h1>
            <p class="lead text-muted">
                Merci d'avoir confirmé votre adresse email, {{ $userWaiting->name }}.
            </p>

            <div class="status-badge">
                <i class="fas fa-shield-check me-2"></i>Email vérifié
            </div>

            <div class="user-info">
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-user me-2"></i>Nom :</span>
                    <span class="info-value">{{ $userWaiting->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-envelope me-2"></i>Email :</span>
                    <span class="info-value">{{ $userWaiting->email }}</span>
                </div>
                @if($userWaiting->phone)
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-phone me-2"></i>Téléphone :</span>
                    <span class="info-value">{{ $userWaiting->phone }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-calendar me-2"></i>Inscription :</span>
                    <span class="info-value">{{ $userWaiting->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-check-double me-2"></i>Confirmation :</span>
                    <span class="info-value">{{ $userWaiting->email_confirmed_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            <div class="alert alert-success">
                <i class="fas fa-hourglass-half me-2"></i>
                <strong>En attente d'approbation</strong><br>
                Notre équipe examinera votre demande dans les 1 à 3 jours ouvrables.
                Vous recevrez un email dès que votre compte sera approuvé.
            </div>

            <div class="mb-3">
                <h5 class="text-start"><i class="fas fa-clock me-2"></i>Que se passe-t-il maintenant ?</h5>
                <ul class="text-start text-muted">
                    <li>Notre équipe va examiner votre demande</li>
                    <li>Vous recevrez un email de notification une fois approuvé</li>
                    <li>Vos identifiants de connexion vous seront envoyés</li>
                    <li>Vous pourrez alors accéder à toutes les fonctionnalités de VintApp</li>
                </ul>
            </div>

            <a href="{{ route('home') }}" class="btn-home">
                <i class="fas fa-home me-2"></i>Retour à l'accueil
            </a>

            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-question-circle me-1"></i>
                    Des questions ? Contactez-nous à support@vintapp.com
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
