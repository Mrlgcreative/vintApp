

<?php $__env->startSection('title', 'Utilisateurs Connectés'); ?>
<?php $__env->startSection('page-title', 'Utilisateurs Connectés en Temps Réel'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .online-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #10B981;
        display: inline-block;
        position: relative;
        animation: pulse-green 2s infinite;
    }
    
    @keyframes pulse-green {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
        }
    }
    
    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        border: 1px solid #e5e7eb;
    }
    
    .stat-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        transform: translateY(-4px);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .user-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        border: 1px solid #e5e7eb;
    }
    
    .user-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .avatar-wrapper {
        position: relative;
        display: inline-block;
    }
    
    .avatar-status {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #10B981;
        border: 2px solid white;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    
    .device-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .device-badge.mobile {
        background: #DBEAFE;
        color: #1E40AF;
    }
    
    .device-badge.tablet {
        background: #E9D5FF;
        color: #6B21A8;
    }
    
    .device-badge.desktop {
        background: #D1FAE5;
        color: #065F46;
    }
    
    .refresh-indicator {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
        color: white;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(106, 13, 173, 0.3);
    }
    
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- En-tête avec actualisation automatique -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="online-indicator"></div>
            <span class="text-sm text-gray-600">Mise à jour automatique toutes les 10 secondes</span>
        </div>
        
        <div class="refresh-indicator" id="refreshIndicator">
            <i class="fas fa-sync-alt mr-2 loading-spinner"></i>
            <span>Actualisation en cours...</span>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total connectés</p>
                    <p class="text-3xl font-bold text-gray-900" id="stat-total"><?php echo e($stats['total_online']); ?></p>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%); color: white;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Sur mobile</p>
                    <p class="text-3xl font-bold text-blue-600" id="stat-mobile"><?php echo e($stats['by_device']['mobile']); ?></p>
                </div>
                <div class="stat-icon" style="background: #DBEAFE; color: #1E40AF;">
                    <i class="fas fa-mobile-alt"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Sur tablette</p>
                    <p class="text-3xl font-bold text-purple-600" id="stat-tablet"><?php echo e($stats['by_device']['tablet']); ?></p>
                </div>
                <div class="stat-icon" style="background: #E9D5FF; color: #6B21A8;">
                    <i class="fas fa-tablet-alt"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Sur ordinateur</p>
                    <p class="text-3xl font-bold text-green-600" id="stat-desktop"><?php echo e($stats['by_device']['desktop']); ?></p>
                </div>
                <div class="stat-icon" style="background: #D1FAE5; color: #065F46;">
                    <i class="fas fa-desktop"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[250px]">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Rechercher par nom ou email..." 
                        class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <select id="deviceFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">Tous les appareils</option>
                <option value="mobile">Mobile</option>
                <option value="tablet">Tablette</option>
                <option value="desktop">Ordinateur</option>
            </select>
            
            <button onclick="refreshData()" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-sync-alt mr-2"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Liste des utilisateurs connectés -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-user-check text-purple-600 mr-2"></i>
                Liste des Utilisateurs Actifs
                <span class="text-sm font-normal text-gray-500 ml-2" id="userCount">(<?php echo e($onlineUsers->count()); ?> en ligne)</span>
            </h3>
        </div>
        
        <div class="p-6">
            <div id="usersGrid" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                <?php $__empty_1 = true; $__currentLoopData = $onlineUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $user = $userData['user'];
                        $session = $userData['session'];
                    ?>
                    <div class="user-card" data-device="<?php echo e($session->device_type); ?>" data-user-name="<?php echo e(strtolower($user->name)); ?>" data-user-email="<?php echo e(strtolower($user->email)); ?>">
                        <div class="flex items-start gap-3">
                            <div class="avatar-wrapper flex-shrink-0">
                                <img src="<?php echo e($user->avatar ?? asset('images/default-avatar.png')); ?>" 
                                    alt="<?php echo e($user->name); ?>" 
                                    class="w-12 h-12 rounded-full object-cover">
                                <span class="avatar-status"></span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-1">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 truncate"><?php echo e($user->name); ?></h4>
                                        <p class="text-xs text-gray-500 truncate"><?php echo e($user->email); ?></p>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="device-badge <?php echo e($session->device_type); ?>">
                                        <i class="fas <?php echo e($session->device_icon); ?> mr-1"></i>
                                        <?php echo e(ucfirst($session->device_type)); ?>

                                    </span>
                                    
                                    <?php if($session->browser): ?>
                                        <span class="text-xs text-gray-600">
                                            <i class="fab <?php echo e($session->browser_icon); ?> mr-1"></i>
                                            <?php echo e($session->browser); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mt-2 flex items-center justify-between text-xs">
                                    <span class="text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?php echo e($session->last_activity_text); ?>

                                    </span>
                                    
                                    <?php if($session->location_text !== 'Localisation inconnue'): ?>
                                        <span class="text-gray-500">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            <?php echo e($session->location_text); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-network-wired mr-1"></i>
                                        <?php echo e($session->ip_address); ?>

                                    </span>
                                    
                                    <button onclick="viewUserDetails(<?php echo e($user->id); ?>)" 
                                        class="text-xs text-purple-600 hover:text-purple-700 font-medium">
                                        <i class="fas fa-eye mr-1"></i>
                                        Détails
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-user-slash text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg">Aucun utilisateur connecté pour le moment</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let refreshInterval;
let isRefreshing = false;

