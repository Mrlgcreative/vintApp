@props([
    'variant' => 'default', // 'default', 'admin', 'minimal'
    'showNewsletter' => true,
    'showSocial' => true,
])

@php
    $isAdmin = $variant === 'admin';
    $isMinimal = $variant === 'minimal';
@endphp

<footer class="{{ $isAdmin ? 'bg-gray-100 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700' : 'bg-gray-800 dark:bg-gray-900' }} text-gray-{{ $isAdmin ? '600 dark:text-gray-400' : '300' }} py-{{ $isMinimal ? '6' : '12' }} mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(!$isMinimal)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <!-- À propos -->
                <div class="col-span-2 md:col-span-1">
                    <h5 class="font-semibold {{ $isAdmin ? 'text-gray-900 dark:text-white' : 'text-white' }} mb-4">
                        <x-app-brand 
                            :show-logo="true"
                            :show-name="true"
                            logo-height="24px"
                            logo-width="80px"
                            name-size="1.25rem"
                            :name-class="$isAdmin ? 'text-gray-900 dark:text-white' : 'text-white'"
                        />
                    </h5>
                    <p class="text-sm {{ $isAdmin ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400' }}">
                        {{ $appDescription ?? 'La marketplace de confiance pour acheter et vendre des articles d\'occasion.' }}
                    </p>
                </div>
                
                <!-- Navigation -->
                <div>
                    <h6 class="font-semibold {{ $isAdmin ? 'text-gray-900 dark:text-white' : 'text-white' }} mb-4">Navigation</h6>
                    <ul class="space-y-2 text-sm">
                        @if($isAdmin)
                            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">Dashboard</a></li>
                            <li><a href="{{ route('admin.users.index') }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">Utilisateurs</a></li>
                            <li><a href="{{ route('admin.items.index') }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">Articles</a></li>
                            <li><a href="{{ route('admin.orders.index') }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">Commandes</a></li>
                        @else
                            <li><a href="{{ route('items.index') }}" class="hover:text-white transition-colors">Articles</a></li>
                            <li><a href="{{ route('categories.index') }}" class="hover:text-white transition-colors">Catégories</a></li>
                            <li><a href="{{ route('brands.index') }}" class="hover:text-white transition-colors">Marques</a></li>
                            @auth
                                <li><a href="{{ route('items.my-items') }}" class="hover:text-white transition-colors">Mes articles</a></li>
                            @endauth
                        @endif
                    </ul>
                </div>
                
                <!-- Support -->
                <div>
                    <h6 class="font-semibold {{ $isAdmin ? 'text-gray-900 dark:text-white' : 'text-white' }} mb-4">Support</h6>
                    <ul class="space-y-2 text-sm">
                        @if($isAdmin)
                            <li><a href="{{ route('admin.support.index') }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">Tickets Support</a></li>
                            <li><a href="{{ route('admin.settings.index') }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">Paramètres</a></li>
                        @else
                            <li><a href="{{ route('support.index') }}" class="hover:text-white transition-colors">Support Client</a></li>
                            <li><a href="{{ route('help.index') }}" class="hover:text-white transition-colors">Centre d'aide</a></li>
                            <li><a href="{{ route('help.index') }}#contact" class="hover:text-white transition-colors">Contact</a></li>
                            <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">CGU</a></li>
                            <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Confidentialité</a></li>
                        @endif
                    </ul>
                </div>
                
                <!-- Réseaux sociaux -->
                @if($showSocial)
                    <div>
                        <h6 class="font-semibold {{ $isAdmin ? 'text-gray-900 dark:text-white' : 'text-white' }} mb-4">Suivez-nous</h6>
                        <div class="flex space-x-3">
                            <a href="https://facebook.com/vintapp" target="_blank" class="{{ $isAdmin ? 'text-gray-400 hover:text-blue-600' : 'text-gray-400 hover:text-white' }} transition-colors">
                                <i class="fab fa-facebook-f text-lg"></i>
                            </a>
                            <a href="https://twitter.com/vintapp" target="_blank" class="{{ $isAdmin ? 'text-gray-400 hover:text-blue-400' : 'text-gray-400 hover:text-white' }} transition-colors">
                                <i class="fab fa-twitter text-lg"></i>
                            </a>
                            <a href="https://instagram.com/vintapp" target="_blank" class="{{ $isAdmin ? 'text-gray-400 hover:text-pink-600' : 'text-gray-400 hover:text-white' }} transition-colors">
                                <i class="fab fa-instagram text-lg"></i>
                            </a>
                            @if($isAdmin)
                                <a href="https://linkedin.com/company/vintapp" target="_blank" class="text-gray-400 hover:text-blue-700 transition-colors">
                                    <i class="fab fa-linkedin-in text-lg"></i>
                                </a>
                            @endif
                        </div>
                        
                        @if($isAdmin)
                            <!-- Version info pour admin -->
                            <div class="mt-4 text-xs text-gray-400 dark:text-gray-500">
                                <p>Version: {{ config('app.version', '1.0.0') }}</p>
                                <p>Laravel: {{ app()->version() }}</p>
                                <p>PHP: {{ phpversion() }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            
            <!-- Newsletter -->
            @if($showNewsletter && !$isAdmin)
                <div class="border-t border-gray-700 dark:border-gray-800 mt-8 pt-8">
                    <div class="text-center max-w-md mx-auto">
                        <h5 class="font-semibold text-white mb-3">📧 Newsletter</h5>
                        <p class="text-sm text-gray-400 mb-4">Recevez nos dernières offres et nouveautés.</p>
                        <form id="newsletterForm" class="flex gap-2">
                            @csrf
                            <input type="email" id="newsletterEmail" 
                                   class="flex-1 px-3 py-2 bg-gray-700 dark:bg-gray-800 text-white rounded-md border border-gray-600 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-vinted-primary-500 focus:border-transparent" 
                                   placeholder="Votre email" required>
                            <button type="submit" class="px-4 py-2 bg-vinted-primary-600 text-white rounded-md hover:bg-vinted-primary-700 transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        <div id="newsletterMessage" class="mt-2 text-sm"></div>
                    </div>
                </div>
            @endif
        @endif
        
        <!-- Copyright -->
        <div class="{{ !$isMinimal ? 'border-t border-gray-'.($isAdmin ? '200 dark:border-gray-700' : '700 dark:border-gray-800').' mt-8 pt-8' : '' }} text-center">
            <p class="text-sm {{ $isAdmin ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400' }}">
                © {{ date('Y') }} {{ $appName ?? config('app.name', 'VintApp') }}. Tous droits réservés.
                @if($isAdmin)
                    <span class="mx-2">|</span>
                    <a href="{{ url('/') }}" target="_blank" class="hover:text-gray-900 dark:hover:text-white transition-colors">
                        <i class="fas fa-external-link-alt mr-1 text-xs"></i>Voir le site
                    </a>
                @endif
            </p>
        </div>
    </div>
</footer>
