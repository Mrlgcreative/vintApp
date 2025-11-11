

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-purple-50/30">
    <div class="container mx-auto px-4 py-8 lg:py-16">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-8">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-purple-600 transition-colors">Accueil</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="<?php echo e(route('items.index')); ?>" class="hover:text-purple-600 transition-colors">Produits</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 font-medium"><?php echo e(Str::limit($item->name, 30)); ?></span>
        </nav>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
            <!-- Galerie d'images verticale -->
            <div class="hidden xl:block xl:col-span-1">
                <?php if($item->images && count($item->images) > 0): ?>
                    <div class="sticky top-20 flex flex-col items-center gap-2">
                        <?php $__currentLoopData = $item->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="thumbnail-item w-14 h-14 lg:w-16 lg:h-16 rounded-xl overflow-hidden border-2 transition-all duration-300 cursor-pointer relative 
                                <?php echo e($index === 0 ? 'border-purple-600 shadow-lg shadow-purple-600/25 scale-105' : 'border-gray-200 hover:border-purple-300'); ?>"
                                data-index="<?php echo e($index); ?>" onclick="changeMainImage('<?php echo e(Storage::url($image)); ?>', this)">
                                <img src="<?php echo e(Storage::url($image)); ?>" 
                                     class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" 
                                     alt="Miniature <?php echo e($index + 1); ?>">
                                <div class="absolute inset-0 bg-gradient-to-br from-purple-600/10 to-purple-700/15 opacity-0 transition-opacity duration-300
                                    <?php echo e($index === 0 ? 'opacity-100' : 'hover:opacity-100'); ?>"></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Images mobiles horizontales -->
            <div class="xl:hidden mb-4">
                <?php if($item->images && count($item->images) > 0): ?>
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        <?php $__currentLoopData = $item->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="thumbnail-item flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all duration-300 cursor-pointer 
                                <?php echo e($index === 0 ? 'border-purple-600 shadow-lg shadow-purple-600/25' : 'border-gray-200'); ?>"
                                data-index="<?php echo e($index); ?>" onclick="changeMainImage('<?php echo e(Storage::url($image)); ?>', this)">
                                <img src="<?php echo e(Storage::url($image)); ?>" 
                                     class="w-full h-full object-cover transition-transform duration-300" 
                                     alt="Miniature <?php echo e($index + 1); ?>">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Image principale -->
            <div class="xl:col-span-6 order-2 xl:order-1">
                <div class="relative bg-white rounded-2xl lg:rounded-3xl p-4 lg:p-6 shadow-xl shadow-purple-600/10 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                    <?php if($item->images && count($item->images) > 0): ?>
                        <img id="mainProductImg" 
                             src="<?php echo e(Storage::url($item->images[0])); ?>" 
                             class="w-full h-64 sm:h-80 lg:h-[400px] xl:h-[500px] object-contain rounded-xl lg:rounded-2xl transition-all duration-300" 
                             alt="<?php echo e($item->name); ?>">
                        <div class="absolute inset-4 lg:inset-6 bg-black/70 backdrop-blur-sm rounded-xl lg:rounded-2xl flex items-center justify-center opacity-0 hover:opacity-100 transition-all duration-300 cursor-zoom-in">
                            <i class="fas fa-search-plus text-white text-3xl lg:text-5xl animate-pulse"></i>
                        </div>
                    <?php else: ?>
                        <div class="h-64 sm:h-80 lg:h-[400px] xl:h-[500px] flex flex-col items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl lg:rounded-2xl text-gray-500">
                            <i class="fas fa-image text-4xl lg:text-6xl mb-4"></i>
                            <p class="text-base lg:text-lg font-medium">Aucune image disponible</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card produit -->
            <div class="xl:col-span-5 order-1 xl:order-2">
                <div class="sticky top-4 lg:top-6 bg-white rounded-2xl lg:rounded-3xl p-6 lg:p-8 shadow-xl shadow-purple-600/10 transform transition-all duration-300 animate-fade-in border border-gray-100/50">
                    <!-- En-tête avec titre et bouton favori -->
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex-1 pr-3 lg:pr-4">
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-3 lg:mb-4"><?php echo e($item->name); ?></h1>
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl bg-gradient-to-r from-purple-50 to-purple-100 text-purple-700 border border-purple-200/50 text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                    <i class="fas fa-tag mr-1.5 lg:mr-2 text-xs lg:text-sm"></i>
                                    <?php echo e($item->category->name); ?>

                                </span>
                                <?php if($item->brand): ?>
                                    <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl bg-gradient-to-r from-indigo-50 to-indigo-100 text-indigo-600 border border-indigo-200/50 text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                        <i class="fas fa-copyright mr-1.5 lg:mr-2 text-xs lg:text-sm"></i>
                                        <?php echo e($item->brand->name); ?>

                                    </span>
                                <?php endif; ?>
                                <?php
                                    $conditionClass = match($item->condition) {
                                        'new' => 'from-emerald-50 to-emerald-100 text-emerald-600 border-emerald-200/50',
                                        'used', 'like_new' => 'from-amber-50 to-amber-100 text-amber-600 border-amber-200/50',
                                        default => 'from-gray-50 to-gray-100 text-gray-600 border-gray-200/50'
                                    };
                                ?>
                                <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl bg-gradient-to-r <?php echo e($conditionClass); ?> text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                    <i class="fas fa-certificate mr-1.5 lg:mr-2 text-xs lg:text-sm"></i>
                                    <?php echo e(ucfirst(str_replace('_', ' ', $item->condition))); ?>

                                </span>
                                
                                
                                <?php if($item->isVerified()): ?>
                                    <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl bg-gradient-to-r from-green-50 to-emerald-100 text-green-700 border border-green-200/50 text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                        <i class="fas fa-shield-alt mr-1.5 lg:mr-2 text-xs lg:text-sm"></i>
                                        <?php if($item->authenticity_badge_type === 'expert_certified'): ?>
                                            Certifié Expert
                                        <?php else: ?>
                                            Vérifié VintApp
                                        <?php endif; ?>
                                    </span>
                                <?php elseif($item->authenticityCheck && $item->authenticityCheck->status === 'pending'): ?>
                                    <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl bg-gradient-to-r from-yellow-50 to-amber-100 text-yellow-700 border border-yellow-200/50 text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                        <i class="fas fa-clock mr-1.5 lg:mr-2 text-xs lg:text-sm animate-pulse"></i>
                                        Vérification en cours
                                    </span>
                                <?php elseif($item->canRequestVerification()): ?>
                                    <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl bg-gradient-to-r from-blue-50 to-blue-100 text-blue-600 border border-blue-200/50 text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                        <i class="fas fa-question-circle mr-1.5 lg:mr-2 text-xs lg:text-sm"></i>
                                        Non vérifié
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if(auth()->guard()->check()): ?>
                            <button class="favorite-btn w-10 h-10 lg:w-12 lg:h-12 rounded-full border-2 border-purple-200/50 bg-white flex items-center justify-center transition-all duration-300 hover:bg-red-50 hover:border-red-400 hover:scale-110 flex-shrink-0" 
                                data-item-id="<?php echo e($item->id); ?>">
                                <i class="fas fa-heart text-red-500 text-sm lg:text-lg transition-transform duration-300 hover:scale-125"></i>
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Section prix -->
                    <div class="bg-gradient-to-r from-purple-50/50 to-purple-100/50 p-4 lg:p-6 rounded-xl lg:rounded-2xl border-2 border-purple-200/30 mb-4 lg:mb-6">
                        <div class="flex items-center justify-between flex-wrap gap-3 lg:gap-4">
                            <span class="text-3xl sm:text-4xl lg:text-5xl font-black bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                                <?php echo e($item->formatted_price); ?>

                            </span>
                            <div class="flex items-center">
                                <?php if($item->quantity > 0): ?>
                                    <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                                    <span class="text-emerald-600 font-semibold text-sm lg:text-base">En stock</span>
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                    <span class="text-red-600 font-semibold text-sm lg:text-base">Rupture de stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Métadonnées du produit -->
                    <div class="bg-gray-50 p-4 lg:p-6 rounded-xl lg:rounded-2xl border border-purple-200/20 mb-4 lg:mb-6">
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
                            <div class="bg-white p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-purple-600 to-purple-700 rounded-md lg:rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-eye text-white text-xs lg:text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Vues</p>
                                    <p class="text-gray-900 font-bold text-sm lg:text-base"><?php echo e($item->views); ?></p>
                                </div>
                            </div>
                            
                            <div class="bg-white p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-purple-600 to-purple-700 rounded-md lg:rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-boxes text-white text-xs lg:text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Quantité</p>
                                    <p class="text-gray-900 font-bold text-sm lg:text-base"><?php echo e($item->quantity); ?></p>
                                </div>
                            </div>

                            <?php if($item->color): ?>
                            <div class="bg-white p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-purple-600 to-purple-700 rounded-md lg:rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-palette text-white text-xs lg:text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Couleur</p>
                                    <p class="text-gray-900 font-bold text-sm lg:text-base"><?php echo e($item->color); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if($item->size): ?>
                            <div class="bg-white p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-purple-600 to-purple-700 rounded-md lg:rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-ruler text-white text-xs lg:text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Taille</p>
                                    <div class="text-gray-900 font-bold text-sm lg:text-base">
                                        <span class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-2 py-1 rounded text-xs font-semibold">
                                            <?php echo e($item->size); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if($item->item_number): ?>
                            <div class="bg-white p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-purple-600 to-purple-700 rounded-md lg:rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-barcode text-white text-xs lg:text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">N° Article</p>
                                    <p class="text-gray-900 font-bold text-sm lg:text-base"><?php echo e($item->item_number); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="bg-white p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-purple-600 to-purple-700 rounded-md lg:rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-calendar-alt text-white text-xs lg:text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Publié le</p>
                                    <p class="text-gray-900 font-bold text-sm lg:text-base"><?php echo e($item->created_at->format('d/m/Y')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section panier -->
                    <div class="bg-gray-50 p-6 rounded-2xl border border-purple-200/20 mb-6">
                        <form method="POST" action="<?php echo e(route('cart.add', $item->id)); ?>" id="addToCartForm">
                            <?php echo csrf_field(); ?>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Quantité</label>
                                <div class="flex items-center max-w-xs">
                                    <button type="button" onclick="decrementQuantity()" 
                                        class="w-11 h-11 bg-white border-2 border-purple-200/50 text-purple-600 rounded-xl flex items-center justify-center font-semibold transition-all duration-300 hover:bg-purple-600 hover:text-white hover:border-purple-600 hover:scale-105">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" name="quantity" id="quantityInput" value="1" min="1" max="<?php echo e(max($item->quantity, 1)); ?>" 
                                        class="flex-1 h-11 border-2 border-purple-200/50 text-center font-bold text-gray-900 text-lg focus:border-purple-600 focus:ring-4 focus:ring-purple-600/20 outline-none transition-all duration-300" <?php echo e($item->quantity == 0 ? 'disabled' : ''); ?>>
                                    <button type="button" onclick="incrementQuantity()" 
                                        class="w-11 h-11 bg-white border-2 border-purple-200/50 text-purple-600 rounded-xl flex items-center justify-center font-semibold transition-all duration-300 hover:bg-purple-600 hover:text-white hover:border-purple-600 hover:scale-105">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <?php if($item->quantity == 0): ?>
                                    <p class="text-red-500 text-sm mt-2 font-medium">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Article en rupture de stock
                                    </p>
                                <?php elseif($item->quantity <= 5): ?>
                                    <p class="text-amber-500 text-sm mt-2 font-medium">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Attention : plus que <?php echo e($item->quantity); ?> en stock
                                    </p>
                                <?php endif; ?>
                            </div>
                            <button type="submit" id="addToCartBtn" 
                                class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white font-bold text-lg py-4 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl relative overflow-hidden group">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-500"></div>
                                <i class="fas fa-shopping-cart mr-3"></i>
                                <span>Ajouter au panier</span>
                            </button>
                        </form>
                    </div>

                    
                    <?php if(auth()->guard()->check()): ?>
                        <?php if($item->user_id === auth()->id()): ?>
                            <?php if($item->canRequestVerification()): ?>
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-200/50 mb-6">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-shield-alt text-blue-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-lg font-bold text-gray-900 mb-2">Authentifiez votre produit</h4>
                                            <p class="text-gray-600 mb-4">
                                                Obtenez le badge <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Vérifié VintApp</span> 
                                                pour rassurer les acheteurs et vendre plus rapidement.
                                            </p>
                                            <div class="flex flex-wrap gap-3">
                                                <a href="<?php echo e(route('authenticity.request', $item)); ?>" 
                                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                                    <i class="fas fa-certificate mr-2"></i>
                                                    Demander la vérification
                                                </a>
                                                <span class="text-sm text-gray-600 flex items-center">
                                                    À partir de $<?php echo e(number_format(5.00, 2)); ?>

                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif($item->authenticityCheck): ?>
                                <div class="bg-white border border-gray-200 p-6 rounded-2xl mb-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-4">
                                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <?php if($item->authenticityCheck->isApproved()): ?>
                                                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                                                <?php elseif($item->authenticityCheck->isRejected()): ?>
                                                    <i class="fas fa-times-circle text-red-600 text-lg"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-clock text-yellow-600 text-lg"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-bold text-gray-900 mb-1">Vérification d'authenticité</h4>
                                                <p class="text-gray-600 mb-2"><?php echo e($item->authenticityCheck->getStatusLabel()); ?></p>
                                                <?php if($item->authenticityCheck->final_decision_at): ?>
                                                    <p class="text-sm text-gray-500">
                                                        Terminée le <?php echo e($item->authenticityCheck->final_decision_at->format('d/m/Y')); ?>

                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <a href="<?php echo e(route('authenticity.status', $item)); ?>" 
                                           class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            Voir détails →
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Description -->
                    <div class="bg-gray-50 p-6 rounded-2xl border-l-4 border-purple-600 mb-6">
                        <h5 class="text-lg font-bold text-gray-900 flex items-center mb-4">
                            <i class="fas fa-align-left text-purple-600 mr-3"></i>
                            Description
                        </h5>
                        <p class="text-gray-600 leading-relaxed"><?php echo e($item->description); ?></p>
                    </div>

                    <!-- Spécifications -->
                    <?php if($item->specifications && is_array($item->specifications) && count($item->specifications) > 0): ?>
                        <div class="bg-gray-50 p-6 rounded-2xl border border-purple-200/20 mb-6">
                            <h5 class="text-lg font-bold text-gray-900 flex items-center mb-4">
                                <i class="fas fa-list-ul text-purple-600 mr-3"></i>
                                Spécifications
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php $__currentLoopData = $item->specifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bg-white p-4 rounded-xl flex justify-between items-center transition-all duration-300 hover:shadow-md hover:translate-x-1">
                                        <span class="font-semibold text-gray-600 text-sm"><?php echo e(is_string($key) ? ucfirst($key) : ''); ?></span>
                                        <span class="font-bold text-gray-900"><?php echo e(is_string($value) ? $value : (is_array($value) ? json_encode($value) : '')); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Vendeur -->
                    <div class="bg-gradient-to-r from-purple-50/30 to-purple-100/30 p-6 rounded-2xl border border-purple-200/20 mb-6">
                        <h5 class="text-lg font-bold text-gray-900 flex items-center mb-4">
                            <i class="fas fa-user-circle text-purple-600 mr-3"></i>
                            Vendeur
                        </h5>
                        <div class="bg-white p-5 rounded-xl transition-all duration-300 hover:shadow-lg">
                            <div class="flex items-center">
                                <div class="w-15 h-15 rounded-full bg-gradient-to-r from-purple-600 to-purple-700 flex items-center justify-center text-white text-2xl mr-4 flex-shrink-0">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-lg font-bold text-gray-900 mb-1"><?php echo e($item->user->name); ?></div>
                                    <small class="text-gray-500 text-sm">
                                        <i class="fas fa-calendar mr-1"></i>
                                        Membre depuis <?php echo e($item->user->created_at->format('M Y')); ?>

                                    </small>
                                </div>
                                <div class="flex items-center text-lg font-bold text-gray-900">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span><?php echo e($averageRating > 0 ? $averageRating : 'Aucun avis'); ?></span>
                                    <?php if($totalReviews > 0): ?>
                                        <span class="text-sm text-gray-500 ml-2">(<?php echo e($totalReviews); ?> avis)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Réductions disponibles -->
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(Auth::id() !== $item->user_id): ?>
                            <div id="discountSection" class="hidden bg-gradient-to-r from-emerald-50 to-emerald-100 border-2 border-emerald-300/50 rounded-2xl p-6 mb-6 animate-fade-in">
                                <div class="flex gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl flex-shrink-0">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="font-bold text-emerald-600 mb-2">Réduction disponible !</h6>
                                        <div id="discountInfo" class="text-gray-600 text-sm"></div>
                                    </div>
                                </div>
                                <button onclick="applyDiscount()" 
                                    class="w-full bg-emerald-500 text-white font-semibold py-3 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                    <i class="fas fa-check mr-2"></i>
                                    Appliquer la réduction
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Boutons d'action -->
                    <div class="space-y-4">
                        <?php if(auth()->guard()->check()): ?>
                            <?php if(Auth::id() !== $item->user_id): ?>
                                <!-- Bouton demande de réduction -->
                                <form id="contactForm" method="POST" action="<?php echo e(route('contact.seller', $item)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="button" onclick="openModal('contactModal')"
                                        class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-4 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl mb-3">
                                        <i class="fas fa-percentage mr-3"></i>
                                        Demander une réduction
                                    </button>
                                </form>
                                
                                <!-- Bouton contact vendeur -->
                                <button onclick="contactSeller()" 
                                    class="w-full bg-white text-purple-600 border-2 border-purple-600 font-semibold py-4 rounded-2xl transition-all duration-300 hover:bg-purple-600 hover:text-white hover:-translate-y-1 hover:shadow-lg">
                                    <i class="fas fa-envelope mr-3"></i>
                                    Contacter le vendeur
                                </button>
                            <?php else: ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <a href="<?php echo e(route('items.edit', $item)); ?>" 
                                        class="bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold py-4 rounded-2xl text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg inline-flex items-center justify-center">
                                        <i class="fas fa-edit mr-3"></i>
                                        Modifier
                                    </a>
                                    <button onclick="deleteItem()" 
                                        class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-4 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                        <i class="fas fa-trash mr-3"></i>
                                        Supprimer
                                    </button>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>" 
                                class="block w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white font-bold text-lg py-5 rounded-2xl text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-3xl">
                                <i class="fas fa-sign-in-alt mr-3"></i>
                                Se connecter pour acheter
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section des commentaires et avis -->
        <?php if($reviews->count() > 0 || $totalReviews > 0): ?>
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 lg:p-8 mb-8 lg:mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900">
                        <i class="fas fa-star text-yellow-400 mr-3"></i>
                        Avis clients
                    </h2>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">
                            <?php echo e($averageRating > 0 ? $averageRating : '0'); ?>/5
                        </div>
                        <div class="text-sm text-gray-500"><?php echo e($totalReviews); ?> avis</div>
                    </div>
                </div>

                <?php if($reviews->count() > 0): ?>
                    <div class="space-y-6">
                        <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="border border-gray-100 rounded-2xl p-6 bg-gradient-to-br from-gray-50/50 to-white">
                                <div class="flex items-start space-x-4">
                                    <!-- Avatar utilisateur -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center text-white font-semibold text-lg">
                                            <?php echo e(strtoupper(substr($review->reviewer->name ?? 'U', 0, 1))); ?>

                                        </div>
                                    </div>
                                    
                                    <!-- Contenu du commentaire -->
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-semibold text-gray-900">
                                                <?php echo e($review->reviewer->name ?? 'Utilisateur anonyme'); ?>

                                            </h4>
                                            <div class="flex items-center space-x-2">
                                                <!-- Étoiles -->
                                                <div class="flex items-center">
                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                        <?php if($i <= $review->rating): ?>
                                                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                                                        <?php else: ?>
                                                            <i class="far fa-star text-gray-300 text-sm"></i>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </div>
                                                <span class="text-sm text-gray-500">
                                                    <?php echo e($review->created_at->diffForHumans()); ?>

                                                </span>
                                            </div>
                                        </div>
                                        
                                        <?php if($review->comment): ?>
                                            <p class="text-gray-700 leading-relaxed"><?php echo e($review->comment); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php if($totalReviews > 2): ?>
                        <div class="mt-6 text-center">
                            <p class="text-gray-500 text-sm">
                                Affichage de 2 avis sur <?php echo e($totalReviews); ?>

                            </p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comment-alt text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500">Aucun commentaire pour le moment</p>
                        <p class="text-sm text-gray-400 mt-2">Soyez le premier à donner votre avis après achat</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Articles similaires -->
        <?php if($similarItems->count() > 0): ?>
            <div class="mt-12 lg:mt-20 bg-white rounded-2xl lg:rounded-3xl p-6 lg:p-12 shadow-xl shadow-purple-600/5">
                <div class="text-center mb-6 lg:mb-8">
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-heart text-purple-600 mr-2 lg:mr-3"></i>
                        Vous aimerez aussi
                    </h3>
                    <p class="text-gray-600 text-base lg:text-lg">Découvrez d'autres articles similaires</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                    <?php $__currentLoopData = $similarItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $similarItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white rounded-xl lg:rounded-2xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 group">
                            <div class="relative overflow-hidden h-32 sm:h-40 lg:h-48 bg-gray-100">
                                <?php if($similarItem->images && count($similarItem->images) > 0): ?>
                                    <img src="<?php echo e(Storage::url($similarItem->images[0])); ?>" 
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" 
                                         alt="<?php echo e($similarItem->name); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-2xl lg:text-4xl">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute inset-0 bg-purple-600/80 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <a href="<?php echo e(route('items.show', $similarItem)); ?>" 
                                        class="w-8 h-8 lg:w-12 lg:h-12 bg-white text-purple-600 rounded-full flex items-center justify-center text-sm lg:text-lg transition-transform duration-300 hover:scale-125">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="p-3 lg:p-5">
                                <h6 class="font-bold text-gray-900 mb-2 lg:mb-3 min-h-[2rem] lg:min-h-[2.5rem] leading-tight text-sm lg:text-base">
                                    <?php echo e(Str::limit($similarItem->name, 35)); ?>

                                </h6>
                                <div class="flex justify-between items-center">
                                    <span class="text-base lg:text-xl font-bold bg-gradient-to-r from-purple-600 to-purple-700 bg-clip-text text-transparent">
                                        <?php echo e($similarItem->formatted_price); ?>

                                    </span>
                                    <a href="<?php echo e(route('items.show', $similarItem)); ?>" 
                                        class="text-purple-600 font-semibold text-xs lg:text-sm transition-all duration-300 hover:text-purple-800 hover:translate-x-1">
                                        Voir <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de demande de réduction -->
<?php if(auth()->guard()->check()): ?>
    <?php if(Auth::id() !== $item->user_id): ?>
        <!-- Overlay Background -->
        <div id="contactModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 invisible transition-all duration-300">
            <!-- Modal Container -->
            <div class="bg-white rounded-2xl lg:rounded-3xl overflow-hidden shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300">
                <!-- Header -->
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6 lg:p-8 flex justify-between items-start">
                    <div>
                        <h5 class="text-xl lg:text-2xl font-bold mb-1" id="contactModalLabel">
                            <i class="fas fa-percentage mr-2 lg:mr-3"></i>
                            Demander une réduction
                        </h5>
                        <p class="text-purple-100 text-sm">Négociez directement avec le vendeur</p>
                    </div>
                    <button type="button" onclick="closeModal('contactModal')" 
                        class="w-8 h-8 lg:w-9 lg:h-9 rounded-full bg-white/20 border-0 text-white flex items-center justify-center transition-all duration-300 hover:bg-white/30 hover:scale-110" 
                        aria-label="Fermer">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <!-- Body -->
                <div class="p-4 lg:p-6">
                    <!-- Aperçu du produit -->
                    <div class="bg-gray-50 p-4 lg:p-6 rounded-xl lg:rounded-2xl mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                            <div>
                                <?php if($item->images && count($item->images) > 0): ?>
                                    <img src="<?php echo e(Storage::url($item->images[0])); ?>" 
                                         class="w-full h-24 lg:h-32 object-cover rounded-lg lg:rounded-xl" 
                                         alt="<?php echo e($item->name); ?>"
                                         loading="lazy">
                                <?php endif; ?>
                            </div>
                            <div class="md:col-span-2">
                                <h6 class="font-bold text-gray-900 mb-2 text-sm lg:text-base"><?php echo e($item->name); ?></h6>
                                <p class="text-gray-600 text-xs lg:text-sm mb-4"><?php echo e(Str::limit($item->description, 120)); ?></p>
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                    <span class="text-lg lg:text-2xl font-bold bg-gradient-to-r from-purple-600 to-purple-700 bg-clip-text text-transparent">
                                        <?php echo e($item->formatted_price); ?>

                                    </span>
                                    <span class="bg-purple-100 text-purple-700 px-2 py-1 lg:px-3 lg:py-1 rounded-lg lg:rounded-xl text-xs lg:text-sm font-semibold w-fit">
                                        <i class="fas fa-tag mr-1"></i>
                                        <?php echo e($item->category->name); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message personnalisé -->
                    <div class="mb-6">
                        <label for="customMessage" class="block font-semibold text-gray-900 mb-2 text-sm lg:text-base">
                            <i class="fas fa-comment-dots text-purple-600 mr-2"></i>
                            Votre message (optionnel)
                        </label>
                        <textarea name="custom_message" 
                                  id="customMessage" 
                                  rows="4" 
                                  class="w-full border-2 border-purple-200/50 rounded-lg lg:rounded-xl p-3 lg:p-4 transition-all duration-300 focus:border-purple-600 focus:ring-4 focus:ring-purple-600/20 outline-none text-sm resize-none"
                                  placeholder="Bonjour, je suis très intéressé(e) par votre produit. Serait-il possible de négocier le prix ?"></textarea>
                        <small class="text-gray-500 text-xs lg:text-sm mt-2 block">
                            <i class="fas fa-info-circle mr-1"></i>
                            Un message automatique sera envoyé si vous laissez ce champ vide
                        </small>
                    </div>

                    <!-- Informations sur le processus -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/50 rounded-xl lg:rounded-2xl p-4 lg:p-6">
                        <div class="font-bold text-blue-600 mb-4 flex items-center text-sm lg:text-base">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Comment ça fonctionne ?
                        </div>
                        <ul class="space-y-2">
                            <li class="flex items-start text-gray-600 text-xs lg:text-sm">
                                <i class="fas fa-check text-blue-600 mr-2 lg:mr-3 mt-1 text-xs"></i>
                                Votre demande est envoyée instantanément au vendeur
                            </li>
                            <li class="flex items-start text-gray-600 text-xs lg:text-sm">
                                <i class="fas fa-check text-blue-600 mr-2 lg:mr-3 mt-1 text-xs"></i>
                                Le vendeur peut vous proposer une réduction personnalisée
                            </li>
                            <li class="flex items-start text-gray-600 text-xs lg:text-sm">
                                <i class="fas fa-check text-blue-600 mr-2 lg:mr-3 mt-1 text-xs"></i>
                                La réduction est appliquée automatiquement si acceptée
                            </li>
                            <li class="flex items-start text-gray-600 text-xs lg:text-sm">
                                <i class="fas fa-check text-blue-600 mr-2 lg:mr-3 mt-1 text-xs"></i>
                                Vous recevez une notification de la réponse
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="bg-gray-50 p-4 lg:p-6 flex flex-col sm:flex-row gap-3 lg:gap-4">
                    <button type="button" onclick="closeModal('contactModal')" 
                        class="flex-1 bg-white text-gray-700 border-2 border-gray-300 font-semibold py-2.5 lg:py-3 rounded-lg lg:rounded-xl transition-all duration-300 hover:bg-gray-50 hover:-translate-y-0.5 text-sm lg:text-base">
                        <i class="fas fa-times mr-2"></i>
                        Annuler
                    </button>
                    <button type="button" id="submitDiscountBtn" onclick="submitDiscountRequest()" 
                        class="flex-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-2.5 lg:py-3 rounded-lg lg:rounded-xl transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg text-sm lg:text-base">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Envoyer la demande
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
// ===== GESTION DU MODAL TAILWIND =====
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('opacity-0', 'invisible');
        modal.classList.add('opacity-100', 'visible');
        
        const modalContent = modal.querySelector('.bg-white');
        if (modalContent) {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }
        
        // Empêcher le scroll du body
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('opacity-100', 'visible');
        modal.classList.add('opacity-0', 'invisible');
        
        const modalContent = modal.querySelector('.bg-white');
        if (modalContent) {
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
        }
        
        // Rétablir le scroll du body
        document.body.style.overflow = '';
    }
}

