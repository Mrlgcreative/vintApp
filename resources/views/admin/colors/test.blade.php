@extends('layouts.admin')

@section('title', 'Test des Couleurs VintApp avec Tailwind')

@section('content')
<div class="max-w-7xl mx-auto py-4 px-3 sm:py-6 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-slate-800 rounded-lg sm:rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-slate-200 dark:border-slate-700">
            <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-palette mr-2 sm:mr-3 text-primary"></i>
                Test des Couleurs VintApp avec Tailwind
            </h1>
            <p class="text-slate-600 dark:text-slate-300 mt-2">Palette active : <span class="font-semibold text-primary">{{ $activePaletteName ?? 'default' }}</span></p>
        </div>
        
        <div class="p-4 sm:p-6 space-y-8">
            <!-- Boutons Tailwind -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-primary">Boutons avec Classes Tailwind</h3>
                <div class="flex flex-wrap gap-3">
                    <button class="bg-primary hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Bouton Primary
                    </button>
                    <button class="bg-secondary hover:bg-secondary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Bouton Secondary
                    </button>
                    <button class="bg-success hover:bg-success-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Bouton Success
                    </button>
                    <button class="bg-danger hover:bg-danger-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Bouton Danger
                    </button>
                    <button class="bg-warning hover:bg-warning-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Bouton Warning
                    </button>
                    <button class="bg-accent hover:bg-accent-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Bouton Accent
                    </button>
                </div>
            </div>

            <!-- Classes utilitaires personnalisées -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-secondary">Classes Utilitaires VintApp</h3>
                <div class="flex flex-wrap gap-3">
                    <button class="btn-primary">Classe btn-primary</button>
                    <button class="btn-secondary">Classe btn-secondary</button>
                    <button class="btn-success">Classe btn-success</button>
                    <button class="btn-danger">Classe btn-danger</button>
                    <button class="btn-warning">Classe btn-warning</button>
                </div>
            </div>
            
            <!-- Badges -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-accent">Badges avec Classes Tailwind</h3>
                <div class="flex flex-wrap gap-2">
                    <span class="badge-primary">Primary</span>
                    <span class="badge-secondary">Secondary</span>
                    <span class="badge-success">Success</span>
                    <span class="badge-danger">Danger</span>
                    <span class="badge-warning">Warning</span>
                    <span class="badge-info">Info</span>
                </div>
            </div>
            
            <!-- Cartes colorées -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-info">Cartes avec Bordures Colorées</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="card-primary p-4 rounded-lg">
                        <h4 class="font-semibold text-primary mb-2">Carte Primary</h4>
                        <p class="text-slate-600 dark:text-slate-300">Contenu avec bordure primary</p>
                    </div>
                    <div class="card-success p-4 rounded-lg">
                        <h4 class="font-semibold text-success mb-2">Carte Success</h4>
                        <p class="text-slate-600 dark:text-slate-300">Contenu avec bordure success</p>
                    </div>
                    <div class="card-danger p-4 rounded-lg">
                        <h4 class="font-semibold text-danger mb-2">Carte Danger</h4>
                        <p class="text-slate-600 dark:text-slate-300">Contenu avec bordure danger</p>
                    </div>
                </div>
            </div>
            
            <!-- Textes colorés -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Textes avec Classes Tailwind</h3>
                <div class="space-y-2">
                    <p class="text-primary">Texte primary avec classe text-primary</p>
                    <p class="text-secondary">Texte secondary avec classe text-secondary</p>
                    <p class="text-success">Texte success avec classe text-success</p>
                    <p class="text-danger">Texte danger avec classe text-danger</p>
                    <p class="text-warning">Texte warning avec classe text-warning</p>
                    <p class="text-info">Texte info avec classe text-info</p>
                    <p class="text-accent">Texte accent avec classe text-accent</p>
                </div>
            </div>
            
            <!-- Alertes -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Alertes</h3>
                <div class="space-y-4">
                    <div class="border-l-4 border-primary bg-primary-50 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-primary"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-primary-800">
                                    Alerte d'information avec bordure et fond primary
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-l-4 border-success bg-success-50 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-success-800">
                                    Alerte de succès avec bordure et fond success
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-l-4 border-danger bg-danger-50 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-danger"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-danger-800">
                                    Alerte d'erreur avec bordure et fond danger
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Changer de Palette</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-4">Testez les différentes palettes pour voir l'effet en temps réel :</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.settings.colors') }}" 
                       class="bg-primary hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                        <i class="fas fa-palette mr-2"></i>
                        Changer de Palette
                    </a>
                    <button onclick="window.location.reload()" 
                            class="bg-secondary hover:bg-secondary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                        <i class="fas fa-refresh mr-2"></i>
                        Recharger
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection