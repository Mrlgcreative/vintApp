@extends('app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fas fa-star me-2"></i> Avis</h2>
        <a href="{{ route('reviews.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Ajouter un avis
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Commande</th>
                            <th>Article</th>
                            <th>Auteur</th>
                            <th>Vendeur</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>#{{ $review->order_id ?? '-' }}</td>
                                <td>{{ $review->item->name ?? '-' }}</td>
                                <td>{{ $review->reviewer->name ?? '-' }}</td>
                                <td>{{ $review->seller->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $review->rating }} <i class="fas fa-star text-warning"></i></span>
                                </td>
                                <td>{{ Str::limit($review->comment, 40) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('reviews.edit', $review) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Supprimer cet avis ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Aucun avis trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">
        {{ $reviews->links() }}
    </div>
</div>
@endsection 