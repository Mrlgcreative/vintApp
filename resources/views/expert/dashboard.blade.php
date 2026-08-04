@extends('layouts.admin')

@section('title', 'Dashboard Expert')
@section('page-title', 'Dashboard Expert')
@section('page-subtitle', 'Bienvenue {{ Auth::user()->name }} — Gérez vos vérifications d\'authenticité')

@section('content')

    {{-- ====== Cartes statistiques ====== --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-5 mb-6">
        <div class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-orange-600">En attente</span>
                <div class="w-9 h-9 bg-orange-50 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-orange-500"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['pending_assignments'] }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Vérifications assignées</p>
        </div>

        <div class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-600">Complétées aujourd'hui</span>
                <div class="w-9 h-9 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['completed_today'] }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Ce jour</p>
        </div>

        <div class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-600">Total vérifiées</span>
                <div class="w-9 h-9 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt text-blue-500"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['total_verified'] }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Depuis le début</p>
        </div>

        <div class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-primary-600">Taux d'approbation</span>
                <div class="w-9 h-9 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percentage text-primary-500"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['approval_rate'], 1) }}%</div>
            <p class="text-[11px] text-slate-400 mt-1">Fiabilité</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Vérifications en attente --}}
        <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                    <i class="fas fa-tasks mr-2 text-primary-500"></i>
                    Vérifications en attente
                </h2>
                <a href="{{ route('expert.verifications.index') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 transition-colors">
                    Voir tout
                </a>
            </div>

            <div class="p-5">
                @forelse($pendingChecks as $check)
                    <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4 mb-3 last:mb-0 transition-all duration-200 hover:shadow-sm hover:border-primary-300 dark:hover:border-primary-700">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    @if(!empty($check->item->images) && isset($check->item->images[0]))
                                        <img src="{{ asset('storage/' . $check->item->images[0]) }}"
                                             class="w-11 h-11 object-cover rounded-lg ring-1 ring-slate-200 dark:ring-slate-700" alt="Produit">
                                    @else
                                        <div class="w-11 h-11 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-slate-400"></i>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-slate-900 dark:text-white truncate">{{ $check->item->name ?? $check->item->title ?? 'Produit sans nom' }}</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $check->item->category->name ?? 'Sans catégorie' }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                                    <span><i class="fas fa-user mr-1.5 text-slate-400"></i>{{ $check->vendor->name ?? 'Vendeur inconnu' }}</span>
                                    <span><i class="fas fa-clock mr-1.5 text-slate-400"></i>{{ $check->expert_assigned_at ? $check->expert_assigned_at->diffForHumans() : 'Date inconnue' }}</span>
                                    @if($check->item->price)
                                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                                            {{ number_format($check->item->price, 0, ',', ' ') }} {{ $check->item->currency }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                @php
                                    $hoursWaiting = $check->expert_assigned_at ? $check->expert_assigned_at->diffInHours(now()) : 0;
                                    $urgencyClass = $hoursWaiting > 48 ? 'bg-red-500' : ($hoursWaiting > 24 ? 'bg-amber-500' : 'bg-emerald-500');
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-white {{ $urgencyClass }}">
                                    @if($hoursWaiting > 48)
                                        Urgent
                                    @elseif($hoursWaiting > 24)
                                        Priorité
                                    @else
                                        Normal
                                    @endif
                                </span>

                                <a href="{{ route('expert.verifications.show', $check) }}"
                                   class="inline-flex items-center px-3.5 py-2 rounded-lg text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 transition-colors shadow-sm shadow-primary-600/20">
                                    <i class="fas fa-eye mr-1.5"></i>
                                    Examiner
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                        </div>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Aucune vérification en attente</p>
                        <p class="text-xs text-slate-400 mt-1">Vous êtes à jour !</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Profil expert --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-14 h-14 rounded-full bg-primary-600 flex items-center justify-center text-white text-lg font-bold shadow-lg shadow-primary-600/25">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $expertProfile ? ucfirst($expertProfile->certification_level) : 'Junior' }} Expert</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex flex-wrap gap-1.5">
                        @if($expertProfile && $expertProfile->specialties)
                            @foreach($expertProfile->specialties as $specialty)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-medium bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
                                    {{ ucfirst($specialty) }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-xs text-slate-500 dark:text-slate-400">Aucune spécialisation définie</span>
                        @endif
                    </div>

                    <a href="{{ route('expert.profile') }}"
                       class="block w-full text-center py-2.5 px-4 rounded-xl border border-primary-600 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 text-sm font-semibold transition-colors">
                        Voir le profil
                    </a>
                </div>
            </div>

            {{-- Activités récentes --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        <i class="fas fa-history mr-2 text-slate-400"></i>
                        Activités récentes
                    </h3>
                </div>
                <div class="p-5">
                    @forelse($recentChecks->take(5) as $recent)
                        <div class="flex items-center justify-between py-2.5 @if(!$loop->last) border-b border-slate-100 dark:border-slate-700 @endif">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                    {{ $recent->item->name ?? $recent->item->title ?? 'Produit sans nom' }}
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $recent->expert_completed_at ? $recent->expert_completed_at->diffForHumans() : 'Date inconnue' }}
                                </p>
                            </div>
                            <div class="ml-3 flex-shrink-0">
                                @if($recent->status === 'expert_approved')
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @else
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-50 dark:bg-red-900/20 text-red-500">
                                        <i class="fas fa-times"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">
                            Aucune activité récente
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">
                    <i class="fas fa-bolt mr-2 text-amber-500"></i>
                    Actions rapides
                </h3>
                <div class="space-y-2.5">
                    <a href="{{ route('expert.verifications.index') }}"
                       class="block w-full text-center py-2.5 px-4 rounded-xl bg-primary-600 text-white hover:bg-primary-700 text-sm font-semibold transition-colors shadow-sm shadow-primary-600/20">
                        <i class="fas fa-list mr-2"></i>
                        Toutes les vérifications
                    </a>
                    <a href="{{ route('expert.verifications.index', ['status' => 'expert_review']) }}"
                       class="block w-full text-center py-2.5 px-4 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        En attente d'examen
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
