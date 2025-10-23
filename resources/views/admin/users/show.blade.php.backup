@extends('layouts.admin')@extends('layouts.admin')



@section('title', 'Détails de l\'utilisateur')@section('title', 'Détails utilisateur - ' . $user->name)

@section('page-title', $user->name)@section('page-title', 'Détails de l\'utilisateur')



@section('page-actions')@section('page-actions')

<div class="d-flex gap-2"><div class="d-flex gap-2">

    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">

        <i class="fas fa-arrow-left me-2"></i>Retour à la liste        <i class="fas fa-arrow-left me-2"></i>Retour

    </a>    </a>

    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">    <div class="dropdown">

        <i class="fas fa-edit me-2"></i>Modifier        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">

    </a>            <i class="fas fa-cog me-2"></i>Actions

    <div class="dropdown">        </button>

        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">        <ul class="dropdown-menu">

            <i class="fas fa-ellipsis-v"></i>            @if($user->is_active ?? true)

        </button>                <li>

        <ul class="dropdown-menu">                    <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="d-inline">

            <li><a class="dropdown-item" href="#" onclick="toggleStatus()">                        @csrf

                <i class="fas fa-{{ $user->status === 'active' ? 'pause' : 'play' }} me-2"></i>                        @method('PATCH')

                {{ $user->status === 'active' ? 'Suspendre' : 'Activer' }}                        <input type="hidden" name="action" value="deactivate">

            </a></li>                        <button type="submit" class="dropdown-item text-warning" 

            <li><a class="dropdown-item" href="#" onclick="sendPasswordReset()">                                onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?')">

                <i class="fas fa-key me-2"></i>Réinitialiser le mot de passe                            <i class="fas fa-pause me-2"></i>Désactiver

            </a></li>                        </button>

            <li><a class="dropdown-item" href="#" onclick="sendWelcomeEmail()">                    </form>

                <i class="fas fa-envelope me-2"></i>Envoyer email de bienvenue                </li>

            </a></li>            @else

            <li><hr class="dropdown-divider"></li>                <li>

            <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete()">                    <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="d-inline">

                <i class="fas fa-trash me-2"></i>Supprimer                        @csrf

            </a></li>                        @method('PATCH')

        </ul>                        <input type="hidden" name="action" value="activate">

    </div>                        <button type="submit" class="dropdown-item text-success">

</div>                            <i class="fas fa-play me-2"></i>Activer

@endsection                        </button>

                    </form>

@section('content')                </li>

