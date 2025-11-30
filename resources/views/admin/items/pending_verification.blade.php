@extends('layouts.admin')

@section('title', 'Items en attente de vérification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Items en attente de vérification</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Système de vérification IA - Triés par score décroissant (meilleurs articles en premier)
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($items->isEmpty())
        <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-lg text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Aucun item en attente de vérification.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($items as $item)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-6">
                        <!-- Images Grid -->
                        <div class="flex-shrink-0 mb-4 lg:mb-0">
                            <div class="grid grid-cols-2 gap-2 w-64">
                                @foreach(array_slice($item->images ?? [], 0, 4) as $index => $image)
                                    <div class="relative group cursor-pointer" onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                                        <img src="{{ asset('storage/' . $image) }}" 
                                             class="w-full h-28 object-cover rounded border-2 border-gray-200 dark:border-gray-600 group-hover:border-blue-500 transition"
                                             alt="Image {{ $index + 1 }}">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition rounded"></div>
                                    </div>
                                @endforeach
                                @if(count($item->images ?? []) > 4)
                                    <div class="w-full h-28 bg-gray-100 dark:bg-gray-700 rounded border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center">
                                        <span class="text-2xl font-bold text-gray-500 dark:text-gray-400">+{{ count($item->images) - 4 }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Item Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-1">
                                        {{ $item->name }}
                                        <span class="text-sm text-gray-500 dark:text-gray-400 font-normal">#{{ $item->id }}</span>
                                    </h3>
                                    <div class="flex items-center space-x-3 text-sm text-gray-600 dark:text-gray-400">
                                        <span>{{ $item->brand->name ?? 'N/A' }}</span>
                                        <span>•</span>
                                        <span>{{ $item->category->name ?? 'N/A' }}</span>
                                        <span>•</span>
                                        <span>{{ $item->currency_symbol ?? '' }} {{ number_format($item->price, 2, ',', ' ') }}</span>
                                    </div>
                                </div>

                                <!-- Verification Score Badge -->
                                @php
                                    $score = $item->verification_score ?? 0;
                                    $badgeColor = $score >= 75 ? 'bg-green-100 text-green-800 border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:border-green-700' 
                                                : ($score >= 50 ? 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-700' 
                                                : 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:border-red-700');
                                @endphp
                                <div class="flex-shrink-0 ml-4">
                                    <div class="px-4 py-2 rounded-full border-2 {{ $badgeColor }} text-center min-w-[80px]">
                                        <div class="text-2xl font-bold">{{ number_format($score, 0) }}</div>
                                        <div class="text-xs uppercase tracking-wide">Score IA</div>
                                    </div>
                                </div>
                            </div>

                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4 line-clamp-2">
                                {{ $item->description }}
                            </p>

                            <!-- Seller Info -->
                            @if($item->user)
                            <div class="flex items-center space-x-2 mb-4 text-sm">
                                <img src="{{ $item->user->avatar ?? asset('images/default-avatar.png') }}" 
                                     class="w-6 h-6 rounded-full"
                                     alt="{{ $item->user->name }}">
                                <span class="text-gray-600 dark:text-gray-400">Vendeur:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $item->user->name }}</span>
                            </div>
                            @endif

                            <!-- AI Analysis Details (Collapsible) -->
                            @if($item->verification_details)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden mb-4">
                                <button type="button" 
                                        onclick="toggleDetails('details-{{ $item->id }}')"
                                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition flex items-center justify-between">
                                    <span class="font-medium text-gray-900 dark:text-white">📊 Détails de l'analyse IA</span>
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 transform transition-transform" id="icon-details-{{ $item->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div id="details-{{ $item->id }}" class="hidden px-4 py-3 bg-white dark:bg-gray-800 space-y-3">
                                    @php
                                        $details = $item->verification_details;
                                        $imageScore = $details['images']['score'] ?? 0;
                                        $textScore = $details['text']['score'] ?? 0;
                                        $coherenceScore = $details['coherence']['score'] ?? 0;
                                    @endphp

                                    <!-- Images Analysis -->
                                    <div class="border-l-4 border-blue-500 pl-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-white">🖼️ Images (40%)</h4>
                                            <span class="text-sm font-bold {{ $imageScore >= 70 ? 'text-green-600' : ($imageScore >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ number_format($imageScore, 1) }}/100
                                            </span>
                                        </div>
                                        @if(isset($details['images']['issues']) && !empty($details['images']['issues']) && is_array($details['images']['issues']))
                                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                                @foreach($details['images']['issues'] as $imageKey => $issue)
                                                    <li class="flex items-start">
                                                        <span class="text-red-500 mr-2">⚠️</span>
                                                        @if(is_array($issue))
                                                            <div>
                                                                <strong>{{ $imageKey }}:</strong>
                                                                @foreach($issue['issues'] ?? [] as $singleIssue)
                                                                    <div class="ml-4">• {{ $singleIssue }}</div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span>{{ $issue }}</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-sm text-green-600 dark:text-green-400">✓ Aucun problème détecté</p>
                                        @endif
                                    </div>

                                    <!-- Text Analysis -->
                                    <div class="border-l-4 border-purple-500 pl-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-white">📝 Texte (30%)</h4>
                                            <span class="text-sm font-bold {{ $textScore >= 70 ? 'text-green-600' : ($textScore >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ number_format($textScore, 1) }}/100
                                            </span>
                                        </div>
                                        @if(isset($details['text']['issues']) && !empty($details['text']['issues']) && is_array($details['text']['issues']))
                                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                                @foreach($details['text']['issues'] as $issue)
                                                    <li class="flex items-start">
                                                        <span class="text-red-500 mr-2">⚠️</span>
                                                        <span>{{ is_array($issue) ? implode(', ', $issue) : $issue }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-sm text-green-600 dark:text-green-400">✓ Aucun problème détecté</p>
                                        @endif
                                    </div>

                                    <!-- Coherence Analysis -->
                                    <div class="border-l-4 border-orange-500 pl-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-white">🔗 Cohérence (30%)</h4>
                                            <span class="text-sm font-bold {{ $coherenceScore >= 70 ? 'text-green-600' : ($coherenceScore >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ number_format($coherenceScore, 1) }}/100
                                            </span>
                                        </div>
                                        @if(isset($details['coherence']['issues']) && !empty($details['coherence']['issues']) && is_array($details['coherence']['issues']))
                                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                                @foreach($details['coherence']['issues'] as $issue)
                                                    <li class="flex items-start">
                                                        <span class="text-red-500 mr-2">⚠️</span>
                                                        <span>{{ is_array($issue) ? implode(', ', $issue) : $issue }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-sm text-green-600 dark:text-green-400">✓ Aucun problème détecté</p>
                                        @endif
                                    </div>

                                    <!-- Admin Rejection Reason (if exists) -->
                                    @if(isset($details['admin_rejection']) && is_array($details['admin_rejection']))
                                        <div class="border-l-4 border-red-500 pl-3 bg-red-50 dark:bg-red-900/20 p-3 rounded">
                                            <h4 class="font-semibold text-red-800 dark:text-red-400 mb-1">❌ Motif de rejet précédent</h4>
                                            <p class="text-sm text-red-700 dark:text-red-300 mb-1">{{ $details['admin_rejection']['reason'] ?? 'Non spécifié' }}</p>
                                            <div class="text-xs text-red-600 dark:text-red-400 mt-2">
                                                <span>Rejeté par: {{ $details['admin_rejection']['rejected_by'] ?? 'N/A' }}</span>
                                                <span class="mx-2">•</span>
                                                <span>Le: {{ $details['admin_rejection']['rejected_at'] ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('admin.items.show', $item) }}" 
                                   class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                    👁️ Voir détails
                                </a>
                                
                                <form action="{{ route('admin.items.approve', $item) }}" method="POST" class="inline approve-form">
                                    @csrf
                                    <button type="submit" 
                                            class="approve-btn px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span class="btn-text">✓ Approuver</span>
                                        <svg class="btn-loader hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </button>
                                </form>
                                
                                <button type="button" 
                                        onclick="openRejectModal({{ $item->id }})"
                                        class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium">
                                    ✗ Rejeter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $items->links() }}
        </div>
    @endif
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeRejectModal(event)">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Rejeter l'item</h3>
            
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Motif du rejet (requis)
                    </label>
                    <textarea name="reason" 
                              id="rejectReason"
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              placeholder="Ex: Images de mauvaise qualité, description contenant du spam, incohérence entre images et description..."></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Le vendeur recevra cette raison par notification.</p>
                </div>
                
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="reject-submit-btn px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="btn-text">Confirmer le rejet</span>
                        <svg class="btn-loader hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="relative max-w-6xl max-h-screen p-4">
        <button onclick="closeImageModal()" 
                class="absolute top-6 right-6 text-white hover:text-gray-300 transition">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" class="max-w-full max-h-screen object-contain" alt="Image agrandie">
    </div>
</div>

<script>
// Gestion du chargement sur les boutons d'approbation
document.querySelectorAll('.approve-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const btn = this.querySelector('.approve-btn');
        const btnText = btn.querySelector('.btn-text');
        const btnLoader = btn.querySelector('.btn-loader');
        
        // Désactiver le bouton et afficher le loader
        btn.disabled = true;
        btnText.textContent = 'Approbation...';
        btnLoader.classList.remove('hidden');
    });
});

