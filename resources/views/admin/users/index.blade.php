@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs')
@section('page-title', 'Gestion des utilisateurs')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-plus"></i>
        <span class="hidden sm:inline">Nouvel utilisateur</span>
        <span class="sm:hidden">Nouveau</span>
    </a>
    <a href="{{ route('admin.users.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-download"></i>
        <span class="hidden sm:inline">Exporter</span>
        <span class="sm:hidden">CSV</span>
    </a>
</div>
@endsection

@section('content')
{{-- Stats rapides --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4 sm:mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-900/30 flex-shrink-0">
                <i class="fas fa-users text-sky-600 dark:text-sky-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Total</p>
                <p class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">{{ $users->total() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex-shrink-0">
                <i class="fas fa-circle text-emerald-500 text-xs"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">En ligne</p>
                <p class="text-lg sm:text-xl font-bold text-emerald-600">{{ $users->filter(fn($u) => $u->isOnline())->count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-900/30 flex-shrink-0">
                <i class="fas fa-check-circle text-violet-600 dark:text-violet-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Vérifiés</p>
                <p class="text-lg sm:text-xl font-bold text-violet-600">{{ $users->filter(fn($u) => $u->email_verified_at)->count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/30 flex-shrink-0">
                <i class="fas fa-shield-halved text-red-600 dark:text-red-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Admins</p>
                <p class="text-lg sm:text-xl font-bold text-red-600">{{ $users->filter(fn($u) => $u->roles->contains('slug', 'admin'))->count() }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filtres --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm mb-4 sm:mb-6">
    <div class="p-3 sm:p-4">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                {{-- Recherche --}}
                <div class="col-span-2 sm:col-span-3 lg:col-span-2">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher nom, email..."
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 py-2.5 pl-10 pr-3.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    </div>
                </div>
                {{-- Rôle --}}
                <div>
                    <select name="role" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                        <option value="">Tous les rôles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Utilisateur</option>
                        <option value="expert" {{ request('role') === 'expert' ? 'selected' : '' }}>Expert</option>
                        <option value="support" {{ request('role') === 'support' ? 'selected' : '' }}>Support</option>
                    </select>
                </div>
                {{-- Statut --}}
                <div>
                    <select name="status" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif (7j)</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                {{-- Boutons --}}
                <div class="col-span-2 sm:col-span-1 lg:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-3 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                        <i class="fas fa-search"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center px-3.5 py-2.5 rounded-xl text-sm text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
            {{-- Tags filtres actifs --}}
            @if(request()->hasAny(['search', 'role', 'status']))
                <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Filtres :</span>
                    @if(request('search'))
                        <a href="{{ route('admin.users.index', request()->except('search')) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-full bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300 transition-colors hover:bg-sky-100 dark:hover:bg-sky-900/50">
                            « {{ Str::limit(request('search'), 20) }} »
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif
                    @if(request('role'))
                        <a href="{{ route('admin.users.index', request()->except('role')) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-full bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-600/20 dark:bg-violet-900/30 dark:text-violet-300 transition-colors hover:bg-violet-100 dark:hover:bg-violet-900/50">
                            Rôle : {{ ucfirst(request('role')) }}
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif
                    @if(request('status'))
                        <a href="{{ route('admin.users.index', request()->except('status')) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300 transition-colors hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
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
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-4 sm:px-6 py-3.5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
        <h3 class="text-sm sm:text-base font-semibold text-slate-900 dark:text-white">
            {{ $users->total() }} utilisateur{{ $users->total() > 1 ? 's' : '' }}
        </h3>
        <span class="text-xs text-slate-500 dark:text-slate-400">
            Page {{ $users->currentPage() }}/{{ $users->lastPage() }}
        </span>
    </div>

    @if($users->count() > 0)
        {{-- Vue Desktop --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Utilisateur</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rôles</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Wallets</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Activité</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="border-t border-slate-100 dark:border-slate-700/50 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/30">
                            {{-- User info --}}
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar_url }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600 flex-shrink-0" alt="">
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400 text-sm font-semibold text-white flex-shrink-0">
                                            {{ $user->initial }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $user->name }}</div>
                                        <div class="flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400">
                                            <span class="truncate">{{ $user->email }}</span>
                                            @if($user->email_verified_at)
                                                <i class="fas fa-check-circle text-emerald-500 flex-shrink-0" title="Vérifié"></i>
                                            @else
                                                <i class="fas fa-circle-exclamation text-amber-500 flex-shrink-0" title="Non vérifié"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            {{-- Roles --}}
                            <td class="px-4 py-3 align-middle">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        @php
                                            $roleColors = [
                                                'admin' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300',
                                                'expert' => 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-900/30 dark:text-violet-300',
                                                'support' => 'bg-teal-50 text-teal-700 ring-teal-600/20 dark:bg-teal-900/30 dark:text-teal-300',
                                                'user' => 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $roleColors[$role->slug] ?? $roleColors['user'] }}">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            {{-- Wallets --}}
                            <td class="px-4 py-3 align-middle text-sm">
                                @if($user->usdWallet() || $user->cdfWallet())
                                    <div class="space-y-0.5 text-xs">
                                        @if($user->usdWallet())
                                            <div class="text-slate-700 dark:text-slate-300"><span class="font-medium text-emerald-600">$</span> {{ number_format($user->usdWallet()->balance, 2) }}</div>
                                        @endif
                                        @if($user->cdfWallet())
                                            <div class="text-slate-700 dark:text-slate-300"><span class="font-medium text-sky-600">CDF</span> {{ number_format($user->cdfWallet()->balance, 0) }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            {{-- Activity --}}
                            <td class="px-4 py-3 align-middle">
                                @if($user->isOnline())
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                        <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                        En ligne
                                    </span>
                                @elseif($user->last_seen)
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $user->last_seen->diffForHumans() }}</span>
                                @else
                                    <span class="text-xs text-slate-400">Jamais</span>
                                @endif
                            </td>
                            {{-- Status --}}
                            <td class="px-4 py-3 align-middle">
                                <div class="flex flex-wrap gap-1">
                                    @if($user->is_suspended ?? false)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">Suspendu</span>
                                    @elseif($user->is_active ?? true)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">Actif</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">Inactif</span>
                                    @endif
                                </div>
                            </td>
                            {{-- Actions --}}
                            <td class="px-4 py-3 align-middle text-right">
                                <div class="relative inline-block" x-data="{ open: false }">
                                    <button @click="open = !open" @click.outside="open = false"
                                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        Actions <i class="fas fa-chevron-down text-[10px]"></i>
                                    </button>
                                    <div x-show="open" x-transition.opacity class="origin-top-right absolute right-0 mt-1 w-44 rounded-xl shadow-lg bg-white dark:bg-slate-800 ring-1 ring-slate-200 dark:ring-slate-700 z-30" style="display:none">
                                        <div class="p-1">
                                            <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">
                                                <i class="fas fa-eye w-4 text-center text-slate-400"></i>Voir
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">
                                                <i class="fas fa-edit w-4 text-center text-slate-400"></i>Modifier
                                            </a>
                                            <div class="my-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                                            @if($user->is_active ?? true)
                                                <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="deactivate">
                                                    <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg"
                                                            onclick="return confirm('Désactiver cet utilisateur ?')">
                                                        <i class="fas fa-pause w-4 text-center"></i>Désactiver
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="activate">
                                                    <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg">
                                                        <i class="fas fa-play w-4 text-center"></i>Activer
                                                    </button>
                                                </form>
                                            @endif
                                            @if(!($user->is_suspended ?? false))
                                                <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="suspend">
                                                    <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-orange-700 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg"
                                                            onclick="return confirm('Suspendre cet utilisateur ?')">
                                                        <i class="fas fa-ban w-4 text-center"></i>Suspendre
                                                    </button>
                                                </form>
                                            @endif
                                            <div class="my-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg"
                                                        onclick="return confirm('Supprimer définitivement cet utilisateur ?')">
                                                    <i class="fas fa-trash w-4 text-center"></i>Supprimer
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

        {{-- Vue Mobile/Tablet --}}
        <div class="lg:hidden divide-y divide-slate-100 dark:divide-slate-700/50">
            @foreach($users as $user)
                <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-3">
                        <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3 flex-1 min-w-0">
                            @if($user->avatar)
                                <img src="{{ $user->avatar_url }}" class="h-11 w-11 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600 flex-shrink-0" alt="">
                            @else
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400 text-sm font-semibold text-white flex-shrink-0">
                                    {{ $user->initial }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $user->name }}</h4>
                                <div class="flex items-center gap-1">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
                                    @if($user->email_verified_at)
                                        <i class="fas fa-check-circle text-emerald-500 text-[10px] flex-shrink-0"></i>
                                    @endif
                                </div>
                            </div>
                        </a>
                        <button @click="open = !open" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors flex-shrink-0">
                            <i class="fas fa-ellipsis-v text-slate-400"></i>
                        </button>
                    </div>

                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach($user->roles as $role)
                            @php
                                $roleColors = [
                                    'admin' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300',
                                    'expert' => 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-900/30 dark:text-violet-300',
                                    'support' => 'bg-teal-50 text-teal-700 ring-teal-600/20 dark:bg-teal-900/30 dark:text-teal-300',
                                    'user' => 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-medium ring-1 ring-inset {{ $roleColors[$role->slug] ?? $roleColors['user'] }}">{{ $role->name }}</span>
                        @endforeach
                        @if($user->is_suspended ?? false)
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">Suspendu</span>
                        @elseif($user->is_active ?? true)
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">Actif</span>
                        @else
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">Inactif</span>
                        @endif
                        @if($user->isOnline())
                            <span class="inline-flex items-center gap-0.5 rounded-full px-2.5 py-0.5 text-[10px] font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                <span class="h-1 w-1 bg-emerald-500 rounded-full animate-pulse"></span>En ligne
                            </span>
                        @endif
                    </div>

                    {{-- Info ligne --}}
                    <div class="flex items-center justify-between mt-2 text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-3">
                            @if($user->usdWallet())
                                <span><span class="font-medium text-emerald-600">$</span>{{ number_format($user->usdWallet()->balance, 2) }}</span>
                            @endif
                            @if($user->cdfWallet())
                                <span><span class="font-medium text-sky-600">CDF</span> {{ number_format($user->cdfWallet()->balance, 0) }}</span>
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
                         class="mt-2 p-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm" style="display:none">
                        <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">
                            <i class="fas fa-eye w-4 text-center text-slate-400"></i>Voir détails
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">
                            <i class="fas fa-edit w-4 text-center text-slate-400"></i>Modifier
                        </a>
                        <div class="my-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                        @if($user->is_active ?? true)
                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="deactivate">
                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg"
                                        onclick="return confirm('Désactiver cet utilisateur ?')">
                                    <i class="fas fa-pause w-4 text-center"></i>Désactiver
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="activate">
                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg">
                                    <i class="fas fa-play w-4 text-center"></i>Activer
                                </button>
                            </form>
                        @endif
                        @if(!($user->is_suspended ?? false))
                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="suspend">
                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-orange-700 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg"
                                        onclick="return confirm('Suspendre cet utilisateur ?')">
                                    <i class="fas fa-ban w-4 text-center"></i>Suspendre
                                </button>
                            </form>
                        @endif
                        <div class="my-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                        <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg"
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
            <i class="fas fa-users text-4xl text-slate-200 dark:text-slate-600 mb-3"></i>
            <h3 class="text-base font-medium text-slate-900 dark:text-white mb-1">Aucun utilisateur trouvé</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Essayez de modifier vos filtres.</p>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 mt-3 text-sm text-primary-600 hover:text-primary-700">
                <i class="fas fa-arrow-left text-xs"></i>Réinitialiser les filtres
            </a>
        </div>
    @endif

    @if($users->hasPages())
        <div class="px-4 sm:px-6 py-3 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900">
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
