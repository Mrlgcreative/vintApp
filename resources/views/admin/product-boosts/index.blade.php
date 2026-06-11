@extends('layouts.admin')

@section('title', 'Boosts appliqués')
@section('page-title', 'Boosts appliqués')

@section('content')
@if(session('success'))
    <div class="flex items-center rounded-xl bg-green-50 p-4 text-green-800 animate-fade-in mb-6">
        <i class="fas fa-check-circle mr-3 text-green-500"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" class="ml-4 text-green-500 hover:text-green-700" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="flex items-center rounded-xl bg-red-50 p-4 text-red-800 animate-fade-in mb-6">
        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" class="ml-4 text-red-500 hover:text-red-700" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="Rechercher un boost...">
                </div>
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expiré</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div>
                <select name="boost_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Tous les types</option>
                    @foreach($boostTypes as $bt)
                        <option value="{{ $bt->id }}" {{ request('boost_type') == $bt->id ? 'selected' : '' }}>{{ $bt->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.product-boosts.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Total</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Actifs</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Expirés</p>
        <p class="text-2xl font-bold text-gray-500">{{ $stats['expired'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <p class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-2">Annulés</p>
        <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <p class="text-xs font-semibold text-primary-600 uppercase tracking-wider mb-2">Revenus</p>
        <p class="text-2xl font-bold text-primary-600">${{ number_format($stats['revenue'], 2) }}</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
        <h5 class="text-lg font-bold text-gray-900 dark:text-white">Liste des boosts</h5>
    </div>
    <div class="p-0">
        @if($boosts->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Article</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Vendeur</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Durée</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Prix</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Vues</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($boosts as $boost)
                    <tr class="hover:bg-gray-50 dark:bg-gray-900 transition-colors">
                        <td class="px-6 py-4">
                            <a href="#" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                {{ Str::limit($boost->item?->name ?? 'N/A', 30) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $boost->user?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium"
                                  style="background: {{ $boost->boostType?->color ?? '#3B82F6' }}20; color: {{ $boost->boostType?->color ?? '#3B82F6' }}">
                                <i class="{{ $boost->boostType?->icon ?? 'fas fa-bolt' }} text-xs"></i>
                                {{ $boost->boostType?->display_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $boost->duration }}j</td>
                        <td class="px-6 py-4 text-sm font-semibold">${{ number_format($boost->total_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm">{{ $boost->views_generated }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $boost->status === 'active' ? 'bg-green-100 text-green-800 border border-green-200' : '' }}
                                {{ $boost->status === 'expired' ? 'bg-gray-100 text-gray-800 border border-gray-200' : '' }}
                                {{ $boost->status === 'cancelled' ? 'bg-red-100 text-red-800 border border-red-200' : '' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                    {{ $boost->status === 'active' ? 'bg-green-500' : '' }}
                                    {{ $boost->status === 'expired' ? 'bg-gray-500' : '' }}
                                    {{ $boost->status === 'cancelled' ? 'bg-red-500' : '' }}">
                                </span>
                                {{ ucfirst($boost->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $boost->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.product-boosts.show', $boost) }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($boosts->hasPages())
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-xs sm:text-sm text-gray-600">
                    Affichage de {{ $boosts->firstItem() }} à {{ $boosts->lastItem() }} sur {{ $boosts->total() }}
                </div>
                {{ $boosts->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <i class="fas fa-rocket text-3xl text-gray-400"></i>
            </div>
            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun boost</h5>
            <p class="text-gray-500">Aucun boost n'a été appliqué pour le moment.</p>
        </div>
        @endif
    </div>
</div>
@endsection
