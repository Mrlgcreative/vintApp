@extends('layouts.admin')

@section('title', 'Candidats Experts')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-user-plus" style="color: var(--color-success);"></i>
                Candidats Experts
            </h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">Sélectionnez des utilisateurs pour les désigner comme experts</p>
        </div>
        <div>
            <a href="{{ route('admin.experts.index') }}" 
               class="hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2"
               style="background-color: var(--color-light);">
                <i class="fas fa-arrow-left"></i>
                Retour aux Experts
            </a>
        </div>
    </div>
        </div>
    </div>

    <!-- Messages de session -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 relative">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
            <button type="button" class="absolute top-3 right-3 text-green-500 hover:text-green-700" 
                    onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 relative">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                <span class="text-red-800">{{ session('error') }}</span>
            </div>
            <button type="button" class="absolute top-3 right-3 text-red-500 hover:text-red-700" 
                    onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Statistiques des candidats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Total Candidats</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $candidates->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Vérifiés</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $candidates->where('email_verified_at', '!=', null)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-primary-100 rounded-full">
                    <i class="fas fa-star text-primary-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Actifs récemment</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $candidates->where('last_activity', '>', now()->subDays(30))->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres de recherche -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filtrer les candidats</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.experts.candidates') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Rechercher</label>
                        <input type="text" id="search" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Nom ou email..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="min_orders" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Min. commandes</label>
                        <input type="number" id="min_orders" name="min_orders" 
                               value="{{ request('min_orders', 0) }}" 
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="verified_only" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Statut</label>
                        <select id="verified_only" name="verified_only" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous</option>
                            <option value="1" {{ request('verified_only') == '1' ? 'selected' : '' }}>Vérifiés seulement</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">&nbsp;</label>
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.experts.candidates') }}" 
                               class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des candidats -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-list text-blue-600"></i>
                Candidats Éligibles ({{ $candidates->total() }})
            </h3>
        </div>
        <div class="p-6">
            @if($candidates->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Utilisateur</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Commandes</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Taux satisfaction</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Dernière activité</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Statut</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($candidates as $candidate)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 dark:bg-gray-900">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 flex-shrink-0">
                                                @if($candidate->avatar)
                                                    @if(str_starts_with($candidate->avatar, 'http://') || str_starts_with($candidate->avatar, 'https://'))
                                                        <img src="{{ $candidate->avatar }}" 
                                                             class="w-10 h-10 rounded-full object-cover" 
                                                             alt="{{ $candidate->name }}"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center" style="display: none;">
                                                            <span class="text-white font-bold text-sm">
                                                                {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <img src="{{ $candidate->avatar_url }}" 
                                                             class="w-10 h-10 rounded-full object-cover" 
                                                             alt="{{ $candidate->name }}"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center" style="display: none;">
                                                            <span class="text-white font-bold text-sm">
                                                                {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-bold text-sm">
                                                            {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">{{ $candidate->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $candidate->email }}</div>
                                                @if($candidate->phone)
                                                    <div class="text-xs text-gray-400">{{ $candidate->phone }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $candidate->orders_count ?? 0 }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">commandes</div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if(isset($candidate->satisfaction_rate) && $candidate->satisfaction_rate > 0)
                                            <div class="font-bold text-green-600">{{ number_format($candidate->satisfaction_rate, 1) }}%</div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 mt-1">
                                                <div class="bg-green-600 h-1 rounded-full" 
                                                     style="width: {{ $candidate->satisfaction_rate }}%"></div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($candidate->last_activity)
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $candidate->last_activity->diffForHumans() }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">Inconnue</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex flex-col items-center gap-1">
                                            @if($candidate->email_verified_at)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                    <i class="fas fa-check mr-1"></i>Vérifié
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                                    <i class="fas fa-times mr-1"></i>Non vérifié
                                                </span>
                                            @endif
                                            
                                            @if($candidate->created_at > now()->subDays(30))
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                    Nouveau
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <button type="button" 
                                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 mx-auto"
                                                onclick="designateExpert({{ $candidate->id }}, '{{ $candidate->name }}')">
                                            <i class="fas fa-user-graduate"></i>
                                            Désigner
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        Affichage de {{ $candidates->firstItem() ?? 0 }} à {{ $candidates->lastItem() ?? 0 }} 
                        sur {{ $candidates->total() }} candidats
                    </div>
                    <div>
                        {{ $candidates->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun candidat trouvé</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Aucun utilisateur ne correspond aux critères de sélection.</p>
                    <a href="{{ route('admin.experts.candidates') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-undo"></i>
                        Réinitialiser les filtres
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de désignation -->
<div id="designateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border max-w-2xl shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- En-tête -->
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-user-graduate text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Désigner comme Expert</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Configurer les paramètres d'expert pour <span id="candidateName" class="font-semibold text-blue-600"></span>
                </p>
            </div>

            <!-- Formulaire -->
            <form id="designateForm">
                <input type="hidden" id="selectedUserId" name="user_id" value="">
                
                <!-- Niveau de certification -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Niveau de Certification</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-900">
                            <input type="radio" name="certification_level" value="junior" class="mr-3" checked>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Junior</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Débutant</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-900">
                            <input type="radio" name="certification_level" value="senior" class="mr-3">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Senior</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Expérimenté</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-900">
                            <input type="radio" name="certification_level" value="master" class="mr-3">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Master</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Expert</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Spécialisations -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Spécialisations <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Option Généraliste -->
                        <label class="flex items-center col-span-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <input type="checkbox" name="specialties[]" value="general" class="mr-2" id="generalSpecialty">
                            <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">🌐 Généraliste (toutes catégories)</span>
                        </label>
                        
                        <!-- Catégories dynamiques -->
                        @foreach($categories as $category)
                        <label class="flex items-center category-checkbox">
                            <input type="checkbox" name="specialties[]" value="{{ $category->slug }}" class="mr-2">
                            <span class="text-sm">{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <div id="specialtyError" class="text-red-500 text-sm mt-1 hidden">
                        Veuillez sélectionner au moins une spécialisation.
                    </div>
                </div>

                <!-- Biographie -->
                <div class="mb-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Biographie / Notes (optionnel)
                    </label>
                    <textarea name="bio" id="bio" rows="3" 
                              placeholder="Ajoutez des informations sur l'expertise de cet utilisateur..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <!-- Boutons -->
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal()" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 dark:text-gray-100 text-base font-medium rounded-lg shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-green-600 text-white text-base font-medium rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                        <i class="fas fa-user-graduate mr-2"></i>
                        Désigner comme Expert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
let selectedUserId = null;

function designateExpert(userId, userName) {
    selectedUserId = userId;
    document.getElementById('candidateName').textContent = userName;
    document.getElementById('selectedUserId').value = userId;
    
    // Réinitialiser le formulaire
    document.getElementById('designateForm').reset();
    document.querySelector('input[name="certification_level"][value="junior"]').checked = true;
    document.getElementById('specialtyError').classList.add('hidden');
    
    document.getElementById('designateModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('designateModal').classList.add('hidden');
    selectedUserId = null;
    document.getElementById('designateForm').reset();
    document.getElementById('specialtyError').classList.add('hidden');
}

// Gestion de la soumission du formulaire
document.getElementById('designateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Vérifier qu'au moins une spécialisation est sélectionnée
    const specialties = document.querySelectorAll('input[name="specialties[]"]:checked');
    if (specialties.length === 0) {
        document.getElementById('specialtyError').classList.remove('hidden');
        return;
    }
    
    document.getElementById('specialtyError').classList.add('hidden');
    
    if (selectedUserId) {
        // Créer un formulaire pour la soumission
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/experts/designate/${selectedUserId}`;
        
        // Ajouter le token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Ajouter le niveau de certification
        const certificationLevel = document.querySelector('input[name="certification_level"]:checked').value;
        const certInput = document.createElement('input');
        certInput.type = 'hidden';
        certInput.name = 'certification_level';
        certInput.value = certificationLevel;
        form.appendChild(certInput);
        
        // Ajouter les spécialisations
        specialties.forEach(function(specialty) {
            const specInput = document.createElement('input');
            specInput.type = 'hidden';
            specInput.name = 'specialties[]';
            specInput.value = specialty.value;
            form.appendChild(specInput);
        });
        
        // Ajouter la biographie si présente
        const bio = document.getElementById('bio').value;
        if (bio.trim()) {
            const bioInput = document.createElement('input');
            bioInput.type = 'hidden';
            bioInput.name = 'bio';
            bioInput.value = bio;
            form.appendChild(bioInput);
        }
        
        // Soumettre le formulaire
        document.body.appendChild(form);
        form.submit();
    }
});

// Fermer le modal en cliquant à l'extérieur
document.getElementById('designateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Gestion des styles des radios buttons
document.querySelectorAll('input[name="certification_level"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        // Retirer la classe active de tous les labels
        document.querySelectorAll('input[name="certification_level"]').forEach(function(r) {
            r.closest('label').classList.remove('border-blue-500', 'bg-blue-50');
            r.closest('label').classList.add('border-gray-300 dark:border-gray-600');
        });
        
        // Ajouter la classe active au label sélectionné
        if (this.checked) {
            this.closest('label').classList.add('border-blue-500', 'bg-blue-50');
            this.closest('label').classList.remove('border-gray-300 dark:border-gray-600');
        }
    });
});

// Initialiser le style du radio button par défaut
document.addEventListener('DOMContentLoaded', function() {
    const defaultRadio = document.querySelector('input[name="certification_level"]:checked');
    if (defaultRadio) {
        defaultRadio.closest('label').classList.add('border-blue-500', 'bg-blue-50');
        defaultRadio.closest('label').classList.remove('border-gray-300 dark:border-gray-600');
    }
    
    // Gestion du checkbox Généraliste
    const generalCheckbox = document.getElementById('generalSpecialty');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox input[type="checkbox"]');
    
    // Quand on coche "Généraliste", décocher les autres
    generalCheckbox.addEventListener('change', function() {
        if (this.checked) {
            categoryCheckboxes.forEach(function(cb) {
                cb.checked = false;
                cb.disabled = true;
                cb.closest('label').classList.add('opacity-50');
            });
        } else {
            categoryCheckboxes.forEach(function(cb) {
                cb.disabled = false;
                cb.closest('label').classList.remove('opacity-50');
            });
        }
    });
    
    // Quand on coche une catégorie, décocher Généraliste
    categoryCheckboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            if (this.checked && generalCheckbox.checked) {
                generalCheckbox.checked = false;
                categoryCheckboxes.forEach(function(c) {
                    c.disabled = false;
                    c.closest('label').classList.remove('opacity-50');
                });
            }
        });
    });
});
</script>
@endsection