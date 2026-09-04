@extends('app')

@section('title', 'Mes expositions')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        @include('seller.partials.sidebar')

        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-emerald-800 rounded-2xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
                        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
                        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <i class="fas fa-store text-base"></i>
                                    </div>
                                    Mes expositions
                                </h1>
                                <p class="text-white/80 mt-2 text-sm sm:text-base">Vos vitrines numériques exposées dans l'annuaire des boutiques</p>
                            </div>
                            <a href="{{ route('seller.expositions.create') }}" class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-medium hover:bg-white/25 active:scale-95 transition-all duration-200">
                                <i class="fas fa-plus"></i> Nouvelle exposition
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    @if($expositions->count() > 0)
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($expositions as $exposition)
                                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-start gap-3 sm:gap-4">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-white {{ $exposition->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}">
                                            <i class="fas {{ $exposition->status === 'active' ? 'fa-store' : 'fa-pause' }}"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0">
                                                    <h6 class="font-semibold text-gray-900 dark:text-white truncate">{{ $exposition->title }}</h6>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $exposition->items()->count() }} article(s) exposé(s) · {{ $exposition->views ?? 0 }} vue(s)
                                                        @if($exposition->is_featured)
                                                            <span class="text-amber-500"><i class="fas fa-star"></i> En vedette</span>
                                                        @endif
                                                        @if($exposition->ends_at) · fin {{ $exposition->ends_at->format('d/m/Y H:i') }} @endif
                                                    </p>
                                                </div>
                                                <div class="text-right flex-shrink-0">
                                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $exposition->status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300' }}">
                                                        <span class="h-1.5 w-1.5 rounded-full {{ $exposition->status === 'active' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                                        {{ $exposition->statusLabel() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 mt-3 flex-wrap">
                                                <a href="{{ route('expositions.show', $exposition->slug) }}" target="_blank" rel="noopener"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    <i class="fas fa-external-link-alt"></i><span class="hidden sm:inline">Voir</span>
                                                </a>
                                                <a href="{{ route('seller.expositions.edit', $exposition) }}"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    <i class="fas fa-edit"></i><span class="hidden sm:inline">Modifier</span>
                                                </a>
                                                <form action="{{ route('seller.expositions.status', $exposition) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                        <i class="fas {{ $exposition->status === 'active' ? 'fa-pause' : 'fa-play' }}"></i>
                                                        <span class="hidden sm:inline">{{ $exposition->status === 'active' ? 'Pause' : 'Activer' }}</span>
                                                    </button>
                                                </form>
                                                <form action="{{ route('seller.expositions.destroy', $exposition) }}" method="POST" onsubmit="return confirm('Supprimer cette exposition ?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-md border border-red-200 dark:border-red-500/40 bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-300 dark:hover:bg-red-500/20 transition-colors">
                                                        <i class="fas fa-trash"></i><span class="hidden sm:inline">Supprimer</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $expositions->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-500/15 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-store text-emerald-400 dark:text-emerald-300 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucune exposition</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">Exposez une sélection de vos articles dans l'annuaire des boutiques pour attirer plus d'acheteurs</p>
                            <a href="{{ route('seller.expositions.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white rounded-xl font-semibold hover:bg-gray-700 transition-colors shadow-sm">
                                <i class="fas fa-plus"></i> Créer une exposition
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection