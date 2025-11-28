

<?php $__env->startSection('content'); ?>

<!-- 🎉 MODALE DE SUCCÈS - Affichée uniquement après vérification d'email -->
<?php if(session('email_verified')): ?>
<div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50" id="emailVerifiedModal">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden transform transition-all duration-500 scale-100">
        <!-- Header avec animation -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-center py-8">
            <!-- Icône animée -->
            <div class="mb-4">
                <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto animate-bounce">
                    <i class="fas fa-check-circle text-emerald-500 text-4xl"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold">
                🎉 Email Vérifié avec Succès !
            </h3>
        </div>
        
        <!-- Contenu -->
        <div class="p-6 text-center">
            <p class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                Bienvenue sur VintApp !
            </p>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Votre compte est maintenant <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded-lg font-medium">ACTIF</span>. Vous avez désormais accès à toutes les fonctionnalités de la plateforme.
            </p>
            
            <!-- Liste des fonctionnalités débloquées -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 mb-6 text-left">
                <p class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <i class="fas fa-unlock text-emerald-500 mr-2"></i>
                    Fonctionnalités débloquées :
                </p>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-500 mr-2"></i>
                        Créer et vendre des articles
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-500 mr-2"></i>
                        Passer des commandes
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-500 mr-2"></i>
                        Envoyer des messages
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-500 mr-2"></i>
                        Gérer votre profil
                    </li>
                </ul>
            </div>
            
            <!-- Confetti symbolique -->
            <div class="text-4xl mb-4">
                🎊 🎉 ✨ 🎊
            </div>
        </div>
        
        <!-- Footer -->
        <div class="p-6 text-center border-t border-gray-100">
            <button type="button" class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg transition-all duration-300 hover:shadow-xl" onclick="document.getElementById('emailVerifiedModal').style.display='none'">
                <i class="fas fa-rocket mr-2"></i>
                Commencer à Explorer
            </button>
        </div>
    </div>
</div>

<script>
    // Fermer la modale si on clique en dehors
    document.getElementById('emailVerifiedModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
    
    // Fermer avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('emailVerifiedModal');
            if (modal) modal.style.display = 'none';
        }
    });
