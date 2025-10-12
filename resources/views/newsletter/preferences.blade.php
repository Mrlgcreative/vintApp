@extends('app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-cog me-2"></i>Gérer mes préférences de newsletter
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <p class="lead">Choisissez les types d'emails que vous souhaitez recevoir :</p>
                    
                    <form method="POST" action="{{ route('newsletter.preferences.update', $subscriber->unsubscribe_token) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-check mb-3 p-3 border rounded">
                            <input class="form-check-input" type="checkbox" name="receive_new_items" id="receive_new_items" 
                                   {{ $subscriber->receive_new_items ? 'checked' : '' }}>
                            <label class="form-check-label" for="receive_new_items">
                                <strong>📦 Nouveaux articles</strong>
                                <p class="text-muted mb-0 ms-4">Recevez une notification quand un nouvel article est ajouté</p>
                            </label>
                        </div>

                        <div class="form-check mb-3 p-3 border rounded">
                            <input class="form-check-input" type="checkbox" name="receive_promotions" id="receive_promotions"
                                   {{ $subscriber->receive_promotions ? 'checked' : '' }}>
                            <label class="form-check-label" for="receive_promotions">
                                <strong>🎁 Promotions et offres spéciales</strong>
                                <p class="text-muted mb-0 ms-4">Recevez nos meilleures offres et promotions exclusives</p>
                            </label>
                        </div>

                        <div class="form-check mb-3 p-3 border rounded">
                            <input class="form-check-input" type="checkbox" name="receive_newsletters" id="receive_newsletters"
                                   {{ $subscriber->receive_newsletters ? 'checked' : '' }}>
                            <label class="form-check-label" for="receive_newsletters">
                                <strong>📰 Newsletter générale</strong>
                                <p class="text-muted mb-0 ms-4">Recevez notre newsletter avec actualités et conseils</p>
                            </label>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Email :</strong> {{ $subscriber->email }}
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer mes préférences
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted">Vous ne souhaitez plus recevoir d'emails ?</p>
                        <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}" 
                           class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt me-2"></i>Se désabonner complètement
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Vos statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h3 class="text-primary">{{ $subscriber->emails_sent }}</h3>
                            <p class="text-muted">Emails reçus</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-success">{{ $subscriber->emails_opened }}</h3>
                            <p class="text-muted">Emails ouverts</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-info">{{ $subscriber->emails_clicked }}</h3>
                            <p class="text-muted">Liens cliqués</p>
                        </div>
                    </div>
                    @if($subscriber->last_email_sent_at)
                        <p class="text-center text-muted mt-3 mb-0">
                            Dernier email : {{ $subscriber->last_email_sent_at->diffForHumans() }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
