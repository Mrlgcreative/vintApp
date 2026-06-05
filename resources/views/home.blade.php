@extends('app')

@section('title', 'VintApp - Fashion Vintage')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    * { 
        font-family: 'Inter', sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    .font-display { 
        font-family: 'Playfair Display', serif; 
    }
    .scrollbar-hide::-webkit-scrollbar { 
        display: none; 
    }
    .scrollbar-hide { 
        -ms-overflow-style: none;
        scrollbar-width: none; 
    }
</style>
@endpush

@section('content')

<!-- Toast Notification -->
<div id="toast" class="fixed top-8 right-8 z-50 bg-black text-white px-6 py-4 rounded-lg shadow-2xl transform translate-x-[500px] transition-all duration-500 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span id="toastMessage" class="text-sm font-medium">Success</span>
</div>

<div class="min-h-screen bg-white">

    <!-- Hero Carousel Component -->
    <x-home.hero-carousel :slides="$heroSlides ?? collect()" />

    <!-- Search Bar Component -->
    <x-home.search-bar />

    <!-- Categories Grid Component -->
    <x-home.category-grid :categories="$categories" />

    <!-- Articles Récents Section -->
    <section id="collection" class="py-20 lg:py-32 bg-white">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- En-tête Section -->
            <div class="mb-12">
                <h2 class="font-display text-3xl lg:text-4xl font-bold text-gray-900">
                    Articles Récents
                </h2>
            </div>

            <!-- Grille Produits -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
                @forelse($latestItems as $item)
                    <x-home.product-card :item="$item" />
                @empty
                    <div class="col-span-full text-center py-20">
                        <div class="mb-4 flex justify-center">
                            <svg class="w-16 h-16 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Aucun article</h3>
                        <a href="{{ route('items.create') ?? '#' }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 text-white rounded-full font-medium hover:bg-gray-800 transition-all">
                            <span>Ajouter un Article</span>
                        </a>
                    </div>
                @endforelse
            </div>
            
            @if($latestItems && $latestItems->count() > 0)
                <div class="text-center mt-12">
                    <a href="{{ route('items.index') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 bg-gray-900 text-white rounded-full font-medium hover:bg-gray-800 transition-all">
                        <span>Voir Plus d'Articles</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Features Section Component -->
    <x-home.features-section />

    <!-- CTA Section Component -->
    <x-home.cta-section />

</div>

<!-- Filter Modal Component -->
<x-home.filter-modal :categories="$categories" />

@endsection

@push('scripts')
<script>
// ============ HERO CAROUSEL ============
let currentSlide = 0;
let totalSlides = 0;
let autoPlayTimeout;
let isTransitioning = false;

function initCarousel() {
    const inner = document.getElementById('carouselInner');
    if (!inner) return;
    
    totalSlides = inner.querySelectorAll('.carousel-slide').length;
    if (totalSlides <= 1) return;
    
    updateDots();
    startAutoPlay();
    setupEventListeners();
}

function goToSlide(index) {
    if (isTransitioning) return;
    
    isTransitioning = true;
    currentSlide = index;
    
    const slides = document.querySelectorAll('.carousel-slide');
    slides.forEach((slide, i) => {
        if (i === index) {
            slide.classList.add('opacity-100', 'z-10');
            slide.classList.remove('opacity-0', 'z-0');
        } else {
            slide.classList.remove('opacity-100', 'z-10');
            slide.classList.add('opacity-0', 'z-0');
        }
    });
    updateDots();
    
    setTimeout(() => {
        isTransitioning = false;
    }, 700);
}

function updateDots() {
    document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
        if (i === currentSlide) {
            dot.classList.add('bg-white', 'w-8');
            dot.classList.remove('bg-white/40', 'w-2.5');
        } else {
            dot.classList.remove('bg-white', 'w-8');
            dot.classList.add('bg-white/40', 'w-2.5');
        }
    });
}

function nextSlide() {
    const next = (currentSlide + 1) % totalSlides;
    goToSlide(next);
}

function prevSlide() {
    const prev = (currentSlide - 1 + totalSlides) % totalSlides;
    goToSlide(prev);
}

function getCurrentSlideDuration() {
    const slide = document.querySelector(`.carousel-slide[data-slide-index="${currentSlide}"]`);
    return (slide ? parseInt(slide.dataset.duration) || 6 : 6) * 1000;
}

