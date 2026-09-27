@extends('app')

@section('title', 'Demander la certification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">

        <a href="{{ route('items.show', $item) }}" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-6">
            <i class="fas fa-arrow-left text-xs"></i>
            Retour au produit
        </a>

        <div class="flex items-start gap-4 mb-8">
            <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-shield-alt text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Certifiez votre produit</h1>
                <p class="text-gray-600 dark:text-gray-300">
                    Obtenez le badge <strong class="text-emerald-600 dark:text-emerald-400">Vérifié VintApp</strong> pour rassurer les acheteurs et vendre plus vite.
                </p>
            </div>
        </div>

        <x-card class="p-5 mb-6">
            <div class="flex items-center gap-4">
                @if($item->images && count($item->images) > 0)
                    <img src="{{ Storage::url($item->images[0]) }}" alt="{{ $item->name }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                @else
                    <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-image text-gray-400"></i>
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $item->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                        {{ $item->brand->name ?? 'Marque non spécifiée' }} · {{ $item->category->name ?? 'Sans catégorie' }}
                    </p>
                </div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $item->formatted_price }}</p>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700/50 flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-300">Frais de certification</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($fee, 2) }}</span>
            </div>
        </x-card>

        @php
            // Sans JavaScript toutes les étapes restent visibles et le formulaire
            // reste soumettable : le script ne fait que piloter l'affichage.
            $startStep = 1;
            if ($errors->has('certificate') || $errors->has('receipt')) {
                $startStep = 2;
            }
            if ($errors->has('serial_number') || $errors->has('purchase_date') || $errors->has('purchase_location') || $errors->has('additional_notes')) {
                $startStep = 3;
            }
            if ($errors->has('terms_accepted')) {
                $startStep = 4;
            }
        @endphp

        @if ($errors->any())
            <x-alert variant="danger" class="mb-6">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <x-card>
            <form action="{{ route('authenticity.submit', $item) }}" method="POST" enctype="multipart/form-data" id="certificationForm" novalidate>
                @csrf

                <ol class="flex items-center gap-2 mb-8" id="stepper">
                    @php
                        $steps = ['Photos', 'Preuves', 'Détails', 'Confirmation'];
                    @endphp
                    @foreach ($steps as $index => $label)
                        @php $n = $index + 1; @endphp
                        <li class="flex items-center gap-2 flex-1 last:flex-none" data-step-indicator="{{ $n }}">
                            <span class="step-dot w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 transition-colors">{{ $n }}</span>
                            <span class="step-label text-xs sm:text-sm text-gray-500 dark:text-gray-400 hidden sm:inline transition-colors">{{ $label }}</span>
                            @if (! $loop->last)
                                <span class="step-line flex-1 h-px bg-gray-200 dark:bg-gray-700"></span>
                            @endif
                        </li>
                    @endforeach
                </ol>

                <div class="h-1 rounded-full bg-gray-200 dark:bg-gray-700 mb-8 overflow-hidden">
                    <div id="stepProgress" class="h-full bg-emerald-600 transition-all duration-300" style="width: 0%"></div>
                </div>

                {{-- Étape 1 : photos --}}
                <section data-step-panel="1">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Photos du produit</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                        Minimum 3 photos nettes : vue de face, de dos et de profil. La première photo sert de référence à l'analyse.
                    </p>

                    <label for="product_images" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Photos <span class="text-red-500">*</span>
                    </label>
                    <input type="file" id="product_images" name="product_images[]" accept="image/jpeg,image/png" multiple
                           class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-gray-600 cursor-pointer">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">JPG ou PNG, 10 Mo maximum par photo, 4 photos au maximum.</p>

                    <ul id="productImageList" class="mt-4 space-y-2"></ul>
                </section>

                {{-- Étape 2 : preuves --}}
                <section data-step-panel="2">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Preuves d'achat</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                        Facultatif, mais un certificat ou un reçu accélère nettement la validation.
                    </p>

                    <div class="space-y-5">
                        <div>
                            <label for="certificate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Certificat d'authenticité ou garantie</label>
                            <input type="file" id="certificate" name="certificate" accept="image/jpeg,image/png,application/pdf"
                                   class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-gray-600 cursor-pointer">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Image ou PDF, 5 Mo maximum.</p>
                        </div>

                        <div>
                            <label for="receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reçu ou facture d'achat</label>
                            <input type="file" id="receipt" name="receipt" accept="image/jpeg,image/png,application/pdf"
                                   class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-gray-600 cursor-pointer">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Image ou PDF, 5 Mo maximum.</p>
                        </div>
                    </div>
                </section>

                {{-- Étape 3 : détails --}}
                <section data-step-panel="3">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Détails de l'achat</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                        Ces informations permettent à l'expert de recouper avec le registre du fabricant.
                    </p>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label for="serial_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Numéro de série ou code produit</label>
                            <x-input id="serial_number" name="serial_number" value="{{ old('serial_number') }}" placeholder="Ex: ABC123456" />
                        </div>

                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'achat</label>
                            <x-input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" />
                        </div>

                        <div>
                            <label for="purchase_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lieu d'achat</label>
                            <x-input id="purchase_location" name="purchase_location" value="{{ old('purchase_location') }}" placeholder="Boutique, site de la marque…" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="additional_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes supplémentaires</label>
                            <x-textarea id="additional_notes" name="additional_notes" rows="4" placeholder="Tout élément utile pour confirmer l'authenticité : accessoires, emballage, particularités…">{{ old('additional_notes') }}</x-textarea>
                        </div>
                    </div>
                </section>

                {{-- Étape 4 : confirmation --}}
                <section data-step-panel="4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Vérifiez et confirmez</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Relisez votre dossier avant de l'envoyer.</p>

                    <dl class="rounded-lg border border-gray-200 dark:border-gray-700/50 divide-y divide-gray-200 dark:divide-gray-700/50 text-sm">
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-gray-600 dark:text-gray-400">Produit</dt>
                            <dd class="font-medium text-gray-900 dark:text-white text-right truncate">{{ $item->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-gray-600 dark:text-gray-400">Photos</dt>
                            <dd class="font-medium text-gray-900 dark:text-white text-right" id="summaryPhotos">—</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-gray-600 dark:text-gray-400">Certificat</dt>
                            <dd class="font-medium text-gray-900 dark:text-white text-right" id="summaryCertificate">—</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-gray-600 dark:text-gray-400">Reçu</dt>
                            <dd class="font-medium text-gray-900 dark:text-white text-right" id="summaryReceipt">—</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-gray-600 dark:text-gray-400">Numéro de série</dt>
                            <dd class="font-medium text-gray-900 dark:text-white text-right" id="summarySerial">{{ old('serial_number') ?: '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-gray-600 dark:text-gray-400">Frais</dt>
                            <dd class="font-semibold text-gray-900 dark:text-white text-right">${{ number_format($fee, 2) }}</dd>
                        </div>
                    </dl>

                    <div class="flex items-start gap-3 mt-6">
                        <input type="checkbox" id="terms_accepted" name="terms_accepted" value="1" @checked(old('terms_accepted'))
                               class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500">
                        <label for="terms_accepted" class="text-sm text-gray-700 dark:text-gray-300">
                            J'accepte les conditions de certification et je certifie que les informations fournies sont exactes.
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                    @error('terms_accepted')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </section>

                <div class="flex items-center justify-between gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700/50">
                    <x-button-outline type="button" id="prevStep" class="hidden" tone="default">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </x-button-outline>

                    <div class="ml-auto flex items-center gap-3">
                        <a href="{{ route('items.show', $item) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Annuler</a>
                        <x-button-primary type="button" id="nextStep" variant="success" class="hidden">
                            Continuer<i class="fas fa-arrow-right ml-2"></i>
                        </x-button-primary>
                        <x-button-primary type="submit" id="submitStep" variant="success">
                            <i class="fas fa-shield-alt mr-2"></i>Envoyer la demande
                        </x-button-primary>
                    </div>
                </div>
            </form>
        </x-card>

        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-6">
            Vos photos sont utilisées uniquement pour la vérification et supprimées au terme du traitement.
        </p>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const form = document.getElementById('certificationForm');
    const panels = Array.from(form.querySelectorAll('[data-step-panel]'));
    const indicators = Array.from(document.querySelectorAll('[data-step-indicator]'));
    const progress = document.getElementById('stepProgress');
    const prevBtn = document.getElementById('prevStep');
    const nextBtn = document.getElementById('nextStep');
    const submitBtn = document.getElementById('submitStep');
    const productImages = document.getElementById('product_images');
    const terms = document.getElementById('terms_accepted');

    let current = {{ $startStep }};
    const last = panels.length;

    function show(step) {
        current = Math.min(Math.max(step, 1), last);

        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.stepPanel !== String(current));
        });

        indicators.forEach((indicator) => {
            const n = Number(indicator.dataset.stepIndicator);
            const dot = indicator.querySelector('.step-dot');
            const label = indicator.querySelector('.step-label');
            const line = indicator.querySelector('.step-line');
            const done = n < current;
            const active = n === current;

            dot.className = 'step-dot w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold border transition-colors';
            if (done) {
                dot.classList.add('bg-emerald-600', 'border-emerald-600', 'text-white');
                dot.innerHTML = '<i class="fas fa-check text-[10px]"></i>';
            } else if (active) {
                dot.classList.add('bg-emerald-600', 'border-emerald-600', 'text-white');
                dot.textContent = n;
            } else {
                dot.classList.add('border-gray-300', 'dark:border-gray-600', 'text-gray-500', 'dark:text-gray-400');
                dot.textContent = n;
            }

            if (label) {
                label.classList.toggle('text-emerald-600', active);
                label.classList.toggle('dark:text-emerald-400', active);
                label.classList.toggle('font-medium', active);
            }

            if (line) {
                line.classList.toggle('bg-emerald-600', done);
            }
        });

        progress.style.width = ((current - 1) / (last - 1)) * 100 + '%';

        prevBtn.classList.toggle('hidden', current === 1);
        nextBtn.classList.toggle('hidden', current === last);
        submitBtn.classList.toggle('hidden', current !== last);

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function fail(message) {
        let box = document.getElementById('stepError');
        if (!box) {
            box = document.createElement('p');
            box.id = 'stepError';
            box.className = 'text-sm text-red-600 dark:text-red-400 mt-4';
            form.querySelector(`[data-step-panel="${current}"]`).appendChild(box);
        }
        box.textContent = message;
    }

    function validate(step) {
        if (step === 1) {
            const count = productImages.files.length;
            if (count === 0) {
                return 'Ajoutez au moins une photo du produit.';
            }
            if (count > 4) {
                return 'Quatre photos maximum.';
            }
        }

        if (step === 4 && !terms.checked) {
            return 'Vous devez accepter les conditions pour envoyer la demande.';
        }

        return null;
    }

    function fileName(input) {
        return input.files.length ? input.files[0].name : '';
    }

    function refreshSummary() {
        const photos = productImages.files.length;
        document.getElementById('summaryPhotos').textContent = photos ? photos + (photos > 1 ? ' photos' : ' photo') : '—';
        document.getElementById('summaryCertificate').textContent = fileName(document.getElementById('certificate')) || '—';
        document.getElementById('summaryReceipt').textContent = fileName(document.getElementById('receipt')) || '—';
        document.getElementById('summarySerial').textContent = document.getElementById('serial_number').value || '—';
    }

    productImages.addEventListener('change', function () {
        const list = document.getElementById('productImageList');
        const files = Array.from(productImages.files);

        list.innerHTML = files.map(function (file, index) {
            const roles = ['Vue de face', 'Vue de dos', 'Vue de profil', 'Détail'];
            const role = roles[index] || 'Photo';
            return '<li class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-700/50 px-3 py-2 text-sm">'
                + '<span class="text-gray-700 dark:text-gray-300 truncate">' + file.name + '</span>'
                + '<span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">' + role + '</span>'
                + '</li>';
        }).join('');

        if (files.length > 4) {
            productImages.value = '';
            list.innerHTML = '';
            fail('Quatre photos maximum.');
        } else if (files.length > 0 && files.length < 3) {
            fail('Ajoutez au moins 3 photos pour accélérer la vérification.');
        }

        refreshSummary();
    });

    ['certificate', 'receipt'].forEach(function (id) {
        document.getElementById(id).addEventListener('change', refreshSummary);
    });

    document.getElementById('serial_number').addEventListener('input', refreshSummary);

    nextBtn.addEventListener('click', function () {
        const error = validate(current);
        if (error) {
            fail(error);
            return;
        }

        const box = document.getElementById('stepError');
        if (box) {
            box.remove();
        }

        refreshSummary();
        show(current + 1);
    });

    prevBtn.addEventListener('click', function () {
        show(current - 1);
    });

    form.addEventListener('submit', function (event) {
        const error = validate(current);
        if (error) {
            event.preventDefault();
            fail(error);
        }
    });

    show(current);
})();
</script>
@endpush
@endsection
