@extends('app')

@section('title', 'Paiement de la vérification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">

        <a href="{{ route('authenticity.status', $check->item) }}" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-6">
            <i class="fas fa-arrow-left text-xs"></i>
            Retour au suivi
        </a>

        <div class="flex items-start gap-4 mb-8">
            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-credit-card text-gray-600 dark:text-gray-300"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Finaliser le paiement</h1>
                <p class="text-gray-600 dark:text-gray-300">Le règlement des frais lance immédiatement l'analyse de votre produit.</p>
            </div>
        </div>

        @if(session('error'))
            <x-alert variant="danger" class="mb-6">{{ session('error') }}</x-alert>
        @endif

        <x-card class="p-5 mb-6">
            <div class="flex items-center gap-4">
                @if(count($check->item->images) > 0)
                    <img src="{{ Storage::url($check->item->images[0]) }}" alt="{{ $check->item->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                @else
                    <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-image text-gray-400"></i>
                    </div>
                @endif

                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $check->item->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                        {{ $check->item->brand->name ?? 'Marque non spécifiée' }} · {{ $check->item->category->name ?? 'Sans catégorie' }}
                    </p>
                </div>

                <p class="font-semibold text-gray-900 dark:text-white flex-shrink-0">{{ $check->item->formatted_price }}</p>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700/50 flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-300">Frais de certification</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($check->verification_fee, 2) }}</span>
            </div>
        </x-card>

        <x-card class="p-5 mb-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Inclus dans la certification</h2>
            <ul class="space-y-3.5">
                @foreach([
                    ['fa-robot', 'Analyse par intelligence artificielle', 'Analyse automatique de vos photos et du numéro de série.'],
                    ['fa-user-check', 'Expertise humaine si nécessaire', 'Un expert de la catégorie tranche en cas de doute.'],
                    ['fa-badge-check', 'Badge d\'authenticité permanent', 'Visible sur votre annonce pour rassurer les acheteurs.'],
                    ['fa-shield-halved', 'Protection anti-fraude renforcée', 'Moins de litiges et de remboursements frauduleux.'],
                ] as [$icon, $title, $description])
                    <li class="flex items-start gap-3">
                        <i class="fas {{ $icon }} text-emerald-600 dark:text-emerald-400 mt-1 text-sm w-4"></i>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $title }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-card>

        <x-card class="p-5">
            @php
                $canPay = $wallet && (float) $wallet->balance >= (float) $check->verification_fee;
            @endphp

            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Moyen de paiement</h2>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700/50 p-4 mb-5">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <i class="fas fa-wallet text-gray-500 dark:text-gray-400"></i>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white">Solde de votre portefeuille VintApp</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Débit automatique en dollars</p>
                        </div>
                    </div>
                    @if($wallet)
                        <p class="font-semibold text-gray-900 dark:text-white flex-shrink-0">{{ number_format((float) $wallet->balance, 2) }} $</p>
                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400 flex-shrink-0">Indisponible</span>
                    @endif
                </div>

                @if(!$canPay)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700/50">
                        <x-alert variant="warning">
                            Solde insuffisant pour couvrir les frais. Rechargez votre portefeuille avant de confirmer.
                        </x-alert>
                        <x-button-outline :href="route('wallet.index')" class="mt-3">
                            <i class="fas fa-plus mr-2"></i>Recharger mon portefeuille
                        </x-button-outline>
                    </div>
                @endif
            </div>

            <x-alert variant="info" class="mb-6">
                Le paiement est définitif et déclenche l'analyse. Un remboursement n'est possible qu'en cas de failure technique de notre part.
            </x-alert>

            <form action="{{ route('authenticity.payment.confirm', $check) }}" method="POST">
                @csrf

                <div class="flex items-start gap-3 mb-6">
                    <input type="checkbox" id="payment_terms" required
                           class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500">
                    <label for="payment_terms" class="text-sm text-gray-700 dark:text-gray-300">
                        Je confirme avoir lu et accepté les conditions de certification et je comprends que ce paiement lance immédiatement l'analyse.
                        <span class="text-red-500">*</span>
                    </label>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('authenticity.request', $check->item) }}" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Modifier ma demande
                    </a>

                    <x-button-primary type="submit" variant="success" size="lg"
                                     :disabled="$canPay ? 'disabled' : null">
                        <i class="fas fa-lock mr-2"></i>
                        Payer {{ number_format($check->verification_fee, 2) }} $
                    </x-button-primary>
                </div>
            </form>
        </x-card>

        <x-card class="p-5 mt-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Et ensuite ?</h2>
            <ol class="space-y-3">
                @foreach([
                    'Analyse automatique de vos photos par l\'IA.',
                    'Assignation à un expert de la catégorie en cas de doute.',
                    'Décision finale, puis notification du résultat.',
                ] as $index => $step)
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold flex items-center justify-center flex-shrink-0">{{ $index + 1 }}</span>
                        <p class="text-sm text-gray-700 dark:text-gray-300 pt-0.5">{{ $step }}</p>
                    </li>
                @endforeach
            </ol>
        </x-card>
    </div>
</div>
@endsection
