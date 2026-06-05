@extends('app')

@section('content')

{{-- Modale de succès - après vérification d'email --}}
@if(session('email_verified'))
<div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50" id="emailVerifiedModal">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-center py-8">
            <div class="mb-4">
                <div class="w-20 h-20 bg-white/20 backdrop-blur rounded-full flex items-center justify-center mx-auto animate-bounce">
                    <i class="fas fa-check-circle text-white text-4xl"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold">Email vérifié avec succès !</h3>
        </div>
        <div class="p-6 text-center">
            <p class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Bienvenue sur VintApp !</p>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Votre compte est maintenant <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 px-2 py-1 rounded-lg font-medium">ACTIF</span>. Vous avez accès à toutes les fonctionnalités.
            </p>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 mb-6 text-left">
                <p class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <i class="fas fa-unlock text-emerald-500 mr-2"></i>
                    Fonctionnalités débloquées :
                </p>
                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <li class="flex items-center"><i class="fas fa-check text-emerald-500 mr-2 text-xs"></i>Créer et vendre des articles</li>
                    <li class="flex items-center"><i class="fas fa-check text-emerald-500 mr-2 text-xs"></i>Passer des commandes</li>
                    <li class="flex items-center"><i class="fas fa-check text-emerald-500 mr-2 text-xs"></i>Envoyer des messages</li>
                    <li class="flex items-center"><i class="fas fa-check text-emerald-500 mr-2 text-xs"></i>Gérer votre profil</li>
                </ul>
            </div>
        </div>
        <div class="px-6 pb-6 text-center">
            <button type="button" class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg shadow-emerald-500/20 transition-all active:scale-[0.98]" onclick="document.getElementById('emailVerifiedModal').style.display='none'">
                <i class="fas fa-rocket mr-2"></i>
                Commencer à explorer
            </button>
        </div>
    </div>
</div>
@endif

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="container mx-auto px-4 max-w-7xl">

        {{-- Statistiques --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg hover:shadow-violet-500/5 hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-violet-100 dark:bg-violet-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-box text-violet-600 dark:text-violet-400 text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_items'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Articles <span class="hidden sm:inline">· {{ $stats['active_items'] ?? 0 }} actifs</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg hover:shadow-emerald-500/5 hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shopping-cart text-emerald-600 dark:text-emerald-400 text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_sales'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ number_format($stats['total_revenue'] ?? 0, 2) }} USD
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg hover:shadow-cyan-500/5 hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-envelope text-cyan-600 dark:text-cyan-400 text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['unread_messages'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Messages non lus</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg hover:shadow-amber-500/5 hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-headset text-amber-600 dark:text-amber-400 text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_support_chats'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Support en attente</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Support Client --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-headset text-amber-500"></i>
                    Support Client
                </h3>
                @if(Route::has('admin.support.index'))
                    <a href="{{ route('admin.support.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors">
                        Voir tout <i class="fas fa-arrow-right text-xs ml-1"></i>
                    </a>
                @else
                    <a href="{{ route('support.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors">
                        Mes demandes <i class="fas fa-arrow-right text-xs ml-1"></i>
                    </a>
                @endif
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_support_chats'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total</div>
                    </div>
                    <div class="text-center p-3 bg-red-50 dark:bg-red-900/10 rounded-xl">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['open_support_chats'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Nouvelles</div>
                    </div>
                    <div class="text-center p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-xl">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_support_chats'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">En cours</div>
                    </div>
                    <div class="text-center p-3 bg-orange-50 dark:bg-orange-900/10 rounded-xl">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['unassigned_support_chats'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Non assignées</div>
                    </div>
                </div>
                @if(($stats['unassigned_support_chats'] ?? 0) > 0)
                    <div class="mt-4 p-3 bg-orange-50 dark:bg-orange-900/10 border border-orange-200 dark:border-orange-800/50 rounded-xl flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-orange-500 flex-shrink-0"></i>
                        <span class="text-sm text-orange-800 dark:text-orange-300 font-medium flex-1">
                            {{ $stats['unassigned_support_chats'] ?? 0 }} conversation(s) nécessitent votre attention
                        </span>
                        @if(Route::has('admin.support.index'))
                            <a href="{{ route('admin.support.index', ['assigned_to' => 'unassigned']) }}" class="text-sm text-orange-600 dark:text-orange-400 hover:text-orange-700 font-semibold flex-shrink-0">
                                Voir <i class="fas fa-arrow-right text-xs ml-1"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Articles et Commandes --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Articles récents --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-box text-violet-500"></i>
                        Articles récents
                    </h3>
                </div>
                <div class="p-4">
                    @if(isset($recentItems) && $recentItems->count() > 0)
                        <div class="space-y-2">
                            @foreach($recentItems as $item)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl hover:bg-violet-50 dark:hover:bg-violet-900/10 transition-colors">
                                    <div class="min-w-0 flex-1">
                                        <h6 class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $item->name }}</h6>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->category->name ?? 'N/A' }}</p>
                                    </div>
                                    <span class="ml-3 px-2.5 py-1 bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 text-xs font-semibold rounded-lg flex-shrink-0">
                                        {{ $item->formatted_price }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-box text-gray-300 dark:text-gray-500"></i>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aucun article récent</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Commandes récentes --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-emerald-500"></i>
                        Commandes récentes
                    </h3>
                </div>
                <div class="p-4">
                    @if(isset($recentOrders) && $recentOrders->count() > 0)
                        <div class="space-y-2">
                            @foreach($recentOrders as $order)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors">
                                    <div class="min-w-0 flex-1">
                                        <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Commande #{{ $order->id }}</h6>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $order->item->name ?? 'N/A' }}</p>
                                    </div>
                                    <span class="ml-3 px-2.5 py-1 text-xs font-semibold rounded-lg flex-shrink-0 {{ $order->status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-shopping-cart text-gray-300 dark:text-gray-500"></i>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune commande récente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Messages et Notifications --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Messages récents --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-envelope text-cyan-500"></i>
                        Messages récents
                    </h3>
                </div>
                <div class="p-4">
                    @if(isset($recentMessages) && $recentMessages->count() > 0)
                        <div class="space-y-2">
                            @foreach($recentMessages as $msg)
                                <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl hover:bg-cyan-50 dark:hover:bg-cyan-900/10 transition-colors">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $msg->sender->name ?? 'N/A' }}</h6>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5 truncate">{{ Str::limit($msg->content, 60) }}</p>
                                        </div>
                                        <span class="text-[11px] text-gray-400 dark:text-gray-500 flex-shrink-0 mt-0.5">{{ $msg->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-envelope text-gray-300 dark:text-gray-500"></i>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aucun message récent</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Notifications --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-bell text-amber-500"></i>
                        Notifications
                    </h3>
                </div>
                <div class="p-4">
                    @if(isset($notifications) && $notifications->count() > 0)
                        <div class="space-y-2">
                            @foreach($notifications as $notif)
                                <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/10 transition-colors">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $notif->title }}</h6>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5 truncate">{{ Str::limit($notif->message, 60) }}</p>
                                        </div>
                                        <span class="text-[11px] text-gray-400 dark:text-gray-500 flex-shrink-0 mt-0.5">{{ $notif->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-bell text-gray-300 dark:text-gray-500"></i>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune notification</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Graphique des ventes --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-chart-line text-primary-500"></i>
                    Ventes (6 derniers mois)
                </h3>
            </div>
            <div class="p-6">
                @php
                    $chartData = $salesChart ?? [
                        'labels' => ['Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct'],
                        'data' => [12, 19, 15, 25, 22, 30]
                    ];
                    $maxValue = max($chartData['data']) ?: 1;
                @endphp

                <div class="flex gap-3 md:gap-4 justify-between" id="sales-chart-container">
                    @foreach($chartData['data'] as $index => $value)
                        @php
                            $percentage = ($value / $maxValue) * 100;
                            $colors = [
                                ['from' => '#8b5cf6', 'to' => '#7c3aed'],
                                ['from' => '#10b981', 'to' => '#059669'],
                                ['from' => '#06b6d4', 'to' => '#0891b2'],
                                ['from' => '#f59e0b', 'to' => '#d97706'],
                                ['from' => '#ef4444', 'to' => '#dc2626'],
                                ['from' => '#6366f1', 'to' => '#4f46e5'],
                            ];
                            $c = $colors[$index % count($colors)];
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1.5 min-w-[48px]">
                            <div class="text-sm md:text-lg font-bold text-gray-800 dark:text-gray-100">{{ $value }}</div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex items-end h-28 md:h-36">
                                <div class="chart-bar w-full rounded-t-lg transition-all duration-700 ease-out"
                                     style="background: linear-gradient(180deg, {{ $c['from'] }}, {{ $c['to'] }}); height: 0%;"
                                     data-height="{{ $percentage }}">
                                </div>
                            </div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1">{{ $chartData['labels'][$index] }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Stats résumé --}}
                <div class="grid grid-cols-3 gap-3 md:gap-4 mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">TOTAL</div>
                        <div class="text-xl md:text-2xl font-bold text-primary-600 dark:text-primary-400">{{ array_sum($chartData['data']) }}</div>
                        <div class="text-[11px] text-gray-400 dark:text-gray-500">ventes</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">MOYENNE</div>
                        <div class="text-xl md:text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ round(array_sum($chartData['data']) / count($chartData['data']), 1) }}</div>
                        <div class="text-[11px] text-gray-400 dark:text-gray-500">par mois</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">MEILLEUR</div>
                        <div class="text-xl md:text-2xl font-bold text-amber-600 dark:text-amber-400">{{ max($chartData['data']) }}</div>
                        <div class="text-[11px] text-gray-400 dark:text-gray-500">{{ $chartData['labels'][array_search(max($chartData['data']), $chartData['data'])] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des barres du graphique
    document.querySelectorAll('.chart-bar').forEach((bar, i) => {
        setTimeout(() => {
            bar.style.height = bar.dataset.height + '%';
        }, 150 + i * 80);
    });

    // Réanimer au scroll si les barres sont encore à 0%
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && entry.target.style.height === '0%') {
                    entry.target.style.height = entry.target.dataset.height + '%';
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.chart-bar').forEach(bar => observer.observe(bar));
    }

    // Modal email vérifié
    const modal = document.getElementById('emailVerifiedModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal) modal.style.display = 'none';
        });
    }
});
</script>
@endpush 