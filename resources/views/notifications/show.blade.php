@extends('app')

@section('title', $notification->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('notifications.index') }}" class="text-white/80 hover:text-white p-1.5 rounded-full hover:bg-white/10 transition-colors">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <h1 class="text-lg font-bold text-white">Notification</h1>
                    </div>
                    <span class="text-xs text-white/60">{{ $notification->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>

            <div class="p-6">
                <!-- Icon + Title -->
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/30 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $notification->icon }} {{ $notification->iconColor }} text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $notification->title }}</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="far fa-clock mr-1"></i>
                                {{ $notification->time_ago }}
                            </span>
                            @if($notification->read_at)
                                <span class="text-sm text-gray-400 dark:text-gray-500">
                                    <i class="far fa-check-circle mr-1"></i>
                                    Lu {{ $notification->read_at->diffForHumans() }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
                                    Nouveau
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Separator -->
                <div class="border-t border-gray-100 dark:border-gray-700/50 mb-6"></div>

                <!-- Type badge -->
                <div class="mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-gray-700/50 rounded-xl text-xs font-medium text-gray-600 dark:text-gray-400">
                        <i class="fas {{ $notification->icon }}"></i>
                        {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                    </span>
                </div>

                <!-- Message -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-5 mb-6">
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                        {{ $notification->message }}
                    </p>
                </div>

                <!-- Action buttons -->
                <div class="flex flex-wrap gap-3">
                    @if(!empty($notification->data['url']))
                        <a href="{{ $notification->data['url'] }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium text-sm rounded-xl transition-all hover:shadow-lg hover:shadow-primary-600/20 active:scale-[0.98]">
                            <i class="fas fa-external-link-alt"></i>
                            Voir les details
                        </a>
                    @endif

                    @if($notification->isUnread())
                        <button onclick="markAsRead()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium text-sm rounded-xl transition-colors">
                            <i class="fas fa-check"></i>
                            Marquer comme lu
                        </button>
                    @else
                        <button onclick="markAsUnread()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium text-sm rounded-xl transition-colors">
                            <i class="fas fa-undo"></i>
                            Marquer comme non lu
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAsRead() {
    fetch('{{ route('notifications.read', $notification) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(() => {
        window.location.reload();
    });
}

function markAsUnread() {
    fetch('{{ route('notifications.unread', $notification) }}', {
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
@endsection