<div class="row">            @endif

    <!-- Informations principales -->            

    <div class="col-lg-8">            @if(!($user->is_suspended ?? false))

        <div class="card">                <li>

            <div class="card-body">                    <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="d-inline">

                <div class="row">                        @csrf

                    <div class="col-md-8">                        @method('PATCH')

                        <div class="d-flex align-items-center mb-3">                        <input type="hidden" name="action" value="suspend">

                            @if($user->avatar)                        <button type="submit" class="dropdown-item text-warning" 

                                <img src="{{ $user->avatar_url }}" class="rounded-circle me-3" width="80" height="80" alt="Avatar {{ $user->name }}">                                onclick="return confirm('Êtes-vous sûr de vouloir suspendre cet utilisateur ?')">

                            @else                            <i class="fas fa-ban me-2"></i>Suspendre

                                <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 80px;">                        </button>

                                    <i class="fas fa-user fa-2x text-muted"></i>                    </form>

                                </div>                </li>

                            @endif            @endif

                            <div>        </ul>

                                <h3 class="card-title mb-1">{{ $user->name }}</h3>    </div>

                                <p class="text-muted mb-1">{{ $user->email }}</p></div>

                                <div>@endsection

                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'suspended' ? 'warning' : 'danger') }}">

                                        {{ ucfirst($user->status ?? 'active') }}@section('content')

                                    </span><div class="row">

                                    @if($user->role)    <!-- Informations de base -->

                                        <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>    <div class="col-md-4">

                                    @endif        <div class="card">

                                    @if($user->email_verified_at)            <div class="card-body text-center">

                                        <span class="badge bg-info">Email vérifié</span>                @if($user->avatar)

                                    @endif                    <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="120" height="120" alt="Avatar">

                                    @if($user->is_seller)                @else

                                        <span class="badge bg-secondary">Vendeur</span>                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; font-size: 3rem;">

                                    @endif                        {{ $user->initial }}

                                </div>                    </div>

                            </div>                @endif

                        </div>                

                                        <h4>{{ $user->name }}</h4>

                        @if($user->bio)                <p class="text-muted">{{ $user->email }}</p>

                            <div class="mb-4">                

                                <h6>Biographie</h6>                <div class="d-flex justify-content-center gap-2 mb-3">

                                <p class="text-muted">{{ $user->bio }}</p>                    @foreach($user->roles as $role)

                            </div>                        <span class="badge bg-{{ $role->slug === 'admin' ? 'danger' : 'primary' }}">

                        @endif                            {{ $role->name }}

                                                </span>

                        <div class="row">                    @endforeach

                            @if($user->phone)                </div>

                                <div class="col-md-6 mb-3">                

                                    <h6>Téléphone</h6>                @if($user->isOnline())

                                    <p class="mb-0">                    <span class="badge bg-success">En ligne</span>

                                        <i class="fas fa-phone me-2"></i>                @else

                                        {{ $user->phone }}                    <span class="badge bg-secondary">Hors ligne</span>

                                    </p>                @endif

                                </div>                

                            @endif                @if($user->is_active ?? true)

                                                <span class="badge bg-success">Actif</span>

                            @if($user->date_of_birth)                @else

                                <div class="col-md-6 mb-3">                    <span class="badge bg-danger">Inactif</span>

                                    <h6>Date de naissance</h6>                @endif

                                    <p class="mb-0">                

                                        <i class="fas fa-birthday-cake me-2"></i>                @if($user->is_suspended ?? false)

                                        {{ $user->date_of_birth->format('d/m/Y') }}                     <span class="badge bg-warning">Suspendu</span>

                                        ({{ $user->date_of_birth->age }} ans)                @endif

                                    </p>            </div>

                                </div>        </div>

                            @endif        

                                    <!-- Informations personnelles -->

                            @if($user->address || $user->city || $user->country)        <div class="card mt-4">

                                <div class="col-md-12 mb-3">            <div class="card-header">

                                    <h6>Adresse</h6>                <h5 class="mb-0">Informations personnelles</h5>

                                    <p class="mb-0">            </div>

                                        <i class="fas fa-map-marker-alt me-2"></i>            <div class="card-body">

                                        @if($user->address){{ $user->address }}<br>@endif                <div class="row mb-2">

                                        @if($user->city || $user->postal_code)                    <div class="col-sm-4"><strong>ID:</strong></div>

                                            {{ $user->postal_code }} {{ $user->city }}<br>                    <div class="col-sm-8">{{ $user->id }}</div>

                                        @endif                </div>

                                        @if($user->country){{ $user->country }}@endif                

                                    </p>                <div class="row mb-2">

                                </div>                    <div class="col-sm-4"><strong>Téléphone:</strong></div>

                            @endif                    <div class="col-sm-8">{{ $user->phone ?? 'Non renseigné' }}</div>

                                            </div>

                            @if($user->language || $user->timezone)                

                                <div class="col-md-6 mb-3">                <div class="row mb-2">

                                    <h6>Préférences</h6>                    <div class="col-sm-4"><strong>Adresse:</strong></div>

                                    @if($user->language)                    <div class="col-sm-8">{{ $user->address ?? 'Non renseignée' }}</div>

                                        <p class="mb-1">                </div>

                                            <i class="fas fa-globe me-2"></i>                

                                            {{ strtoupper($user->language) }}                <div class="row mb-2">

                                        </p>                    <div class="col-sm-4"><strong>Localisation:</strong></div>

                                    @endif                    <div class="col-sm-8">{{ $user->location ?? 'Non renseignée' }}</div>

                                    @if($user->timezone)                </div>

                                        <p class="mb-0">                

                                            <i class="fas fa-clock me-2"></i>                <div class="row mb-2">

                                            {{ $user->timezone }}                    <div class="col-sm-4"><strong>Email vérifié:</strong></div>

                                        </p>                    <div class="col-sm-8">

                                    @endif                        @if($user->email_verified_at)

                                </div>                            <span class="text-success">Oui</span>

                            @endif                            <small class="text-muted d-block">{{ $user->email_verified_at->format('d/m/Y H:i') }}</small>

                        </div>                        @else

                    </div>                            <span class="text-danger">Non</span>

                                            @endif

                    <div class="col-md-4">                    </div>

                        <!-- Informations de connexion -->                </div>

                        <div class="card border">                

                            <div class="card-body">                <div class="row mb-2">

                                <h6 class="card-title">Dernière activité</h6>                    <div class="col-sm-4"><strong>Inscription:</strong></div>

                                @if($user->last_login_at)                    <div class="col-sm-8">{{ $user->created_at->format('d/m/Y H:i') }}</div>

                                    <p class="mb-2">                </div>

                                        <strong>Dernière connexion:</strong><br>                

                                        {{ $user->last_login_at->format('d/m/Y H:i') }}                <div class="row mb-2">

                                    </p>                    <div class="col-sm-4"><strong>Dernière connexion:</strong></div>

                                @endif                    <div class="col-sm-8">

                                <p class="mb-2">                        @if($user->last_seen)

                                    <strong>Membre depuis:</strong><br>                            {{ $user->last_seen->format('d/m/Y H:i') }}

                                    {{ $user->created_at->format('d/m/Y') }}                            <small class="text-muted d-block">{{ $user->last_seen->diffForHumans() }}</small>

                                </p>                        @else

                                @if($user->last_seen_at)                            <span class="text-muted">Jamais connecté</span>

                                    <p class="mb-0">                        @endif

                                        <strong>Vu pour la dernière fois:</strong><br>                    </div>

                                        {{ $user->last_seen_at->diffForHumans() }}                </div>

                                    </p>            </div>

                                @endif        </div>

                            </div>    </div>

                        </div>    

                    </div>    <!-- Statistiques et activité -->

                </div>    <div class="col-md-8">

            </div>        <!-- Statistiques -->

        </div>        <div class="row">

                    <div class="col-md-3 mb-4">

        <!-- Articles de l'utilisateur -->                <div class="card text-center">

        @if($user->items && $user->items->count() > 0)                    <div class="card-body">

            <div class="card mt-4">                        <i class="fas fa-box fa-2x text-primary mb-2"></i>

                <div class="card-header d-flex justify-content-between align-items-center">                        <h4>{{ $stats['total_items'] }}</h4>

                    <h5 class="card-title mb-0">Articles mis en vente</h5>                        <small class="text-muted">Articles</small>

                    <a href="{{ route('admin.items.index', ['user' => $user->id]) }}" class="btn btn-sm btn-outline-primary">                    </div>

                        Voir tous les articles                </div>

                    </a>            </div>

                </div>            

                <div class="card-body">            <div class="col-md-3 mb-4">

                    <div class="table-responsive">                <div class="card text-center">

                        <table class="table table-hover">                    <div class="card-body">

                            <thead>                        <i class="fas fa-shopping-cart fa-2x text-success mb-2"></i>

                                <tr>                        <h4>{{ $stats['total_orders'] }}</h4>

                                    <th>Article</th>                        <small class="text-muted">Commandes</small>

                                    <th>Catégorie</th>                    </div>

                                    <th>Prix</th>                </div>

                                    <th>Statut</th>            </div>

                                    <th>Créé le</th>            

                                    <th>Actions</th>            <div class="col-md-3 mb-4">

                                </tr>                <div class="card text-center">

                            </thead>                    <div class="card-body">

                            <tbody>                        <i class="fas fa-dollar-sign fa-2x text-warning mb-2"></i>

                                @foreach($user->items->take(5) as $item)                        <h4>${{ number_format($stats['total_revenue'], 2) }}</h4>

                                    <tr>                        <small class="text-muted">Revenus</small>

                                        <td>                    </div>

                                            <div class="d-flex align-items-center">                </div>

                                                @if($item->images && $item->images->first())            </div>

                                                    <img src="{{ $item->images->first()->url }}"             

                                                         class="rounded me-2"             <div class="col-md-3 mb-4">

                                                         width="40"                 <div class="card text-center">

                                                         height="40"                     <div class="card-body">

                                                         alt="{{ $item->title }}">                        <i class="fas fa-star fa-2x text-info mb-2"></i>

                                                @endif                        <h4>{{ number_format($stats['average_rating'], 1) }}</h4>

                                                <div>                        <small class="text-muted">Note moyenne</small>

                                                    <div class="fw-bold">{{ $item->title }}</div>                    </div>

                                                    <small class="text-muted">{{ Str::limit($item->description, 30) }}</small>                </div>

                                                </div>            </div>

                                            </div>        </div>

                                        </td>        

                                        <td>{{ $item->category->name ?? 'Sans catégorie' }}</td>        <!-- Wallets -->

                                        <td>{{ number_format($item->price, 2) }} €</td>        <div class="card mb-4">

                                        <td>            <div class="card-header">

                                            <span class="badge bg-{{ $item->status === 'active' ? 'success' : 'secondary' }}">                <h5 class="mb-0">Portefeuilles</h5>

                                                {{ ucfirst($item->status) }}            </div>

                                            </span>            <div class="card-body">

                                        </td>                <div class="row">

                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>                    @if($user->usdWallet())

                                        <td>                        <div class="col-md-6">

                                            <a href="{{ route('admin.items.show', $item) }}" class="btn btn-sm btn-outline-info">                            <div class="border rounded p-3">

                                                <i class="fas fa-eye"></i>                                <div class="d-flex justify-content-between align-items-center">

                                            </a>                                    <div>

                                        </td>                                        <h6 class="mb-0">Wallet USD</h6>

                                    </tr>                                        <small class="text-muted">Devise principale</small>

                                @endforeach                                    </div>

                            </tbody>                                    <div class="text-end">

                        </table>                                        <h5 class="mb-0">${{ number_format($user->usdWallet()->balance, 2) }}</h5>

                    </div>                                        <small class="text-muted">USD</small>

                </div>                                    </div>

            </div>                                </div>

        @endif                            </div>

                                </div>

        <!-- Commandes récentes -->                    @endif

        @if($user->orders && $user->orders->count() > 0)                    

            <div class="card mt-4">                    @if($user->cdfWallet())

                <div class="card-header d-flex justify-content-between align-items-center">                        <div class="col-md-6">

                    <h5 class="card-title mb-0">Commandes récentes</h5>                            <div class="border rounded p-3">

                    <a href="{{ route('admin.orders.index', ['user' => $user->id]) }}" class="btn btn-sm btn-outline-primary">                                <div class="d-flex justify-content-between align-items-center">

                        Voir toutes les commandes                                    <div>

                    </a>                                        <h6 class="mb-0">Wallet CDF</h6>

                </div>                                        <small class="text-muted">Devise locale</small>

                <div class="card-body">                                    </div>

                    <div class="table-responsive">                                    <div class="text-end">

                        <table class="table table-hover">                                        <h5 class="mb-0">{{ number_format($user->cdfWallet()->balance, 0) }}</h5>

                            <thead>                                        <small class="text-muted">CDF</small>

                                <tr>                                    </div>

                                    <th>Commande</th>                                </div>

                                    <th>Total</th>                            </div>

                                    <th>Statut</th>                        </div>

                                    <th>Date</th>                    @endif

                                    <th>Actions</th>                </div>

                                </tr>            </div>

                            </thead>        </div>

                            <tbody>        

                                @foreach($user->orders->take(5) as $order)        <!-- Dernières transactions -->

                                    <tr>        <div class="card">

                                        <td>#{{ $order->id }}</td>            <div class="card-header d-flex justify-content-between align-items-center">

                                        <td>{{ number_format($order->total, 2) }} €</td>                <h5 class="mb-0">Dernières transactions</h5>

                                        <td>                <a href="{{ route('admin.transactions.index', ['search' => $user->email]) }}" class="btn btn-sm btn-outline-primary">

                                            <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">                    Voir toutes

                                                {{ ucfirst($order->status) }}                </a>

                                            </span>            </div>

                                        </td>            <div class="card-body">

                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>                @if($recentTransactions->count() > 0)

                                        <td>                    <div class="table-responsive">

                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-info">                        <table class="table table-sm">

                                                <i class="fas fa-eye"></i>                            <thead>

                                            </a>                                <tr>

                                        </td>                                    <th>Date</th>

                                    </tr>                                    <th>Type</th>

                                @endforeach                                    <th>Montant</th>

                            </tbody>                                    <th>Statut</th>

                        </table>                                    <th>Description</th>

                    </div>                                </tr>

                </div>                            </thead>

            </div>                            <tbody>

        @endif                                @foreach($recentTransactions as $transaction)

    </div>                                    <tr>

                                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>

    <!-- Sidebar avec statistiques -->                                        <td>

    <div class="col-lg-4">                                            <span class="badge bg-secondary">{{ $transaction->type }}</span>

        <div class="card">                                        </td>

            <div class="card-header">                                        <td class="font-monospace">

                <h5 class="card-title mb-0">Statistiques</h5>                                            {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}

            </div>                                        </td>

            <div class="card-body">                                        <td>

                <div class="row text-center">                                            @if($transaction->status === 'completed')

                    <div class="col-6 mb-3">                                                <span class="badge bg-success">Terminé</span>

                        <div class="border-end">                                            @elseif($transaction->status === 'pending')

                            <h3 class="text-primary">{{ $user->items_count ?? 0 }}</h3>                                                <span class="badge bg-warning">En attente</span>

                            <small class="text-muted">Articles</small>                                            @else

                        </div>                                                <span class="badge bg-danger">Échoué</span>

                    </div>                                            @endif

                    <div class="col-6 mb-3">                                        </td>

                        <h3 class="text-success">{{ $user->orders_count ?? 0 }}</h3>                                        <td>{{ $transaction->description }}</td>

                        <small class="text-muted">Commandes</small>                                    </tr>

                    </div>                                @endforeach

                    <div class="col-6">                            </tbody>

                        <div class="border-end">                        </table>

                            <h3 class="text-info">{{ $user->reviews_count ?? 0 }}</h3>                    </div>

                            <small class="text-muted">Avis donnés</small>                @else

                        </div>                    <div class="text-center text-muted">

                    </div>                        <i class="fas fa-receipt fa-3x mb-3"></i>

                    <div class="col-6">                        <p>Aucune transaction récente</p>

                        <h3 class="text-warning">{{ number_format($user->rating ?? 0, 1) }}/5</h3>                    </div>

                        <small class="text-muted">Note moyenne</small>                @endif

                    </div>            </div>

                </div>        </div>

            </div>    </div>

        </div></div>

        @endsection
        <!-- Portefeuille -->
        @if($user->wallet)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Portefeuille</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="text-success">{{ number_format($user->wallet->balance, 2) }} €</h2>
                        <p class="text-muted">Solde disponible</p>
                    </div>
                    <div class="d-grid">
                        <a href="{{ route('admin.wallet.show', $user->wallet) }}" class="btn btn-outline-primary">
                            Voir les transactions
                        </a>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Informations système -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Informations système</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>ID:</strong>
                        <span>{{ $user->id }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>Inscrit le:</strong>
                        <span>{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>Modifié le:</strong>
                        <span>{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                
                @if($user->email_verified_at)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <strong>Email vérifié le:</strong>
                            <span>{{ $user->email_verified_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                @endif
                
                @if($user->last_login_at)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <strong>Dernière connexion:</strong>
                            <span>{{ $user->last_login_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Notifications et préférences -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Préférences</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <i class="fas fa-{{ $user->notifications_enabled ?? 1 ? 'bell' : 'bell-slash' }} me-2"></i>
                    Notifications {{ $user->notifications_enabled ?? 1 ? 'activées' : 'désactivées' }}
                </div>
                <div class="mb-2">
                    <i class="fas fa-{{ $user->marketing_emails ? 'envelope' : 'envelope-open-text' }} me-2"></i>
                    Emails marketing {{ $user->marketing_emails ? 'acceptés' : 'refusés' }}
                </div>
                @if($user->language)
                    <div class="mb-2">
                        <i class="fas fa-language me-2"></i>
                        Langue: {{ strtoupper($user->language) }}
                    </div>
                @endif
                @if($user->timezone)
                    <div class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Fuseau: {{ $user->timezone }}
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Modifier l'utilisateur
                    </a>
                    
                    <button class="btn btn-outline-secondary" onclick="sendPasswordReset()">
                        <i class="fas fa-key me-2"></i>Réinitialiser le mot de passe
                    </button>
                    
                    <button class="btn btn-outline-info" onclick="sendMessage()">
                        <i class="fas fa-envelope me-2"></i>Envoyer un message
                    </button>
                    
                    <button class="btn btn-outline-warning" onclick="exportUserData()">
                        <i class="fas fa-download me-2"></i>Exporter les données
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ $user->name }}</strong> ?</p>
                @if($user->items_count > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cet utilisateur possède {{ $user->items_count }} article(s).
                    </div>
                @endif
                @if($user->orders_count > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cet utilisateur a {{ $user->orders_count }} commande(s) associée(s).
                    </div>
                @endif
                <p class="text-danger small">Cette action est irréversible et supprimera toutes les données associées.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function toggleStatus() {
    fetch(`/admin/users/{{ $user->id }}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification du statut');
        }
    });
}

function sendPasswordReset() {
    if (confirm('Envoyer un email de réinitialisation du mot de passe à {{ $user->name }} ?')) {
        fetch(`/admin/users/{{ $user->id }}/send-password-reset`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Email de réinitialisation envoyé avec succès');
            } else {
                alert('Erreur lors de l\'envoi de l\'email');
            }
        });
    }
}

function sendWelcomeEmail() {
    if (confirm('Envoyer un email de bienvenue à {{ $user->name }} ?')) {
        fetch(`/admin/users/{{ $user->id }}/send-welcome`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Email de bienvenue envoyé avec succès');
            } else {
                alert('Erreur lors de l\'envoi de l\'email');
            }
        });
    }
}

function sendMessage() {
    const message = prompt('Message à envoyer à {{ $user->name }} :');
    if (message) {
        fetch(`/admin/users/{{ $user->id }}/send-message`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Message envoyé avec succès');
            } else {
                alert('Erreur lors de l\'envoi du message');
            }
        });
    }
}

function exportUserData() {
    window.location.href = `/admin/users/{{ $user->id }}/export`;
}
</script>
@endpush