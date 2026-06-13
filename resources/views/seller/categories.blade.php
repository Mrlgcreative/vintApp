@extends('app')

@section('title', 'Catégories')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        @include('seller.partials.sidebar')

        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Catégories</h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Gérez les catégories de vos articles</p>
                    </div>
                    <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors">
                        <i class="fas fa-plus"></i> Ajouter
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($categories as $category)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-primary-600 dark:text-primary-400">
                                    <i class="fas fa-folder"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $category->name }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category->items_count ?? 0 }} article(s)</p>
                                </div>
                            </div>
                            @if($category->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $category->description }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-tags text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucune catégorie</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Aucune catégorie disponible pour le moment</p>
                            <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors">
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