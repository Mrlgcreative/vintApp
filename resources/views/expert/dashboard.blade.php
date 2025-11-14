@extends('layouts.admin')

@section('title', 'Dashboard Expert')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- En-tête avec gradient -->
    <div class="bg-gradient-to-r from-indigo-500 to-primary-600 rounded-xl p-8 text-white mb-8 shadow-lg">
        <h1 class="text-3xl font-bold mb-2">
            <i class="fas fa-tachometer-alt mr-3"></i>
            Dashboard Expert
        </h1>
        <p class="text-indigo-100">
            Bienvenue {{ Auth::user()->name }} - Gérez vos vérifications d'authenticité
        </p>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm font-medium">En attente</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_assignments'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm font-medium">Complétées aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm font-medium">Total vérifiées</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_verified'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-primary-100 rounded-full">
                    <i class="fas fa-percentage text-primary-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm font-medium">Taux d'approbation</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['approval_rate'], 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Vérifications en attente -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-xl shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-tasks mr-2 text-indigo-600"></i>
                            Vérifications en attente
                        </h2>
                        <a href="{{ route('expert.verifications.index') }}" 
                           class="text-indigo-600 hover:text-indigo-700 font-medium">
                            Voir tout
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @forelse($pendingChecks as $check)
                        <div class="border border-gray-200 rounded-lg p-4 mb-4 last:mb-0 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        @if(!empty($check->item->images) && isset($check->item->images[0]))
                                            <img src="{{ asset('storage/' . $check->item->images[0]) }}" 
                                                 class="w-12 h-12 object-cover rounded-lg mr-3" alt="Produit">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-lg mr-3 flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $check->item->name ?? $check->item->title ?? 'Produit sans nom' }}</h3>
                                            <p class="text-sm text-gray-500">{{ $check->item->category->name ?? 'Sans catégorie' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center text-sm text-gray-600 space-x-4">
                                        <span>
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $check->vendor->name ?? 'Vendeur inconnu' }}
                                        </span>
                                        <span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $check->expert_assigned_at ? $check->expert_assigned_at->diffForHumans() : 'Date inconnue' }}
                                        </span>
                                        @if($check->item->price)
                                        <span>
                                            <i class="fas fa-dollar-sign mr-1"></i>
                                                                                    <div class="mt-2">
                                            <span class="text-xl font-bold text-gray-900">
                                                {{ number_format($check->item->price, 0, ',', ' ') }} {{ $check->item->currency }}
                                            </span>
                                        </div>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    @php
                                        $hoursWaiting = $check->expert_assigned_at ? $check->expert_assigned_at->diffInHours(now()) : 0;
                                        $urgencyClass = $hoursWaiting > 48 ? 'bg-red-500' : ($hoursWaiting > 24 ? 'bg-yellow-500' : 'bg-green-500');
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white {{ $urgencyClass }}">
                                        @if($hoursWaiting > 48)
                                            Urgent
                                        @elseif($hoursWaiting > 24)
                                            Priorité
                                        @else
                                            Normal
                                        @endif
                                    </span>

                                    <a href="{{ route('expert.verifications.show', $check) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>
                                        Examiner
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-green-400 text-4xl mb-3"></i>
                            <p class="text-gray-500 font-medium">Aucune vérification en attente</p>
                            <p class="text-sm text-gray-400">Vous êtes à jour !</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations expert et activités récentes -->
        <div class="space-y-6">
            <!-- Profil expert -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-primary-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="ml-4">
                        <h3 class="font-bold text-gray-900">{{ Auth::user()->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $expertProfile ? ucfirst($expertProfile->level) : 'Bronze' }} Expert</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Spécialisations</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @if($expertProfile && $expertProfile->specialties)
                            @foreach($expertProfile->specialties as $specialty)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ ucfirst($specialty) }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-xs text-gray-500">Aucune spécialisation définie</span>
                        @endif
                    </div>
                    
                    <div class="pt-3 border-t border-gray-200">
                        <a href="{{ route('expert.profile') }}" 
                           class="block w-full text-center py-2 px-4 border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors">
                            Voir le profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Vérifications récentes -->
            <div class="bg-white rounded-xl shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">
                        <i class="fas fa-history mr-2 text-gray-400"></i>
                        Activités récentes
                    </h3>
                </div>
                <div class="p-6">
                    @forelse($recentChecks->take(5) as $recent)
                        <div class="flex items-center justify-between py-2 @if(!$loop->last) border-b border-gray-100 @endif">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $recent->item->name ?? $recent->item->title ?? 'Produit sans nom' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $recent->expert_completed_at ? $recent->expert_completed_at->diffForHumans() : 'Date inconnue' }}
                                </p>
                            </div>
                            <div class="ml-3">
                                @if($recent->status === 'expert_approved')
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-times-circle text-red-500"></i>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">
                            Aucune activité récente
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="font-bold text-gray-900 mb-4">
                    <i class="fas fa-bolt mr-2 text-yellow-400"></i>
                    Actions rapides
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('expert.verifications.index') }}" 
                       class="block w-full text-center py-2 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-list mr-2"></i>
                        Toutes les vérifications
                    </a>
                    <a href="{{ route('expert.verifications.index', ['status' => 'expert_review']) }}" 
                       class="block w-full text-center py-2 px-4 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        En attente d'examen
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection