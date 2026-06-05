@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs')
@section('page-title', 'Gestion des utilisateurs')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.users.create') }}" 
       class="inline-flex items-center px-3 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-plus mr-1.5"></i>
        <span class="hidden sm:inline">Nouvel utilisateur</span>
        <span class="sm:hidden">Nouveau</span>
    </a>
    <a href="{{ route('admin.users.index', array_merge(request()->query(), ['export' => 'csv'])) }}" 
       class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-download mr-1.5"></i>
        <span class="hidden sm:inline">Exporter</span>
        <span class="sm:hidden">CSV</span>
    </a>
</div>
@endsection

@section('content')
{{-- Stats rapides --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4 sm:mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">{{ $users->total() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-circle text-green-500 text-xs"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">En ligne</p>
                <p class="text-lg sm:text-xl font-bold text-green-600">{{ $users->filter(fn($u) => $u->isOnline())->count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-purple-600 dark:text-purple-400"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Vérifiés</p>
                <p class="text-lg sm:text-xl font-bold text-purple-600">{{ $users->filter(fn($u) => $u->email_verified_at)->count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-shield-alt text-red-600 dark:text-red-400"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Admins</p>
                <p class="text-lg sm:text-xl font-bold text-red-600">{{ $users->filter(fn($u) => $u->roles->contains('slug', 'admin'))->count() }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filtres --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-4 sm:mb-6">
    <div class="p-3 sm:p-4">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                {{-- Recherche --}}
                <div class="col-span-2 sm:col-span-3 lg:col-span-2">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher nom, email..."
                               class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                </div>
                {{-- Rôle --}}
                <div>
                    <select name="role" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="">Tous les rôles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Utilisateur</option>
                        <option value="expert" {{ request('role') === 'expert' ? 'selected' : '' }}>Expert</option>
                        <option value="support" {{ request('role') === 'support' ? 'selected' : '' }}>Support</option>
                    </select>
                </div>
                {{-- Statut --}}
                <div>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif (7j)</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                {{-- Boutons --}}
                <div class="col-span-2 sm:col-span-1 lg:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-search mr-1.5"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
            {{-- Tags filtres actifs --}}
            @if(request()->hasAny(['search', 'role', 'status']))
                <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Filtres :</span>
                    @if(request('search'))
                        <a href="{{ route('admin.users.index', request()->except('search')) }}" class="inline-flex items-center gap-1 px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                            « {{ Str::limit(request('search'), 20) }} »
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif
                    @if(request('role'))
                        <a href="{{ route('admin.users.index', request()->except('role')) }}" class="inline-flex items-center gap-1 px-2 py-0.5 text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors">
                            Rôle : {{ ucfirst(request('role')) }}
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif
                    @if(request('status'))
                        <a href="{{ route('admin.users.index', request()->except('status')) }}" class="inline-flex items-center gap-1 px-2 py-0.5 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                            Statut : {{ request('status') === 'active' ? 'Actif' : 'Inactif' }}
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif
                </div>
            @endif
        </form>
    </div>
</div>

{{-- Tableau des utilisateurs --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-4 sm:px-6 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">
            {{ $users->total() }} utilisateur{{ $users->total() > 1 ? 's' : '' }}
        </h3>
        <span class="text-xs text-gray-500 dark:text-gray-400">
            Page {{ $users->currentPage() }}/{{ $users->lastPage() }}
        </span>
    </div>

    @if($users->count() > 0)
        {{-- Vue Desktop --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rôles</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Wallets</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Activité</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            {{-- User info --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar_url }}" class="w-9 h-9 rounded-full flex-shrink-0" alt="">
                                    @else
                                        <div class="w-9 h-9 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                            {{ $user->initial }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</div>
                                        <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="truncate">{{ $user->email }}</span>
                                            @if($user->email_verified_at)
                                                <i class="fas fa-check-circle text-green-500 flex-shrink-0" title="Vérifié"></i>
                                            @else
                                                <i class="fas fa-exclamation-circle text-yellow-500 flex-shrink-0" title="Non vérifié"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            {{-- Roles --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        @php
                                            $roleColors = [
                                                'admin' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                'expert' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                                'support' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400',
                                                'user' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$role->slug] ?? $roleColors['user'] }}">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            {{-- Wallets --}}
                            <td class="px-4 py-3 text-sm">
                                @if($user->usdWallet() || $user->cdfWallet())
                                    <div class="space-y-0.5 text-xs">
                                        @if($user->usdWallet())
                                            <div class="text-gray-700 dark:text-gray-300"><span class="font-medium text-green-600">$</span> {{ number_format($user->usdWallet()->balance, 2) }}</div>
                                        @endif
                                        @if($user->cdfWallet())
                                            <div class="text-gray-700 dark:text-gray-300"><span class="font-medium text-blue-600">CDF</span> {{ number_format($user->cdfWallet()->balance, 0) }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            {{-- Activity --}}
                            <td class="px-4 py-3">
                                @if($user->isOnline())
                                    <span class="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400 font-medium">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                        En ligne
                                    </span>
                                @elseif($user->last_seen)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $user->last_seen->diffForHumans() }}</span>
                                @else
                                    <span class="text-xs text-gray-400">Jamais</span>
                                @endif
                            </td>
                            {{-- Status --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @if($user->is_suspended ?? false)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">Suspendu</span>
                                    @elseif($user->is_active ?? true)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Actif</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Inactif</span>
                                    @endif
                                </div>
                            </td>
                            {{-- Actions --}}
                            <td class="px-4 py-3 text-right">
                                <div class="relative inline-block" x-data="{ open: false }">
                                    <button @click="open = !open" @click.outside="open = false"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                        Actions <i class="fas fa-chevron-down text-[10px]"></i>
                                    </button>
                                    <div x-show="open" x-transition.opacity class="origin-top-right absolute right-0 mt-1 w-44 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black/5 dark:ring-white/10 z-30" style="display:none">
                                        <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <i class="fas fa-eye w-4 text-center text-gray-400"></i>Voir
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <i class="fas fa-edit w-4 text-center text-gray-400"></i>Modifier
                                        </a>
                                        <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                        @if($user->is_active ?? true)
                                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="deactivate">
                                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-yellow-700 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20"
                                                        onclick="return confirm('Désactiver cet utilisateur ?')">
                                                    <i class="fas fa-pause w-4 text-center"></i>Désactiver
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="activate">
                                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20">
                                                    <i class="fas fa-play w-4 text-center"></i>Activer
                                                </button>
                                            </form>
                                        @endif
                                        @if(!($user->is_suspended ?? false))
                                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="suspend">
                                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-orange-700 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20"
                                                        onclick="return confirm('Suspendre cet utilisateur ?')">
                                                    <i class="fas fa-ban w-4 text-center"></i>Suspendre
                                                </button>
                                            </form>
                                        @endif
                                        <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                        <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                                                    onclick="return confirm('Supprimer définitivement cet utilisateur ?')">
                                                <i class="fas fa-trash w-4 text-center"></i>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Vue Mobile/Tablet --}}
        <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($users as $user)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-3">
                        <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3 flex-1 min-w-0">
                            @if($user->avatar)
                                <img src="{{ $user->avatar_url }}" class="w-11 h-11 rounded-full flex-shrink-0" alt="">
                            @else
                                <div class="w-11 h-11 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                    {{ $user->initial }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $user->name }}</h4>
                                <div class="flex items-center gap-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                                    @if($user->email_verified_at)
                                        <i class="fas fa-check-circle text-green-500 text-[10px] flex-shrink-0"></i>
                                    @endif
                                </div>
                            </div>
                        </a>
                        <button @click="open = !open" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
                            <i class="fas fa-ellipsis-v text-gray-400"></i>
                        </button>
                    </div>

                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach($user->roles as $role)
                            @php
                                $roleColors = [
                                    'admin' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'expert' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                    'support' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400',
                                    'user' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $roleColors[$role->slug] ?? $roleColors['user'] }}">{{ $role->name }}</span>
                        @endforeach
                        @if($user->is_suspended ?? false)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">Suspendu</span>
                        @elseif($user->is_active ?? true)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Actif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Inactif</span>
                        @endif
                        @if($user->isOnline())
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span>En ligne
                            </span>
                        @endif
                    </div>

                    {{-- Info ligne --}}
                    <div class="flex items-center justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-3">
                            @if($user->usdWallet())
                                <span><span class="font-medium text-green-600">$</span>{{ number_format($user->usdWallet()->balance, 2) }}</span>
                            @endif
                            @if($user->cdfWallet())
                                <span><span class="font-medium text-blue-600">CDF</span> {{ number_format($user->cdfWallet()->balance, 0) }}</span>
                            @endif
                        </div>
                        <span>
                            @if($user->last_seen)
                                <i class="fas fa-clock mr-0.5"></i>{{ $user->last_seen->diffForHumans() }}
                            @else
                                Jamais connecté
                            @endif
                        </span>
                    </div>

                    {{-- Dropdown mobile --}}
                    <div x-show="open" x-transition @click.outside="open = false"
                         class="mt-2 py-1 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-sm" style="display:none">
                        <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-eye w-4 text-center text-gray-400"></i>Voir détails
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-edit w-4 text-center text-gray-400"></i>Modifier
                        </a>
                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                        @if($user->is_active ?? true)
                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="deactivate">
                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-yellow-700 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20"
                                        onclick="return confirm('Désactiver cet utilisateur ?')">
                                    <i class="fas fa-pause w-4 text-center"></i>Désactiver
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="activate">
                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20">
                                    <i class="fas fa-play w-4 text-center"></i>Activer
                                </button>
                            </form>
                        @endif
                        @if(!($user->is_suspended ?? false))
                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="suspend">
                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-orange-700 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20"
                                        onclick="return confirm('Suspendre cet utilisateur ?')">
                                    <i class="fas fa-ban w-4 text-center"></i>Suspendre
                                </button>
                            </form>
                        @endif
                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                        <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                                    onclick="return confirm('Supprimer définitivement ?')">
                                <i class="fas fa-trash w-4 text-center"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <i class="fas fa-users text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-1">Aucun utilisateur trouvé</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Essayez de modifier vos filtres.</p>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 mt-3 text-sm text-primary-600 hover:text-primary-700">
                <i class="fas fa-arrow-left text-xs"></i>Réinitialiser les filtres
            </a>
        </div>
    @endif

    @if($users->hasPages())
        <div class="px-4 sm:px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select[name="role"], select[name="status"]').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush