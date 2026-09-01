@extends('app')

@section('title', 'Mes articles')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        @include('seller.partials.sidebar')

        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Mes articles</h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $items->total() }} article(s) publié(s)</p>
                    </div>
                    <a href="{{ route('items.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors">
                        <i class="fas fa-plus"></i> Nouvel article
                    </a>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    @if($items->count() > 0)
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($items as $item)
                                <div class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                                        @if($item->images && count($item->images) > 0)
                                            <img src="{{ Storage::url($item->images[0]) }}" class="w-full h-full object-cover" alt="">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h6 class="font-semibold text-gray-900 dark:text-white truncate">{{ $item->name }}</h6>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->category->name ?? 'N/A' }} · {{ $item->views }} vues</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $item->formatted_price }}</p>
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $item->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $item->status }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('boost.index', ['item_id' => $item->id]) }}" class="px-3 py-2 text-sm rounded-lg border border-yellow-300 dark:border-yellow-700 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors" title="Booster">
                                            <i class="fas fa-rocket"></i>
                                        </a>
                                        <a href="{{ route('items.edit', $item) }}" class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                class="px-3 py-2 text-sm rounded-lg border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors delete-item"
                                                data-item-id="{{ $item->id }}"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $items->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-box text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun article</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">Commencez par publier votre premier article</p>
                            <a href="{{ route('items.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors">
                                <i class="fas fa-plus"></i> Publier un article
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-item');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            const row = this.closest('.flex.items-center.gap-4.p-4');

            if (confirm('Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.')) {
                this.disabled = true;
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                let notifContainer = document.querySelector('#notif-container');
                if (!notifContainer) {
                    notifContainer = document.createElement('div');
                    notifContainer.id = 'notif-container';
                    notifContainer.className = 'fixed top-4 right-4 z-[100] space-y-2';
                    document.body.appendChild(notifContainer);
                }

                const showNotification = (message, type) => {
                    const el = document.createElement('div');
                    el.className = 'px-4 py-3 rounded-xl shadow-lg text-sm font-medium text-white transition-all duration-300 ' +
                        (type === 'success' ? 'bg-emerald-500' : 'bg-red-500');
                    el.textContent = message;
                    notifContainer.appendChild(el);
                    setTimeout(() => { el.style.opacity = '0'; }, 2500);
                    setTimeout(() => el.remove(), 2800);
                };

                fetch(`/items/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        if (row) {
                            row.style.transition = 'all 0.3s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-100%)';
                            setTimeout(() => row.remove(), 300);
                        }
                        if (document.querySelectorAll('.delete-item').length === 0) {
                            setTimeout(() => window.location.reload(), 300);
                        }
                    } else {
                        showNotification(data.message || 'Erreur lors de la suppression', 'error');
                        this.disabled = false;
                        this.innerHTML = originalContent;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Une erreur est survenue lors de la suppression', 'error');
                    this.disabled = false;
                    this.innerHTML = originalContent;
                });
            }
        });
    });
});
</script>
@endpush