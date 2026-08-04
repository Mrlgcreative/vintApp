@extends('layouts.admin')

@section('title', 'Vérifier - ' . $item->name)
@section('page-title', 'Vérification d\'article')
@section('page-subtitle', 'Examinez attentivement cet article et décidez de l\'approuver ou de le rejeter')

@section('content')

    {{-- Navigation --}}
    <div class="mb-5">
        <a href="{{ route('expert.items.pending') }}" class="inline-flex items-center text-sm text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la liste
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Images --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center">
                        <i class="fas fa-images mr-2.5 text-primary-500"></i>
                        Images ({{ count($item->images ?? []) }})
                    </h2>
                </div>

                <div class="p-6">
                    @if(!empty($item->images))
                        @php
                            $imageUrls = $item->getImageUrls();
                        @endphp
                        <div class="space-y-4">
                            @foreach($imageUrls as $index => $imageUrl)
                                <div class="relative group cursor-pointer rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-700">
                                    <img src="{{ $imageUrl }}"
                                         class="w-full h-96 object-cover group-hover:brightness-110 transition"
                                         alt="Image {{ $index + 1 }}"
                                         onclick="openImageModal('{{ $imageUrl }}')">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white text-3xl opacity-0 group-hover:opacity-100 transition"></i>
                                    </div>
                                    <div class="absolute top-3 right-3 bg-slate-900/80 text-white px-3 py-1 rounded-lg text-sm font-medium backdrop-blur">
                                        {{ $index + 1 }}/{{ count($item->images) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-image text-4xl text-slate-300 dark:text-slate-600 mb-4"></i>
                            <p class="text-slate-500 dark:text-slate-400">Aucune image disponible</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center">
                        <i class="fas fa-align-left mr-2.5 text-primary-500"></i>
                        Description
                    </h2>
                </div>

                <div class="p-6">
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">
                        {{ $item->description ?? 'Aucune description' }}
                    </p>
                </div>
            </div>

            {{-- Détails supplémentaires --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center">
                        <i class="fas fa-info-circle mr-2.5 text-primary-500"></i>
                        Détails de l'article
                    </h2>
                </div>

                <div class="p-6 grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider mb-1">Catégorie</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">
                            {{ $item->category?->name ?? 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider mb-1">Marque</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">
                            {{ $item->brand?->name ?? 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider mb-1">Prix</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">
                            {{ number_format($item->price, 0, ',', ' ') }} FCFA
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider mb-1">État</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-white capitalize">
                            {{ $item->condition ?? 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider mb-1">Créé le</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">
                            {{ $item->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider mb-1">Quantité disponible</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">
                            {{ $item->quantity ?? 1 }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Informations du vendeur --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center">
                        <i class="fas fa-user mr-2.5 text-primary-500"></i>
                        Informations du vendeur
                    </h2>
                </div>

                <div class="p-6">
                    <div class="flex items-start gap-4">
                        @if($item->user->avatar)
                            <img src="{{ asset('storage/' . $item->user->avatar) }}"
                                 class="w-14 h-14 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600"
                                 alt="{{ $item->user->name }}">
                        @else
                            <div class="w-14 h-14 rounded-full bg-primary-600 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-primary-600/25">
                                {{ substr($item->user->name, 0, 1) }}
                            </div>
                        @endif

                        <div class="flex-1">
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                {{ $item->user->name }}
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                <i class="fas fa-envelope mr-2"></i>
                                {{ $item->user->email }}
                            </p>
                            @if($item->user->phone)
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    <i class="fas fa-phone mr-2"></i>
                                    {{ $item->user->phone }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barre latérale (Décision) --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm lg:sticky lg:top-24 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center">
                        <i class="fas fa-gavel mr-2.5 text-primary-500"></i>
                        Votre décision
                    </h2>
                </div>

                <form id="verificationForm" method="POST" action="{{ route('expert.items.submit-verification', $item) }}" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="decision" id="decisionInput" value="">

                    <button type="submit"
                            onclick="setDecision('approved')"
                            class="w-full px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2 shadow-sm shadow-emerald-600/20">
                        <i class="fas fa-check-circle"></i>
                        Approuver
                    </button>

                    <button type="button"
                            onclick="toggleRejectForm()"
                            class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2 shadow-sm shadow-red-600/20">
                        <i class="fas fa-times-circle"></i>
                        Rejeter
                    </button>

                    <div id="rejectForm" class="hidden space-y-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">
                                Raison du rejet
                            </label>
                            <textarea id="rejectionReason"
                                      name="rejection_reason"
                                      placeholder="Veuillez expliquer pourquoi vous rejetez cet article..."
                                      rows="4"
                                      class="w-full px-3.5 py-3 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-red-500/40 focus:border-red-500 focus:outline-none resize-none transition-colors"></textarea>
                        </div>

                        <button type="submit"
                                onclick="setDecision('rejected')"
                                class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i>
                            Confirmer le rejet
                        </button>

                        <button type="button"
                                onclick="toggleRejectForm()"
                                class="w-full px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-white rounded-xl text-sm font-semibold transition">
                            Annuler
                        </button>
                    </div>
                </form>

                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                    <h3 class="text-xs font-semibold text-slate-900 dark:text-white mb-4 flex items-center uppercase tracking-wider">
                        <i class="fas fa-list-check mr-2 text-primary-500"></i>
                        Points de contrôle
                    </h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded text-primary-600 focus:ring-primary-500/40" id="check1">
                            <label for="check1" class="text-slate-600 dark:text-slate-400 cursor-pointer">
                                Les images sont claires et de bonne qualité
                            </label>
                        </li>
                        <li class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded text-primary-600 focus:ring-primary-500/40" id="check2">
                            <label for="check2" class="text-slate-600 dark:text-slate-400 cursor-pointer">
                                La description correspond aux images
                            </label>
                        </li>
                        <li class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded text-primary-600 focus:ring-primary-500/40" id="check3">
                            <label for="check3" class="text-slate-600 dark:text-slate-400 cursor-pointer">
                                Le prix est raisonnable
                            </label>
                        </li>
                        <li class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded text-primary-600 focus:ring-primary-500/40" id="check4">
                            <label for="check4" class="text-slate-600 dark:text-slate-400 cursor-pointer">
                                Le produit est authentique (selon votre expertise)
                            </label>
                        </li>
                        <li class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded text-primary-600 focus:ring-primary-500/40" id="check5">
                            <label for="check5" class="text-slate-600 dark:text-slate-400 cursor-pointer">
                                Aucun contenu offensant ou illégal
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal pour les images --}}
    <div id="imageModal" class="hidden fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4">
        <div class="max-w-4xl w-full relative">
            <img id="modalImage" src="" class="w-full h-auto" alt="">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-slate-300 transition w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function setDecision(decision) {
    document.getElementById('decisionInput').value = decision;
}

function toggleRejectForm() {
    const rejectForm = document.getElementById('rejectForm');
    rejectForm.classList.toggle('hidden');
    if (!rejectForm.classList.contains('hidden')) {
        document.getElementById('rejectionReason').focus();
    }
}

function openImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    const modal = document.getElementById('imageModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('imageModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    document.getElementById('verificationForm').addEventListener('submit', function(e) {
        const decision = document.getElementById('decisionInput').value;
        if (!decision) {
            e.preventDefault();
            alert('Veuillez faire une sélection : approuver ou rejeter');
            return;
        }

        if (decision === 'rejected') {
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                e.preventDefault();
                alert('Veuillez fournir une raison pour le rejet');
                return;
            }
        }
    });
});
</script>
@endpush
