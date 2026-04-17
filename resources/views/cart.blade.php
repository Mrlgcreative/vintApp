@extends('app')

@section('content')

{{-- Toast notifications --}}
@if(session('success') || session('error'))
<div id="toast" class="fixed top-4 right-4 z-50 translate-x-[400px] transition-transform duration-500 ease-out">
    @if(session('success'))
        <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 pl-4 pr-5 py-4 rounded-2xl shadow-xl flex items-center gap-3 backdrop-blur-sm">
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-green-600 dark:text-green-400 text-sm"></i>
            </div>
            <span class="text-sm font-medium">{{ session('success') }}</span>
            <button onclick="this.closest('#toast').style.transform='translateX(400px)'" class="ml-2 text-green-400 hover:text-green-600 dark:hover:text-green-300">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-white dark:bg-gray-800 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 pl-4 pr-5 py-4 rounded-2xl shadow-xl flex items-center gap-3 backdrop-blur-sm">
            <div class="w-8 h-8 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation text-red-600 dark:text-red-400 text-sm"></i>
            </div>
            <span class="text-sm font-medium">{{ session('error') }}</span>
            <button onclick="this.closest('#toast').style.transform='translateX(400px)'" class="ml-2 text-red-400 hover:text-red-600 dark:hover:text-red-300">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif
</div>
@endif

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 md:py-10">
    <div class="max-w-6xl mx-auto px-4">

        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/20">
                    <i class="fas fa-shopping-cart text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Mon panier</h1>
                    @if(!empty($cart))
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ count($cart) }} article{{ count($cart) > 1 ? 's' : '' }}</p>
                    @endif
                </div>
            </div>

            <a href="{{ route('items.index') }}" class="hidden sm:flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
                Continuer mes achats
            </a>
        </div>

        @if(empty($cart))
            {{-- Panier vide --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-12 md:p-16 text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shopping-cart text-3xl text-gray-300 dark:text-gray-500"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Votre panier est vide</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">Découvrez nos articles et ajoutez-les à votre panier pour commencer</p>
                <a href="{{ route('items.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold transition-colors shadow-lg shadow-primary-600/20">
                    <i class="fas fa-store text-sm"></i>
                    Explorer les articles
                </a>
            </div>
        @else
            @php
                $total = 0;
                foreach ($cart as $cartItem) {
                    $total += $cartItem['price'] * $cartItem['quantity'];
                }
                $lastCurrency = end($cart)['currency'] ?? 'CDF';
            @endphp

            <div class="lg:grid lg:grid-cols-3 lg:gap-6">
                {{-- Colonne principale : articles --}}
                <div class="lg:col-span-2 space-y-3 mb-6 lg:mb-0">

                    {{-- Barre d'actions --}}
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Articles</h2>
                        <form method="POST" action="{{ route('cart.clear') }}" class="cart-clear-form">
                            @csrf
                            <button type="submit" class="flex items-center gap-1.5 text-sm text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 font-medium transition-colors">
                                <i class="fas fa-trash-alt text-xs"></i>
                                Tout vider
                            </button>
                        </form>
                    </div>

                    {{-- Liste des articles --}}
                    @foreach($cart as $item)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 md:p-5 hover:border-primary-200 dark:hover:border-primary-800 hover:shadow-lg hover:shadow-primary-500/5 transition-all duration-300 group">
                            <div class="flex gap-4">
                                {{-- Image --}}
                                <div class="w-20 h-20 md:w-24 md:h-24 bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden flex-shrink-0">
                                    @if($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-image text-2xl text-gray-300 dark:text-gray-500"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Détails --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="min-w-0">
                                            <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $item['name'] }}</h3>
                                            @if(isset($item['has_discount']) && $item['has_discount'])
                                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-tag text-[10px]"></i>
                                                    -{{ $item['discount_percentage'] }}%
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Supprimer --}}
                                        <form method="POST" action="{{ route('cart.remove', $item['id']) }}" class="cart-remove-form">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center text-gray-300 hover:text-red-500 dark:text-gray-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Supprimer">
                                                <i class="fas fa-trash-alt text-sm"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Prix + quantité --}}
                                    <div class="flex items-end justify-between gap-3 mt-3">
                                        <div>
                                            @if(isset($item['has_discount']) && $item['has_discount'])
                                                <div class="text-xs text-gray-400 dark:text-gray-500 line-through mb-0.5">
                                                    {{ number_format($item['original_price'], 0, ',', ' ') }} {{ $item['currency'] }}
                                                </div>
                                            @endif
                                            <div class="text-lg font-bold {{ isset($item['has_discount']) && $item['has_discount'] ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                                                {{ number_format($item['price'], 0, ',', ' ') }} {{ $item['currency'] }}
                                            </div>
                                        </div>

                                        {{-- Contrôle quantité --}}
                                        <form method="POST" action="{{ route('cart.update', $item['id']) }}" class="flex items-center gap-2">
                                            @csrf
                                            <div class="flex items-center bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 overflow-hidden">
                                                <button type="button" onclick="updateQty(this, -1)" class="w-8 h-8 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                                       class="w-10 h-8 bg-transparent text-center text-sm font-semibold text-gray-800 dark:text-white border-x border-gray-200 dark:border-gray-600 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                <button type="button" onclick="updateQty(this, 1)" class="w-8 h-8 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- Sous-total --}}
                                    @if($item['quantity'] > 1)
                                        <div class="mt-2 text-xs text-gray-400 dark:text-gray-500 text-right">
                                            Sous-total : <span class="font-semibold text-gray-600 dark:text-gray-300">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} {{ $item['currency'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Colonne latérale : résumé --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 lg:sticky lg:top-24">
                        <h2 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-receipt text-primary-500 text-sm"></i>
                            Résumé de la commande
                        </h2>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Sous-total ({{ count($cart) }} article{{ count($cart) > 1 ? 's' : '' }})</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ number_format($total, 0, ',', ' ') }} {{ $lastCurrency }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Livraison</span>
                                <span class="text-gray-400 dark:text-gray-500 text-xs italic">Calculée à la caisse</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mb-5">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                                <span class="text-xl font-bold text-primary-600 dark:text-primary-400">{{ number_format($total, 0, ',', ' ') }} {{ $lastCurrency }}</span>
                            </div>
                        </div>

                        <a href="{{ route('cart.checkout') }}" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-primary-600 hover:bg-primary-700 active:scale-[0.98] text-white rounded-xl font-semibold transition-all shadow-lg shadow-primary-600/20">
                            <i class="fas fa-lock text-sm"></i>
                            Passer à la caisse
                        </a>

                        <div class="mt-4 flex items-center justify-center gap-3 text-gray-400 dark:text-gray-500 text-xs">
                            <i class="fas fa-shield-alt"></i>
                            <span>Paiement sécurisé</span>
                            <span class="w-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></span>
                            <i class="fas fa-undo"></i>
                            <span>Retour facile</span>
                        </div>

                        <a href="{{ route('items.index') }}" class="mt-4 w-full flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors sm:hidden">
                            <i class="fas fa-arrow-left text-xs"></i>
                            Continuer mes achats
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toast auto-show + auto-hide
    const toast = document.getElementById('toast');
    if (toast) {
        requestAnimationFrame(() => toast.style.transform = 'translateX(0)');
        setTimeout(() => toast.style.transform = 'translateX(400px)', 4000);
    }

    // Confirmation suppression article
    document.querySelectorAll('.cart-remove-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Supprimer cet article du panier ?')) e.preventDefault();
        });
    });
    // Confirmation vider panier
    document.querySelectorAll('.cart-clear-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Vider complètement le panier ?')) e.preventDefault();
        });
    });
});

// +/- quantité avec auto-submit
function updateQty(btn, delta) {
    const input = btn.closest('form').querySelector('input[name="quantity"]');
    const newVal = Math.max(1, parseInt(input.value) + delta);
    input.value = newVal;
    btn.closest('form').submit();
}
</script>
@endpush