<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-inscriptions fermées - VintApp</title>
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

        .closed-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-container {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .icon-container i {
            font-size: 3rem;
            color: white;
        }

        h1 {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .message {
            color: #4a5568;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .info-box {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 1rem;
            margin-top: 2rem;
            border-radius: 8px;
            text-align: left;
        }

        .info-box h6 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .info-box p {
            color: #718096;
            margin: 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="closed-container">
        <div class="icon-container">
            <i class="fas fa-lock"></i>
        </div>

        <h1>Pré-inscriptions fermées</h1>

        <p class="message">
            {{ $message }}
        </p>

        <div class="info-box">
            <h6><i class="fas fa-info-circle me-2"></i>Information</h6>
            <p>Les pré-inscriptions sont temporairement suspendues. Revenez bientôt pour ne pas manquer l'ouverture !</p>
        </div>

        <div class="mt-4">
            <a href="{{ url('/') }}" class="btn-home">
                <i class="fas fa-home me-2"></i>Retour à l'accueil
            </a>
        </div>

        <div class="mt-4">
            <small class="text-muted">
                <i class="fas fa-envelope me-1"></i>
                Pour plus d'informations, contactez-nous à <a href="mailto:{{ $contactEmail ?? 'contact@vintapp.com' }}">{{ $contactEmail ?? 'contact@vintapp.com' }}</a>
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
