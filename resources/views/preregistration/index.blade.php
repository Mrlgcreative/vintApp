<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-inscription - VintApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --gradient-bg: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .preregistration-container {
            max-width: 600px;
            width: 100%;
            margin: 2rem;
        }

        .preregistration-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
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

        .card-header-custom {
            background: var(--gradient-bg);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .card-header-custom h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .card-header-custom p {
            margin: 0;
            opacity: 0.95;
            font-size: 1.1rem;
        }

        .card-body-custom {
            padding: 2.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.15);
        }

        .btn-submit {
            background: var(--gradient-bg);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            padding: 1rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .feature-list {
            background: #f9fafb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .feature-item:last-child {
            margin-bottom: 0;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-bg);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .alert-custom {
            border-radius: 12px;
            border: none;
        }

        .required-star {
            color: #ef4444;
        }

        .stats-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin: 0.25rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="preregistration-container">
        <div class="preregistration-card">
            <!-- Header -->
            <div class="card-header-custom">
                <h1><i class="fas fa-rocket me-2"></i>Rejoignez VintApp !</h1>
                <p>Soyez parmi les premiers à découvrir notre plateforme</p>
                <div class="mt-3">
                    <span class="stats-badge"><i class="fas fa-users me-1"></i> {{ \App\Models\UserWaiting::count() }}+ inscrits</span>
                    <span class="stats-badge"><i class="fas fa-check-circle me-1"></i> {{ \App\Models\UserWaiting::approved()->count() }} approuvés</span>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-custom">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-custom">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Erreur(s) :</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Features -->
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                        <div>
                            <strong>Accès prioritaire</strong><br>
                            <small class="text-muted">Soyez les premiers à utiliser la plateforme</small>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-gift"></i></div>
                        <div>
                            <strong>Bonus de bienvenue</strong><br>
                            <small class="text-muted">Recevez des crédits gratuits à votre inscription</small>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-bell"></i></div>
                        <div>
                            <strong>Notifications exclusives</strong><br>
                            <small class="text-muted">Restez informé du lancement</small>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('preregistration.store') }}" id="preregistrationForm">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Nom complet <span class="required-star">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               required
                               placeholder="Ex: Jean Dupont">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Adresse email <span class="required-star">*</span>
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required
                               placeholder="votre.email@example.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            Téléphone <span class="text-muted">(optionnel)</span>
                        </label>
                        <input type="tel" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}"
                               placeholder="Ex: 0812345678 ou +243812345678"
                               pattern="^(\+?243|0)?[0-9]{9}$">
                        <small class="text-muted">Format: 0812345678 ou +243812345678</small>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="country" class="form-label">
                            Pays <span class="required-star">*</span>
                        </label>
                        <select class="form-select @error('country') is-invalid @enderror" 
                                id="country" 
                                name="country" 
                                required>
                            <option value="Congo (RDC)" {{ old('country') == 'Congo (RDC)' ? 'selected' : 'selected' }}>🇨🇩 Congo (RDC)</option>
                            <option value="Congo (Brazzaville)" {{ old('country') == 'Congo (Brazzaville)' ? 'selected' : '' }}>🇨🇬 Congo (Brazzaville)</option>
                            <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>🇫🇷 France</option>
                            <option value="Belgique" {{ old('country') == 'Belgique' ? 'selected' : '' }}>🇧🇪 Belgique</option>
                            <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>🇨🇦 Canada</option>
                            <option value="Autre" {{ old('country') == 'Autre' ? 'selected' : '' }}>🌍 Autre</option>
                        </select>
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label">
                            Pourquoi voulez-vous rejoindre VintApp ? <span class="text-muted">(optionnel)</span>
                        </label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  id="message" 
                                  name="message" 
                                  rows="3"
                                  maxlength="1000"
                                  placeholder="Partagez votre intérêt pour notre plateforme...">{{ old('message') }}</textarea>
                        <small class="text-muted">Maximum 1000 caractères</small>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-rocket me-2"></i>Je m'inscris maintenant !
                    </button>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-lock me-1"></i>
                            Vos données sont sécurisées et ne seront jamais partagées
                        </small>
                    </div>
                </form>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <a href="{{ route('preregistration.stats') }}" class="text-decoration-none">
                        <i class="fas fa-chart-line me-1"></i>Voir les statistiques
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
