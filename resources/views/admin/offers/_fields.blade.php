@php
    $o = $offer ?? null;
@endphp

@if($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">
        <ul class="list-disc pl-4 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Titre <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $o->title ?? '') }}" required maxlength="255"
               class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>

    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
        <textarea name="description" rows="3"
                  class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">{{ old('description', $o->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Type de réduction <span class="text-red-500">*</span></label>
        <select name="type" id="offer-type"
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            <option value="percent" {{ old('type', $o->type ?? 'percent') === 'percent' ? 'selected' : '' }}>Pourcentage (%)</option>
            <option value="fixed" {{ old('type', $o->type ?? '') === 'fixed' ? 'selected' : '' }}>Montant fixe</option>
        </select>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Valeur <span class="text-red-500">*</span></label>
        <input type="number" name="value" step="0.01" min="0.01" required value="{{ old('value', $o->value ?? '') }}"
               class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Périmètre <span class="text-red-500">*</span></label>
        <select name="scope" id="offer-scope"
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            <option value="global" {{ old('scope', $o->scope ?? '') === 'global' ? 'selected' : '' }}>Toute la boutique</option>
            <option value="categories" {{ old('scope', $o->scope ?? '') === 'categories' ? 'selected' : '' }}>Catégories</option>
            <option value="items" {{ old('scope', $o->scope ?? '') === 'items' ? 'selected' : '' }}>Produits spécifiques</option>
        </select>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Réduction en vedette</label>
        <select name="is_featured" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            <option value="0" {{ old('is_featured', $o->is_featured ?? 0) == 0 ? 'selected' : '' }}>Non</option>
            <option value="1" {{ old('is_featured', $o->is_featured ?? 0) == 1 ? 'selected' : '' }}>Oui</option>
        </select>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Vente flash (compte à rebours)</label>
        <select name="is_flash_sale" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            <option value="0" {{ old('is_flash_sale', $o->is_flash_sale ?? 0) == 0 ? 'selected' : '' }}>Non</option>
            <option value="1" {{ old('is_flash_sale', $o->is_flash_sale ?? 0) == 1 ? 'selected' : '' }}>Oui</option>
        </select>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Statut</label>
        <select name="status" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            <option value="active" {{ old('status', $o->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="paused" {{ old('status', $o->status ?? '') === 'paused' ? 'selected' : '' }}>En pause</option>
        </select>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Limite d'utilisations</label>
        <input type="number" name="max_redemptions" min="1" value="{{ old('max_redemptions', $o->max_redemptions ?? '') }}"
               class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Laisser vide pour illimité.</p>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Début</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $o->starts_at ? \Carbon\Carbon::parse($o->starts_at)->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Fin</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $o->ends_at ? \Carbon\Carbon::parse($o->ends_at)->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>
</div>

<!-- Cibles : catégories -->
<div id="scope-categories" class="mt-5 hidden">
    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Catégories cibles <span class="text-red-500">*</span></label>
    <select name="categories[]" multiple class="js-offer-categories w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-800 dark:text-white" style="min-height:140px">
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ (old('categories') && in_array($cat->id, old('categories'))) || ($o && $o->categories->contains($cat->id)) ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Maintenez Ctrl (Cmd) pour sélectionner plusieurs catégories.</p>
</div>

<!-- Cibles : produits -->
<div id="scope-items" class="mt-5 hidden">
    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Produits cibles <span class="text-red-500">*</span></label>
    <select name="items[]" multiple class="js-offer-items w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-800 dark:text-white" style="min-height:160px">
        @foreach($items as $item)
            <option value="{{ $item->id }}" {{ (old('items') && in_array($item->id, old('items'))) || ($o && $o->items->contains($item->id)) ? 'selected' : '' }}>{{ $item->name }} ({{ $item->formatted_price }})</option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Maintenez Ctrl (Cmd) pour sélectionner plusieurs produits.</p>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const scopeEl = document.getElementById('offer-scope');
    const catEl = document.getElementById('scope-categories');
    const itemEl = document.getElementById('scope-items');

    function showScope() {
        const v = scopeEl.value;
        catEl.classList.toggle('hidden', v !== 'categories');
        itemEl.classList.toggle('hidden', v !== 'items');
    }
    scopeEl.addEventListener('change', showScope);
    showScope();
});
</script>
@endpush