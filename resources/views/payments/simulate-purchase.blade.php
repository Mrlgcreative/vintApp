@extends('app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-green-600 text-white px-6 py-4 text-center">
                <h4 class="text-xl font-semibold flex items-center justify-center">
                    <i class="fas fa-shopping-cart mr-3"></i>Test d'Achat avec Paiement Simulé
                </h4>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Simulation d'achat</h5>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Cette page simule un achat complet avec paiement mobile money. 
                        Les données sont créées automatiquement pour les tests.
                    </p>
                    
                    <div id="cart-simulation" class="space-y-3 mb-6">
                        <h6 class="font-medium text-gray-900 dark:text-white">Articles simulés dans le panier :</h6>
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-900 dark:text-white">Article Test 1 x 1</span>
                                <span class="font-semibold text-gray-900 dark:text-white">15.00 USD</span>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-900 dark:text-white">Article Test 2 x 2</span>
                                <span class="font-semibold text-gray-900 dark:text-white">30.00 USD</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-semibold text-gray-900 dark:text-white">Total simulé :</span>
                                <span class="text-xl font-bold text-green-600">45.00 USD</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form id="test-payment-form">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="test-buyer" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Utilisateur test</label>
                            <select id="test-buyer" name="buyer_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Choisir un utilisateur</option>
                                @foreach(App\Models\User::take(10)->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="test-phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Numéro Mobile Money</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-r-0 border-gray-300 rounded-l-lg text-sm font-medium text-gray-700 dark:text-gray-200">+243</span>
                                <select id="test-phone" name="phone" required class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-r-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="850123456">850123456 (Orange Money)</option>
                                    <option value="810987654">810987654 (M-Pesa)</option>
                                    <option value="970456789">970456789 (Airtel Money)</option>
                                    <option value="900123789">900123789 (Africell Money)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="test-amount" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Montant</label>
                            <input type="number" id="test-amount" name="amount" value="45.00" step="0.01" min="1" required 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="test-currency" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Devise</label>
                            <select id="test-currency" name="currency" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="USD">USD - Dollar Américain</option>
                                <option value="CDF">CDF - Franc Congolais</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="test-purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Objet du paiement</label>
                        <input type="text" id="test-purpose" name="purpose" value="Test d'achat simulé - VintApp" required 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    
                    <button type="submit" id="test-submit-btn" 
                            class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors font-medium text-lg">
                        <i class="fas fa-credit-card mr-2"></i>
                        <span id="test-btn-text">Simuler l'Achat Complet</span>
                        <div id="test-loading" class="hidden ml-2 inline-block">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </form>
                
                <div id="test-result" class="hidden mt-6"></div>
                
                <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h6 class="font-medium text-blue-900 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Comment ça fonctionne
                    </h6>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Cette simulation crée une transaction réelle dans la base de données</li>
                        <li>Le paiement a 80% de chance de réussir (simulation réaliste)</li>
                        <li>En cas de succès, des commandes sont automatiquement créées</li>
                        <li>Le système de notation post-achat se déclenche automatiquement</li>
                        <li>Les fonds sont attribués aux wallets "pending" des vendeurs</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('test-payment-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('test-submit-btn');
    const btnText = document.getElementById('test-btn-text');
    const loading = document.getElementById('test-loading');
    const result = document.getElementById('test-result');
    
    // Éléments du formulaire
    const buyerId = document.getElementById('test-buyer').value;
    const phone = document.getElementById('test-phone').value;
    const amount = document.getElementById('test-amount').value;
    const currency = document.getElementById('test-currency').value;
    const purpose = document.getElementById('test-purpose').value;
    
    // Validation
    if (!buyerId) {
        showResult('error', 'Veuillez sélectionner un utilisateur');
        return;
    }
    
    // Déterminer le provider selon le numéro
    let provider = 'orange_money';
    if (phone.startsWith('81')) provider = 'mpesa';
    else if (phone.startsWith('97')) provider = 'airtel_money';
    else if (phone.startsWith('90')) provider = 'africell';
    
    // Interface de chargement
    submitBtn.disabled = true;
    btnText.textContent = 'Traitement en cours...';
    loading.classList.remove('hidden');
    result.classList.add('hidden');
    
    try {
        console.log('Envoi de la simulation...', { buyerId, phone, amount, currency, purpose, provider });
        
        const response = await fetch('{{ route("payments.simulate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                buyer_id: parseInt(buyerId),
                phone: phone,
                amount: parseFloat(amount),
                currency: currency,
                purpose: purpose,
                provider: provider
            })
        });
        
        const data = await response.json();
        console.log('Réponse reçue:', data);
        
        if (response.ok && data.status === 'success') {
            showResult('success', `
                <h3 class="text-lg font-semibold text-green-900 mb-3">🎉 Achat simulé avec succès !</h3>
                <div class="space-y-2 text-green-800">
                    <p><strong>Transaction ID:</strong> ${data.transaction_id}</p>
                    <p><strong>Montant payé:</strong> ${data.amount} ${data.currency}</p>
                    <p><strong>Message:</strong> ${data.message}</p>
                    ${data.distribution ? `
                        <div class="mt-4">
                            <p class="font-medium">Répartition des fonds:</p>
                            <ul class="list-disc list-inside ml-4 mt-2">
                                ${data.distribution.map(d => `
                                    <li>${d.beneficiary_type}: ${d.amount} ${data.currency}</li>
                                `).join('')}
                            </ul>
                        </div>
                    ` : ''}
                </div>
                <div class="mt-6 flex gap-3">
                    <a href="/payments/success/${data.transaction_id}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check-circle mr-2"></i>Voir la Page de Succès
                    </a>
                    <button onclick="location.reload()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-redo mr-2"></i>Nouveau Test
                    </button>
                </div>
            `);
        } else {
            showResult('error', `
                <h3 class="text-lg font-semibold text-red-900 mb-3">❌ Échec de la simulation</h3>
                <div class="text-red-800">
                    <p><strong>Erreur:</strong> ${data.message || 'Une erreur est survenue'}</p>
                    <p class="text-sm mt-2">Ceci est normal dans un environnement de test - réessayez !</p>
                </div>
                <div class="mt-4">
                    <button onclick="location.reload()" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-redo mr-2"></i>Réessayer
                    </button>
                </div>
            `);
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        showResult('error', `
            <h3 class="text-lg font-semibold text-red-900 mb-3">❌ Erreur de communication</h3>
            <div class="text-red-800">
                <p>Impossible de communiquer avec le serveur.</p>
                <p class="text-sm mt-2"><strong>Détail:</strong> ${error.message}</p>
            </div>
            <div class="mt-4">
                <button onclick="location.reload()" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-redo mr-2"></i>Réessayer
                </button>
            </div>
        `);
    } finally {
        // Restaurer le bouton
        submitBtn.disabled = false;
        btnText.textContent = 'Simuler l\'Achat Complet';
        loading.classList.add('hidden');
    }
});

function showResult(type, content) {
    const result = document.getElementById('test-result');
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
    
    result.innerHTML = `
        <div class="${bgColor} border rounded-lg p-6">
            ${content}
        </div>
    `;
    result.classList.remove('hidden');
    
    // Scroll vers le résultat
    result.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>
@endpush
@endsection