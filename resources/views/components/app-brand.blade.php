{{-- Composant logo/nom de l'application --}}
<div class="app-brand d-flex align-items-center {{ $class ?? '' }}">
    @if($showLogo ?? true)
        <img src="{{ asset($appLogo) }}" 
             alt="{{ $appName }}" 
             class="app-logo me-2" 
             style="height: {{ $logoHeight ?? '40px' }}; max-width: {{ $logoWidth ?? '120px' }}; object-fit: contain;">
    @endif
    
    @if($showName ?? true)
        <span class="app-name fw-bold {{ $nameClass ?? 'text-dark' }}" 
              style="font-size: {{ $nameSize ?? '1.5rem' }};">
            {{ $appName }}
        </span>
    @endif
</div>