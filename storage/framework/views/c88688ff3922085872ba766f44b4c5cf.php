

<?php $__env->startSection('title', 'Détails de l\'item #' . $item->id); ?>

<?php $__env->startSection('page-title', 'Détails de l\'item'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto">
    <!-- Header avec actions -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="<?php echo e(route('admin.items.pending_verification')); ?>" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-2">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($item->name); ?></h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Créé le <?php echo e($item->created_at->format('d/m/Y à H:i')); ?>

                <?php if($item->verified_at): ?>
                    • Vérifié le <?php echo e($item->verified_at->format('d/m/Y à H:i')); ?>

                <?php endif; ?>
            </p>
        </div>

        <!-- Badge de statut -->
        <?php
            $statusConfig = [
                'approved' => ['class' => 'bg-green-100 text-green-800 border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:border-green-700', 'icon' => 'fa-check-circle', 'label' => 'Approuvé'],
                'pending' => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-700', 'icon' => 'fa-clock', 'label' => 'En attente'],
                'rejected' => ['class' => 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:border-red-700', 'icon' => 'fa-times-circle', 'label' => 'Rejeté'],
            ];
            $status = $statusConfig[$item->verification_status] ?? $statusConfig['pending'];
        ?>
        <div class="px-4 py-2 rounded-lg border-2 <?php echo e($status['class']); ?> flex items-center space-x-2">
            <i class="fas <?php echo e($status['icon']); ?>"></i>
            <span class="font-semibold"><?php echo e($status['label']); ?></span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Images -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-images mr-2 text-primary-600"></i>
                        Images (<?php echo e(count($item->images ?? [])); ?>)
                    </h3>
                    
                    <?php if(!empty($item->images)): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <?php $__currentLoopData = $item->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="relative group cursor-pointer" onclick="openImageModal('<?php echo e(asset('storage/' . $image)); ?>', '<?php echo e($item->name); ?> - Image <?php echo e($index + 1); ?>')">
                                    <img src="<?php echo e(asset('storage/' . $image)); ?>" 
                                         class="w-full h-48 object-cover rounded-lg border-2 border-gray-200 dark:border-gray-600 group-hover:border-primary-500 transition"
                                         alt="Image <?php echo e($index + 1); ?>">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition rounded-lg flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition"></i>
                                    </div>
                                    <div class="absolute top-2 left-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded text-xs">
                                        <?php echo e($index + 1); ?>/<?php echo e(count($item->images)); ?>

                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Aucune image disponible</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-align-left mr-2 text-primary-600"></i>
                        Description
                    </h3>
                    <div class="prose dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?php echo e($item->description); ?></p>
                    </div>
                </div>
            </div>

            <!-- Analyse IA -->
            <?php if($item->verification_details): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-robot mr-2 text-primary-600"></i>
                        Analyse IA
                        <?php if($item->verification_score): ?>
                            <span class="ml-auto text-2xl font-bold <?php echo e($item->verification_score >= 75 ? 'text-green-600' : ($item->verification_score >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                <?php echo e(number_format($item->verification_score, 0)); ?>/100
                            </span>
                        <?php endif; ?>
                    </h3>

                    <?php
                        $details = $item->verification_details;
                        $imageScore = $details['images']['score'] ?? 0;
                        $textScore = $details['text']['score'] ?? 0;
                        $coherenceScore = $details['coherence']['score'] ?? 0;
                    ?>

                    <div class="space-y-4">
                        <!-- Images Analysis -->
                        <div class="border-l-4 border-blue-500 pl-4 py-3 bg-blue-50 dark:bg-blue-900/20 rounded-r">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900 dark:text-white flex items-center">
                                    🖼️ Analyse des images (40%)
                                </h4>
                                <span class="text-lg font-bold <?php echo e($imageScore >= 70 ? 'text-green-600' : ($imageScore >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                    <?php echo e(number_format($imageScore, 1)); ?>/100
                                </span>
                            </div>
                            <?php if(isset($details['images']['issues']) && !empty($details['images']['issues'])): ?>
                                <ul class="space-y-1 mt-2">
                                    <?php $__currentLoopData = $details['images']['issues']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                            <span class="text-red-500 mr-2">⚠️</span>
                                            <span><?php echo e($issue); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-sm text-green-600 dark:text-green-400 mt-2">✓ Aucun problème détecté</p>
                            <?php endif; ?>
                        </div>

                        <!-- Text Analysis -->
                        <div class="border-l-4 border-purple-500 pl-4 py-3 bg-purple-50 dark:bg-purple-900/20 rounded-r">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900 dark:text-white flex items-center">
                                    📝 Analyse du texte (30%)
                                </h4>
                                <span class="text-lg font-bold <?php echo e($textScore >= 70 ? 'text-green-600' : ($textScore >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                    <?php echo e(number_format($textScore, 1)); ?>/100
                                </span>
                            </div>
                            <?php if(isset($details['text']['issues']) && !empty($details['text']['issues'])): ?>
                                <ul class="space-y-1 mt-2">
                                    <?php $__currentLoopData = $details['text']['issues']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                            <span class="text-red-500 mr-2">⚠️</span>
                                            <span><?php echo e($issue); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-sm text-green-600 dark:text-green-400 mt-2">✓ Aucun problème détecté</p>
                            <?php endif; ?>
                        </div>

                        <!-- Coherence Analysis -->
                        <div class="border-l-4 border-orange-500 pl-4 py-3 bg-orange-50 dark:bg-orange-900/20 rounded-r">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900 dark:text-white flex items-center">
                                    🔗 Analyse de cohérence (30%)
                                </h4>
                                <span class="text-lg font-bold <?php echo e($coherenceScore >= 70 ? 'text-green-600' : ($coherenceScore >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                    <?php echo e(number_format($coherenceScore, 1)); ?>/100
                                </span>
                            </div>
                            <?php if(isset($details['coherence']['issues']) && !empty($details['coherence']['issues'])): ?>
                                <ul class="space-y-1 mt-2">
                                    <?php $__currentLoopData = $details['coherence']['issues']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                            <span class="text-red-500 mr-2">⚠️</span>
                                            <span><?php echo e($issue); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-sm text-green-600 dark:text-green-400 mt-2">✓ Aucun problème détecté</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Admin Rejection Reason -->
                    <?php if(isset($details['admin_rejection'])): ?>
                        <div class="mt-4 border-l-4 border-red-500 pl-4 py-3 bg-red-50 dark:bg-red-900/20 rounded-r">
                            <h4 class="font-semibold text-red-800 dark:text-red-400 mb-2">❌ Motif de rejet</h4>
                            <p class="text-sm text-red-700 dark:text-red-300"><?php echo e($details['admin_rejection']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Informations principales -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-primary-600"></i>
                        Informations
                    </h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Prix</dt>
                            <dd class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                <?php echo e($item->currency_symbol ?? ''); ?> <?php echo e(number_format($item->price, 2, ',', ' ')); ?>

                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Catégorie</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo e($item->category->name ?? 'N/A'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Marque</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo e($item->brand->name ?? 'N/A'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Condition</dt>
                            <dd class="text-sm text-gray-900 dark:text-white capitalize"><?php echo e($item->condition ?? 'N/A'); ?></dd>
                        </div>
                        <?php if($item->size): ?>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Taille</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo e($item->size); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if($item->color): ?>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Couleur</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo e($item->color); ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- Vendeur -->
            <?php if($item->user): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-user mr-2 text-primary-600"></i>
                        Vendeur
                    </h3>
                    <div class="flex items-center space-x-3">
                        <img src="<?php echo e($item->user->avatar ?? asset('images/default-avatar.png')); ?>" 
                             class="w-12 h-12 rounded-full object-cover"
                             alt="<?php echo e($item->user->name); ?>">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white"><?php echo e($item->user->name); ?></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($item->user->email); ?></p>
                        </div>
                    </div>
                    <a href="<?php echo e(route('admin.users.show', $item->user)); ?>" 
                       class="mt-4 block text-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Voir le profil
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <?php if($item->verification_status === 'pending'): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-cogs mr-2 text-primary-600"></i>
                        Actions
                    </h3>
                    <div class="space-y-3">
                        <form action="<?php echo e(route('admin.items.approve', $item)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" 
                                    class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>
                                Approuver l'item
                            </button>
                        </form>
                        
                        <button type="button" 
                                onclick="openRejectModal(<?php echo e($item->id); ?>)"
                                class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            Rejeter l'item
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Historique -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-history mr-2 text-primary-600"></i>
                        Historique
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start">
                            <i class="fas fa-plus-circle text-blue-500 mr-2 mt-1"></i>
                            <div>
                                <p class="text-gray-900 dark:text-white font-medium">Création</p>
                                <p class="text-gray-500 dark:text-gray-400"><?php echo e($item->created_at->format('d/m/Y à H:i')); ?></p>
                            </div>
                        </div>
                        <?php if($item->verified_at): ?>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                            <div>
                                <p class="text-gray-900 dark:text-white font-medium">Vérification</p>
                                <p class="text-gray-500 dark:text-gray-400"><?php echo e($item->verified_at->format('d/m/Y à H:i')); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($item->updated_at && $item->updated_at != $item->created_at): ?>
                        <div class="flex items-start">
                            <i class="fas fa-edit text-orange-500 mr-2 mt-1"></i>
                            <div>
                                <p class="text-gray-900 dark:text-white font-medium">Dernière modification</p>
                                <p class="text-gray-500 dark:text-gray-400"><?php echo e($item->updated_at->format('d/m/Y à H:i')); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeRejectModal(event)">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Rejeter l'item</h3>
            
            <form id="rejectForm" action="<?php echo e(route('admin.items.reject', $item)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Motif du rejet (requis)
                    </label>
                    <textarea name="reason" 
                              id="rejectReason"
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              placeholder="Ex: Images de mauvaise qualité, description contenant du spam, incohérence entre images et description..."></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Le vendeur recevra cette raison par notification.</p>
                </div>
                
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium">
                        Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="relative max-w-6xl max-h-screen p-4">
        <button onclick="closeImageModal()" 
                class="absolute top-6 right-6 text-white hover:text-gray-300 transition z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div class="text-center">
            <img id="modalImage" src="" class="max-w-full max-h-screen object-contain mx-auto" alt="Image agrandie">
            <p id="modalImageCaption" class="text-white text-sm mt-4"></p>
        </div>
    </div>
</div>

<script>
function openRejectModal(itemId) {
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectReason').focus();
}

function closeRejectModal(event) {
    if (!event || event.target.id === 'rejectModal') {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openImageModal(imageUrl, caption) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalImageCaption');
    modalImage.src = imageUrl;
    modalCaption.textContent = caption || '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
        closeImageModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/admin/items/show.blade.php ENDPATH**/ ?>