@extends('layouts.admin')

@section('title', 'Gestion des Wallets')
@section('page-title', 'Gestion des Wallets Entreprise')

@push('styles')
<style>
    .page-wrapper { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        position: relative;
    }

    .page-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .content-wrapper {
        position: relative;
        z-index: 1;
        background: rgba(255,255,255,0.95);
        border-radius: 20px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.1);
        backdrop-filter: blur(20px);
    }

    .stats-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        color: white;
        overflow: hidden;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 160px;
        background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .stats-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }

    .stats-card.wallets {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stats-card.usd {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .stats-card.cdf {
        background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
    }

    .stats-card.commissions {
        background: linear-gradient(135deg, #8360c3 0%, #2ebf91 100%);
    }

    .main-card {
        background: white;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .enterprise-wallet {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        overflow: hidden;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .enterprise-wallet:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }

    .balance-display {
        font-size: 3rem;
        font-weight: 800;
        line-height: 1;
        text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .currency-badge {
        background: rgba(255,255,255,0.9);
        color: #2c3e50;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.875rem;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .btn-modern {
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .table-modern {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 16px;
    }

    .modal-modern .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }

    .modal-modern .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 16px 16px 0 0;
        border-bottom: none;
        padding: 1.5rem;
    }

    .form-control-modern {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .animate-fade-in {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="page-wrapper min-h-screen py-8">
    <div class="content-wrapper mx-8 my-8 p-12 max-w-7xl mx-auto">
        <h1 class="text-5xl font-bold text-center mb-12 bg-gradient-to-r from-indigo-600 to-primary-600 bg-clip-text text-transparent animate-fade-in">
            💼 Gestion des Portefeuilles Entreprise
        </h1>

        <!-- Statistiques Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-12 animate-fade-in">
            <div class="stats-card wallets group">
                <div class="p-8 h-full flex flex-col justify-center items-center text-center">
                    <div class="text-6xl mb-4 transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3 class="text-4xl font-bold mb-2">{{ $totalWallets ?? 0 }}</h3>
                    <p class="text-base font-semibold uppercase tracking-wider">Portefeuilles Actifs</p>
                </div>
            </div>
            <div class="stats-card usd group">
                <div class="p-8 h-full flex flex-col justify-center items-center text-center">
                    <div class="text-6xl mb-4 transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 class="text-4xl font-bold mb-2">${{ number_format($totalUsdBalance ?? 0, 2) }}</h3>
                    <p class="text-base font-semibold uppercase tracking-wider">Solde Total USD</p>
                </div>
            </div>
            <div class="stats-card cdf group">
                <div class="p-8 h-full flex flex-col justify-center items-center text-center">
                    <div class="text-6xl mb-4 transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3 class="text-4xl font-bold mb-2">{{ number_format($totalCdfBalance ?? 0, 0, ',', ' ') }}</h3>
                    <p class="text-base font-semibold uppercase tracking-wider">Solde Total CDF</p>
                </div>
            </div>
            <div class="stats-card commissions group">
                <div class="p-8 h-full flex flex-col justify-center items-center text-center">
                    <div class="text-6xl mb-4 transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-4xl font-bold mb-2">{{ $commissionsToday ?? 0 }}</h3>
                    <p class="text-base font-semibold uppercase tracking-wider">Commissions du Jour</p>
                </div>
            </div>
        </div>

        <!-- Portefeuilles Entreprise -->
        <div class="mb-12 animate-fade-in">
            <div class="main-card">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b-2 border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h5 class="text-xl font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                        <i class="fas fa-building mr-3"></i>
                        Portefeuilles Entreprise VintApp
                    </h5>
                    <button class="btn-modern btn-primary-modern flex items-center" onclick="openModal('createWalletModal')">
                        <i class="fas fa-plus mr-2"></i>
                        Nouveau Portefeuille
                    </button>
                </div>
                <div class="p-6">
                    @if(!empty($enterpriseWallets) && count($enterpriseWallets) > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($enterpriseWallets ?? [] as $wallet)
                                <div class="enterprise-wallet group">
                                    <div class="p-8 relative z-10">
                                        <div class="flex justify-between items-start mb-6">
                                            <div>
                                                <h6 class="text-white text-lg font-semibold mb-2 flex items-center">
                                                    <i class="fas fa-building mr-3"></i>
                                                    Portefeuille Entreprise {{ $wallet->currency ?? 'N/A' }}
                                                </h6>
                                                <small class="text-white opacity-80">
                                                    Commission: {{ $wallet->commission_rate ?? 5 }}%
                                                </small>
                                            </div>
                                            <span class="currency-badge">
                                                {{ $wallet->currency ?? 'N/A' }}
                                            </span>
                                        </div>
                                        
                                        <div class="text-center mb-6">
                                            <div class="balance-display text-white">
                                                @if(($wallet->currency ?? 'USD') === 'USD')
                                                    ${{ number_format($wallet->balance ?? 0, 2) }}
                                                @else
                                                    {{ number_format($wallet->balance ?? 0, 0, ',', ' ') }} FC
                                                @endif
                                            </div>
                                            <small class="text-white opacity-80">Solde disponible</small>
                                        </div>

                                        <div class="bg-white dark:bg-gray-800 bg-opacity-10 rounded-xl p-4 mb-6 backdrop-blur-sm">
                                            <div class="grid grid-cols-2 gap-4 text-center text-white">
                                                <div>
                                                    <div class="text-xl font-semibold mb-1">{{ $wallet->total_transactions ?? 0 }}</div>
                                                    <div class="text-xs uppercase tracking-wider opacity-80">Transactions</div>
                                                </div>
                                                <div>
                                                    <div class="text-xl font-semibold mb-1">
                                                        @if(($wallet->currency ?? 'USD') === 'USD')
                                                            ${{ number_format($wallet->total_commissions ?? 0, 2) }}
                                                        @else
                                                            {{ number_format($wallet->total_commissions ?? 0, 0, ',', ' ') }} FC
                                                        @endif
                                                    </div>
                                                    <div class="text-xs uppercase tracking-wider opacity-80">Commissions</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex gap-3">
                                            <button class="flex-1 px-4 py-3 bg-white dark:bg-gray-800 bg-opacity-10 text-white border-2 border-white border-opacity-30 rounded-lg font-semibold uppercase tracking-wide text-sm transition-all duration-300 hover:bg-opacity-20 hover:border-opacity-50 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center" 
                                                    onclick="openCommissionModal('{{ $wallet->id ?? '' }}')">
                                                <i class="fas fa-plus-circle mr-2"></i>
                                                Ajouter
                                            </button>
                                            <button class="flex-1 px-4 py-3 bg-white dark:bg-gray-800 text-gray-800 border-2 border-white rounded-lg font-semibold uppercase tracking-wide text-sm transition-all duration-300 hover:bg-gray-50 dark:bg-gray-900 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center" 
                                                    onclick="openWithdrawModal('{{ $wallet->id ?? '' }}')">
                                                <i class="fas fa-arrow-down mr-2"></i>
                                                Retirer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state text-center py-16 px-8">
                            <i class="fas fa-wallet text-6xl text-gray-400 mb-6 opacity-60"></i>
                            <h5 class="text-gray-600 dark:text-gray-300 font-semibold text-xl mb-4">Aucun Portefeuille Entreprise</h5>
                            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">Créez votre premier portefeuille entreprise pour commencer à gérer les commissions et les finances de VintApp.</p>
                            <button class="btn-modern btn-primary-modern flex items-center mx-auto" onclick="openModal('createWalletModal')">
                                <i class="fas fa-plus mr-2"></i>
                                Créer Premier Portefeuille
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Graphiques et Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12 animate-fade-in">
            <div class="lg:col-span-2">
                <div class="main-card">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b-2 border-gray-200 dark:border-gray-700">
                        <h5 class="text-xl font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                            <i class="fas fa-chart-area mr-3"></i>
                            Évolution des Commissions (30 derniers jours)
                        </h5>
                    </div>
                    <div class="p-6">
                        <div class="h-80 relative">
                            <canvas id="commissionsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="main-card h-full">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b-2 border-gray-200 dark:border-gray-700">
                        <h5 class="text-xl font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                            <i class="fas fa-chart-pie mr-3"></i>
                            Répartition par Devise
                        </h5>
                    </div>
                    <div class="p-6">
                        <div class="h-80 relative">
                            <canvas id="currencyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières Transactions Entreprise -->
        <div class="animate-fade-in">
            <div class="main-card">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b-2 border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h5 class="text-xl font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                        <i class="fas fa-history mr-3"></i>
                        Dernières Transactions Entreprise
                    </h5>
                    <a href="{{ route('admin.transactions.index') }}" class="btn-modern btn-primary-modern text-sm flex items-center">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Voir Toutes
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(!empty($recentTransactions) && count($recentTransactions) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date/Heure</th>
                                        <th>Type</th>
                                        <th>Montant</th>
                                        <th>Devise</th>
                                        <th>Description</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions ?? [] as $transaction)
                                        <tr class="transaction-row">
                                            <td>
                                                <div class="text-dark fw-semibold">
                                                    {{ $transaction->created_at->format('d/m/Y') }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $transaction->created_at->format('H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($transaction->type === 'credit')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-plus me-1"></i>Commission
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-minus me-1"></i>Retrait
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-warning' }}">
                                                    {{ $transaction->type === 'credit' ? '+' : '-' }}
                                                    @if($transaction->currency === 'USD')
                                                        ${{ number_format($transaction->amount, 2) }}
                                                    @else
                                                        {{ number_format($transaction->amount, 0, ',', ' ') }} FC
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge currency-badge {{ $transaction->currency === 'USD' ? 'bg-success' : 'bg-info' }}">
                                                    {{ $transaction->currency }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;">
                                                    {{ $transaction->description ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                @switch($transaction->status ?? 'pending')
                                                    @case('completed')
                                                        <span class="status-badge bg-success text-white">Complété</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="status-badge bg-warning text-dark">En attente</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="status-badge bg-danger text-white">Échoué</span>
                                                        @break
                                                    @default
                                                        <span class="status-badge bg-secondary text-white">{{ $transaction->status ?? 'N/A' }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Aucune transaction récente trouvée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Créer Wallet -->
<div id="createWalletModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="bg-gradient-to-r from-indigo-600 to-primary-600 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h5 class="text-xl font-bold flex items-center">
                    <i class="fas fa-plus mr-3"></i>
                    Créer un Nouveau Portefeuille Entreprise
                </h5>
                <button type="button" class="text-white hover:text-gray-200 text-2xl font-bold" onclick="closeModal('createWalletModal')">&times;</button>
            </div>
        </div>
        <form action="{{ route('admin.wallets.store') }}" method="POST">
            @csrf
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="currency" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Devise *</label>
                        <select class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800" name="currency" id="currency" required>
                            <option value="">🌍 Sélectionner une devise</option>
                            <option value="USD">💵 USD (Dollar Américain)</option>
                            <option value="CDF">🇨🇩 CDF (Franc Congolais)</option>
                        </select>
                        <small class="text-gray-500 dark:text-gray-400 text-sm mt-1 block">Choisissez la devise principale du portefeuille</small>
                    </div>
                    
                    <div>
                        <label for="commission_rate" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Taux de Commission (%)</label>
                        <input type="number" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800" name="commission_rate" id="commission_rate" 
                               value="5.00" step="0.01" min="0" max="100" placeholder="Ex: 5.00">
                        <small class="text-gray-500 dark:text-gray-400 text-sm mt-1 block">Pourcentage de commission sur les transactions</small>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="initial_balance" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Solde Initial</label>
                    <input type="number" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800" name="initial_balance" id="initial_balance" 
                           value="0.00" step="0.01" min="0" placeholder="0.00">
                    <small class="text-gray-500 dark:text-gray-400 text-sm mt-1 block">Solde initial du wallet (optionnel)</small>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 px-8 py-4 rounded-b-2xl flex justify-end space-x-4">
                <button type="button" class="px-6 py-3 bg-gray-300 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-400 transition-colors duration-200" onclick="closeModal('createWalletModal')">
                    Annuler
                </button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-primary-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-primary-700 transition-all duration-200 transform hover:scale-105 flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Créer Portefeuille
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ajouter Commission -->
<div id="addCommissionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full">
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h5 class="text-xl font-bold flex items-center">
                    <i class="fas fa-plus-circle mr-3"></i>
                    Ajouter Commission
                </h5>
                <button type="button" class="text-white hover:text-gray-200 text-2xl font-bold" onclick="closeModal('addCommissionModal')">&times;</button>
            </div>
        </div>
        <form id="addCommissionForm" method="POST">
            @csrf
            <div class="p-8">
                <div class="mb-6">
                    <label for="amount" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Montant *</label>
                    <input type="number" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800" name="amount" id="commissionAmount" 
                           step="0.01" min="0.01" required placeholder="Entrez le montant">
                </div>
                
                <div class="mb-6">
                    <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Description</label>
                    <textarea class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800" name="description" id="commissionDescription" 
                              rows="3" placeholder="Description de la commission (optionnel)"></textarea>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 px-8 py-4 rounded-b-2xl flex justify-end space-x-4">
                <button type="button" class="px-6 py-3 bg-gray-300 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-400 transition-colors duration-200" onclick="closeModal('addCommissionModal')">
                    Annuler
                </button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105 flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Ajouter Commission
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Retirer Fonds -->
<div id="withdrawModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full">
        <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h5 class="text-xl font-bold flex items-center">
                    <i class="fas fa-arrow-down mr-3"></i>
                    Retirer des Fonds
                </h5>
                <button type="button" class="text-white hover:text-gray-200 text-2xl font-bold" onclick="closeModal('withdrawModal')">&times;</button>
            </div>
        </div>
        <form id="withdrawForm" method="POST">
            @csrf
            <div class="p-8">
                <div class="mb-6">
                    <label for="amount" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Montant *</label>
                    <input type="number" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800" name="amount" id="withdrawAmount" 
                           step="0.01" min="0.01" required placeholder="Entrez le montant à retirer">
                </div>
                
                <div class="mb-6">
                    <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Description</label>
                    <textarea class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-gray-50 dark:bg-gray-900 focus:bg-white dark:bg-gray-800" name="description" id="withdrawDescription" 
                              rows="3" placeholder="Raison du retrait (optionnel)"></textarea>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 px-8 py-4 rounded-b-2xl flex justify-end space-x-4">
                <button type="button" class="px-6 py-3 bg-gray-300 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-400 transition-colors duration-200" onclick="closeModal('withdrawModal')">
                    Annuler
                </button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105 flex items-center">
                    <i class="fas fa-minus mr-2"></i>
                    Retirer Fonds
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Fonctions pour gérer les modales
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openCommissionModal(walletId) {
    const form = document.getElementById('addCommissionForm');
    form.action = `/admin/wallets/${walletId}/add-commission`;
    openModal('addCommissionModal');
}

function openWithdrawModal(walletId) {
    const form = document.getElementById('withdrawForm');
    form.action = `/admin/wallets/${walletId}/withdraw`;
    openModal('withdrawModal');
}

// Fermer les modales en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    const modals = ['createWalletModal', 'addCommissionModal', 'withdrawModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            closeModal(modalId);
        }
    });
});

// Fermer les modales avec la touche Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['createWalletModal', 'addCommissionModal', 'withdrawModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                closeModal(modalId);
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Graphique des commissions
    const commissionsCtx = document.getElementById('commissionsChart');
    if (commissionsCtx) {
        new Chart(commissionsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels'] ?? []) !!},
                datasets: [{
                    label: 'Commissions USD',
                    data: {!! json_encode($chartData['usd'] ?? []) !!},
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Commissions CDF',
                    data: {!! json_encode($chartData['cdf'] ?? []) !!},
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Graphique en secteurs des devises
    const currencyCtx = document.getElementById('currencyChart');
    if (currencyCtx) {
        new Chart(currencyCtx, {
            type: 'doughnut',
            data: {
                labels: ['USD', 'CDF'],
                datasets: [{
                    data: [{!! $totalUsdBalance ?? 0 !!}, {!! $totalCdfBalance ?? 0 !!}],
                    backgroundColor: ['#28a745', '#007bff'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
});
</script>

<!-- Script de test pour déboguer les modals -->
<script src="{{ asset('js/wallet-modal-test.js') }}"></script>

<script>
// Test supplémentaire pour vérifier la console
setTimeout(function() {
    console.log('=== DIAGNOSTIC MODAL WALLETS ===');
    console.log('Page chargée après 2 secondes');
    console.log('Tentative d\'ouverture du modal de test...');
    
    // Test si on peut ouvrir un modal manuellement
    if (typeof openModal !== 'undefined') {
        console.log('✅ Fonction openModal disponible');
        // Tester dans 1 seconde
        setTimeout(() => {
            console.log('Test d\'ouverture automatique du modal createWalletModal...');
            try {
                openModal('createWalletModal');
                console.log('✅ Modal ouvert avec succès');
                
                // Fermer après 3 secondes
                setTimeout(() => {
                    closeModal('createWalletModal');
                    console.log('✅ Modal fermé avec succès');
                }, 3000);
            } catch (error) {
                console.error('❌ Erreur lors de l\'ouverture du modal:', error);
            }
        }, 1000);
    } else {
        console.error('❌ Fonction openModal non disponible');
    }
}, 2000);
</script>
@endpush
