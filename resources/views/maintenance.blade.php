<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ config('app.name', 'VintApp') }} — Maintenance</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

        :root{
            color-scheme:light;
            --page:#fafafa;
            --background:#ffffff;
            --foreground:#0a0a0a;
            --card:#ffffff;
            --card-foreground:#0a0a0a;
            --primary:#18181b;
            --primary-hover:#27272a;
            --primary-foreground:#fafafa;
            --secondary:#f4f4f5;
            --secondary-foreground:#18181b;
            --muted:#f4f4f5;
            --muted-foreground:#71717a;
            --accent:#f4f4f5;
            --accent-foreground:#18181b;
            --border:#e4e4e7;
            --ring:#0a0a0a;
            --radius:0.625rem;
        }

        .dark{
            color-scheme:dark;
            --page:#0a0a0a;
            --background:#18181b;
            --foreground:#fafafa;
            --card:#18181b;
            --card-foreground:#fafafa;
            --primary:#fafafa;
            --primary-hover:#e4e4e7;
            --primary-foreground:#0a0a0a;
            --secondary:#27272a;
            --secondary-foreground:#fafafa;
            --muted:#27272a;
            --muted-foreground:#a1a1aa;
            --accent:#27272a;
            --accent-foreground:#fafafa;
            --border:#27272a;
            --ring:#fafafa;
        }

        body{
            font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
            background-color:var(--page);
            color:var(--foreground);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:24px;
            -webkit-font-smoothing:antialiased;
        }

        .card{
            width:100%;
            max-width:448px;
            background-color:var(--card);
            color:var(--card-foreground);
            border:1px solid var(--border);
            border-radius:var(--radius);
            box-shadow:0 1px 2px 0 rgb(0 0 0 / 0.05);
            overflow:hidden;
        }

        .card__header{
            padding:24px;
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:16px;
            border-bottom:1px solid var(--border);
        }

        .header-row{
            display:flex;
            align-items:flex-start;
            gap:12px;
        }

        .header-text{
            display:flex;
            flex-direction:column;
            gap:4px;
        }

        .icon-badge{
            width:40px;
            height:40px;
            flex-shrink:0;
            border-radius:calc(var(--radius) - 2px);
            background-color:var(--muted);
            color:var(--muted-foreground);
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .card__title{
            font-size:1.125rem;
            line-height:1.4;
            font-weight:600;
            letter-spacing:-0.01em;
        }

        .card__description{
            font-size:0.875rem;
            line-height:1.6;
            color:var(--muted-foreground);
        }

        .card__content{
            padding:24px;
            display:flex;
            flex-direction:column;
            gap:16px;
        }

        .badge{
            display:inline-flex;
            align-items:center;
            gap:6px;
            width:fit-content;
            padding:4px 10px;
            border-radius:calc(var(--radius) - 4px);
            background-color:var(--secondary);
            color:var(--secondary-foreground);
            font-size:0.75rem;
            line-height:1.4;
            font-weight:500;
        }

        .card__footer{
            padding:24px;
            display:flex;
            flex-direction:column;
            gap:16px;
            border-top:1px solid var(--border);
        }

        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            height:36px;
            padding:0 16px;
            border-radius:calc(var(--radius) - 4px);
            background-color:var(--primary);
            color:var(--primary-foreground);
            font-size:0.875rem;
            font-weight:500;
            line-height:1;
            text-decoration:none;
            white-space:nowrap;
            transition:background-color .15s ease;
        }

        .btn:hover{
            background-color:var(--primary-hover);
        }

        .btn:focus-visible,.toggle:focus-visible{
            outline:none;
            box-shadow:0 0 0 2px var(--card),0 0 0 4px var(--ring);
        }

        .toggle{
            flex-shrink:0;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:36px;
            height:36px;
            padding:0;
            border:1px solid var(--border);
            border-radius:calc(var(--radius) - 4px);
            background-color:transparent;
            color:var(--foreground);
            cursor:pointer;
            transition:background-color .15s ease;
        }

        .toggle:hover{
            background-color:var(--accent);
            color:var(--accent-foreground);
        }

        .icon-sun{display:none}
        .dark .icon-sun{display:block}
        .dark .icon-moon{display:none}

        .copyright{
            font-size:0.75rem;
            line-height:1.4;
            color:var(--muted-foreground);
            text-align:center;
        }

        @media (max-width:480px){
            .card__header,.card__content,.card__footer{padding:20px}
        }
    </style>
    <script>
        (function () {
            var root = document.documentElement;
            var stored = null;

            try {
                stored = localStorage.getItem('vintapp-theme');
            } catch (e) {
                stored = null;
            }

            var prefersDark = window.matchMedia
                && window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (stored === 'dark' || (!stored && prefersDark)) {
                root.classList.add('dark');
            }
        })();
    </script>
</head>
<body>
    <main class="card">
        <div class="card__header">
            <div class="header-row">
                <div class="icon-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </div>
                <div class="header-text">
                    <h1 class="card__title">Site en maintenance</h1>
                    <p class="card__description">Le service est momentanément indisponible.</p>
                </div>
            </div>
            <button type="button" class="toggle" id="theme-toggle" aria-label="Basculer le thème clair et sombre">
                <svg class="icon-moon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                </svg>
                <svg class="icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M6.3 17.7l-1.4 1.4M19.1 4.9l-1.4 1.4"></path>
                </svg>
            </button>
        </div>

        <div class="card__content" role="status" aria-live="polite">
            <p class="card__description">{{ $message ?? 'Nous effectuons actuellement des travaux de maintenance sur le site.' }}</p>

            @if(!empty($estimated_time))
                <span class="badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 2"></path>
                    </svg>
                    Temps estimé : {{ $estimated_time }}
                </span>
            @endif
        </div>

        <div class="card__footer">
            <a class="btn" href="mailto:{{ $contact_email ?? 'support@vintapp.com' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    <path d="m2 7 10 6 10-6"></path>
                </svg>
                Nous contacter
            </a>
            <p class="copyright">&copy; {{ date('Y') }} {{ config('app.name', 'VintApp') }}</p>
        </div>
    </main>

    <script>
        (function () {
            var root = document.documentElement;
            var toggle = document.getElementById('theme-toggle');

            toggle.addEventListener('click', function () {
                var dark = root.classList.toggle('dark');

                try {
                    localStorage.setItem('vintapp-theme', dark ? 'dark' : 'light');
                } catch (e) {
                    /* stockage indisponible : le theme ne sera pas memorise */
                }
            });

            setTimeout(function () {
                window.location.reload();
            }, 30000);
        })();
    </script>
</body>
</html>
