@extends('layouts.admin')

@section('title', 'Wallets Pending')
@section('page-title', 'Wallets Pending - Argent en Attente de Confirmation')

@section('content')
<!-- Section d'information - Responsive -->
<div class="mb-6">
    <div class="flex items-start gap-4 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sky-800 sm:p-6 dark:border-sky-800 dark:bg-sky-900/20 dark:text-sky-300">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-xl text-sky-600 dark:text-sky-300"></i>
        </div>
        <div>
            <h3 class="mb-2 text-sm font-semibold">À propos des Wallets Pending</h3>
            <div class="space-y-2 text-sm">
                <p>• Ces wallets contiennent de l'argent en attente de confirmation de réception par l'acheteur</p>
                <p>• L'argent reste bloqué jusqu'à ce que l'acheteur clique sur "Commande Reçue"</p>
                <p>• Après confirmation, la distribution automatique transfère : Commission (10%) + Transport (5%) → Plateforme | Reste (85%) → Vendeur</p>
                <p>• <strong>Type</strong> : "pending" (sécurisé, non retirable) | <strong>Type</strong> : "main" (retirable après confirmation)</p>
            </div>
        </div>
    </div>
</div>

<!-- Carte principale avec en-tête responsive -->
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <!-- En-tête avec totaux - Responsive -->
    <div class="border-b border-slate-100 bg-slate-50 px-4 py-4 md:px-6 dark:border-slate-700 dark:bg-slate-900">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
            <h3 class="flex items-center text-lg font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-wallet mr-2 text-amber-500"></i>
                <span class="hidden sm:inline">Wallets Pending</span>
                <span class="sm:hidden">Pending</span>
                <span class="ml-2 inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-sm font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                    {{ $pendingWallets->total() }}
                </span>
            </h3>

            <!-- Totaux en grille responsive -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:flex lg:items-center lg:gap-6">
                <div class="text-center lg:text-right">
                    <span class="mb-1 block text-xs text-slate-500 dark:text-slate-300">Total USD</span>
                    <span class="text-lg font-bold tabular-nums text-emerald-600 dark:text-emerald-400">
                        ${{ number_format($pendingWallets->where('currency', 'USD')->sum('balance'), 2) }}
                    </span>
                </div>
                <div class="text-center lg:text-right">
                    <span class="mb-1 block text-xs text-slate-500 dark:text-slate-300">Total CDF</span>
                    <span class="text-lg font-bold tabular-nums text-sky-600 dark:text-sky-400">
                        {{ number_format($pendingWallets->where('currency', 'CDF')->sum('balance'), 0, ',', ' ') }} FC
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal - Table sur desktop, cartes sur mobile -->
    <div class="p-0">
        @if($pendingWallets->count() > 0)
            <!-- Vue tableau - Desktop et tablettes -->
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Vendeur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Devise</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Montant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Dernière MAJ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($pendingWallets as $wallet)
                            <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if($wallet->user?->avatar)
                                            <img src="{{ $wallet->user->avatar_url }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600" alt="Avatar">
                                        @else
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 text-sm font-semibold text-white">
                                                {{ $wallet->user?->initial ?? 'U' }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $wallet->user?->name ?? 'Utilisateur supprimé' }}</div>
                                            <div class="text-sm text-slate-500 dark:text-slate-400">{{ $wallet->user?->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    @if($wallet->currency === 'USD')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">{{ $wallet->currency }}</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">{{ $wallet->currency }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    <div class="text-sm font-bold tabular-nums text-slate-900 dark:text-white">
                                        {{ number_format($wallet->balance, $wallet->currency === 'USD' ? 2 : 0, ',', ' ') }} {{ $wallet->currency }}
                                    </div>
                                    @php
                                        $commission = $wallet->balance * 0.10;
                                        $transport = $wallet->balance * 0.05;
                                        $seller = $wallet->balance - $commission - $transport;
                                    @endphp
                                    <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        Distribution : {{ number_format($seller, $wallet->currency === 'USD' ? 2 : 0) }} vendeur + {{ number_format($commission + $transport, $wallet->currency === 'USD' ? 2 : 0) }} plateforme
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                        <i class="fas fa-lock"></i>
                                        {{ ucfirst($wallet->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    @if($wallet->is_active)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                            <i class="fas fa-circle text-xs"></i>Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">
                                            <i class="fas fa-circle text-xs"></i>Inactif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                    <div>{{ $wallet->updated_at->format('d/m/Y H:i') }}</div>
                                    <div class="text-xs text-slate-400">{{ $wallet->updated_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        @if($wallet->user)
                                            <a href="{{ route('admin.users.show', $wallet->user) }}"
                                               class="inline-flex items-center gap-1.5 rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20 transition-colors hover:bg-sky-100 dark:bg-sky-900/30 dark:text-sky-300"
                                               title="Voir utilisateur">
                                                <i class="fas fa-user text-sm"></i>
                                                <span class="font-medium">Profil</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('orders.index') }}?seller_id={{ $wallet->user_id }}&status=pending"
                                           class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 transition-colors hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300"
                                           title="Voir commandes en attente">
                                            <i class="fas fa-shopping-cart text-sm"></i>
                                            <span class="font-medium">Commandes</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Vue cartes - Mobile et petites tablettes -->
            <div class="lg:hidden">
                <div class="space-y-4 p-4">
                    @foreach($pendingWallets as $wallet)
                        <div class="wallet-card rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                            <!-- En-tête de la carte avec utilisateur -->
                            <div class="mb-4 flex items-center gap-3">
                                @if($wallet->user?->avatar)
                                    <img src="{{ $wallet->user->avatar_url }}" class="h-12 w-12 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600" alt="Avatar">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 text-sm font-semibold text-white">
                                        {{ $wallet->user?->initial ?? 'U' }}
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ $wallet->user?->name ?? 'Utilisateur supprimé' }}</div>
                                    <div class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $wallet->user?->email ?? 'N/A' }}</div>
                                </div>
                                @if($wallet->currency === 'USD')
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">{{ $wallet->currency }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">{{ $wallet->currency }}</span>
                                @endif
                            </div>

                            <!-- Informations financières -->
                            <div class="mb-4 rounded-xl bg-slate-50 p-3 dark:bg-slate-900/50">
                                <div class="mb-2 text-lg font-bold tabular-nums text-slate-900 dark:text-white">
                                    {{ number_format($wallet->balance, $wallet->currency === 'USD' ? 2 : 0, ',', ' ') }} {{ $wallet->currency }}
                                </div>
                                @php
                                    $commission = $wallet->balance * 0.10;
                                    $transport = $wallet->balance * 0.05;
                                    $seller = $wallet->balance - $commission - $transport;
                                @endphp
                                <div class="text-xs text-slate-600 dark:text-slate-300">
                                    <div class="mb-1 font-medium">Distribution prévue :</div>
                                    <div class="space-y-1">
                                        <div>• Vendeur : {{ number_format($seller, $wallet->currency === 'USD' ? 2 : 0) }} {{ $wallet->currency }}</div>
                                        <div>• Plateforme : {{ number_format($commission + $transport, $wallet->currency === 'USD' ? 2 : 0) }} {{ $wallet->currency }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statuts et informations -->
                            <div class="mb-4 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                    <i class="fas fa-lock"></i>
                                    {{ ucfirst($wallet->type) }}
                                </span>
                                @if($wallet->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                        <i class="fas fa-circle text-xs"></i>Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">
                                        <i class="fas fa-circle text-xs"></i>Inactif
                                    </span>
                                @endif
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $wallet->updated_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col gap-2 sm:flex-row">
                                @if($wallet->user)
                                    <a href="{{ route('admin.users.show', $wallet->user) }}"
                                       class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-sky-50 px-4 py-2 text-sm font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20 transition-colors hover:bg-sky-100 dark:bg-sky-900/30 dark:text-sky-300"
                                       title="Voir utilisateur">
                                        <i class="fas fa-user text-sm"></i>
                                        Voir Profil
                                    </a>
                                @endif
                                <a href="{{ route('orders.index') }}?seller_id={{ $wallet->user_id }}&status=pending"
                                   class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 transition-colors hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300"
                                   title="Voir commandes en attente">
                                    <i class="fas fa-shopping-cart text-sm"></i>
                                    Commandes
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- État vide - Responsive -->
            <div class="px-4 py-12 text-center">
                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                    <i class="fas fa-check-circle text-3xl text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <h5 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">Aucun wallet pending</h5>
                <p class="mx-auto mb-4 max-w-md text-slate-500 dark:text-slate-400">Tous les paiements ont été confirmés et distribués.</p>
                <p class="mx-auto max-w-lg text-sm text-slate-400">Les wallets pending apparaissent ici lorsqu'un acheteur paie mais n'a pas encore confirmé la réception.</p>
            </div>
        @endif
    </div>

    <!-- Pagination responsive -->
    @if($pendingWallets->hasPages())
        <div class="border-t border-slate-100 px-4 py-4 md:px-6 dark:border-slate-700">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-slate-600 dark:text-slate-200">
                    Affichage de
                    <span class="font-medium">{{ $pendingWallets->firstItem() ?? 0 }}</span>
                    à
                    <span class="font-medium">{{ $pendingWallets->lastItem() ?? 0 }}</span>
                    sur
                    <span class="font-medium">{{ $pendingWallets->total() }}</span>
                    résultats
                </div>
                <div class="flex justify-center">
                    {{ $pendingWallets->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Amélioration UX pour les cartes mobiles
    const walletCards = document.querySelectorAll('.wallet-card');

    walletCards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });

        card.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Auto-refresh des timestamps toutes les minutes
    function updateTimestamps() {
        const timestamps = document.querySelectorAll('[data-timestamp]');
        timestamps.forEach(element => {
            const timestamp = element.getAttribute('data-timestamp');
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;

            if (diff < 60000) {
                element.textContent = 'À l\'instant';
            } else if (diff < 3600000) {
                element.textContent = `Il y a ${Math.floor(diff / 60000)} min`;
            } else if (diff < 86400000) {
                element.textContent = `Il y a ${Math.floor(diff / 3600000)}h`;
            }
        });
    }

    // Mettre à jour toutes les minutes
    setInterval(updateTimestamps, 60000);

    // Performance monitoring pour mobile
    if (window.innerWidth < 768) {
        console.log('🚀 Mode mobile optimisé activé');

        // Lazy loading des images avatar
        const avatars = document.querySelectorAll('img[src*="googleusercontent"]');
        avatars.forEach(img => {
            img.loading = 'lazy';
        });
    }
});
</script>
@endpush
@endsection
