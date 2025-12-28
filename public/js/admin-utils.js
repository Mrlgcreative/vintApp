/**
 * Utilitaires JavaScript pour l'interface admin Tailwind
 */

// Gestion des dropdowns natifs
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        const isHidden = dropdown.classList.contains('hidden');
        
        // Fermer tous les autres dropdowns
        document.querySelectorAll('[id$="-dropdown"]').forEach(d => {
            if (d.id !== dropdownId) {
                d.classList.add('hidden');
            }
        });
        
        // Toggle le dropdown courant
        if (isHidden) {
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }
}

// Fermer les dropdowns en cliquant ailleurs
document.addEventListener('click', function(e) {
    if (!e.target.closest('[onclick*="toggleDropdown"]')) {
        document.querySelectorAll('[id$="-dropdown"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Gestion des modals natives
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.toggle('hidden');
    }
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Fermer les modals avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.fixed.inset-0').forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    }
});

// Gestion des notifications toast
function showToast(message, type = 'success', duration = 5000) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all transform translate-x-full opacity-0`;
    
    // Couleurs selon le type
    switch(type) {
        case 'success':
            toast.className += ' bg-green-500 text-white';
            break;
        case 'error':
            toast.className += ' bg-red-500 text-white';
            break;
        case 'warning':
            toast.className += ' bg-yellow-500 text-white';
            break;
        default:
            toast.className += ' bg-gray-800 text-white';
    }
    
    toast.innerHTML = `
        <div class="flex items-center">
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 100);
    
    // Auto-suppression
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// Gestion des états de chargement
function setLoading(buttonElement, loading = true) {
    if (loading) {
        buttonElement.disabled = true;
        buttonElement.dataset.originalText = buttonElement.innerHTML;
        buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Chargement...';
        buttonElement.classList.add('opacity-75', 'cursor-not-allowed');
    } else {
        buttonElement.disabled = false;
        buttonElement.innerHTML = buttonElement.dataset.originalText;
        buttonElement.classList.remove('opacity-75', 'cursor-not-allowed');
    }
}

// Confirmation avant action
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Copier dans le presse-papier
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showToast('Copié dans le presse-papier', 'success', 2000);
    } catch (err) {
        showToast('Erreur lors de la copie', 'error', 2000);
    }
}

// Formatage des nombres
function formatNumber(number, locale = 'fr-FR') {
    return new Intl.NumberFormat(locale).format(number);
}

// Formatage des devises
function formatCurrency(amount, currency = 'XOF', locale = 'fr-FR') {
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency
    }).format(amount);
}

// Validation d'email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Génération de couleurs aléatoires pour les avatars
function generateAvatarColor(name) {
    const colors = [
        'bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500',
        'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500'
    ];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colors[Math.abs(hash) % colors.length];
}

// Auto-save pour les formulaires
function enableAutoSave(formId, saveUrl, interval = 30000) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    let autoSaveTimeout;
    
    form.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            const formData = new FormData(form);
            
            fetch(saveUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Sauvegarde automatique effectuée', 'success', 2000);
                }
            })
            .catch(() => {
                showToast('Erreur lors de la sauvegarde automatique', 'error', 3000);
            });
        }, interval);
    });
}

// Recherche en temps réel
function enableLiveSearch(searchInputId, resultsContainerId, searchUrl) {
    const searchInput = document.getElementById(searchInputId);
    const resultsContainer = document.getElementById(resultsContainerId);
    
    if (!searchInput || !resultsContainer) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    let resultsHtml = '';
                    
                    if (data.results && data.results.length > 0) {
                        data.results.forEach(result => {
                            resultsHtml += `
                                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100">
                                    <div class="font-medium text-gray-900">${result.title}</div>
                                    <div class="text-sm text-gray-500">${result.subtitle || ''}</div>
                                </div>
                            `;
                        });
                    } else {
                        resultsHtml = '<div class="p-3 text-center text-gray-500">Aucun résultat trouvé</div>';
                    }
                    
                    resultsContainer.innerHTML = resultsHtml;
                })
                .catch(() => {
                    resultsContainer.innerHTML = '<div class="p-3 text-center text-red-500">Erreur lors de la recherche</div>';
                });
        }, 300);
    });
}

// Initialisation lors du chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips si nécessaire
    // Initialiser les composants interactifs
});