@extends('app')

@section('title', $exposition->title)

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 pb-16">

    <!-- Hero -->
    <section class="bg-gradient-to-r from-gray-900 via-gray-800 to-emerald-800 text-white">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <a href="{{ route('expositions.index') }}" class="inline-flex items-center gap-2 text-white/70 hover:text-white text-sm mb-6 transition-colors">
                <i class="fas fa-arrow-left"></i> Toutes les boutiques
            </a>
            <div class="flex items-start gap-4 flex-wrap">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-300 text-xl flex-shrink-0">
                    <i class="fas fa-store"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-3xl lg:text-5xl font-bold mb-2">{{ $exposition->title }}</h1>
                    <p class="text-white/80 text-sm lg:text-base">
                        Boutique de <span class="font-semibold text-white">{{ $exposition->user?->name ?? 'Vendeur' }}</span>
                        @if($exposition->ends_at)
                            · visible jusqu'au {{ $exposition->ends_at->format('d/m/Y H:i') }}
                        @endif
                    </p>
                    @if($exposition->description)
                        <p class="text-white/70 mt-3 max-w-2xl">{{ $exposition->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if($items->count() > 0)
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-6">
                Articles exposés <span class="text-sm font-normal text-gray-400">({{ $items->count() }})</span>
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                @foreach($items as $item)
                    <x-home.product-card :item="$item" />
                @endforeach
            </div>
        </div>
    @else
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <i class="fas fa-box-open text-5xl text-gray-300 mb-6"></i>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Aucun article exposé</h2>
            <p class="text-gray-500 dark:text-gray-400">Cette boutique n'a pas encore ajouté d'articles à sa vitrine.</p>
        </div>
    @endif
</div>
@endsection