// Fermer le modal en cliquant en dehors
document.addEventListener('click', function(e) {
    const modal = document.getElementById('contactModal');
    if (modal && e.target === modal) {
        closeModal('contactModal');
    }
});

// Fermer le modal avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal('contactModal');
    }
});

// ===== GALERIE D'IMAGES =====
function changeMainImage(src, element) {
    const mainImg = document.getElementById('mainProductImg');
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    
    // Retirer active de tous les thumbnails
    thumbnails.forEach(thumb => {
        thumb.classList.remove('border-purple-600', 'shadow-lg', 'shadow-purple-600/25', 'scale-105');
        thumb.classList.add('border-transparent');
    });
    
    // Ajouter active au thumbnail cliqué
    element.classList.remove('border-transparent');
    element.classList.add('border-purple-600', 'shadow-lg', 'shadow-purple-600/25', 'scale-105');
    
    // Changer l'image avec transition
    mainImg.style.opacity = '0';
    setTimeout(() => {
        mainImg.src = src;
        mainImg.style.opacity = '1';
    }, 150);
}

// ===== GESTION DE LA QUANTITÉ =====
function incrementQuantity() {
    const input = document.getElementById('quantityInput');
    const max = parseInt(input.getAttribute('max'));
    const currentValue = parseInt(input.value) || 1;
    
    if (currentValue < max && max > 0) {
        input.value = currentValue + 1;
    }
}

