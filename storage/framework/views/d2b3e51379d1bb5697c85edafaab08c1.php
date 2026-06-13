<?php $__env->startSection('title', 'Booster vos Produits'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Booster vos Produits</h1>
            <p class="text-gray-500 dark:text-gray-400">Augmentez la visibilité de vos produits et boostez vos ventes</p>
            <?php if(request()->has('item_id')): ?>
                <?php
                    $preselectedItem = \App\Models\Item::find(request('item_id'));
                ?>
                <?php if($preselectedItem): ?>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 mt-4 flex items-start gap-3">
                        <i class="fas fa-info-circle text-emerald-600 dark:text-emerald-400 mt-0.5"></i>
                        <div>
                            <span class="font-semibold text-emerald-800 dark:text-emerald-300">Article présélectionné :</span>
                            <span class="text-emerald-700 dark:text-emerald-400">"<?php echo e($preselectedItem->name); ?>" — Choisissez un type de boost pour cet article.</span>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <a href="<?php echo e(route('boost.dashboard')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 border border-primary text-primary font-medium rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
            <i class="fas fa-chart-bar"></i>Mon Dashboard
        </a>
    </div>

    <?php
        $walletBalance = auth()->user()->cdfWallet()?->balance ?? 0;
        $isLowBalance = $walletBalance < 1000;
    ?>
    <div class="rounded-2xl border p-6 mb-8 flex flex-col sm:flex-row sm:items-center gap-4 <?php echo e($isLowBalance ? 'bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-800' : 'bg-primary-50 dark:bg-primary-900/10 border-primary-100 dark:border-primary-800'); ?>">
        <div class="flex items-center gap-4 flex-grow">
            <div class="w-12 h-12 rounded-xl <?php echo e($isLowBalance ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400'); ?> flex items-center justify-center text-xl flex-shrink-0">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <h3 class="font-semibold <?php echo e($isLowBalance ? 'text-red-900 dark:text-red-200' : 'text-primary-900 dark:text-primary-200'); ?>">Solde de votre portefeuille</h3>
                <span class="text-2xl font-bold <?php echo e($isLowBalance ? 'text-red-700 dark:text-red-400' : 'text-primary-700 dark:text-primary-400'); ?>"><?php echo e(number_format($walletBalance, 0, ',', ' ')); ?> CDF</span>
                <?php if($isLowBalance): ?>
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>Solde faible — Rechargez pour acheter des boosts</p>
                <?php endif; ?>
            </div>
        </div>
        <a href="#" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-medium text-white transition-colors flex-shrink-0 <?php echo e($isLowBalance ? 'bg-red-600 hover:bg-red-700' : 'bg-primary hover:bg-primary-600'); ?>">
            <i class="fas fa-plus"></i>Recharger
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $boostTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $boostType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-200 flex flex-col" data-boost-type="<?php echo e($boostType->id); ?>">
            <div class="p-6 text-white relative" style="background: <?php echo e($boostType->color ?? '#3B82F6'); ?>;">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold flex items-center gap-3">
                        <i class="<?php echo e($boostType->icon ?? 'fas fa-star'); ?>"></i>
                        <?php echo e($boostType->display_name); ?>

                    </h3>
                    <?php if($boostType->is_premium): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-400 text-yellow-900">
                        <i class="fas fa-crown"></i> Premium
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="p-6 flex-grow flex flex-col">
                <p class="text-gray-500 dark:text-gray-400 mb-4"><?php echo e($boostType->description); ?></p>

                <div class="mb-4 space-y-2">
                    <?php
                        $userCurrency = auth()->user()->preferred_currency ?? 'CDF';
                        $price = $userCurrency === 'USD' ? $boostType->price_usd : $boostType->price_cdf;
                        $currencySymbol = $userCurrency === 'USD' ? '$' : 'CDF';
                    ?>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Prix de base</span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                            <?php if($userCurrency === 'USD'): ?>
                                $<?php echo e(number_format($price, 2)); ?>

                            <?php else: ?>
                                <?php echo e(number_format($price, 0, ',', ' ')); ?> CDF
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if($boostType->price_per_day > 0): ?>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Par jour</span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">+<?php echo e(number_format($boostType->price_per_day, 0, ',', ' ')); ?> <?php echo e($currencySymbol); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Durée</span>
                        <span class="text-gray-900 dark:text-white"><?php echo e($boostType->min_duration); ?>–<?php echo e($boostType->max_duration); ?> jours</span>
                    </div>
                </div>

                <?php if($boostType->benefits): ?>
                <div class="mb-4">
                    <h6 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Avantages</h6>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = json_decode($boostType->benefits, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $benefit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-emerald-500 mt-0.5 text-sm"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300"><?php echo e($benefit); ?></span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <div class="px-6 pb-6">
                <button class="w-full bg-primary hover:bg-primary-600 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 select-boost-btn" data-boost-type-id="<?php echo e($boostType->id); ?>">
                    <i class="fas fa-rocket"></i>Choisir ce boost
                </button>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full">
            <div class="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                <i class="fas fa-exclamation-circle text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h4 class="text-xl font-medium text-gray-600 dark:text-gray-400 mb-2">Aucun type de boost disponible</h4>
                <p class="text-gray-500 dark:text-gray-500">Veuillez contacter l'administrateur.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="productSelectionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity modal-overlay"></div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full mx-auto shadow-2xl transform transition-all modal-content border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-box text-primary"></i>Choisissez le produit à booster
                </h3>
                <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors close-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <form id="boostForm" class="hidden">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="selectedBoostType" name="boost_type_id">
                    <input type="hidden" id="selectedItemId" name="item_id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durée (jours)</label>
                            <select class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary focus:border-transparent transition-all" id="boostDuration" name="duration" required>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prix total</label>
                            <div class="flex">
                                <input type="text" id="totalPrice" class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-l-xl bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100" readonly>
                                <span class="px-4 py-2.5 bg-gray-100 dark:bg-gray-800 border border-l-0 border-gray-200 dark:border-gray-600 rounded-r-xl text-sm text-gray-700 dark:text-gray-300">CDF</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" class="px-5 py-2.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl font-medium transition-colors close-modal">Annuler</button>
                        <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary-600 text-white rounded-xl font-medium transition-colors flex items-center gap-2">
                            <i class="fas fa-credit-card"></i>Acheter le boost
                        </button>
                    </div>
                </form>

                <div id="productsList">
                    <div class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-primary border-t-transparent"></div>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Chargement...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.product-item {
    border: 2px solid transparent;
    border-radius: 12px;
    transition: all 0.2s ease;
    cursor: pointer;
    padding: 1rem;
    margin-bottom: 0.5rem;
}
.product-item:hover {
    border-color: var(--color-primary-500, #3B82F6);
    background-color: #F8FAFC;
}
.dark .product-item:hover {
    background-color: rgba(255,255,255,0.03);
}
.product-item.selected {
    border-color: var(--color-primary-500, #3B82F6);
    background-color: #EBF8FF;
}
.dark .product-item.selected {
    background-color: rgba(59,130,246,0.08);
}
.product-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 1rem;
}
.modal-overlay {
    transition: opacity 0.3s ease-in-out;
}
.modal-content {
    transition: all 0.3s ease-in-out;
    transform: scale(0.95);
    opacity: 0;
}
.modal-content.show {
    transform: scale(1);
    opacity: 1;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const WALLET_BALANCE = <?php echo e(auth()->user()->cdfWallet()?->balance ?? 0); ?>;

document.addEventListener('DOMContentLoaded', function() {
    let selectedBoostTypeId = null;
    let selectedItemId = null;

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg border-l-4 transform transition-all duration-500 translate-x-full';

        const colors = {
            success: 'border-emerald-500',
            error: 'border-red-500',
            warning: 'border-yellow-500',
            info: 'border-primary'
        };
        const icons = {
            success: 'fas fa-check-circle text-emerald-500',
            error: 'fas fa-exclamation-circle text-red-500',
            warning: 'fas fa-exclamation-triangle text-yellow-500',
            info: 'fas fa-info-circle text-primary'
        };

        notification.classList.add(colors[type] || colors.info);
        notification.innerHTML = `
            <div class="p-4 flex items-start gap-3">
                <i class="${icons[type] || icons.info} text-xl flex-shrink-0"></i>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 flex-1">${message}</p>
                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 flex-shrink-0" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    }

    function showModal() {
        const modal = document.getElementById('productSelectionModal');
        const modalContent = modal.querySelector('.modal-content');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => modalContent.classList.add('show'), 50);
    }

    function hideModal() {
        const modal = document.getElementById('productSelectionModal');
        const modalContent = modal.querySelector('.modal-content');
        modalContent.classList.remove('show');
        document.body.style.overflow = '';
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('close-modal')) {
            hideModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hideModal();
    });

    document.querySelectorAll('.select-boost-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedBoostTypeId = this.dataset.boostTypeId;
            loadUserProducts();
            showModal();
        });
    });

    function loadUserProducts() {
        const productsList = document.getElementById('productsList');
        productsList.innerHTML = `
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-primary border-t-transparent"></div>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Chargement...</p>
            </div>
        `;

        const urlParams = new URLSearchParams(window.location.search);
        const preselectedItemId = urlParams.get('item_id');

        fetch('<?php echo e(route("boost.user-items")); ?>', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.items.length > 0) {
                let html = `<h6 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Sélectionnez un produit</h6><div class="space-y-2">`;

                data.items.forEach(item => {
                    const imageUrl = item.images && item.images.length > 0
                        ? item.images[0].image_url
                        : 'https://via.placeholder.com/60';

                    const hasActiveBoost = item.active_boosts && item.active_boosts.length > 0;
                    const disabledClass = hasActiveBoost ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer';
                    const isPreselected = preselectedItemId && item.id == preselectedItemId;
                    const selectedClass = isPreselected && !hasActiveBoost ? 'selected' : '';
                    const checkIconClass = isPreselected && !hasActiveBoost ? '' : 'hidden';

                    let boostInfo = '';
                    if (hasActiveBoost) {
                        const boost = item.active_boosts[0];
                        const expiresAt = new Date(boost.expires_at).toLocaleDateString('fr-FR');
                        boostInfo = `<p class="text-sm text-yellow-600 dark:text-yellow-400"><i class="fas fa-star mr-1"></i>Boost actif jusqu'au ${expiresAt}</p>`;
                    }

                    html += `
                        <div class="product-item flex items-center gap-3 p-4 rounded-xl border-2 transition-all duration-200 ${disabledClass} ${selectedClass}"
                             data-item-id="${item.id}" ${hasActiveBoost ? 'data-disabled="true"' : ''}>
                            <img src="${imageUrl}" alt="${item.title}" class="w-14 h-14 object-cover rounded-lg flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <h6 class="font-medium text-gray-900 dark:text-white truncate">${item.title}</h6>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Prix: ${new Intl.NumberFormat('fr-FR').format(item.price)} CDF</p>
                                ${boostInfo}
                            </div>
                            <div class="flex items-center flex-shrink-0">
                                ${hasActiveBoost ? '<i class="fas fa-lock text-gray-400 mr-2"></i>' : ''}
                                <i class="fas fa-check-circle text-emerald-500 ${checkIconClass}"></i>
                            </div>
                        </div>
                    `;

                    if (isPreselected && !hasActiveBoost) {
                        selectedItemId = item.id;
                    }
                });

                html += `</div><p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Cliquez sur un produit pour le sélectionner</p>`;
                productsList.innerHTML = html;
                addProductSelectionEvents();

                if (preselectedItemId && selectedItemId) {
                    showBoostForm();
                }
            } else {
                productsList.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-box-open text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <h5 class="text-xl font-medium text-gray-600 dark:text-gray-400 mb-2">Aucun produit disponible</h5>
                        <p class="text-gray-500 dark:text-gray-500 mb-6">Vous n'avez pas de produits actifs dans votre catalogue.</p>
                        <a href="/items/create" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-600 text-white px-5 py-2.5 rounded-xl font-medium transition-colors">
                            <i class="fas fa-plus"></i>Ajouter un produit
                        </a>
                    </div>
                `;
            }
        })
        .catch(() => {
            productsList.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                    <p class="text-gray-500 dark:text-gray-400 mb-3">Erreur lors du chargement</p>
                    <button class="bg-primary-50 text-primary px-4 py-2 rounded-xl text-sm font-medium hover:bg-primary-100 transition-colors" onclick="loadUserProducts()">Réessayer</button>
                </div>
            `;
        });
    }

    function addProductSelectionEvents() {
        document.querySelectorAll('.product-item').forEach(item => {
            item.addEventListener('click', function() {
                if (this.dataset.disabled === 'true') {
                    showNotification('Ce produit a déjà un boost actif.', 'warning');
                    return;
                }
                document.querySelectorAll('.product-item').forEach(p => {
                    p.classList.remove('selected');
                    p.querySelector('.fa-check-circle').classList.add('hidden');
                });
                this.classList.add('selected');
                this.querySelector('.fa-check-circle').classList.remove('hidden');
                selectedItemId = this.dataset.itemId;
                showBoostForm();
            });
        });
    }

    function showBoostForm() {
        document.getElementById('selectedBoostType').value = selectedBoostTypeId;
        document.getElementById('selectedItemId').value = selectedItemId;
        loadDurationOptions();
        document.getElementById('boostForm').classList.remove('hidden');
        document.getElementById('productsList').style.opacity = '0.7';
    }

    function loadDurationOptions() {
        const durationSelect = document.getElementById('boostDuration');
        durationSelect.innerHTML = '<option value="">Chargement...</option>';

        fetch(`/boost/durations/${selectedBoostTypeId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            durationSelect.innerHTML = '';
            const durations = data.success && data.durations.length > 0 ? data.durations : [1, 3, 7, 14];
            durations.forEach(days => {
                const option = document.createElement('option');
                option.value = days;
                option.textContent = `${days} jour${days > 1 ? 's' : ''}`;
                durationSelect.appendChild(option);
            });
            durationSelect.value = durations[0];
            calculatePrice();
            durationSelect.addEventListener('change', calculatePrice);
        })
        .catch(() => {
            const fallback = [1, 3, 7, 14];
            durationSelect.innerHTML = '';
            fallback.forEach(days => {
                const option = document.createElement('option');
                option.value = days;
                option.textContent = `${days} jour${days > 1 ? 's' : ''}`;
                durationSelect.appendChild(option);
            });
            durationSelect.value = fallback[0];
            calculatePrice();
            durationSelect.addEventListener('change', calculatePrice);
        });
    }

    function calculatePrice() {
        const duration = document.getElementById('boostDuration').value;

        fetch('<?php echo e(route("boost.calculate-price")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ boost_type_id: selectedBoostTypeId, item_id: selectedItemId, duration: duration })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalPrice').value = new Intl.NumberFormat('fr-FR').format(data.price);
                const submitBtn = document.querySelector('#boostForm button[type="submit"]');
                if (data.price > WALLET_BALANCE) {
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Solde insuffisant';
                } else {
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitBtn.innerHTML = '<i class="fas fa-credit-card"></i> Acheter le boost';
                }
            }
        })
        .catch(() => showNotification('Erreur lors du calcul du prix', 'error'));
    }

    document.getElementById('boostForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const totalPriceText = document.getElementById('totalPrice').value;
        const totalPrice = parseInt(totalPriceText.replace(/[^\d]/g, ''));
        if (totalPrice > WALLET_BALANCE) {
            showNotification(`Solde insuffisant. Vous avez ${new Intl.NumberFormat('fr-FR').format(WALLET_BALANCE)} CDF.`, 'error');
            return;
        }

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
        submitBtn.disabled = true;

        fetch('<?php echo e(route("boost.purchase")); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json().catch(() => ({ success: false, message: 'Erreur serveur' })))
        .then(data => {
            if (data.success) {
                showNotification('Boost acheté avec succès !', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showNotification(data.message || 'Erreur lors de l\'achat', 'error');
            }
        })
        .catch(() => showNotification('Erreur inattendue', 'error'))
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/boost/index.blade.php ENDPATH**/ ?>