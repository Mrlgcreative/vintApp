@extends('app')

@section('title', 'VintPass - ' . $vintPass->pass_id)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Link -->
        <a href="{{ route('vintpass.index') }}" 
           class="inline-flex items-center gap-2 text-gray-400 hover:text-white mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à mes VintPass
        </a>

        <!-- Main Card -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl shadow-2xl overflow-hidden border border-gray-700">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-6 relative overflow-hidden">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                
                <div class="relative flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-3xl">🏆</span>
                            <span class="text-white/60 text-sm uppercase tracking-wider">VintPass Officiel</span>
                        </div>
                        <h1 class="text-3xl font-bold text-white font-mono">{{ $vintPass->pass_id }}</h1>
                    </div>
                    
                    @php
                        $level = $vintPass->authenticity_level;
                        $badgeClass = match($level['level']) {
                            'platinum' => 'bg-gradient-to-r from-purple-400 to-purple-600',
                            'gold' => 'bg-gradient-to-r from-yellow-400 to-yellow-600',
                            'silver' => 'bg-gradient-to-r from-gray-300 to-gray-500',
                            'bronze' => 'bg-gradient-to-r from-orange-400 to-orange-600',
                            default => 'bg-gradient-to-r from-blue-400 to-blue-600',
                        };
                    @endphp
                    <span class="{{ $badgeClass }} text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                        {{ $level['icon'] }} {{ $level['label'] }}
                    </span>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column - Article Info -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Article Card -->
                    <div class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700">
                        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <span>📦</span> Article Certifié
                        </h2>
                        
                        <div class="flex gap-4">
                            @if($vintPass->item && $vintPass->item->first_image_url)
                            <img src="{{ $vintPass->item->first_image_url }}" 
                                 alt="{{ $vintPass->item->name }}"
                                 class="w-32 h-32 object-cover rounded-xl">
                            @else
                            <div class="w-32 h-32 bg-gray-700 rounded-xl flex items-center justify-center">
                                <span class="text-4xl">📷</span>
                            </div>
                            @endif
                            
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-white mb-1">
                                    {{ $vintPass->item_snapshot['name'] ?? $vintPass->item?->name ?? 'Article' }}
                                </h3>
                                @if($vintPass->item_snapshot['brand'] ?? false)
                                <p class="text-blue-400 mb-2">{{ $vintPass->item_snapshot['brand'] }}</p>
                                @endif
                                @if($vintPass->item_snapshot['price'] ?? $vintPass->item?->price)
                                <p class="text-green-400 font-bold text-lg">
                                    {{ number_format($vintPass->item_snapshot['price'] ?? $vintPass->item->price, 0, ',', ' ') }} FC
                                </p>
                                @endif
                                
                                @if($vintPass->item)
                                <a href="{{ route('items.show', $vintPass->item) }}" 
                                   class="inline-flex items-center gap-1 text-gray-400 hover:text-white text-sm mt-2 transition-colors">
                                    Voir l'article
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Score & Verification -->
                    <div class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700">
                        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <span>✅</span> Score d'Authenticité
                        </h2>
                        
                        <div class="flex items-center gap-6">
                            <!-- Score Ring -->
                            <div class="relative w-28 h-28 flex-shrink-0">
                                <svg class="w-28 h-28 transform -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="45" stroke="rgba(255,255,255,0.1)" stroke-width="8" fill="none"/>
                                    @php
                                        $score = $vintPass->final_score ?? 0;
                                        $circumference = 2 * pi() * 45;
                                        $offset = $circumference - ($score / 100 * $circumference);
                                        $strokeColor = $score >= 90 ? '#10b981' : ($score >= 75 ? '#3b82f6' : ($score >= 50 ? '#f59e0b' : '#ef4444'));
                                    @endphp
                                    <circle cx="50" cy="50" r="45" 
                                            stroke="{{ $strokeColor }}" 
                                            stroke-width="8" 
                                            fill="none"
                                            stroke-dasharray="{{ $circumference }}"
                                            stroke-dashoffset="{{ $offset }}"
                                            stroke-linecap="round"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-white">{{ number_format($score, 1) }}%</span>
                                </div>
                            </div>
                            
                            <!-- Score Breakdown -->
                            <div class="flex-1 space-y-2">
                                @if($vintPass->ia_score)
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400">🤖 Score IA</span>
                                    <span class="text-white font-mono">{{ number_format($vintPass->ia_score, 1) }}%</span>
                                </div>
                                @endif
                                @if($vintPass->expert_score)
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400">👨‍💼 Score Expert</span>
                                    <span class="text-white font-mono">{{ number_format($vintPass->expert_score, 1) }}%</span>
                                </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400">📊 Score Final</span>
                                    <span class="text-green-400 font-mono font-bold">{{ number_format($vintPass->final_score, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Blockchain Info -->
                    @if($vintPass->blockchain_hash)
                    <div class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700">
                        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <span>🔗</span> Certification Blockchain
                        </h2>
                        
                        <div class="space-y-3">
                            <div>
                                <p class="text-gray-400 text-sm mb-1">Hash Blockchain</p>
                                <p class="font-mono text-green-400 text-sm break-all bg-black/30 rounded-lg p-3">
                                    {{ $vintPass->blockchain_hash }}
                                </p>
                            </div>
                            
                            @if($vintPass->blockchain_network)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Réseau</span>
                                <span class="text-white">{{ ucfirst($vintPass->blockchain_network) }}</span>
                            </div>
                            @endif
                            
                            @if($vintPass->blockchain_tx_id)
                            <div>
                                <p class="text-gray-400 text-sm mb-1">Transaction ID</p>
                                <a href="#" class="font-mono text-blue-400 text-sm hover:underline break-all">
                                    {{ $vintPass->blockchain_tx_id }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Ownership History -->
                    <div class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700">
                        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <span>📜</span> Historique de Propriété
                        </h2>
                        
                        @php
                            $history = $vintPass->ownership_history ?? [];
                        @endphp
                        
                        @if(count($history) > 0)
                        <div class="relative">
                            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-600"></div>
                            
                            @foreach($history as $index => $entry)
                            <div class="relative flex gap-4 pb-6 last:pb-0">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center z-10 flex-shrink-0
                                    {{ $index === 0 ? 'bg-green-500' : 'bg-gray-600' }}">
                                    @if($index === 0)
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    @else
                                    <span class="text-white text-sm">{{ count($history) - $index }}</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-white font-medium">
                                        {{ $entry['user_name'] ?? 'Utilisateur #' . ($entry['user_id'] ?? 'N/A') }}
                                    </p>
                                    <p class="text-gray-400 text-sm">
                                        {{ \Carbon\Carbon::parse($entry['date'] ?? now())->format('d/m/Y à H:i') }}
                                    </p>
                                    @if(isset($entry['type']))
                                    <span class="inline-block mt-1 text-xs px-2 py-1 rounded-full
                                        {{ $entry['type'] === 'sale' ? 'bg-green-500/20 text-green-400' : 'bg-blue-500/20 text-blue-400' }}">
                                        {{ $entry['type'] === 'sale' ? 'Vente' : 'Création' }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-400 text-center py-4">
                            Vous êtes le premier propriétaire de ce VintPass
                        </p>
                        @endif
                    </div>
                </div>

                <!-- Right Column - QR Code & Actions -->
                <div class="space-y-6">
                    
                    <!-- QR Code -->
                    <div class="bg-white rounded-2xl p-6 text-center">
                        @if($vintPass->qr_code_path && Storage::exists($vintPass->qr_code_path))
                        <div class="bg-white p-4 rounded-xl inline-block mb-4">
                            {!! Storage::get($vintPass->qr_code_path) !!}
                        </div>
                        @else
                        <div class="w-40 h-40 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <span class="text-4xl">📱</span>
                        </div>
                        @endif
                        
                        <p class="text-gray-600 text-sm mb-4">
                            Scannez ce QR code pour vérifier l'authenticité
                        </p>
                        
                        <a href="{{ route('vintpass.download-qr', $vintPass) }}" 
                           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Télécharger QR
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700">
                        <h3 class="text-white font-bold mb-4">📊 Statistiques</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Scans totaux</span>
                                <span class="text-white font-bold">{{ $vintPass->scan_count }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Transferts</span>
                                <span class="text-white font-bold">{{ $vintPass->transfer_count }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Créé le</span>
                                <span class="text-white">{{ $vintPass->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($vintPass->activated_at)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Activé le</span>
                                <span class="text-white">{{ $vintPass->activated_at->format('d/m/Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700">
                        <h3 class="text-white font-bold mb-4">📋 Statut</h3>
                        
                        @if($vintPass->status === 'active')
                        <div class="flex items-center gap-3 p-3 bg-green-500/10 rounded-xl border border-green-500/30">
                            <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                            <span class="text-green-400 font-medium">Actif & Valide</span>
                        </div>
                        @elseif($vintPass->status === 'pending')
                        <div class="flex items-center gap-3 p-3 bg-yellow-500/10 rounded-xl border border-yellow-500/30">
                            <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                            <span class="text-yellow-400 font-medium">En attente d'activation</span>
                        </div>
                        @elseif($vintPass->status === 'suspended')
                        <div class="flex items-center gap-3 p-3 bg-orange-500/10 rounded-xl border border-orange-500/30">
                            <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                            <span class="text-orange-400 font-medium">Suspendu</span>
                        </div>
                        @else
                        <div class="flex items-center gap-3 p-3 bg-red-500/10 rounded-xl border border-red-500/30">
                            <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                            <span class="text-red-400 font-medium">Révoqué</span>
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700">
                        <h3 class="text-white font-bold mb-4">⚡ Actions</h3>
                        
                        <div class="space-y-3">
                            <a href="{{ route('vintpass.scans', ['vintPass' => $vintPass]) }}" 
                               class="flex items-center gap-3 p-3 bg-gray-700/50 hover:bg-gray-700 rounded-xl transition-colors group">
                                <span class="text-xl">🔍</span>
                                <span class="text-gray-300 group-hover:text-white">Historique des scans</span>
                            </a>
                            <a href="{{ route('vintpass.transfers', ['vintPass' => $vintPass]) }}" 
                               class="flex items-center gap-3 p-3 bg-gray-700/50 hover:bg-gray-700 rounded-xl transition-colors group">
                                <span class="text-xl">🔄</span>
                                <span class="text-gray-300 group-hover:text-white">Historique des transferts</span>
                            </a>
                            <button onclick="copyLink()" 
                                    class="w-full flex items-center gap-3 p-3 bg-gray-700/50 hover:bg-gray-700 rounded-xl transition-colors group">
                                <span class="text-xl">🔗</span>
                                <span class="text-gray-300 group-hover:text-white">Copier le lien</span>
                            </button>
                        </div>
                    </div>

                    <!-- Public Link -->
                    <div class="bg-blue-500/10 rounded-2xl p-4 border border-blue-500/30">
                        <p class="text-blue-400 text-sm mb-2">🌍 Lien public de vérification</p>
                        <div class="flex items-center gap-2">
                            <input type="text" id="publicLink" readonly 
                                   value="{{ $vintPass->public_url }}"
                                   class="flex-1 bg-transparent border-0 text-white text-sm font-mono focus:ring-0 p-0">
                            <button onclick="copyLink()" 
                                    class="text-blue-400 hover:text-blue-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyLink() {
    const link = document.getElementById('publicLink').value;
    navigator.clipboard.writeText(link).then(() => {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.textContent = 'Lien copié !';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}
</script>
@endsection
