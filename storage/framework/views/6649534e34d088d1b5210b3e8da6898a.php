<?php $__env->startSection('title', 'Mon Profil'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mon Profil</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Gérez vos informations personnelles et vos préférences</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6 text-center">
                        <?php if($user->avatar_url): ?>
                            <img src="<?php echo e($user->avatar_url); ?>" 
                                 alt="<?php echo e($user->name); ?>" 
                                 class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-primary-100 dark:border-primary-900">
                        <?php else: ?>
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 text-white flex items-center justify-center mx-auto mb-4 text-3xl font-bold border-4 border-primary-100 dark:border-primary-900">
                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                            </div>
                        <?php endif; ?>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1"><?php echo e($user->name); ?></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4"><?php echo e($user->email); ?></p>
                        <button onclick="showAvatarModal()" 
                                class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition-all font-medium text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Changer l'avatar
                        </button>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Statistiques
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="text-center p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl">
                                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400"><?php echo e($stats['total_items']); ?></div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Articles</div>
                                <div class="text-[10px] text-gray-400 dark:text-gray-500"><?php echo e($stats['active_items']); ?> actifs</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-xl">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400"><?php echo e($stats['completed_orders']); ?></div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Vendus</div>
                                <div class="text-[10px] text-gray-400 dark:text-gray-500"><?php echo e($stats['sold_items']); ?> soldés</div>
                            </div>
                            <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?php echo e(number_format($stats['total_revenue'], 0)); ?>$</div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Revenus USD</div>
                                <div class="text-[10px] text-gray-400 dark:text-gray-500"><?php echo e(number_format($stats['total_revenue_cdf'], 0)); ?> FC</div>
                            </div>
                            <div class="text-center p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400"><?php echo e(number_format($stats['average_rating'], 1)); ?></div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Note</div>
                                <div class="text-[10px] text-gray-400 dark:text-gray-500"><?php echo e($stats['reviews_count']); ?> avis</div>
                            </div>
                        </div>
                        <hr class="my-3 border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Messages</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                <?php echo e($stats['unread_messages']); ?>/<?php echo e($stats['total_messages']); ?>

                                <?php if($stats['unread_messages'] > 0): ?>
                                    <span class="inline-flex w-2 h-2 bg-red-500 rounded-full ml-1"></span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-2">
                            <span class="text-gray-500 dark:text-gray-400">Favoris</span>
                            <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($stats['favorites_count']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Tabs -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden" x-data="{ activeTab: 'general' }">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex flex-wrap gap-2 p-4">
                            <button @click="activeTab = 'general'" 
                                    :class="activeTab === 'general' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Général
                            </button>
                            <button @click="activeTab = 'security'" 
                                    :class="activeTab === 'security' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Sécurité
                            </button>
                            <button @click="activeTab = 'preferences'" 
                                    :class="activeTab === 'preferences' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Préférences
                            </button>
                            <button @click="activeTab = 'notifications'" 
                                    :class="activeTab === 'notifications' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                Notifications
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        <!-- General Tab -->
                        <div x-show="activeTab === 'general'" x-cloak>
                            <form method="POST" action="<?php echo e(route('profile.update')); ?>" class="space-y-6">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Nom complet
                                        </label>
                                        <input type="text" 
                                               id="name" 
                                               name="name" 
                                               value="<?php echo e(old('name', $user->name)); ?>" 
                                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               required>
                                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Email
                                        </label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               value="<?php echo e(old('email', $user->email)); ?>" 
                                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               required>
                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Téléphone
                                        </label>
                                        <input type="tel" 
                                               id="phone" 
                                               name="phone" 
                                               value="<?php echo e(old('phone', $user->phone)); ?>" 
                                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Localisation
                                        </label>
                                        <input type="text" 
                                               id="location" 
                                               name="location" 
                                               value="<?php echo e(old('location', $user->location)); ?>" 
                                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <div>
                                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Bio
                                    </label>
                                    <textarea id="bio" 
                                              name="bio" 
                                              rows="4" 
                                              class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('bio', $user->bio)); ?></textarea>
                                    <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Sauvegarder
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Security Tab -->
                        <div x-show="activeTab === 'security'" x-cloak class="space-y-8">
                            <!-- Change Password -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Changer le mot de passe</h3>
                                <form method="POST" action="<?php echo e(route('profile.password')); ?>" class="space-y-4">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Mot de passe actuel
                                        </label>
                                        <input type="password" 
                                               id="current_password" 
                                               name="current_password" 
                                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               required>
                                        <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Nouveau mot de passe
                                            </label>
                                            <input type="password" 
                                                   id="password" 
                                                   name="password" 
                                                   class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   required>
                                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div>
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Confirmer le mot de passe
                                            </label>
                                            <input type="password" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all"
                                                   required>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" 
                                                class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                            </svg>
                                            Changer le mot de passe
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                                <!-- 2FA Section -->
                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Authentification à deux facteurs</h3>
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center justify-center w-12 h-12 rounded-xl <?php echo e(auth()->user()->google2fa_enabled ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-200 dark:bg-gray-700'); ?>">
                                                <svg class="w-6 h-6 <?php echo e(auth()->user()->google2fa_enabled ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-white">Protection renforcée de votre compte</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    <?php if(auth()->user()->google2fa_enabled): ?>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Activée
                                                        </span>
                                                        L'authentification à deux facteurs est active
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                            </svg>
                                                            Désactivée
                                                        </span>
                                                        Ajoutez une couche de sécurité supplémentaire
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <a href="<?php echo e(route('two-factor.index')); ?>" 
                                           class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition-all font-medium text-sm flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Gérer
                                        </a>
                                    </div>
                                </div>

                                <!-- Danger Zone -->
                                <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 rounded-xl p-6">
                                    <div class="flex items-start gap-3 mb-4">
                                        <div class="flex items-center justify-center w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex-shrink-0">
                                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-red-900 dark:text-red-100 mb-1">Zone de danger</h4>
                                            <p class="text-sm text-red-800 dark:text-red-200">
                                                Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <form method="POST" action="<?php echo e(route('profile.destroy')); ?>" onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer votre compte ? Cette action est irréversible.')" class="space-y-4">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <div>
                                            <label for="delete_password" class="block text-sm font-medium text-red-900 dark:text-red-100 mb-2">
                                                Confirmez avec votre mot de passe
                                            </label>
                                            <input type="password" 
                                                   id="delete_password" 
                                                   name="password" 
                                                   class="w-full px-4 py-3 border-2 border-red-300 dark:border-red-700 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:bg-red-900/20 dark:text-white transition-all <?php $__errorArgs = ['password', 'userDeletion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   required>
                                            <?php $__errorArgs = ['password', 'userDeletion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <button type="submit" 
                                                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Supprimer définitivement mon compte
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Preferences Tab -->
                        <div x-show="activeTab === 'preferences'" x-cloak>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Préférences</h3>
                            
                            <!-- Thème -->
                            <div class="mb-8">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Thème de l'interface</h4>
                                <form method="POST" action="<?php echo e(route('profile.theme')); ?>" class="space-y-4">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="theme_preference" value="auto" <?php echo e($user->theme_preference === 'auto' ? 'checked' : ''); ?> class="peer sr-only">
                                            <div class="bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 peer-checked:border-primary-600 dark:peer-checked:border-primary-400 rounded-xl p-5 text-center transition-all hover:border-primary-400 dark:hover:border-primary-500 peer-checked:shadow-lg">
                                                <svg class="w-10 h-10 mx-auto mb-2 text-gray-400 dark:text-gray-500 peer-checked:text-primary-600 dark:peer-checked:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                                <h5 class="font-semibold text-sm text-gray-900 dark:text-white">Auto</h5>
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Suit le système</p>
                                            </div>
                                        </label>
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="theme_preference" value="light" <?php echo e($user->theme_preference === 'light' ? 'checked' : ''); ?> class="peer sr-only">
                                            <div class="bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 peer-checked:border-primary-600 dark:peer-checked:border-primary-400 rounded-xl p-5 text-center transition-all hover:border-primary-400 dark:hover:border-primary-500 peer-checked:shadow-lg">
                                                <svg class="w-10 h-10 mx-auto mb-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                                <h5 class="font-semibold text-sm text-gray-900 dark:text-white">Clair</h5>
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Thème clair</p>
                                            </div>
                                        </label>
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="theme_preference" value="dark" <?php echo e($user->theme_preference === 'dark' ? 'checked' : ''); ?> class="peer sr-only">
                                            <div class="bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 peer-checked:border-primary-600 dark:peer-checked:border-primary-400 rounded-xl p-5 text-center transition-all hover:border-primary-400 dark:hover:border-primary-500 peer-checked:shadow-lg">
                                                <svg class="w-10 h-10 mx-auto mb-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                                </svg>
                                                <h5 class="font-semibold text-sm text-gray-900 dark:text-white">Sombre</h5>
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Thème sombre</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold text-sm transition-all">
                                            Appliquer le thème
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <hr class="border-gray-200 dark:border-gray-700 mb-8">

                            <!-- Newsletter -->
                            <div class="mb-8">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Notifications email</h4>
                                <form method="POST" action="<?php echo e(route('profile.preferences')); ?>" class="space-y-4">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <div>
                                                <p class="font-medium text-sm text-gray-900 dark:text-white">Newsletter</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Recevoir les offres et actualités</p>
                                            </div>
                                        </div>
                                        <input type="hidden" name="newsletter_subscribed" value="0">
                                        <input type="checkbox" name="newsletter_subscribed" value="1" <?php echo e($user->newsletter_subscribed ? 'checked' : ''); ?> class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                    </label>
                                    <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                            <div>
                                                <p class="font-medium text-sm text-gray-900 dark:text-white">Notifications push</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Alertes en temps réel sur vos ventes</p>
                                            </div>
                                        </div>
                                        <input type="hidden" name="push_enabled" value="0">
                                        <input type="checkbox" name="push_enabled" value="1" checked class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                    </label>
                                    <div class="flex justify-end">
                                        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold text-sm transition-all">
                                            Sauvegarder
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <hr class="border-gray-200 dark:border-gray-700 mb-8">

                            <!-- Langue et devise -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Langue et région</h4>
                                <form method="POST" action="<?php echo e(route('profile.preferences')); ?>" class="space-y-4">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="locale" class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Langue</label>
                                            <select id="locale" name="locale" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all text-sm">
                                                <option value="fr" selected>Français</option>
                                                <option value="en">English</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="currency" class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">Devise</label>
                                            <select id="currency" name="currency" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all text-sm">
                                                <option value="USD">$ USD</option>
                                                <option value="CDF">FC CDF</option>
                                                <option value="EUR">€ EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold text-sm transition-all">
                                            Sauvegarder
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Notifications Tab -->
                        <div x-show="activeTab === 'notifications'" x-cloak>
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Historique des notifications</h3>
                                <button onclick="markAllAsRead()" 
                                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-all text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Marquer tout comme lu
                                </button>
                            </div>
                            
                            <div id="notifications-list" class="space-y-3">
                                <?php $__empty_1 = true; $__currentLoopData = $user->notifications()->latest()->take(10)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="notification-item bg-white dark:bg-gray-700 rounded-xl p-4 border border-gray-200 dark:border-gray-600 hover:border-primary-300 dark:hover:border-primary-600 transition-all <?php echo e(!$notification->read_at ? 'ring-2 ring-primary-100 dark:ring-primary-900' : ''); ?>" data-id="<?php echo e($notification->id); ?>">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm"><?php echo e($notification->title); ?></h4>
                                                    <?php if(!$notification->read_at): ?>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 flex-shrink-0">Nouveau</span>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e($notification->message); ?></p>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <span class="text-xs text-gray-400 dark:text-gray-500"><?php echo e($notification->created_at->diffForHumans()); ?></span>
                                                <?php if(!$notification->read_at): ?>
                                                    <button onclick="markAsRead(<?php echo e($notification->id); ?>)" class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors text-gray-400 hover:text-primary-600" title="Marquer comme lu">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="text-center py-16">
                                        <svg class="w-20 h-20 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucune notification</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Vous n'avez aucune notification pour le moment</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Modal -->
<div id="avatarModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Changer l'avatar</h3>
                <button onclick="closeAvatarModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="<?php echo e(route('profile.avatar')); ?>" enctype="multipart/form-data" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sélectionner une image
                    </label>
                    <input type="file" 
                           name="avatar" 
                           accept="image/*" 
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all">
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">PNG, JPG ou GIF (max. 2MB)</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" 
                            onclick="closeAvatarModal()" 
                            class="flex-1 px-4 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-medium">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition-all font-semibold">
                        Changer l'avatar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function showAvatarModal() {
    document.getElementById('avatarModal').classList.remove('hidden');
    document.getElementById('avatarModal').classList.add('flex');
}

function closeAvatarModal() {
    document.getElementById('avatarModal').classList.add('hidden');
    document.getElementById('avatarModal').classList.remove('flex');
}

function markAsRead(id) {
    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Content-Type': 'application/json' }
    }).then(r => r.json()).then(() => {
        const item = document.querySelector(`.notification-item[data-id="${id}"]`);
        if (item) {
            item.classList.remove('ring-2', 'ring-primary-100', 'dark:ring-primary-900');
            const badge = item.querySelector('.flex-shrink-0');
            if (badge) badge.remove();
            const btn = item.querySelector('button[title="Marquer comme lu"]');
            if (btn) btn.remove();
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Content-Type': 'application/json' }
    }).then(r => r.json()).then(() => {
        document.querySelectorAll('.notification-item').forEach(item => {
            item.classList.remove('ring-2', 'ring-primary-100', 'dark:ring-primary-900');
            const badge = item.querySelector('.flex-shrink-0');
            if (badge) badge.remove();
            const btn = item.querySelector('button[title="Marquer comme lu"]');
            if (btn) btn.remove();
        });
    });
}

// Close modal on outside click
document.getElementById('avatarModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAvatarModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAvatarModal();
    }
});
</script>
<?php $__env->stopPush(); ?>

<style>
[x-cloak] { display: none !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/profile/edit.blade.php ENDPATH**/ ?>