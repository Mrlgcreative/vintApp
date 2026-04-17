@extends('layouts.admin')

@section('title', 'Utilisateurs Connectés')
@section('page-title', 'Utilisateurs Connectés en Temps Réel')

@push('styles')
<style>
    @keyframes pulse-dot { 0%,100%{ box-shadow:0 0 0 0 rgba(16,185,129,.55) } 50%{ box-shadow:0 0 0 6px rgba(16,185,129,0) } }
    .pulse-dot{ animation:pulse-dot 2s infinite }
    @keyframes spin-icon { to{ transform:rotate(360deg) } }
    .spin-icon{ animation:spin-icon .8s linear infinite }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Header bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 pulse-dot"></span>
            <span class="text-xs text-gray-500 dark:text-gray-400">Mise à jour automatique toutes les 10 s</span>
        </div>
        <div id="refreshIndicator" class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary-600 text-white text-xs font-medium rounded-full shadow-sm">
            <svg class="w-3.5 h-3.5 spin-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span>Actualisation…</span>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-primary-600">Total connectés</span>
                <div class="w-9 h-9 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white" id="stat-total">{{ $stats['total_online'] }}</p>
        </div>

        {{-- Mobile --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-600">Sur mobile</span>
                <div class="w-9 h-9 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white" id="stat-mobile">{{ $stats['by_device']['mobile'] }}</p>
        </div>

        {{-- Tablette --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-violet-600">Sur tablette</span>
                <div class="w-9 h-9 bg-violet-50 dark:bg-violet-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white" id="stat-tablet">{{ $stats['by_device']['tablet'] }}</p>
        </div>

        {{-- Desktop --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-600">Sur ordinateur</span>
                <div class="w-9 h-9 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white" id="stat-desktop">{{ $stats['by_device']['desktop'] }}</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[220px] relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Rechercher par nom ou email…"
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
            </div>
            <select id="deviceFilter"
                    class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                <option value="">Tous les appareils</option>
                <option value="mobile">Mobile</option>
                <option value="tablet">Tablette</option>
                <option value="desktop">Ordinateur</option>
            </select>
            <button onclick="refreshData()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Actualiser
            </button>
        </div>
    </div>

    {{-- Carte géographique --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center gap-2.5">
                <svg class="w-4.5 h-4.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Localisation des utilisateurs</h3>
                <span class="text-[11px] text-gray-400 font-normal" id="mapUserCount"></span>
            </div>
            <button onclick="resetMapView()" class="text-[11px] text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Recentrer
            </button>
        </div>
        <div id="usersMap" class="w-full h-[350px] sm:h-[420px] z-0"></div>
    </div>

    {{-- Liste utilisateurs --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center gap-2.5">
                <svg class="w-4.5 h-4.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Utilisateurs Actifs</h3>
                <span class="text-[11px] text-gray-400 font-normal" id="userCount">({{ $onlineUsers->count() }} en ligne)</span>
            </div>
        </div>

        <div class="p-5">
            <div id="usersGrid" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($onlineUsers as $userData)
                    @php $user = $userData['user']; $session = $userData['session']; @endphp
                    <div class="rounded-xl border border-gray-100 dark:border-gray-700/50 p-4 hover:shadow-md transition-all duration-200 user-card"
                         data-device="{{ $session->device_type }}"
                         data-user-name="{{ strtolower($user->name) }}"
                         data-user-email="{{ strtolower($user->email) }}">
                        <div class="flex items-start gap-3">
                            {{-- Avatar --}}
                            <div class="relative flex-shrink-0">
                                <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}" alt="{{ $user->name }}"
                                     class="w-11 h-11 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700">
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                            </div>

                            <div class="flex-1 min-w-0">
                                {{-- Nom + email --}}
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $user->name }}</h4>
                                <p class="text-[11px] text-gray-400 truncate">{{ $user->email }}</p>

                                {{-- Device + Browser --}}
                                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                    @php
                                        $deviceColors = ['mobile' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400', 'tablet' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400', 'desktop' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium {{ $deviceColors[$session->device_type] ?? $deviceColors['desktop'] }}">
                                        @if($session->device_type === 'mobile')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @elseif($session->device_type === 'tablet')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @else
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        @endif
                                        {{ ucfirst($session->device_type) }}
                                    </span>

                                    @if($session->browser)
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $session->browser }}</span>
                                    @endif
                                </div>

                                {{-- Activité + Localisation --}}
                                <div class="flex items-center justify-between mt-2 text-[11px] text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $session->last_activity_text }}
                                    </span>
                                    @if($session->location_text !== 'Localisation inconnue')
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $session->location_text }}
                                        </span>
                                    @endif
                                </div>

                                {{-- IP + Détails --}}
                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/30">
                                    <span class="text-[11px] text-gray-400 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                                        {{ $session->ip_address }}
                                    </span>
                                    <button onclick="viewUserDetails({{ $user->id }})"
                                            class="text-[11px] font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Détails
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-14">
                        <svg class="w-12 h-12 text-gray-200 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        <p class="text-sm text-gray-400">Aucun utilisateur connecté pour le moment</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Carte Leaflet ──
    const map = L.map('usersMap', { zoomControl: true, scrollWheelZoom: true }).setView([0, 20], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
        maxZoom: 18
    }).addTo(map);

    let markersLayer = L.layerGroup().addTo(map);
    const defaultBounds = L.latLngBounds([[-35, -20], [60, 55]]);

    // Données initiales de la vue Blade
    @php
        $mapData = $onlineUsers->map(function($ud) {
            $s = $ud['session'];
            $u = $ud['user'];
            return [
                'name' => $u->name,
                'email' => $u->email,
                'avatar' => $u->avatar ?? asset('images/default-avatar.png'),
                'lat' => $s->latitude,
                'lng' => $s->longitude,
                'location' => $s->location_text,
                'device_type' => $s->device_type,
                'user_id' => $u->id,
            ];
        })->values();
    @endphp
    const initialMapData = @json($mapData);

    function updateMapMarkers(users) {
        markersLayer.clearLayers();
        const bounds = [];
        let count = 0;

        const deviceColors = { mobile: '#3b82f6', tablet: '#8b5cf6', desktop: '#10b981' };

        (users || []).forEach(u => {
            const lat = parseFloat(u.lat || u.latitude);
            const lng = parseFloat(u.lng || u.longitude);
            if (!lat || !lng || isNaN(lat) || isNaN(lng)) return;

            count++;
            bounds.push([lat, lng]);

            const color = deviceColors[u.device_type] || deviceColors.desktop;
            const icon = L.divIcon({
                html: `<div style="position:relative">
                    <div style="width:32px;height:32px;border-radius:50%;border:2.5px solid ${color};overflow:hidden;background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.25)">
                        <img src="${(u.avatar || '').replace(/"/g, '&quot;')}" style="width:100%;height:100%;object-fit:cover" onerror="this.style.display='none'"/>
                    </div>
                    <div style="position:absolute;bottom:-2px;right:-2px;width:10px;height:10px;border-radius:50%;background:#10b981;border:2px solid #fff"></div>
                </div>`,
                className: '',
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -18]
            });

            const esc = s => s ? String(s).replace(/</g,'&lt;').replace(/>/g,'&gt;') : '';
            const popup = `
                <div style="font-family:system-ui;min-width:160px">
                    <div style="font-weight:600;font-size:13px;margin-bottom:2px">${esc(u.name || (u.user && u.user.name))}</div>
                    <div style="font-size:11px;color:#6b7280;margin-bottom:4px">${esc(u.email || (u.user && u.user.email))}</div>
                    <div style="font-size:11px;color:#6b7280">📍 ${esc(u.location)}</div>
                    <div style="font-size:11px;color:#6b7280">📱 ${esc((u.device_type||'desktop').charAt(0).toUpperCase()+(u.device_type||'desktop').slice(1))}</div>
                </div>`;

            L.marker([lat, lng], { icon }).bindPopup(popup).addTo(markersLayer);
        });

        document.getElementById('mapUserCount').textContent = count > 0 ? `(${count} localisé${count > 1 ? 's' : ''})` : '(aucune position)';

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 12 });
        } else {
            map.fitBounds(defaultBounds);
        }
    }

    window.resetMapView = function() {
        if (markersLayer.getLayers().length > 0) {
            const bounds = [];
            markersLayer.eachLayer(m => { if (m.getLatLng) bounds.push(m.getLatLng()); });
            if (bounds.length) map.fitBounds(bounds, { padding: [40, 40], maxZoom: 12 });
        } else {
            map.fitBounds(defaultBounds);
        }
    };

    // Init map avec données Blade
    updateMapMarkers(initialMapData);

    // Forcer le recalcul de la taille après le rendu
    setTimeout(() => map.invalidateSize(), 200);

    // ── Auto-refresh ──
    let isRefreshing = false;

    const indicator = document.getElementById('refreshIndicator');
    const searchInput = document.getElementById('searchInput');
    const deviceFilter = document.getElementById('deviceFilter');

    searchInput.addEventListener('input', applyFilters);
    deviceFilter.addEventListener('change', applyFilters);

    window.refreshData = async function() {
        if (isRefreshing) return;
        isRefreshing = true;
        setIndicator('loading');

        try {
            const res = await fetch('{{ route("admin.users.online.data") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error(res.status);
            const data = await res.json();

            if (data.success) {
                updateStats(data);
                updateUsersList(data.users);
                updateMapMarkers(data.users.map(u => ({
                    name: u.user.name,
                    email: u.user.email,
                    avatar: u.user.avatar,
                    lat: u.latitude,
                    lng: u.longitude,
                    location: u.location,
                    device_type: u.device_type,
                    user_id: u.user.id
                })));
                setIndicator('success');
            }
        } catch (e) {
            setIndicator('error');
        } finally {
            isRefreshing = false;
        }
    };

    function setIndicator(state) {
        const icons = {
            loading: '<svg class="w-3.5 h-3.5 spin-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>',
            success: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
            error:   '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };
        const labels = { loading: 'Actualisation…', success: 'Mis à jour', error: 'Erreur' };
        indicator.innerHTML = icons[state] + '<span>' + labels[state] + '</span>';

        if (state !== 'loading') {
            setTimeout(() => {
                indicator.innerHTML = icons.loading.replace('spin-icon','') + '<span>Prochaine dans 10 s</span>';
            }, 2500);
        }
    }

    function updateStats(data) {
        const dc = { mobile: 0, tablet: 0, desktop: 0 };
        data.users.forEach(u => { if (dc.hasOwnProperty(u.device_type)) dc[u.device_type]++; });
        document.getElementById('stat-total').textContent = data.count;
        document.getElementById('stat-mobile').textContent = dc.mobile;
        document.getElementById('stat-tablet').textContent = dc.tablet;
        document.getElementById('stat-desktop').textContent = dc.desktop;
        document.getElementById('userCount').textContent = '(' + data.count + ' en ligne)';
    }

    function updateUsersList(users) {
        const grid = document.getElementById('usersGrid');

        if (!users.length) {
            grid.innerHTML = '<div class="col-span-full text-center py-14"><svg class="w-12 h-12 text-gray-200 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg><p class="text-sm text-gray-400">Aucun utilisateur connecté</p></div>';
            return;
        }

        const deviceSvg = {
            mobile:  '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
            tablet:  '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
            desktop: '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'
        };
        const deviceCls = {
            mobile:  'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
            tablet:  'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400',
            desktop: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
        };

        grid.innerHTML = users.map(ud => {
            const u = ud.user, dt = ud.device_type || 'desktop';
            const esc = s => s ? s.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') : '';
            return `
            <div class="rounded-xl border border-gray-100 dark:border-gray-700/50 p-4 hover:shadow-md transition-all duration-200 user-card"
                 data-device="${esc(dt)}" data-user-name="${esc(u.name.toLowerCase())}" data-user-email="${esc(u.email.toLowerCase())}">
                <div class="flex items-start gap-3">
                    <div class="relative flex-shrink-0">
                        <img src="${esc(u.avatar)}" alt="${esc(u.name)}" class="w-11 h-11 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700">
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">${esc(u.name)}</h4>
                        <p class="text-[11px] text-gray-400 truncate">${esc(u.email)}</p>
                        <div class="flex flex-wrap items-center gap-1.5 mt-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium ${deviceCls[dt] || deviceCls.desktop}">${deviceSvg[dt] || deviceSvg.desktop} ${dt.charAt(0).toUpperCase()+dt.slice(1)}</span>
                            ${ud.browser ? '<span class="text-[11px] text-gray-500 dark:text-gray-400">'+esc(ud.browser)+'</span>' : ''}
                        </div>
                        <div class="flex items-center justify-between mt-2 text-[11px] text-gray-400">
                            <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>${esc(ud.last_activity)}</span>
                            ${ud.location && ud.location !== 'Localisation inconnue' ? '<span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'+esc(ud.location)+'</span>' : ''}
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/30">
                            <span class="text-[11px] text-gray-400 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>${esc(ud.ip_address)}</span>
                            <button onclick="viewUserDetails(${u.id})" class="text-[11px] font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Détails
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');

        applyFilters();
    }

    function applyFilters() {
        const term = searchInput.value.toLowerCase();
        const device = deviceFilter.value;
        let count = 0;

        document.querySelectorAll('.user-card').forEach(card => {
            const name = card.dataset.userName || '';
            const email = card.dataset.userEmail || '';
            const dev = card.dataset.device || '';
            const show = (!term || name.includes(term) || email.includes(term)) && (!device || dev === device);
            card.style.display = show ? '' : 'none';
            if (show) count++;
        });

        document.getElementById('userCount').textContent = '(' + count + ' affichés)';
    }

    window.viewUserDetails = function(id) {
        window.location.href = '/admin/users/' + id;
    };

    // Démarrer l'auto-refresh après que tout soit défini
    let refreshInterval = setInterval(refreshData, 10000);
    window.addEventListener('beforeunload', () => clearInterval(refreshInterval));
});
</script>
@endpush