// Démarrer l'actualisation automatique
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
    setupFilters();
});

function startAutoRefresh() {
    // Actualiser toutes les 10 secondes
    refreshInterval = setInterval(() => {
        refreshData();
    }, 10000);
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

async function refreshData() {
    if (isRefreshing) return;
    
    isRefreshing = true;
    const indicator = document.getElementById('refreshIndicator');
    indicator.innerHTML = '<i class="fas fa-sync-alt mr-2 loading-spinner"></i><span>Actualisation...</span>';
    
    try {
        const response = await fetch('<?php echo e(route("admin.users.online.data")); ?>', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error('Erreur réseau');
        
        const data = await response.json();
        
        if (data.success) {
            updateStats(data);
            updateUsersList(data.users);
            
            indicator.innerHTML = '<i class="fas fa-check mr-2"></i><span>Mis à jour</span>';
            setTimeout(() => {
                indicator.innerHTML = '<i class="fas fa-sync-alt mr-2"></i><span>Prochaine actualisation dans 10s</span>';
            }, 2000);
        }
        
    } catch (error) {
        console.error('Erreur lors de l\'actualisation:', error);
        indicator.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i><span>Erreur</span>';
    } finally {
        isRefreshing = false;
    }
}

function updateStats(data) {
    // Compter par type d'appareil
    const deviceCounts = {
        mobile: 0,
        tablet: 0,
        desktop: 0
    };
    
    data.users.forEach(user => {
        if (deviceCounts.hasOwnProperty(user.device_type)) {
            deviceCounts[user.device_type]++;
        }
    });
    
    // Mettre à jour les statistiques
    document.getElementById('stat-total').textContent = data.count;
    document.getElementById('stat-mobile').textContent = deviceCounts.mobile;
    document.getElementById('stat-tablet').textContent = deviceCounts.tablet;
    document.getElementById('stat-desktop').textContent = deviceCounts.desktop;
    document.getElementById('userCount').textContent = `(${data.count} en ligne)`;
}

function updateUsersList(users) {
    const grid = document.getElementById('usersGrid');
    
    if (users.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="fas fa-user-slash text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">Aucun utilisateur connecté pour le moment</p>
            </div>
        `;
        return;
    }
    
    const userCards = users.map(userData => {
        const user = userData.user;
        const deviceBadgeClass = userData.device_type;
        const deviceIcon = userData.device_icon;
        const browserIcon = userData.browser_icon;
        
        return `
            <div class="user-card" data-device="${userData.device_type}" 
                data-user-name="${user.name.toLowerCase()}" 
                data-user-email="${user.email.toLowerCase()}">
                <div class="flex items-start gap-3">
                    <div class="avatar-wrapper flex-shrink-0">
                        <img src="${user.avatar}" alt="${user.name}" 
                            class="w-12 h-12 rounded-full object-cover">
                        <span class="avatar-status"></span>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-1">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 truncate">${user.name}</h4>
                                <p class="text-xs text-gray-500 truncate">${user.email}</p>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="device-badge ${deviceBadgeClass}">
                                <i class="fas ${deviceIcon} mr-1"></i>
                                ${userData.device_type.charAt(0).toUpperCase() + userData.device_type.slice(1)}
                            </span>
                            
                            ${userData.browser ? `
                                <span class="text-xs text-gray-600">
                                    <i class="fab ${browserIcon} mr-1"></i>
                                    ${userData.browser}
                                </span>
                            ` : ''}
                        </div>
                        
                        <div class="mt-2 flex items-center justify-between text-xs">
                            <span class="text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                ${userData.last_activity}
                            </span>
                            
                            ${userData.location !== 'Localisation inconnue' ? `
                                <span class="text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    ${userData.location}
                                </span>
                            ` : ''}
                        </div>
                        
                        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-network-wired mr-1"></i>
                                ${userData.ip_address}
                            </span>
                            
                            <button onclick="viewUserDetails(${user.id})" 
                                class="text-xs text-purple-600 hover:text-purple-700 font-medium">
                                <i class="fas fa-eye mr-1"></i>
                                Détails
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    grid.innerHTML = userCards;
    applyFilters();
}

function setupFilters() {
    const searchInput = document.getElementById('searchInput');
    const deviceFilter = document.getElementById('deviceFilter');
    
    searchInput.addEventListener('input', applyFilters);
    deviceFilter.addEventListener('change', applyFilters);
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const deviceType = document.getElementById('deviceFilter').value;
    const userCards = document.querySelectorAll('.user-card');
    
    let visibleCount = 0;
    
    userCards.forEach(card => {
        const userName = card.dataset.userName || '';
        const userEmail = card.dataset.userEmail || '';
        const cardDevice = card.dataset.device || '';
        
        const matchesSearch = searchTerm === '' || 
            userName.includes(searchTerm) || 
            userEmail.includes(searchTerm);
        
        const matchesDevice = deviceType === '' || cardDevice === deviceType;
        
        if (matchesSearch && matchesDevice) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Mettre à jour le compteur
    document.getElementById('userCount').textContent = `(${visibleCount} affichés)`;
}

function viewUserDetails(userId) {
    window.location.href = `/admin/users/${userId}`;
}

// Arrêter l'actualisation quand on quitte la page
window.addEventListener('beforeunload', () => {
    stopAutoRefresh();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/users/online.blade.php ENDPATH**/ ?>