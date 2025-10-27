// Test des modals de gestion des wallets

// Test de débogage
console.log('Script wallet-modal-test.js chargé');

// Test des fonctions modales
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, test des modals...');
    
    // Test si les fonctions existent
    console.log('openModal function exists:', typeof openModal !== 'undefined');
    console.log('closeModal function exists:', typeof closeModal !== 'undefined');
    console.log('openCommissionModal function exists:', typeof openCommissionModal !== 'undefined');
    console.log('openWithdrawModal function exists:', typeof openWithdrawModal !== 'undefined');
    
    // Test les éléments DOM
    const modals = [
        'createWalletModal',
        'addCommissionModal', 
        'withdrawModal'
    ];
    
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        console.log(`Modal ${modalId} trouvé:`, modal !== null);
    });
    
    // Test les boutons
    const buttons = document.querySelectorAll('[onclick*="Modal"]');
    console.log('Boutons avec onclick Modal trouvés:', buttons.length);
    
    buttons.forEach((button, index) => {
        console.log(`Bouton ${index + 1}:`, button.getAttribute('onclick'));
    });
    
    // Ajouter des listeners de test aux boutons
    const createBtn = document.querySelector('[onclick="openModal(\'createWalletModal\')"]');
    const commissionBtn = document.querySelector('[onclick="openCommissionModal()"]');
    const withdrawBtns = document.querySelectorAll('[onclick*="openWithdrawModal"]');
    
    if (createBtn) {
        console.log('Bouton Créer Wallet trouvé');
        createBtn.addEventListener('click', function() {
            console.log('Click sur bouton Créer Wallet détecté!');
        });
    }
    
    if (commissionBtn) {
        console.log('Bouton Commission trouvé');
        commissionBtn.addEventListener('click', function() {
            console.log('Click sur bouton Commission détecté!');
        });
    }
    
    if (withdrawBtns.length > 0) {
        console.log(`${withdrawBtns.length} boutons Retirer trouvés`);
        withdrawBtns.forEach((btn, index) => {
            btn.addEventListener('click', function() {
                console.log(`Click sur bouton Retirer ${index + 1} détecté!`);
            });
        });
    }
    
    console.log('Tests de débogage terminés');
});

// Fonction de test manuel
function testModal(modalId) {
    console.log(`Test manuel du modal: ${modalId}`);
    if (typeof openModal !== 'undefined') {
        openModal(modalId);
        console.log('Modal ouvert avec succès');
    } else {
        console.error('Fonction openModal non trouvée');
    }
}

// Exposer la fonction de test
window.testModal = testModal;