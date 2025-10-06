@extends('app')

@section('content')
<style>
/* Override Bootstrap avec des styles Tailwind-like */
.tw-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1rem;
    margin-top: 2rem;
}
.tw-grid {
    display: grid;
    gap: 1.5rem;
}
.tw-grid-cols-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}
.tw-grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}
.tw-grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}
.tw-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    border: 1px solid #f3f4f6;
    transition: all 0.2s;
}
.tw-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px) scale(1.02);
}
.tw-p-6 { padding: 1.5rem; }
.tw-p-4 { padding: 1rem; }
.tw-mb-8 { margin-bottom: 2rem; }
.tw-mb-4 { margin-bottom: 1rem; }
.tw-flex { display: flex; }
.tw-items-center { align-items: center; }
.tw-space-x-4 > * + * { margin-left: 1rem; }
.tw-space-y-3 > * + * { margin-top: 0.75rem; }
.tw-w-12 { width: 3rem; }
.tw-h-12 { height: 3rem; }
.tw-rounded-full { border-radius: 9999px; }
.tw-bg-gradient-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
.tw-bg-gradient-green { background: linear-gradient(135deg, #10b981, #34d399); }
.tw-bg-gradient-blue { background: linear-gradient(135deg, #06b6d4, #67e8f9); }
.tw-bg-gradient-orange { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.tw-text-white { color: white; }
.tw-text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
.tw-font-bold { font-weight: 700; }
.tw-font-semibold { font-weight: 600; }
.tw-text-gray-900 { color: #111827; }
.tw-text-gray-600 { color: #4b5563; }
.tw-text-gray-500 { color: #6b7280; }
.tw-text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.tw-bg-slate-50 { background-color: #f8fafc; }
.tw-border-b { border-bottom-width: 1px; }
.tw-border-gray-100 { border-color: #f3f4f6; }
.tw-rounded-t-xl { border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem; }
.tw-bg-gray-50 { background-color: #f9fafb; }
.tw-rounded-lg { border-radius: 0.5rem; }
.tw-hover-violet:hover { background-color: #f3f0ff; }
.tw-hover-green:hover { background-color: #ecfdf5; }
.tw-hover-blue:hover { background-color: #f0f9ff; }
.tw-hover-orange:hover { background-color: #fffbeb; }
.tw-px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
.tw-py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
.tw-bg-violet-500 { background-color: #8b5cf6; }
.tw-bg-green-500 { background-color: #10b981; }
.tw-bg-yellow-500 { background-color: #eab308; }
.tw-text-center { text-align: center; }
.tw-py-8 { padding-top: 2rem; padding-bottom: 2rem; }
.tw-py-12 { padding-top: 3rem; padding-bottom: 3rem; }
.tw-text-xl { font-size: 1.25rem; line-height: 1.75rem; }
.tw-text-2xl { font-size: 1.5rem; line-height: 2rem; }
.tw-text-indigo-600 { color: #4f46e5; }
.tw-bg-indigo-50 { background-color: #eef2ff; }
.tw-border-indigo-100 { border-color: #e0e7ff; }
.tw-flex-1 { flex: 1 1 0%; }
.tw-justify-between { justify-content: space-between; }
.tw-items-start { align-items: flex-start; }
.tw-ml-3 { margin-left: 0.75rem; }
.tw-whitespace-nowrap { white-space: nowrap; }
.tw-text-xs { font-size: 0.75rem; line-height: 1rem; }
.tw-leading-relaxed { line-height: 1.625; }

@media (max-width: 1024px) {
    .tw-grid-cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .tw-grid-cols-4, .tw-grid-cols-2 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
    .tw-container { padding: 0 0.5rem; }
}
</style>

<div class="tw-container">
    <!-- Cards de statistiques -->
    <div class="tw-grid tw-grid-cols-4 tw-mb-8">
        <!-- Card Articles -->
        <div class="tw-card">
            <div class="tw-p-6 tw-flex tw-items-center tw-space-x-4">
                <div class="tw-w-12 tw-h-12 tw-bg-gradient-violet tw-rounded-full tw-flex tw-items-center" style="justify-content: center;">
                    <i class="fas fa-box tw-text-white" style="font-size: 1.25rem;"></i>
                </div>
                <div class="tw-flex-1">
                    <div class="tw-text-gray-600 tw-font-semibold">Articles</div>
                    <div class="tw-text-3xl tw-font-bold tw-text-gray-900">{{ $stats['total_items'] ?? 0 }}</div>
                    <div class="tw-text-sm tw-text-gray-500">Actifs : {{ $stats['active_items'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Card Ventes -->
        <div class="tw-card">
            <div class="tw-p-6 tw-flex tw-items-center tw-space-x-4">
                <div class="tw-w-12 tw-h-12 tw-bg-gradient-green tw-rounded-full tw-flex tw-items-center" style="justify-content: center;">
                    <i class="fas fa-shopping-cart tw-text-white" style="font-size: 1.25rem;"></i>
                </div>
                <div class="tw-flex-1">
                    <div class="tw-text-gray-600 tw-font-semibold">Ventes</div>
                    <div class="tw-text-3xl tw-font-bold tw-text-gray-900">{{ $stats['total_sales'] ?? 0 }}</div>
                    <div class="tw-text-sm tw-text-gray-500">Revenu : {{ number_format($stats['total_revenue'] ?? 0, 2) }} €</div>
                </div>
            </div>
        </div>

        <!-- Card Messages -->
        <div class="tw-card">
            <div class="tw-p-6 tw-flex tw-items-center tw-space-x-4">
                <div class="tw-w-12 tw-h-12 tw-bg-gradient-blue tw-rounded-full tw-flex tw-items-center" style="justify-content: center;">
                    <i class="fas fa-envelope tw-text-white" style="font-size: 1.25rem;"></i>
                </div>
                <div class="tw-flex-1">
                    <div class="tw-text-gray-600 tw-font-semibold">Messages</div>
                    <div class="tw-text-3xl tw-font-bold tw-text-gray-900">{{ $stats['unread_messages'] ?? 0 }}</div>
                    <div class="tw-text-sm tw-text-gray-500">Non lus</div>
                </div>
            </div>
        </div>

        <!-- Card Support -->
        <div class="tw-card">
            <div class="tw-p-6 tw-flex tw-items-center tw-space-x-4">
                <div class="tw-w-12 tw-h-12 tw-bg-gradient-orange tw-rounded-full tw-flex tw-items-center" style="justify-content: center;">
                    <i class="fas fa-headset tw-text-white" style="font-size: 1.25rem;"></i>
                </div>
                <div class="tw-flex-1">
                    <div class="tw-text-gray-600 tw-font-semibold">Support</div>
                    <div class="tw-text-3xl tw-font-bold tw-text-gray-900">{{ $stats['pending_support_chats'] ?? 0 }}</div>
                    <div class="tw-text-sm tw-text-gray-500">En attente</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Support -->
    <div class="tw-mb-8">
        <div class="tw-card">
            <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">
                <div class="tw-flex tw-justify-between tw-items-center">
                    <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                        <i class="fas fa-headset" style="color: #f59e0b; margin-right: 0.75rem;"></i>
                        Support Client
                    </h3>
                    @if(Route::has('admin.support.index'))
                        <a href="{{ route('admin.support.index') }}" 
                           class="tw-text-sm tw-text-blue-600 hover:tw-text-blue-800 tw-font-medium">
                            Voir tout
                        </a>
                    @else
                        <a href="{{ route('support.index') }}" 
                           class="tw-text-sm tw-text-blue-600 hover:tw-text-blue-800 tw-font-medium">
                            Mes demandes
                        </a>
                    @endif
                </div>
            </div>
            <div class="tw-p-6">
                <div class="tw-grid tw-grid-cols-4 tw-gap-4">
                    <div class="tw-text-center">
                        <div class="tw-text-2xl tw-font-bold tw-text-gray-900">{{ $stats['total_support_chats'] ?? 0 }}</div>
                        <div class="tw-text-sm tw-text-gray-500">Total conversations</div>
                    </div>
                    <div class="tw-text-center">
                        <div class="tw-text-2xl tw-font-bold tw-text-red-600">{{ $stats['open_support_chats'] ?? 0 }}</div>
                        <div class="tw-text-sm tw-text-gray-500">Nouvelles demandes</div>
                    </div>
                    <div class="tw-text-center">
                        <div class="tw-text-2xl tw-font-bold tw-text-yellow-600">{{ $stats['pending_support_chats'] ?? 0 }}</div>
                        <div class="tw-text-sm tw-text-gray-500">En cours</div>
                    </div>
                    <div class="tw-text-center">
                        <div class="tw-text-2xl tw-font-bold tw-text-orange-600">{{ $stats['unassigned_support_chats'] ?? 0 }}</div>
                        <div class="tw-text-sm tw-text-gray-500">Non assignées</div>
                    </div>
                </div>
                @if(($stats['unassigned_support_chats'] ?? 0) > 0)
                    <div class="tw-mt-4 tw-p-3 tw-bg-orange-50 tw-border tw-border-orange-200 tw-rounded-lg">
                        <div class="tw-flex tw-items-center">
                            <i class="fas fa-exclamation-triangle tw-text-orange-600 tw-mr-2"></i>
                            <span class="tw-text-orange-800 tw-font-medium">
                                {{ $stats['unassigned_support_chats'] ?? 0 }} conversation(s) nécessitent votre attention
                            </span>
                            @if(Route::has('admin.support.index'))
                                <a href="{{ route('admin.support.index', ['assigned_to' => 'unassigned']) }}" 
                                   class="tw-ml-auto tw-text-orange-600 hover:tw-text-orange-800 tw-font-medium">
                                    Voir →
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Section Articles et Commandes -->
    <div class="tw-grid tw-grid-cols-2 tw-mb-8">
        <!-- Articles récents -->
        <div class="tw-card">
            <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">
                <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                    <i class="fas fa-box" style="color: #8b5cf6; margin-right: 0.75rem;"></i>
                    Articles récents
                </h3>
            </div>
            <div class="tw-p-6">
                @if(isset($recentItems) && $recentItems->count() > 0)
                    <div class="tw-space-y-3">
                        @foreach($recentItems as $item)
                            <div class="tw-flex tw-justify-between tw-items-center tw-p-4 tw-bg-gray-50 tw-rounded-lg tw-hover-violet" style="transition: background-color 0.15s;">
                                <div>
                                    <h6 class="tw-font-semibold tw-text-gray-900 tw-mb-4" style="margin-bottom: 0.25rem;">{{ $item->name }}</h6>
                                    <small class="tw-text-gray-500">{{ $item->category->name ?? 'N/A' }}</small>
                                </div>
                                <span class="tw-px-3 tw-py-1 tw-bg-violet-500 tw-text-white tw-text-sm tw-font-semibold" style="border-radius: 9999px;">
                                    {{ $item->formatted_price }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="tw-text-gray-500 tw-text-center tw-py-8">Aucun article récent</p>
                @endif
            </div>
        </div>

        <!-- Commandes récentes -->
        <div class="tw-card">
            <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">
                <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                    <i class="fas fa-shopping-cart" style="color: #10b981; margin-right: 0.75rem;"></i>
                    Commandes récentes
                </h3>
            </div>
            <div class="tw-p-6">
                @if(isset($recentOrders) && $recentOrders->count() > 0)
                    <div class="tw-space-y-3">
                        @foreach($recentOrders as $order)
                            <div class="tw-flex tw-justify-between tw-items-center tw-p-4 tw-bg-gray-50 tw-rounded-lg tw-hover-green" style="transition: background-color 0.15s;">
                                <div>
                                    <h6 class="tw-font-semibold tw-text-gray-900" style="margin-bottom: 0.25rem;">Commande #{{ $order->id }}</h6>
                                    <small class="tw-text-gray-500">{{ $order->item->name ?? 'N/A' }}</small>
                                </div>
                                <span class="tw-px-3 tw-py-1 tw-text-sm tw-font-semibold {{ $order->status === 'completed' ? 'tw-bg-green-500' : 'tw-bg-yellow-500' }} tw-text-white" style="border-radius: 9999px;">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="tw-text-gray-500 tw-text-center tw-py-8">Aucune commande récente</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Section Messages et Notifications -->
    <div class="tw-grid tw-grid-cols-2 tw-mb-8">
        <!-- Messages récents -->
        <div class="tw-card">
            <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">
                <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                    <i class="fas fa-envelope" style="color: #06b6d4; margin-right: 0.75rem;"></i>
                    Messages récents
                </h3>
            </div>
            <div class="tw-p-6">
                @if(isset($recentMessages) && $recentMessages->count() > 0)
                    <div class="tw-space-y-3">
                        @foreach($recentMessages as $msg)
                            <div class="tw-p-4 tw-bg-gray-50 tw-rounded-lg tw-hover-blue" style="transition: background-color 0.15s;">
                                <div class="tw-flex tw-justify-between tw-items-start">
                                    <div class="tw-flex-1">
                                        <h6 class="tw-font-semibold tw-text-gray-900" style="margin-bottom: 0.25rem;">{{ $msg->sender->name ?? 'N/A' }}</h6>
                                        <p class="tw-text-gray-600 tw-text-sm tw-leading-relaxed">{{ Str::limit($msg->content, 50) }}</p>
                                    </div>
                                    <small class="tw-text-gray-500 tw-text-xs tw-ml-3 tw-whitespace-nowrap">{{ $msg->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="tw-text-gray-500 tw-text-center tw-py-8">Aucun message récent</p>
                @endif
            </div>
        </div>

        <!-- Notifications -->
        <div class="tw-card">
            <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">  
                <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                    <i class="fas fa-bell" style="color: #f59e0b; margin-right: 0.75rem;"></i>
                    Notifications
                </h3>
            </div>
            <div class="tw-p-6">
                @if(isset($notifications) && $notifications->count() > 0)
                    <div class="tw-space-y-3">
                        @foreach($notifications as $notif)
                            <div class="tw-p-4 tw-bg-gray-50 tw-rounded-lg tw-hover-orange" style="transition: background-color 0.15s;">
                                <div class="tw-flex tw-justify-between tw-items-start">
                                    <div class="tw-flex-1">
                                        <h6 class="tw-font-semibold tw-text-gray-900" style="margin-bottom: 0.25rem;">{{ $notif->title }}</h6>
                                        <p class="tw-text-gray-600 tw-text-sm tw-leading-relaxed">{{ Str::limit($notif->message, 50) }}</p>
                                    </div>
                                    <small class="tw-text-gray-500 tw-text-xs tw-ml-3 tw-whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="tw-text-gray-500 tw-text-center tw-py-8">Aucune notification</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Section Graphique des ventes -->
    <div class="tw-card">
        <div class="tw-p-4 tw-border-b tw-border-gray-100 tw-bg-slate-50 tw-rounded-t-xl">
            <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900 tw-flex tw-items-center">
                <i class="fas fa-chart-line" style="color: #4f46e5; margin-right: 0.75rem;"></i>
                Évolution des ventes (6 derniers mois)
            </h3>
        </div>
        <div class="tw-p-6">
            @if(isset($salesChart))
                <div class="tw-text-center">
                    <p class="tw-text-gray-500 tw-mb-4">Graphique des ventes</p>
                    <div class="tw-grid tw-grid-cols-2 tw-mb-4" style="grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 1rem;">
                        @foreach($salesChart['labels'] as $index => $label)
                            <div class="tw-text-center tw-p-4 tw-bg-indigo-50 tw-rounded-lg tw-border-indigo-100" style="border: 1px solid;">
                                <div class="tw-text-2xl tw-font-bold tw-text-indigo-600 tw-mb-4" style="margin-bottom: 0.5rem;">{{ $salesChart['data'][$index] ?? 0 }}</div>
                                <small class="tw-text-gray-600 tw-font-semibold">{{ $label }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="tw-text-center tw-py-12">
                    <div style="width: 4rem; height: 4rem; background-color: #f3f4f6; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-chart-line" style="color: #9ca3af; font-size: 1.5rem;"></i>
                    </div>
                    <p class="tw-text-gray-500">Aucune donnée de vente disponible</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 