<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejoignez VintApp - Pré-inscription</title>
    <meta name="description" content="Inscrivez-vous dès maintenant à VintApp et bénéficiez d'un accès prioritaire à notre plateforme innovante.">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .checkbox-item {
            transition: all 0.3s ease;
        }
        
        .checkbox-item:hover {
            transform: translateX(4px);
            background-color: rgba(99, 102, 241, 0.05);
        }
        
        .checkbox-item input:checked + label {
            color: #6366f1;
            font-weight: 600;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen">
    <!-- Background decorations -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-white/5 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
    </div>
    
    <div class="relative min-h-screen flex items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-4xl">
            <!-- Card principale -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up">
                <!-- Header avec dégradé -->
                <div class="relative bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-6 sm:px-8 lg:px-12 py-8 sm:py-12 text-white overflow-hidden">
                    <!-- Decorative circles -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
                    
                    <div class="relative z-10 text-center">
                        <div class="inline-block mb-4">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center mx-auto">
                                <i class="fas fa-rocket text-3xl sm:text-4xl"></i>
                            </div>
                        </div>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4">
                            Bienvenue sur VintApp !
                        </h1>
                        <p class="text-lg sm:text-xl text-white/90 mb-6 max-w-2xl mx-auto">
                            Rejoignez notre communauté et découvrez une expérience unique
                        </p>
                        
                        <!-- Stats badges -->
                        <div class="flex flex-wrap justify-center gap-3 sm:gap-4">
                            <div class="bg-white/20 backdrop-blur-lg rounded-full px-4 sm:px-6 py-2 sm:py-3">
                                <i class="fas fa-users mr-2"></i>
                                <span class="font-semibold"><?php echo e(\App\Models\UserWaiting::count()); ?>+</span>
                                <span class="hidden sm:inline ml-1">membres inscrits</span>
                                <span class="sm:hidden ml-1">inscrits</span>
                            </div>
                            <div class="bg-white/20 backdrop-blur-lg rounded-full px-4 sm:px-6 py-2 sm:py-3">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span class="font-semibold"><?php echo e(\App\Models\UserWaiting::approved()->count()); ?></span>
                                <span class="hidden sm:inline ml-1">approuvés</span>
                                <span class="sm:hidden ml-1">✓</span>
                            </div>
                            <div class="bg-white/20 backdrop-blur-lg rounded-full px-4 sm:px-6 py-2 sm:py-3">
                                <i class="fas fa-clock mr-2"></i>
                                <span class="hidden sm:inline">Lancement imminent</span>
                                <span class="sm:hidden">Bientôt</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Body du formulaire -->
                <div class="px-6 sm:px-8 lg:px-12 py-8 sm:py-12">
                <!-- Body du formulaire -->
                <div class="px-6 sm:px-8 lg:px-12 py-8 sm:py-12">
                    <!-- Alerts -->
                    <?php if(session('success')): ?>
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-green-800 font-medium"><?php echo e(session('success')); ?></p>
                            </div>
                            <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-green-500 hover:text-green-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-red-800 font-medium"><?php echo e(session('error')); ?></p>
                            </div>
                            <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl mt-1"></i>
                                <div class="flex-1">
                                    <p class="text-red-800 font-semibold mb-2">Veuillez corriger les erreurs suivantes :</p>
                                    <ul class="list-disc list-inside space-y-1 text-red-700">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Avantages -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-4 sm:p-6 text-center transform hover:scale-105 transition-transform duration-300">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-bolt text-white text-xl sm:text-2xl"></i>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-1 text-sm sm:text-base">Accès prioritaire</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Soyez parmi les premiers utilisateurs</p>
                        </div>
                        
                        <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-2xl p-4 sm:p-6 text-center transform hover:scale-105 transition-transform duration-300">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-gift text-white text-xl sm:text-2xl"></i>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-1 text-sm sm:text-base">Bonus exclusif</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Crédits de bienvenue offerts</p>
                        </div>
                        
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-4 sm:p-6 text-center transform hover:scale-105 transition-transform duration-300">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-bell text-white text-xl sm:text-2xl"></i>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-1 text-sm sm:text-base">Infos en avant-première</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Restez informé du lancement</p>
                        </div>
                    </div>

                    <!-- Formulaire -->
                    <form method="POST" action="<?php echo e(route('preregistration.store')); ?>" id="preregistrationForm" class="space-y-6">
                    <!-- Formulaire -->
                    <form method="POST" action="<?php echo e(route('preregistration.store')); ?>" id="preregistrationForm" class="space-y-6">
                        <?php echo csrf_field(); ?>

                        <!-- Nom complet -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user text-indigo-500 mr-2"></i>Nom complet 
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-4 py-3 border-2 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 text-gray-900 placeholder-gray-400" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo e(old('name')); ?>"
                                   required
                                   placeholder="Ex: Jean Dupont">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope text-indigo-500 mr-2"></i>Adresse email 
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   class="w-full px-4 py-3 border-2 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 text-gray-900 placeholder-gray-400" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo e(old('email')); ?>"
                                   required
                                   placeholder="votre.email@example.com">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Téléphone -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-phone text-indigo-500 mr-2"></i>Téléphone 
                                <span class="text-gray-400 text-xs font-normal">(optionnel)</span>
                            </label>
                            <input type="tel" 
                                   class="w-full px-4 py-3 border-2 <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 text-gray-900 placeholder-gray-400" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?php echo e(old('phone')); ?>"
                                   placeholder="Ex: 0812345678 ou +243812345678"
                                   pattern="^(\+?243|0)?[0-9]{9}$">
                            <p class="mt-1 text-xs text-gray-500">Format: 0812345678 ou +243812345678</p>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Pays -->
                        <div>
                            <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-globe text-indigo-500 mr-2"></i>Pays 
                                <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-4 py-3 border-2 <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 text-gray-900" 
                                    id="country" 
                                    name="country" 
                                    required>
                                <option value="Congo (RDC)" <?php echo e(old('country') == 'Congo (RDC)' ? 'selected' : 'selected'); ?>>🇨🇩 Congo (RDC)</option>
                                <option value="Congo (Brazzaville)" <?php echo e(old('country') == 'Congo (Brazzaville)' ? 'selected' : ''); ?>>🇨🇬 Congo (Brazzaville)</option>
                                <option value="France" <?php echo e(old('country') == 'France' ? 'selected' : ''); ?>>🇫🇷 France</option>
                                <option value="Belgique" <?php echo e(old('country') == 'Belgique' ? 'selected' : ''); ?>>🇧🇪 Belgique</option>
                                <option value="Canada" <?php echo e(old('country') == 'Canada' ? 'selected' : ''); ?>>🇨🇦 Canada</option>
                                <option value="Autre" <?php echo e(old('country') == 'Autre' ? 'selected' : ''); ?>>🌍 Autre</option>
                            </select>
                            <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Motivations - Checklist -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-heart text-indigo-500 mr-2"></i>Pourquoi voulez-vous rejoindre VintApp ?
                                <span class="text-gray-400 text-xs font-normal ml-1">(optionnel - plusieurs choix possibles)</span>
                            </label>
                            <div class="bg-gray-50 rounded-xl p-4 sm:p-6 space-y-3">
                                <div class="checkbox-item rounded-lg p-3 flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           id="reason_buyer" 
                                           name="reasons[]" 
                                           value="Acheter des produits vintage de qualité"
                                           <?php echo e(is_array(old('reasons')) && in_array('Acheter des produits vintage de qualité', old('reasons')) ? 'checked' : ''); ?>

                                           class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-0.5 flex-shrink-0">
                                    <label for="reason_buyer" class="text-gray-700 cursor-pointer flex-1">
                                        <span class="block font-medium">🛍️ Acheter des produits vintage</span>
                                        <span class="text-xs text-gray-500">Découvrir et acquérir des pièces uniques</span>
                                    </label>
                                </div>
                                
                                <div class="checkbox-item rounded-lg p-3 flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           id="reason_seller" 
                                           name="reasons[]" 
                                           value="Vendre mes articles vintage"
                                           <?php echo e(is_array(old('reasons')) && in_array('Vendre mes articles vintage', old('reasons')) ? 'checked' : ''); ?>

                                           class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-0.5 flex-shrink-0">
                                    <label for="reason_seller" class="text-gray-700 cursor-pointer flex-1">
                                        <span class="block font-medium">💼 Vendre mes articles vintage</span>
                                        <span class="text-xs text-gray-500">Monétiser ma collection ou mes trouvailles</span>
                                    </label>
                                </div>
                                
                                <div class="checkbox-item rounded-lg p-3 flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           id="reason_business" 
                                           name="reasons[]" 
                                           value="Développer mon business vintage"
                                           <?php echo e(is_array(old('reasons')) && in_array('Développer mon business vintage', old('reasons')) ? 'checked' : ''); ?>

                                           class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-0.5 flex-shrink-0">
                                    <label for="reason_business" class="text-gray-700 cursor-pointer flex-1">
                                        <span class="block font-medium">🚀 Développer mon business</span>
                                        <span class="text-xs text-gray-500">Faire croître mon activité professionnelle</span>
                                    </label>
                                </div>
                                
                                <div class="checkbox-item rounded-lg p-3 flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           id="reason_community" 
                                           name="reasons[]" 
                                           value="Rejoindre une communauté de passionnés"
                                           <?php echo e(is_array(old('reasons')) && in_array('Rejoindre une communauté de passionnés', old('reasons')) ? 'checked' : ''); ?>

                                           class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-0.5 flex-shrink-0">
                                    <label for="reason_community" class="text-gray-700 cursor-pointer flex-1">
                                        <span class="block font-medium">👥 Rejoindre la communauté</span>
                                        <span class="text-xs text-gray-500">Échanger avec d'autres passionnés</span>
                                    </label>
                                </div>
                                
                                <div class="checkbox-item rounded-lg p-3 flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           id="reason_collection" 
                                           name="reasons[]" 
                                           value="Enrichir ma collection personnelle"
                                           <?php echo e(is_array(old('reasons')) && in_array('Enrichir ma collection personnelle', old('reasons')) ? 'checked' : ''); ?>

                                           class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-0.5 flex-shrink-0">
                                    <label for="reason_collection" class="text-gray-700 cursor-pointer flex-1">
                                        <span class="block font-medium">⭐ Enrichir ma collection</span>
                                        <span class="text-xs text-gray-500">Compléter et valoriser mes pièces</span>
                                    </label>
                                </div>
                                
                                <div class="checkbox-item rounded-lg p-3 flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           id="reason_sustainable" 
                                           name="reasons[]" 
                                           value="Consommer de manière responsable et durable"
                                           <?php echo e(is_array(old('reasons')) && in_array('Consommer de manière responsable et durable', old('reasons')) ? 'checked' : ''); ?>

                                           class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-0.5 flex-shrink-0">
                                    <label for="reason_sustainable" class="text-gray-700 cursor-pointer flex-1">
                                        <span class="block font-medium">♻️ Consommation responsable</span>
                                        <span class="text-xs text-gray-500">Privilégier la seconde main et l'économie circulaire</span>
                                    </label>
                                </div>
                                
                                <div class="checkbox-item rounded-lg p-3 flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           id="reason_curiosity" 
                                           name="reasons[]" 
                                           value="Découvrir une nouvelle plateforme innovante"
                                           <?php echo e(is_array(old('reasons')) && in_array('Découvrir une nouvelle plateforme innovante', old('reasons')) ? 'checked' : ''); ?>

                                           class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-0.5 flex-shrink-0">
                                    <label for="reason_curiosity" class="text-gray-700 cursor-pointer flex-1">
                                        <span class="block font-medium">🔍 Curiosité et découverte</span>
                                        <span class="text-xs text-gray-500">Explorer une nouvelle expérience d'achat/vente</span>
                                    </label>
                                </div>
                            </div>
                            <?php $__errorArgs = ['reasons'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Bouton submit -->
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white font-bold py-4 px-6 rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                            <i class="fas fa-rocket mr-2"></i>
                            Rejoindre VintApp maintenant !
                        </button>

                        <!-- Message sécurité -->
                        <div class="text-center">
                            <p class="text-sm text-gray-500 flex items-center justify-center gap-2">
                                <i class="fas fa-lock"></i>
                                <span>Vos données sont sécurisées et ne seront jamais partagées</span>
                            </p>
                        </div>
                    </form>

                    </form>

                    <!-- Footer Links -->
                    <div class="mt-8 pt-6 border-t border-gray-200 text-center space-y-3">
                        <a href="<?php echo e(route('preregistration.stats')); ?>" 
                           class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                            <i class="fas fa-chart-line"></i>
                            <span>Voir les statistiques d'inscription</span>
                        </a>
                        
                        <div class="flex flex-wrap justify-center gap-4 text-sm text-gray-500">
                            <a href="#" class="hover:text-indigo-600 transition-colors">
                                <i class="fas fa-question-circle mr-1"></i>FAQ
                            </a>
                            <a href="#" class="hover:text-indigo-600 transition-colors">
                                <i class="fas fa-shield-alt mr-1"></i>Confidentialité
                            </a>
                            <a href="#" class="hover:text-indigo-600 transition-colors">
                                <i class="fas fa-envelope mr-1"></i>Contact
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer externe -->
            <div class="text-center mt-8 text-white/90">
                <p class="text-sm sm:text-base">
                    © <?php echo e(date('Y')); ?> VintApp. Tous droits réservés.
                </p>
                <p class="text-xs sm:text-sm mt-2 text-white/70">
                    La marketplace du vintage qui révolutionne l'achat et la vente de pièces uniques
                </p>
            </div>
        </div>
    </div>

    <script>
        // Animation au scroll
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts après 5 secondes
            setTimeout(() => {
                const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
                alerts.forEach(alert => {
                    if (alert.querySelector('button')) return; // Skip if has close button clicked
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
            
            // Smooth checkbox interactions
            const checkboxItems = document.querySelectorAll('.checkbox-item');
            checkboxItems.forEach(item => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                const label = item.querySelector('label');
                
                item.addEventListener('click', function(e) {
                    if (e.target !== checkbox) {
                        checkbox.checked = !checkbox.checked;
                    }
                });
            });
            
            // Form validation feedback
            const form = document.getElementById('preregistrationForm');
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Inscription en cours...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/preregistration/index.blade.php ENDPATH**/ ?>