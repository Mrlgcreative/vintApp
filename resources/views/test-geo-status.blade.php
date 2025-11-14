<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de statut - VintApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">🔍 Test de Restriction Géographique</h1>
        
        <div class="space-y-4">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                <h2 class="font-semibold text-blue-800">Votre IP</h2>
                <p class="text-blue-900">{{ request()->ip() }}</p>
            </div>

            <div class="bg-green-50 border-l-4 border-green-500 p-4">
                <h2 class="font-semibold text-green-800">Environnement</h2>
                <p class="text-green-900">{{ app()->environment() }}</p>
            </div>

            <div class="bg-primary-50 border-l-4 border-primary-500 p-4">
                <h2 class="font-semibold text-primary-800">Restriction géographique</h2>
                <p class="text-primary-900">
                    {{ config('app.disable_geo_restriction') ? '❌ DÉSACTIVÉE' : '✅ ACTIVÉE' }}
                </p>
            </div>

            @auth
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                <h2 class="font-semibold text-yellow-800">Statut utilisateur</h2>
                <p class="text-yellow-900">
                    ✅ Connecté en tant que: {{ auth()->user()->name }}
                    @if(auth()->user()->hasRole('admin'))
                        <span class="inline-block px-2 py-1 bg-red-500 text-white text-xs rounded ml-2">ADMIN (BYPASS ACTIF)</span>
                    @endif
                </p>
            </div>
            @else
            <div class="bg-gray-50 border-l-4 border-gray-500 p-4">
                <h2 class="font-semibold text-gray-800">Statut utilisateur</h2>
                <p class="text-gray-900">❌ Non connecté (visiteur)</p>
            </div>
            @endauth

            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4">
                <h2 class="font-semibold text-indigo-800">Villes autorisées</h2>
                <p class="text-indigo-900">{{ \App\Models\AllowedCity::active()->count() }} villes actives</p>
                <ul class="mt-2 text-sm text-indigo-800">
                    @foreach(\App\Models\AllowedCity::active()->limit(5)->pluck('name') as $city)
                        <li>• {{ $city }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                <h2 class="font-semibold text-red-800">Raisons possibles du bypass</h2>
                <ul class="text-red-900 space-y-1 text-sm">
                    @if(config('app.disable_geo_restriction'))
                        <li>✅ DISABLE_GEO_RESTRICTION = true</li>
                    @endif
                    
                    @if(app()->environment('local') && request()->ip() === '127.0.0.1')
                        <li>✅ IP localhost (127.0.0.1) en environnement local</li>
                    @endif
                    
                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                        <li>✅ Utilisateur admin authentifié</li>
                    @endif
                    
                    @if(request()->is('admin/*'))
                        <li>✅ Route admin (exclue de la vérification)</li>
                    @endif
                </ul>
            </div>

            <div class="mt-6 flex gap-4">
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                            Se déconnecter et tester
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-block">
                        Se connecter
                    </a>
                @endauth
                
                <a href="/" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded inline-block">
                    Tester la page d'accueil
                </a>
            </div>
        </div>
    </div>
</body>
</html>
