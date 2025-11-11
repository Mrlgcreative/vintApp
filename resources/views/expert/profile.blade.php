@extends('layouts.admin')

@section('title', 'Profil Expert')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <!-- En-tête -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 mb-8 mx-4 lg:mx-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center">
            <div class="text-white">
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 flex items-center">
                    <i class="fas fa-user-shield mr-3"></i>
                    Profil Expert
                </h1>
                <p class="text-indigo-100 text-lg">
                    Gérez vos informations et consultez vos statistiques
                </p>
            </div>
            <div class="mt-4 lg:mt-0">
                <a href="{{ route('expert.dashboard') }}" 
                   class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-6 py-2 rounded-lg transition-all duration-300 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mx-4 lg:mx-8">
        <!-- Profil principal -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-8 text-center border-b border-gray-200">
                    <div class="w-32 h-32 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl text-white shadow-lg">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $expert->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $expert->email }}</p>
                    
                    @php
                        $levelClasses = [
                            'bronze' => 'bg-orange-100 text-orange-800',
                            'silver' => 'bg-gray-100 text-gray-800',
                            'gold' => 'bg-yellow-100 text-yellow-800'
                        ];
                        $level = $expertProfile->level ?? 'bronze';
                    @endphp
                    <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold uppercase tracking-wide mb-4 {{ $levelClasses[$level] ?? $levelClasses['bronze'] }}">
                        Niveau {{ ucfirst($level) }}
                    </span>

                    <div class="bg-gradient-to-r from-green-500 to-teal-500 text-white px-6 py-3 rounded-2xl inline-block font-semibold mb-4">
                        <i class="fas fa-percentage mr-2"></i>
                        Commission: {{ $expertProfile->commission_rate }}%
                    </div>

                    @if($expertProfile->specialties ?? [])
                        <div class="flex flex-wrap justify-center gap-2">
                            @foreach($expertProfile->specialties as $specialty)
                                <span class="bg-gray-100 border border-gray-300 px-3 py-1 rounded-full text-sm text-gray-700">
                                    {{ ucfirst($specialty) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="p-8 space-y-6">
                    <div class="bg-gray-50 border-l-4 border-indigo-500 p-4 rounded-lg">
                        <div class="flex items-center text-indigo-600 font-semibold mb-2">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Expert depuis
                        </div>
                        <p class="text-gray-900 font-medium">{{ $expertProfile->created_at->format('d/m/Y') }}</p>
                        <p class="text-gray-500 text-sm">{{ $expertProfile->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="bg-gray-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                        <div class="flex items-center text-yellow-600 font-semibold mb-2">
                            <i class="fas fa-clock mr-2"></i>
                            Temps moyen d'examen
                        </div>
                        <p class="text-gray-900 font-medium">{{ $detailedStats['avg_review_time'] }} minutes</p>
                    </div>

                    @if($expertProfile->bio)
                        <div class="bg-gray-50 border-l-4 border-blue-500 p-4 rounded-lg">
                            <div class="flex items-center text-blue-600 font-semibold mb-2">
                                <i class="fas fa-info-circle mr-2"></i>
                                Biographie
                            </div>
                            <p class="text-gray-700">{{ $expertProfile->bio }}</p>
                        </div>
                    @endif

                    @if($expertProfile->credentials)
                        <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-lg">
                            <div class="flex items-center text-green-600 font-semibold mb-2">
                                <i class="fas fa-certificate mr-2"></i>
                                Certifications
                            </div>
                            <p class="text-gray-700">{{ $expertProfile->credentials }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistiques et performances -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Statistiques détaillées -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h4 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-indigo-600"></i>
                    Statistiques de performance
                </h4>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gray-50 border-l-4 border-indigo-500 p-6 rounded-lg text-center">
                        <div class="text-3xl font-bold text-indigo-600 mb-2">{{ $detailedStats['total_assigned'] }}</div>
                        <div class="text-gray-600 font-medium uppercase text-sm tracking-wide">Total assigné</div>
                    </div>

                    <div class="bg-gray-50 border-l-4 border-yellow-500 p-6 rounded-lg text-center">
                        <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $detailedStats['pending'] }}</div>
                        <div class="text-gray-600 font-medium uppercase text-sm tracking-wide">En attente</div>
                    </div>

                    <div class="bg-gray-50 border-l-4 border-green-500 p-6 rounded-lg text-center">
                        <div class="text-3xl font-bold text-green-600 mb-2">{{ $detailedStats['approved'] }}</div>
                        <div class="text-gray-600 font-medium uppercase text-sm tracking-wide">Approuvées</div>
                    </div>

                    <div class="bg-gray-50 border-l-4 border-red-500 p-6 rounded-lg text-center">
                        <div class="text-3xl font-bold text-red-600 mb-2">{{ $detailedStats['rejected'] }}</div>
                        <div class="text-gray-600 font-medium uppercase text-sm tracking-wide">Rejetées</div>
                    </div>
                </div>

                <!-- Taux d'approbation -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="flex items-center text-green-600 font-semibold mb-4">
                            <i class="fas fa-thumbs-up mr-2"></i>
                            Taux d'approbation
                        </div>
                        <div class="flex items-center">
                            <div class="flex-grow bg-gray-200 rounded-full h-3 mr-4">
                                <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" 
                                     style="width: {{ $expertProfile->approval_rate ?? 0 }}%"></div>
                            </div>
                            <span class="text-xl font-bold text-gray-900">{{ number_format($expertProfile->approval_rate ?? 0, 1) }}%</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="flex items-center text-yellow-600 font-semibold mb-4">
                            <i class="fas fa-star mr-2"></i>
                            Score de qualité
                        </div>
                        <div class="flex items-center">
                            @php
                                $qualityScore = min(100, ($expertProfile->approval_rate ?? 0) * 0.7 + 
                                              (100 - $detailedStats['avg_review_time']) * 0.3);
                            @endphp
                            <div class="flex-grow bg-gray-200 rounded-full h-3 mr-4">
                                <div class="bg-yellow-500 h-3 rounded-full transition-all duration-1000" 
                                     style="width: {{ $qualityScore }}%"></div>
                            </div>
                            <span class="text-xl font-bold text-gray-900">{{ number_format($qualityScore, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique de performance -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h5 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-chart-line mr-3 text-blue-600"></i>
                    Évolution des performances
                </h5>
                <div class="h-48 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600 font-medium">Graphique de performance</p>
                        <p class="text-sm text-gray-400">Fonctionnalité à venir</p>
                    </div>
                </div>
            </div>

            <!-- Formulaire de modification -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h4 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-edit mr-3 text-gray-600"></i>
                    Modifier le profil
                </h4>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('expert.profile.update') }}" method="POST" 
                      class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label for="bio" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-user-edit mr-2"></i>
                                Biographie
                            </label>
                            <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                      name="bio" id="bio" rows="4"
                                      placeholder="Décrivez votre expérience et expertise...">{{ old('bio', $expertProfile->bio) }}</textarea>
                            <p class="text-sm text-gray-500 mt-2">
                                Partagez votre expérience dans l'authentification de produits de luxe
                            </p>
                            @error('bio')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="credentials" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-certificate mr-2"></i>
                                Certifications et qualifications
                            </label>
                            <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                      name="credentials" id="credentials" rows="3"
                                      placeholder="Listez vos certifications, formations et qualifications...">{{ old('credentials', $expertProfile->credentials) }}</textarea>
                            <p class="text-sm text-gray-500 mt-2">
                                Exemple: Certificat GIA, Formation horlogerie suisse, etc.
                            </p>
                            @error('credentials')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-center mt-8">
                        <button type="submit" 
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i>
                            Mettre à jour le profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Animation des barres de progression
    $('.bg-green-500, .bg-yellow-500').each(function() {
        const $this = $(this);
        const width = $this.attr('style').match(/width:\s*(\d+\.?\d*)%/);
        if (width) {
            $this.css('width', '0%').animate({
                width: width[1] + '%'
            }, 1000);
        }
    });

    // Animation des stats au chargement
    $('.text-3xl').each(function() {
        const $this = $(this);
        const finalNumber = parseInt($this.text());
        if (!isNaN(finalNumber)) {
            $this.text('0');
            
            $({ counter: 0 }).animate({ counter: finalNumber }, {
                duration: 1500,
                step: function() {
                    $this.text(Math.ceil(this.counter));
                }
            });
        }
    });
});
</script>
@endpush