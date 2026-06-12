<?php $__env->startSection('title', 'Vérifier - ' . $item->name); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <!-- Navigation -->
    <div class="mb-6">
        <a href="<?php echo e(route('expert.items.pending')); ?>" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la liste
        </a>
    </div>

    <!-- En-tête avec gradient -->
    <div class="bg-gradient-to-r from-indigo-500 to-primary-600 rounded-xl p-8 text-white mb-8 shadow-lg">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-shield-alt mr-3"></i>
                    Vérification d'article
                </h1>
                <p class="text-indigo-100">
                    Veuillez examiner cet article attentivement et décider de l'approuver ou de le rejeter
                </p>
            </div>
            <span class="px-4 py-2 bg-yellow-400 text-yellow-900 rounded-lg font-semibold">
                <i class="fas fa-clock mr-2"></i>
                En attente
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Colonne principale (Images et informations) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Images -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-images mr-3 text-primary-600"></i>
                        Images (<?php echo e(count($item->images ?? [])); ?>)
                    </h2>
                </div>

                <div class="p-6">
                    <?php if(!empty($item->images)): ?>
                        <?php
                            $imageUrls = $item->getImageUrls();
                        ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $imageUrls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $imageUrl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="relative group cursor-pointer rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                    <img src="<?php echo e($imageUrl); ?>" 
                                         class="w-full h-96 object-cover group-hover:brightness-110 transition"
                                         alt="Image <?php echo e($index + 1); ?>"
                                         onclick="openImageModal('<?php echo e($imageUrl); ?>')">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white text-3xl opacity-0 group-hover:opacity-100 transition"></i>
                                    </div>
                                    <div class="absolute top-3 right-3 bg-black bg-opacity-75 text-white px-3 py-1 rounded text-sm font-medium">
                                        <?php echo e($index + 1); ?>/<?php echo e(count($item->images)); ?>

                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <i class="fas fa-image text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">Aucune image disponible</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-align-left mr-3 text-primary-600"></i>
                        Description
                    </h2>
                </div>

                <div class="p-6">
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">
                        <?php echo e($item->description ?? 'Aucune description'); ?>

                    </p>
                </div>
            </div>

            <!-- Détails supplémentaires -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-info-circle mr-3 text-primary-600"></i>
                        Détails de l'article
                    </h2>
                </div>

                <div class="p-6 grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">Catégorie</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            <?php echo e($item->category?->name ?? 'N/A'); ?>

                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">Marque</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            <?php echo e($item->brand?->name ?? 'N/A'); ?>

                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">Prix</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            <?php echo e(number_format($item->price, 0, ',', ' ')); ?> FCFA
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">État</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">
                            <?php echo e($item->condition ?? 'N/A'); ?>

                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">Créé le</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            <?php echo e($item->created_at->format('d/m/Y H:i')); ?>

                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">Quantité disponible</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            <?php echo e($item->quantity ?? 1); ?>

                        </p>
                    </div>
                </div>
            </div>

            <!-- Informations du vendeur -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-user mr-3 text-primary-600"></i>
                        Informations du vendeur
                    </h2>
                </div>

                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <?php if($item->user->avatar): ?>
                            <img src="<?php echo e(asset('storage/' . $item->user->avatar)); ?>" 
                                 class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600"
                                 alt="<?php echo e($item->user->name); ?>">
                        <?php else: ?>
                            <div class="w-16 h-16 rounded-full bg-primary-600 flex items-center justify-center text-white text-2xl font-bold">
                                <?php echo e(substr($item->user->name, 0, 1)); ?>

                            </div>
                        <?php endif; ?>

                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <?php echo e($item->user->name); ?>

                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <i class="fas fa-envelope mr-2"></i>
                                <?php echo e($item->user->email); ?>

                            </p>
                            <?php if($item->user->phone): ?>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-phone mr-2"></i>
                                    <?php echo e($item->user->phone); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre latérale (Décision) -->
        <div class="lg:col-span-1">
            <!-- Formulaire de décision -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md sticky top-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-gavel mr-3 text-primary-600"></i>
                        Votre décision
                    </h2>
                </div>

                <form id="verificationForm" method="POST" action="<?php echo e(route('expert.items.submit-verification', $item)); ?>" class="p-6 space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="decision" id="decisionInput" value="">

                    <!-- Bouton Approuver -->
                    <button type="submit" 
                            onclick="setDecision('approved')"
                            class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2 group">
                        <i class="fas fa-check-circle group-hover:scale-110 transition"></i>
                        Approuver
                    </button>

                    <!-- Bouton Rejeter -->
                    <button type="button" 
                            onclick="toggleRejectForm()"
                            class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2 group">
                        <i class="fas fa-times-circle group-hover:scale-110 transition"></i>
                        Rejeter
                    </button>

                    <!-- Formulaire de rejet caché -->
                    <div id="rejectForm" class="hidden space-y-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Raison du rejet
                            </label>
                            <textarea id="rejectionReason" 
                                      name="rejection_reason" 
                                      placeholder="Veuillez expliquer pourquoi vous rejetez cet article..."
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white resize-none"></textarea>
                        </div>

                        <button type="submit" 
                                onclick="setDecision('rejected')"
                                class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i>
                            Confirmer le rejet
                        </button>

                        <button type="button" 
                                onclick="toggleRejectForm()"
                                class="w-full px-6 py-3 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-semibold transition">
                            Annuler
                        </button>
                    </div>
                </form>

                <!-- Critères de vérification -->
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-checklist mr-2 text-primary-600"></i>
                        Points de contrôle
                    </h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded" id="check1">
                            <label for="check1" class="text-gray-600 dark:text-gray-400 cursor-pointer">
                                Les images sont claires et de bonne qualité
                            </label>
                        </li>
                        <li class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded" id="check2">
                            <label for="check2" class="text-gray-600 dark:text-gray-400 cursor-pointer">
                                La description correspond aux images
                            </label>
                        </li>
                        <li class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded" id="check3">
                            <label for="check3" class="text-gray-600 dark:text-gray-400 cursor-pointer">
                                Le prix est raisonnable
                            </label>
                        </li>
                        <li class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded" id="check4">
                            <label for="check4" class="text-gray-600 dark:text-gray-400 cursor-pointer">
                                Le produit est authentique (selon votre expertise)
                            </label>
                        </li>
                        <li class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded" id="check5">
                            <label for="check5" class="text-gray-600 dark:text-gray-400 cursor-pointer">
                                Aucun contenu offensant ou illégal
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les images -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
    <div class="max-w-4xl w-full relative">
        <img id="modalImage" src="" class="w-full h-auto" alt="">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition">
            <i class="fas fa-times text-3xl"></i>
        </button>
    </div>
</div>

<script>
function setDecision(decision) {
    document.getElementById('decisionInput').value = decision;
}

function toggleRejectForm() {
    const rejectForm = document.getElementById('rejectForm');
    rejectForm.classList.toggle('hidden');
    if (!rejectForm.classList.contains('hidden')) {
        document.getElementById('rejectionReason').focus();
    }
}

function openImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Fermer le modal en cliquant en dehors
document.getElementById('imageModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Validation du formulaire
document.getElementById('verificationForm').addEventListener('submit', function(e) {
    const decision = document.getElementById('decisionInput').value;
    if (!decision) {
        e.preventDefault();
        alert('Veuillez faire une sélection : approuver ou rejeter');
        return;
    }

    if (decision === 'rejected') {
        const reason = document.getElementById('rejectionReason').value.trim();
        if (!reason) {
            e.preventDefault();
            alert('Veuillez fournir une raison pour le rejet');
            return;
        }
    }
});
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/expert/items/show-for-verification.blade.php ENDPATH**/ ?>