<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vérification de localisation - VintApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-blue-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <!-- Logo/Icon -->
            <div class="mb-6">
                <div class="w-24 h-24 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-3">📍 Vérification de localisation</h1>
            <p class="text-gray-600 mb-6">VintApp utilise votre position GPS pour vérifier que vous êtes dans une ville où le service est disponible.</p>

            <!-- Status -->
            <div id="status" class="mb-6">
                <div class="flex items-center justify-center space-x-2 text-gray-500">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>En attente de votre autorisation...</span>
                </div>
            </div>

            <!-- Villes autorisées -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <p class="text-sm font-semibold text-blue-900 mb-2">🏙️ Villes actuellement disponibles :</p>
                <div class="flex flex-wrap gap-2 justify-center">
                    @foreach(\App\Models\AllowedCity::active()->orderBy('name')->get() as $city)
                        <span class="bg-white text-blue-700 text-xs px-3 py-1 rounded-full shadow-sm">
                            {{ $city->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- Bouton -->
            <button 
                onclick="requestLocation()" 
                id="requestBtn"
                class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold py-4 px-6 rounded-xl hover:from-purple-700 hover:to-blue-700 transition transform hover:scale-105 shadow-lg"
            >
                📍 Autoriser la géolocalisation
            </button>

            <!-- Info -->
            <p class="text-xs text-gray-500 mt-4">
                🔒 Vos données de localisation sont uniquement utilisées pour vérifier votre éligibilité. Elles ne sont pas stockées ni partagées.
            </p>
        </div>
    </div>

    <script>
        function showStatus(message, type = 'info') {
            const statusDiv = document.getElementById('status');
            const colors = {
                info: 'text-blue-600',
                success: 'text-green-600',
                error: 'text-red-600',
                warning: 'text-yellow-600'
            };
            
            statusDiv.innerHTML = `
                <div class="flex items-center justify-center space-x-2 ${colors[type]}">
                    <span>${message}</span>
                </div>
            `;
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Rayon de la Terre en km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        async function requestLocation() {
            const btn = document.getElementById('requestBtn');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            if (!navigator.geolocation) {
                showStatus('❌ Géolocalisation non supportée par votre navigateur', 'error');
                btn.disabled = false;
                btn.innerHTML = '📍 Autoriser la géolocalisation';
                return;
            }

            showStatus('📡 Obtention de votre position GPS...', 'info');

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    showStatus(`📍 Position détectée: ${lat.toFixed(4)}, ${lon.toFixed(4)}`, 'info');

                    try {
                        // ✅ Utiliser la route web au lieu de l'API pour que la session fonctionne
                        const response = await fetch('{{ route("location.validate.post") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin', // Important pour les cookies de session
                            body: JSON.stringify({ latitude: lat, longitude: lon })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showStatus(`✅ Accès autorisé ! Ville détectée: ${data.city}`, 'success');
                            setTimeout(() => {
                                window.location.href = '{{ route("home") }}';
                            }, 1500);
                        } else {
                            showStatus(`❌ ${data.message}`, 'error');
                            btn.disabled = false;
                            btn.innerHTML = '🔄 Réessayer';
                        }
                    } catch (error) {
                        showStatus('❌ Erreur de connexion', 'error');
                        btn.disabled = false;
                        btn.innerHTML = '🔄 Réessayer';
                    }
                },
                (error) => {
                    let message = 'Erreur de géolocalisation';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = "❌ Vous devez autoriser la géolocalisation pour utiliser VintApp";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = "❌ Position non disponible";
                            break;
                        case error.TIMEOUT:
                            message = "❌ Délai d'attente dépassé";
                            break;
                    }
                    showStatus(message, 'error');
                    btn.disabled = false;
                    btn.innerHTML = '🔄 Réessayer';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        // Auto-demander la position au chargement
        window.addEventListener('load', () => {
            setTimeout(() => {
                requestLocation();
            }, 1000);
        });
    </script>
</body>
</html>
