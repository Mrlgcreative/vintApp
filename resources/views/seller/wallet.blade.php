@extends('app')

@section('title', 'Mon wallet')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        @include('seller.partials.sidebar')

        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-vinted-primary-600 via-vinted-primary-500 to-vinted-primary-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
                        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
                        <div class="relative">
                            <h1 class="text-2xl sm:text-3xl font-bold flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                    <i class="fas fa-wallet text-base"></i>
                                </div>
                                Mon wallet
                            </h1>
                            <p class="text-white/80 mt-2 text-sm sm:text-base">Vos soldes et transactions</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-vinted-primary-50 dark:bg-vinted-primary-500/10 flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-vinted-primary-600 dark:text-vinted-primary-400"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Solde USD</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $usdWallet?->formatted_balance ?? '$0.00' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                                <i class="fas fa-coins text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Solde CDF</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $cdfWallet?->formatted_balance ?? '0,00 FC' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="font-semibold text-gray-900 dark:text-white">Transactions récentes</h2>
                    </div>

                    @if($recentTransactions->count() > 0)
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($recentTransactions as $transaction)
                                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-center gap-4 flex-wrap sm:flex-nowrap">
                                        <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <i class="{{ $transaction->payment_method_icon }} text-gray-500 dark:text-gray-300"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h6 class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($transaction->purpose ?? $transaction->type) }}</h6>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                {{ $transaction->description ?? \Illuminate\Support\Str::title($transaction->type) }} · {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <p class="font-bold {{ $transaction->amount >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $transaction->amount >= 0 ? '+' : '' }}{{ $transaction->formatted_amount }}
                                            </p>
                                            <span class="text-xs text-gray-400">{{ $transaction->status }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $recentTransactions->links() }}
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                <i class="fas fa-receipt text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Aucune transaction</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Vos mouvements de wallet apparaîtront ici.</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection