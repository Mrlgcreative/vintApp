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
            --primary-600:#7c3aed;
            --primary-700:#6d28d9;
            --accent-600:#db2777;
            --indigo-600:#4f46e5;
            --ink:#111827;
            --muted:#6b7280;
            --line:#e5e7eb;
        }

        body{
            font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
            background:linear-gradient(135deg,var(--indigo-600) 0%,var(--primary-600) 55%,var(--accent-600) 100%);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:24px;
            color:var(--ink);
            -webkit-font-smoothing:antialiased;
        }

        .card{
            width:100%;
            max-width:600px;
            background:#fff;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 24px 48px -12px rgba(17,24,39,.35);
        }

        .card__head{
            position:relative;
            background:linear-gradient(135deg,var(--indigo-600) 0%,var(--primary-600) 55%,var(--accent-600) 100%);
            padding:48px 32px;
            text-align:center;
            color:#fff;
        }

        .card__head::after{
            content:"";
            position:absolute;
            width:320px;
            height:320px;
            border-radius:50%;
            background:rgba(255,255,255,.08);
            top:-160px;
            right:-100px;
        }

        .gear{
            width:64px;
            height:64px;
            margin:0 auto 20px;
            display:block;
            color:#fff;
            animation:spin 6s linear infinite;
        }

        @keyframes spin{
            to{transform:rotate(360deg)}
        }

        .card__title{
            font-size:26px;
            line-height:1.2;
            font-weight:800;
            letter-spacing:-.01em;
        }

        .card__body{
            padding:32px;
            text-align:center;
        }

        .message{
            font-size:17px;
            line-height:1.6;
            color:#374151;
        }

        .badge{
            display:inline-flex;
            align-items:center;
            gap:8px;
            margin-top:20px;
            padding:10px 16px;
            border-radius:12px;
            background:#f5f3ff;
            border:1px solid #ddd6fe;
            color:var(--primary-700);
            font-size:14px;
            font-weight:600;
        }

        .apology{
            margin-top:20px;
            font-size:13px;
            line-height:1.6;
            color:var(--muted);
        }

        .card__foot{
            padding:24px 32px 28px;
            border-top:1px solid var(--line);
            text-align:center;
        }

        .foot-label{
            font-size:13px;
            color:var(--muted);
            margin-bottom:14px;
        }

        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding:12px 22px;
            border-radius:12px;
            background:var(--primary-600);
            color:#fff;
            font-size:15px;
            font-weight:600;
            text-decoration:none;
            transition:background .2s ease,transform .2s ease;
        }

        .btn:hover{
            background:var(--primary-700);
            transform:translateY(-1px);
        }

        .btn:focus-visible{
            outline:2px solid var(--primary-600);
            outline-offset:3px;
        }

        .copyright{
            margin-top:20px;
            font-size:12px;
            color:#9ca3af;
        }

        @media (prefers-reduced-motion: reduce){
            .gear{animation:none}
            .btn{transition:none}
        }

        @media (max-width:480px){
            .card__head{padding:36px 20px}
            .card__body,.card__foot{padding:24px 20px}
            .card__title{font-size:22px}
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="card__head">
            <svg class="gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
            <h1 class="card__title">Site en maintenance</h1>
        </div>

        <div class="card__body" role="status" aria-live="polite">
            <p class="message">{{ $message ?? 'Nous effectuons actuellement des travaux de maintenance sur le site.' }}</p>

            @if(!empty($estimated_time))
                <p class="badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 2"></path>
                    </svg>
                    Temps estimé : {{ $estimated_time }}
                </p>
            @endif

            <p class="apology">Nous nous excusons pour la gêne occasionnée et travaillons à rétablir le service dans les plus brefs délais.</p>
        </div>

        <div class="card__foot">
            <p class="foot-label">Besoin d'aide ? Contactez-nous :</p>
            <a class="btn" href="mailto:{{ $contact_email ?? 'support@vintapp.com' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    <path d="m2 7 10 6 10-6"></path>
                </svg>
                Nous contacter
            </a>
            <p class="copyright">&copy; {{ date('Y') }} {{ config('app.name', 'VintApp') }}. Tous droits réservés.</p>
        </div>
    </main>

    <script>
        setTimeout(function () {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>
