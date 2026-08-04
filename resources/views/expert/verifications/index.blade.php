@extends('layouts.admin')

@section('title', 'Mes Vérifications - Expert')
@section('page-title', 'Mes Vérifications')
@section('page-subtitle', 'Gérez toutes vos demandes de vérification d\'authenticité')

@section('content')

    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-5 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="text-2xl font-bold text-slate-900 dark:text-white mb-1">{{ $verifications->total() }}</div>
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total</div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-orange-200 dark:border-orange-800/30 shadow-sm p-5">
            <div class="text-2xl font-bold text-orange-600 mb-1">{{ $verifications->where('status', 'expert_review')->count() }}</div>
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">En attente</div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-emerald-200 dark:border-emerald-800/30 shadow-sm p-5">
            <div class="text-2xl font-bold text-emerald-600 mb-1">{{ $verifications->where('status', 'expert_approved')->count() }}</div>
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Approuvées</div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-red-200 dark:border-red-800/30 shadow-sm p-5">
            <div class="text-2xl font-bold text-red-600 mb-1">{{ $verifications->where('status', 'expert_rejected')->count() }}</div>
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Rejetées</div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('expert.verifications.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Statut</label>
                    <select name="status" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 focus:outline-none transition-colors">
                        <option value="">Tous les statuts</option>
                        <option value="expert_review" {{ request('status') === 'expert_review' ? 'selected' : '' }}>
                            En attente d'examen
                        </option>
                        <option value="expert_approved" {{ request('status') === 'expert_approved' ? 'selected' : '' }}>
                            Approuvées
                        </option>
                        <option value="expert_rejected" {{ request('status') === 'expert_rejected' ? 'selected' : '' }}>
                            Rejetées
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Catégorie</label>
                    <select name="category" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 focus:outline-none transition-colors">
                        <option value="">Toutes les catégories</option>
                        @foreach(\App\Models\Category::all() as $category)
                            <option value="{{ $category->slug }}"
                                {{ request('category') === $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex space-x-3">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm shadow-primary-600/20">
                        <i class="fas fa-filter mr-2"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('expert.verifications.index') }}"
                       class="px-6 py-2.5 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        @if(request()->hasAny(['status', 'category']))
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">Filtres actifs:</span>
                @if(request('status'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
                        Statut: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                    </span>
                @endif
                @if(request('category'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
                        Catégorie: {{ \App\Models\Category::where('slug', request('category'))->first()?->name }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Liste des vérifications --}}
    @forelse($verifications as $verification)
        @php
            $hoursWaiting = $verification->expert_assigned_at?->diffInHours(now()) ?? 0;
            $priorityColor = $hoursWaiting > 48 ? 'bg-red-500' : ($hoursWaiting > 24 ? 'bg-amber-500' : 'bg-emerald-500');
        @endphp

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm mb-5 overflow-hidden transition-all duration-200 hover:shadow-md">
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-700 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-3">
                    <strong class="text-sm text-slate-900 dark:text-white">Vérification #{{ $verification->id }}</strong>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $verification->getStatusBadgeClass() }}">
                        {{ $verification->getStatusLabel() }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold text-white {{ $priorityColor }}">
                        @if($hoursWaiting > 48)
                            Urgent
                        @elseif($hoursWaiting > 24)
                            Priorité
                        @else
                            Normal
                        @endif
                    </span>
                </div>
                <small class="text-xs text-slate-500 dark:text-slate-400">
                    Créée {{ $verification->created_at->diffForHumans() }}
                </small>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-2">
                        @if(!empty($verification->item->images) && isset($verification->item->images[0]))
                            <img src="{{ asset('storage/' . $verification->item->images[0]) }}"
                                 class="w-20 h-20 object-cover rounded-xl ring-1 ring-slate-200 dark:ring-slate-700" alt="Produit">
                        @else
                            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                                <i class="fas fa-image text-slate-400"></i>
                            </div>
                        @endif
                    </div>

                    <div class="lg:col-span-6 space-y-2">
                        <h5 class="font-semibold text-slate-900 dark:text-white">{{ $verification->item->name ?? $verification->item->title ?? 'Produit sans nom' }}</h5>
                        <div class="text-sm text-slate-600 dark:text-slate-300">
                            <div class="flex items-center mb-1.5">
                                <i class="fas fa-tag mr-2.5 w-4 text-slate-400"></i>
                                {{ $verification->item->category->name ?? 'Sans catégorie' }}
                            </div>
                            <div class="flex items-center mb-1.5">
                                <i class="fas fa-building mr-2.5 w-4 text-slate-400"></i>
                                {{ $verification->item->brand->name ?? 'Marque non spécifiée' }}
                            </div>
                            <div class="flex items-center mb-1.5">
                                <i class="fas fa-user mr-2.5 w-4 text-slate-400"></i>
                                Vendeur: {{ $verification->vendor->name }}
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-dollar-sign mr-2.5 w-4 text-slate-400"></i>
                                <span class="font-semibold">{{ number_format($verification->item->price, 0, ',', ' ') }} {{ $verification->item->currency }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div class="flex items-center gap-1.5 mb-4">
                            <span class="inline-flex items-center text-[11px] font-medium {{ in_array($verification->status, ['pending_payment', 'ai_analysis', 'expert_review', 'expert_approved', 'expert_rejected']) ? 'text-emerald-600' : 'text-slate-400' }}">
                                <span class="w-2 h-2 rounded-full {{ in_array($verification->status, ['pending_payment', 'ai_analysis', 'expert_review', 'expert_approved', 'expert_rejected']) ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }} mr-1"></span>
                                Soumis
                            </span>
                            <i class="fas fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
                            <span class="inline-flex items-center text-[11px] font-medium {{ $verification->status !== 'pending_payment' ? 'text-emerald-600' : 'text-slate-400' }}">
                                <span class="w-2 h-2 rounded-full {{ $verification->status !== 'pending_payment' ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }} mr-1"></span>
                                Payé
                            </span>
                            <i class="fas fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
                            <span class="inline-flex items-center text-[11px] font-medium {{ in_array($verification->status, ['expert_approved', 'expert_rejected']) ? 'text-emerald-600' : ($verification->status === 'expert_review' ? 'text-blue-600' : 'text-slate-400') }}">
                                <span class="w-2 h-2 rounded-full {{ in_array($verification->status, ['expert_approved', 'expert_rejected']) ? 'bg-emerald-500' : ($verification->status === 'expert_review' ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-600') }} mr-1"></span>
                                Vérifié
                            </span>
                        </div>

                        <div class="space-y-1.5 mb-4">
                            @if($verification->expert_assigned_at)
                                <small class="block text-xs text-slate-500 dark:text-slate-400">
                                    <i class="fas fa-clock mr-1.5"></i>
                                    Assigné {{ $verification->expert_assigned_at->diffForHumans() }}
                                </small>
                            @endif
                            @if($verification->expert_completed_at)
                                <small class="block text-xs text-slate-500 dark:text-slate-400">
                                    <i class="fas fa-check mr-1.5"></i>
                                    Terminé {{ $verification->expert_completed_at->diffForHumans() }}
                                </small>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('expert.verifications.show', $verification) }}"
                               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold transition-colors shadow-sm shadow-primary-600/20">
                                <i class="fas fa-eye mr-1.5"></i>
                                {{ $verification->status === 'expert_review' ? 'Examiner' : 'Voir détails' }}
                            </a>

                            @if($verification->status === 'expert_review')
                                <form action="{{ route('expert.verifications.start', $verification) }}"
                                      method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="px-3.5 py-2 border border-emerald-500 text-emerald-600 rounded-xl text-xs font-semibold hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors"
                                            title="Commencer l'examen">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                @if($verification->expert_notes)
                    <div class="mt-5 p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border-l-4 border-primary">
                        <strong class="text-sm text-slate-900 dark:text-white">Notes de l'expert :</strong>
                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $verification->expert_notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-10 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                <i class="fas fa-search text-slate-400 text-xl"></i>
            </div>
            <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Aucune vérification trouvée</h4>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">
                @if(request()->hasAny(['status', 'category']))
                    Aucune vérification ne correspond aux filtres sélectionnés.
                @else
                    Vous n'avez pas encore de vérifications assignées.
                @endif
            </p>
            @if(request()->hasAny(['status', 'category']))
                <a href="{{ route('expert.verifications.index') }}"
                   class="inline-flex items-center px-5 py-2.5 border border-primary-600 text-primary-600 rounded-xl text-sm font-semibold hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                    Voir toutes les vérifications
                </a>
            @endif
        </div>
    @endforelse

    @if($verifications->hasPages())
        <div class="flex justify-center mt-8">
            {{ $verifications->appends(request()->query())->links() }}
        </div>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($verifications->where('status', 'expert_review')->count() > 0)
            setInterval(function() {
                // Optionnel: recharger automatiquement si des vérifications sont en attente
                // location.reload();
            }, 300000); // Toutes les 5 minutes
        @endif
    });
</script>
@endpush
