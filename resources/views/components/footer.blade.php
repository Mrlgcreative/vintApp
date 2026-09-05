@props([
    'variant' => 'default', // 'default', 'admin', 'minimal'
    'showNewsletter' => true,
    'showSocial' => true,
])

@php
    $isAdmin = $variant === 'admin';
    $isMinimal = $variant === 'minimal';
@endphp

<footer class="{{ $isMinimal ? 'bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400' : 'bg-gray-50 dark:bg-gray-950 border-t border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400' }} py-{{ $isMinimal ? '6' : '12' }} mt-8 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(!$isMinimal)
            {{-- Marque + Newsletter --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8 pb-10 {{ !$isAdmin ? 'border-b border-gray-200 dark:border-gray-800' : '' }}">
                <div class="max-w-sm">
                    <h5 class="mb-3">
                        <x-app-brand
                            :show-logo="true"
                            :show-name="true"
                            logo-height="24px"
                            logo-width="80px"
                            name-size="1.1rem"
                            name-class="text-gray-900 dark:text-white"
                        />
                    </h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $appDescription ?? 'La marketplace de confiance pour acheter et vendre des articles d\'occasion.' }}
                    </p>
                </div>

                @if($showNewsletter && !$isAdmin)
                    <div class="w-full md:w-auto md:max-w-md">
                        <h6 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Newsletter</h6>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Recevez nos dernières offres et nouveautés.</p>
                        <form id="newsletterForm" class="flex gap-2">
                            @csrf
                            <input type="email" id="newsletterEmail"
                                   class="flex-1 h-10 px-3.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors"
                                   placeholder="Votre email" required>
                            <button type="submit" class="h-10 px-4 bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white rounded-md text-sm font-medium inline-flex items-center gap-2 transition-colors">
                                <i class="fas fa-paper-plane text-xs"></i>
                                S'abonner
                            </button>
                        </form>
                        <div id="newsletterMessage" class="mt-2 text-sm"></div>
                    </div>
                @endif
            </div>

            {{-- 3 colonnes sur mobile --}}
            <div class="grid grid-cols-3 gap-4 sm:gap-6 mt-10">
                <!-- Navigation -->
                <div>
                    <h6 class="text-xs font-semibold uppercase tracking-wider text-gray-900 dark:text-white mb-4 {{ $isAdmin ? '' : '' }}">Navigation</h6>
                    <ul class="space-y-2.5 text-sm">
                        @if($isAdmin)
                            <li><a href="{{ route('admin.dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Dashboard</a></li>
                            <li><a href="{{ route('admin.users.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Utilisateurs</a></li>
                            <li><a href="{{ route('admin.items.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Articles</a></li>
                            <li><a href="{{ route('admin.orders.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Commandes</a></li>
                        @else
                            <li><a href="{{ route('items.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Articles</a></li>
                            <li><a href="{{ route('promotions') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Promotions</a></li>
                            <li><a href="{{ route('expositions.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Expos</a></li>
                            <li><a href="{{ route('categories.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Catégories</a></li>
                            <li><a href="{{ route('brands.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Marques</a></li>
                            @auth
                                <li><a href="{{ route('items.my-items') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Mes articles</a></li>
                            @endauth
                        @endif
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h6 class="text-xs font-semibold uppercase tracking-wider text-gray-900 dark:text-white mb-4">Support</h6>
                    <ul class="space-y-2.5 text-sm">
                        @if($isAdmin)
                            <li><a href="{{ route('admin.support.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Tickets Support</a></li>
                            <li><a href="{{ route('admin.settings.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Paramètres</a></li>
                        @else
                            <li><a href="{{ route('support.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Support Client</a></li>
                            <li><a href="{{ route('help.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Centre d'aide</a></li>
                            <li><a href="{{ route('help.index') }}#contact" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Contact</a></li>
                            <li><a href="{{ route('terms') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">CGU</a></li>
                            <li><a href="{{ route('privacy') }}" class="text-gray-600 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">Confidentialité</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Réseaux sociaux -->
                @if($showSocial)
                    <div>
                        <h6 class="text-xs font-semibold uppercase tracking-wider text-gray-900 dark:text-white mb-4">Suivez-nous</h6>
                        <div class="flex flex-col items-start gap-3">
                            <div class="flex gap-2">
                                <a href="https://facebook.com/vintapp" target="_blank" aria-label="Facebook" class="w-9 h-9 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 hover:text-white hover:bg-vinted-primary-600 hover:border-vinted-primary-600 flex items-center justify-center transition-all duration-200">
                                    <i class="fab fa-facebook-f text-sm"></i>
                                </a>
                                <a href="https://twitter.com/vintapp" target="_blank" aria-label="Twitter" class="w-9 h-9 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 hover:text-white hover:bg-vinted-primary-600 hover:border-vinted-primary-600 flex items-center justify-center transition-all duration-200">
                                    <i class="fab fa-twitter text-sm"></i>
                                </a>
                                <a href="https://instagram.com/vintapp" target="_blank" aria-label="Instagram" class="w-9 h-9 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 hover:text-white hover:bg-vinted-primary-600 hover:border-vinted-primary-600 flex items-center justify-center transition-all duration-200">
                                    <i class="fab fa-instagram text-sm"></i>
                                </a>
                                @if($isAdmin)
                                    <a href="https://linkedin.com/company/vintapp" target="_blank" aria-label="LinkedIn" class="w-9 h-9 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 hover:text-white hover:bg-vinted-primary-600 hover:border-vinted-primary-600 flex items-center justify-center transition-all duration-200">
                                        <i class="fab fa-linkedin-in text-sm"></i>
                                    </a>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-500">Rejoignez la communauté</p>
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
        @endif

        <!-- Copyright -->
        <div class="{{ !$isMinimal ? 'border-t border-gray-200 dark:border-gray-800 mt-10 pt-6' : '' }} flex flex-col sm:flex-row items-center justify-between gap-3 text-center">
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                © {{ date('Y') }} {{ $appName ?? config('app.name', 'VintApp') }}. Tous droits réservés.
            </p>
            @if($isAdmin)
                <a href="{{ url('/') }}" target="_blank" class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">
                    <i class="fas fa-external-link-alt mr-1"></i>Voir le site
                </a>
            @endif
        </div>
    </div>
</footer>