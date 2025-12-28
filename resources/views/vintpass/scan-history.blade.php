@extends('app')

@section('title', 'Historique des Scans - ' . $vintPass->pass_id)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Link -->
        <a href="{{ route('vintpass.show', $vintPass) }}" 
           class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour au VintPass
        </a>

        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                    <span class="text-3xl">🔍</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Historique des Scans</h1>
                    <p class="text-gray-500 dark:text-gray-400">VintPass: {{ $vintPass->pass_id }}</p>
                </div>
                <div class="ml-auto text-right">
                    <p class="text-3xl font-bold text-blue-600">{{ $scans->total() }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Scans totaux</p>
                </div>
            </div>
        </div>

        <!-- Scans List -->
        @if($scans->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($scans as $scan)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <!-- Icon -->
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0
                            {{ $scan->is_verified ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-gray-700' }}">
                            @if($scan->is_verified)
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            @else
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    @if($scan->user)
                                    {{ $scan->user->name }}
                                    @else
                                    Visiteur anonyme
                                    @endif
                                </p>
                                @if($scan->is_verified)
                                <span class="text-xs bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 px-2 py-0.5 rounded-full">
                                    Vérifié ✓
                                </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <span>{{ $scan->created_at->format('d/m/Y à H:i') }}</span>
                                @if($scan->ip_address)
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                    {{ $scan->ip_address }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="text-right hidden sm:block">
                            @if($scan->location)
                            <p class="text-gray-900 dark:text-white">{{ $scan->location }}</p>
                            @endif
                            @if($scan->device_type)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ ucfirst($scan->device_type) }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $scans->links() }}
        </div>
        
        @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center shadow-lg">
            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-4xl">🔍</span>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Aucun scan</h2>
            <p class="text-gray-500 dark:text-gray-400">
                Ce VintPass n'a pas encore été scanné. Partagez le QR code pour permettre la vérification.
            </p>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center shadow">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $scans->where('is_verified', true)->count() }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Scans vérifiés</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center shadow">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $scans->unique('ip_address')->count() }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">IPs uniques</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center shadow">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $scans->whereNotNull('user_id')->count() }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Utilisateurs</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center shadow">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $scans->where('user_id', null)->count() }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Anonymes</p>
            </div>
        </div>
    </div>
</div>
@endsection
