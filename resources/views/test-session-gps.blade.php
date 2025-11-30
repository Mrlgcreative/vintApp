<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Session GPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-4">🧪 Test Session GPS</h1>
        
        <div class="space-y-4">
            <!-- État session -->
            <div class="p-4 bg-blue-50 rounded-lg">
                <h2 class="font-semibold mb-2">📊 État de la session :</h2>
                <pre class="text-sm bg-gray-800 text-green-400 p-3 rounded overflow-x-auto">{{ json_encode(session()->all(), JSON_PRETTY_PRINT) }}</pre>
            </div>

            <!-- Test validation -->
            <div class="p-4 bg-purple-50 rounded-lg">
                <h2 class="font-semibold mb-2">🎯 Test validation Kolwezi :</h2>
                <button onclick="testGPS()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                    📍 Tester GPS Kolwezi
                </button>
                <div id="result" class="mt-3"></div>
            </div>

            <!-- Actions -->
            <div class="p-4 bg-green-50 rounded-lg">
                <h2 class="font-semibold mb-2">🔄 Actions :</h2>
                <div class="flex gap-2">
                    <button onclick="location.reload()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        🔄 Recharger
                    </button>
                    <button onclick="clearSession()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        🗑️ Vider session
                    </button>
                    <a href="{{ route('home') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-block">
                        🏠 Aller à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function testGPS() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<p class="text-gray-600">⏳ Test en cours...</p>';

            try {
                const response = await fetch('{{ route("location.validate.post") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        latitude: -10.723847,  // Position Kolwezi
                        longitude: 25.5046347
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="p-3 bg-green-100 border border-green-300 rounded">
                            <p class="font-semibold text-green-800">✅ Validation réussie !</p>
                            <p class="text-sm text-green-700">Ville: ${data.city}</p>
                            <p class="text-sm text-green-700">Distance: ${data.distance} km</p>
                            <p class="text-xs text-green-600 mt-2">Session devrait être sauvegardée. Rechargez pour vérifier.</p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="p-3 bg-red-100 border border-red-300 rounded">
                            <p class="font-semibold text-red-800">❌ Validation échouée</p>
                            <p class="text-sm text-red-700">${data.message}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="p-3 bg-red-100 border border-red-300 rounded">
                        <p class="font-semibold text-red-800">❌ Erreur</p>
                        <p class="text-sm text-red-700">${error.message}</p>
                    </div>
                `;
            }
        }

        async function clearSession() {
            if (confirm('Vider la session GPS ?')) {
                try {
                    await fetch('{{ route("logout") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    location.reload();
                } catch (error) {
                    console.error(error);
                    location.reload();
                }
            }
        }
    </script>
</body>
</html>
