@extends('app')

@section('content')
<div class="container py-4">
    <!-- En-tête de la catégorie -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            @if($category->icon)
                                <i class="{{ $category->icon }} text-primary fs-1 me-3"></i>
                            @else
                                <i class="fas fa-folder text-muted fs-1 me-3"></i>
                            @endif
                            <div>
                                <h1 class="h2 mb-1">{{ $category->name }}</h1>
                                @if($category->description)
                                    <p class="text-muted mb-2">{{ $category->description }}</p>
                                @endif
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="fas fa-box me-1"></i>
                                        {{ $category->items_count ?? 0 }} article(s)
                                    </small>
                                    @if($category->parent)
                                        <small class="text-muted">
                                            <i class="fas fa-level-up-alt me-1"></i>
                                            Sous-catégorie de 
                                            <a href="{{ route('categories.show', $category->parent) }}" class="text-decoration-none">
                                                {{ $category->parent->name }}
                                            </a>
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i> Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('categories.edit', $category) }}">
                                        <i class="fas fa-edit me-2"></i> Modifier
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('items.index', ['category' => $category->id]) }}">
                                        <i class="fas fa-list me-2"></i> Voir tous les articles
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="dropdown-item text-danger"
                                                onclick="return confirm('Supprimer cette catégorie ? Tous les articles associés seront également supprimés.')">
                                            <i class="fas fa-trash me-2"></i> Supprimer
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sous-catégories -->
    @if(isset($subcategories) && $subcategories->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i> Sous-catégories</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($subcategories as $subcategory)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-center">
                                    @if($subcategory->icon)
                                        <i class="{{ $subcategory->icon }} text-primary fs-3 mb-2"></i>
                                    @else
                                        <i class="fas fa-folder text-muted fs-3 mb-2"></i>
                                    @endif
                                    <h6 class="card-title">{{ $subcategory->name }}</h6>
                                    <p class="card-text small text-muted">{{ Str::limit($subcategory->description, 60) }}</p>
                                    <span class="badge bg-info">{{ $subcategory->items_count ?? 0 }} articles</span>
                                    <div class="mt-2">
                                        <a href="{{ route('categories.show', $subcategory) }}" class="btn btn-sm btn-outline-primary">
                                            Voir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Articles de cette catégorie -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i> Articles de cette catégorie</h5>
                    <a href="{{ route('items.create', ['category' => $category->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Ajouter un article
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($items) && $items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Prix</th>
                                    <th>État</th>
                                    <th>Statut</th>
                                    <th>Vendeur</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td>
                                        @if($item->images)
                                            @php 
                                                // Vérifier si $item->images est déjà un array ou une string JSON
                                                $images = is_array($item->images) ? $item->images : json_decode($item->images, true);
                                            @endphp
                                            @if($images && is_array($images) && count($images) > 0)
                                                <img src="{{ asset('storage/' . $images[0]) }}" 
                                                     alt="{{ $item->name }}" 
                                                     style="width:50px;height:50px;object-fit:cover;" 
                                                     class="rounded">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                     style="width:50px;height:50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                 style="width:50px;height:50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->name }}</div>
                                        <small class="text-muted">{{ Str::limit($item->description, 40) }}</small>
                                    </td>
                                    <td class="fw-bold">{{ $item->formatted_price ?? $item->price . ' FC' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->condition_color ?? 'secondary' }}">
                                            {{ ucfirst($item->condition) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $item->status_color ?? 'secondary' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->user->name ?? 'Inconnu' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('items.show', $item) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Voir l'article">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if(method_exists($items, 'links'))
                    <div class="card-footer bg-white">
                        {{ $items->links() }}
                    </div>
                    @endif
                    
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fs-1 text-muted mb-3"></i>
                        <h6 class="text-muted">Aucun article dans cette catégorie</h6>
                        <a href="{{ route('items.create', ['category' => $category->id]) }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-plus me-1"></i> Ajouter le premier article
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton retour -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour aux catégories
            </a>
        </div>
    </div>
</div>
@endsection