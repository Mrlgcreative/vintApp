@extends('app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                    <h2>Désinscription confirmée</h2>
                    <p class="lead">Vous avez été désinscrit(e) de notre newsletter.</p>
                    <p class="text-muted">
                        Nous sommes désolés de vous voir partir. Vous ne recevrez plus d'emails de notre part.
                    </p>
                    
                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Retour à l'accueil
                        </a>
                        <a href="{{ route('newsletter.preferences', $subscriber->unsubscribe_token) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-cog me-2"></i>Gérer mes préférences
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