function decrementQuantity() {
    const input = document.getElementById('quantityInput');
    const min = parseInt(input.getAttribute('min'));
    const currentValue = parseInt(input.value) || 1;
    
    if (currentValue > min && currentValue > 0) {
        input.value = currentValue - 1;
    }
}

// ===== CONTACT VENDEUR =====
function contactSeller() {
    window.location.href = `/messages/conversation/<?php echo e($item->user_id); ?>?item=<?php echo e($item->id); ?>`;
}

// ===== DEMANDE DE RÉDUCTION =====
function submitDiscountRequest() {
    const form = document.getElementById('contactForm');
    const customMessage = document.getElementById('customMessage').value;
    const submitBtn = document.getElementById('submitDiscountBtn');
    
    // Ajouter le message personnalisé au formulaire
    if (customMessage.trim()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'custom_message';
        input.value = customMessage;
        form.appendChild(input);
    }
    
    // Désactiver le bouton et afficher l'état de chargement
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi en cours...';
    
    // Afficher une notification
    showNotification('Envoi de votre demande en cours...', 'info');
    
    // Soumettre le formulaire après un court délai
    setTimeout(() => {
        form.submit();
    }, 500);
}

// ===== VÉRIFIER LES RÉDUCTIONS DISPONIBLES =====
<?php if(auth()->guard()->check()): ?>
    <?php if(Auth::id() !== $item->user_id): ?>
        document.addEventListener('DOMContentLoaded', function() {
            checkAvailableDiscounts();
        });
        
        function checkAvailableDiscounts() {
            fetch(`/discounts/item/<?php echo e($item->id); ?>/available`)
                .then(response => response.json())
                .then(discounts => {
                    if (discounts.length > 0) {
                        const discount = discounts[0];
                        showDiscountSection(discount);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification des réductions:', error);
                });
        }
        
        function showDiscountSection(discount) {
            const section = document.getElementById('discountSection');
            const info = document.getElementById('discountInfo');
            
            const savings = discount.original_price - discount.final_price;
            const formattedSavings = new Intl.NumberFormat('fr-FR').format(savings);
            const formattedFinalPrice = new Intl.NumberFormat('fr-FR').format(discount.final_price);
            const currencySymbol = '<?php echo e($item->currency_symbol); ?>';
            
            info.innerHTML = `
                <div class="mb-2">
                    <strong class="block mb-1">Réduction de ${discount.discount_percentage}% !</strong>
                    <small class="text-gray-500 block">
                        Prix original: <span class="line-through">${currencySymbol} ${new Intl.NumberFormat('fr-FR').format(discount.original_price)}</span>
                    </small>
                    <small class="block mt-1">
                        <span class="font-bold text-emerald-600">
                            Nouveau prix: ${currencySymbol} ${formattedFinalPrice}
                        </span>
                    </small>
                    <small class="text-gray-500 block mt-1">
                        Économie: <span class="font-bold text-emerald-600">${currencySymbol} ${formattedSavings}</span>
                    </small>
                    <small class="text-gray-500 block mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        Valable jusqu'au ${new Date(discount.expires_at).toLocaleDateString('fr-FR')}
                    </small>
                </div>
            `;
            
            section.classList.remove('hidden');
            section.dataset.discountId = discount.id;
            
            updateAddToCartButton(true);
        }
        
        function applyDiscount() {
            const section = document.getElementById('discountSection');
            const discountId = section.dataset.discountId;
            
            if (!discountId) return;
            
            fetch(`/discounts/${discountId}/apply`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const priceElement = document.querySelector('.bg-gradient-to-r.from-purple-600.to-purple-800.bg-clip-text.text-transparent');
                    if (priceElement && priceElement.parentElement) {
                        const currencySymbol = '<?php echo e($item->currency_symbol); ?>';
                        priceElement.parentElement.innerHTML = `
                            <div class="flex items-baseline justify-between flex-wrap gap-4">
                                <span class="text-2xl lg:text-3xl line-through text-gray-400"><?php echo e($item->formatted_price); ?></span>
                                <span class="text-4xl lg:text-5xl font-black text-emerald-600">
                                    ${currencySymbol} ${new Intl.NumberFormat('fr-FR').format(data.final_price)}
                                </span>
                                <span class="bg-emerald-500 text-white px-3 py-1 rounded-lg text-sm font-semibold">
                                    -${data.discount_percentage}%
                                </span>
                            </div>
                        `;
                    }
                    
                    section.classList.add('hidden');
                    updateAddToCartButton(true);
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.error || 'Erreur lors de l\'application de la réduction', 'danger');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'danger');
            });
        }
    <?php endif; ?>
