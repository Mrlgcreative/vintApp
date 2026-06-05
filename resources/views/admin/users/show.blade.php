@extends('layouts.admin')

@section('title', 'Détails utilisateur - ' . $user->name)

@section('page-title', 'Détails de l\'utilisateur')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Retour
    </a>
    <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-edit mr-2"></i>Modifier
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 text-center">
                @if($user->avatar)
                    <img src="{{ $user->avatar_url }}" class="rounded-full mx-auto mb-4 w-32 h-32 object-cover" alt="Avatar">
                @else
                    <div class="rounded-full bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center mx-auto mb-4 w-32 h-32 text-5xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                
                <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $user->name }}</h4>
                <p class="text-gray-500 dark:text-gray-400 mb-4">{{ $user->email }}</p>
                
                <div class="flex justify-center gap-2 mb-4 flex-wrap">
                    @foreach($user->roles as $role)
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $role->slug === 'admin' ? 'bg-red-100 text-red-700' : 'bg-primary-100 text-primary-700' }}">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </div>
                
                <div class="flex justify-center gap-2 flex-wrap">
                    @if($user->is_active ?? true)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">Actif</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">Inactif</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-6 py-4">
                <h5 class="text-xl font-bold text-white">Informations</h5>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <i class="fas fa-box text-3xl text-blue-600 mb-2"></i>
                        <h4 class="text-2xl font-bold">{{ $user->items()->count() }}</h4>
                        <p class="text-sm">Articles</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <i class="fas fa-shopping-cart text-3xl text-green-600 mb-2"></i>
                        <h4 class="text-2xl font-bold">{{ $user->ordersAsBuyer()->count() }}</h4>
                        <p class="text-sm">Achats</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
