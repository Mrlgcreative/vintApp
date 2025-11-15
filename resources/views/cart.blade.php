@extends('app')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    * { font-family: 'Inter', sans-serif; }
    
    .cart-item-hover { 
        transition: all 0.3s ease; 
    }
    .cart-item-hover:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 25px -8px rgba(124, 58, 237, 0.3); 
    }
    
    .price-badge { 
        background: linear-gradient(135deg, #10b981, #059669); 
    }
    
    .discount-glow {
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
    }
    
    .btn-gradient { 
        background: linear-gradient(135deg, #7c3aed, #a78bfa); 
    }
    .btn-gradient:hover { 
        background: linear-gradient(135deg, #6d28d9, #8b5cf6); 
    }
</style>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')

<!-- Toast pour les notifications -->
<div id="toast" class="fixed top-4 right-4 z-50 transform translate-x-[400px] transition-transform duration-300 flex items-center gap-3">
    @if(session('success'))
        <div class="bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3">
            <span class="text-xl">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3">
            <span class="text-xl">❌</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif
</div>

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-violet-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        
        <!-- En-tête du panier -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/60 p-6 mb-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-primary-600 rounded-xl flex items-center justify-center">
                        <span class="text-white text-xl">🛒</span>
                    </div>
                    Mon panier
                </h1>
                @if(!empty($cart))
                    <span class="px-4 py-2 bg-violet-100 text-violet-700 rounded-full text-sm font-semibold">
                        {{ count($cart) }} article{{ count($cart) > 1 ? 's' : '' }}
                    </span>
                @endif
            </div>
        </div>

        @if(empty($cart))
            <!-- Panier vide -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/60 p-12 text-center">
                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-4xl text-slate-400">🛒</span>
                </div>
                <h3 class="text-xl font-semibold text-slate-700 mb-3">Votre panier est vide</h3>
                <p class="text-slate-500 mb-6">Découvrez nos articles et ajoutez-les à votre panier</p>
                <a href="{{ route('items.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-violet-600 to-violet-700 text-white rounded-xl font-semibold hover:from-violet-700 hover:to-violet-800 transition-all shadow-lg">
                    <span>🏪</span>
                    Continuer mes achats
                </a>
            </div>
        @else
            <!-- Actions du panier -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 border-2 border-red-200 hover:border-red-300 text-red-600 rounded-xl font-medium transition-all">
                        <span>🗑️</span>
                        Vider le panier
                    </button>
                </form>
                
                <a href="{{ route('items.index') }}" class="flex items-center gap-2 px-4 py-2 bg-slate-50 hover:bg-slate-100 border-2 border-slate-200 hover:border-slate-300 text-slate-600 rounded-xl font-medium transition-all">
                    <span>🔙</span>
                    Continuer mes achats
                </a>
            </div>

            <!-- Articles du panier - Version Desktop -->
            <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden mb-8">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-slate-600 uppercase tracking-wide">
                        <div class="col-span-5">Article</div>
                        <div class="col-span-2 text-center">Prix</div>
                        <div class="col-span-2 text-center">Quantité</div>
                        <div class="col-span-2 text-center">Sous-total</div>
                        <div class="col-span-1 text-center">Action</div>
                    </div>
                </div>
                
                <div class="divide-y divide-slate-100">
                    @php $total = 0; @endphp
                    @foreach($cart as $item)
                        @php $total += $item['price'] * $item['quantity']; @endphp
                        <div class="cart-item-hover px-6 py-6">
                            <div class="grid grid-cols-12 gap-4 items-center">
                                <!-- Article -->
                                <div class="col-span-5 flex items-center gap-4">
                                    <div class="w-20 h-20 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0">
                                        @if($item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <span class="text-2xl text-slate-400">📷</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-slate-800 mb-1">{{ $item['name'] }}</h3>
                                        @if(isset($item['has_discount']) && $item['has_discount'])
                                            <span class="inline-flex items-center gap-1 px-3 py-1 price-badge text-white rounded-full text-xs font-medium discount-glow">
                                                <span>🏷️</span>
                                                -{{ $item['discount_percentage'] }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Prix -->
                                <div class="col-span-2 text-center">
                                    @if(isset($item['has_discount']) && $item['has_discount'])
                                        <div class="space-y-1">
                                            <div class="text-sm text-slate-400 line-through">
                                                {{ number_format($item['original_price'], 0, ',', ' ') }} {{ $item['currency'] }}
                                            </div>
                                            <div class="text-green-600 font-bold">
                                                {{ number_format($item['price'], 0, ',', ' ') }} {{ $item['currency'] }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="font-semibold text-slate-700">
                                            {{ number_format($item['price'], 0, ',', ' ') }} {{ $item['currency'] }}
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Quantité -->
                                <div class="col-span-2 text-center">
                                    <form method="POST" action="{{ route('cart.update', $item['id']) }}" class="flex items-center justify-center gap-2">
                                        @csrf
                                        <div class="flex items-center bg-slate-50 rounded-lg border-2 border-slate-200 focus-within:border-violet-500 transition-colors">
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-16 px-3 py-2 bg-transparent text-center font-semibold text-slate-700 focus:outline-none">
                                        </div>
                                        <button type="submit" class="w-8 h-8 bg-violet-100 hover:bg-violet-200 text-violet-600 rounded-lg transition-colors flex items-center justify-center">
                                            <span class="text-sm">🔄</span>
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Sous-total -->
                                <div class="col-span-2 text-center">
                                    <div class="font-bold text-lg text-slate-800">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} {{ $item['currency'] }}
                                    </div>
                                </div>
                                
                                <!-- Action -->
                                <div class="col-span-1 text-center">
                                    <form method="POST" action="{{ route('cart.remove', $item['id']) }}">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors flex items-center justify-center">
                                            <span class="text-sm">❌</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Articles du panier - Version Mobile -->
            <div class="lg:hidden space-y-4 mb-8">
                @php $total = 0; @endphp
                @foreach($cart as $item)
                    @php $total += $item['price'] * $item['quantity']; @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/60 p-4 cart-item-hover">
                        <div class="flex gap-4">
                            <div class="w-20 h-20 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-2xl text-slate-400">📷</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 space-y-3">
                                <div>
                                    <h3 class="font-semibold text-slate-800 mb-1">{{ $item['name'] }}</h3>
                                    @if(isset($item['has_discount']) && $item['has_discount'])
                                        <span class="inline-flex items-center gap-1 px-2 py-1 price-badge text-white rounded-full text-xs font-medium">
                                            <span>🏷️</span>
                                            -{{ $item['discount_percentage'] }}%
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if(isset($item['has_discount']) && $item['has_discount'])
                                            <div class="text-sm text-slate-400 line-through">
                                                {{ number_format($item['original_price'], 0, ',', ' ') }} {{ $item['currency'] }}
                                            </div>
                                            <div class="text-green-600 font-bold">
                                                {{ number_format($item['price'], 0, ',', ' ') }} {{ $item['currency'] }}
                                            </div>
                                        @else
                                            <div class="font-semibold text-slate-700">
                                                {{ number_format($item['price'], 0, ',', ' ') }} {{ $item['currency'] }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('cart.update', $item['id']) }}" class="flex items-center gap-2">
                                            @csrf
                                            <div class="flex items-center bg-slate-50 rounded-lg border-2 border-slate-200">
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-12 px-2 py-1 bg-transparent text-center text-sm font-semibold text-slate-700 focus:outline-none">
                                            </div>
                                            <button type="submit" class="w-8 h-8 bg-violet-100 hover:bg-violet-200 text-violet-600 rounded-lg transition-colors flex items-center justify-center">
                                                <span class="text-sm">🔄</span>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('cart.remove', $item['id']) }}">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors flex items-center justify-center">
                                                <span class="text-sm">❌</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <span class="text-sm text-slate-500">Sous-total: </span>
                                    <span class="font-bold text-slate-800">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} {{ $item['currency'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Résumé et checkout -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/60 p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-lg">
                            <span class="text-slate-600">Sous-total :</span>
                            <span class="font-semibold text-slate-800">{{ number_format($total, 0, ',', ' ') }} {{ $item['currency'] ?? 'CDF' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-slate-500">
                            <span>Frais de livraison :</span>
                            <span>Calculés à l'étape suivante</span>
                        </div>
                        <hr class="border-slate-200">
                        <div class="flex items-center justify-between text-xl font-bold">
                            <span class="text-slate-800">Total :</span>
                            <span class="text-violet-600">{{ number_format($total, 0, ',', ' ') }} {{ $item['currency'] ?? 'CDF' }}</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('items.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors text-center">
                            Continuer mes achats
                        </a>
                        <a href="{{ route('cart.checkout') }}" class="px-8 py-3 btn-gradient text-white rounded-xl font-semibold hover:shadow-lg transition-all text-center flex items-center justify-center gap-2">
                            <span>💳</span>
                            Passer à la caisse
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Animation d'entrée pour le toast
document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('toast');
    if (toast && toast.children.length > 0) {
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(400px)';
        }, 4000);
    }
});

// Confirmation avant suppression
document.querySelectorAll('form[action*="cart.remove"], form[action*="cart.clear"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        const isRemove = this.action.includes('cart.remove');
        const isClear = this.action.includes('cart.clear');
        
        let message = '';
        if (isRemove) message = 'Êtes-vous sûr de vouloir supprimer cet article du panier ?';
        if (isClear) message = 'Êtes-vous sûr de vouloir vider complètement le panier ?';
        
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
});
</script>

@endsection