@extends('layouts.admin')

@section('title', 'Paramètres Système - Test')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Test Paramètres Système</h4>
                </div>
                <div class="card-body">
                    <p>Page de test pour les paramètres.</p>
                    
                    <p>Routes disponibles :</p>
                    <ul>
                        <li>Index: <a href="{{ route('admin.settings.index') }}">{{ route('admin.settings.index') }}</a></li>
                        <li>Dashboard: <a href="{{ route('admin.dashboard') }}">{{ route('admin.dashboard') }}</a></li>
                    </ul>

                    @if(isset($settings))
                        <p>Settings trouvés : {{ $settings->count() }}</p>
                    @else
                        <p>Aucun settings trouvé</p>
                    @endif

                    @if(isset($categories))
                        <p>Catégories : {{ $categories->count() }}</p>
                    @else
                        <p>Aucune catégorie trouvée</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection