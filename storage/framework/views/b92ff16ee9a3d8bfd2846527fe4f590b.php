<?php $__env->startSection('title', 'Articles vérifiés par les experts'); ?>
<?php $__env->startSection('page-title', 'Articles vérifiés par les experts'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg text-sm flex items-center gap-2">
        <i class="fas fa-check-circle flex-shrink-0"></i>
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4 sm:mb-6">
    <?php
        $totalItems = $items->total();
        $highScore = $items->filter(fn($i) => ($i->verification_score ?? 0) >= 75)->count();
        $midScore = $items->filter(fn($i) => ($i->verification_score ?? 0) >= 50 && ($i->verification_score ?? 0) < 75)->count();
        $lowScore = $items->filter(fn($i) => ($i->verification_score ?? 0) < 50)->count();
    ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-box text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total vérifiés</p>
                <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white"><?php echo e($totalItems); ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-star text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Score ≥ 75</p>
                <p class="text-lg sm:text-xl font-bold text-green-600"><?php echo e($highScore); ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Score 50-74</p>
                <p class="text-lg sm:text-xl font-bold text-yellow-600"><?php echo e($midScore); ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-times-circle text-red-600 dark:text-red-400"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Score < 50</p>
                <p class="text-lg sm:text-xl font-bold text-red-600"><?php echo e($lowScore); ?></p>
            </div>
        </div>
    </div>
</div>

<?php if($items->isEmpty()): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-2xl text-gray-400 dark:text-gray-500"></i>
        </div>
        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-1">Aucun article récemment vérifié</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Les articles vérifiés par les experts apparaîtront ici.</p>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $score = $item->verification_score ?? 0;
                $scoreColor = $score >= 75 ? 'text-green-600 dark:text-green-400' : ($score >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
                $scoreBg = $score >= 75 ? 'bg-green-100 dark:bg-green-900/30 border-green-200 dark:border-green-800' : ($score >= 50 ? 'bg-yellow-100 dark:bg-yellow-900/30 border-yellow-200 dark:border-yellow-800' : 'bg-red-100 dark:bg-red-900/30 border-red-200 dark:border-red-800');
                $scoreRing = $score >= 75 ? 'ring-green-500' : ($score >= 50 ? 'ring-yellow-500' : 'ring-red-500');
            ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden" x-data="{ showDetails: false }">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col lg:flex-row lg:gap-5">
                        
                        <div class="flex-shrink-0 mb-4 lg:mb-0" x-data="{ modal: false, modalSrc: '' }">
                            <div class="grid grid-cols-4 sm:grid-cols-4 lg:grid-cols-2 gap-1.5 w-full lg:w-56">
                                <?php $__currentLoopData = array_slice($item->images ?? [], 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="relative group cursor-pointer aspect-square" @click="modal = true; modalSrc = '<?php echo e(asset('storage/' . $image)); ?>'">
                                        <img src="<?php echo e(asset('storage/' . $image)); ?>" 
                                             class="w-full h-full object-cover rounded-lg border border-gray-200 dark:border-gray-600 group-hover:border-primary-500 transition" loading="lazy"
                                             alt="Image <?php echo e($index + 1); ?>">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition rounded-lg"></div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($item->images ?? []) > 4): ?>
                                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 flex items-center justify-center">
                                        <span class="text-lg font-bold text-gray-500 dark:text-gray-400">+<?php echo e(count($item->images) - 4); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div x-show="modal" x-transition.opacity @click="modal = false" @keydown.escape.window="modal = false"
                                 class="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4" style="display:none">
                                <button @click="modal = false" class="absolute top-4 right-4 text-white/80 hover:text-white transition">
                                    <i class="fas fa-times text-2xl"></i>
                                </button>
                                <img :src="modalSrc" class="max-w-full max-h-[90vh] object-contain rounded-lg" alt="Image agrandie">
                            </div>
                        </div>

                        
                        <div class="flex-1 min-w-0">
                            
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="min-w-0">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white truncate">
                                        <?php echo e($item->name); ?>

                                        <span class="text-xs text-gray-400 font-normal ml-1">#<?php echo e($item->id); ?></span>
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        <?php if($item->brand): ?>
                                            <span class="inline-flex items-center gap-1"><i class="fas fa-tag"></i><?php echo e($item->brand->name); ?></span>
                                        <?php endif; ?>
                                        <?php if($item->category): ?>
                                            <span>•</span>
                                            <span><?php echo e($item->category->name); ?></span>
                                        <?php endif; ?>
                                        <span>•</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-200"><?php echo e($item->currency_symbol ?? ''); ?> <?php echo e(number_format($item->price, 2, ',', ' ')); ?></span>
                                    </div>
                                </div>
                                
                                <div class="flex-shrink-0 w-16 h-16 sm:w-[72px] sm:h-[72px] rounded-full border-2 <?php echo e($scoreBg); ?> flex flex-col items-center justify-center ring-2 <?php echo e($scoreRing); ?> ring-offset-2 ring-offset-white dark:ring-offset-gray-800">
                                    <span class="text-xl sm:text-2xl font-bold <?php echo e($scoreColor); ?> leading-none"><?php echo e(number_format($score, 0)); ?></span>
                                    <span class="text-[9px] uppercase tracking-wider <?php echo e($scoreColor); ?> opacity-75">Score</span>
                                </div>
                            </div>

                            
                            <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-3"><?php echo e($item->description); ?></p>

                            
                            <div class="flex flex-wrap items-center gap-3 mb-3 text-xs">
                                <?php if($item->user): ?>
                                    <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                        <?php if($item->user->avatar): ?>
                                            <img src="<?php echo e($item->user->avatar_url ?? $item->user->avatar); ?>" class="w-5 h-5 rounded-full" alt="">
                                        <?php else: ?>
                                            <div class="w-5 h-5 rounded-full bg-primary-500 flex items-center justify-center text-white text-[10px] font-semibold"><?php echo e(substr($item->user->name, 0, 1)); ?></div>
                                        <?php endif; ?>
                                        <span>Vendeur : <span class="font-medium text-gray-900 dark:text-white"><?php echo e($item->user->name); ?></span></span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex items-center gap-1.5 text-green-600 dark:text-green-400">
                                    <i class="fas fa-user-shield"></i>
                                    <span>Vérifié par : <span class="font-medium"><?php echo e($item->verifiedBy->name ?? 'Expert'); ?></span></span>
                                    <?php if($item->verified_at): ?>
                                        <span class="text-gray-400 dark:text-gray-500">• <?php echo e($item->verified_at->diffForHumans()); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if($item->status === 'active'): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                        <i class="fas fa-globe"></i>Publié
                                    </span>
                                <?php endif; ?>
                            </div>

                            
                            <?php if($item->verification_details): ?>
                                <?php
                                    $details = $item->verification_details;
                                    $imageScore = $details['images']['score'] ?? 0;
                                    $textScore = $details['text']['score'] ?? 0;
                                    $coherenceScore = $details['coherence']['score'] ?? 0;
                                ?>
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden mb-3">
                                    <button type="button" @click="showDetails = !showDetails"
                                            class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition flex items-center justify-between text-sm">
                                        <span class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                            <i class="fas fa-chart-bar text-gray-400"></i>Détails de l'analyse IA
                                        </span>
                                        <div class="flex items-center gap-3">
                                            
                                            <div class="hidden sm:flex items-center gap-2 text-xs">
                                                <span class="<?php echo e($imageScore >= 70 ? 'text-green-600' : ($imageScore >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                                    <i class="fas fa-image mr-0.5"></i><?php echo e(number_format($imageScore, 0)); ?>

                                                </span>
                                                <span class="<?php echo e($textScore >= 70 ? 'text-green-600' : ($textScore >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                                    <i class="fas fa-font mr-0.5"></i><?php echo e(number_format($textScore, 0)); ?>

                                                </span>
                                                <span class="<?php echo e($coherenceScore >= 70 ? 'text-green-600' : ($coherenceScore >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                                    <i class="fas fa-link mr-0.5"></i><?php echo e(number_format($coherenceScore, 0)); ?>

                                                </span>
                                            </div>
                                            <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" :class="showDetails && 'rotate-180'"></i>
                                        </div>
                                    </button>
                                    <div x-show="showDetails" x-transition style="display:none">
                                        <div class="px-3 py-3 space-y-3 bg-white dark:bg-gray-800">
                                            
                                            <div class="border-l-4 border-blue-500 pl-3">
                                                <div class="flex items-center justify-between mb-1">
                                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                        <i class="fas fa-image text-blue-500"></i>Images <span class="font-normal text-gray-400">(40%)</span>
                                                    </h4>
                                                    <span class="text-sm font-bold <?php echo e($imageScore >= 70 ? 'text-green-600' : ($imageScore >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                                        <?php echo e(number_format($imageScore, 1)); ?>/100
                                                    </span>
                                                </div>
                                                <?php if(isset($details['images']['issues']) && !empty($details['images']['issues']) && is_array($details['images']['issues'])): ?>
                                                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                                        <?php $__currentLoopData = $details['images']['issues']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $imageKey => $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li class="flex items-start gap-1.5">
                                                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 flex-shrink-0"></i>
                                                                <?php if(is_array($issue)): ?>
                                                                    <div>
                                                                        <strong><?php echo e($imageKey); ?>:</strong>
                                                                        <?php $__currentLoopData = $issue['issues'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $singleIssue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <div class="ml-3">• <?php echo e($singleIssue); ?></div>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <span><?php echo e($issue); ?></span>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                <?php else: ?>
                                                    <p class="text-xs text-green-600 dark:text-green-400 flex items-center gap-1"><i class="fas fa-check"></i>Aucun problème détecté</p>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="border-l-4 border-purple-500 pl-3">
                                                <div class="flex items-center justify-between mb-1">
                                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                        <i class="fas fa-font text-purple-500"></i>Texte <span class="font-normal text-gray-400">(30%)</span>
                                                    </h4>
                                                    <span class="text-sm font-bold <?php echo e($textScore >= 70 ? 'text-green-600' : ($textScore >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                                        <?php echo e(number_format($textScore, 1)); ?>/100
                                                    </span>
                                                </div>
                                                <?php if(isset($details['text']['issues']) && !empty($details['text']['issues']) && is_array($details['text']['issues'])): ?>
                                                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                                        <?php $__currentLoopData = $details['text']['issues']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li class="flex items-start gap-1.5">
                                                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 flex-shrink-0"></i>
                                                                <span><?php echo e(is_array($issue) ? implode(', ', $issue) : $issue); ?></span>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                <?php else: ?>
                                                    <p class="text-xs text-green-600 dark:text-green-400 flex items-center gap-1"><i class="fas fa-check"></i>Aucun problème détecté</p>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="border-l-4 border-orange-500 pl-3">
                                                <div class="flex items-center justify-between mb-1">
                                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                        <i class="fas fa-link text-orange-500"></i>Cohérence <span class="font-normal text-gray-400">(30%)</span>
                                                    </h4>
                                                    <span class="text-sm font-bold <?php echo e($coherenceScore >= 70 ? 'text-green-600' : ($coherenceScore >= 50 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                                        <?php echo e(number_format($coherenceScore, 1)); ?>/100
                                                    </span>
                                                </div>
                                                <?php if(isset($details['coherence']['issues']) && !empty($details['coherence']['issues']) && is_array($details['coherence']['issues'])): ?>
                                                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                                        <?php $__currentLoopData = $details['coherence']['issues']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li class="flex items-start gap-1.5">
                                                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 flex-shrink-0"></i>
                                                                <span><?php echo e(is_array($issue) ? implode(', ', $issue) : $issue); ?></span>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                <?php else: ?>
                                                    <p class="text-xs text-green-600 dark:text-green-400 flex items-center gap-1"><i class="fas fa-check"></i>Aucun problème détecté</p>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if(isset($details['admin_rejection']) && is_array($details['admin_rejection'])): ?>
                                                <div class="border-l-4 border-red-500 pl-3 bg-red-50 dark:bg-red-900/20 p-3 rounded-r-lg">
                                                    <h4 class="text-sm font-semibold text-red-800 dark:text-red-400 mb-1 flex items-center gap-1.5">
                                                        <i class="fas fa-ban"></i>Motif de rejet précédent
                                                    </h4>
                                                    <p class="text-xs text-red-700 dark:text-red-300"><?php echo e($details['admin_rejection']['reason'] ?? 'Non spécifié'); ?></p>
                                                    <div class="text-[10px] text-red-600 dark:text-red-400 mt-1.5 flex flex-wrap gap-x-2">
                                                        <span>Rejeté par : <?php echo e($details['admin_rejection']['rejected_by'] ?? 'N/A'); ?></span>
                                                        <span>•</span>
                                                        <span>Le : <?php echo e($details['admin_rejection']['rejected_at'] ?? 'N/A'); ?></span>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="<?php echo e(route('admin.items.show', $item)); ?>" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-eye text-xs"></i>Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($items->hasPages()): ?>
        <div class="mt-6">
            <?php echo e($items->appends(request()->query())->links()); ?>

        </div>
    <?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/admin/items/pending_verification.blade.php ENDPATH**/ ?>