<?php endif; ?>

// ===== SUPPRESSION D'ARTICLE =====
function deleteItem() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.')) {
        fetch(`/items/<?php echo e($item->id); ?>`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Article supprimé avec succès', 'success');
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1500);
            } else {
                showNotification('Erreur lors de la suppression', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Une erreur est survenue', 'danger');
        });
    }
}

// ===== GESTION DES FAVORIS =====
const favoriteBtn = document.querySelector('.favorite-btn');
if (favoriteBtn) {
    favoriteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const itemId = this.dataset.itemId;
        
        fetch(`/items/${itemId}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const icon = this.querySelector('i');
                if (data.is_favorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.classList.add('bg-red-50', 'border-red-400');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.classList.remove('bg-red-50', 'border-red-400');
                }
                showNotification(data.message, 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Une erreur est survenue', 'danger');
        });
    });
}

// ===== SYSTÈME DE NOTIFICATIONS =====
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    
    const iconMap = {
        success: 'check-circle',
        danger: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    const colorMap = {
        success: 'emerald',
        danger: 'red',
        warning: 'amber',
        info: 'blue'
    };
    
    const color = colorMap[type];
    
    notification.className = `fixed top-5 right-5 z-50 min-w-80 max-w-md bg-white rounded-2xl shadow-2xl p-5 border-l-4 border-${color}-500 animate-slide-in-right`;
    
    notification.innerHTML = `
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-${color}-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-${iconMap[type]} text-${color}-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 leading-relaxed">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                class="w-6 h-6 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-all duration-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.add('animate-slide-out-right');
            setTimeout(() => notification.remove(), 400);
        }
    }, 5000);
}

// ===== MISE À JOUR DU BOUTON PANIER =====
function updateAddToCartButton(hasDiscount) {
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (addToCartBtn && hasDiscount) {
        addToCartBtn.innerHTML = `
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-500"></div>
            <i class="fas fa-shopping-cart mr-3"></i>
            <span>Ajouter avec réduction</span>
            <i class="fas fa-tag ml-3"></i>
        `;
        addToCartBtn.className = 'w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold text-lg py-4 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl relative overflow-hidden group';
    }
}

// Animations CSS personnalisées via Tailwind
document.head.insertAdjacentHTML('beforeend', `
<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-in-right {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slide-out-right {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-slide-in-right {
    animation: slide-in-right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-slide-out-right {
    animation: slide-out-right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.shadow-3xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
</style>
`);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/items/show.blade.php ENDPATH**/ ?>