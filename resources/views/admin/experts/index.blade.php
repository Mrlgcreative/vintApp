@extends('layouts.admin')

@section('title', 'Gestion des Experts')
@section('page-title', 'Gestion des Experts')
@section('page-subtitle', "Gérer les experts en vérification d'authenticité")

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.experts.candidates') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-user-plus"></i>
        <span class="hidden sm:inline">Désigner un Expert</span>
        <span class="sm:hidden">Désigner</span>
    </a>
    <button onclick="toggleStats()"
            class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700">
        <i class="fas fa-chart-bar"></i>
        <span class="hidden sm:inline">Statistiques</span>
    </button>
</div>
@endsection

@push('styles')
<style>
    @keyframes pulse-dot { 0%,100%{ box-shadow:0 0 0 0 rgba(16,185,129,.55) } 50%{ box-shadow:0 0 0 6px rgba(16,185,129,0) } }
    .pulse-dot{ animation:pulse-dot 2s infinite }
</style>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
        {{-- Total Experts --}}
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total Experts</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['total_experts']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-sky-200 bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-400">
                    <i class="fas fa-user-graduate text-[10px]"></i>
                    Experts
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-users text-xs text-sky-500"></i>
                    Équipe de vérification
                </div>
                <div class="text-xs text-slate-400">Experts désignés</div>
            </div>
        </div>

        {{-- Experts Actifs --}}
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Experts Actifs</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['active_experts']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 pulse-dot"></span>
                    Actifs
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-user-check text-xs text-emerald-500"></i>
                    Disponibles
                </div>
                <div class="text-xs text-slate-400">Experts opérationnels</div>
            </div>
        </div>

        {{-- Vérifications Totales --}}
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Vérifications</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['total_verifications']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-violet-200 bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-400">
                    <i class="fas fa-certificate text-[10px]"></i>
                    Effectuées
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-certificate text-xs text-violet-500"></i>
                    Demandes traitées
                </div>
                <div class="text-xs text-slate-400">Sur toute la plateforme</div>
            </div>
        </div>

        {{-- En Attente --}}
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">En Attente</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['pending_verifications']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fas fa-clock text-[10px]"></i>
                    À traiter
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-clock text-xs text-amber-500"></i>
                    En cours de validation
                </div>
                <div class="text-xs text-slate-400">Vérifications en attente</div>
            </div>
        </div>
    </div>

    {{-- Filtres et recherche --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                    <i class="fas fa-filter text-sm"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Filtres de recherche</h3>
            </div>
            <button onclick="toggleFilters()"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                <i class="fas fa-chevron-down text-[10px] transition-transform" id="filterChevron"></i>
                Filtres
            </button>
        </div>
        <div id="filtersPanel" class="hidden border-t border-slate-100 p-4 sm:p-5 dark:border-slate-700">
            <form method="GET" action="{{ route('admin.experts.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Rechercher</label>
                        <input type="text" id="search" name="search"
                               value="{{ request('search') }}"
                               placeholder="Nom ou email..."
                               class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Statut</label>
                        <select id="status" name="status"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            <option value="">Tous</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                    </div>
                    <div>
                        <label for="specialization" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Spécialisation</label>
                        <select id="specialization" name="specialization"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            <option value="">Toutes</option>
                            <option value="luxury" {{ request('specialization') === 'luxury' ? 'selected' : '' }}>Luxe</option>
                            <option value="sneakers" {{ request('specialization') === 'sneakers' ? 'selected' : '' }}>Sneakers</option>
                            <option value="watches" {{ request('specialization') === 'watches' ? 'selected' : '' }}>Montres</option>
                            <option value="handbags" {{ request('specialization') === 'handbags' ? 'selected' : '' }}>Sacs</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Trier par</label>
                        <select id="sort" name="sort"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom</option>
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date d'ajout</option>
                            <option value="verifications_count" {{ request('sort') === 'verifications_count' ? 'selected' : '' }}>Nb vérifications</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.experts.index') }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Liste des experts --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="flex items-center gap-2 text-sm sm:text-base font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-list text-primary-600"></i>
                Liste des Experts ({{ $experts->total() }})
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Page {{ $experts->currentPage() }}/{{ $experts->lastPage() }}
            </span>
        </div>

        <div class="p-3 sm:p-5">
            @if($experts->count() > 0)
                {{-- Vue Table Desktop --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900">
                            <tr class="border-b border-slate-200 dark:border-slate-700">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Expert</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Spécialisations</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Niveau</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Vérifications</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Taux d'approbation</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($experts as $expert)
                                <tr class="border-t border-slate-100 dark:border-slate-700 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                    <td class="px-4 py-3 align-middle">
                                        <div class="flex items-center gap-3">
                                            @if($expert->user)
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    @if($expert->user->avatar)
                                                        <img src="{{ $expert->user->avatar_url }}"
                                                             class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600"
                                                             alt="{{ $expert->user->name }}"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="hidden h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400">
                                                            <span class="text-sm font-semibold text-white">{{ strtoupper(substr($expert->user->name, 0, 1)) }}</span>
                                                        </div>
                                                    @else
                                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400">
                                                            <span class="text-sm font-semibold text-white">{{ strtoupper(substr($expert->user->name, 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ $expert->user->name }}</div>
                                                    <div class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $expert->user->email }}</div>
                                                </div>
                                            @else
                                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-slate-400 dark:bg-slate-600">
                                                    <span class="text-sm font-bold text-white">?</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-slate-900 dark:text-white">Utilisateur supprimé</div>
                                                    <div class="text-xs text-slate-500 dark:text-slate-400">N/A</div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        @if($expert->specialties && count($expert->specialties) > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($expert->specialties as $specialty)
                                                    <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">
                                                        {{ ucfirst(str_replace('_', ' ', $specialty)) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">Aucune spécialisation</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset
                                            @if($expert->certification_level === 'master') bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300
                                            @elseif($expert->certification_level === 'senior') bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300
                                            @else bg-slate-100 text-slate-700 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300
                                            @endif">
                                            {{ ucfirst($expert->certification_level) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        <div class="font-semibold tabular-nums text-slate-900 dark:text-white">{{ $expert->verification_count }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">vérifications</div>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        @if($expert->approval_rate > 0)
                                            <div class="font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">{{ number_format($expert->approval_rate, 1) }}%</div>
                                            <div class="mt-1 h-1 w-full rounded-full bg-slate-200 dark:bg-slate-700">
                                                <div class="h-1 rounded-full bg-emerald-500" style="width: {{ $expert->approval_rate }}%"></div>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        <label class="inline-flex cursor-pointer items-center">
                                            <input type="checkbox"
                                                   class="peer sr-only"
                                                   data-expert-id="{{ $expert->id }}"
                                                   {{ $expert->is_active ? 'checked' : '' }}
                                                   onchange="toggleExpertStatus({{ $expert->id }})">
                                            <div class="relative h-6 w-11 rounded-full bg-slate-200 transition-colors after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all peer-checked:after:start-[22px] peer-checked:after:border-white peer-checked:bg-primary-600 peer-focus:ring-4 peer-focus:ring-primary-500/40 dark:bg-slate-700 dark:after:border-slate-600"></div>
                                            <span class="ml-2 text-xs text-slate-600 dark:text-slate-300">
                                                {{ $expert->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </label>
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('admin.experts.show', $expert) }}"
                                               class="rounded-lg p-2 text-sky-600 transition-colors hover:bg-sky-50 dark:text-sky-400 dark:hover:bg-sky-900/20"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.experts.edit', $expert) }}"
                                               class="rounded-lg p-2 text-emerald-600 transition-colors hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="rounded-lg p-2 text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                    title="Révoquer le statut d'expert"
                                                    onclick="revokeExpert({{ $expert->id }}, '{{ $expert->user?->name ?? 'Utilisateur supprimé' }}')">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Vue Cards Mobile --}}
                <div class="space-y-3 lg:hidden">
                    @foreach($experts as $expert)
                        <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800 space-y-3">
                            {{-- En-tête : Avatar + Nom + Statut --}}
                            <div class="flex items-center justify-between">
                                <div class="flex min-w-0 items-center gap-3">
                                    @if($expert->user)
                                        <div class="h-10 w-10 flex-shrink-0">
                                            @if($expert->user->avatar)
                                                <img src="{{ $expert->user->avatar_url }}"
                                                     class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600"
                                                     alt="{{ $expert->user->name }}"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="hidden h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400">
                                                    <span class="text-sm font-semibold text-white">{{ strtoupper(substr($expert->user->name, 0, 1)) }}</span>
                                                </div>
                                            @else
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400">
                                                    <span class="text-sm font-semibold text-white">{{ strtoupper(substr($expert->user->name, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $expert->user->name }}</div>
                                            <div class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $expert->user->email }}</div>
                                        </div>
                                    @else
                                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-slate-400 dark:bg-slate-600">
                                            <span class="text-sm font-bold text-white">?</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900 dark:text-white">Utilisateur supprimé</div>
                                        </div>
                                    @endif
                                </div>
                                <label class="inline-flex flex-shrink-0 cursor-pointer items-center">
                                    <input type="checkbox" class="peer sr-only" data-expert-id="{{ $expert->id }}"
                                           {{ $expert->is_active ? 'checked' : '' }}
                                           onchange="toggleExpertStatus({{ $expert->id }})">
                                    <div class="relative h-6 w-11 rounded-full bg-slate-200 transition-colors after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all peer-checked:after:start-[22px] peer-checked:after:border-white peer-checked:bg-primary-600 peer-focus:ring-4 peer-focus:ring-primary-500/40 dark:bg-slate-700 dark:after:border-slate-600"></div>
                                </label>
                            </div>

                            {{-- Infos : Niveau + Spécialisations --}}
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset
                                    @if($expert->certification_level === 'master') bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300
                                    @elseif($expert->certification_level === 'senior') bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300
                                    @else bg-slate-100 text-slate-700 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300
                                    @endif">
                                    {{ ucfirst($expert->certification_level) }}
                                </span>
                                @if($expert->specialties && count($expert->specialties) > 0)
                                    @foreach($expert->specialties as $specialty)
                                        <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">
                                            {{ ucfirst(str_replace('_', ' ', $specialty)) }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>

                            {{-- Stats + Actions --}}
                            <div class="flex items-center justify-between border-t border-slate-100 pt-2 dark:border-slate-700">
                                <div class="flex items-center gap-4 text-sm">
                                    <div class="text-center">
                                        <span class="font-semibold tabular-nums text-slate-900 dark:text-white">{{ $expert->verification_count }}</span>
                                        <span class="ml-0.5 text-xs text-slate-500 dark:text-slate-400">vérif.</span>
                                    </div>
                                    @if($expert->approval_rate > 0)
                                        <div class="flex items-center gap-1">
                                            <span class="font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">{{ number_format($expert->approval_rate, 0) }}%</span>
                                            <div class="h-1.5 w-12 rounded-full bg-slate-200 dark:bg-slate-700">
                                                <div class="h-1.5 rounded-full bg-emerald-500" style="width: {{ $expert->approval_rate }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.experts.show', $expert) }}"
                                       class="rounded-lg p-2 text-sky-600 transition-colors hover:bg-sky-50 dark:text-sky-400" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.experts.edit', $expert) }}"
                                       class="rounded-lg p-2 text-emerald-600 transition-colors hover:bg-emerald-50 dark:text-emerald-400" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="rounded-lg p-2 text-red-600 transition-colors hover:bg-red-50 dark:text-red-400"
                                            title="Révoquer"
                                            onclick="revokeExpert({{ $expert->id }}, '{{ $expert->user?->name ?? 'Utilisateur supprimé' }}')">
                                        <i class="fas fa-user-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-4 flex flex-col items-center justify-between gap-3 sm:mt-6 sm:flex-row sm:gap-4">
                    <div class="order-2 text-xs text-slate-600 sm:order-1 sm:text-sm dark:text-slate-300">
                        {{ $experts->firstItem() ?? 0 }}-{{ $experts->lastItem() ?? 0 }} sur {{ $experts->total() }}
                    </div>
                    <div class="order-1 w-full overflow-x-auto sm:order-2 sm:w-auto">
                        {{ $experts->links() }}
                    </div>
                </div>
            @else
                <div class="px-4 py-8 text-center sm:py-12">
                    <i class="fas fa-user-graduate mb-3 text-4xl text-slate-300 sm:mb-4 sm:text-6xl dark:text-slate-600"></i>
                    <h3 class="mb-2 text-base font-semibold text-slate-900 sm:text-lg dark:text-white">Aucun expert désigné</h3>
                    <p class="mb-4 text-sm text-slate-500 sm:mb-6 sm:text-base">Commencez par désigner des utilisateurs comme experts.</p>
                    <a href="{{ route('admin.experts.candidates') }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors sm:px-6 sm:py-3">
                        <i class="fas fa-user-plus"></i>
                        Désigner un Expert
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    const chevron = document.getElementById('filterChevron');
    panel.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
}

function toggleStats() {
    alert('Statistiques détaillées - À implémenter');
}

function toggleExpertStatus(expertId) {
    fetch(`/admin/experts/${expertId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors du changement de statut');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du changement de statut');
    });
}

function revokeExpert(expertId, expertName) {
    if (confirm(`Êtes-vous sûr de vouloir révoquer le statut d'expert de ${expertName} ?`)) {
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        fetch(`/admin/experts/${expertId}/revoke`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La réponse n\'est pas au format JSON');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.message) {
                    alert(data.message);
                }
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    location.reload();
                }
            } else {
                alert(data.message || 'Erreur lors de la révocation du statut');
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erreur détaillée:', error);
            alert(`Erreur lors de la révocation: ${error.message}`);
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
}
</script>
@endpush