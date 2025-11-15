<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Simulation Paiement - VintApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 dark:bg-gray-800">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                    Test Simulation Paiement
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
                    Testez le système de paiement mobile simulé
                </p>
            </div>
            
            <!-- Formulaire de test -->
            <form id="payment-form" class="mt-8 space-y-6">
                @csrf
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="buyer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">ID Utilisateur</label>
                        <select id="buyer_id" name="buyer_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Choisir un utilisateur</option>
                            @foreach(App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Montant</label>
                        <input id="amount" name="amount" type="number" step="0.01" min="1" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="100.00" value="10.00">
                    </div>
                    
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Devise</label>
                        <select id="currency" name="currency" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="USD">USD - Dollar Américain</option>
                            <option value="CDF">CDF - Franc Congolais</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="provider" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Opérateur</label>
                        <select id="provider" name="provider" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="orange_money">Orange Money</option>
                            <option value="airtel_money">Airtel Money</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="africell">Africell Money</option>
                            <option value="illicocash">Illicocash</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Numéro de téléphone</label>
                        <input id="phone" name="phone" type="text" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="891234567" pattern="[0-9]{9}">
                    </div>
                    
                    <div>
                        <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Objet du paiement</label>
                        <input id="purpose" name="purpose" type="text" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Test de paiement simulé" value="Test de paiement simulé">
                    </div>
                </div>
                
                <div>
                    <button type="submit" id="submit-btn" 
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="btn-text">Simuler le Paiement</span>
                        <div id="loading" class="hidden ml-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </div>
            </form>
            
            <!-- Zone de résultats -->
            <div id="result" class="hidden mt-6 p-4 rounded-md">
                <div id="result-content"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('payment-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const loading = document.getElementById('loading');
            const result = document.getElementById('result');
            const resultContent = document.getElementById('result-content');
            
            // Affichage du chargement
            submitBtn.disabled = true;
            btnText.textContent = 'Traitement en cours...';
            loading.classList.remove('hidden');
            result.classList.add('hidden');
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('{{ route("payments.simulate") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    result.className = 'mt-6 p-4 rounded-md bg-green-50 border border-green-200';
                    resultContent.innerHTML = `
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Paiement réussi !</h3>
                                <div class="mt-2 text-sm text-green-700">
                                    <p><strong>Transaction ID:</strong> ${data.transaction_id}</p>
                                    <p><strong>Montant:</strong> ${data.amount} ${data.currency}</p>
                                    <p><strong>Message:</strong> ${data.message}</p>
                                    ${data.distribution ? `
                                        <div class="mt-3">
                                            <p><strong>Répartition:</strong></p>
                                            <ul class="list-disc list-inside ml-4">
                                                ${data.distribution.map(d => `
                                                    <li>${d.beneficiary_type}: ${d.amount} ${data.currency}</li>
                                                `).join('')}
                                            </ul>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="mt-4">
                                    <a href="/payments/success/${data.transaction_id}" 
                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Voir la page de succès
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    result.className = 'mt-6 p-4 rounded-md bg-red-50 border border-red-200';
                    resultContent.innerHTML = `
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Paiement échoué</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>${data.message}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                result.classList.remove('hidden');
                
            } catch (error) {
                console.error('Erreur:', error);
                result.className = 'mt-6 p-4 rounded-md bg-red-50 border border-red-200';
                resultContent.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erreur de communication</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Une erreur est survenue lors de la communication avec le serveur.</p>
                            </div>
                        </div>
                    </div>
                `;
                result.classList.remove('hidden');
            } finally {
                // Restaurer le bouton
                submitBtn.disabled = false;
                btnText.textContent = 'Simuler le Paiement';
                loading.classList.add('hidden');
            }
        });
    </script>
</body>
</html>