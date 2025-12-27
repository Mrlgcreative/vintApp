// Test des modals de gestion des wallets

// Test des fonctions modales
document.addEventListener('DOMContentLoaded', function() {
    // Test si les fonctions existent (silencieux)
    const modalFunctionsExist = typeof openModal !== 'undefined' && 
                                 typeof closeModal !== 'undefined';
    
    // Test les éléments DOM
    const modals = [
        'createWalletModal',
        'addCommissionModal', 
        'withdrawModal'
    ];
    
    // Vérification silencieuse
    modals.forEach(modalId => {
        document.getElementById(modalId);
    });
});

// Fonction de test manuel
function testModal(modalId) {
    if (typeof openModal !== 'undefined') {
        openModal(modalId);
    }
}

// Exposer la fonction de test
window.testModal = testModal;