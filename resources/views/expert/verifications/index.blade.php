@extends('layouts.admin')

@section('title', 'Mes Vérifications - Expert')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <!-- En-tête -->
    <div class="bg-gradient-to-r from-primary to-primary-600 rounded-xl p-8 text-white mb-8 shadow-lg">
        <h1 class="text-3xl font-bold mb-2">
            <i class="fas fa-tasks mr-3"></i>
            Mes Vérifications
        </h1>
        <p class="text-primary-100">
            Gérez toutes vos demandes de vérification d'authenticité
        </p>
    </div>

    <!-- Statistiques rapides -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 mb-8 shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-primary mb-1">{{ $verifications->total() }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600 mb-1">
                    {{ $verifications->where('status', 'expert_review')->count() }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider">En attente</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 mb-1">
                    {{ $verifications->where('status', 'expert_approved')->count() }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider">Approuvées</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600 mb-1">
                    {{ $verifications->where('status', 'expert_rejected')->count() }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rejetées</div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('expert.verifications.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Statut</label>
                    <select name="status" class="w-full px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Catégorie</label>
                    <select name="category" class="w-full px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
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
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('expert.verifications.index') }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 dark:bg-gray-900 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        @if(request()->hasAny(['status', 'category']))
            <div class="mt-4 flex flex-wrap gap-2">
                <strong class="text-gray-700 dark:text-gray-200">Filtres actifs:</strong>
                @if(request('status'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                        Statut: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                    </span>
                @endif
                @if(request('category'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                        Catégorie: {{ \App\Models\Category::where('slug', request('category'))->first()?->name }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Liste des vérifications -->
    @forelse($verifications as $verification)
        @php
            $hoursWaiting = $verification->expert_assigned_at?->diffInHours(now()) ?? 0;
            $priorityClass = $hoursWaiting > 48 ? 'border-l-red-500' : ($hoursWaiting > 24 ? 'border-l-yellow-500' : 'border-l-green-500');
        @endphp
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md mb-6 overflow-hidden hover:shadow-lg transition-shadow border-l-4 {{ $priorityClass }}">
            <div class="p-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <strong>Vérification #{{ $verification->id }}</strong>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $verification->getStatusBadgeClass() }}">
                            {{ $verification->getStatusLabel() }}
                        </span>
                    </div>
                    <small class="text-gray-500 dark:text-gray-400">
                        Créée {{ $verification->created_at->diffForHumans() }}
                    </small>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Image du produit -->
                    <div class="lg:col-span-2">
                        @if(!empty($verification->item->images) && isset($verification->item->images[0]))
                            <img src="{{ asset('storage/' . $verification->item->images[0]) }}" 
                                 class="w-20 h-20 object-cover rounded-lg" alt="Produit">
                        @else
                            <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Détails du produit -->
                    <div class="lg:col-span-6 space-y-2">
                        <h5 class="font-semibold text-gray-900 dark:text-white">{{ $verification->item->name ?? $verification->item->title ?? 'Produit sans nom' }}</h5>
                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-tag mr-2 w-4"></i>
                                {{ $verification->item->category->name ?? 'Sans catégorie' }}
                            </div>
                            <div class="flex items-center mb-1">
                                <i class="fas fa-building mr-2 w-4"></i>
                                {{ $verification->item->brand->name ?? 'Marque non spécifiée' }}
                            </div>
                            <div class="flex items-center mb-1">
                                <i class="fas fa-user mr-2 w-4"></i>
                                Vendeur: {{ $verification->vendor->name }}
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-dollar-sign mr-2 w-4"></i>
                                Prix: {{ number_format($verification->item->price, 0, ',', ' ') }} {{ $verification->item->currency }}
                            </div>
                        </div>
                    </div>

                    <!-- Timeline et actions -->
                    <div class="lg:col-span-4">
                        <!-- Timeline de statut -->
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="flex items-center text-xs {{ in_array($verification->status, ['pending_payment', 'ai_analysis', 'expert_review', 'expert_approved', 'expert_rejected']) ? 'text-green-600' : 'text-gray-400' }}">
                                <div class="w-2 h-2 rounded-full {{ in_array($verification->status, ['pending_payment', 'ai_analysis', 'expert_review', 'expert_approved', 'expert_rejected']) ? 'bg-green-500' : 'bg-gray-300' }} mr-1"></div>
                                Soumis
                            </div>
                            <div class="flex items-center text-xs {{ $verification->status !== 'pending_payment' ? 'text-green-600' : 'text-gray-400' }}">
                                <div class="w-2 h-2 rounded-full {{ $verification->status !== 'pending_payment' ? 'bg-green-500' : 'bg-gray-300' }} mr-1"></div>
                                Payé
                            </div>
                            <div class="flex items-center text-xs {{ in_array($verification->status, ['expert_approved', 'expert_rejected']) ? 'text-green-600' : ($verification->status === 'expert_review' ? 'text-blue-600' : 'text-gray-400') }}">
                                <div class="w-2 h-2 rounded-full {{ in_array($verification->status, ['expert_approved', 'expert_rejected']) ? 'bg-green-500' : ($verification->status === 'expert_review' ? 'bg-blue-500' : 'bg-gray-300') }} mr-1"></div>
                                Vérifié
                            </div>
                        </div>

                        <!-- Informations temporelles -->
                        <div class="space-y-1 mb-4">
                            @if($verification->expert_assigned_at)
                                <small class="block text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-clock mr-1"></i>
                                    Assigné {{ $verification->expert_assigned_at->diffForHumans() }}
                                </small>
                            @endif
                            @if($verification->expert_completed_at)
                                <small class="block text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-check mr-1"></i>
                                    Terminé {{ $verification->expert_completed_at->diffForHumans() }}
                                </small>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('expert.verifications.show', $verification) }}" 
                               class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-700 transition-colors text-sm">
                                <i class="fas fa-eye mr-1"></i>
                                {{ $verification->status === 'expert_review' ? 'Examiner' : 'Voir détails' }}
                            </a>

                            @if($verification->status === 'expert_review')
                                <form action="{{ route('expert.verifications.start', $verification) }}" 
                                      method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 border border-green-500 text-green-600 rounded-lg hover:bg-green-50 transition-colors text-sm"
                                            title="Commencer l'examen">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notes expert si disponibles -->
                @if($verification->expert_notes)
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                        <strong class="text-gray-900 dark:text-white">Notes de l'expert :</strong>
                        <p class="mt-1 text-gray-700 dark:text-gray-200">{{ $verification->expert_notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 text-center">
            <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
            <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aucune vérification trouvée</h4>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                @if(request()->hasAny(['status', 'category']))
                    Aucune vérification ne correspond aux filtres sélectionnés.
                @else
                    Vous n'avez pas encore de vérifications assignées.
                @endif
            </p>
            @if(request()->hasAny(['status', 'category']))
                <a href="{{ route('expert.verifications.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-primary text-primary rounded-lg hover:bg-primary-50 transition-colors">
                    Voir toutes les vérifications
                </a>
            @endif
        </div>
    @endforelse

    <!-- Pagination -->
    @if($verifications->hasPages())
        <div class="flex justify-center mt-8">
            {{ $verifications->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh pour les vérifications en cours
    @if($verifications->where('status', 'expert_review')->count() > 0)
        setInterval(function() {
            // Optionnel: recharger automatiquement si des vérifications sont en attente
            // location.reload();
        }, 300000); // Toutes les 5 minutes
    @endif
</script>
@endpush