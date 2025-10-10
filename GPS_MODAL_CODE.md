# Code à ajouter pour le modal GPS amélioré

## Fonctions JavaScript à ajouter dans la section @push('scripts')

```javascript
// ========================================
// 🌍 GESTION DU FORMULAIRE MULTI-PAYS
// ========================================

// Charger les villes majeures d'un pays
function loadMajorCities(countryCode) {
    if (!countryCode) {
        document.getElementById('majorCitiesContainer').classList.add('hidden');
        return;
    }
    
    // Mettre à jour le nom du pays
    const select = document.getElementById('countrySelect');
    const selectedOption = select.options[select.selectedIndex];
    document.getElementById('countryName').value = selectedOption.dataset.name;
    
    // Charger les villes majeures
    fetch(`/admin/settings/locations/api/countries/${countryCode}/major-cities`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.cities.length > 0) {
                displayMajorCities(data.cities);
                document.getElementById('majorCitiesContainer').classList.remove('hidden');
            } else {
                document.getElementById('majorCitiesContainer').classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('majorCitiesContainer').classList.add('hidden');
        });
}

// Afficher les villes majeures
function displayMajorCities(cities) {
    const container = document.getElementById('majorCitiesList');
    container.innerHTML = '';
    
    cities.slice(0, 6).forEach(city => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'px-3 py-2 text-sm bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-lg transition-colors text-left';
        button.innerHTML = `
            <div class="font-medium text-gray-900">${city.name}</div>
            <div class="text-xs text-gray-500">${city.population ? formatNumber(city.population) + ' hab.' : ''}</div>
        `;
        button.onclick = () => fillCityData(city);
        container.appendChild(button);
    });
}

// Remplir les données de la ville
function fillCityData(city) {
    document.getElementById('cityName').value = city.name;
    document.getElementById('cityLatitude').value = city.latitude;
    document.getElementById('cityLongitude').value = city.longitude;
    
    if (city.population) {
        document.getElementById('cityPopulation').value = city.population;
    }
    
    if (city.timezone) {
        document.getElementById('cityTimezone').value = city.timezone;
    }
    
    // Valider les coordonnées
    if (city.latitude && city.longitude) {
        validateCoordinates(city.latitude, city.longitude);
    }
}

// Valider les coordonnées GPS
function validateCoordinates(lat, lng) {
    const countryCode = document.getElementById('countrySelect').value;
    
    if (!countryCode || !lat || !lng) return;
    
    fetch('/admin/settings/locations/api/validate-coordinates', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            country_code: countryCode,
            latitude: parseFloat(lat),
            longitude: parseFloat(lng)
        })
    })
    .then(response => response.json())
    .then(data => {
        const validationDiv = document.getElementById('gpsValidation');
        validationDiv.classList.remove('hidden');
        
        if (data.is_valid) {
            validationDiv.innerHTML = `
                <div class="flex items-center gap-2 text-green-700">
                    <i class="fas fa-check-circle"></i>
                    <span>${data.message} (${data.distance_km} km du centre)</span>
                </div>
            `;
        } else {
            validationDiv.innerHTML = `
                <div class="flex items-center gap-2 text-orange-600">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>${data.message} (${data.distance_km} km du centre)</span>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Erreur validation:', error);
    });
}

// Ouvrir le picker GPS (carte modale)
function openGPSPicker() {
    alert('Fonctionnalité GPS Picker à venir!\n\nVous pourrez cliquer sur la carte pour sélectionner les coordonnées.');
    // TODO: Implémenter une modale avec carte Leaflet interactive
}

// Surveiller les changements de coordonnées
document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.getElementById('cityLatitude');
    const lngInput = document.getElementById('cityLongitude');
    
    if (latInput && lngInput) {
        latInput.addEventListener('change', function() {
            const lat = this.value;
            const lng = lngInput.value;
            if (lat && lng) {
                validateCoordinates(lat, lng);
            }
        });
        
        lngInput.addEventListener('change', function() {
            const lat = latInput.value;
            const lng = this.value;
            if (lat && lng) {
                validateCoordinates(lat, lng);
            }
        });
    }
    
    // Charger les villes majeures au chargement si pays sélectionné
    const countrySelect = document.getElementById('countrySelect');
    if (countrySelect && countrySelect.value) {
        loadMajorCities(countrySelect.value);
    }
});
```

## Instructions d'intégration

1. **Ajouter ces fonctions** dans le bloc `@push('scripts')` existant, juste après les fonctions de carte Leaflet

2. **Le modal amélioré** avec sélecteur de pays et GPS est déjà ajouté dans le fichier

3. **Tester** :
   - Ouvrir le modal "Ajouter une ville"
   - Sélectionner un pays → les villes majeures s'affichent
   - Cliquer sur une ville majeure → remplit automatiquement nom, GPS, population
   - Modifier les coordonnées → validation automatique
   - Voir la carte principale avec les villes colorées par statut

## Fonctionnalités implémentées

✅ Sélecteur de pays avec drapeaux emoji  
✅ Chargement des villes majeures par pays  
✅ Auto-remplissage des données (nom, GPS, population)  
✅ Validation des coordonnées GPS  
✅ Messages de validation en temps réel  
✅ Carte interactive Leaflet avec clustering  
✅ Markers colorés (vert=actif, rouge=inactif)  
✅ Popups avec infos complètes  
✅ Filtrage par pays  
✅ Actualisation en temps réel  

## Prochaines améliorations

🔄 GPS Picker modal (carte cliquable pour sélectionner coordonnées)  
🔄 Reverse geocoding (obtenir nom ville depuis coordonnées)  
🔄 Import CSV de villes en masse  
🔄 Export des villes en KML/GeoJSON  
