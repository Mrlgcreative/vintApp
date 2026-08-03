@extends('layouts.admin')

@section('title', 'Détails utilisateur - ' . $user->name)
@section('page-title', 'Détails de l\'utilisateur')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-arrow-left"></i>Retour
    </a>
    <a href="{{ route('admin.users.edit', $user) }}"
       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-pen"></i>Modifier
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Carte profil --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-primary-600 to-primary-500 h-24"></div>
            <div class="p-6 -mt-12 text-center">
                @if($user->avatar)
                    <img src="{{ $user->avatar_url }}" class="rounded-full mx-auto mb-4 w-24 h-24 object-cover ring-4 ring-white dark:ring-slate-800 shadow-md" alt="Avatar">
                @else
                    <div class="rounded-full bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center mx-auto mb-4 w-24 h-24 text-4xl font-bold ring-4 ring-white dark:ring-slate-800 shadow-md">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-1">{{ $user->name }}</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ $user->email }}</p>

                <div class="flex justify-center gap-2 mb-4 flex-wrap">
                    @foreach($user->roles as $role)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $role->slug === 'admin' ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-300' : 'bg-primary-50 text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-900/30 dark:text-primary-300' }}">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </div>

                <div class="flex justify-center gap-2 flex-wrap">
                    @if($user->is_active ?? true)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                            <i class="fas fa-circle text-[6px]"></i>Actif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">
                            <i class="fas fa-circle text-[6px]"></i>Inactif
                        </span>
                    @endif
                </div>

                @if($user->created_at)
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Membre depuis</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistiques & informations --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-sky-600 dark:text-sky-400">Articles</span>
                    <div class="w-9 h-9 bg-sky-100 dark:bg-sky-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-box text-sky-600 dark:text-sky-400"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $user->items()->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Achats</span>
                    <div class="w-9 h-9 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-cart-shopping text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $user->ordersAsBuyer()->count() }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                <i class="fas fa-circle-info text-primary-600"></i>
                <h5 class="font-semibold text-slate-900 dark:text-white">Informations</h5>
            </div>
            <div class="p-5 space-y-4 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400">ID utilisateur</span>
                    <span class="font-medium text-slate-900 dark:text-white">#{{ $user->id }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Adresse email</span>
                    <span class="font-medium text-slate-900 dark:text-white">{{ $user->email }}</span>
                </div>
                @if($user->email_verified_at)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Email vérifié</span>
                        <span class="inline-flex items-center gap-1.5 font-medium text-emerald-600 dark:text-emerald-400">
                            <i class="fas fa-circle-check"></i>{{ $user->email_verified_at->format('d/m/Y') }}
                        </span>
                    </div>
                @endif
                @if($user->phone)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Téléphone</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $user->phone }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
