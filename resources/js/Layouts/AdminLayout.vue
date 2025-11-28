<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <!-- Admin Navigation -->
            <nav class="border-b border-gray-200 bg-white shadow-sm">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link href="/" class="flex items-center gap-2">
                                    <span class="text-2xl">🎯</span>
                                    <span class="text-xl font-bold text-purple-600">VintApp Admin</span>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <Link
                                    href="/admin"
                                    :class="[
                                        'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium transition',
                                        route().current('admin.dashboard') 
                                            ? 'border-purple-500 text-gray-900' 
                                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                    ]"
                                >
                                    📊 Dashboard
                                </Link>
                                
                                <Link
                                    href="/admin/monitoring"
                                    :class="[
                                        'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium transition',
                                        route().current('admin.monitoring.*') 
                                            ? 'border-purple-500 text-gray-900' 
                                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                    ]"
                                >
                                    📈 Monitoring
                                </Link>
                                
                                <a
                                    href="/telescope"
                                    target="_blank"
                                    class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 transition"
                                >
                                    🔭 Telescope
                                </a>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center gap-4">
                            <!-- User Info -->
                            <div class="text-sm text-gray-600">
                                👤 {{ page.props.auth?.user?.name || 'Admin' }}
                            </div>
                            
                            <!-- Logout -->
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="text-sm text-gray-600 hover:text-gray-900 transition"
                            >
                                🚪 Déconnexion
                            </Link>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none transition"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <Link
                            href="/admin"
                            :class="[
                                'block border-l-4 py-2 pe-4 ps-3 text-base font-medium transition',
                                route().current('admin.dashboard')
                                    ? 'border-purple-400 bg-purple-50 text-purple-700'
                                    : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800'
                            ]"
                        >
                            📊 Dashboard
                        </Link>
                        
                        <Link
                            href="/admin/monitoring"
                            :class="[
                                'block border-l-4 py-2 pe-4 ps-3 text-base font-medium transition',
                                route().current('admin.monitoring.*')
                                    ? 'border-purple-400 bg-purple-50 text-purple-700'
                                    : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800'
                            ]"
                        >
                            📈 Monitoring
                        </Link>
                        
                        <a
                            href="/telescope"
                            target="_blank"
                            class="block border-l-4 border-transparent py-2 pe-4 ps-3 text-base font-medium text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800 transition"
                        >
                            🔭 Telescope
                        </a>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ page.props.auth?.user?.name || 'Admin' }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ page.props.auth?.user?.email || '' }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="block w-full px-4 py-2 text-start text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800 transition"
                            >
                                🚪 Déconnexion
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
