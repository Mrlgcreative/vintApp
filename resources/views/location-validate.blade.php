<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vérification de localisation — VintApp</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: Figtree, ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-violet-50/80 via-zinc-50 to-zinc-100 text-zinc-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10 sm:px-6">
        <div class="w-full max-w-md">
            <p class="text-center text-xs font-semibold uppercase tracking-[0.2em] text-violet-600/90 mb-3">VintApp</p>

            <div class="rounded-2xl border border-zinc-200/80 bg-white/95 shadow-xl shadow-violet-900/5 backdrop-blur-sm overflow-hidden">
                <div class="px-6 pt-8 pb-6 text-center border-b border-zinc-100 bg-gradient-to-br from-white to-violet-50/40">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-600 text-white shadow-lg shadow-violet-600/25">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Confirmation de zone</h1>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                        Votre connexion est analysée automatiquement. Si besoin, précisez votre position ci-dessous — une seule option suffit.
                    </p>
                </div>

                <div class="px-6 py-5 space-y-4">
                    @if(!empty($hint))
                        <div class="rounded-xl border border-amber-200/80 bg-amber-50/90 px-4 py-3 text-sm text-amber-950">
                            <span class="font-medium text-amber-900">Astuce — </span>{{ $hint }}
                        </div>
                    @endif

                    @if(!empty($success))
                        <div class="rounded-xl border border-emerald-200/80 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-900">
                            {{ $success }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="rounded-xl border border-red-200/80 bg-red-50/90 px-4 py-3 text-sm text-red-900" role="alert">
                            {{ $errors->first('location') ?: $errors->first() }}
                        </div>
                    @endif

                    <div class="rounded-xl border border-zinc-200 bg-zinc-50/60 p-4">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <h2 class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Zones desservies</h2>
                            <span class="text-xs tabular-nums text-zinc-400">
                                @if($allowed->isEmpty())
                                    —
                                @else
                                    {{ $allowed->count() }} {{ $allowed->count() > 1 ? 'villes' : 'ville' }}
                                @endif
                            </span>
                        </div>
                        <div class="max-h-28 overflow-y-auto pr-1 flex flex-wrap gap-1.5">
                            @forelse($allowed as $cityName)
                                <span class="inline-flex items-center rounded-lg bg-white px-2.5 py-1 text-xs font-medium text-zinc-700 ring-1 ring-zinc-200/80 shadow-sm">{{ $cityName }}</span>
                            @empty
                                <span class="text-xs text-zinc-500">Aucune ville configurée pour le moment.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="button" id="requestBtn"
                                onclick="requestLocation()"
                                class="group relative w-full flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-3.5 text-sm font-semibold text-white shadow-md shadow-violet-600/20 transition hover:bg-violet-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span id="requestBtnLabel">Utiliser la position de l’appareil</span>
                        </button>

                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-zinc-200"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white px-3 text-xs font-medium uppercase tracking-wider text-zinc-400">ou</span>
                            </div>
                        </div>

                        <form id="manual-form" onsubmit="return submitManual()" class="space-y-3">
                            <div>
                                <label for="city" class="block text-sm font-medium text-zinc-700 mb-1.5">Saisir une ville desservie</label>
                                <input id="city" name="city" type="text" list="cities" autocomplete="off"
                                       class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 placeholder:text-zinc-400 shadow-sm transition focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                       placeholder="Ex. Kinshasa, Lubumbashi…"/>
                                <datalist id="cities">
                                    @foreach($allowed as $cityName)
                                        <option value="{{ $cityName }}">{{ $cityName }}</option>
                                    @endforeach
                                </datalist>
                            </div>
                            <button type="submit"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm font-semibold text-zinc-800 shadow-sm transition hover:bg-zinc-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2">
                                Valider la ville
                            </button>
                        </form>
                    </div>

                    <div id="messages" class="space-y-2 min-h-[2.5rem]" role="status" aria-live="polite" aria-atomic="true"></div>

                    <p class="text-center text-xs leading-relaxed text-zinc-500 pt-1">
                        Localisation utilisée uniquement pour vérifier l’éligibilité aux zones VintApp.
                    </p>
                </div>

                <div class="px-6 py-4 bg-zinc-50/80 border-t border-zinc-100 flex items-center justify-center gap-4 text-sm">
                    <a href="{{ route('home') }}" class="font-medium text-violet-700 hover:text-violet-800 transition">Accueil</a>
                    <span class="text-zinc-300" aria-hidden="true">|</span>
                    <a href="{{ route('location.unauthorized') }}" class="font-medium text-zinc-500 hover:text-zinc-800 transition">Zones non couvertes</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const BTN_GEO_DEFAULT = 'Utiliser la position de l’appareil';
        const BTN_GEO_WAIT = 'Localisation…';
        const BTN_GEO_RETRY = 'Réessayer';

        function showMessage(msg, type = 'info') {
            const el = document.getElementById('messages');
            const div = document.createElement('div');
            const styles = {
                error: 'border-red-200/90 bg-red-50 text-red-900',
                info: 'border-violet-200/90 bg-violet-50 text-violet-950',
                success: 'border-emerald-200/90 bg-emerald-50 text-emerald-900',
            };
            const cls = styles[type] || styles.info;
            div.className = 'rounded-xl border px-4 py-3 text-sm ' + cls;
            div.textContent = msg;
            el.appendChild(div);
            if (type !== 'error') {
                setTimeout(() => { if (div.parentNode) div.remove(); }, 6000);
            }
        }

        function setGeoButtonState(disabled, label) {
            const btn = document.getElementById('requestBtn');
            const lbl = document.getElementById('requestBtnLabel');
            btn.disabled = !!disabled;
            if (label) lbl.textContent = label;
        }

        function requestLocation() {
            const btn = document.getElementById('requestBtn');
            setGeoButtonState(true, BTN_GEO_WAIT);

            if (!navigator.geolocation) {
                showMessage('La géolocalisation n’est pas disponible sur ce navigateur.', 'error');
                setGeoButtonState(false, BTN_GEO_DEFAULT);
                return;
            }

            showMessage('Demande d’accès à la position…', 'info');

            navigator.geolocation.getCurrentPosition(function (pos) {
                const lat = pos.coords.latitude;
                const lon = pos.coords.longitude;

                fetch('{{ route("location.validate.submit") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ lat, lng: lon }),
                })
                    .then((r) => r.json())
                    .then((j) => {
                        if (j.ok) {
                            showMessage('C’est bon — redirection…', 'success');
                            setTimeout(() => (location.href = '{{ route("home") }}'), 900);
                        } else if (j.redirect) {
                            window.location.href = j.redirect;
                        } else {
                            showMessage(j.message || 'Impossible de valider la position.', 'error');
                            setGeoButtonState(false, BTN_GEO_RETRY);
                        }
                    })
                    .catch(() => {
                        showMessage('Erreur réseau. Réessayez.', 'error');
                        setGeoButtonState(false, BTN_GEO_RETRY);
                    });
            }, function (err) {
                let message = 'Erreur de géolocalisation.';
                switch (err.code) {
                    case 1:
                        message = 'Permission refusée. Autorisez la position dans les paramètres du navigateur, ou saisissez votre ville.';
                        break;
                    case 2:
                        message = 'Position indisponible. Utilisez la saisie manuelle.';
                        break;
                    case 3:
                        message = 'Délai dépassé. Réessayez ou saisissez votre ville.';
                        break;
                }
                showMessage(message, 'error');
                setGeoButtonState(false, BTN_GEO_RETRY);
            }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
        }

        function submitManual() {
            const city = document.getElementById('city').value.trim();
            if (!city) {
                showMessage('Indiquez une ville desservie.', 'error');
                return false;
            }

            fetch('{{ route("location.validate.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ city }),
            })
                .then((r) => r.json())
                .then((j) => {
                    if (j.ok) {
                        showMessage('Ville enregistrée — redirection…', 'success');
                        setTimeout(() => (location.href = '{{ route("home") }}'), 900);
                    } else if (j.redirect) {
                        window.location.href = j.redirect;
                    } else {
                        showMessage(j.message || 'Ville non reconnue dans les zones desservies.', 'error');
                    }
                })
                .catch(() => showMessage('Erreur réseau. Réessayez.', 'error'));

            return false;
        }
    </script>
</body>
</html>