function startAutoPlay() {
    if (totalSlides <= 1) return;
    stopAutoPlay();
    autoPlayTimeout = setTimeout(function autoNext() {
        nextSlide();
        autoPlayTimeout = setTimeout(autoNext, getCurrentSlideDuration());
    }, getCurrentSlideDuration());
}

function stopAutoPlay() {
    if (autoPlayTimeout) {
        clearTimeout(autoPlayTimeout);
    }
}

function setupEventListeners() {
    const heroSection = document.querySelector('section');
    if (heroSection) {
        heroSection.addEventListener('mouseenter', stopAutoPlay);
        heroSection.addEventListener('mouseleave', startAutoPlay);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            stopAutoPlay();
            prevSlide();
            setTimeout(startAutoPlay, 3000);
        } else if (e.key === 'ArrowRight') {
            stopAutoPlay();
            nextSlide();
            setTimeout(startAutoPlay, 3000);
        }
    });

    let touchStartX = 0;
    let touchEndX = 0;

    heroSection.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
        stopAutoPlay();
    });

    heroSection.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
        setTimeout(startAutoPlay, 3000);
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        }
    }
}

// ============ (Categories are now a static grid — no JS needed) ============

// ============ TOAST NOTIFICATIONS ============
function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    if (toast && toastMessage) {
        toastMessage.textContent = message;
        toast.style.transform = 'translateX(0)';
        setTimeout(() => {
            toast.style.transform = 'translateX(500px)';
        }, 3000);
    }
}

function closeToast() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.style.transform = 'translateX(500px)';
    }
}

// ============ FILTER MODAL ============
function toggleFiltersModal() {
    const modal = document.getElementById('filterModal');
    if (modal) {
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
        
        if (!modal.classList.contains('hidden')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
}

function resetFilters() {
    document.getElementById('filterForm').reset();
}

// ============ CART FUNCTIONS ============
function addToCart(itemId) {
    const button = event.target.closest('button');
    
    fetch('{{ route("cart.add", ":id") }}'.replace(':id', itemId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    }).then(res => {
        if (res.ok) {
            showToast('Ajouté au panier avec succès !');
            if (button) {
                button.classList.add('scale-90');
                setTimeout(() => button.classList.remove('scale-90'), 200);
            }
        } else {
            showToast('Erreur lors de l\'ajout au panier', 'error');
        }
    }).catch(() => {
        showToast('Erreur lors de l\'ajout au panier', 'error');
    });
}

function toggleFavorite(itemId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const button = event.currentTarget;
    const icon = button.querySelector('svg');
    
    if (icon.classList.contains('fill-current')) {
        icon.classList.remove('fill-current', 'text-red-500');
        showToast('Retiré des favoris');
    } else {
        icon.classList.add('fill-current', 'text-red-500');
        showToast('Ajouté aux favoris');
    }
}

// ============ SCROLL ANIMATIONS ============
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const sections = document.querySelectorAll('section:not(:first-child)');
    sections.forEach((section) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        section.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
        observer.observe(section);
    });
}

// ============ BACK TO TOP BUTTON ============
function initBackToTop() {
    const backToTopButton = document.createElement('button');
    backToTopButton.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    `;
    backToTopButton.className = 'fixed bottom-8 right-8 w-12 h-12 bg-black text-white rounded-full shadow-lg opacity-0 transition-opacity hover:bg-gray-900 z-40';
    backToTopButton.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
    document.body.appendChild(backToTopButton);

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 500) {
            backToTopButton.style.opacity = '1';
        } else {
            backToTopButton.style.opacity = '0';
        }
    });
}

// ============ INITIALIZATION ============
document.addEventListener('DOMContentLoaded', () => {
    initCarousel();
    initCategoriesNavigation();
    initScrollAnimations();
    initBackToTop();
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('filterModal');
            if (modal && !modal.classList.contains('hidden')) {
                toggleFiltersModal();
            }
        }
    });
    
    document.getElementById('filterModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'filterModal') {
            toggleFiltersModal();
        }
    });
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

window.addEventListener('load', () => {
    document.body.classList.add('loaded');
});
</script>
@endpush
