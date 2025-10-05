<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Maintenance</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .maintenance-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .maintenance-card {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.125);
        }
        
        .maintenance-icon {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    </style>
</head>
<body class="antialiased">
    <div class="maintenance-bg min-h-screen flex items-center justify-center px-4">
        <div class="maintenance-card rounded-2xl shadow-2xl p-8 max-w-lg w-full text-center">
            <div class="maintenance-icon mb-6">
                <svg class="mx-auto h-20 w-20 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-4">
                Site en Maintenance
            </h1>
            
            <div class="text-gray-600 mb-6 space-y-3">
                <p class="text-lg">
                    {{ $message ?? 'Nous effectuons actuellement des travaux de maintenance sur le site.' }}
                </p>
                
                @if(isset($estimated_time) && $estimated_time)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-blue-800 font-medium">
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Temps estimé : {{ $estimated_time }}
                        </p>
                    </div>
                @endif
                
                <p class="text-sm">
                    Nous nous excusons pour la gêne occasionnée et travaillons à rétablir le service dans les plus brefs délais.
                </p>
            </div>
            
            <div class="border-t border-gray-200 pt-6">
                <p class="text-sm text-gray-500 mb-3">
                    Besoin d'aide ? Contactez-nous :
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="mailto:{{ $contact_email ?? 'support@vintapp.com' }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Nous contacter
                    </a>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-400">
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
</body>
</html>