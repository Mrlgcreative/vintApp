<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-palette text-blue-600"></i>
            Palette de Couleurs
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Choisissez le thème de couleurs pour l'application</p>
    </div>

    <form action="{{ route('admin.settings.colors') }}" method="POST" id="colorPaletteForm">
        @csrf
        <div class="space-y-4">
            @foreach($palettes as $paletteKey => $palette)
                <div class="palette-option border rounded-lg p-4 cursor-pointer transition-all hover:shadow-md {{ $activePalette === $paletteKey ? 'border-blue-500 bg-blue-50' : 'border-gray-200 dark:border-gray-700' }}"
                     onclick="selectPalette('{{ $paletteKey }}')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <input type="radio" 
                                   name="palette" 
                                   value="{{ $paletteKey }}" 
                                   id="palette_{{ $paletteKey }}"
                                   {{ $activePalette === $paletteKey ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600">
                            
                            <div>
                                <label for="palette_{{ $paletteKey }}" class="font-medium text-gray-900 dark:text-white cursor-pointer">
                                    {{ $palette['name'] }}
                                </label>
                                <div class="flex gap-1 mt-1">
                                    @foreach(['primary', 'secondary', 'success', 'danger', 'warning'] as $colorName)
                                        @if(isset($palette['colors'][$colorName]))
                                            <div class="w-6 h-6 rounded-full border border-white shadow-sm"
                                                 style="background-color: {{ $palette['colors'][$colorName] }}"
                                                 title="{{ ucfirst($colorName) }}"></div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        @if($activePalette === $paletteKey)
                            <div class="text-blue-600">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Aperçu détaillé -->
                    <div class="mt-3 grid grid-cols-2 md:grid-cols-5 gap-2">
                        @foreach($palette['colors'] as $colorName => $colorValue)
                            @if($colorName !== 'name')
                                <div class="text-center">
                                    <div class="w-full h-8 rounded border border-white shadow-sm mb-1"
                                         style="background-color: {{ $colorValue }}"></div>
                                    <span class="text-xs text-gray-600 dark:text-gray-300 capitalize">{{ $colorName }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="enable_dark_mode" class="w-4 h-4 text-blue-600">
                    <span class="text-sm text-gray-700 dark:text-gray-200">Activer le mode sombre</span>
                </label>
                
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="auto_dark_mode" class="w-4 h-4 text-blue-600">
                    <span class="text-sm text-gray-700 dark:text-gray-200">Basculement automatique</span>
                </label>
            </div>
            
            <div class="flex gap-2">
                <button type="button" 
                        onclick="previewPalette()"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 rounded-lg hover:bg-gray-200 dark:bg-gray-700 transition-colors">
                    <i class="fas fa-eye mr-1"></i>
                    Aperçu
                </button>
                
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-1"></i>
                    Appliquer
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function selectPalette(paletteKey) {
    // Décocher tous les autres
    document.querySelectorAll('input[name="palette"]').forEach(input => {
        input.checked = false;
        input.closest('.palette-option').classList.remove('border-blue-500', 'bg-blue-50');
        input.closest('.palette-option').classList.add('border-gray-200');
        input.closest('.palette-option').classList.add('dark:border-gray-700');
    });
    
    // Cocher celui sélectionné
    const selectedInput = document.getElementById('palette_' + paletteKey);
    selectedInput.checked = true;
    selectedInput.closest('.palette-option').classList.add('border-blue-500', 'bg-blue-50');
    selectedInput.closest('.palette-option').classList.remove('border-gray-200 dark:border-gray-700');
}

function previewPalette() {
    const selectedPalette = document.querySelector('input[name="palette"]:checked')?.value;
    if (selectedPalette) {
        // Appliquer un aperçu temporaire
        fetch(`/admin/settings/colors/preview/${selectedPalette}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Appliquer les styles CSS temporairement
                const style = document.createElement('style');
                style.id = 'preview-palette';
                style.textContent = data.css;
                
                // Supprimer l'ancien aperçu s'il existe
                const oldStyle = document.getElementById('preview-palette');
                if (oldStyle) {
                    oldStyle.remove();
                }
                
                document.head.appendChild(style);
                
                // Afficher une notification
                showNotification('Aperçu appliqué ! Actualisez la page pour annuler.', 'info');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'aperçu:', error);
            showNotification('Erreur lors de l\'aperçu', 'error');
        });
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
        type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
        'bg-blue-100 text-blue-800 border border-blue-200'
    }`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>