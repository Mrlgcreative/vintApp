@extends('app')

@section('title', 'Demander la vérification d\'authenticité')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-8">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Vérification d'authenticité</h1>
                    <p class="text-gray-600 mb-4">
                        Obtenez le badge <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Authentifié par VintApp</span> pour renforcer la confiance des acheteurs.
                    </p>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-blue-400">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $item->name }}</h3>
                        <p class="text-gray-600">{{ $item->brand->name ?? 'Marque non spécifiée' }} • {{ $item->category->name ?? 'Catégorie' }}</p>
                        <p class="text-2xl font-bold text-indigo-600 mt-2">{{ $item->formatted_price }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avantages de la vérification -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Badge de confiance</h3>
                <p class="text-gray-600 text-sm">Obtenez un badge visible qui rassure les acheteurs sur l'authenticité de votre produit.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 border">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Meilleures ventes</h3>
                <p class="text-gray-600 text-sm">Les produits vérifiés se vendent jusqu'à 3x plus rapidement avec un prix premium.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 border">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Protection anti-fraude</h3>
                <p class="text-gray-600 text-sm">Protection renforcée contre les litiges et les remboursements frauduleux.</p>
            </div>
        </div>

        <!-- Formulaire de demande -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Informations de vérification</h2>
            
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erreurs détectées :</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('authenticity.submit', $item) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Images du produit (obligatoires) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Photos du produit <span class="text-red-500">*</span>
                    </label>
                    <p class="text-sm text-gray-500 mb-4">Fournissez au moins 3 photos HD : vue de face, de dos, et de profil. Maximum 4 photos.</p>
                    <input type="file" name="product_images[]" accept="image/*" multiple required 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPG, PNG. Taille max: 10MB par image.</p>
                </div>

                <!-- Certificat d'authenticité (optionnel) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Certificat d'authenticité ou garantie
                    </label>
                    <input type="file" name="certificate" accept="image/*,application/pdf" 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                    <p class="text-xs text-gray-500 mt-1">Si disponible, joindre le certificat officiel.</p>
                </div>

                <!-- Reçu d'achat (optionnel) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Reçu ou facture d'achat
                    </label>
                    <input type="file" name="receipt" accept="image/*,application/pdf" 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                    <p class="text-xs text-gray-500 mt-1">Preuve d'achat pour renforcer l'authenticité.</p>
                </div>

                <!-- Informations supplémentaires -->
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Numéro de série ou code produit
                        </label>
                        <input type="text" id="serial_number" name="serial_number" 
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Ex: ABC123456">
                    </div>
                    
                    <div>
                        <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date d'achat
                        </label>
                        <input type="date" id="purchase_date" name="purchase_date" 
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label for="purchase_location" class="block text-sm font-medium text-gray-700 mb-2">
                        Lieu d'achat
                    </label>
                    <input type="text" id="purchase_location" name="purchase_location" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ex: Boutique officielle, Site web de la marque...">
                </div>

                <div>
                    <label for="additional_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes supplémentaires
                    </label>
                    <textarea id="additional_notes" name="additional_notes" rows="3"
                              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Toute information qui pourrait aider à vérifier l'authenticité..."></textarea>
                </div>

                <!-- Conditions -->
                <div class="border-t pt-6">
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" id="terms_accepted" name="terms_accepted" required
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="terms_accepted" class="text-sm text-gray-700">
                            J'accepte les <a href="#" class="text-blue-600 hover:underline">conditions de vérification</a> et je certifie que les informations fournies sont exactes. 
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                </div>

                <!-- Informations sur les frais -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">💰 Frais de vérification</h3>
                    <p class="text-sm text-gray-600 mb-2">
                        <span class="font-semibold">${{ number_format(5.00 * (isset($multiplier) ? $multiplier : 1.0), 2) }}</span> 
                        pour cette catégorie de produit.
                    </p>
                    <p class="text-xs text-gray-500">
                        ✓ Analyse par intelligence artificielle<br>
                        ✓ Vérification par expert certifié si nécessaire<br>
                        ✓ Badge d'authenticité permanent<br>
                        ✓ Protection anti-fraude renforcée
                    </p>
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-between pt-6">
                    <a href="{{ route('items.show', $item) }}" 
                       class="text-gray-600 hover:text-gray-800 font-medium">
                        ← Retour au produit
                    </a>
                    
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Soumettre la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('input[name="product_images[]"]').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    if (files.length < 3) {
        alert('Veuillez sélectionner au moins 3 photos du produit.');
        e.target.value = '';
    }
    if (files.length > 4) {
        alert('Maximum 4 photos autorisées.');
        e.target.value = '';
    }
});
</script>
@endsection