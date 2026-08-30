@extends('layouts.admin')

@section('title', 'Logs système')
@section('page-title', 'Logs système')
@section('page-subtitle', 'Consultez et gérez les logs de l\'application')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <button onclick="clearLogs()"
            class="inline-flex items-center gap-2 rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-broom"></i>Vider les logs
    </button>
    <button onclick="downloadLogs()"
            class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-download"></i>Télécharger
    </button>
</div>
@endsection

@section('content')
<!-- Statistiques -->
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Erreurs aujourd'hui</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-red-600">{{ $stats['error'] ?? 0 }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
                <i class="fas fa-circle-xmark text-[10px]"></i>
                Erreurs
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-circle-xmark text-xs text-red-500"></i>
                Erreurs critiques
            </div>
            <div class="text-xs text-slate-400">Niveau error et supérieur</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Avertissements</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-amber-600">{{ $stats['warning'] ?? 0 }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                <i class="fas fa-triangle-exclamation text-[10px]"></i>
                Warnings
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-triangle-exclamation text-xs text-amber-500"></i>
                Avertissements
            </div>
            <div class="text-xs text-slate-400">Niveau warning</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Informations</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-sky-600">{{ $stats['info'] ?? 0 }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-sky-200 bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-400">
                <i class="fas fa-circle-info text-[10px]"></i>
                Info
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-circle-info text-xs text-sky-500"></i>
                Informations
            </div>
            <div class="text-xs text-slate-400">Niveaux notice et info</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Taille du fichier</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-primary-600">{{ number_format(($fileSize ?? 0) / 1024, 0) }} KB</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-primary-200 bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700 dark:border-primary-500/30 dark:bg-primary-500/10 dark:text-primary-400">
                <i class="fas fa-file-lines text-[10px]"></i>
                Fichier
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-file-lines text-xs text-primary-500"></i>
                laravel.log
            </div>
            <div class="text-xs text-slate-400">{{ number_format(($fileSize ?? 0), 0, '.', ' ') }} octets</div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
        <i class="fas fa-filter text-primary-600"></i>
        Filtres
    </h3>
    <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Niveau -->
        <div>
            <label for="level" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                <i class="fas fa-layer-group mr-1 text-slate-400"></i>
                Niveau
            </label>
            <select class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    id="level" name="level">
                <option value="">Tous les niveaux</option>
                <option value="emergency" {{ request('level') === 'emergency' ? 'selected' : '' }}>🚨 Emergency</option>
                <option value="alert" {{ request('level') === 'alert' ? 'selected' : '' }}>🔴 Alert</option>
                <option value="critical" {{ request('level') === 'critical' ? 'selected' : '' }}>❌ Critical</option>
                <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>❗ Error</option>
                <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>⚠️ Warning</option>
                <option value="notice" {{ request('level') === 'notice' ? 'selected' : '' }}>📢 Notice</option>
                <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>ℹ️ Info</option>
                <option value="debug" {{ request('level') === 'debug' ? 'selected' : '' }}>🐛 Debug</option>
            </select>
        </div>

        <!-- Date -->
        <div>
            <label for="date" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                <i class="fas fa-calendar mr-1 text-slate-400"></i>
                Date
            </label>
            <input type="date"
                   class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                   id="date" name="date" value="{{ request('date') }}">
        </div>

        <!-- Recherche -->
        <div>
            <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                <i class="fas fa-search mr-1 text-slate-400"></i>
                Recherche
            </label>
            <input type="text"
                   class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                   id="search" name="search" placeholder="Rechercher..." value="{{ request('search') }}">
        </div>

        <!-- Bouton -->
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">&nbsp;</label>
            <button type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                <i class="fas fa-filter"></i>
                Filtrer
            </button>
        </div>
    </form>
</div>

