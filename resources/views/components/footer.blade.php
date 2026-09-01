@props([
    'variant' => 'default', // 'default', 'admin', 'minimal'
    'showNewsletter' => true,
    'showSocial' => true,
])

@php
    $isAdmin = $variant === 'admin';
    $isMinimal = $variant === 'minimal';
@endphp

<footer class="{{ $isAdmin ? 'bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400' : 'bg-gray-900 dark:bg-gray-950 text-gray-300' }} py-{{ $isMinimal ? '6' : '12' }} mt-8 relative">
    @if(!$isMinimal && !$isAdmin)
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-vinted-primary-700 via-vinted-primary-500 to-vinted-primary-700"></div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(!$isMinimal)
            <div class="grid grid-cols-3 gap-4 sm:gap-6 md:grid-cols-4">
                <!-- À propos -->
                <div>
                    <h5 class="mb-4">
                        <x-app-brand
                            :show-logo="true"
                            :show-name="true"
                            logo-height="24px"
                            logo-width="80px"
                            name-size="1.1rem"
                            :name-class="$isAdmin ? 'text-gray-900 dark:text-white' : 'text-white'"
                        />
                    </h5>
                    <p class="text-xs sm:text-sm {{ $isAdmin ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400' }}">
                        {{ $appDescription ?? 'La marketplace de confiance pour acheter et vendre des articles d\'occasion.' }}
                    </p>
                </div>

                <!-- Navigation -->
                <div>
                    <h6 class="text-xs font-semibold uppercase tracking-wider {{ $isAdmin ? 'text-gray-900 dark:text-white' : 'text-white' }} mb-4">Navigation</h6>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        @if($isAdmin)
                            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-vinted-primary-600 transition-colors">Dashboard</a></li>
                            <li><a href="{{ route('admin.users.index') }}" class="hover:text-vinted-primary-600 transition-colors">Utilisateurs</a></li>
                            <li><a href="{{ route('admin.items.index') }}" class="hover:text-vinted-primary-600 transition-colors">Articles</a></li>
                            <li><a href="{{ route('admin.orders.index') }}" class="hover:text-vinted-primary-600 transition-colors">Commandes</a></li>
                        @else
                            <li><a href="{{ route('items.index') }}" class="hover:text-vinted-primary-400 transition-colors">Articles</a></li>
                            <li><a href="{{ route('categories.index') }}" class="hover:text-vinted-primary-400 transition-colors">Catégories</a></li>
                            <li><a href="{{ route('brands.index') }}" class="hover:text-vinted-primary-400 transition-colors">Marques</a></li>
                            @auth
                                <li><a href="{{ route('items.my-items') }}" class="hover:text-vinted-primary-400 transition-colors">Mes articles</a></li>
                            @endauth
                        @endif
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h6 class="text-xs font-semibold uppercase tracking-wider {{ $isAdmin ? 'text-gray-900 dark:text-white' : 'text-white' }} mb-4">Support</h6>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        @if($isAdmin)
                            <li><a href="{{ route('admin.support.index') }}" class="hover:text-vinted-primary-600 transition-colors">Tickets Support</a></li>
                            <li><a href="{{ route('admin.settings.index') }}" class="hover:text-vinted-primary-600 transition-colors">Paramètres</a></li>
                        @else
                            <li><a href="{{ route('support.index') }}" class="hover:text-vinted-primary-400 transition-colors">Support Client</a></li>
                            <li><a href="{{ route('help.index') }}" class="hover:text-vinted-primary-400 transition-colors">Centre d'aide</a></li>
                            <li><a href="{{ route('help.index') }}#contact" class="hover:text-vinted-primary-400 transition-colors">Contact</a></li>
                            <li><a href="{{ route('terms') }}" class="hover:text-vinted-primary-400 transition-colors">CGU</a></li>
                            <li><a href="{{ route('privacy') }}" class="hover:text-vinted-primary-400 transition-colors">Confidentialité</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Réseaux sociaux -->
                @if($showSocial)
                    <div class="col-span-3 md:col-span-1">
                        <h6 class="text-xs font-semibold uppercase tracking-wider {{ $isAdmin ? 'text-gray-900 dark:text-white' : 'text-white' }} mb-4">Suivez-nous</h6>
                        <div class="flex space-x-2">
                            <a href="https://facebook.com/vintapp" target="_blank" aria-label="Facebook" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full {{ $isAdmin ? 'bg-gray-200 dark:bg-gray-700 hover:bg-blue-600 hover:text-white text-gray-500 dark:text-gray-300' : 'bg-white/10 hover:bg-vinted-primary-600 text-gray-300 hover:text-white' }} flex items-center justify-center transition-all duration-200">
                                <i class="fab fa-facebook-f text-sm"></i>
                            </a>
                            <a href="https://twitter.com/vintapp" target="_blank" aria-label="Twitter" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full {{ $isAdmin ? 'bg-gray-200 dark:bg-gray-700 hover:bg-blue-400 hover:text-white text-gray-500 dark:text-gray-300' : 'bg-white/10 hover:bg-vinted-primary-600 text-gray-300 hover:text-white' }} flex items-center justify-center transition-all duration-200">
                                <i class="fab fa-twitter text-sm"></i>
                            </a>
                            <a href="https://instagram.com/vintapp" target="_blank" aria-label="Instagram" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full {{ $isAdmin ? 'bg-gray-200 dark:bg-gray-700 hover:bg-pink-600 hover:text-white text-gray-500 dark:text-gray-300' : 'bg-white/10 hover:bg-vinted-primary-600 text-gray-300 hover:text-white' }} flex items-center justify-center transition-all duration-200">
                                <i class="fab fa-instagram text-sm"></i>
                            </a>
                            @if($isAdmin)
                                <a href="https://linkedin.com/company/vintapp" target="_blank" aria-label="LinkedIn" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-blue-700 hover:text-white text-gray-500 dark:text-gray-300 flex items-center justify-center transition-all duration-200">
                                    <i class="fab fa-linkedin-in text-sm"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            @if($isAdmin)
                <!-- Version info pour admin -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-400 dark:text-gray-500 flex flex-wrap gap-4">
                    <p>Version: {{ config('app.version', '1.0.0') }}</p>
                    <p>Laravel: {{ app()->version() }}</p>
                    <p>PHP: {{ phpversion() }}</p>
                </div>
            @endif

            <!-- Newsletter -->
            @if($showNewsletter && !$isAdmin)
                <div class="border-t border-white/10 dark:border-gray-800 mt-8 pt-8">
                    <div class="text-center max-w-md mx-auto">
                        <h5 class="font-semibold text-white mb-3">
                            <i class="fas fa-envelope text-vinted-primary-400 mr-2"></i>Newsletter
                        </h5>
                        <p class="text-sm text-gray-400 mb-4">Recevez nos dernières offres et nouveautés.</p>
                        <form id="newsletterForm" class="flex gap-2">
                            @csrf
                            <input type="email" id="newsletterEmail"
                                   class="flex-1 px-3 py-2.5 bg-white/5 dark:bg-gray-800 text-white rounded-lg border border-white/10 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-vinted-primary-500 focus:border-transparent"
                                   placeholder="Votre email" required>
                            <button type="submit" class="px-4 py-2.5 bg-vinted-primary-600 text-white rounded-lg hover:bg-vinted-primary-700 transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        <div id="newsletterMessage" class="mt-2 text-sm"></div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Copyright -->
        <div class="{{ !$isMinimal ? 'border-t border-white/10 dark:border-gray-800 mt-8 pt-6' : '' }} text-center">
            <p class="text-xs sm:text-sm {{ $isAdmin ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400' }}">
                © {{ date('Y') }} {{ $appName ?? config('app.name', 'VintApp') }}. Tous droits réservés.
                @if($isAdmin)
                    <span class="mx-2">|</span>
                    <a href="{{ url('/') }}" target="_blank" class="hover:text-vinted-primary-600 transition-colors">
                        <i class="fas fa-external-link-alt mr-1 text-xs"></i>Voir le site
                    </a>
                @endif
            </p>
        </div>
    </div>
</footer>