// Gestion du chargement sur le formulaire de rejet
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    const btn = this.querySelector('.reject-submit-btn');
    const btnText = btn.querySelector('.btn-text');
    const btnLoader = btn.querySelector('.btn-loader');
    
    // Vérifier que le motif est rempli
    const reason = document.getElementById('rejectReason').value.trim();
    if (!reason) {
        e.preventDefault();
        alert('Veuillez saisir un motif de rejet');
        return;
    }
    
    // Désactiver le bouton et afficher le loader
    btn.disabled = true;
    btnText.textContent = 'Rejet en cours...';
    btnLoader.classList.remove('hidden');
});

function toggleDetails(id) {
    const details = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        details.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function openRejectModal(itemId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/admin/items/${itemId}/reject`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectReason').focus();
    
    // Réinitialiser le bouton
    const btn = form.querySelector('.reject-submit-btn');
    const btnText = btn.querySelector('.btn-text');
    const btnLoader = btn.querySelector('.btn-loader');
    btn.disabled = false;
    btnText.textContent = 'Confirmer le rejet';
    btnLoader.classList.add('hidden');
}

function closeRejectModal(event) {
    if (!event || event.target.id === 'rejectModal') {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openImageModal(imageUrl) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageUrl;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
        closeImageModal();
    }
});
</script>
@endsection
