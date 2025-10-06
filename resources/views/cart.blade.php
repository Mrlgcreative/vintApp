@extends('app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Mon panier</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(empty($cart))
        <div class="alert alert-info">Votre panier est vide.</div>
    @else
    <form method="POST" action="{{ route('cart.clear') }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i> Vider le panier</button>
    </form>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($cart as $item)
                    @php $total += $item['price'] * $item['quantity']; @endphp
                    <tr>
                        <td>
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" width="50" class="me-2 rounded">
                            @endif
                            <div>
                                {{ $item['name'] }}
                                @if(isset($item['has_discount']) && $item['has_discount'])
                                    <br><small class="badge bg-success">
                                        <i class="fas fa-tag me-1"></i>
                                        Réduction {{ $item['discount_percentage'] }}%
                                    </small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if(isset($item['has_discount']) && $item['has_discount'])
                                <div>
                                    <span class="text-decoration-line-through text-muted small">
                                        {{ $item['original_price'] }} {{ $item['currency'] }}
                                    </span>
                                    <br>
                                    <span class="text-success fw-bold">
                                        {{ $item['price'] }} {{ $item['currency'] }}
                                    </span>
                                </div>
                            @else
                                {{ $item['price'] }} {{ $item['currency'] }}
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('cart.update', $item['id']) }}" class="d-inline">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" style="width:60px;">
                                <button type="submit" class="btn btn-outline-primary btn-sm ms-1"><i class="fas fa-sync"></i></button>
                            </form>
                        </td>
                        <td><b>{{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency'] }}</b></td>
                        <td>
                            <form method="POST" action="{{ route('cart.remove', $item['id']) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-times"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total :</th>
                    <th colspan="2">{{ number_format($total, 2) }} {{ $item['currency'] ?? '' }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="d-flex justify-content-end">
        <a href="{{ route('cart.checkout') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-credit-card me-2"></i>Passer à la caisse
        </a>
    </div>
    @endif
</div>
@endsection 