@php
    $cookiePolicyUrl = route('privacy');
@endphp

<div
    id="cookie-banner"
    class="hidden fixed inset-x-0 bottom-0 z-[100] p-4 sm:p-6"
    role="region"
    aria-label="Consentement aux cookies"
>
    <div class="mx-auto flex max-w-4xl flex-col gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-lg sm:flex-row sm:items-center dark:border-gray-700 dark:bg-gray-900">
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                Nous utilisons des cookies
            </p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Les cookies strictement nécessaires permettent au site de fonctionner : ils maintiennent
                ta session et ta connexion. Les cookies de mesure d'audience restent désactivés tant que
                tu ne les acceptes pas.
                <a href="{{ $cookiePolicyUrl }}" class="font-medium underline hover:text-gray-900 dark:hover:text-white">
                    En savoir plus
                </a>
            </p>
        </div>

        <div class="flex shrink-0 flex-col gap-2 sm:flex-row">
            <button
                type="button"
                data-cookie-choice="reject"
                class="inline-flex h-9 items-center justify-center rounded-md border border-gray-300 bg-white px-4 text-sm font-medium text-gray-900 transition-colors hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:hover:bg-gray-800"
            >
                Tout refuser
            </button>
            <button
                type="button"
                data-cookie-choice="accept"
                class="inline-flex h-9 items-center justify-center rounded-md bg-gray-900 px-4 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
            >
                Tout accepter
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        var STORAGE_KEY = 'vintapp-cookie-consent';
        var POLICY_VERSION = 1;

        function readConsent() {
            try {
                var raw = localStorage.getItem(STORAGE_KEY);

                if (!raw) {
                    return null;
                }

                var parsed = JSON.parse(raw);

                return parsed && parsed.v === POLICY_VERSION ? parsed : null;
            } catch (e) {
                return null;
            }
        }

        function saveConsent(choice) {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify({
                    v: POLICY_VERSION,
                    choice: choice,
                    ts: new Date().toISOString(),
                }));
            } catch (e) {
                /* navigation sans stockage : la banniere reapparaitra */
            }
        }

        function init() {
            var banner = document.getElementById('cookie-banner');

            if (!banner) {
                return;
            }

            function close() {
                banner.classList.add('hidden');
            }

            function open() {
                banner.classList.remove('hidden');
            }

            banner.addEventListener('click', function (event) {
                var button = event.target.closest('[data-cookie-choice]');

                if (!button) {
                    return;
                }

                saveConsent(button.getAttribute('data-cookie-choice'));
                close();
            });

            document.addEventListener('click', function (event) {
                if (event.target.closest('[data-cookie-settings]')) {
                    open();
                }
            });

            window.vintappCookies = { open: open, close: close, read: readConsent };

            if (readConsent() === null) {
                open();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
