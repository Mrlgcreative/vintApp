@extends('layouts.admin')

@section('title', 'Codes promo')
@section('page-title', 'Codes promo')
@section('page-subtitle', 'Gestion des codes promo (coupons)')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <button type="button" onclick="document.getElementById('create-coupon').scrollIntoView({behavior:'smooth'})"
       class="inline-flex items-center gap-2 rounded-lg bg-gray-900 hover:bg-gray-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-plus"></i><span class="hidden sm:inline">Nouveau code</span><span class="sm:hidden">Ajouter</span>
    </button>
</div>
@endsection

@section('content')
@if(session('success'))
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
        <i class="fas fa-circle-check mr-2"></i>{{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Création -->
<div id="create-coupon" class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <h3 class="mb-4 font-semibold text-slate-900 dark:text-white"><i class="fas fa-plus mr-2 text-emerald-500"></i>Créer un code promo</h3>
    <form method="POST" action="{{ route('admin.coupons.store') }}" class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-4">
        @csrf
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Code <span class="text-slate-400">(vide = auto)</span></label>
            <div class="flex gap-2">
                <input type="text" name="code" id="coupon-code" value="{{ old('code') }}"
                       placeholder="Auto-généré (ex : VINT4K7PQZ)" maxlength="50"
                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                <button type="button" onclick="generateCode()"
                        class="shrink-0 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                        title="Générer un code aléatoire">
                    <i class="fas fa-dice"></i>
                </button>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Libellé</label>
            <input type="text" name="title" value="{{ old('title') }}" maxlength="255"
                   class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Type *</label>
            <select name="type" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                <option value="percent" {{ old('type') === 'fixed' ? '' : 'selected' }}>Pourcentage (%)</option>
                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Montant fixe</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Valeur *</label>
            <input type="number" step="0.01" min="0.01" name="value" required value="{{ old('value') }}"
                   class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Devise (fixe)</label>
            <select name="currency" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                @foreach(['USD', 'CDF', 'XAF', 'XOF', 'EUR'] as $cur)
                    <option value="{{ $cur }}" {{ old('currency', 'USD') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Commande min.</label>
            <input type="number" step="0.01" min="0" name="min_amount" value="{{ old('min_amount') }}" placeholder="Ex : 50"
                   class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Max utilisations</label>
            <input type="number" min="1" name="max_redemptions" value="{{ old('max_redemptions') }}" placeholder="Illimité"
                   class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Valide du <span class="text-slate-400">(vide = immédiat)</span></label>
            <div class="flex gap-2">
                <input type="datetime-local" name="starts_at" id="coupon-starts" value="{{ old('starts_at') }}"
                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                <button type="button" onclick="setStartToday()"
                        class="shrink-0 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                        title="Commence aujourd'hui à 00:00">
                    Aujourd'hui
                </button>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Valide jusqu'au <span class="text-slate-400">(vide = illimité)</span></label>
            <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"
                   class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
        </div>
        <div class="flex items-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                <i class="fas fa-check"></i> Créer
            </button>
        </div>
    </form>
</div>

<!-- Liste -->
<div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <form method="GET" class="flex flex-col gap-3 border-b border-slate-200 p-3 dark:border-slate-700 md:flex-row md:items-center">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un code..."
                   class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
        </div>
        <button type="submit" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">
            Filtrer
        </button>
        @if(request('search'))
            <a href="{{ route('admin.coupons.index') }}" class="rounded-lg px-4 py-2 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                Réinitialiser
            </a>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Réduction</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3">Utilisations</th>
                    <th class="px-4 py-3">Validité</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($coupons as $coupon)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-4 py-3">
                            <span class="font-mono font-semibold text-slate-900 dark:text-white">{{ $coupon->code }}</span>
                            @if($coupon->title)
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $coupon->title }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-emerald-600 dark:text-emerald-400">{{ $coupon->discount_label }}</td>
                        <td class="px-4 py-3">
                            @if($coupon->status === 'active')
                                <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                                    <i class="fas fa-circle-check"></i> Actif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    <i class="fas fa-circle-pause"></i> Inactif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ $coupon->redemption_count }}<span class="text-slate-400">/{{ $coupon->max_redemptions ?? '∞' }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            @if($coupon->starts_at || $coupon->ends_at)
                                <p class="text-xs">{{ $coupon->starts_at?->format('d/m/Y') ?? '—' }} → {{ $coupon->ends_at?->format('d/m/Y') ?? '∞' }}</p>
                            @else
                                <span class="text-slate-400">Permanente</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <form action="{{ route('admin.coupons.status', $coupon) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600"
                                            title="{{ $coupon->status === 'active' ? 'Désactiver' : 'Activer' }}">
                                        <i class="fas fa-{{ $coupon->status === 'active' ? 'pause' : 'play' }} text-xs"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                                      onsubmit="return confirm('Supprimer le code {{ $coupon->code }} ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20"
                                            title="Supprimer">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                            <i class="fas fa-tag mb-2 text-2xl"></i>
                            <p>Aucun code promo pour le moment.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-200 p-4 dark:border-slate-700">
        {{ $coupons->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = 'VINT';
    for (let i = 0; i < 6; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    const input = document.getElementById('coupon-code');
    input.value = code;
    input.focus();
}

function setStartToday() {
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    const pad = (n) => String(n).padStart(2, '0');
    const value = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T00:00`;
    document.getElementById('coupon-starts').value = value;
}
</script>
@endpush