<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item->name }} - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #4a4a4a;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: #1a1a1a;
            padding: 32px 28px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
        }
        .header p {
            margin: 6px 0 0;
            color: rgba(255, 255, 255, 0.65);
            font-size: 14px;
        }
        .content {
            padding: 32px 28px;
        }
        .content p {
            margin: 0 0 16px;
            font-size: 15px;
        }
        .card {
            background: #f5f5f5;
            padding: 16px 20px;
            border-radius: 8px;
            margin: 16px 0;
        }
        .card h3 {
            color: #1a1a1a;
            margin: 0 0 8px;
            font-size: 16px;
        }
        .card p {
            margin: 4px 0;
            font-size: 14px;
            color: #4a4a4a;
        }
        .btn {
            display: inline-block;
            background: #1a1a1a;
            color: #ffffff !important;
            text-decoration: none !important;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            margin: 16px 0;
        }
        .footer {
            padding: 24px 28px;
            text-align: center;
            border-top: 1px solid #e5e5e5;
        }
        .footer p {
            color: #9a9a9a;
            margin: 4px 0;
            font-size: 13px;
        }
        .footer a {
            color: #1a1a1a;
        }
        @media (max-width: 600px) {
            .container { margin: 12px; border-radius: 10px; }
            .header, .content, .footer { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $item->name }}</h1>
            <p>Modération de votre article</p>
        </div>
        <div class="content">
            @php
                $lines = match ($action) {
                    'approved' => [
                        'Bonne nouvelle ! Votre article a été approuvé et est maintenant visible sur la plateforme.',
                    ],
                    'rejected' => [
                        'Votre article a été rejeté par notre équipe de modération.',
                        $reason ? "Raison : {$reason}" : null,
                    ],
                    'blocked' => [
                        'Votre article a été bloqué par notre équipe de modération.',
                        "Il n'est plus visible sur la plateforme.",
                        $reason ? "Raison : {$reason}" : null,
                    ],
                    'suspended' => [
                        'Votre article a été suspendu temporairement.',
                        $reason ? "Raison : {$reason}" : null,
                        $days
                            ? 'Cette suspension prendra fin le ' . now()->addDays($days)->format('d/m/Y') . '.'
                            : 'La suspension est pour une durée indéterminée.',
                    ],
                    'unsuspended' => [
                        'Bonne nouvelle ! Votre article est de nouveau visible sur la plateforme.',
                    ],
                    default => ["Votre article a été mis à jour."],
                };
            @endphp

            @foreach (array_filter($lines) as $line)
                <p>{{ $line }}</p>
            @endforeach

            <div class="card">
                <h3>{{ $item->name }}</h3>
                <p><strong>Prix :</strong> {{ number_format((float) $item->price, 0, ',', ' ') }} {{ $item->currency ?? 'USD' }}</p>
                @if ($item->status)
                    <p><strong>Statut :</strong> {{ $item->status }}</p>
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ route('items.show', $item) }}" class="btn">Voir mon article</a>
            </div>
        </div>
        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>Email envoyé suite à une action de modération sur votre article.</p>
        </div>
    </div>
</body>
</html>
