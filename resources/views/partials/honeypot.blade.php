{{--
    Piège à robots (honeypot) — voir App\Http\Middleware\ProtectAgainstBots.

    Les champs ci-dessous sont hors écran pour un humain, mais parfaitement
    présents au DOM : un robot qui remplit « tous les champs du formulaire »
    les renseigne et se fait éjecter.

    Masquage en inline CSS (et non en classe Tailwind) car la CSP impose
    style-src 'self' 'unsafe-inline' : un style inline passe dans les deux
    environnements (dev et production), une classe purifiée par à coup sûr
    ne le ferait pas.

    Ne jamais utiliser old() sur ces champs : le timestamp doit être régénéré
    à chaque rendu, sinon un utilisateur qui corrige une erreur de validation
    repart avec un timestamp périmé et se fait rejeter en boucle.
--}}
@foreach ((array) config('honeypot.fields') as $honeypotField)
    <div aria-hidden="true" style="position:absolute!important;left:-9999px!important;top:auto!important;width:1px!important;height:1px!important;overflow:hidden!important;">
        <label for="{{ $honeypotField }}">{{ ucfirst(str_replace('_', ' ', $honeypotField)) }}</label>
        <input type="text"
               id="{{ $honeypotField }}"
               name="{{ $honeypotField }}"
               value=""
               tabindex="-1"
               autocomplete="off">
    </div>
@endforeach
<input type="hidden" name="{{ config('honeypot.timestamp_field') }}" value="{{ time() }}">
