@extends('app')

@section('title', 'Vérification VintPass - ' . $vintPass->pass_id)

@push('styles')
<style>
    .vintpass-card {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        border-radius: 24px;
        overflow: hidden;
        position: relative;
    }
    
    .vintpass-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #f59e0b, #eab308, #fbbf24);
    }
    
    .score-ring {
        position: relative;
        width: 120px;
        height: 120px;
    }
    
    .score-ring svg {
        transform: rotate(-90deg);
    }
    
    .score-ring .progress {
        stroke-dasharray: 339.292;
        stroke-dashoffset: calc(339.292 - (339.292 * var(--score)) / 100);
        transition: stroke-dashoffset 1s ease-out;
    }
    
    .badge-platinum { background: linear-gradient(135deg, #a855f7, #7c3aed); }
    .badge-gold { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .badge-silver { background: linear-gradient(135deg, #9ca3af, #6b7280); }
    .badge-bronze { background: linear-gradient(135deg, #f97316, #ea580c); }
    .badge-basic { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    
    .blockchain-hash {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        background: rgba(0,0,0,0.3);
        padding: 8px 12px;
        border-radius: 8px;
        word-break: break-all;
    }
    
    .pulse-valid {
        animation: pulse-green 2s infinite;
    }
    
    @keyframes pulse-green {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        50% { box-shadow: 0 0 0 15px rgba(34, 197, 94, 0); }
    }
    
    .history-timeline {
        position: relative;
        padding-left: 24px;
    }
    
    .history-timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
    }
    
    .history-item::before {
        content: '';
        position: absolute;
        left: -20px;
        top: 6px;
        width: 12px;
        height: 12px;
        background: #3b82f6;
        border-radius: 50%;
        border: 2px solid #1a1a2e;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-8 px-4">
    <div class="max-w-2xl mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full mb-4">
                <span class="text-2xl">🏆</span>
                <span class="text-white font-semibold">VintPass™</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Certificat d'Authenticité</h1>
            <p class="text-gray-400">Vérification blockchain immuable</p>
        </div>

        <!-- Status Banner -->
        @if($isValid)
        <div class="bg-green-500/20 border border-green-500/50 rounded-2xl p-4 mb-6 flex items-center gap-4 pulse-valid">
            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h3 class="text-green-400 font-bold text-lg">AUTHENTIQUE</h3>
                <p class="text-green-300/80 text-sm">Ce VintPass est valide et vérifié</p>
            </div>
        </div>
        @else
        <div class="bg-red-500/20 border border-red-500/50 rounded-2xl p-4 mb-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <h3 class="text-red-400 font-bold text-lg">{{ strtoupper($data['status']['label']) }}</h3>
                <p class="text-red-300/80 text-sm">Ce VintPass n'est pas actif</p>
            </div>
        </div>
        @endif

        <!-- Main Card -->
        <div class="vintpass-card shadow-2xl mb-6">
            <!-- Pass ID Header -->
            <div class="p-6 border-b border-white/10">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">ID du Certificat</p>
                        <p class="text-white font-mono text-xl font-bold">{{ $data['pass_id'] }}</p>
                    </div>
                    <div class="badge-{{ $data['authenticity_level']['level'] }} px-4 py-2 rounded-full">
                        <span class="text-white font-semibold">
                            {{ $data['authenticity_level']['icon'] }} {{ $data['authenticity_level']['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Item Info -->
            <div class="p-6 border-b border-white/10">
                <div class="flex gap-4">
                    @if($data['item']['image'])
                    <img src="{{ $data['item']['image'] }}" 
                         alt="{{ $data['item']['name'] }}"
                         class="w-24 h-24 object-cover rounded-xl">
                    @endif
                    <div class="flex-1">
                        <h2 class="text-white font-bold text-lg mb-1">{{ $data['item']['name'] }}</h2>
                        @if($data['item']['brand'])
                        <p class="text-blue-400 font-medium mb-1">{{ $data['item']['brand'] }}</p>
                        @endif
                        @if($data['item']['category'])
                        <p class="text-gray-400 text-sm">{{ $data['item']['category'] }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Score Section -->
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center justify-center gap-8">
                    <!-- Score Ring -->
                    <div class="score-ring" style="--score: {{ $data['final_score'] }}">
                        <svg class="w-full h-full" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="54" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="8"/>
                            <circle cx="60" cy="60" r="54" fill="none" stroke="#22c55e" stroke-width="8" 
                                    class="progress" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <span class="text-3xl font-bold text-white">{{ number_format($data['final_score'], 1) }}</span>
                                <span class="text-gray-400 text-sm block">/ 100</span>
                            </div>
                        </div>
                    </div>

                    <!-- Score Details -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🤖</span>
                            <div>
                                <p class="text-gray-400 text-xs">Score IA</p>
                                <p class="text-white font-semibold">{{ $data['verification']['ai_score'] ?? 'N/A' }}%</p>
                            </div>
                        </div>
                        @if($data['verification']['expert_score'])
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">👨‍💼</span>
                            <div>
                                <p class="text-gray-400 text-xs">Score Expert</p>
                                <p class="text-white font-semibold">{{ $data['verification']['expert_score'] }}%</p>
                            </div>
                        </div>
                        @endif
                        @if($data['verification']['expert_name'])
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="text-gray-400 text-xs">Vérifié par</p>
                                <p class="text-white font-semibold">{{ $data['verification']['expert_name'] }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Blockchain Section -->
            @if($data['blockchain']['hash'])
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">🔗</span>
                    <h3 class="text-white font-semibold">Blockchain</h3>
                    @if($data['blockchain']['confirmed'])
                    <span class="bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded-full">Confirmé</span>
                    @endif
                </div>
                <div class="blockchain-hash text-gray-300">
                    {{ $data['blockchain']['hash'] }}
                </div>
                <p class="text-gray-500 text-xs mt-2">
                    Réseau: {{ ucfirst($data['blockchain']['network']) }}
                </p>
            </div>
            @endif

            <!-- Ownership Info -->
            <div class="p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xl">📜</span>
                    <h3 class="text-white font-semibold">Historique de Propriété</h3>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="bg-white/5 rounded-xl p-4">
                        <p class="text-2xl font-bold text-white">{{ $data['ownership']['transfer_count'] }}</p>
                        <p class="text-gray-400 text-xs">Transferts</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <p class="text-2xl font-bold text-white">{{ $data['stats']['scan_count'] }}</p>
                        <p class="text-gray-400 text-xs">Scans</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <p class="text-2xl font-bold text-blue-400">{{ $data['ownership']['history_count'] }}</p>
                        <p class="text-gray-400 text-xs">Propriétaires</p>
                    </div>
                </div>
                
                @if($data['ownership']['current_owner'])
                <div class="mt-4 bg-white/5 rounded-xl p-4">
                    <p class="text-gray-400 text-xs mb-1">Propriétaire actuel</p>
                    <p class="text-white font-semibold">{{ $data['ownership']['current_owner'] }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-gray-500 text-sm mb-4">
                Émis le {{ $data['issued_at'] }}
            </p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('home') }}" 
                   class="bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-full transition-all">
                    Visiter VintApp
                </a>
                @auth
                <a href="{{ route('items.index') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full transition-all">
                    Explorer les Articles
                </a>
                @endauth
            </div>
        </div>

        <!-- Trust Badges -->
        <div class="mt-8 flex justify-center gap-6 text-gray-500">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xs">Sécurisé</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                <span class="text-xs">Blockchain</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span class="text-xs">Immuable</span>
            </div>
        </div>
    </div>
</div>
@endsection
