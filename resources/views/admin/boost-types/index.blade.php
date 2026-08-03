@extends('layouts.admin')

@section('title', "Types de Boost")
@section('page-title', "Types de Boost")

@section('page-actions')
<div class="flex flex-wrap gap-3">
    <a href="{{ route('admin.boost-types.create') }}"
       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
        <i class="fas fa-plus mr-2"></i>Nouveau type
    </a>
</div>
@endsection

@section('content')
@if(session('success'))
    <div class="flex items-center rounded-xl bg-green-50 p-4 text-green-800 animate-fade-in mb-6" role="alert">
        <i class="fas fa-check-circle mr-3 text-green-500"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" class="ml-4 text-green-500 hover:text-green-700" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="flex items-center rounded-xl bg-red-50 p-4 text-red-800 animate-fade-in mb-6" role="alert">
        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" class="ml-4 text-red-500 hover:text-red-700" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
    <div class="p-6">
        <form method="GET" action="{{ route('admin.boost-types.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text"
                           class="w-full pl-10 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           name="search"
                           placeholder="Rechercher un type de boost..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div>
                <select name="sort" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="sort_order" {{ request('sort', 'sort_order') === 'sort_order' ? 'selected' : '' }}>Tri par défaut</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom A-Z</option>
                    <option value="-name" {{ request('sort') === '-name' ? 'selected' : '' }}>Nom Z-A</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Plus ancien</option>
                    <option value="-created_at" {{ request('sort') === '-created_at' ? 'selected' : '' }}>Plus récent</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.boost-types.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 dark:bg-slate-900 transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-primary-600 uppercase tracking-wider mb-2">Total types</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $boostTypes->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bolt text-2xl text-primary-600"></i>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-primary-500 to-primary-600"></div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Actifs</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $boostTypes->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider mb-2">Premium</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $boostTypes->where('is_premium', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-crown text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-yellow-500 to-yellow-600"></div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">Boosts actifs</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ \App\Models\ProductBoost::where('status', 'active')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rocket text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
    </div>
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-4 md:p-6 border-b border-slate-200 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h5 class="text-lg md:text-xl font-bold text-slate-900 dark:text-white mb-1">Liste des types de boost</h5>
                <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400">{{ $boostTypes->total() }} type(s)</p>
            </div>
        </div>
    </div>
    <div class="p-0">
        @if($boostTypes->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Nom</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Prix</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Durées</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Boosts</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Ordre</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($boostTypes as $bt)
                    <tr class="hover:bg-slate-50 dark:bg-slate-900 transition-colors {{ !$bt->is_active ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white" style="background: {{ $bt->color ?? '#3B82F6' }}">
                                    <i class="{{ $bt->icon ?? 'fas fa-bolt' }}"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $bt->display_name }}</div>
                                    <div class="text-xs text-slate-500">({{ $bt->name }})</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-slate-900 dark:text-white">${{ number_format($bt->price_usd ?? 0, 2) }}</div>
                            <div class="text-xs text-slate-500">{{ number_format($bt->price_cdf ?? 0, 0, ',', ' ') }} FC</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($bt->available_durations ?? [] as $d)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">{{ $d }}j</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bt->is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-slate-100 text-slate-800 border border-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $bt->is_active ? 'bg-green-500' : 'bg-slate-500' }} mr-1.5"></span>
                                {{ $bt->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                            @if($bt->is_premium)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 ml-1">
                                    <i class="fas fa-crown mr-1"></i>Premium
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-primary-600">{{ $bt->product_boosts_count ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $bt->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-1 justify-end items-center">
                                <a href="{{ route('admin.boost-types.show', $bt) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors"
                                   title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.boost-types.edit', $bt) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary-600 hover:bg-primary-50 transition-colors"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.boost-types.update-status', $bt) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $bt->is_active ? '0' : '1' }}">
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $bt->is_active ? 'text-yellow-600 hover:bg-yellow-50' : 'text-green-600 hover:bg-green-50' }} transition-colors"
                                            title="{{ $bt->is_active ? 'Désactiver' : 'Activer' }}">
                                        <i class="fas fa-{{ $bt->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <button onclick="confirmDelete({{ $bt->id }}, '{{ $bt->display_name }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition-colors"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($boostTypes->hasPages())
        <div class="p-4 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                    Affichage de {{ $boostTypes->firstItem() }} à {{ $boostTypes->lastItem() }}
                    sur {{ $boostTypes->total() }} résultats
                </div>
                {{ $boostTypes->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 mb-4">
                <i class="fas fa-bolt text-3xl text-primary-600"></i>
            </div>
            <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Aucun type de boost</h5>
            <p class="text-slate-500 dark:text-slate-400 mb-4">Créez votre premier type de boost.</p>
            <a href="{{ route('admin.boost-types.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Nouveau type
            </a>
        </div>
        @endif
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog">
    <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-red-800 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Confirmer la suppression
                    </h3>
                    <button type="button" onclick="closeDeleteModal()" class="text-red-400 hover:text-red-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 px-6 py-4">
                <p class="text-slate-700 dark:text-slate-200 mb-4">
                    Êtes-vous sûr de vouloir supprimer <strong class="text-slate-900 dark:text-white" id="deleteItemName"></strong> ?
                </p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <p class="text-sm text-yellow-700">Cette action est irréversible.</p>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="inline-flex justify-center items-center px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 transition-colors w-full sm:w-auto">
                    Annuler
                </button>
                <form id="deleteForm" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex justify-center items-center w-full px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteForm').action = '{{ url('admin/boost-types') }}/' + id;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>
@endpush
