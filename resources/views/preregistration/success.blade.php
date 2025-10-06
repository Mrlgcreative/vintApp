<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci pour votre inscription ! - VintApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .success-container {
            max-width: 600px;
            width: 100%;
            margin: 2rem;
        }

        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            text-align: center;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            animation: bounce 1s ease-in-out;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .success-icon i {
            font-size: 3rem;
            color: white;
        }

        h1 {
            color: #1f2937;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .lead-text {
            color: #6b7280;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .step-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            text-align: left;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 1rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            padding: 1rem 2rem;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>

            <h1>Merci pour votre inscription !</h1>
            <p class="lead-text">
                Votre demande de pré-inscription a été enregistrée avec succès.
            </p>

            <div class="alert alert-info">
                <i class="fas fa-envelope me-2"></i>
                <strong>Vérifiez votre email !</strong><br>
                Nous vous avons envoyé un email de confirmation. Cliquez sur le lien pour confirmer votre adresse.
            </div>

            <div class="text-start mt-4 mb-4">
                <h5 class="mb-3"><i class="fas fa-list-check me-2"></i>Prochaines étapes :</h5>
                
                <div class="step-card">
                    <span class="step-number">1</span>
                    <strong>Confirmez votre email</strong><br>
                    <small class="text-muted">Cliquez sur le lien dans l'email que nous vous avons envoyé</small>
                </div>

                <div class="step-card">
                    <span class="step-number">2</span>
                    <strong>Approbation par l'équipe</strong><br>
                    <small class="text-muted">Notre équipe examinera votre demande</small>
                </div>

                <div class="step-card">
                    <span class="step-number">3</span>
                    <strong>Recevez vos identifiants</strong><br>
                    <small class="text-muted">Une fois approuvé, vous recevrez vos identifiants de connexion</small>
                </div>

                <div class="step-card">
                    <span class="step-number">4</span>
                    <strong>Profitez de VintApp !</strong><br>
                    <small class="text-muted">Connectez-vous et découvrez toutes les fonctionnalités</small>
                </div>
            </div>

            <div class="alert alert-warning">
                <i class="fas fa-clock me-2"></i>
                <strong>Délai de traitement :</strong> 1 à 3 jours ouvrables
            </div>

            <a href="{{ route('home') }}" class="btn btn-primary-custom">
                <i class="fas fa-home me-2"></i>Retour à l'accueil
            </a>

            <div class="mt-4">
                <small class="text-muted">
                    Vous n'avez pas reçu l'email ? Vérifiez vos spams ou contactez-nous.
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
