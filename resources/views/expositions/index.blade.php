@extends('app')

@section('title', 'Boutiques & expositions')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 pb-16">

    <!-- Hero -->
    <section class="bg-gradient-to-r from-gray-900 via-gray-800 to-emerald-800 text-white">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <h1 class="text-3xl lg:text-5xl font-bold mb-4">
                Boutiques
                <span class="text-emerald-400">& expositions</span>
            </h1>
            <p class="text-white/80 text-sm lg:text-base max-w-2xl">
                Découvrez les vendeurs qui ouvrent leur vitrine numérique et exposent une sélection de leurs meilleurs articles pendant une durée limitée.
            </p>
        </div>
    </section>

    @forelse($expositions as $exposition)
        <section class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-8">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">{{ $exposition->title }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                par <span class="font-medium text-gray-700 dark:text-gray-300">{{ $exposition->user?->name ?? 'Vendeur' }}</span>
                                @if($exposition->ends_at)
                                    · jusqu'au {{ $exposition->ends_at->format('d/m/Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('expositions.show', $exposition->slug) }}" class="inline-flex items-center gap-2 bg-gray-900 dark:bg-emerald-600 text-white rounded-xl px-4 py-2.5 text-sm font-medium hover:bg-gray-700 dark:hover:bg-emerald-700 transition-colors">
                        <i class="fas fa-eye"></i> Visiter la boutique
                    </a>
                </div>
                @if($exposition->description)
                    <p class="mt-3 text-sm lg:text-base text-gray-600 dark:text-gray-300 max-w-3xl">{{ $exposition->description }}</p>
                @endif
            </div>

            @php
                $products = $exposition->items
                    ->filter(fn($it) => $it->status === 'active')
                    ->take(4);
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
                    <p>Aucun article actuellement exposé dans cette boutique.</p>
                </div>
            @endif
        </section>
    @empty
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <i class="fas fa-store text-5xl text-gray-300 mb-6"></i>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Aucune boutique ouverte pour le moment</h2>
            <p class="text-gray-500 dark:text-gray-400">Les vendeurs ouvrent leurs vitrines numériques sur des périodes définies. Revenez bientôt !</p>
        </div>
    @endforelse

    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{ $expositions->links() }}
    </div>
</div>
@endsection