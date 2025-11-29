

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-primary-50/30 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300 mb-8">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-primary-600 transition-colors">Accueil</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="<?php echo e(route('items.index')); ?>" class="hover:text-primary-600 transition-colors">Produits</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 dark:text-white font-medium">Vendre un article</span>
        </nav>

        <div class="max-w-4xl mx-auto">
            <!-- Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl shadow-xl shadow-primary-600/10 border border-gray-100/50 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-6 lg:p-8">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white dark:bg-gray-800/20 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-plus text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold">Vendre un article</h1>
                            <p class="text-primary-100 mt-1">Créez votre annonce en quelques étapes</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="p-6 lg:p-8">
                    <form method="POST" action="<?php echo e(route('items.store')); ?>" enctype="multipart/form-data" id="createItemForm" class="space-y-8">
                        <?php echo csrf_field(); ?>

                        <!-- Informations de base -->
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Informations de base</h2>
                            </div>

                            <!-- Nom et Prix -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Nom de l'article -->
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-tag text-primary-600 mr-2"></i>
                                        Nom de l'article *
                                    </label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo e(old('name')); ?>" 
                                           placeholder="Ex: iPhone 14 Pro Max 256GB"
                                           required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?php echo e($message); ?>

                                        </p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Prix -->
                                <div>
                                    <label for="price" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-dollar-sign text-primary-600 mr-2"></i>
                                        Prix *
                                    </label>
                                    <input type="number" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="price" 
                                           name="price" 
                                           value="<?php echo e(old('price')); ?>" 
                                           placeholder="0.00" 
                                           step="0.01" 
                                           min="0" 
                                           required>
                                    <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?php echo e($message); ?>

                                        </p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-align-left text-primary-600 mr-2"></i>
                                    Description détaillée *
                                </label>
                                <textarea class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 resize-none <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Décrivez votre article en détail pour attirer les acheteurs..."
                                          required><?php echo e(old('description')); ?></textarea>
                                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        <?php echo e($message); ?>

                                    </p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Décrivez votre article en détail pour attirer les acheteurs
                                </p>
                            </div>
                        </div>

                        <!-- Détails du produit -->
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Détails du produit</h2>
                            </div>

                            <!-- Couleur, Taille, Numéro -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Couleur -->
                                <div>
                                    <label for="color" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-palette text-primary-600 mr-2"></i>
                                        Couleur
                                    </label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="color" 
                                           name="color" 
                                           value="<?php echo e(old('color')); ?>" 
                                           placeholder="Ex: Noir, Blanc, Rouge">
                                    <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?php echo e($message); ?>

                                        </p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Taille -->
                                <div>
                                    <label for="size" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-ruler text-primary-600 mr-2"></i>
                                        Taille
                                    </label>
                                    <select class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['size'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="size"
                                            name="size">
                                        <option value="">Choisir une taille</option>
                                        <option value="XS" <?php echo e(old('size') == 'XS' ? 'selected' : ''); ?>>XS</option>
                                        <option value="S" <?php echo e(old('size') == 'S' ? 'selected' : ''); ?>>S</option>
                                        <option value="M" <?php echo e(old('size') == 'M' ? 'selected' : ''); ?>>M</option>
                                        <option value="L" <?php echo e(old('size') == 'L' ? 'selected' : ''); ?>>L</option>
                                        <option value="XL" <?php echo e(old('size') == 'XL' ? 'selected' : ''); ?>>XL</option>
                                        <option value="XXL" <?php echo e(old('size') == 'XXL' ? 'selected' : ''); ?>>XXL</option>
                                        <option value="XXXL" <?php echo e(old('size') == 'XXXL' ? 'selected' : ''); ?>>XXXL</option>
                                    </select>
                                    <?php $__errorArgs = ['size'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?php echo e($message); ?>

                                        </p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Numéro d'article -->
                                <div>
                                    <label for="item_number" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-hashtag text-primary-600 mr-2"></i>
                                        Numéro d'article
                                    </label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['item_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="item_number" 
                                           name="item_number" 
                                           value="<?php echo e(old('item_number')); ?>" 
                                           placeholder="Ex: ABC123">
                                    <?php $__errorArgs = ['item_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?php echo e($message); ?>

                                        </p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <!-- Catégorie et Marque -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Catégorie -->
                                <div>
                                    <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-folder text-primary-600 mr-2"></i>
                                        Catégorie *
                                    </label>
                                    <select class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">Choisir une catégorie</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($category->id); ?>" 
                                                    <?php echo e(old('category_id') == $category->id ? 'selected' : ''); ?>>
                                                <?php echo e($category->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?php echo e($message); ?>

                                        </p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Marque -->
                                <div>
                                    <label for="brand_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-trademark text-primary-600 mr-2"></i>
                                        Marque
                                    </label>
                                    <select class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['brand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="brand_id" 
                                            name="brand_id">
                                        <option value="">Choisir une marque (optionnel)</option>
                                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($brand->id); ?>" 
                                                    <?php echo e(old('brand_id') == $brand->id ? 'selected' : ''); ?>>
                                                <?php echo e($brand->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['brand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?php echo e($message); ?>

                                        </p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <!-- Devise et État -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Devise -->
                                <div>
                                    <label for="currency" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-money-bill text-primary-600 mr-2"></i>
                                        Devise *
                                    </label>
                                    <select class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="currency" 
                                            name="currency" 
                                            required>
                                        <option value="">Choisir la devise</option>
                                        <option value="USD" <?php echo e(old('currency') == 'USD' ? 'selected' : ''); ?>>USD ($)</option>
                                        <option value="CDF" <?php echo e(old('currency') == 'CDF' ? 'selected' : ''); ?>>CDF (FC)</option>
                                    </select>
                                    <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?php echo e($message); ?>

                                        </p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- État -->
                                <div>
                                    <label for="condition" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-star text-primary-600 mr-2"></i>
                                        État de l'article *
                                    </label>
                                    <select class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="condition" 
                                            name="condition" 
                                            required>
                                        <option value="">Choisir l'état</option>
                                        <option value="new" <?php echo e(old('condition') == 'new' ? 'selected' : ''); ?>>Neuf</option>
                                        <option value="like_new" <?php echo e(old('condition') == 'like_new' ? 'selected' : ''); ?>>Comme neuf</option>
                                        <option value="good" <?php echo e(old('condition') == 'good' ? 'selected' : ''); ?>>Bon état</option>
                                        <option value="fair" <?php echo e(old('condition') == 'fair' ? 'selected' : ''); ?>>État correct</option>
                                        <option value="poor" <?php echo e(old('condition') == 'poor' ? 'selected' : ''); ?>>Usé</option>
                                    </select>
                                    <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?php echo e($message); ?>

                                        </p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Quantité -->
                                <div>
                                    <label for="quantity" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-boxes text-primary-600 mr-2"></i>
                                        Quantité disponible *
                                    </label>
                                    <input type="number" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="quantity" 
                                           name="quantity" 
                                           value="<?php echo e(old('quantity', 1)); ?>" 
                                           min="1" 
                                           required>
                                    <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <?php echo e($message); ?>

                                        </p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-images"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Photos de l'article</h2>
                            </div>

                            <div>
                                <label for="images" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-upload text-primary-600 mr-2"></i>
                                    Sélectionner des images
                                </label>
                                <div class="relative">
                                    <input type="file" 
                                           class="w-full px-4 py-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="images" 
                                           name="images[]" 
                                           multiple 
                                           accept="image/*">
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div class="text-center">
                                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                            <p class="text-gray-500 dark:text-gray-400">Glissez vos images ici ou cliquez pour sélectionner</p>
                                        </div>
                                    </div>
                                </div>
                                <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        <?php echo e($message); ?>

                                    </p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Vous pouvez sélectionner plusieurs images. Formats acceptés : JPG, PNG, GIF (max 2MB par image)
                                </p>
                                
                                <!-- Prévisualisation des images -->
                                <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 hidden">
                                    <!-- Les prévisualisations seront ajoutées ici -->
                                </div>
                            </div>
                        </div>

                        <!-- Spécifications -->
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Spécifications (optionnel)</h2>
                            </div>

                            <div id="specificationsContainer" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end specification-row">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Nom de la spécification</label>
                                        <input type="text" 
                                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300" 
                                               name="specifications[key][]" 
                                               placeholder="Ex: Couleur, Matériau">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Valeur</label>
                                        <input type="text" 
                                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300" 
                                               name="specifications[value][]" 
                                               placeholder="Ex: Rouge, Coton">
                                    </div>
                                    <div>
                                        <button type="button" class="w-full bg-red-100 hover:bg-red-200 text-red-600 px-4 py-3 rounded-xl transition-all duration-300 remove-specification">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="bg-primary-100 hover:bg-primary-200 text-primary-600 px-6 py-3 rounded-xl transition-all duration-300 font-semibold" id="addSpecification">
                                <i class="fas fa-plus mr-2"></i>
                                Ajouter une spécification
                            </button>
                        </div>

                        <!-- Boutons -->
                        <div class="flex flex-col sm:flex-row justify-between gap-4 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <a href="<?php echo e(route('items.index')); ?>" class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-6 py-3 rounded-xl transition-all duration-300 font-semibold text-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white px-8 py-3 rounded-xl transition-all duration-300 font-bold text-lg shadow-lg hover:shadow-xl hover:-translate-y-1">
                                <i class="fas fa-check mr-2"></i>
                                Publier l'article
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prévisualisation des images
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    
    // Enhanced image validation: check size and dimensions before preview
    window.imageValidationResults = [];
    imageInput.addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        imagePreview.classList.add('hidden');
        window.imageValidationResults = [];

        const files = Array.from(this.files || []);
        if (files.length === 0) return;

        imagePreview.classList.remove('hidden');

        files.forEach((file, index) => {
            const result = { ok: true, issues: [] };

            // Size check (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                result.ok = false;
                result.issues.push('Taille > 2MB');
            }

            if (!file.type.startsWith('image/')) {
                result.ok = false;
                result.issues.push('Type non image');
            }

            const reader = new FileReader();
            reader.onload = function(ev) {
                const img = new Image();
                img.onload = function() {
                    // Dimension check
                    if (img.width < 200 || img.height < 200) {
                        result.ok = false;
                        result.issues.push(`Dimensions trop petites (${img.width}x${img.height})`);
                    }

                    // Add preview with badge
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${ev.target.result}" 
                             class="w-full h-32 object-cover rounded-xl shadow-md group-hover:shadow-lg transition-all duration-300"
                             alt="Prévisualisation ${index + 1}">
                        <button type="button" class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-all duration-300 remove-image opacity-0 group-hover:opacity-100" 
                                data-index="${index}">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    `;

                    // Badge issues
                    if (!result.ok) {
                        const badge = document.createElement('div');
                        badge.className = 'absolute bottom-2 left-2 bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded';
                        badge.textContent = 'Vérifier';
                        div.appendChild(badge);
                    }

                    imagePreview.appendChild(div);
                    window.imageValidationResults[index] = result;
                };
                img.onerror = function() {
                    result.ok = false;
                    result.issues.push('Image illisible');
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <div class="w-full h-32 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500">Fichier image invalide</div>
                        <button type="button" class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-all duration-300 remove-image opacity-0 group-hover:opacity-100" 
                                data-index="${index}">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    `;
                    imagePreview.appendChild(div);
                    window.imageValidationResults[index] = result;
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    });

    // Supprimer une image de la prévisualisation
    imagePreview.addEventListener('click', function(e) {
        if (e.target.closest('.remove-image')) {
            const button = e.target.closest('.remove-image');
            const index = parseInt(button.dataset.index);
            const dt = new DataTransfer();
            const input = document.getElementById('images');
            const { files } = input;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }
            
            input.files = dt.files;
            button.closest('.relative').remove();
            
            if (imagePreview.children.length === 0) {
                imagePreview.classList.add('hidden');
            }
        }
    });

    // Gestion des spécifications
    const addSpecBtn = document.getElementById('addSpecification');
    const specContainer = document.getElementById('specificationsContainer');
    
    addSpecBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'grid grid-cols-1 md:grid-cols-5 gap-4 items-end specification-row';
        newRow.innerHTML = `
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Nom de la spécification</label>
                <input type="text" 
                       class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300" 
                       name="specifications[key][]" 
                       placeholder="Ex: Couleur, Matériau">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Valeur</label>
                <input type="text" 
                       class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none transition-all duration-300" 
                       name="specifications[value][]" 
                       placeholder="Ex: Rouge, Coton">
            </div>
            <div>
                <button type="button" class="w-full bg-red-100 hover:bg-red-200 text-red-600 px-4 py-3 rounded-xl transition-all duration-300 remove-specification">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        specContainer.appendChild(newRow);
    });

    // Supprimer une spécification
    specContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-specification')) {
            e.target.closest('.specification-row').remove();
        }
    });

    // Validation du formulaire
    const form = document.getElementById('createItemForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500', 'ring-4', 'ring-red-500/20');
                field.classList.remove('border-gray-200 dark:border-gray-700');
                isValid = false;
            } else {
                field.classList.remove('border-red-500', 'ring-4', 'ring-red-500/20');
                field.classList.add('border-gray-200');
                field.classList.add('dark:border-gray-700');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showNotification('Veuillez remplir tous les champs obligatoires', 'error');
            return;
        }

        // Check image validation results if images selected
        const input = document.getElementById('images');
        if (!input || !input.files || input.files.length < 3) {
            e.preventDefault();
            showNotification('Veuillez fournir au minimum 3 images de bonne qualité', 'error');
            return;
        }
        
        if (input && input.files && input.files.length > 0) {
            const results = window.imageValidationResults || [];
            // If results not yet filled, prevent submit and ask user to wait
            if (results.length !== input.files.length) {
                e.preventDefault();
                showNotification('Veuillez patienter pendant la vérification des images...', 'warning');
                return;
            }

            // If any image has issues, prevent submit
            const bad = results.find(r => r && r.ok === false);
            if (bad) {
                e.preventDefault();
                showNotification('Certaines images nécessitent une vérification (taille/dimensions). Veuillez corriger avant de publier.', 'error');
                return;
            }
        }
    });

    // Validation du prix
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('input', function() {
        if (this.value < 0) {
            this.value = 0;
        }
    });

    // Validation de la quantité
    const quantityInput = document.getElementById('quantity');
    quantityInput.addEventListener('input', function() {
        if (this.value < 1) {
            this.value = 1;
        }
    });

    // Animation des input focus
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-primary-500/20');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-primary-500/20');
        });
    });
});

// Fonction pour afficher les notifications
function showNotification(message, type = 'info') {
    const colors = {
        success: 'from-emerald-500 to-emerald-600',
        error: 'from-red-500 to-red-600',
        warning: 'from-amber-500 to-amber-600',
        info: 'from-blue-500 to-blue-600'
    };

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    const notification = document.createElement('div');
    notification.className = `fixed top-5 right-5 z-50 min-w-80 max-w-md bg-gradient-to-r ${colors[type]} text-white rounded-2xl shadow-2xl p-4 transform transition-all duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="w-6 h-6 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas ${icons[type]}"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                class="w-6 h-6 rounded-full hover:bg-white dark:bg-gray-800/20 flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Suppression automatique
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/items/create.blade.php ENDPATH**/ ?>