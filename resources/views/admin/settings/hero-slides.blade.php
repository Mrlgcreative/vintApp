@extends('layouts.admin')

@section('title', 'Gestion du Carrousel Hero')
@section('page-title', 'Gestion du Carrousel Hero')

@section('page-actions')
<button onclick="showAddModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
    <i class="fas fa-plus"></i>Ajouter une Slide
</button>
@endsection

@push('styles')
<style>
/* Styles pour le slider de durée */
.slider {
    -webkit-appearance: none;
    appearance: none;
    background: #e2e8f0;
    outline: none;
    border-radius: 0.5rem;
}
.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #8b5cf6;
    cursor: pointer;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: #8b5cf6;
    cursor: pointer;
    border-radius: 50%;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Carrousel Tailwind */
.carousel-slide {
    transition: opacity 0.7s ease-in-out;
}
.carousel-dot {
    transition: all 0.3s ease;
}
.carousel-dot.active {
    width: 2rem;
    border-radius: 9999px;
}
</style>
@endpush

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20 px-4 py-3">
            <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
            <p class="flex-1 text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
            <button type="button" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 px-4 py-3">
            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
            <p class="flex-1 text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            <button type="button" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="p-5 sm:p-6">
            <!-- Aperçu du carrousel -->
            <div class="mb-8 bg-slate-50 dark:bg-slate-900 rounded-xl p-4 sm:p-6 border border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-primary-100 dark:bg-primary-900 mr-3">
                        <i class="fas fa-eye text-primary-600 dark:text-primary-400"></i>
                    </span>
                    Aperçu du Carrousel
                </h3>
                @if($slides->where('is_active', true)->count() > 0)
                    @php $activeSlides = $slides->where('is_active', true)->values(); @endphp
                    <div id="twCarousel" class="relative rounded-xl overflow-hidden shadow-lg">
                        <!-- Slides -->
                        <div class="relative min-h-[480px] sm:min-h-[440px] md:min-h-[420px] lg:min-h-[400px]">
                            @foreach($activeSlides as $index => $slide)
                                <div class="carousel-slide absolute inset-0 flex items-center p-6 sm:p-10 transition-all duration-500 {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                                     data-slide-index="{{ $index }}"
                                     style="background-color: {{ $slide->background_color ?? '#6A0DAD' }};">
                                    <div class="w-full">
                                        <div class="flex flex-col md:flex-row items-center gap-6 md:gap-10 {{ $slide->image_position === 'left' ? 'md:flex-row-reverse' : '' }}">
                                            <!-- Texte -->
                                            <div class="flex-1 {{ $slide->text_position === 'center' ? 'text-center' : ($slide->text_position === 'right' ? 'text-right' : 'text-left') }}">
                                                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 leading-tight">{{ $slide->title }}</h2>
                                                @if($slide->subtitle)
                                                    <p class="text-base sm:text-lg text-white/85 mb-6">{{ $slide->subtitle }}</p>
                                                @endif
                                                <div class="flex flex-wrap gap-3 {{ $slide->text_position === 'center' ? 'justify-center' : ($slide->text_position === 'right' ? 'justify-end' : 'justify-start') }}">
                                                    @if($slide->button_primary_text)
                                                        <a href="{{ $slide->button_primary_url ?? '#' }}" class="inline-flex items-center px-5 py-2.5 bg-white text-slate-900 font-semibold rounded-xl shadow-md hover:bg-slate-100 hover:shadow-lg transition-all duration-200">
                                                            {{ $slide->button_primary_text }}
                                                        </a>
                                                    @endif
                                                    @if($slide->button_secondary_text)
                                                        <a href="{{ $slide->button_secondary_url ?? '#' }}" class="inline-flex items-center px-5 py-2.5 bg-transparent text-white font-semibold rounded-xl border-2 border-white/70 hover:bg-white/10 hover:border-white transition-all duration-200">
                                                            {{ $slide->button_secondary_text }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                            <!-- Image -->
                                            <div class="flex-1 flex justify-center">
                                                <img src="/storage/{{ $slide->image_url }}" 
                                                     class="object-contain drop-shadow-2xl rounded-xl"
                                                     style="max-height: {{ $slide->image_size === 'small' ? '250px' : ($slide->image_size === 'medium' ? '350px' : ($slide->image_size === 'large' ? '450px' : '100%')) }};"
                                                     alt="{{ $slide->title }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigation flèches -->
                        <button onclick="carouselPrev()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/30 backdrop-blur-sm text-white flex items-center justify-center hover:bg-black/50 transition-all duration-200 z-20">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button onclick="carouselNext()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/30 backdrop-blur-sm text-white flex items-center justify-center hover:bg-black/50 transition-all duration-200 z-20">
                            <i class="fas fa-chevron-right"></i>
                        </button>

                        <!-- Indicateurs -->
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20">
                            @foreach($activeSlides as $index => $slide)
                                <button onclick="carouselGoto({{ $index }})"
                                        class="carousel-dot h-3 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-8 bg-white active' : 'w-3 bg-white/50 hover:bg-white/75' }}"
                                        data-dot-index="{{ $index }}"></button>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 mb-4">
                            <i class="fas fa-image text-3xl opacity-50"></i>
                        </div>
                        <p class="text-sm">Aucune slide active. Ajoutez et activez des slides pour voir l'aperçu.</p>
                    </div>
                @endif
            </div>

            <!-- Liste des slides -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-primary-100 dark:bg-primary-900 mr-3">
                        <i class="fas fa-list text-primary-600 dark:text-primary-400"></i>
                    </span>
                    Toutes les Slides
                    <span class="ml-2 inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">{{ $slides->count() }}</span>
                </h3>

                @if($slides->count() > 0)
                    <div id="slidesList" class="space-y-3">
                        @foreach($slides as $slide)
                            <div class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-300 slide-item" data-slide-id="{{ $slide->id }}">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <!-- Image -->
                                    <div class="flex-shrink-0 relative overflow-hidden rounded-xl">
                                        <img src="/storage/{{ $slide->image_url }}" alt="{{ $slide->title }}" class="w-full md:w-48 h-32 object-cover rounded-xl group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-xl"></div>
                                    </div>
                                    
                                    <!-- Contenu -->
                                    <div class="flex-grow">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $slide->title }}</h4>
                                                @if($slide->subtitle)
                                                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ $slide->subtitle }}</p>
                                                @endif
                                                <!-- Affichage de la durée -->
                                                <div class="flex items-center gap-4 mt-2">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-clock mr-1"></i>{{ $slide->display_duration ?? 6 }}s
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                                                        <i class="fas fa-expand-arrows-alt mr-1"></i>{{ ucfirst($slide->image_size ?? 'medium') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <!-- Handle de glisser-déposer -->
                                                <button class="drag-handle cursor-move p-2 text-slate-400 hover:text-slate-600 dark:text-slate-300">
                                                    <i class="fas fa-grip-vertical"></i>
                                                </button>
                                                
                                                <!-- Badge statut -->
                                                @if($slide->is_active)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                                        <i class="fas fa-eye-slash mr-1"></i>Inactive
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Boutons -->
                                        @if($slide->button_primary_text || $slide->button_secondary_text)
                                            <div class="flex flex-wrap gap-2 mb-3">
                                                @if($slide->button_primary_text)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-primary-100 text-primary-800">
                                                        <i class="fas fa-link mr-1"></i>{{ $slide->button_primary_text }}
                                                    </span>
                                                @endif
                                                @if($slide->button_secondary_text)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-link mr-1"></i>{{ $slide->button_secondary_text }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <!-- Actions -->
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <button onclick="editSlide({{ $slide->id }})" class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-md transition-colors">
                                                <i class="fas fa-edit mr-1.5"></i>Modifier
                                            </button>
                                            
                                            <form action="{{ route('admin.settings.hero-slides.toggle', $slide) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 {{ $slide->is_active ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700' : 'bg-green-50 hover:bg-green-100 text-green-700' }} text-sm font-medium rounded-md transition-colors">
                                                    <i class="fas {{ $slide->is_active ? 'fa-eye-slash' : 'fa-eye' }} mr-1.5"></i>
                                                    {{ $slide->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.settings.hero-slides.destroy', $slide) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette slide ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-md transition-colors">
                                                    <i class="fas fa-trash mr-1.5"></i>Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-gradient-to-b from-slate-50 to-white dark:from-slate-900 dark:to-slate-800 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-700 mb-4">
                            <i class="fas fa-images text-4xl text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Aucune slide</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-6 max-w-sm mx-auto">Commencez par ajouter votre première slide pour le carrousel hero.</p>
                        <button onclick="showAddModal()" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Ajouter une Slide
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier Slide -->
<div id="slideModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 px-4 transition-opacity duration-300">
    <div class="relative top-10 sm:top-16 mx-auto p-6 w-full max-w-2xl shadow-2xl rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 mb-10">
        <div class="mt-2">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-primary-100 dark:bg-primary-900 mr-3">
                        <i class="fas fa-image text-primary-600 dark:text-primary-400"></i>
                    </span>
                    <span id="modalTitle">Ajouter une Slide</span>
                </h3>
                <button onclick="hideModal()" class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 dark:text-slate-300 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <form id="slideForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="POST" id="formMethod">
                
                <!-- Titre -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="slideTitle" required 
                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Ex: Bienvenue sur VintApp">
                </div>
                
                <!-- Sous-titre -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Sous-titre
                    </label>
                    <textarea name="subtitle" id="slideSubtitle" rows="2"
                              class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                              placeholder="Description courte..."></textarea>
                </div>
                
                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Image <span class="text-red-500" id="imageRequired">*</span>
                    </label>
                    <input type="file" name="image" id="slideImage" accept="image/*"
                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           onchange="previewImage(this)">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Formats: JPG, PNG, GIF - Max 5MB - Recommandé: 1920x1080px (PNG haute résolution)</p>
                    <div id="imagePreview" class="mt-3 hidden">
                        <img id="previewImg" src="" alt="Aperçu" class="w-full h-48 object-contain rounded-xl bg-slate-50 dark:bg-slate-900">
                    </div>
                </div>
                
                <!-- Couleur de fond personnalisable -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Couleur de fond <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="background_color" id="backgroundColor" value="#6A0DAD"
                               class="h-10 w-20 border border-slate-300 dark:border-slate-600 rounded cursor-pointer">
                        <input type="text" id="backgroundColorHex" value="#6A0DAD" 
                               pattern="^#[0-9A-Fa-f]{6}$"
                               class="flex-1 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="#6A0DAD">
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Choisissez la couleur de fond du carrousel</p>
                </div>
                
                <!-- Position du texte et de l'image -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                            Position du texte <span class="text-red-500">*</span>
                        </label>
                        <select name="text_position" id="textPosition" required
                                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="left">À gauche</option>
                            <option value="center">Au centre</option>
                            <option value="right">À droite</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                            Position de l'image <span class="text-red-500">*</span>
                        </label>
                        <select name="image_position" id="imagePosition" required
                                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="right">À droite</option>
                            <option value="left">À gauche</option>
                        </select>
                    </div>
                </div>
                
                <!-- Taille de l'image -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Taille de l'image <span class="text-red-500">*</span>
                    </label>
                    <select name="image_size" id="imageSize" required
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="small">Petite (250px)</option>
                        <option value="medium" selected>Moyenne (350px)</option>
                        <option value="large">Grande (450px)</option>
                        <option value="full">Pleine hauteur (100%)</option>
                    </select>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Définit la hauteur maximale de l'image dans le carrousel</p>
                </div>
                
                <!-- Durée d'affichage -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                        Durée d'affichage <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="display_duration" id="displayDuration" 
                               min="3" max="15" step="1" value="6" required
                               class="w-24 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent text-center">
                        <span class="text-sm text-slate-600 dark:text-slate-300 font-medium">secondes</span>
                        <input type="range" id="durationRange" 
                               min="3" max="15" step="1" value="6"
                               class="flex-1 h-2 bg-slate-200 dark:bg-slate-700 rounded-xl appearance-none cursor-pointer slider">
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Temps d'affichage de cette slide avant passage automatique à la suivante (3-15 secondes)</p>
                </div>
                
                <!-- Bouton Principal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                            Texte Bouton Principal
                        </label>
                        <input type="text" name="button_primary_text" id="buttonPrimaryText"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="Ex: Commencer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                            URL Bouton Principal
                        </label>
                        <input type="text" name="button_primary_url" id="buttonPrimaryUrl"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="/register">
                    </div>
                </div>
                
                <!-- Bouton Secondaire -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                            Texte Bouton Secondaire
                        </label>
                        <input type="text" name="button_secondary_text" id="buttonSecondaryText"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="Ex: Explorer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                            URL Bouton Secondaire
                        </label>
                        <input type="text" name="button_secondary_url" id="buttonSecondaryUrl"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="/items">
                    </div>
                </div>
                
                <!-- Statut -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="slideIsActive" value="1" checked
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 dark:border-slate-600 rounded">
                    <label for="slideIsActive" class="ml-2 block text-sm text-slate-700 dark:text-slate-200">
                        Activer cette slide immédiatement
                    </label>
                </div>
                
                <!-- Boutons -->
                <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" onclick="hideModal()" 
                            class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-medium rounded-xl transition-all duration-200">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitButtonText">Ajouter</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
// Carrousel Preview
let carouselCurrent = 0;
let carouselTotal = {{ $slides->where('is_active', true)->count() }};
let carouselAuto = null;

function carouselGoto(index) {
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    slides.forEach((s, i) => {
        s.classList.toggle('opacity-100', i === index);
        s.classList.toggle('opacity-0', i !== index);
        s.classList.toggle('z-10', i === index);
        s.classList.toggle('z-0', i !== index);
    });
    dots.forEach((d, i) => {
        d.classList.toggle('w-8', i === index);
        d.classList.toggle('bg-white', i === index);
        d.classList.toggle('active', i === index);
        d.classList.toggle('w-3', i !== index);
        d.classList.toggle('bg-white/50', i !== index);
    });
    carouselCurrent = index;
    clearInterval(carouselAuto);
    carouselAuto = setInterval(() => carouselNext(), 5000);
}
function carouselPrev() { carouselGoto((carouselCurrent - 1 + carouselTotal) % carouselTotal); }
function carouselNext() { carouselGoto((carouselCurrent + 1) % carouselTotal); }

if (carouselTotal > 1) {
    carouselAuto = setInterval(() => carouselNext(), 5000);
}

// Variables globales
let currentSlideId = null;
let slidesData = @json($slides);

// Initialiser Sortable.js pour le drag & drop
document.addEventListener('DOMContentLoaded', function() {
    const slidesList = document.getElementById('slidesList');
    if (slidesList) {
        new Sortable(slidesList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                updateSlidesOrder();
            }
        });
    }
});

// Afficher le modal d'ajout
function showAddModal() {
    currentSlideId = null;
    document.getElementById('modalTitle').textContent = 'Ajouter une Slide';
    document.getElementById('submitButtonText').textContent = 'Ajouter';
    document.getElementById('slideForm').action = '{{ route("admin.settings.hero-slides.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('imageRequired').classList.remove('hidden');
    document.getElementById('slideImage').required = true;
    
    // Réinitialiser le formulaire
    document.getElementById('slideForm').reset();
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('backgroundColor').value = '#6A0DAD';
    document.getElementById('backgroundColorHex').value = '#6A0DAD';
    document.getElementById('textPosition').value = 'left';
    document.getElementById('imagePosition').value = 'right';
    document.getElementById('imageSize').value = 'medium';
    document.getElementById('displayDuration').value = 6;
    document.getElementById('durationRange').value = 6;
    
    document.getElementById('slideModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Afficher le modal d'édition
function editSlide(slideId) {
    currentSlideId = slideId;
    const slide = slidesData.find(s => s.id === slideId);
    
    if (!slide) return;
    
    document.getElementById('modalTitle').textContent = 'Modifier la Slide';
    document.getElementById('submitButtonText').textContent = 'Mettre à jour';
    document.getElementById('slideForm').action = `/admin/settings/hero-slides/${slideId}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('imageRequired').classList.add('hidden');
    document.getElementById('slideImage').required = false;
    
    // Remplir le formulaire
    document.getElementById('slideTitle').value = slide.title || '';
    document.getElementById('slideSubtitle').value = slide.subtitle || '';
    document.getElementById('backgroundColor').value = slide.background_color || '#6A0DAD';
    document.getElementById('backgroundColorHex').value = slide.background_color || '#6A0DAD';
    document.getElementById('textPosition').value = slide.text_position || 'left';
    document.getElementById('imagePosition').value = slide.image_position || 'right';
    document.getElementById('imageSize').value = slide.image_size || 'medium';
    document.getElementById('displayDuration').value = slide.display_duration || 6;
    document.getElementById('durationRange').value = slide.display_duration || 6;
    document.getElementById('buttonPrimaryText').value = slide.button_primary_text || '';
    document.getElementById('buttonPrimaryUrl').value = slide.button_primary_url || '';
    document.getElementById('buttonSecondaryText').value = slide.button_secondary_text || '';
    document.getElementById('buttonSecondaryUrl').value = slide.button_secondary_url || '';
    document.getElementById('slideIsActive').checked = slide.is_active;
    
    // Afficher l'aperçu de l'image existante
    document.getElementById('imagePreview').classList.remove('hidden');
    document.getElementById('previewImg').src = `/storage/${slide.image_url}`;
    
    document.getElementById('slideModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Cacher le modal
function hideModal() {
    document.getElementById('slideModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Prévisualiser l'image
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').classList.remove('hidden');
            document.getElementById('previewImg').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Synchroniser le color picker avec le champ texte
document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.getElementById('backgroundColor');
    const colorHex = document.getElementById('backgroundColorHex');
    
    if (colorPicker && colorHex) {
        colorPicker.addEventListener('input', function() {
            colorHex.value = this.value.toUpperCase();
        });
        
        colorHex.addEventListener('input', function() {
            const hex = this.value;
            if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
                colorPicker.value = hex;
            }
        });
    }
    
    // Synchroniser le champ durée avec le slider
    const durationInput = document.getElementById('displayDuration');
    const durationRange = document.getElementById('durationRange');
    
    if (durationInput && durationRange) {
        durationInput.addEventListener('input', function() {
            const value = Math.max(3, Math.min(15, parseInt(this.value) || 6));
            this.value = value;
            durationRange.value = value;
        });
        
        durationRange.addEventListener('input', function() {
            durationInput.value = this.value;
        });
    }
});

// Mettre à jour l'ordre des slides
function updateSlidesOrder() {
    const slideItems = document.querySelectorAll('.slide-item');
    const order = Array.from(slideItems).map(item => parseInt(item.dataset.slideId));
    
    fetch('{{ route("admin.settings.hero-slides.reorder") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Ordre mis à jour avec succès !', 'success');
        } else {
            showNotification('Erreur lors de la mise à jour de l\'ordre', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la mise à jour de l\'ordre', 'error');
    });
}

// Afficher une notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform translate-x-full opacity-0 transition-all duration-300`;
    
    switch(type) {
        case 'success':
            notification.className += ' bg-green-500 text-white';
            break;
        case 'error':
            notification.className += ' bg-red-500 text-white';
            break;
        default:
            notification.className += ' bg-blue-500 text-white';
    }
    
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-slate-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
        notification.classList.add('translate-x-0', 'opacity-100');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}
</script>
@endsection
