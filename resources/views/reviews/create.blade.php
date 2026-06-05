@extends('app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white dark:bg-gray-800 border-bottom-0">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-plus me-2"></i>Ajouter un avis</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('reviews.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="order_id" class="form-label">Commande</label>
                            <select name="order_id" id="order_id" class="form-select">
                                <option value="">-- Sélectionner --</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>#{{ $order->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="item_id" class="form-label">Article</label>
                            <select name="item_id" id="item_id" class="form-select">
                                <option value="">-- Sélectionner --</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reviewer_id" class="form-label">Auteur de l'avis</label>
                            <select name="reviewer_id" id="reviewer_id" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('reviewer_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="seller_id" class="form-label">Vendeur</label>
                            <select name="seller_id" id="seller_id" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('seller_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="rating" class="form-label">Note <span class="text-danger">*</span></label>
                            <select name="rating" id="rating" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }} étoile{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Commentaire</label>
                            <textarea name="comment" id="comment" class="form-control" rows="3" maxlength="1000">{{ old('comment') }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('reviews.index') }}" class="btn btn-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 