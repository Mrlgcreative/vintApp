@extends('app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Récapitulatif de la commande</h2>
    @if(empty($cart))
        <div class="alert alert-info">Votre panier est vide.</div>
    @else
    <div class="table-responsive mb-4">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $item)
                    <tr>
                        <td>
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" width="50" class="me-2 rounded">
                            @endif
                            {{ $item['name'] }}
                        </td>
                        <td>{{ $item['price'] }} {{ $item['currency'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td><b>{{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency'] }}</b></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total :</th>
                    <th>{{ number_format($total, 2) }} {{ $item['currency'] ?? '' }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="d-flex justify-content-end">
        <a href="{{ route('cart.pay') }}" class="btn btn-success btn-lg">
            <i class="fas fa-mobile-alt me-2"></i>Payer par Mobile Money
        </a>
    </div>
    @endif
</div>
@endsection 