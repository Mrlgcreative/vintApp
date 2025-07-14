@extends('app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-star me-2"></i>Détail de l'avis</h4>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Commande</dt>
                        <dd class="col-7">#{{ $review->order_id ?? '-' }}</dd>
                        <dt class="col-5">Article</dt>
                        <dd class="col-7">{{ $review->item->name ?? '-' }}</dd>
                        <dt class="col-5">Auteur</dt>
                        <dd class="col-7">{{ $review->reviewer->name ?? '-' }}</dd>
                        <dt class="col-5">Vendeur</dt>
                        <dd class="col-7">{{ $review->seller->name ?? '-' }}</dd>
                        <dt class="col-5">Note</dt>
                        <dd class="col-7"><span class="badge bg-warning text-dark">{{ $review->rating }} <i class="fas fa-star text-warning"></i></span></dd>
                        <dt class="col-5">Commentaire</dt>
                        <dd class="col-7">{{ $review->comment ?? '-' }}</dd>
                        <dt class="col-5">Statut</dt>
                        <dd class="col-7">{{ $review->status ?? '-' }}</dd>
                        <dt class="col-5">Créé le</dt>
                        <dd class="col-7">{{ $review->created_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('reviews.edit', $review) }}" class="btn btn-outline-primary me-2"><i class="fas fa-edit"></i> Modifier</a>
                        <a href="{{ route('reviews.index') }}" class="btn btn-secondary">Retour</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 