@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs')
@section('page-title', 'Gestion des utilisateurs')

@section('page-actions')
<div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
    <a href="{{ route('admin.users.create') }}" 
       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
        <i class="fas fa-plus mr-2"></i>
        <span class="hidden sm:inline">Nouvel utilisateur</span>
        <span class="sm:hidden">Nouveau</span>
    </a>
    <button class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200" 
            onclick="toggleBulkActions()">
        <i class="fas fa-tasks mr-2"></i>
        <span class="hidden sm:inline">Actions groupées</span>
        <span class="sm:hidden">Actions</span>
    </button>
    <a href="{{ route('admin.users.index', ['export' => 'csv']) }}" 
       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
        <i class="fas fa-download mr-2"></i>
        <span class="hidden sm:inline">Exporter CSV</span>
        <span class="sm:hidden">Export</span>
    </a>
</div>
@endsection

@section('content')
<!-- Filtres -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4 sm:mb-6">
    <div class="p-4 sm:p-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
            <!-- Ligne 1: Recherche -->
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm sm:text-base" 
                           id="search" name="search" value="{{ request('search') }}" placeholder="Nom ou email...">
                </div>
            </div>
            
            <!-- Ligne 2: Filtres -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm sm:text-base" 
                            id="role" name="role">
                        <option value="">Tous les rôles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Utilisateur</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm sm:text-base" 
                            id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                
                <!-- Boutons -->
                <div class="flex items-end">
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full">
                        <button type="submit" 
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        <a href="{{ route('admin.users.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tableau des utilisateurs -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900">
            Utilisateurs ({{ $users->total() }})
        </h3>
    </div>
    <div>
        @if($users->count() > 0)
            <!-- Vue Desktop (Table) - Cachée sur mobile et tablet -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wallets</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière connexion</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($user->avatar)
                                            <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full mr-4" alt="Avatar">
                                        @else
                                            <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-4">
                                                {{ $user->initial }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-900">{{ $user->email }}</span>
                                        @if($user->email_verified_at)
                                            <i class="fas fa-check-circle text-green-500 ml-2" title="Email vérifié"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle text-yellow-500 ml-2" title="Email non vérifié"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $role->slug === 'admin' ? 'bg-red-100 text-red-800' : 'bg-primary-100 text-primary-800' }}">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="space-y-1">
                                        @if($user->usdWallet())
                                            <div>USD: {{ number_format($user->usdWallet()->balance, 2) }}</div>
                                        @endif
                                        @if($user->cdfWallet())
                                            <div>CDF: {{ number_format($user->cdfWallet()->balance, 0) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($user->last_seen)
                                        <div class="text-sm text-gray-900">{{ $user->last_seen->diffForHumans() }}</div>
                                        @if($user->isOnline())
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                En ligne
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-500">Jamais connecté</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        @if($user->is_active ?? true)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Actif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Inactif
                                            </span>
                                        @endif
                                        
                                        @if($user->is_suspended ?? false)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Suspendu
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="relative">
                                        <button class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500" 
                                                type="button" onclick="toggleDropdown('user-desktop-{{ $user->id }}-dropdown')">
                                            Actions
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>
                                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10" 
                                             id="user-desktop-{{ $user->id }}-dropdown">
                                            <div class="py-1">
                                                <a href="{{ route('admin.users.show', $user) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-eye mr-3 w-4"></i>Voir détails
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-edit mr-3 w-4"></i>Modifier
                                                </a>
                                                <div class="border-t border-gray-100"></div>
                                                @if($user->is_active ?? true)
                                                    <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="action" value="deactivate">
                                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50" 
                                                                onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?')">
                                                            <i class="fas fa-pause mr-3 w-4"></i>Désactiver
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="action" value="activate">
                                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                                            <i class="fas fa-play mr-3 w-4"></i>Activer
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if(!($user->is_suspended ?? false))
                                                    <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="action" value="suspend">
                                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50" 
                                                                onclick="return confirm('Êtes-vous sûr de vouloir suspendre cet utilisateur ?')">
                                                            <i class="fas fa-ban mr-3 w-4"></i>Suspendre
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <div class="border-t border-gray-100"></div>
                                                <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                                                        <i class="fas fa-trash mr-3 w-4"></i>Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Vue Mobile/Tablet (Cards) - Cachée sur desktop -->
            <div class="lg:hidden divide-y divide-gray-200">
                @foreach($users as $user)
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                        <!-- En-tête utilisateur -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar_url }}" class="w-12 h-12 rounded-full flex-shrink-0" alt="Avatar">
                                @else
                                    <div class="w-12 h-12 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                        {{ $user->initial }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</h4>
                                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                    <p class="text-xs text-gray-400">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                            
                            <!-- Menu actions mobile -->
                            <div class="relative ml-2 flex-shrink-0">
                                <button onclick="toggleMobileDropdown({{ $user->id }})" 
                                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-ellipsis-v text-gray-500"></i>
                                </button>
                                <div id="mobile-dropdown-{{ $user->id }}" 
                                     class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
                                    <div class="py-1">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-eye mr-3 w-4"></i>Voir détails
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-edit mr-3 w-4"></i>Modifier
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        @if($user->is_active ?? true)
                                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="deactivate">
                                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50" 
                                                        onclick="return confirm('Désactiver cet utilisateur ?')">
                                                    <i class="fas fa-pause mr-3 w-4"></i>Désactiver
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="activate">
                                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                                    <i class="fas fa-play mr-3 w-4"></i>Activer
                                                </button>
                                            </form>
                                        @endif
                                        @if(!($user->is_suspended ?? false))
                                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="suspend">
                                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50" 
                                                        onclick="return confirm('Suspendre cet utilisateur ?')">
                                                    <i class="fas fa-ban mr-3 w-4"></i>Suspendre
                                                </button>
                                            </form>
                                        @endif
                                        <div class="border-t border-gray-100"></div>
                                        <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50" 
                                                    onclick="return confirm('Supprimer cet utilisateur ? Action irréversible.')">
                                                <i class="fas fa-trash mr-3 w-4"></i>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Badges et informations -->
                        <div class="space-y-2">
                            <!-- Rôles -->
                            <div class="flex flex-wrap gap-1">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $role->slug === 'admin' ? 'bg-red-100 text-red-800' : 'bg-primary-100 text-primary-800' }}">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                                
                                @if($user->is_active ?? true)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactif
                                    </span>
                                @endif
                                
                                @if($user->is_suspended ?? false)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Suspendu
                                    </span>
                                @endif
                                
                                @if($user->isOnline())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1 animate-pulse"></span>
                                        En ligne
                                    </span>
                                @endif
                                
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-check-circle mr-1"></i>Email vérifié
                                    </span>
                                @endif
                            </div>

                            <!-- Wallets -->
                            @if($user->usdWallet() || $user->cdfWallet())
                                <div class="flex gap-3 text-xs">
                                    @if($user->usdWallet())
                                        <div class="flex items-center">
                                            <i class="fas fa-wallet text-green-600 mr-1"></i>
                                            <span class="font-medium">USD:</span>
                                            <span class="ml-1">{{ number_format($user->usdWallet()->balance, 2) }}</span>
                                        </div>
                                    @endif
                                    @if($user->cdfWallet())
                                        <div class="flex items-center">
                                            <i class="fas fa-wallet text-blue-600 mr-1"></i>
                                            <span class="font-medium">CDF:</span>
                                            <span class="ml-1">{{ number_format($user->cdfWallet()->balance, 0) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Dernière connexion -->
                            <div class="text-xs text-gray-500">
                                @if($user->last_seen)
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $user->last_seen->diffForHumans() }}
                                @else
                                    <i class="fas fa-clock mr-1"></i>
                                    Jamais connecté
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">Aucun utilisateur trouvé</h3>
                <p class="text-sm text-gray-500">Aucun utilisateur ne correspond aux critères de recherche.</p>
            </div>
        @endif
    </div>
    
    @if($users->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on select change
    document.querySelectorAll('select[name="role"], select[name="status"]').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});

// Dropdown toggle function pour desktop
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id$="-dropdown"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(el => {
        if (el.id !== dropdownId) {
            el.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Toggle dropdown mobile pour les actions
function toggleMobileDropdown(userId) {
    const dropdownId = 'mobile-dropdown-' + userId;
    const dropdown = document.getElementById(dropdownId);
    const allMobileDropdowns = document.querySelectorAll('[id^="mobile-dropdown-"]');
    
    // Fermer tous les autres dropdowns mobiles
    allMobileDropdowns.forEach(el => {
        if (el.id !== dropdownId) {
            el.classList.add('hidden');
        }
    });
    
    // Toggle le dropdown actuel
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleDropdown"]') && 
        !event.target.closest('[onclick*="toggleMobileDropdown"]')) {
        document.querySelectorAll('[id$="-dropdown"], [id^="mobile-dropdown-"]').forEach(el => {
            el.classList.add('hidden');
        });
    }
});

// Bulk actions toggle
function toggleBulkActions() {
    // Implementation for bulk actions modal/panel
    alert('Fonctionnalité des actions groupées à implémenter');
}
</script>
@endpush