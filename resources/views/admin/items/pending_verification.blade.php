@extends('app')

@section('title', 'Items en attente de vérification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Items en attente de vérification</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">{{ session('success') }}</div>
    @endif

    @if($items->isEmpty())
        <div class="p-6 bg-gray-50 rounded">Aucun item en attente.</div>
    @else
    <div class="space-y-4">
        @foreach($items as $item)
        <div class="bg-white rounded shadow p-4 flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('storage/' . ($item->images[0] ?? 'placeholder.png')) }}" class="w-20 h-20 object-cover rounded" alt="{{ $item->name }}">
                <div>
                    <h3 class="font-semibold">{{ $item->name }} <span class="text-sm text-gray-500">#{{ $item->id }}</span></h3>
                    <p class="text-sm text-gray-600">{{ Str::limit($item->description, 120) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Signalements: {{ isset($item->specifications['image_verification']) ? count($item->specifications['image_verification']) : 0 }}</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('items.show', $item) }}" class="px-4 py-2 bg-gray-100 rounded">Voir</a>
                <form action="{{ route('admin.items.approve', $item) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Approuver</button>
                </form>
                <form action="{{ route('admin.items.reject', $item) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="reason" value="Rejeté par l'équipe de modération">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Rejeter</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $items->links() }}</div>
    @endif
</div>
@endsection
