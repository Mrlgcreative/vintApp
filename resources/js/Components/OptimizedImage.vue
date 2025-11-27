<script setup>
/**
 * Composant d'image optimisée avec lazy loading et WebP
 * 
 * Usage:
 * <OptimizedImage 
 *   :src="image.url" 
 *   :alt="image.alt"
 *   :placeholder="image.placeholder"
 *   sizes="(max-width: 768px) 100vw, 50vw"
 * />
 */
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    src: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: null,
    },
    sizes: {
        type: String,
        default: '100vw',
    },
    aspectRatio: {
        type: String,
        default: '16/9',
    },
    objectFit: {
        type: String,
        default: 'cover', // cover, contain, fill
    },
    lazy: {
        type: Boolean,
        default: true,
    },
});

const imageRef = ref(null);
const isLoaded = ref(false);
const hasError = ref(false);

// Générer les sources WebP et responsive
const webpSrc = computed(() => {
    const ext = props.src.split('.').pop();
    return props.src.replace(`.${ext}`, '.webp');
});

const responsiveSizes = computed(() => {
    const sizes = ['small', 'medium', 'large', 'xlarge'];
    const ext = props.src.split('.').pop();
    const basePath = props.src.replace(`.${ext}`, '');
    
    return sizes.map(size => ({
        size,
        url: `${basePath}_${size}.${ext}`,
        webp: `${basePath}_${size}.webp`,
        width: size === 'small' ? 320 : 
               size === 'medium' ? 768 : 
               size === 'large' ? 1024 : 1920,
    }));
});

const srcSet = computed(() => {
    return responsiveSizes.value
        .map(item => `${item.url} ${item.width}w`)
        .join(', ');
});

const webpSrcSet = computed(() => {
    return responsiveSizes.value
        .map(item => `${item.webp} ${item.width}w`)
        .join(', ');
});

const handleLoad = () => {
    isLoaded.value = true;
};

const handleError = () => {
    hasError.value = true;
    isLoaded.value = true;
};

// Intersection Observer pour lazy loading
onMounted(() => {
    if (!props.lazy || !imageRef.value) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const sources = img.previousElementSibling?.querySelectorAll('source');
                    
                    // Charger les sources WebP
                    sources?.forEach(source => {
                        if (source.dataset.srcset) {
                            source.srcset = source.dataset.srcset;
                        }
                    });
                    
                    // Charger l'image
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                    }
                    
                    observer.unobserve(img);
                }
            });
        },
        {
            rootMargin: '50px',
        }
    );

    observer.observe(imageRef.value);
});
</script>

<template>
    <div class="optimized-image-wrapper" :style="{ aspectRatio }">
        <!-- Placeholder blur (LQIP) -->
        <div 
            v-if="placeholder && !isLoaded"
            class="absolute inset-0 bg-cover bg-center blur-md scale-110"
            :style="{ backgroundImage: `url(${placeholder})` }"
        />

        <picture>
            <!-- Source WebP avec srcset responsive -->
            <source
                v-if="lazy"
                type="image/webp"
                :data-srcset="webpSrcSet"
                :sizes="sizes"
            />
            <source
                v-else
                type="image/webp"
                :srcset="webpSrcSet"
                :sizes="sizes"
            />

            <!-- Source JPEG/PNG fallback -->
            <source
                v-if="lazy"
                :data-srcset="srcSet"
                :sizes="sizes"
            />
            <source
                v-else
                :srcset="srcSet"
                :sizes="sizes"
            />

            <!-- Image finale -->
            <img
                ref="imageRef"
                :data-src="lazy ? src : undefined"
                :src="lazy ? placeholder || 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\'/%3E' : src"
                :alt="alt"
                :class="[
                    'w-full h-full transition-opacity duration-300',
                    isLoaded ? 'opacity-100' : 'opacity-0',
                    objectFit === 'cover' ? 'object-cover' : 
                    objectFit === 'contain' ? 'object-contain' : 
                    'object-fill'
                ]"
                :loading="lazy ? 'lazy' : 'eager'"
                @load="handleLoad"
                @error="handleError"
            />
        </picture>

        <!-- Loading spinner -->
        <div 
            v-if="!isLoaded && !placeholder"
            class="absolute inset-0 flex items-center justify-center bg-gray-100"
        >
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
        </div>

        <!-- Erreur de chargement -->
        <div
            v-if="hasError"
            class="absolute inset-0 flex items-center justify-center bg-gray-200"
        >
            <div class="text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm">Image non disponible</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.optimized-image-wrapper {
    @apply relative overflow-hidden bg-gray-100;
    width: 100%;
}

.optimized-image-wrapper img {
    @apply absolute inset-0;
}
</style>
