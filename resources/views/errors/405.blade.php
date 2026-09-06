<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Erreur 405 — Méthode non autorisée — {{ config('app.name', 'VintApp') }}</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;background:#f9fafb;color:#1f2937;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .card{background:#ffffff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.06);padding:40px 32px;width:100%;max-width:420px;text-align:center}
        .badge{display:inline-flex;align-items:center;gap:8px;border:1px solid #e5e7eb;border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600;color:#6b7280;background:#fff;margin-bottom:24px}
        .icon{width:64px;height:64px;margin:0 auto 20px;border-radius:50%;background:#f5f3ff;display:flex;align-items:center;justify-content:center}
        .icon svg{width:32px;height:32px;stroke:#7c3aed}
        h1{font-size:20px;font-weight:700;letter-spacing:-.01em;color:#111827;margin-bottom:8px}
        p.sub{font-size:14px;color:#6b7280;margin-bottom:24px}
        .msg{background:#f5f3ff;border:1px solid #ede9fe;border-radius:12px;padding:16px;font-size:14px;color:#6d28d9;text-align:left;margin-bottom:24px;display:flex;gap:10px;align-items:flex-start}
        .msg svg{width:16px;height:16px;flex-shrink:0;margin-top:1px}
        .btn{display:block;width:100%;height:40px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:10px;transition:background .15s}
        .btn.primary{background:#7c3aed;color:#fff}
        .btn.primary:hover{background:#6d28d9}
        .btn.secondary{border:1px solid #e5e7eb;color:#374151;background:#fff}
        .btn.secondary:hover{background:#f9fafb}
        .btn.ghost{background:#f3f4f6;color:#6b7280}
        .btn.ghost:hover{background:#e5e7eb}
        .foot{margin-top:20px;font-size:12px;color:#9ca3af}
        .foot a{color:#7c3aed;text-decoration:none}
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">
            Erreur 405
            <span style="color:#e5e7eb">·</span>
            Méthode non autorisée
        </span>

        <div class="icon">
            <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>

        <h1>Oops ! Une erreur est survenue</h1>
        <p class="sub">La méthode demandée n'est pas autorisée pour cette ressource.</p>

        <div class="msg">
            <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Cette action doit être effectuée depuis le formulaire de paiement. Revenez à la page précédente pour poursuivre votre commande.</span>
        </div>

        <a href="javascript:history.back()" class="btn primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour
        </a>
        <a href="{{ route('cart.index') }}" class="btn secondary">Voir mon panier</a>
        <a href="{{ route('dashboard') }}" class="btn ghost">Retour au tableau de bord</a>

        <p class="foot">Besoin d'aide ? <a href="{{ route('support.index') }}">Contactez le support</a> — disponible 24/7</p>
    </div>
</body>
</html>