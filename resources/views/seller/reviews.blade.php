@extends('app')

@section('title', 'Mes notes')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        @include('seller.partials.sidebar')

        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-vinted-primary-600 via-vinted-primary-500 to-vinted-primary-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
                        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
                        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <i class="fas fa-star text-base"></i>
                                    </div>
                                    Mes notes
                                </h1>
                                <p class="text-white/80 mt-2 text-sm sm:text-base">Avis reçus sur vos articles</p>
                            </div>
                            <div class="flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5">
                                <span class="text-2xl font-bold">{{ number_format($stats['average'] ?? 0, 1) }}</span>
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($stats['average'] ?? 0) ? 'text-yellow-300' : 'text-white/30' }} text-xs"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    @if($reviews->count() > 0)
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($reviews as $review)
                                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-vinted-primary-500 to-vinted-primary-700 flex items-center justify-center text-white flex-shrink-0 font-semibold">
                                            {{ strtoupper(substr($review->reviewer->name ?? 'A', 0, 2)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $review->reviewer->name ?? 'Anonyme' }}</span>
                                                <span class="flex items-center gap-0.5 text-yellow-500">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= ($review->rating ?? 0) ? 'text-yellow-500' : 'text-gray-200 dark:text-gray-600' }} text-xs"></i>
                                                    @endfor
                                                </span>
                                            </div>
                                            @if($review->comment)
                                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $review->comment }}</p>
                                            @endif
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
                                                @if($review->item)
                                                    <span class="text-xs text-gray-300 dark:text-gray-600">·</span>
                                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $review->item->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $reviews->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-16 h-16 bg-vinted-primary-50 dark:bg-vinted-primary-500/15 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-star text-vinted-primary-400 dark:text-vinted-primary-300 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun avis</h3>
                            <p class="text-gray-500 dark:text-gray-400">Les avis de vos acheteurs apparaîtront ici</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection