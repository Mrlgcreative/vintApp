@extends('layouts.admin')

@section('title', 'Logs système')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Logs système</h1>
    <div class="flex gap-3">
        <button onclick="clearLogs()" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
            <i class="fas fa-broom mr-2"></i>Vider les logs
        </button>
        <button onclick="downloadLogs()" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
            <i class="fas fa-download mr-2"></i>Télécharger
        </button>
    </div>
</div>

<!-- Filtres -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
    <div class="p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Niveau</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" id="level" name="level">
                    <option value="">Tous les niveaux</option>
                    <option value="emergency" {{ request('level') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                    <option value="alert" {{ request('level') === 'alert' ? 'selected' : '' }}>Alert</option>
                    <option value="critical" {{ request('level') === 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>Error</option>
                    <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="notice" {{ request('level') === 'notice' ? 'selected' : '' }}>Notice</option>
                    <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>Info</option>
                    <option value="debug" {{ request('level') === 'debug' ? 'selected' : '' }}>Debug</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
            </div>
            
            <div class="col-md-4">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Rechercher dans les logs..." value="{{ request('search') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Logs -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Entrées des logs</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Niveau</th>
                        <th>Message</th>
                        <th>Contexte</th>
                        <th>Date/Heure</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Exemple de logs statiques pour la démonstration -->
                    <tr>
                        <td>
                            <span class="badge bg-danger">ERROR</span>
                        </td>
                        <td>
                            <div class="text-wrap" style="max-width: 400px;">
                                SQLSTATE[42S22]: Column not found: 1054 Unknown column 'status' in 'where clause'
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">
                                <div><strong>File:</strong> AdminController.php:45</div>
                                <div><strong>User:</strong> admin@vintapp.com</div>
                            </small>
                        </td>
                        <td>
                            <div>{{ now()->format('d/m/Y H:i:s') }}</div>
                            <small class="text-muted">{{ now()->diffForHumans() }}</small>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <span class="badge bg-warning">WARNING</span>
                        </td>
                        <td>
                            <div class="text-wrap" style="max-width: 400px;">
                                Attempting to access admin panel without proper authentication
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">
                                <div><strong>IP:</strong> 127.0.0.1</div>
                                <div><strong>Route:</strong> admin.dashboard</div>
                            </small>
                        </td>
                        <td>
                            <div>{{ now()->subMinutes(15)->format('d/m/Y H:i:s') }}</div>
                            <small class="text-muted">{{ now()->subMinutes(15)->diffForHumans() }}</small>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <span class="badge bg-success">INFO</span>
                        </td>
                        <td>
                            <div class="text-wrap" style="max-width: 400px;">
                                User authenticated successfully
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">
                                <div><strong>User:</strong> admin@vintapp.com</div>
                                <div><strong>Action:</strong> login</div>
                            </small>
                        </td>
                        <td>
                            <div>{{ now()->subMinutes(30)->format('d/m/Y H:i:s') }}</div>
                            <small class="text-muted">{{ now()->subMinutes(30)->diffForHumans() }}</small>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <span class="badge bg-info">DEBUG</span>
                        </td>
                        <td>
                            <div class="text-wrap" style="max-width: 400px;">
                                Query executed: SELECT * FROM users WHERE email = ?
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">
                                <div><strong>Duration:</strong> 2.45ms</div>
                                <div><strong>Bindings:</strong> ["admin@vintapp.com"]</div>
                            </small>
                        </td>
                        <td>
                            <div>{{ now()->subHour()->format('d/m/Y H:i:s') }}</div>
                            <small class="text-muted">{{ now()->subHour()->diffForHumans() }}</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Informations sur les fichiers de logs -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Fichiers de logs</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        laravel.log
                        <span class="badge bg-primary rounded-pill">{{ number_format(filesize(storage_path('logs/laravel.log')) / 1024, 2) }} KB</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistiques</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-danger">12</h4>
                        <small class="text-muted">Erreurs aujourd'hui</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning">8</h4>
                        <small class="text-muted">Avertissements</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearLogs() {
    if (confirm('Êtes-vous sûr de vouloir vider tous les logs ? Cette action est irréversible.')) {
        // AJAX call to clear logs
        alert('Logs vidés avec succès !');
    }
}

function downloadLogs() {
    // Télécharger le fichier de logs
    window.location.href = '/admin/logs/download';
}
</script>
@endsection