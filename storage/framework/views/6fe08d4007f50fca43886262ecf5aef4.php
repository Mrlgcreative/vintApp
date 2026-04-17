<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Espace Agent'); ?> - <?php echo e($appName ?? 'VintApp'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset($appFavicon ?? '/favicon.png')); ?>">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/dynamic-colors.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    <div class="min-h-full flex flex-col">

        
        <header class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-14 sm:h-16">
                    
                    <a href="<?php echo e(route('agent.dashboard')); ?>" class="flex items-center gap-2 sm:gap-3 group">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-lg flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                            <i class="fas fa-headset text-white text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white leading-tight">Espace Agent</h1>
                            <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 leading-tight hidden sm:block">Support <?php echo e($appName ?? 'VintApp'); ?></p>
                        </div>
                    </a>

                    
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="<?php echo e(route('agent.dashboard')); ?>"
                           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('agent.dashboard') ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                            <i class="fas fa-tachometer-alt text-xs"></i>
                            <span>Tableau de bord</span>
                        </a>
                        <a href="<?php echo e(route('agent.tickets')); ?>"
                           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('agent.tickets') ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                            <i class="fas fa-inbox text-xs"></i>
                            <span>Mes Tickets</span>
                            <?php
                                $myActiveCount = \App\Models\SupportChat::where('admin_id', auth()->id())->whereNotIn('status', ['closed'])->count();
                            ?>
                            <?php if($myActiveCount > 0): ?>
                                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold rounded-full bg-emerald-500 text-white"><?php echo e($myActiveCount); ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?php echo e(route('agent.unassigned')); ?>"
                           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('agent.unassigned') ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            <span>Non assignés</span>
                            <?php
                                $unassignedCount = \App\Models\SupportChat::whereNull('admin_id')->whereIn('status', ['open'])->count();
                            ?>
                            <?php if($unassignedCount > 0): ?>
                                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold rounded-full bg-red-500 text-white"><?php echo e($unassignedCount); ?></span>
                            <?php endif; ?>
                        </a>
                    </nav>

                    
                    <div class="flex items-center gap-2 sm:gap-3">
                        
                        <div class="flex md:hidden items-center gap-1">
                            <a href="<?php echo e(route('agent.dashboard')); ?>"
                               class="p-2 rounded-lg transition-colors <?php echo e(request()->routeIs('agent.dashboard') ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                                <i class="fas fa-tachometer-alt"></i>
                            </a>
                            <a href="<?php echo e(route('agent.tickets')); ?>"
                               class="p-2 rounded-lg transition-colors relative <?php echo e(request()->routeIs('agent.tickets') ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                                <i class="fas fa-inbox"></i>
                                <?php if($myActiveCount > 0): ?>
                                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 text-[9px] font-bold bg-emerald-500 text-white rounded-full flex items-center justify-center"><?php echo e($myActiveCount > 9 ? '9+' : $myActiveCount); ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="<?php echo e(route('agent.unassigned')); ?>"
                               class="p-2 rounded-lg transition-colors relative <?php echo e(request()->routeIs('agent.unassigned') ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'); ?>">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php if($unassignedCount > 0): ?>
                                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 text-[9px] font-bold bg-red-500 text-white rounded-full flex items-center justify-center"><?php echo e($unassignedCount > 9 ? '9+' : $unassignedCount); ?></span>
                                <?php endif; ?>
                            </a>
                        </div>

                        <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

                        
                        <?php
                            $currentAgent = \App\Models\SupportAgent::where('user_id', auth()->id())->first();
                        ?>
                        <?php if($currentAgent): ?>
                            <span class="hidden sm:inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full <?php echo e($currentAgent->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'); ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?php echo e($currentAgent->is_active ? 'bg-green-500' : 'bg-gray-400'); ?> mr-1.5"></span>
                                <?php echo e($currentAgent->is_active ? 'En ligne' : 'Hors ligne'); ?>

                            </span>
                        <?php endif; ?>

                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                    <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                                </div>
                                <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(Auth::user()->name); ?></span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400 hidden sm:block"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 py-1 z-50">
                                <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-600">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e(Auth::user()->name); ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo e(Auth::user()->email); ?></p>
                                </div>
                                <a href="<?php echo e(route('profile.index')); ?>" class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-user mr-2 text-gray-400"></i>Mon profil
                                </a>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <?php if(session('success')): ?>
                <div class="mt-4 flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <i class="fas fa-check-circle flex-shrink-0"></i>
                    <span class="flex-1 text-sm"><?php echo e(session('success')); ?></span>
                    <button type="button" class="text-green-400 hover:text-green-600" @click="show = false"><i class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="mt-4 flex items-center gap-3 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                    <span class="flex-1 text-sm"><?php echo e(session('error')); ?></span>
                    <button type="button" class="text-red-400 hover:text-red-600" @click="show = false"><i class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>
        </div>

        
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        
        <footer class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>&copy; <?php echo e(date('Y')); ?> <?php echo e($appName ?? 'VintApp'); ?> — Espace Agent</span>
                <span>
                    <?php if($currentAgent): ?>
                        <?php echo e($currentAgent->activeChatsCount()); ?>/<?php echo e($currentAgent->max_chats); ?> tickets actifs
                    <?php endif; ?>
                </span>
            </div>
        </footer>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\Mes projets\vintApp\resources\views/layouts/agent.blade.php ENDPATH**/ ?>