</script>
<?php endif; ?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-indigo-50/30 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        <!-- Cards de statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Card Articles -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg shadow-violet-600/10 border border-gray-100/50 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-violet-500 to-violet-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-box text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-600 dark:text-gray-300 font-semibold">Articles</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total_items'] ?? 0); ?></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Actifs : <?php echo e($stats['active_items'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <!-- Card Ventes -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg shadow-emerald-600/10 border border-gray-100/50 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-600 dark:text-gray-300 font-semibold">Ventes</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total_sales'] ?? 0); ?></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <?php echo e(number_format($stats['total_revenue'] ?? 0, 2)); ?> USD
                            <span class="text-gray-400">|</span>
                            <?php echo e(number_format(($stats['total_revenue'] ?? 0) * 2450, 0)); ?> CDF
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card Messages -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg shadow-cyan-600/10 border border-gray-100/50 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-envelope text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-600 dark:text-gray-300 font-semibold">Messages</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['unread_messages'] ?? 0); ?></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Non lus</p>
                    </div>
                </div>
            </div>

            <!-- Card Support -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg shadow-amber-600/10 border border-gray-100/50 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-headset text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-600 dark:text-gray-300 font-semibold">Support</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['pending_support_chats'] ?? 0); ?></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En attente</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Support -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl shadow-xl shadow-amber-600/10 border border-gray-100/50 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-headset text-amber-500 mr-3"></i>
                            Support Client
                        </h3>
                        <?php if(Route::has('admin.support.index')): ?>
                            <a href="<?php echo e(route('admin.support.index')); ?>" 
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                Voir tout
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('support.index')); ?>" 
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                Mes demandes
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total_support_chats'] ?? 0); ?></div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total conversations</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600"><?php echo e($stats['open_support_chats'] ?? 0); ?></div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Nouvelles demandes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600"><?php echo e($stats['pending_support_chats'] ?? 0); ?></div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">En cours</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600"><?php echo e($stats['unassigned_support_chats'] ?? 0); ?></div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Non assignées</div>
                        </div>
                    </div>
                    <?php if(($stats['unassigned_support_chats'] ?? 0) > 0): ?>
                        <div class="mt-6 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-orange-600 mr-3"></i>
                                <span class="text-orange-800 font-medium">
                                    <?php echo e($stats['unassigned_support_chats'] ?? 0); ?> conversation(s) nécessitent votre attention
                                </span>
                                <?php if(Route::has('admin.support.index')): ?>
                                    <a href="<?php echo e(route('admin.support.index', ['assigned_to' => 'unassigned'])); ?>" 
                                       class="ml-auto text-orange-600 hover:text-orange-800 font-medium transition-colors">
                                        Voir →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Section Articles et Commandes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Articles récents -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl shadow-xl shadow-violet-600/10 border border-gray-100/50 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-box text-violet-500 mr-3"></i>
                        Articles récents
                    </h3>
                </div>
                <div class="p-6">
                    <?php if(isset($recentItems) && $recentItems->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $recentItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-900 rounded-xl hover:bg-violet-50 transition-colors duration-200">
                                    <div>
                                        <h6 class="font-semibold text-gray-900 dark:text-white mb-1"><?php echo e($item->name); ?></h6>
                                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($item->category->name ?? 'N/A'); ?></p>
                                    </div>
                                    <span class="px-3 py-1 bg-violet-500 text-white text-sm font-semibold rounded-full">
                                        <?php echo e($item->formatted_price); ?>

                                    </span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Aucun article récent</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Commandes récentes -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl shadow-xl shadow-emerald-600/10 border border-gray-100/50 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-shopping-cart text-emerald-500 mr-3"></i>
                        Commandes récentes
                    </h3>
                </div>
                <div class="p-6">
                    <?php if(isset($recentOrders) && $recentOrders->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-900 rounded-xl hover:bg-emerald-50 transition-colors duration-200">
                                    <div>
                                        <h6 class="font-semibold text-gray-900 dark:text-white mb-1">Commande #<?php echo e($order->id); ?></h6>
                                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($order->item->name ?? 'N/A'); ?></p>
                                    </div>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full <?php echo e($order->status === 'completed' ? 'bg-emerald-500 text-white' : 'bg-yellow-500 text-white'); ?>">
                                        <?php echo e(ucfirst($order->status)); ?>

                                    </span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Aucune commande récente</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Section Messages et Notifications -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Messages récents -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl shadow-xl shadow-cyan-600/10 border border-gray-100/50 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-envelope text-cyan-500 mr-3"></i>
                        Messages récents
                    </h3>
                </div>
                <div class="p-6">
                    <?php if(isset($recentMessages) && $recentMessages->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $recentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl hover:bg-cyan-50 transition-colors duration-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h6 class="font-semibold text-gray-900 dark:text-white mb-1"><?php echo e($msg->sender->name ?? 'N/A'); ?></h6>
                                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed"><?php echo e(Str::limit($msg->content, 50)); ?></p>
                                        </div>
                                        <p class="text-gray-500 dark:text-gray-400 text-xs ml-3 whitespace-nowrap"><?php echo e($msg->created_at->diffForHumans()); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Aucun message récent</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notifications -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl shadow-xl shadow-amber-600/10 border border-gray-100/50 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 p-6">  
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-bell text-amber-500 mr-3"></i>
                        Notifications
                    </h3>
                </div>
                <div class="p-6">
                    <?php if(isset($notifications) && $notifications->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl hover:bg-amber-50 transition-colors duration-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h6 class="font-semibold text-gray-900 dark:text-white mb-1"><?php echo e($notif->title); ?></h6>
                                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed"><?php echo e(Str::limit($notif->message, 50)); ?></p>
                                        </div>
                                        <p class="text-gray-500 dark:text-gray-400 text-xs ml-3 whitespace-nowrap"><?php echo e($notif->created_at->diffForHumans()); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Aucune notification</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Graphique des ventes -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl shadow-xl shadow-indigo-600/10 border border-gray-100/50 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-chart-line text-indigo-500 mr-3"></i>
                    Évolution des ventes (6 derniers mois)
                </h3>
            </div>
            <div class="p-6">
                <?php
                    // Données de démonstration si $salesChart n'existe pas
                    $chartData = $salesChart ?? [
                        'labels' => ['Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct'],
                        'data' => [12, 19, 15, 25, 22, 30]
                    ];
                    $maxValue = max($chartData['data']) ?: 1;
                ?>
                
                <!-- Graphique en barres visuelles -->
                <div class="p-4 bg-white dark:bg-gray-800">
                    <!-- Message d'aide scroll mobile (visible uniquement sur mobile) -->
                    <div id="chart-scroll-hint" class="hidden text-center text-gray-500 dark:text-gray-400 text-xs mb-2 p-2 bg-gray-100 dark:bg-gray-800 rounded">
                        <i class="fas fa-hand-point-right"></i> Faites défiler horizontalement pour voir tous les mois
                    </div>
                    
                    <div id="sales-chart-container" class="flex gap-4 justify-between">
                        <?php $__currentLoopData = $chartData['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $percentage = ($value / $maxValue) * 100;
                                $colors = ['#8b5cf6', '#10b981', '#06b6d4', '#f59e0b', '#ef4444', '#6366f1'];
                                $color = $colors[$index % count($colors)];
                            ?>
                            <div class="flex-1 flex flex-col items-center gap-2 min-w-[60px]">
                                <!-- Valeur au-dessus de la barre -->
                                <div class="text-xl font-bold text-gray-800 dark:text-gray-100 min-h-[30px]">
                                    <?php echo e($value); ?>

                                </div>
                                <!-- Conteneur de barre avec fond visible -->
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-lg relative overflow-hidden flex-grow flex items-end border border-gray-300 dark:border-gray-600 h-32">
                                    <div class="chart-bar w-full rounded-t-lg transition-all duration-1000 ease-out relative shadow-inner"
                                         style="background: linear-gradient(180deg, <?php echo e($color); ?> 0%, <?php echo e($color); ?>cc 100%); height: 0%;"
                                         data-height="<?php echo e($percentage); ?>">
                                         <!-- Mini étiquette sur la barre -->
                                         <div class="absolute top-1 left-0 right-0 text-center text-white text-xs font-semibold opacity-90">
                                             <?php echo e(number_format($percentage, 0)); ?>%
                                         </div>
                                    </div>
                                </div>
                                <!-- Label du mois -->
                                <div class="text-sm font-semibold text-gray-600 dark:text-gray-300 mt-2">
                                    <?php echo e($chartData['labels'][$index]); ?>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <!-- Légende et statistiques -->
                    <div class="grid grid-cols-3 gap-4 mt-8 pt-6 border-t-2 border-gray-100">
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-2">TOTAL</div>
                            <div class="text-3xl font-bold text-primary-600">
                                <?php echo e(array_sum($chartData['data'])); ?>

                            </div>
                            <div class="text-xs text-gray-400 mt-1">ventes</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-2">MOYENNE</div>
                            <div class="text-3xl font-bold text-emerald-600">
                                <?php echo e(round(array_sum($chartData['data']) / count($chartData['data']), 1)); ?>

                            </div>
                            <div class="text-xs text-gray-400 mt-1">par mois</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-2">MEILLEUR</div>
                            <div class="text-3xl font-bold text-amber-600">
                                <?php echo e(max($chartData['data'])); ?>

                            </div>
                            <div class="text-xs text-gray-400 mt-1"><?php echo e($chartData['labels'][array_search(max($chartData['data']), $chartData['data'])]); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<script>
    // Animation des barres du graphique avec fallback
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🎨 Initialisation du graphique...');
        
        const bars = document.querySelectorAll('.chart-bar');
        console.log('📊 Barres trouvées:', bars.length);
        
        if (bars.length === 0) {
            console.warn('⚠️ Aucune barre trouvée!');
            return;
        }
        
        // Animation immédiate avec délai progressif
        bars.forEach((bar, index) => {
            const targetHeight = bar.getAttribute('data-height');
            console.log(`Barre ${index}: hauteur cible = ${targetHeight}%`);
            
            setTimeout(() => {
                bar.style.height = targetHeight + '%';
                bar.style.opacity = '1';
            }, 200 + (index * 100)); // Délai progressif pour effet cascade
        });
        
        // Observer pour réanimer si nécessaire (scroll)
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && entry.target.style.height === '0%') {
                        const bar = entry.target;
                        const targetHeight = bar.getAttribute('data-height');
                        setTimeout(() => {
                            bar.style.height = targetHeight + '%';
                            bar.style.opacity = '1';
                        }, 100);
                    }
                });
            }, { threshold: 0.1 });
            
            bars.forEach(bar => observer.observe(bar));
        }
        
        console.log('✅ Graphique initialisé avec succès');
    });
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/dashboard/index.blade.php ENDPATH**/ ?>