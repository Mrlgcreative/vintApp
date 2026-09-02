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

<input type="hidden" name="scope" value="items">

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Titre <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $o?->title ?? '') }}" required maxlength="255"
               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40">
    </div>

    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
        <textarea name="description" rows="3"
                  class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40">{{ old('description', $o?->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Type de réduction <span class="text-red-500">*</span></label>
        <select name="type" id="offer-type"
                class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40">
            <option value="percent" {{ old('type', $o?->type ?? 'percent') === 'percent' ? 'selected' : '' }}>Pourcentage (%)</option>
            <option value="fixed" {{ old('type', $o?->type ?? '') === 'fixed' ? 'selected' : '' }}>Montant fixe</option>
        </select>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Valeur <span class="text-red-500">*</span></label>
        <input type="number" name="value" step="0.01" min="0.01" required value="{{ old('value', $o?->value ?? '') }}"
               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40">
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Vente flash</label>
        <select name="is_flash_sale"
                class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40">
            <option value="0" {{ old('is_flash_sale', $o?->is_flash_sale ?? 0) == 0 ? 'selected' : '' }}>Non</option>
            <option value="1" {{ old('is_flash_sale', $o?->is_flash_sale ?? 0) == 1 ? 'selected' : '' }}>Oui</option>
        </select>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Limite d'utilisations</label>
        <input type="number" name="max_redemptions" min="1" value="{{ old('max_redemptions', $o?->max_redemptions ?? '') }}"
               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Laisser vide pour illimité.</p>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Début</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $o?->starts_at ? \Carbon\Carbon::parse($o->starts_at)->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40">
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Fin</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $o?->ends_at ? \Carbon\Carbon::parse($o->ends_at)->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white transition-colors focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500/40">
    </div>
</div>

<div class="mt-5">
    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Produits en promotion <span class="text-red-500">*</span></label>
    @if($items->count() > 0)
        <select name="items[]" multiple required class="js-offer-items w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white" style="min-height:160px">
            @foreach($items as $item)
                <option value="{{ $item->id }}" {{ (old('items') && in_array($item->id, old('items'))) || ($o?->items?->contains($item->id)) ? 'selected' : '' }}>{{ $item->name }} ({{ $item->formatted_price }})</option>
            @endforeach
        </select>
    @else
        <p class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
            Vous n'avez aucun produit publié. <a href="{{ route('items.create') }}" class="font-semibold underline">Publiez un article</a> avant de créer une offre.
        </p>
    @endif
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maintenez Ctrl (Cmd) pour sélectionner plusieurs produits.</p>
</div>