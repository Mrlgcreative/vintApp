<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Déploiement progressif - {{ config('app.name', 'VintApp') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#7c3aed',
                            50: '#faf5ff',
                            100: '#f3e8ff',
                            200: '#e9d5ff',
                            300: '#d8b4fe',
                            400: '#c084fc',
                            500: '#a855f7',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95'
                        },
                        accent: {
                            DEFAULT: '#ec4899',
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            200: '#fbcfe8',
                            300: '#f9a8d4',
                            400: '#f472b6',
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d'
                        }
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="max-w-4xl w-full">
            <!-- Card principale -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden card-hover">
                <!-- Header avec icône -->
                <div class="relative bg-gradient-to-r from-indigo-600 via-primary-600 to-accent-600 px-6 py-12 sm:px-12 sm:py-16 overflow-hidden">
                    <div class="absolute top-0 left-0 w-64 h-64 bg-white dark:bg-gray-800 opacity-10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white dark:bg-gray-800 opacity-10 rounded-full translate-x-1/2 translate-y-1/2"></div>

                    <div class="relative text-center">
                        <div class="inline-flex items-center justify-center w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-orange-400 to-red-500 rounded-full shadow-2xl floating mb-6">
                            <i class="fas fa-map-marked-alt text-4xl sm:text-5xl text-white"></i>
                        </div>

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-4">
                            Bientôt chez vous
                        </h1>
                        <p class="text-xl sm:text-2xl text-white/90 font-medium max-w-2xl mx-auto">
                            {{ config('app.name', 'VintApp') }} arrive progressivement dans ta ville
                        </p>
                    </div>
                </div>

                <!-- Contenu -->
                <div class="px-6 py-8 sm:px-12 sm:py-12 space-y-8">
                    <div class="text-center space-y-4">
                        <p class="text-lg sm:text-xl text-gray-700 dark:text-gray-200 leading-relaxed max-w-3xl mx-auto">
                            Nous déployons progressivement notre plateforme pour garantir
                            <span class="font-semibold text-primary-600">la meilleure expérience</span> possible à chaque utilisateur.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
