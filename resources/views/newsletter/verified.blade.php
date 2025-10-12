@extends('app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                    <h2>Email vérifié !</h2>
                    <p class="lead">Merci d'avoir confirmé votre adresse email.</p>
                    <p class="text-muted">
                        Votre inscription à notre newsletter est maintenant active. 
                        Vous recevrez nos prochaines actualités et offres.
                    </p>
                    
                    <div class="mt-4">
                        <a href="{{ route('items.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Découvrir nos articles
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
