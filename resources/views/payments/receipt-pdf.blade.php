<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reçu - {{ $transaction->receipt_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a202c; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #7c3aed; }
        .header h1 { font-size: 20px; color: #7c3aed; margin: 0 0 3px; }
        .header p { font-size: 10px; color: #6b7280; margin: 0; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; color: #059669; background: #d1fae5; margin-bottom: 12px; }
        .amount { text-align: center; margin-bottom: 15px; }
        .amount .label { font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; }
        .amount .value { font-size: 26px; font-weight: 800; margin: 2px 0; }
        .amount .currency { font-size: 14px; color: #6b7280; }
        .divider { border-top: 1px dashed #d1d5db; margin: 12px 0; }
        .details { width: 100%; }
        .details td { padding: 4px 0; }
        .details .label { color: #6b7280; }
        .details .value { text-align: right; font-weight: 600; }
        .signature { background: #f9fafb; border-radius: 6px; padding: 8px 10px; margin-top: 10px; font-size: 9px; }
        .signature .sig-label { font-size: 8px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        .signature .sig-value { font-family: 'DejaVu Sans Mono', monospace; color: #6b7280; word-break: break-all; margin-top: 3px; font-size: 8px; }
        .footer { text-align: center; margin-top: 15px; padding-top: 10px; border-top: 1px dashed #d1d5db; font-size: 8px; color: #9ca3af; }
        .mono { font-family: 'DejaVu Sans Mono', monospace; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'VintApp') }}</h1>
        <p>Reçu de paiement électronique</p>
    </div>

    <div style="text-align:center;">
        <span class="badge">Paiement confirmé</span>
    </div>

    <div class="amount">
        <div class="label">Montant payé</div>
        <div class="value">{{ number_format($transaction->amount, 2) }} <span class="currency">{{ $transaction->currency }}</span></div>
    </div>

    <div class="divider"></div>

    <table class="details">
        <tr><td class="label">N° Reçu</td><td class="value mono">{{ $transaction->receipt_number }}</td></tr>
        <tr><td class="label">Transaction</td><td class="value mono" style="font-size:10px;">{{ $transaction->transaction_id ?? $transaction->id }}</td></tr>
        @if($transaction->provider)
        <tr><td class="label">Opérateur</td><td class="value">{{ ucfirst(str_replace('_', ' ', $transaction->provider)) }}</td></tr>
        @endif
        @if($transaction->phone)
        <tr><td class="label">Téléphone</td><td class="value mono">+243 {{ $transaction->phone }}</td></tr>
        @endif
        <tr><td class="label">Date</td><td class="value">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td></tr>
        <tr><td class="label">Devise</td><td class="value">{{ $transaction->currency }}</td></tr>
    </table>

    <div class="divider"></div>

    <div class="signature">
        <div class="sig-label">Signature numérique</div>
        <div class="sig-value">{{ $transaction->receipt_signature }}</div>
    </div>

    <div class="footer">
        <p style="margin:0 0 2px;">Reçu généré le {{ $transaction->receipt_generated_at?->format('d/m/Y à H:i:s') ?? $transaction->updated_at->format('d/m/Y à H:i:s') }}</p>
        <p style="margin:0;">Signature valide — Document authentique</p>
    </div>
</body>
</html>