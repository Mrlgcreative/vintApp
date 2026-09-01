@extends('app')

@section('title', 'Promotions')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 pb-16">

    <!-- Hero -->
    <section class="bg-gradient-to-r from-gray-900 via-gray-800 to-emerald-800 text-white">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <h1 class="text-3xl lg:text-5xl font-bold mb-4">
                Promotions
                <span class="text-emerald-400">Flash deals</span>
            </h1>
            <p class="text-white/80 text-sm lg:text-base max-w-2xl">
                Découvrez toutes les offres et ventes flash de la boutique. Faites de bonnes affaires avant la fin des promos !
            </p>
        </div>
    </section>

    @forelse($offers as $offer)
        <!-- Bloc offre -->
        <section class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-8">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-4">
                        <span class="px-3 py-1 {{ $offer->is_flash_sale ? 'bg-red-500' : 'bg-emerald-500' }} text-white text-sm font-bold rounded-full flex items-center gap-1.5">
                            <i class="fas {{ $offer->is_flash_sale ? 'fa-bolt' : 'fa-percent' }}"></i>
                            {{ $offer->discountLabel() }}
                        </span>
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">{{ $offer->title }}</h2>
                        @if($offer->is_featured)
                            <i class="fas fa-star text-amber-400" title="Offre vedette"></i>
                        @endif
                    </div>
                    <div class="flex items-center gap-6">
                        @if($offer->is_flash_sale && $offer->ends_at && !$offer->ends_at->isPast())
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300" data-countdown="{{ $offer->ends_at->timestamp }}">
                                <span class="text-gray-400">Se termine dans :</span>
                                <span class="inline-flex items-center gap-1 rounded-lg bg-red-50 dark:bg-red-500/10 px-3 py-1.5 font-bold text-red-600 dark:text-red-400 tabular-nums" data-countdown-target>
                                    --:--:--
                                </span>
                            </div>
                        @elseif($offer->ends_at && $offer->ends_at->isFuture())
                            <span class="text-sm text-gray-500 dark:text-gray-400">Jusqu'au {{ $offer->ends_at->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                </div>
                @if($offer->description)
                    <p class="mt-3 text-sm lg:text-base text-gray-600 dark:text-gray-300 max-w-3xl">{{ $offer->description }}</p>
                @endif
            </div>

            <!-- Produits concernés -->
            @php
                $products = $offer->scope === 'global'
                    ? \App\Models\Item::where('status', 'active')->latest()->take(8)->get()
                    : ($offer->scope === 'categories'
                        ? \App\Models\Item::whereIn('category_id', $offer->categories->pluck('id'))->where('status', 'active')->take(8)->get()
                        : $offer->items()->where('status', 'active')->take(8)->get());
            @endphp
            @if($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                    @foreach($products as $item)
                        <x-home.product-card :item="$item" />
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 py-12 text-center text-gray-400">
                    <i class="fas fa-box-open text-3xl mb-3"></i>
                    <p>Aucun produit en promotion dans cette offre pour le moment.</p>
                </div>
            @endif
        </section>
    @empty
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <i class="fas fa-tags text-5xl text-gray-300 mb-6"></i>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Aucune promotion en cours</h2>
            <p class="text-gray-500 dark:text-gray-400">Revenez bientôt pour découvrir nos offres !</p>
        </div>
    @endforelse
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-countdown]').forEach(function (el) {
        const endTs = parseInt(el.dataset.countdown, 10);
        const target = el.querySelector('[data-countdown-target]');
        function tick() {
            const diff = endTs - Math.floor(Date.now() / 1000);
            if (diff <= 0) { target.textContent = '00:00:00'; window.location.reload(); return; }
            const h = String(Math.floor(diff / 3600)).padStart(2, '0');
            const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const s = String(diff % 60).padStart(2, '0');
            target.textContent = h + ':' + m + ':' + s;
        }
        tick();
        setInterval(tick, 1000);
    });
});
</script>
@endpush
@endsection