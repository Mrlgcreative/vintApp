<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'VintApp')); ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700;800;900&family=Montserrat:wght@500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN (fallback si Vite ne fonctionne pas) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind CSS via Vite -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>

    <!-- Custom Styles avec responsive -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .text-3d {
            position: relative;
            display: inline-block;
        }
        
        .text-3d::before {
            content: attr(data-text);
            position: absolute;
            left: 2px;
            top: 2px;
            color: #7c3aed;
            text-shadow: 0 0 10px rgba(124, 58, 237, 0.6);
            z-index: -1;
        }

        .text-3d::after {
            content: attr(data-text);
            position: absolute;
            left: 4px;
            top: 4px;
            color: #3b82f6;
            text-shadow: 0 0 15px rgba(59, 130, 246, 0.6);
            z-index: -2;
        }
        
        /* Animation pulse personnalisée */
        @keyframes pulse-custom {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }
        }
        
        .animate-pulse-custom {
            animation: pulse-custom 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        /* Animation des points */
        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .loading-dot {
            animation: bounce 1.4s infinite ease-in-out both;
        }
        
        .loading-dot:nth-child(1) {
            animation-delay: -0.32s;
        }
        
        .loading-dot:nth-child(2) {
            animation-delay: -0.16s;
        }
        
        /* Responsive font sizes */
        @media (max-width: 640px) {
            .logo-text {
                font-size: 3.5rem;
                line-height: 1;
            }
        }
        
        @media (min-width: 641px) and (max-width: 768px) {
            .logo-text {
                font-size: 5rem;
                line-height: 1;
            }
        }
        
        @media (min-width: 769px) {
            .logo-text {
                font-size: 7rem;
                line-height: 1;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-white to-blue-50 min-h-screen w-full flex items-center justify-center p-4 sm:p-6 md:p-8 overflow-hidden">

    <div class="text-center w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl mx-auto space-y-6 sm:space-y-8 md:space-y-10">

        <!-- Logo 3D Responsive -->
        <div class="relative">
            <h1 
                class="logo-text font-poppins font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-purple-600 via-purple-500 to-blue-500 drop-shadow-2xl animate-pulse-custom relative text-3d select-none"
                >
                VintApp
            </h1>
            
            <!-- Effet de brillance -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-30 animate-pulse pointer-events-none"></div>
        </div>

   
       
    </div>

    <script>
        // Redirection automatique après 3 secondes
        const redirectTimer = setTimeout(() => {
            window.location.href = "<?php echo e(route('home')); ?>";
        }, 3000);

        // Permettre de passer immédiatement au clic ou touch
        const redirectToHome = () => {
            clearTimeout(redirectTimer);
            window.location.href = "<?php echo e(route('home')); ?>";
        };

        document.addEventListener('click', redirectToHome);
        document.addEventListener('touchstart', redirectToHome, { passive: true });
        
        // Empêcher le zoom sur double tap (iOS)
        let lastTouchEnd = 0;
        document.addEventListener('touchend', (event) => {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>

</body>
</html><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/splash.blade.php ENDPATH**/ ?>