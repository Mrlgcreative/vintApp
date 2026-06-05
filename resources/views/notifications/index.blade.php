@extends('app')

@section('title', 'Notifications')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="text-white/80 hover:text-white p-1.5 rounded-full hover:bg-white/10 transition-colors">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <h1 class="text-lg font-bold text-white">Notifications</h1>
                            <p class="text-xs text-white/70">
                                @if($unreadCount > 0)
                                    {{ $unreadCount }} non {{ $unreadCount > 1 ? 'lues' : 'lue' }}
                                @else
                                    Aucune notification non lue
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($unreadCount > 0)
                            <button onclick="markAllAsRead()" class="px-3 py-1.5 bg-white/15 text-white text-xs rounded-xl hover:bg-white/25 transition-colors">
                                <i class="fas fa-check-double mr-1"></i>
                                Tout marquer lu
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Filtres rapides -->
            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700/50 flex gap-2 overflow-x-auto">
                <button class="filter-btn px-3 py-1.5 text-xs font-medium rounded-xl bg-primary-600 text-white transition-colors whitespace-nowrap" data-filter="all" onclick="filterNotifications('all')">
                    Toutes
                </button>
                <button class="filter-btn px-3 py-1.5 text-xs font-medium rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors whitespace-nowrap" data-filter="unread" onclick="filterNotifications('unread')">
                    Non lues
                </button>
                <button class="filter-btn px-3 py-1.5 text-xs font-medium rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors whitespace-nowrap" data-filter="orders" onclick="filterNotifications('orders')">
                    Commandes
                </button>
                <button class="filter-btn px-3 py-1.5 text-xs font-medium rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors whitespace-nowrap" data-filter="messages" onclick="filterNotifications('messages')">
                    Messages
                </button>
                <button class="filter-btn px-3 py-1.5 text-xs font-medium rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors whitespace-nowrap" data-filter="wallet" onclick="filterNotifications('wallet')">
                    Portefeuille
                </button>
            </div>

            @if($notifications->count() > 0)
                <!-- Liste des notifications -->
                <div class="divide-y divide-gray-100 dark:divide-gray-700/50" id="notificationsList">
                    @foreach($notifications as $notification)
                        <a href="{{ route('notifications.show', $notification) }}"
                           class="notification-item flex items-start gap-3 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group {{ $notification->isUnread() ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' }}"
                           data-type="{{ $notification->type }}"
                           data-read="{{ $notification->read_at ? 'read' : 'unread' }}">
                            <!-- Icon -->
                            <div class="w-10 h-10 rounded-xl {{ $notification->isUnread() ? 'bg-primary-100 dark:bg-primary-900/30' : 'bg-gray-100 dark:bg-gray-700' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas {{ $notification->icon }} {{ $notification->iconColor }} text-lg"></i>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="text-sm {{ $notification->isUnread() ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-700 dark:text-gray-300' }}">
                                        {{ $notification->title }}
                                    </h4>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap flex-shrink-0">
                                        {{ $notification->time_ago }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">
                                    {{ $notification->message }}
                                </p>
                                @if($notification->isUnread())
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="w-2 h-2 rounded-full bg-primary-600"></span>
                                        <span class="text-xs text-primary-600 dark:text-primary-400 font-medium">Nouveau</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-shrink-0 text-gray-300 dark:text-gray-600 group-hover:text-gray-500 dark:group-hover:text-gray-400 transition-colors self-center">
                                <i class="fas fa-chevron-right text-sm"></i>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/50">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @else
                <!-- Empty state -->
                <div class="text-center py-20 px-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-bell-slash text-3xl text-blue-400 dark:text-blue-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Aucune notification</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                        Vous recevrez des notifications lorsque quelqu'un vous enverra un message, commandera un article, ou appliquera une reduction.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentFilter = 'all';

function filterNotifications(filter) {
    currentFilter = filter;
    const items = document.querySelectorAll('.notification-item');
    const buttons = document.querySelectorAll('.filter-btn');

    buttons.forEach(btn => {
        const isActive = btn.dataset.filter === filter;
        btn.className = `filter-btn px-3 py-1.5 text-xs font-medium rounded-xl transition-colors whitespace-nowrap ${
            isActive
                ? 'bg-primary-600 text-white'
                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
        }`;
    });

    let visibleCount = 0;
    items.forEach(item => {
        let show = false;
        const type = item.dataset.type;
        const read = item.dataset.read;

        switch (filter) {
            case 'all':
                show = true;
                break;
            case 'unread':
                show = read === 'unread';
                break;
            case 'orders':
                show = type.startsWith('order_') || type.startsWith('refund_') || type.startsWith('local_delivery_');
                break;
            case 'messages':
                show = type === 'new_message';
                break;
            case 'wallet':
                show = type.startsWith('wallet_') || type === 'affiliate_commission';
                break;
            default:
                show = true;
        }

        item.style.display = show ? 'flex' : 'none';
        if (show) visibleCount++;
    });
}

function markAllAsRead() {
    fetch('{{ route('notifications.read-all') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(() => {
        window.location.reload();
    });
}
</script>
@endpush

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
@endsection