<!-- Logs - Vue Desktop -->
<div class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm lg:block dark:border-slate-700 dark:bg-slate-800">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
        <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
            <i class="fas fa-list text-slate-600 dark:text-slate-300"></i>
            Entrées des logs
            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                {{ count($logs) }}
            </span>
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 dark:bg-slate-900">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Niveau</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Message</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Contexte</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Date/Heure</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white dark:bg-slate-800">
                @forelse($logs as $log)
                    <tr class="transition-colors hover:bg-slate-50 dark:bg-slate-900">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $levelLower = strtolower($log['level']);
                                $badgeClasses = match($levelLower) {
                                    'emergency', 'alert', 'critical', 'error' => 'bg-red-100 text-red-700 ring-red-600/10 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/30',
                                    'warning' => 'bg-amber-100 text-amber-700 ring-amber-600/10 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/30',
                                    'notice', 'info' => 'bg-sky-100 text-sky-700 ring-sky-600/10 dark:bg-sky-500/10 dark:text-sky-400 dark:ring-sky-500/30',
                                    'debug' => 'bg-slate-100 text-slate-700 ring-slate-600/10 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-500/20',
                                    default => 'bg-slate-100 text-slate-700 ring-slate-600/10 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-500/20',
                                };
                                $iconColor = match($levelLower) {
                                    'emergency', 'alert', 'critical', 'error' => 'text-red-500',
                                    'warning' => 'text-amber-500',
                                    'notice', 'info' => 'text-sky-500',
                                    'debug' => 'text-slate-400',
                                    default => 'text-slate-400',
                                };
                                $icon = match($levelLower) {
                                    'emergency', 'alert', 'critical', 'error' => 'fa-circle-xmark',
                                    'warning' => 'fa-triangle-exclamation',
                                    'notice', 'info' => 'fa-circle-info',
                                    'debug' => 'fa-bug',
                                    default => 'fa-circle',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badgeClasses }}">
                                <i class="fas {{ $icon }} text-[10px] {{ $iconColor }}"></i>
                                {{ strtoupper($log['level']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-md text-sm text-slate-900 dark:text-white">
                                {{ \Illuminate\Support\Str::limit($log['message'], 100) }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1 text-sm text-slate-600 dark:text-slate-300">
                                <div><span class="font-medium">Env:</span> {{ $log['env'] }}</div>
                                @if(!empty(trim($log['context'])))
                                    <div class="max-w-xs overflow-hidden text-xs">
                                        <span class="font-medium">Context:</span>
                                        {{ \Illuminate\Support\Str::limit($log['context'], 50) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($log['datetime'])->format('d/m/Y H:i:s') }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($log['datetime'])->diffForHumans() }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <i class="fas fa-inbox mb-3 text-4xl"></i>
                                <p class="text-base font-medium">Aucun log trouvé</p>
                                <p class="text-sm">Les logs s'afficheront ici lorsqu'ils seront générés</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="flex justify-center border-t border-slate-100 px-5 py-4 dark:border-slate-700">
        {{ $logs->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Logs - Vue Mobile (Cards) -->
<div class="space-y-4 lg:hidden">
    @forelse($logs as $log)
        @php
            $levelLower = strtolower($log['level']);
            $bgClass = match($levelLower) {
                'emergency', 'alert', 'critical', 'error' => 'bg-red-50 dark:bg-red-950/30 border-red-100 dark:border-red-900/30',
                'warning' => 'bg-amber-50 dark:bg-amber-950/30 border-amber-100 dark:border-amber-900/30',
                'notice', 'info' => 'bg-sky-50 dark:bg-sky-950/30 border-sky-100 dark:border-sky-900/30',
                'debug' => 'bg-slate-50 dark:bg-slate-900 border-slate-100 dark:border-slate-800',
                default => 'bg-slate-50 dark:bg-slate-900 border-slate-100 dark:border-slate-800',
            };
            $badgeClass = match($levelLower) {
                'emergency', 'alert', 'critical', 'error' => 'bg-red-100 text-red-700 ring-red-600/10 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/30',
                'warning' => 'bg-amber-100 text-amber-700 ring-amber-600/10 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/30',
                'notice', 'info' => 'bg-sky-100 text-sky-700 ring-sky-600/10 dark:bg-sky-500/10 dark:text-sky-400 dark:ring-sky-500/30',
                'debug' => 'bg-slate-100 text-slate-700 ring-slate-600/10 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-500/20',
                default => 'bg-slate-100 text-slate-700 ring-slate-600/10 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-500/20',
            };
            $iconColor = match($levelLower) {
                'emergency', 'alert', 'critical', 'error' => 'text-red-500',
                'warning' => 'text-amber-500',
                'notice', 'info' => 'text-sky-500',
                'debug' => 'text-slate-400',
                default => 'text-slate-400',
            };
            $icon = match($levelLower) {
                'emergency', 'alert', 'critical', 'error' => 'fa-circle-xmark',
                'warning' => 'fa-triangle-exclamation',
                'notice', 'info' => 'fa-circle-info',
                'debug' => 'fa-bug',
                default => 'fa-circle',
            };
        @endphp

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="{{ $bgClass }} border-b px-4 py-3">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badgeClass }}">
                        <i class="fas {{ $icon }} text-[10px] {{ $iconColor }}"></i>
                        {{ strtoupper($log['level']) }}
                    </span>
                    <span class="text-xs text-slate-600 dark:text-slate-300">{{ \Carbon\Carbon::parse($log['datetime'])->diffForHumans() }}</span>
                </div>
            </div>
            <div class="space-y-3 p-4">
                <div>
                    <p class="mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">Message:</p>
                    <p class="text-sm text-slate-900 dark:text-white">{{ $log['message'] }}</p>
                </div>
                <div>
                    <p class="mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">Contexte:</p>
                    <div class="space-y-1 text-sm text-slate-600 dark:text-slate-300">
                        <div><span class="font-medium">Env:</span> {{ $log['env'] }}</div>
                        @if(!empty(trim($log['context'])))
                            <div class="max-h-24 overflow-auto rounded bg-slate-50 p-2 text-xs dark:bg-slate-900">
                                <pre class="whitespace-pre-wrap text-xs">{{ \Illuminate\Support\Str::limit($log['context'], 200) }}</pre>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="border-t border-slate-100 pt-2">
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($log['datetime'])->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-slate-200 bg-white p-12 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex flex-col items-center justify-center text-slate-400">
                <i class="fas fa-inbox mb-4 text-5xl"></i>
                <p class="text-center text-lg font-medium">Aucun log trouvé</p>
                <p class="mt-2 text-center text-sm">Les logs s'afficheront ici lorsqu'ils seront générés</p>
            </div>
        </div>
    @endforelse

    @if($logs->hasPages())
    <div class="flex justify-center pt-2">
        {{ $logs->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function clearLogs() {
    if (confirm('Êtes-vous sûr de vouloir vider tous les logs ? Cette action est irréversible.')) {
        // AJAX call to clear logs
        fetch('/admin/logs/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Logs vidés avec succès !');
            location.reload();
        })
        .catch(error => {
            alert('Erreur lors de la suppression des logs');
            console.error(error);
        });
    }
}

function downloadLogs() {
    window.location.href = '/admin/logs/download';
}
</script>
@endpush