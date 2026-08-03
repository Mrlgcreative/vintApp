@extends('layouts.admin')

@section('title', $boostType->display_name)
@section('page-title', $boostType->display_name)

@section('page-actions')
<div class="flex flex-wrap gap-3">
    <a href="{{ route('admin.boost-types.edit', $boostType) }}"
       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-edit mr-2"></i>Modifier
    </a>
    <a href="{{ route('admin.boost-types.index') }}"
       class="inline-flex items-center px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 dark:bg-slate-900 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Retour
    </a>
</div>
@endsection

@section('content')
@if(session('success'))
    <div class="flex items-center rounded-xl bg-green-50 p-4 text-green-800 animate-fade-in mb-6">
        <i class="fas fa-check-circle mr-3 text-green-500"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" class="ml-4 text-green-500 hover:text-green-700" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Revenus générés</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">${{ number_format($stats['total_revenue'], 2) }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">Boosts actifs</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['active_count'] }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider mb-2">Vues générées</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_views']) }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-2">Clics générés</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_clicks']) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-2xl" style="background: {{ $boostType->color ?? '#3B82F6' }}">
                    <i class="{{ $boostType->icon ?? 'fas fa-bolt' }}"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-lg">{{ $boostType->display_name }}</h4>
                    <p class="text-sm text-slate-500">({{ $boostType->name }})</p>
                </div>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Statut</span>
                    <span class="font-semibold {{ $boostType->is_active ? 'text-green-600' : 'text-slate-400' }}">
                        {{ $boostType->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Premium</span>
                    <span class="font-semibold">{{ $boostType->is_premium ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Prix USD</span>
                    <span class="font-semibold">${{ number_format($boostType->price_usd ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Prix CDF</span>
                    <span class="font-semibold">{{ number_format($boostType->price_cdf ?? 0, 0, ',', ' ') }} FC</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Ordre</span>
                    <span class="font-semibold">{{ $boostType->sort_order }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                    <span class="text-slate-500">Durées</span>
                    <span class="font-semibold">
                        @foreach($boostType->available_durations ?? [] as $d)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-900 mr-1">{{ $d }}j</span>
                        @endforeach
                    </span>
                </div>
            </div>
        </div>

        @if($boostType->description)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <h4 class="font-semibold text-slate-900 dark:text-white mb-2">Description</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $boostType->description }}</p>
        </div>
        @endif

        @if($boostType->benefits)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <h4 class="font-semibold text-slate-900 dark:text-white mb-3">Avantages</h4>
            <ul class="space-y-2">
                @foreach((array) $boostType->benefits as $benefit)
                    <li class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <i class="fas fa-check text-green-500 mt-0.5"></i>
                        <span>{{ $benefit }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-4 md:p-6 border-b border-slate-200 dark:border-slate-700">
                <h5 class="text-lg font-bold text-slate-900 dark:text-white">Boosts récents</h5>
            </div>
            <div class="p-0">
                @if($recentBoosts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Article</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Utilisateur</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Durée</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Prix</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Statut</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($recentBoosts as $pb)
                            <tr class="hover:bg-slate-50 dark:bg-slate-900 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="#" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                        {{ $pb->item?->name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-200">{{ $pb->user?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $pb->duration }}j</td>
                                <td class="px-6 py-4 text-sm font-semibold">${{ number_format($pb->total_price, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $pb->status === 'active' ? 'bg-green-100 text-green-800' : ($pb->status === 'expired' ? 'bg-slate-100 text-slate-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($pb->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $pb->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <p class="text-slate-500">Aucun boost pour ce type.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
