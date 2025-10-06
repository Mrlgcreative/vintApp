<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Maintenance</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        
        .maintenance-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .maintenance-card {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.125);
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175);
        }
        
        .maintenance-icon {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            color: #667eea;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
        
        .time-badge {
            background-color: #dbeafe;
            border: 1px solid #bfdbfe;
        }
        
        .contact-btn {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .contact-btn:hover {
            background-color: #5568d3;
            border-color: #5568d3;
        }
    </style>
</head>
<body>
    <div class="maintenance-bg px-3">
        <div class="maintenance-card p-4 p-md-5 text-center" style="max-width: 600px; width: 100%;">
            <div class="maintenance-icon mb-4">
                <i class="fas fa-cog fa-4x"></i>
            </div>
            
            <h1 class="h2 fw-bold text-dark mb-4">
                Site en Maintenance
            </h1>
            
            <div class="text-muted mb-4">
                <p class="fs-5 mb-3">
                    {{ $message ?? 'Nous effectuons actuellement des travaux de maintenance sur le site.' }}
                </p>
                
                @if(isset($estimated_time) && $estimated_time)
                    <div class="time-badge rounded p-3 mb-3">
                        <p class="text-primary fw-medium mb-0">
                            <i class="far fa-clock me-2"></i>
                            Temps estimé : {{ $estimated_time }}
                        </p>
                    </div>
                @endif
                
                <p class="small">
                    Nous nous excusons pour la gêne occasionnée et travaillons à rétablir le service dans les plus brefs délais.
                </p>
            </div>
            
            <div class="border-top pt-4">
                <p class="small text-secondary mb-3">
                    Besoin d'aide ? Contactez-nous :
                </p>
                
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="mailto:{{ $contact_email ?? 'support@vintapp.com' }}" 
                       class="btn contact-btn text-white d-inline-flex align-items-center justify-content-center">
                        <i class="far fa-envelope me-2"></i>
                        Nous contacter
                    </a>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-top">
                <p class="small text-muted mb-0">
                    © {{ date('Y') }} {{ config('app.name', 'VintApp') }}. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Auto-refresh toutes les 30 secondes pour vérifier si la maintenance est terminée -->
    <script>
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>