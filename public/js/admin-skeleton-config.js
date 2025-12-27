/**
 * Configuration du Page Skeleton pour l'interface Admin
 * VintApp PWA
 */

(function() {
    'use strict';

    // Attendre que PageSkeletonLoader soit chargé
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof PageSkeletonLoader === 'undefined') {
            return;
        }

        // Configuration personnalisée pour l'admin
        const adminSkeletonConfig = {
            minDisplayTime: 300,  // Plus court pour l'admin (interface rapide)
            fadeOutDuration: 200
        };

        // Détection du type de page admin
        const AdminPageDetector = {
            isDashboard: function() {
                return window.location.pathname.includes('/admin') && 
                       (window.location.pathname.endsWith('/dashboard') || 
                        window.location.pathname.endsWith('/admin'));
            },

            isUsersList: function() {
                return window.location.pathname.includes('/admin/users') && 
                       !window.location.pathname.match(/\/\d+$/);
            },

            isUserDetail: function() {
                return window.location.pathname.match(/\/admin\/users\/\d+/);
            },

            isOrdersList: function() {
                return window.location.pathname.includes('/admin/orders') && 
                       !window.location.pathname.match(/\/\d+$/);
            },

            isTransactionsList: function() {
                return window.location.pathname.includes('/admin/transactions');
            },

            isSupportChat: function() {
                return window.location.pathname.includes('/admin/support');
            },

            isSettings: function() {
                return window.location.pathname.includes('/admin/settings') || 
                       window.location.pathname.includes('/admin/locations');
            },

            isVerification: function() {
                return window.location.pathname.includes('/admin/items/pending_verification') ||
                       window.location.pathname.includes('/admin/experts') ||
                       window.location.pathname.includes('/expert/verifications');
            },

            isAffiliate: function() {
                return window.location.pathname.includes('/admin/affiliate');
            },

            isMonitoring: function() {
                return window.location.pathname.includes('/admin/monitoring');
            },

            getPageType: function() {
                // Vérifier d'abord l'attribut data-page-type
                const pageTypeAttr = document.querySelector('[data-page-type]');
                if (pageTypeAttr) {
                    return pageTypeAttr.getAttribute('data-page-type');
                }

                // Sinon, détecter automatiquement
                if (this.isDashboard()) return 'dashboard';
                if (this.isUsersList() || this.isOrdersList() || this.isTransactionsList()) return 'list';
                if (this.isUserDetail()) return 'detail';
                if (this.isSupportChat()) return 'chat';
                if (this.isSettings()) return 'form';
                if (this.isVerification()) return 'verification';
                if (this.isAffiliate()) return 'dashboard';
                if (this.isMonitoring()) return 'dashboard';

                return 'dashboard'; // Par défaut
            }
        };

        // Templates personnalisés pour l'admin
        const AdminSkeletonTemplates = {
            // Template pour page de liste (utilisateurs, commandes, transactions)
            showAdminList: function(skeleton) {
                const html = `
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <!-- Header -->
                        <div class="mb-6">
                            <div class="skeleton-loader skeleton-title w-64 mb-3"></div>
                            <div class="flex gap-4 items-center">
                                <div class="skeleton-loader skeleton-button w-32"></div>
                                <div class="skeleton-loader skeleton-button w-32"></div>
                                <div class="flex-1"></div>
                                <div class="skeleton-loader skeleton-button w-40"></div>
                            </div>
                        </div>

                        <!-- Filtres -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 mb-6 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="skeleton-loader h-10"></div>
                                <div class="skeleton-loader h-10"></div>
                                <div class="skeleton-loader h-10"></div>
                                <div class="skeleton-loader h-10"></div>
                            </div>
                        </div>

                        <!-- Tableau -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                            <!-- En-têtes du tableau -->
                            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                <div class="grid grid-cols-5 gap-4">
                                    <div class="skeleton-loader skeleton-text w-24"></div>
                                    <div class="skeleton-loader skeleton-text w-32"></div>
                                    <div class="skeleton-loader skeleton-text w-28"></div>
                                    <div class="skeleton-loader skeleton-text w-20"></div>
                                    <div class="skeleton-loader skeleton-text w-24"></div>
                                </div>
                            </div>
                            <!-- Lignes du tableau -->
                            ${Array(8).fill(0).map(() => `
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <div class="grid grid-cols-5 gap-4 items-center">
                                        <div class="flex items-center gap-3">
                                            <div class="skeleton-loader skeleton-avatar"></div>
                                            <div class="skeleton-loader skeleton-text w-32"></div>
                                        </div>
                                        <div class="skeleton-loader skeleton-text w-40"></div>
                                        <div class="skeleton-loader skeleton-text w-24"></div>
                                        <div class="skeleton-loader skeleton-button w-20 h-6"></div>
                                        <div class="skeleton-loader skeleton-button w-24 h-8"></div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-between items-center">
                            <div class="skeleton-loader skeleton-text w-48"></div>
                            <div class="flex gap-2">
                                ${Array(5).fill(0).map(() => `
                                    <div class="skeleton-loader w-10 h-10"></div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                `;
                skeleton.showCustom(html);
            },

            // Template pour page de détail/formulaire
            showAdminDetail: function(skeleton) {
                const html = `
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <!-- Header avec actions -->
                        <div class="flex justify-between items-center mb-6">
                            <div class="skeleton-loader skeleton-title w-80"></div>
                            <div class="flex gap-3">
                                <div class="skeleton-loader skeleton-button w-32"></div>
                                <div class="skeleton-loader skeleton-button w-32"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Colonne principale -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Card 1 -->
                                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                                    <div class="skeleton-loader skeleton-title w-48 mb-6"></div>
                                    <div class="space-y-4">
                                        ${Array(5).fill(0).map(() => `
                                            <div>
                                                <div class="skeleton-loader skeleton-text w-32 mb-2"></div>
                                                <div class="skeleton-loader h-10 w-full"></div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>

                                <!-- Card 2 -->
                                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                                    <div class="skeleton-loader skeleton-title w-40 mb-6"></div>
                                    <div class="space-y-4">
                                        ${Array(3).fill(0).map(() => `
                                            <div class="skeleton-loader skeleton-text w-full"></div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar -->
                            <div class="space-y-6">
                                <!-- Card sidebar 1 -->
                                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                                    <div class="skeleton-loader skeleton-title w-32 mb-4"></div>
                                    <div class="space-y-3">
                                        ${Array(4).fill(0).map(() => `
                                            <div class="flex justify-between">
                                                <div class="skeleton-loader skeleton-text w-24"></div>
                                                <div class="skeleton-loader skeleton-text w-20"></div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>

                                <!-- Card sidebar 2 -->
                                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                                    <div class="skeleton-loader skeleton-avatar w-24 h-24 mx-auto mb-4"></div>
                                    <div class="skeleton-loader skeleton-text w-32 mx-auto mb-2"></div>
                                    <div class="skeleton-loader skeleton-text w-40 mx-auto"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                skeleton.showCustom(html);
            },

            // Template pour page de vérification
            showVerification: function(skeleton) {
                const html = `
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <!-- Header -->
                        <div class="mb-6">
                            <div class="skeleton-loader skeleton-title w-72 mb-3"></div>
                            <div class="skeleton-loader skeleton-text w-96"></div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                            ${Array(4).fill(0).map(() => `
                                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div class="skeleton-loader w-12 h-12 rounded-full"></div>
                                        <div class="flex-1">
                                            <div class="skeleton-loader skeleton-text w-24 mb-2"></div>
                                            <div class="skeleton-loader skeleton-title w-16"></div>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>

                        <!-- Items Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            ${Array(6).fill(0).map(() => `
                                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm">
                                    <div class="skeleton-loader skeleton-image h-48"></div>
                                    <div class="p-4">
                                        <div class="skeleton-loader skeleton-title w-3/4 mb-3"></div>
                                        <div class="skeleton-loader skeleton-text w-full mb-2"></div>
                                        <div class="skeleton-loader skeleton-text w-2/3 mb-4"></div>
                                        <div class="flex gap-2">
                                            <div class="skeleton-loader skeleton-button flex-1"></div>
                                            <div class="skeleton-loader skeleton-button flex-1"></div>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
                skeleton.showCustom(html);
            }
        };

        // Exposer globalement pour l'utiliser dans navigation-skeleton.js
        window.AdminPageDetector = AdminPageDetector;
        window.AdminSkeletonTemplates = AdminSkeletonTemplates;
        window.adminSkeletonConfig = adminSkeletonConfig;
    });
})();
