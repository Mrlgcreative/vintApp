@extends('app')

@section('title', 'Catégories')

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
                                        <i class="fas fa-tags text-base"></i>
                                    </div>
                                    Catégories
                                </h1>
                                <p class="text-white/80 mt-2 text-sm sm:text-base">Gérez les catégories de vos articles</p>
                            </div>
                            <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-medium hover:bg-white/25 active:scale-95 transition-all duration-200">
                                <i class="fas fa-plus"></i> Ajouter
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($categories as $category)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg hover:border-vinted-primary-200 dark:hover:border-vinted-primary-500/30 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-3">
                                <x-icon :icon="'fas fa-folder'" tone="primary" size="md" />
                                <div class="min-w-0">
                                    <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $category->name }}</h3>
                                </div>
                            </div>
                            @if($category->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">{{ $category->description }}</p>
                            @endif
                            <div class="flex items-center justify-between mt-1">
                                <x-badge :variant="'soft-primary'">{{ $category->items_count ?? 0 }} article(s)</x-badge>
                                <a href="{{ route('categories.show', $category) }}" class="text-xs font-medium text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 transition-colors">Voir →</a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <div class="w-16 h-16 bg-vinted-primary-50 dark:bg-vinted-primary-500/15 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-tags text-vinted-primary-400 dark:text-vinted-primary-300 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucune catégorie</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Aucune catégorie disponible pour le moment</p>
                            <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-vinted-primary-600 text-white rounded-xl font-semibold hover:bg-vinted-primary-700 transition-colors shadow-sm">
                                <i class="fas fa-plus"></i> Créer la première catégorie
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>
</div>
@endsection