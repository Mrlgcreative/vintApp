@extends('layouts.admin')

@section('title', 'Vérification #' . $check->id)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <!-- En-tête -->
    <div class="bg-gradient-to-r from-indigo-500 to-primary-600 rounded-xl p-8 text-white mb-8 shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-shield-alt mr-3"></i>
                    Vérification #{{ $check->id }}
                </h1>
                <p class="text-indigo-100">
                    Examen expert - {{ $check->item->name ?? 'Produit sans nom' }}
                </p>
            </div>
            <div>
                <a href="{{ route('expert.verifications.index') }}" 
                   class="px-4 py-2 bg-white dark:bg-gray-800 bg-opacity-20 text-white rounded-lg hover:bg-opacity-30 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <!-- Statut si terminé -->
    @if(in_array($check->status, ['expert_approved', 'expert_rejected']))
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8 text-center">
            <h4 class="text-lg font-semibold text-green-800 mb-2">
                <i class="fas fa-{{ $check->status === 'expert_approved' ? 'check-circle' : 'times-circle' }} mr-2"></i>
                Vérification {{ $check->status === 'expert_approved' ? 'Approuvée' : 'Rejetée' }}
            </h4>
            <p class="text-green-600">
                Cette vérification a été finalisée le {{ $check->expert_completed_at->format('d/m/Y à H:i') }}
            </p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <!-- Informations sur le produit -->
        <div class="p-8 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                <i class="fas fa-box-open mr-2 text-indigo-600"></i>
                Informations sur le produit
            </h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <!-- Galerie d'images du produit -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                        @forelse($check->item->images ?? [] as $image)
                            <div class="aspect-square rounded-lg overflow-hidden shadow-md cursor-pointer hover:shadow-lg transition-shadow" 
                                 data-bs-toggle="modal" data-bs-target="#imageModal" 
                                 data-image="{{ asset('storage/' . $image) }}">
                                <img src="{{ asset('storage/' . $image) }}" 
                                     alt="Image produit" class="w-full h-full object-cover">
                            </div>
                        @empty
                            <div class="aspect-square bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Titre</div>
                            <div class="text-lg text-gray-900 dark:text-white">{{ $check->item->name ?? 'Produit sans nom' }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Prix</div>
                                <div class="text-lg text-gray-900 dark:text-white">{{ number_format($check->item->price, 0, ',', ' ') }} {{ $check->item->currency }}</div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Catégorie</div>
                                <div class="text-lg text-gray-900 dark:text-white">{{ $check->item->category->name ?? 'Non spécifiée' }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Marque</div>
                                <div class="text-lg text-gray-900 dark:text-white">{{ $check->item->brand->name ?? 'Non spécifiée' }}</div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">État</div>
                                <div class="text-lg text-gray-900 dark:text-white">{{ ucfirst($check->item->condition ?? 'Non spécifié') }}</div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Vendeur</div>
                            <div class="text-lg text-gray-900 dark:text-white">
                                {{ $check->vendor->name }}
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $check->vendor->email }}</div>
                            </div>
                        </div>
                    </div>

                    @if($check->item->description)
                        <div class="mt-4">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Description</div>
                            <div class="text-gray-900 dark:text-white">{{ $check->item->description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Images de vérification soumises -->
        @if($check->verificationImages->count() > 0)
            <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    <i class="fas fa-camera mr-2 text-blue-600"></i>
                    Images de vérification soumises ({{ $check->verificationImages->count() }})
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($check->verificationImages as $image)
                        <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700 cursor-pointer hover:border-indigo-300 transition-colors" 
                             data-bs-toggle="modal" data-bs-target="#imageModal"
                             data-image="{{ asset('storage/' . $image->image_path) }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="Image de vérification" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Analyse IA -->
        @if($check->ai_analysis_result)
            <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    <i class="fas fa-robot mr-2 text-yellow-600"></i>
                    Analyse Intelligence Artificielle
                </h3>

                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6">
                    @php
                        $aiData = is_string($check->ai_analysis_result) 
                            ? json_decode($check->ai_analysis_result, true) 
                            : $check->ai_analysis_result;
                        $score = $aiData['authenticity_score'] ?? 50;
                        $scoreClass = $score >= 70 ? 'from-green-400 to-green-600' : ($score >= 40 ? 'from-yellow-400 to-orange-500' : 'from-red-400 to-red-600');
                    @endphp

                    <div class="flex items-center mb-4">
                        <div class="w-20 h-20 bg-gradient-to-br {{ $scoreClass }} rounded-full flex items-center justify-center text-white text-xl font-bold mr-6">
                            {{ $score }}%
                        </div>
                        <div>
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white">Score d'authenticité IA</h5>
                            <p class="text-gray-600 dark:text-gray-300">
                                @if($score >= 70)
                                    <span class="text-green-600">Très probable que le produit soit authentique</span>
                                @elseif($score >= 40)
                                    <span class="text-yellow-600">Authenticité incertaine - Examen expert requis</span>
                                @else
                                    <span class="text-red-600">Suspicion de contrefaçon</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if(isset($aiData['details']))
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                            <h6 class="font-semibold text-gray-900 dark:text-white mb-2">Détails de l'analyse :</h6>
                            <ul class="space-y-1">
                                @foreach($aiData['details'] as $detail)
                                    <li class="text-gray-700 dark:text-gray-200">• {{ $detail }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Historique -->
        <div class="p-8 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                <i class="fas fa-history mr-2 text-gray-600 dark:text-gray-300"></i>
                Historique de la vérification
            </h3>

            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-300"></div>
                
                @foreach($check->auditLogs->sortBy('created_at') as $log)
                    <div class="relative mb-6 last:mb-0">
                        <div class="absolute left-2 w-3 h-3 bg-indigo-600 rounded-full border-2 border-white shadow"></div>
                        <div class="ml-10 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex justify-content-between items-start mb-2">
                                <strong class="text-gray-900 dark:text-white">{{ $log->getActionLabel() }}</strong>
                                <small class="text-gray-500 dark:text-gray-400">{{ $log->created_at->format('d/m/Y à H:i') }}</small>
                            </div>
                            @if($log->performer)
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">Par: {{ $log->performer->name }}</p>
                            @endif
                            @if($log->details)
                                @php
                                    $details = is_string($log->details) ? json_decode($log->details, true) : $log->details;
                                @endphp
                                @if(is_array($details))
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
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

        <!-- Notes expert existantes -->
        @if($check->expert_notes)
            <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    <i class="fas fa-comment mr-2 text-indigo-600"></i>
                    Notes de l'expert
                </h3>
                <div class="bg-gray-50 dark:bg-gray-900 border-l-4 border-indigo-500 p-4 rounded-lg">
                    <p class="text-gray-700 dark:text-gray-200">{{ $check->expert_notes }}</p>
                </div>
            </div>
        @endif

        <!-- Formulaire de décision expert -->
        @if($check->status === 'expert_review')
            <div class="p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    <i class="fas fa-gavel mr-2 text-green-600"></i>
                    Finaliser la vérification
                </h3>

                <form action="{{ route('expert.verifications.finalize', $check) }}" method="POST" 
                      class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6" id="expertForm">
                    @csrf
                    
                    <!-- Boutons de décision -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="border-2 border-green-300 rounded-lg p-4 cursor-pointer hover:bg-green-50 transition-colors decision-button approve" 
                             data-decision="approve">
                            <div class="text-center">
                                <i class="fas fa-check-circle text-green-600 text-3xl mb-2"></i>
                                <h5 class="font-semibold text-gray-900 dark:text-white">Approuver</h5>
                                <p class="text-gray-600 dark:text-gray-300">Le produit est authentique</p>
                            </div>
                        </div>
                        
                        <div class="border-2 border-red-300 rounded-lg p-4 cursor-pointer hover:bg-red-50 transition-colors decision-button reject" 
                             data-decision="reject">
                            <div class="text-center">
                                <i class="fas fa-times-circle text-red-600 text-3xl mb-2"></i>
                                <h5 class="font-semibold text-gray-900 dark:text-white">Rejeter</h5>
                                <p class="text-gray-600 dark:text-gray-300">Le produit n'est pas authentique</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="decision" id="decisionInput" required>

                    <!-- Notes de l'expert -->
                    <div class="mb-6">
                        <label for="expert_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            <i class="fas fa-pen mr-1"></i>
                            Notes et justification (obligatoire)
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                  name="expert_notes" id="expert_notes" rows="4" 
                                  placeholder="Détaillez votre analyse et les raisons de votre décision..." 
                                  required>{{ old('expert_notes') }}</textarea>
                        @error('expert_notes')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Niveau de confiance -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            <i class="fas fa-percent mr-1"></i>
                            Niveau de confiance
                        </label>
                        <div class="text-center text-2xl font-bold text-indigo-600 mb-2" id="confidenceDisplay">85%</div>
                        <input type="range" class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer" 
                               name="confidence_level" id="confidenceSlider" min="1" max="100" value="85" required>
                        <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <span>Très incertain</span>
                            <span>Totalement certain</span>
                        </div>
                        @error('confidence_level')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="text-center">
                        <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                                id="submitBtn" disabled>
                            <i class="fas fa-gavel mr-2"></i>
                            Finaliser la vérification
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<!-- Modal pour les images -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image détaillée</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Image détaillée">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Gestion du slider de confiance
    $('#confidenceSlider').on('input', function() {
        $('#confidenceDisplay').text($(this).val() + '%');
    });

    // Gestion des boutons de décision
    $('.decision-button').on('click', function() {
        $('.decision-button').removeClass('selected bg-green-100 bg-red-100').addClass('border-gray-300 dark:border-gray-600');
        
        const decision = $(this).data('decision');
        $('#decisionInput').val(decision);
        $('#submitBtn').prop('disabled', false);

        if (decision === 'approve') {
            $(this).addClass('selected bg-green-100 border-green-500');
            $('#submitBtn').removeClass('bg-indigo-600 hover:bg-indigo-700 bg-red-600 hover:bg-red-700').addClass('bg-green-600 hover:bg-green-700');
        } else {
            $(this).addClass('selected bg-red-100 border-red-500');
            $('#submitBtn').removeClass('bg-indigo-600 hover:bg-indigo-700 bg-green-600 hover:bg-green-700').addClass('bg-red-600 hover:bg-red-700');
        }
    });

    // Modal d'image
    $('[data-bs-toggle="modal"]').on('click', function() {
        const imageSrc = $(this).data('image');
        $('#modalImage').attr('src', imageSrc);
    });

    // Validation du formulaire
    $('#expertForm').on('submit', function(e) {
        if (!$('#decisionInput').val()) {
            e.preventDefault();
            alert('Veuillez sélectionner une décision (Approuver ou Rejeter)');
            return false;
        }
        
        if ($('#expert_notes').val().trim().length < 10) {
            e.preventDefault();
            alert('Veuillez fournir une justification détaillée d\'au moins 10 caractères');
            $('#expert_notes').focus();
            return false;
        }
        
        return confirm('Êtes-vous sûr de votre décision ? Cette action est irréversible.');
    });
});
</script>
@endpush