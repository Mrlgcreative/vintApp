@extends('layouts.admin')

@section('title', 'Vérification #' . $check->id)
@section('page-title', 'Vérification #' . $check->id)
@section('page-subtitle', 'Examen expert - ' . ($check->item->name ?? 'Produit sans nom'))

@section('content')

    {{-- Bandeau retour --}}
    <div class="mb-5">
        <a href="{{ route('expert.verifications.index') }}" class="inline-flex items-center text-sm text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la liste
        </a>
    </div>

    {{-- Statut si terminé --}}
    @if(in_array($check->status, ['expert_approved', 'expert_rejected']))
        @php
            $isApproved = $check->status === 'expert_approved';
        @endphp
        <div class="rounded-2xl border p-5 mb-6 flex items-center gap-4 {{ $isApproved ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-900/30 dark:bg-emerald-900/20' : 'border-red-200 bg-red-50 dark:border-red-900/30 dark:bg-red-900/20' }}">
            <div class="w-11 h-11 rounded-full flex items-center justify-center text-white {{ $isApproved ? 'bg-emerald-500' : 'bg-red-500' }}">
                <i class="fas fa-{{ $isApproved ? 'check' : 'times' }}"></i>
            </div>
            <div>
                <h4 class="font-semibold {{ $isApproved ? 'text-emerald-800 dark:text-emerald-300' : 'text-red-800 dark:text-red-300' }}">
                    Vérification {{ $isApproved ? 'Approuvée' : 'Rejetée' }}
                </h4>
                <p class="text-sm {{ $isApproved ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                    Cette vérification a été finalisée le {{ $check->expert_completed_at->format('d/m/Y à H:i') }}
                </p>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        {{-- Informations sur le produit --}}
        <div class="p-6 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-5">
                <i class="fas fa-box-open mr-2 text-primary-500"></i>
                Informations sur le produit
            </h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @forelse($check->item->images ?? [] as $image)
                            <div class="aspect-square rounded-xl overflow-hidden ring-1 ring-slate-200 dark:ring-slate-700 cursor-pointer hover:ring-primary-500 dark:hover:ring-primary-500 transition-all group relative"
                                 data-bs-toggle="modal" data-bs-target="#imageModal"
                                 data-image="{{ asset('storage/' . $image) }}">
                                <img src="{{ asset('storage/' . $image) }}"
                                     alt="Image produit" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/20 transition-colors">
                                    <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                </div>
                            </div>
                        @empty
                            <div class="aspect-square bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                                <i class="fas fa-image text-slate-400 text-2xl"></i>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                            <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Titre</div>
                            <div class="text-base text-slate-900 dark:text-white">{{ $check->item->name ?? 'Produit sans nom' }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Prix</div>
                                <div class="text-base font-semibold text-slate-900 dark:text-white">{{ number_format($check->item->price, 0, ',', ' ') }} {{ $check->item->currency }}</div>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Catégorie</div>
                                <div class="text-base text-slate-900 dark:text-white">{{ $check->item->category->name ?? 'Non spécifiée' }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Marque</div>
                                <div class="text-base text-slate-900 dark:text-white">{{ $check->item->brand->name ?? 'Non spécifiée' }}</div>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">État</div>
                                <div class="text-base text-slate-900 dark:text-white">{{ ucfirst($check->item->condition ?? 'Non spécifié') }}</div>
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                            <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Vendeur</div>
                            <div class="text-base text-slate-900 dark:text-white">
                                {{ $check->vendor->name }}
                                <div class="text-sm text-slate-500 dark:text-slate-400">{{ $check->vendor->email }}</div>
                            </div>
                        </div>
                    </div>

                    @if($check->item->description)
                        <div class="mt-4">
                            <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Description</div>
                            <div class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap">{{ $check->item->description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Images de vérification soumises --}}
        @if($check->verificationImages->count() > 0)
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-5">
                    <i class="fas fa-camera mr-2 text-blue-500"></i>
                    Images de vérification soumises ({{ $check->verificationImages->count() }})
                </h3>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($check->verificationImages as $image)
                        <div class="aspect-square rounded-xl overflow-hidden ring-2 ring-slate-200 dark:ring-slate-700 cursor-pointer hover:ring-primary-500 dark:hover:ring-primary-500 transition-all group relative"
                             data-bs-toggle="modal" data-bs-target="#imageModal"
                             data-image="{{ asset('storage/' . $image->image_path) }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="Image de vérification" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/20 transition-colors">
                                <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Analyse IA --}}
        @if($check->ai_analysis_result)
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-5">
                    <i class="fas fa-robot mr-2 text-amber-500"></i>
                    Analyse Intelligence Artificielle
                </h3>

                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-6">
                    @php
                        $aiData = is_string($check->ai_analysis_result)
                            ? json_decode($check->ai_analysis_result, true)
                            : $check->ai_analysis_result;
                        $score = $aiData['authenticity_score'] ?? 50;
                        $scoreColor = $score >= 70 ? 'bg-emerald-500' : ($score >= 40 ? 'bg-amber-500' : 'bg-red-500');
                    @endphp

                    <div class="flex items-center mb-5">
                        <div class="w-20 h-20 {{ $scoreColor }} rounded-full flex items-center justify-center text-white text-xl font-bold mr-6 shadow-lg">
                            {{ $score }}%
                        </div>
                        <div>
                            <h5 class="text-base font-semibold text-slate-900 dark:text-white">Score d'authenticité IA</h5>
                            <p class="text-sm text-slate-600 dark:text-slate-300">
                                @if($score >= 70)
                                    <span class="text-emerald-600 dark:text-emerald-400">Très probable que le produit soit authentique</span>
                                @elseif($score >= 40)
                                    <span class="text-amber-600 dark:text-amber-400">Authenticité incertaine - Examen expert requis</span>
                                @else
                                    <span class="text-red-600 dark:text-red-400">Suspicion de contrefaçon</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if(isset($aiData['details']))
                        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 ring-1 ring-slate-200 dark:ring-slate-700">
                            <h6 class="font-semibold text-slate-900 dark:text-white mb-2 text-sm">Détails de l'analyse :</h6>
                            <ul class="space-y-1.5 text-sm">
                                @foreach($aiData['details'] as $detail)
                                    <li class="text-slate-700 dark:text-slate-200 flex items-start gap-2">
                                        <i class="fas fa-circle text-[6px] mt-2 text-slate-400"></i>
                                        <span>{{ $detail }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Historique --}}
        <div class="p-6 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-5">
                <i class="fas fa-history mr-2 text-slate-400"></i>
                Historique de la vérification
            </h3>

            <div class="relative">
                <div class="absolute left-[7px] top-2 bottom-2 w-0.5 bg-slate-200 dark:bg-slate-700"></div>

                @foreach($check->auditLogs->sortBy('created_at') as $log)
                    <div class="relative mb-5 last:mb-0 pl-8">
                        <div class="absolute left-0 top-1.5 w-3.5 h-3.5 bg-primary-600 rounded-full border-2 border-white dark:border-slate-800 shadow"></div>
                        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-2 gap-2">
                                <strong class="text-sm text-slate-900 dark:text-white">{{ $log->getActionLabel() }}</strong>
                                <small class="text-xs text-slate-500 dark:text-slate-400 flex-shrink-0">{{ $log->created_at->format('d/m/Y à H:i') }}</small>
                            </div>
                            @if($log->performer)
                                <p class="text-sm text-slate-600 dark:text-slate-300 mb-1">Par: {{ $log->performer->name }}</p>
                            @endif
                            @if($log->details)
                                @php
                                    $details = is_string($log->details) ? json_decode($log->details, true) : $log->details;
                                @endphp
                                @if(is_array($details))
                                    <div class="text-xs text-slate-500 dark:text-slate-400 space-y-0.5">
                                        @foreach($details as $key => $value)
                                            <div>
                                                <strong>{{ ucfirst($key) }}:</strong>
                                                @if(is_array($value))
                                                    {{ json_encode($value) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Notes expert existantes --}}
        @if($check->expert_notes)
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-5">
                    <i class="fas fa-comment mr-2 text-primary-500"></i>
                    Notes de l'expert
                </h3>
                <div class="bg-slate-50 dark:bg-slate-900 border-l-4 border-primary p-4 rounded-xl">
                    <p class="text-sm text-slate-700 dark:text-slate-200">{{ $check->expert_notes }}</p>
                </div>
            </div>
        @endif

        {{-- Formulaire de décision expert --}}
        @if($check->status === 'expert_review')
            <div class="p-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-5">
                    <i class="fas fa-gavel mr-2 text-emerald-600"></i>
                    Finaliser la vérification
                </h3>

                <form action="{{ route('expert.verifications.finalize', $check) }}" method="POST"
                      class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-6" id="expertForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="border-2 border-emerald-300 dark:border-emerald-700 rounded-2xl p-5 cursor-pointer hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors decision-button approve"
                             data-decision="approve">
                            <div class="text-center">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                                </div>
                                <h5 class="font-semibold text-slate-900 dark:text-white">Approuver</h5>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Le produit est authentique</p>
                            </div>
                        </div>

                        <div class="border-2 border-red-300 dark:border-red-700 rounded-2xl p-5 cursor-pointer hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors decision-button reject"
                             data-decision="reject">
                            <div class="text-center">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                                </div>
                                <h5 class="font-semibold text-slate-900 dark:text-white">Rejeter</h5>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Le produit n'est pas authentique</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="decision" id="decisionInput" required>

                    <div class="mb-6">
                        <label for="expert_notes" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                            <i class="fas fa-pen mr-1"></i>
                            Notes et justification (obligatoire)
                        </label>
                        <textarea class="w-full px-4 py-3 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 focus:outline-none transition-colors"
                                  name="expert_notes" id="expert_notes" rows="4"
                                  placeholder="Détaillez votre analyse et les raisons de votre décision..."
                                  required>{{ old('expert_notes') }}</textarea>
                        @error('expert_notes')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                            <i class="fas fa-percent mr-1"></i>
                            Niveau de confiance
                        </label>
                        <div class="text-center text-2xl font-bold text-primary-600 dark:text-primary-400 mb-2" id="confidenceDisplay">85%</div>
                        <input type="range" class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-primary-600"
                               name="confidence_level" id="confidenceSlider" min="1" max="100" value="85" required>
                        <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                            <span>Très incertain</span>
                            <span>Totalement certain</span>
                        </div>
                        @error('confidence_level')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center">
                        <button type="submit"
                                class="px-8 py-3 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm shadow-primary-600/20"
                                id="submitBtn" disabled>
                            <i class="fas fa-gavel mr-2"></i>
                            Finaliser la vérification
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    {{-- Modal pour les images --}}
    <div id="imageModal" class="hidden fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4">
        <div class="max-w-4xl w-full relative">
            <img id="modalImage" src="" class="w-full h-auto rounded-xl" alt="Image détaillée">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-slate-300 transition w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('confidenceSlider');
    const display = document.getElementById('confidenceDisplay');
    const submitBtn = document.getElementById('submitBtn');
    const decisionInput = document.getElementById('decisionInput');
    const decisionButtons = document.querySelectorAll('.decision-button');
    const expertForm = document.getElementById('expertForm');

    if (slider) {
        slider.addEventListener('input', function() {
            display.textContent = this.value + '%';
        });
    }

    decisionButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            decisionButtons.forEach(b => {
                b.classList.remove('bg-emerald-50', 'bg-red-50', 'border-emerald-500', 'border-red-500');
                b.classList.add('border-emerald-300', 'dark:border-emerald-700');
            });

            const decision = this.dataset.decision;
            decisionInput.value = decision;
            submitBtn.disabled = false;

            if (decision === 'approve') {
                this.classList.add('selected', 'bg-emerald-50', 'border-emerald-500');
                submitBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                submitBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
            } else {
                this.classList.add('selected', 'bg-red-50', 'border-red-500');
                submitBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            }
        });
    });

    document.querySelectorAll('[data-image]').forEach(el => {
        el.addEventListener('click', function() {
            document.getElementById('modalImage').src = this.dataset.image;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        });
    });

    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeImageModal();
        });
    }

    if (expertForm) {
        expertForm.addEventListener('submit', function(e) {
            if (!decisionInput.value) {
                e.preventDefault();
                alert('Veuillez sélectionner une décision (Approuver ou Rejeter)');
                return false;
            }
            if (document.getElementById('expert_notes').value.trim().length < 10) {
                e.preventDefault();
                alert('Veuillez fournir une justification détaillée d\'au moins 10 caractères');
                document.getElementById('expert_notes').focus();
                return false;
            }
            return confirm('Êtes-vous sûr de votre décision ? Cette action est irréversible.');
        });
    }
});

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}
</script>
@endpush
