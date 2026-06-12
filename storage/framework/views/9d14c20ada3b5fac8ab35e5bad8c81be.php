<?php $__env->startSection('title', 'Modifier l\'article'); ?>
<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-amber-50/30 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300 mb-8">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-amber-600 transition-colors">Accueil</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="<?php echo e(route('items.index')); ?>" class="hover:text-amber-600 transition-colors">Produits</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="<?php echo e(route('items.show', $item)); ?>" class="hover:text-amber-600 transition-colors"><?php echo e($item->name); ?></a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 dark:text-white font-medium">Modifier</span>
        </nav>

        <div class="max-w-4xl mx-auto">
            <!-- Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl shadow-xl shadow-amber-600/10 border border-gray-100/50 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white p-6 lg:p-8">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white dark:bg-gray-800/20 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-edit text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold">Modifier l'article</h1>
                            <p class="text-amber-100 mt-1">Mettez à jour les informations de votre produit</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="p-6 lg:p-8">
                    <form method="POST" action="<?php echo e(route('items.update', $item)); ?>" enctype="multipart/form-data" id="editItemForm" class="space-y-8">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <!-- Informations de base -->
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Informations de base</h2>
                            </div>

                            <!-- Nom et Prix -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Nom de l'article -->
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-tag text-amber-600 mr-2"></i>
                                        Nom de l'article *
                                    </label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo e(old('name', $item->name)); ?>" 
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
                                        <i class="fas fa-dollar-sign text-amber-600 mr-2"></i>
                                        Prix *
                                    </label>
                                    <input type="number" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="price" 
                                           name="price" 
                                           value="<?php echo e(old('price', $item->price)); ?>" 
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

                            <!-- Devise -->
                            <div>
                                <label for="currency" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-money-bill text-amber-600 mr-2"></i>
                                    Devise *
                                </label>
                                <select class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['currency'];
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
                                    <option value="USD" <?php echo e(old('currency', $item->currency) == 'USD' ? 'selected' : ''); ?>>USD ($)</option>
                                    <option value="CDF" <?php echo e(old('currency', $item->currency) == 'CDF' ? 'selected' : ''); ?>>CDF (FC)</option>
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

                            <!-- Description -->
                            <div class="col-span-full">
                                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-align-left text-amber-600 mr-2"></i>
                                    Description détaillée *
                                </label>
                                <textarea class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300 resize-none <?php $__errorArgs = ['description'];
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
                                          placeholder="Décrivez votre article en détail..."
                                          required><?php echo e(old('description', $item->description)); ?></textarea>
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
                            </div>
                        </div>

                        <!-- Catégorie et informations produit -->
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Informations produit</h2>
                            </div>

                            <!-- Catégorie et Marque -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Catégorie -->
                                <div>
                                    <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-folder text-amber-600 mr-2"></i>
                                        Catégorie *
                                    </label>
                                    <select class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['category_id'];
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
                                                    <?php echo e(old('category_id', $item->category_id) == $category->id ? 'selected' : ''); ?>>
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
                                        <i class="fas fa-trademark text-amber-600 mr-2"></i>
                                        Marque
                                    </label>
                                    <select class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['brand_id'];
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
                                                    <?php echo e(old('brand_id', $item->brand_id) == $brand->id ? 'selected' : ''); ?>>
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

                            <!-- État et Quantité -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- État -->
                                <div>
                                    <label for="condition" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-star text-amber-600 mr-2"></i>
                                        État de l'article *
                                    </label>
                                    <select class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['condition'];
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
                                        <option value="new" <?php echo e(old('condition', $item->condition) == 'new' ? 'selected' : ''); ?>>Neuf</option>
                                        <option value="like_new" <?php echo e(old('condition', $item->condition) == 'like_new' ? 'selected' : ''); ?>>Comme neuf</option>
                                        <option value="good" <?php echo e(old('condition', $item->condition) == 'good' ? 'selected' : ''); ?>>Bon état</option>
                                        <option value="fair" <?php echo e(old('condition', $item->condition) == 'fair' ? 'selected' : ''); ?>>État correct</option>
                                        <option value="poor" <?php echo e(old('condition', $item->condition) == 'poor' ? 'selected' : ''); ?>>Usé</option>
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
                                        <i class="fas fa-boxes text-amber-600 mr-2"></i>
                                        Quantité disponible *
                                    </label>
                                    <input type="number" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-4 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="quantity" 
                                           name="quantity" 
                                           value="<?php echo e(old('quantity', $item->quantity)); ?>" 
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

                        <!-- Images existantes -->
                        <?php if($item->images && count($item->images) > 0): ?>
                            <div class="space-y-6">
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-images"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Images actuelles</h2>
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <?php $__currentLoopData = $item->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="relative group">
                                            <img src="<?php echo e(Storage::url($image)); ?>" 
                                                 class="w-full h-32 object-cover rounded-xl shadow-md group-hover:shadow-lg transition-all duration-300"
                                                 alt="Image <?php echo e($index + 1); ?>">
                                            <button type="button" class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-all duration-300 remove-existing-image opacity-0 group-hover:opacity-100" 
                                                    data-image="<?php echo e($image); ?>">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Nouvelles images -->
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ajouter de nouvelles images</h2>
                            </div>

                            <div>
                                <label for="images" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-upload text-amber-600 mr-2"></i>
                                    Sélectionner des images
                                </label>
                                <div class="relative">
                                    <input type="file" 
                                           class="w-full px-4 py-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300 <?php $__errorArgs = ['images.*'];
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
                                    Vous pouvez ajouter de nouvelles images aux existantes
                                </p>
                                
                                <!-- Prévisualisation des nouvelles images -->
                                <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 hidden">
                                    <!-- Les prévisualisations seront ajoutées ici -->
                                </div>
                            </div>
                        </div>

                        <!-- Spécifications -->
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Spécifications</h2>
                            </div>

                            <div id="specificationsContainer" class="space-y-4">
                                <?php if($item->specifications && is_array($item->specifications) && count($item->specifications) > 0): ?>
                                    <?php $__currentLoopData = $item->specifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end specification-row">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Nom de la spécification</label>
                                                <input type="text" 
                                                       class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300" 
                                                       name="specifications[key][]" 
                                                       value="<?php echo e(is_string($key) ? $key : ''); ?>"
                                                       placeholder="Ex: Couleur, Matériau">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Valeur</label>
                                                <input type="text" 
                                                       class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300" 
                                                       name="specifications[value][]" 
                                                       value="<?php echo e(is_string($value) ? $value : ''); ?>"
                                                       placeholder="Ex: Rouge, Coton">
                                            </div>
                                            <div>
                                                <button type="button" class="w-full bg-red-100 hover:bg-red-200 text-red-600 px-4 py-3 rounded-xl transition-all duration-300 remove-specification">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end specification-row">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Nom de la spécification</label>
                                            <input type="text" 
                                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300" 
                                                   name="specifications[key][]" 
                                                   placeholder="Ex: Couleur, Matériau">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Valeur</label>
                                            <input type="text" 
                                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300" 
                                                   name="specifications[value][]" 
                                                   placeholder="Ex: Rouge, Coton">
                                        </div>
                                        <div>
                                            <button type="button" class="w-full bg-red-100 hover:bg-red-200 text-red-600 px-4 py-3 rounded-xl transition-all duration-300 remove-specification">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <button type="button" class="bg-amber-100 hover:bg-amber-200 text-amber-600 px-6 py-3 rounded-xl transition-all duration-300 font-semibold" id="addSpecification">
                                <i class="fas fa-plus mr-2"></i>
                                Ajouter une spécification
                            </button>
                        </div>

                        <!-- Boutons -->
                        <div class="flex flex-col sm:flex-row justify-between gap-4 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <a href="<?php echo e(route('items.show', $item)); ?>" class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-6 py-3 rounded-xl transition-all duration-300 font-semibold text-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-8 py-3 rounded-xl transition-all duration-300 font-bold text-lg shadow-lg hover:shadow-xl hover:-translate-y-1">
                                <i class="fas fa-save mr-2"></i>
                                Sauvegarder les modifications
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
    // Prévisualisation des nouvelles images
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        imagePreview.classList.add('hidden');
        
        if (this.files.length > 0) {
            imagePreview.classList.remove('hidden');
            
            Array.from(this.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        div.innerHTML = `
                            <img src="${e.target.result}" 
                                 class="w-full h-32 object-cover rounded-xl shadow-md group-hover:shadow-lg transition-all duration-300"
                                 alt="Prévisualisation ${index + 1}">
                            <button type="button" class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-all duration-300 remove-image opacity-0 group-hover:opacity-100" 
                                    data-index="${index}">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        `;
                        imagePreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // Supprimer une nouvelle image de la prévisualisation
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

    // Supprimer une image existante
    document.querySelectorAll('.remove-existing-image').forEach(btn => {
        btn.addEventListener('click', function() {
            const image = this.dataset.image;
            if (confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
                // Ici vous pourriez ajouter une logique pour supprimer l'image du serveur
                this.closest('.relative').remove();
            }
        });
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
                       class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300" 
                       name="specifications[key][]" 
                       placeholder="Ex: Couleur, Matériau">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Valeur</label>
                <input type="text" 
                       class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-amber-600 focus:ring-4 focus:ring-amber-600/20 outline-none transition-all duration-300" 
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
    const form = document.getElementById('editItemForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500', 'ring-4', 'ring-red-500/20');
                field.classList.remove('border-gray-200', 'dark:border-gray-700');
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
            this.parentElement.classList.add('ring-2', 'ring-amber-500/20');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-amber-500/20');
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
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/items/edit.blade.php ENDPATH**/ ?>