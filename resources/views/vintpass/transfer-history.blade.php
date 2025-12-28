@extends('app')

@section('title', 'Historique des Transferts - ' . $vintPass->pass_id)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Link -->
        <a href="{{ route('vintpass.show', $vintPass) }}" 
           class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour au VintPass
        </a>

        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                    <span class="text-3xl">🔄</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Historique des Transferts</h1>
                    <p class="text-gray-500 dark:text-gray-400">VintPass: {{ $vintPass->pass_id }}</p>
                </div>
                <div class="ml-auto text-right">
                    <p class="text-3xl font-bold text-purple-600">{{ $transfers->total() }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Transferts totaux</p>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        @if($transfers->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <div class="relative">
                <!-- Vertical line -->
                <div class="absolute left-6 top-4 bottom-4 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                
                <div class="space-y-6">
                    @foreach($transfers as $index => $transfer)
                    <div class="relative flex gap-4">
                        <!-- Node -->
                        <div class="w-12 h-12 rounded-full flex items-center justify-center z-10 flex-shrink-0
                            {{ $index === 0 ? 'bg-green-500' : 'bg-purple-500' }}">
                            @if($index === 0)
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $transfer->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                    <span class="inline-block mt-1 text-xs px-2 py-1 rounded-full
                                        {{ $transfer->transfer_type === 'sale' ? 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400' : 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400' }}">
                                        {{ $transfer->transfer_type === 'sale' ? '💰 Vente' : '🎁 Transfert' }}
                                    </span>
                                </div>
                                
                                @if($transfer->price)
                                <div class="text-right">
                                    <p class="text-lg font-bold text-green-600">
                                        {{ number_format($transfer->price, 0, ',', ' ') }} FC
                                    </p>
                                </div>
                                @endif
                            </div>

                            <!-- Transfer Flow -->
                            <div class="flex items-center gap-4">
                                <!-- From -->
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">De</p>
                                    <div class="flex items-center gap-2">
                                        @if($transfer->fromUser && $transfer->fromUser->avatar)
                                        <img src="{{ $transfer->fromUser->avatar }}" 
                                             alt="{{ $transfer->fromUser->name }}"
                                             class="w-8 h-8 rounded-full object-cover">
                                        @else
                                        <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                            <span class="text-sm">👤</span>
                                        </div>
                                        @endif
                                        <span class="text-gray-900 dark:text-white font-medium truncate">
                                            {{ $transfer->fromUser?->name ?? 'Utilisateur inconnu' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Arrow -->
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </div>

                                <!-- To -->
                                <div class="flex-1 text-right">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">À</p>
                                    <div class="flex items-center gap-2 justify-end">
                                        <span class="text-gray-900 dark:text-white font-medium truncate">
                                            {{ $transfer->toUser?->name ?? 'Utilisateur inconnu' }}
                                        </span>
                                        @if($transfer->toUser && $transfer->toUser->avatar)
                                        <img src="{{ $transfer->toUser->avatar }}" 
                                             alt="{{ $transfer->toUser->name }}"
                                             class="w-8 h-8 rounded-full object-cover">
                                        @else
                                        <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                            <span class="text-sm">👤</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Order reference -->
                            @if($transfer->order_id)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Commande: <span class="font-mono text-blue-600">#{{ $transfer->order_id }}</span>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $transfers->links() }}
        </div>
        
        @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center shadow-lg">
            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-4xl">🔄</span>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Aucun transfert</h2>
            <p class="text-gray-500 dark:text-gray-400">
                Ce VintPass n'a pas encore été transféré. Le premier transfert aura lieu lors de la prochaine vente.
            </p>
        </div>
        @endif

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
            <div class="flex gap-3">
                <span class="text-2xl">ℹ️</span>
                <div>
                    <h3 class="font-semibold text-blue-900 dark:text-blue-300 mb-1">Comment fonctionne le transfert ?</h3>
                    <p class="text-blue-700 dark:text-blue-400 text-sm">
                        Lorsque vous vendez un article avec un VintPass, le certificat d'authenticité est automatiquement 
                        transféré au nouveau propriétaire. L'historique complet reste visible pour garantir la traçabilité.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
