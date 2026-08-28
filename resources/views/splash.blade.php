<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="theme-color" content="#f8fafc" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0c1016" media="(prefers-color-scheme: dark)">
<title>{{ config('app.name', 'VintApp') }}</title>
<link rel="preconnect" href="{{ route('home') }}">
<script>
(function () {
    var stored = null;
    try { stored = localStorage.getItem('theme'); } catch (e) {}
    var isDark = stored === 'dark' ||
        ((!stored || stored === 'auto') &&
            window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.add(isDark ? 'dark' : 'light');
})();
</script>
@vite(['resources/css/app.css'])
</head>
<body class="relative flex h-screen items-center justify-center overflow-hidden bg-slate-50 font-sans dark:bg-dark-900">

<div id="splashContainer" role="status" aria-label="Chargement de {{ config('app.name', 'VintApp') }}"
     class="relative z-10 flex flex-col items-center justify-center">

    <!-- Logo V -->
    <div class="relative mb-8 flex h-44 w-44 items-center justify-center sm:h-48 sm:w-48">
        <div class="absolute h-[150px] w-[150px] rounded-full -z-10 animate-splash-circle [background:radial-gradient(circle,rgba(139,92,246,0.12),transparent_70%)] sm:h-[170px] sm:w-[170px] motion-reduce:animate-none"></div>

        <svg class="animate-splash-cart h-[150px] w-[150px] opacity-0 will-change-[transform,opacity] motion-reduce:animate-none motion-reduce:opacity-100 motion-reduce:transform-none" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect x="0" y="0" width="512" height="512" rx="96" fill="#8B5CF6" />
            <path d="M 128 144 L 202 144 L 256 300 L 310 144 L 384 144 L 300 380 L 212 380 Z" fill="#FFFFFF" />
        </svg>
    </div>

    <!-- Loader -->
    <div class="mt-8 animate-fade-in motion-reduce:animate-none">
        <div class="flex w-full flex-col items-center justify-center gap-4">
            <div class="flex h-20 w-20 animate-spin items-center justify-center rounded-full border-4 border-transparent border-t-blue-400 motion-reduce:animate-none">
                <div class="flex h-16 w-16 animate-spin items-center justify-center rounded-full border-4 border-transparent border-t-red-400 motion-reduce:animate-none"></div>
            </div>
        </div>
    </div>
</div>

<p class="animate-splash-hint fixed bottom-6 left-0 right-0 text-center text-xs font-medium tracking-[0.05em] text-gray-400 opacity-0 motion-reduce:animate-none motion-reduce:opacity-100 sm:bottom-8 dark:text-gray-500">Appuyez pour continuer</p>

<script>
(function() {
  var container = document.getElementById('splashContainer');
  var redirecting = false;
  var homeUrl = @json(route('home'));

  function goHome() {
    if (redirecting) return;
    redirecting = true;
    container.classList.add('animate-splash-outro');
    setTimeout(function() {
      window.location.href = homeUrl;
    }, 600);
  }

  // Auto-redirect après la fin des animations + progress bar
  var timer = setTimeout(goHome, 4200);

  // Skip au clic/touch
  function skip() {
    clearTimeout(timer);
    goHome();
  }

  document.addEventListener('click', skip);
  document.addEventListener('touchstart', skip, { passive: true });
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ' ' || e.key === 'Escape') skip();
  });
})();
</script>

</body>
</html>
