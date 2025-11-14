<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Révocation Expert</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <h1>Test de Révocation Expert</h1>
    
    <div id="output"></div>
    
    <button onclick="testRevokeExpert()">Tester la révocation (Expert ID: 1)</button>
    
    <script>
    function testRevokeExpert() {
        const output = document.getElementById('output');
        output.innerHTML = '<p>Test en cours...</p>';
        
        const expertId = 1; // ID de test
        
        console.log('Début du test de révocation pour expert ID:', expertId);
        
        // Vérifier le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            output.innerHTML = '<p style="color: red;">❌ Token CSRF manquant</p>';
            return;
        }
        
        console.log('Token CSRF trouvé:', csrfToken.getAttribute('content'));
        
        fetch(`/admin/experts/${expertId}/revoke`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Status de la réponse:', response.status);
            console.log('Headers de la réponse:', response.headers);
            
            // Vérifier si la réponse est OK
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Erreur HTTP ${response.status}: ${text}`);
                });
            }
            
            // Vérifier le type de contenu
            const contentType = response.headers.get('content-type');
            console.log('Type de contenu:', contentType);
            
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error(`Réponse non-JSON reçue: ${text}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            
            if (data.success) {
                output.innerHTML = `<p style="color: green;">✅ Succès: ${data.message}</p>`;
            } else {
                output.innerHTML = `<p style="color: orange;">⚠️ Échec: ${data.message}</p>`;
            }
        })
        .catch(error => {
            console.error('Erreur complète:', error);
            output.innerHTML = `<p style="color: red;">❌ Erreur: ${error.message}</p>`;
        });
    }
    </script>
</body>
</html>