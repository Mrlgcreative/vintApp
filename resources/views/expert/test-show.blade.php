@extends('layouts.admin')

@section('title', 'Test Vérification #' . $check->id)

@section('content')
<div class="p-6">
    <h1>Test Vérification #{{ $check->id }}</h1>
    
    <div class="space-y-4">
        <div>
            <strong>Item Name:</strong> {{ $check->item->name ?? 'Produit sans nom' }}
        </div>
        
        <div>
            <strong>Prix:</strong> {{ number_format($check->item->price, 0, ',', ' ') }} {{ $check->item->currency }}
        </div>
        
        <div>
            <strong>Catégorie:</strong> {{ $check->item->category->name ?? 'Non spécifiée' }}
        </div>
        
        <div>
            <strong>Marque:</strong> {{ $check->item->brand->name ?? 'Non spécifiée' }}
        </div>
        
        <div>
            <strong>Condition:</strong> {{ ucfirst($check->item->condition ?? 'Non spécifié') }}
        </div>
        
        @if($check->item->description)
        <div>
            <strong>Description:</strong> {{ $check->item->description }}
        </div>
        @endif
        
        <div>
            <strong>Images:</strong>
            @forelse($check->item->images ?? [] as $image)
                <span>{{ $image }}</span>{{ !$loop->last ? ', ' : '' }}
            @empty
                Aucune image
            @endforelse
        </div>
    </div>
</div>
@endsection