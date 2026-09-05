<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="theme-color" content="#7c3aed">
<title>Télécharger VintApp | {{ config('app.name', 'VintApp') }}</title>
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
<body class="min-h-screen bg-slate-50 font-sans dark:bg-dark-900">

<!-- Hero -->
<section class="relative overflow-hidden bg-slate-950">
    <div class="absolute inset-0 [background:radial-gradient(circle_at_70%_20%,rgba(139,92,246,0.25),transparent_55%),radial-gradient(circle_at_20%_0%,rgba(139,92,246,0.15),transparent_40%)]"></div>
    <div class="pointer-events-none absolute -top-40 right-1/4 h-[36rem] w-[36rem] rounded-full bg-vinted-primary/25 blur-[140px]"></div>
    <div class="pointer-events-none absolute -bottom-52 -left-20 h-[30rem] w-[30rem] rounded-full bg-vinted-accent/20 blur-[120px]"></div>

    <div class="relative mx-auto max-w-6xl px-6 py-20 lg:py-28 text-center">
        <!-- Logo V -->
        <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-vinted-500 to-vinted-primary shadow-2xl shadow-vinted-primary/40 ring-1 ring-white/20">
            <svg class="h-14 w-14" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M 128 144 L 202 144 L 256 300 L 310 144 L 384 144 L 300 380 L 212 380 Z" fill="#FFFFFF" />
            </svg>
        </div>

        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-white/70 backdrop-blur-md">
            <span class="h-1.5 w-1.5 rounded-full bg-vinted-primary animate-pulse"></span>
            Application Android
        </span>

        <h1 class="mt-6 font-display text-4xl font-bold text-white sm:text-5xl lg:text-6xl leading-[1.1] tracking-tight">
            VintApp
            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-white via-white to-vinted-300">
                sur ton téléphone
            </span>
        </h1>

        <p class="mx-auto mt-6 max-w-2xl text-base text-white/70 sm:text-lg leading-relaxed">
            Achète, vends et découvre des pièces vintage authentiques où que tu sois.
            Télécharge l'application Android et emporte VintApp partout.
        </p>

        <!-- Bouton téléchargement -->
        <div class="mt-10 flex flex-col items-center gap-4">
            <a href="{{ route('download.apk') }}"
               class="group inline-flex items-center gap-3 rounded-full bg-gradient-to-r from-vinted-500 to-vinted-primary px-8 py-4 text-base font-semibold text-white shadow-xl shadow-vinted-primary/40 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-vinted-primary/50">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 16.5l-5-5h3V9h4v4.5h3l-5 5z"/>
                </svg>
                <span>Télécharger l'APK</span>
                <span class="text-sm font-normal text-white/70">(7,9 Mo)</span>
                <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v9m0 0l-4-4m4 4l4-4"/>
                </svg>
            </a>
            <p class="text-xs text-white/50">Version 1.0.0 · Compatible Android 7.0 et plus · Gratuit</p>
        </div>
    </div>
</section>

<!-- Caractéristiques -->
<section class="mx-auto max-w-6xl px-6 py-16 lg:py-20">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 sm:gap-6">
        <div class="group rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-xl hover:border-vinted-primary/30 dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-vinted-accent transition-transform duration-300 group-hover:scale-110 dark:bg-vinted-primary/20">
                <svg class="h-6 w-6 text-vinted-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="mb-1.5 font-semibold text-gray-900 dark:text-white">Application native</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Expérience fluide et rapide au quotidien, conçue pour Android.</p>
        </div>

        <div class="group rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-xl hover:border-vinted-primary/30 dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-vinted-accent transition-transform duration-300 group-hover:scale-110 dark:bg-vinted-primary/20">
                <svg class="h-6 w-6 text-vinted-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h3 class="mb-1.5 font-semibold text-gray-900 dark:text-white">Paiements sécurisés</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Achats protégés et paiement mobile intégré (KPay, mobile money).</p>
        </div>

        <div class="group rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-xl hover:border-vinted-primary/30 dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-vinted-accent transition-transform duration-300 group-hover:scale-110 dark:bg-vinted-primary/20">
                <svg class="h-6 w-6 text-vinted-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="mb-1.5 font-semibold text-gray-900 dark:text-white">100% Authentique</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Chaque pièce est vérifiée par nos experts avant mise en vente.</p>
        </div>
    </div>
</section>

<!-- Instructions d'installation Android -->
<section class="mx-auto max-w-4xl px-6 pb-16 lg:pb-20">
    <h2 class="mb-8 text-center font-display text-2xl font-bold text-gray-900 sm:text-3xl dark:text-white">
        Comment installer l'application ?
    </h2>

    <div class="space-y-6">
        <div class="flex gap-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-vinted-primary font-bold text-white">1</div>
            <div class="mt-1">
                <h3 class="font-semibold text-gray-900 dark:text-white">Autoriser les sources inconnues</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Va dans <span class="font-medium text-gray-700 dark:text-gray-300">Paramètres → Sécurité</span> de ton téléphone et active « Sources inconnues » (ou « Installer des applications inconnues »).</p>
            </div>
        </div>

        <div class="flex gap-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-vinted-primary font-bold text-white">2</div>
            <div class="mt-1">
                <h3 class="font-semibold text-gray-900 dark:text-white">Télécharger l'APK</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Appuie sur le bouton « Télécharger l'APK » ci-dessus et attends la fin du téléchargement (7,9 Mo).</p>
            </div>
        </div>

        <div class="flex gap-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-vinted-primary font-bold text-white">3</div>
            <div class="mt-1">
                <h3 class="font-semibold text-gray-900 dark:text-white">Installer et se connecter</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ouvre le fichier téléchargé, appuie sur « Installer », puis ouvre VintApp et connecte-toi avec ton compte.</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="border-t border-gray-200 bg-white py-8 dark:border-gray-700 dark:bg-gray-800">
    <div class="mx-auto flex max-w-6xl flex-col items-center gap-3 px-6 text-center sm:flex-row sm:justify-between sm:text-left">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 rounded-md bg-gradient-to-br from-vinted-500 to-vinted-primary p-0.5" viewBox="0 0 512 512"><path d="M 128 144 L 202 144 L 256 300 L 310 144 L 384 144 L 300 380 L 212 380 Z" fill="#fff"/></svg>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ config('app.name', 'VintApp') }}</span>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500">© {{ date('Y') }} {{ config('app.name', 'VintApp') }} · Tous droits réservés</p>
        <div class="flex items-center gap-4 text-xs text-gray-400">
            <a href="{{ url('/') }}" class="hover:text-vinted-primary">Site web</a>
            <a href="{{ url('/legal/terms') }}" class="hover:text-vinted-primary">CGU</a>
            <a href="{{ url('/legal/privacy') }}" class="hover:text-vinted-primary">Confidentialité</a>
        </div>
    </div>
</footer>

</body>
</html>