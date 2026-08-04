@extends('layouts.admin')

@section('title', 'Profil Expert')
@section('page-title', 'Profil Expert')
@section('page-subtitle', 'Gérez vos informations et consultez vos statistiques')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profil principal --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="p-8 text-center border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/60">
                    <div class="w-24 h-24 rounded-full bg-primary-600 flex items-center justify-center mx-auto mb-5 text-3xl text-white shadow-lg shadow-primary-600/30 ring-4 ring-primary-100 dark:ring-primary-900/40">
                        {{ strtoupper(substr($expert->name, 0, 1)) }}
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1">{{ $expert->name }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ $expert->email }}</p>

                    @php
                        $levelClasses = [
                            'junior' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                            'senior' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'master' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
                        ];
                        $level = $expertProfile->certification_level ?? 'junior';
                    @endphp
                    <span class="inline-block px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide mb-4 {{ $levelClasses[$level] ?? $levelClasses['junior'] }}">
                        Niveau {{ ucfirst($level) }}
                    </span>

                    <div class="inline-flex items-center gap-2 bg-primary-600 text-white px-5 py-2 rounded-xl text-sm font-semibold mb-4 shadow-sm shadow-primary-600/25">
                        <i class="fas fa-percentage"></i>
                        Commission: {{ $expertProfile->commission_rate }}%
                    </div>

                    @if($expertProfile->specialties ?? [])
                        <div class="flex flex-wrap justify-center gap-2">
                            @foreach($expertProfile->specialties as $specialty)
                                <span class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 px-3 py-1 rounded-full text-xs text-slate-700 dark:text-slate-200">
                                    {{ ucfirst($specialty) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="p-6 space-y-3">
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4">
                        <div class="flex items-center text-primary-600 dark:text-primary-400 font-semibold text-sm mb-1.5">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Expert depuis
                        </div>
                        <p class="text-slate-900 dark:text-white font-medium">{{ $expertProfile->created_at->format('d/m/Y') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $expertProfile->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4">
                        <div class="flex items-center text-amber-600 dark:text-amber-400 font-semibold text-sm mb-1.5">
                            <i class="fas fa-clock mr-2"></i>
                            Temps moyen d'examen
                        </div>
                        <p class="text-slate-900 dark:text-white font-medium">{{ $detailedStats['avg_review_time'] }} minutes</p>
                    </div>

                    @if($expertProfile->bio)
                        <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4">
                            <div class="flex items-center text-blue-600 dark:text-blue-400 font-semibold text-sm mb-1.5">
                                <i class="fas fa-info-circle mr-2"></i>
                                Biographie
                            </div>
                            <p class="text-sm text-slate-700 dark:text-slate-200">{{ $expertProfile->bio }}</p>
                        </div>
                    @endif

                    @if($expertProfile->credentials)
                        <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4">
                            <div class="flex items-center text-emerald-600 dark:text-emerald-400 font-semibold text-sm mb-1.5">
                                <i class="fas fa-certificate mr-2"></i>
                                Certifications
                            </div>
                            <p class="text-sm text-slate-700 dark:text-slate-200">{{ $expertProfile->credentials }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Statistiques et performances --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-5 flex items-center">
                    <i class="fas fa-chart-bar mr-2.5 text-primary-500"></i>
                    Statistiques de performance
                </h4>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-primary-600 dark:text-primary-400 mb-1" data-count="{{ $detailedStats['total_assigned'] }}">{{ $detailedStats['total_assigned'] }}</div>
                        <div class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Total assigné</div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mb-1" data-count="{{ $detailedStats['pending'] }}">{{ $detailedStats['pending'] }}</div>
                        <div class="text-[11px] font-medium uppercase tracking-wide text-slate-500">En attente</div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mb-1" data-count="{{ $detailedStats['approved'] }}">{{ $detailedStats['approved'] }}</div>
                        <div class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Approuvées</div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400 mb-1" data-count="{{ $detailedStats['rejected'] }}">{{ $detailedStats['rejected'] }}</div>
                        <div class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Rejetées</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-5">
                        <div class="flex items-center text-emerald-600 dark:text-emerald-400 font-semibold text-sm mb-4">
                            <i class="fas fa-thumbs-up mr-2"></i>
                            Taux d'approbation
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex-grow bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-1000" style="width: 0%" data-width="{{ $expertProfile->approval_rate ?? 0 }}%"></div>
                            </div>
                            <span class="text-lg font-bold text-slate-900 dark:text-white">{{ number_format($expertProfile->approval_rate ?? 0, 1) }}%</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-5">
                        <div class="flex items-center text-amber-600 dark:text-amber-400 font-semibold text-sm mb-4">
                            <i class="fas fa-star mr-2"></i>
                            Score de qualité
                        </div>
                        <div class="flex items-center gap-4">
                            @php
                                $qualityScore = min(100, ($expertProfile->approval_rate ?? 0) * 0.7 +
                                              (100 - $detailedStats['avg_review_time']) * 0.3);
                            @endphp
                            <div class="flex-grow bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-amber-500 h-2.5 rounded-full transition-all duration-1000" style="width: 0%" data-width="{{ $qualityScore }}%"></div>
                            </div>
                            <span class="text-lg font-bold text-slate-900 dark:text-white">{{ number_format($qualityScore, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Graphique de performance --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <h5 class="text-sm font-bold text-slate-900 dark:text-white mb-5 flex items-center">
                    <i class="fas fa-chart-line mr-2.5 text-blue-500"></i>
                    Évolution des performances
                </h5>
                <div class="h-48 bg-slate-50 dark:bg-slate-900 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                        <p class="text-slate-600 dark:text-slate-300 font-medium">Graphique de performance</p>
                        <p class="text-sm text-slate-400">Fonctionnalité à venir</p>
                    </div>
                </div>
            </div>

            {{-- Formulaire de modification --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-5 flex items-center">
                    <i class="fas fa-edit mr-2.5 text-slate-400"></i>
                    Modifier le profil
                </h4>

                @if(session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-xl mb-5 flex items-center text-sm">
                        <i class="fas fa-check-circle mr-3"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('expert.profile.update') }}" method="POST"
                      class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label for="bio" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2 flex items-center">
                                <i class="fas fa-user-edit mr-2"></i>
                                Biographie
                            </label>
                            <textarea class="w-full px-4 py-3 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 focus:outline-none transition-colors"
                                      name="bio" id="bio" rows="4"
                                      placeholder="Décrivez votre expérience et expertise...">{{ old('bio', $expertProfile->bio) }}</textarea>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                Partagez votre expérience dans l'authentification de produits de luxe
                            </p>
                            @error('bio')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="credentials" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2 flex items-center">
                                <i class="fas fa-certificate mr-2"></i>
                                Certifications et qualifications
                            </label>
                            <textarea class="w-full px-4 py-3 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 focus:outline-none transition-colors"
                                      name="credentials" id="credentials" rows="3"
                                      placeholder="Listez vos certifications, formations et qualifications...">{{ old('credentials', $expertProfile->credentials) }}</textarea>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                Exemple: Certificat GIA, Formation horlogerie suisse, etc.
                            </p>
                            @error('credentials')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-center mt-8">
                        <button type="submit"
                                class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-3 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-primary-600/25 hover:shadow-xl hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i>
                            Mettre à jour le profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-width]').forEach(function(bar) {
        const target = bar.getAttribute('data-width');
        setTimeout(function() { bar.style.width = target; }, 100);
    });

    document.querySelectorAll('[data-count]').forEach(function(el) {
        const target = parseInt(el.getAttribute('data-count'), 10);
        if (isNaN(target)) return;
        const duration = 1200;
        const start = performance.now();
        function tick(now) {
            const progress = Math.min((now - start) / duration, 1);
            el.textContent = Math.ceil(progress * target);
            if (progress < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    });
});
</script>
@endpush
