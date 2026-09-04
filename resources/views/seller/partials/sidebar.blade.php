<aside class="w-64 lg:w-72 flex-shrink-0 hidden lg:block min-h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 sticky top-0">
    <div class="p-5">
        <div class="flex items-center gap-3 mb-7">
            <div class="w-10 h-10 rounded-lg bg-vinted-primary-600 flex items-center justify-center text-white shadow-md shadow-vinted-primary-600/30">
                <i class="fas fa-store"></i>
            </div>
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">Espace vendeur</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[140px]">{{ Auth::user()->name }}</p>
            </div>
        </div>

        <nav class="space-y-1">
            <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.dashboard') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas fa-chart-pie w-5 text-center"></i> Tableau de bord
            </a>
            <a href="{{ route('seller.items') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.items') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas fa-box w-5 text-center"></i> Mes articles
            </a>
            <a href="{{ route('seller.sales') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.sales') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas fa-shopping-cart w-5 text-center"></i> Mes ventes
            </a>
            <a href="{{ route('seller.offers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.offers.*') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas fa-tags w-5 text-center"></i> Mes offres
            </a>
            <a href="{{ route('seller.expositions.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.expositions.*') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas fa-store w-5 text-center"></i> Mes expositions
            </a>
            <a href="{{ route('seller.wallet') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.wallet') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas fa-wallet w-5 text-center"></i> Mon wallet
            </a>
            <a href="{{ route('messages.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('messages.*') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas fa-envelope w-5 text-center"></i> Messagerie
            </a>
            <a href="{{ route('seller.categories') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.categories') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas fa-tags w-5 text-center"></i> Catégories
            </a>
            <a href="{{ route('seller.brands') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.brands') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas fa-building w-5 text-center"></i> Marques
            </a>
            <a href="{{ route('seller.reviews') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.reviews') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas fa-star w-5 text-center"></i> Mes notes
            </a>
        </nav>

        <div class="mt-7 pt-5 border-t border-gray-200 dark:border-gray-800 space-y-2">
            <a href="{{ route('items.create') }}" class="flex items-center justify-center gap-2 w-full h-10 bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white rounded-md text-sm font-medium transition-colors">
                <i class="fas fa-plus"></i> Nouvel article
            </a>
            <a href="{{ url('/') }}" class="flex items-center justify-center gap-2 w-full h-10 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-md text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</aside>

{{-- Mobile hamburger + drawer --}}
<div class="lg:hidden fixed top-[70px] right-4 z-[60]">
    <button id="seller-drawer-toggle" class="w-10 h-10 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" aria-label="Menu vendeur">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>
    </button>
</div>

<div id="seller-drawer-overlay" class="lg:hidden fixed inset-0 z-[55] bg-black/40 backdrop-blur-sm hidden transition-opacity duration-300"></div>

<div id="seller-drawer" class="lg:hidden fixed top-0 left-0 z-[60] h-full w-72 max-w-[85vw] bg-white dark:bg-gray-900 shadow-2xl border-r border-gray-200 dark:border-gray-800 transform -translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
    <div class="p-5">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-vinted-primary-600 flex items-center justify-center text-white shadow-md shadow-vinted-primary-600/30">
                    <i class="fas fa-store text-sm"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-sm text-gray-900 dark:text-white">Espace vendeur</h2>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ Auth::user()->name }}</p>
                </div>
            </div>
            <button id="seller-drawer-close" class="w-8 h-8 rounded-md flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="space-y-1">
            <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.dashboard') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}" onclick="closeDrawer()">
                <i class="fas fa-chart-pie w-5 text-center"></i> Tableau de bord
            </a>
            <a href="{{ route('seller.items') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.items') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}" onclick="closeDrawer()">
                <i class="fas fa-box w-5 text-center"></i> Mes articles
            </a>
            <a href="{{ route('seller.sales') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.sales') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}" onclick="closeDrawer()">
                <i class="fas fa-shopping-cart w-5 text-center"></i> Mes ventes
            </a>
            <a href="{{ route('seller.offers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.offers.*') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}" onclick="closeDrawer()">
                <i class="fas fa-tags w-5 text-center"></i> Mes offres
            </a>
            <a href="{{ route('seller.expositions.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.expositions.*') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}" onclick="closeDrawer()">
                <i class="fas fa-store w-5 text-center"></i> Mes expositions
            </a>
            <a href="{{ route('seller.wallet') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.wallet') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}" onclick="closeDrawer()">
                <i class="fas fa-wallet w-5 text-center"></i> Mon wallet
            </a>
            <hr class="my-2 border-gray-200 dark:border-gray-800">
            <a href="{{ route('messages.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('messages.*') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}" onclick="closeDrawer()">
                <i class="fas fa-envelope w-5 text-center"></i> Messagerie
            </a>
            <a href="{{ route('seller.categories') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.categories') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}" onclick="closeDrawer()">
                <i class="fas fa-tags w-5 text-center"></i> Catégories
            </a>
            <a href="{{ route('seller.brands') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.brands') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}" onclick="closeDrawer()">
                <i class="fas fa-building w-5 text-center"></i> Marques
            </a>
            <a href="{{ route('seller.reviews') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('seller.reviews') ? 'bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}" onclick="closeDrawer()">
                <i class="fas fa-star w-5 text-center"></i> Mes notes
            </a>
        </nav>

        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-800 space-y-2">
            <a href="{{ route('items.create') }}" class="flex items-center justify-center gap-2 w-full h-10 bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white rounded-md text-sm font-medium transition-colors" onclick="closeDrawer()">
                <i class="fas fa-plus"></i> Nouvel article
            </a>
            <a href="{{ url('/') }}" class="flex items-center justify-center gap-2 w-full h-10 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-md text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors" onclick="closeDrawer()">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
const drawerToggle = document.getElementById('seller-drawer-toggle');
const drawerClose = document.getElementById('seller-drawer-close');
const drawer = document.getElementById('seller-drawer');
const overlay = document.getElementById('seller-drawer-overlay');

function openDrawer() {
    drawer.classList.remove('-translate-x-full');
    drawer.classList.add('translate-x-0');
    overlay.classList.remove('hidden');
    setTimeout(() => overlay.classList.add('opacity-100'), 10);
}

function closeDrawer() {
    drawer.classList.add('-translate-x-full');
    drawer.classList.remove('translate-x-0');
    overlay.classList.remove('opacity-100');
    setTimeout(() => overlay.classList.add('hidden'), 300);
}

if (drawerToggle) drawerToggle.addEventListener('click', openDrawer);
if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
if (overlay) overlay.addEventListener('click', closeDrawer);
</script>
@endpush