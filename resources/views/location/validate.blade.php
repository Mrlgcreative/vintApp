@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Validation de localisation</h1>

    @if(!empty($hint))
        <div class="mb-4 p-3 bg-yellow-100 border-l-4 border-yellow-500">{{ $hint }}</div>
    @endif

    @if(!empty($success))
        <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500">{{ $success }}</div>
    @endif

    <p class="mb-4">Pour continuer, autorisez la géolocalisation ou saisissez votre ville.</p>

    <div class="mb-6">
        <button id="btn-geo" class="px-4 py-2 bg-blue-600 text-white rounded">Autoriser la géolocalisation</button>
    </div>

    <div class="mb-6">
        <form id="manual-form" onsubmit="return submitManual()">
            <label for="city">Saisir la ville</label>
            <input id="city" name="city" class="block border rounded p-2 w-full" list="cities" />
            <datalist id="cities">
                @foreach($allowed as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </datalist>
            <div class="mt-3">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded">Valider ma ville</button>
            </div>
        </form>
    </div>

    <div id="messages"></div>
</div>

@push('scripts')
<script>
    function showMessage(msg, type='info'){
        const el = document.getElementById('messages');
        el.innerHTML = `<div class="p-3 mt-2 ${type==='error'?'bg-red-100 border-l-4 border-red-500':'bg-green-100 border-l-4 border-green-500'}">${msg}</div>`;
    }

    document.getElementById('btn-geo').addEventListener('click', function(){
        if(!navigator.geolocation){
            showMessage('Géolocalisation non supportée', 'error');
            return;
        }

        navigator.geolocation.getCurrentPosition(function(pos){
            fetch('/api/validate-location', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                body: JSON.stringify({ lat: pos.coords.latitude, lng: pos.coords.longitude })
            }).then(r=>r.json()).then(j=>{
                if(j.ok){
                    showMessage('Localisation enregistrée — redirection en cours');
                    setTimeout(()=>location.href='/',500);
                } else {
                    showMessage(j.message || 'Erreur', 'error');
                }
            }).catch(e=>{
                console.error(e);
                showMessage('Erreur lors de la validation', 'error');
            });
        }, function(err){
            console.warn(err);
            if(err.code===1){
                showMessage('Permission de localisation refusée. Vérifiez Réglages > Confidentialité > Service de localisation.', 'error');
            } else {
                showMessage('Impossible d\'obtenir la localisation. Essayez la saisie manuelle.', 'error');
            }
        }, { enableHighAccuracy: true, timeout: 10000 });
    });

    function submitManual(){
        const city = document.getElementById('city').value;
        if(!city) { showMessage('Veuillez saisir une ville', 'error'); return false; }

        fetch('/api/validate-location', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
            body: JSON.stringify({ city })
        }).then(r=>r.json()).then(j=>{
            if(j.ok){
                showMessage('Ville acceptée — redirection...');
                setTimeout(()=>location.href='/',500);
            } else {
                showMessage(j.message || 'Ville non autorisée', 'error');
            }
        }).catch(e=>{ showMessage('Erreur réseau', 'error'); });

        return false;
    }
</script>
@endpush

@endsection
