<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zone non disponible - VintApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        .city-pill {
            transition: all 0.2s ease;
        }

        .city-pill:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .icon-bounce {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="max-w-4xl w-full">
            <!-- Card principale -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden card-hover">
                <!-- Header avec icône -->
                <div class="relative bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-6 py-12 sm:px-12 sm:py-16 overflow-hidden">
                    <!-- Cercles décoratifs -->
                    <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white opacity-10 rounded-full translate-x-1/2 translate-y-1/2"></div>
                    
                    <div class="relative text-center">
                        <!-- Icône principale avec animation -->
                        <div class="inline-flex items-center justify-center w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-orange-400 to-red-500 rounded-full shadow-2xl floating mb-6">
                            <i class="fas fa-map-marked-alt text-4xl sm:text-5xl text-white"></i>
                        </div>
                        
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-4">
                            Oops ! 🌍
                        </h1>
                        <p class="text-xl sm:text-2xl text-white/90 font-medium max-w-2xl mx-auto">
                            VintApp n'est pas encore disponible dans ta ville
                        </p>
                    </div>
                </div>

                <!-- Contenu -->
                <div class="px-6 py-8 sm:px-12 sm:py-12 space-y-8">
                    <!-- Message principal -->
                    <div class="text-center space-y-4">
                        <p class="text-lg sm:text-xl text-gray-700 leading-relaxed max-w-3xl mx-auto">
                            Nous déployons progressivement notre plateforme pour garantir 
                            <span class="font-semibold text-purple-600">la meilleure expérience</span> possible à chaque utilisateur.
                        </p>
                    </div>

                    <!-- Raisons de la restriction -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 sm:p-8">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6 flex items-center justify-center sm:justify-start">
                            <i class="fas fa-info-circle text-purple-600 mr-3"></i>
                            Pourquoi cette restriction ?
                        </h2>
                        
                        <div class="space-y-5">
                            <div class="flex items-start space-x-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                                    <i class="fas fa-star text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1 text-base sm:text-lg">Qualité de service garantie</h3>
                                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                        Nous voulons assurer une expérience optimale et fluide à tous nos utilisateurs
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                                    <i class="fas fa-truck text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1 text-base sm:text-lg">Logistique de livraison</h3>
                                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                        Nous préparons nos partenaires de livraison dans chaque nouvelle ville
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                                    <i class="fas fa-vial text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1 text-base sm:text-lg">Tests et optimisations</h3>
                                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                        Nous testons rigoureusement toutes les fonctionnalités avant chaque expansion
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $allowedCities = \App\Models\AllowedCity::active()
                            ->orderBy('name')
                            ->pluck('name')
                            ->take(12);
                    @endphp

                    @if($allowedCities->isNotEmpty())
                    <!-- Villes disponibles -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 sm:p-8">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6 flex items-center justify-center sm:justify-start">
                            <i class="fas fa-city text-indigo-600 mr-3"></i>
                            Villes actuellement desservies
                        </h2>
                        
                        <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach($allowedCities as $city)
                            <div class="city-pill bg-white rounded-xl px-4 py-3 shadow-sm hover:shadow-md flex items-center space-x-2 cursor-pointer">
                                <i class="fas fa-map-pin text-indigo-500 text-sm"></i>
                                <span class="text-gray-800 font-medium text-sm truncate">{{ $city }}</span>
                            </div>
                            @endforeach
                        </div>

                        @if($allowedCities->count() >= 12)
                        <div class="text-center mt-4">
                            <p class="text-gray-500 text-sm italic">
                                <i class="fas fa-ellipsis-h mr-1"></i>
                                Et d'autres villes bientôt...
                            </p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- CTA Section -->
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-6 sm:p-8">
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full mb-4 icon-bounce shadow-xl">
                                <i class="fas fa-bell text-2xl sm:text-3xl text-white"></i>
                            </div>
                            <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mb-3">
                                Tu veux qu'on arrive dans ta ville ? 🚀
                            </h3>
                            <p class="text-gray-600 text-sm sm:text-base max-w-xl mx-auto">
                                Inscris-toi à notre liste d'attente et sois le premier informé lors du lancement dans ta région !
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-stretch sm:items-center max-w-2xl mx-auto">
                            <a href="{{ route('preregistration.index') }}" 
                               class="btn-primary text-white px-6 sm:px-8 py-4 rounded-xl font-semibold text-base sm:text-lg shadow-lg inline-flex items-center justify-center space-x-2 flex-1 sm:flex-initial">
                                <i class="fas fa-envelope"></i>
                                <span>Me notifier lors du lancement</span>
                            </a>
                            
                            <a href="mailto:support@vintapp.com" 
                               class="bg-white border-2 border-gray-300 text-gray-700 hover:border-purple-500 hover:text-purple-600 px-6 sm:px-8 py-4 rounded-xl font-semibold text-base sm:text-lg inline-flex items-center justify-center space-x-2 transition-all flex-1 sm:flex-initial">
                                <i class="fas fa-headset"></i>
                                <span>Nous contacter</span>
                            </a>
                        </div>
                    </div>

                    <!-- Footer social -->
                    <div class="border-t border-gray-200 pt-8">
                        <div class="text-center space-y-5">
                            <p class="text-gray-700 font-semibold text-base sm:text-lg">
                                Suivez-nous sur les réseaux sociaux
                            </p>
                            <div class="flex justify-center space-x-4 sm:space-x-6">
                                <a href="#" class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform shadow-lg hover:shadow-xl" title="Facebook">
                                    <i class="fab fa-facebook-f text-lg"></i>
                                </a>
                                <a href="#" class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform shadow-lg hover:shadow-xl" title="Instagram">
                                    <i class="fab fa-instagram text-lg"></i>
                                </a>
                                <a href="#" class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform shadow-lg hover:shadow-xl" title="Twitter">
                                    <i class="fab fa-twitter text-lg"></i>
                                </a>
                                <a href="#" class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform shadow-lg hover:shadow-xl" title="WhatsApp">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </a>
                            </div>
                            <p class="text-gray-500 text-sm">
                                © 2025 <span class="font-semibold text-purple-600">VintApp</span>. Tous droits réservés.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Badge "Bientôt disponible" animé -->
            <div class="text-center mt-6">
                <div class="inline-flex items-center space-x-2 bg-white/90 backdrop-blur-sm px-6 py-3 rounded-full shadow-lg">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-semibold text-gray-700">Expansion en cours dans toute la RDC</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
