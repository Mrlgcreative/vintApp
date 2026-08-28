<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte monitoring - {{ config('app.name') }}</title>
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
            padding: 28px;
            text-align: center;
        }
        .header.critical { background: #b91c1c; }
        .header.warning { background: #d97706; }
        .header.info { background: #4338ca; }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
        }
        .header p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }
        .body {
            padding: 28px;
        }
        .body h2 {
            margin: 0 0 8px;
            color: #18181b;
            font-size: 18px;
        }
        .body p {
            margin: 0 0 16px;
            color: #4a4a4a;
            font-size: 14px;
        }
        .meta {
            background: #f8f8f8;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            padding: 14px 16px;
            margin: 16px 0;
            font-size: 13px;
            color: #4a4a4a;
        }
        .meta strong {
            color: #18181b;
        }
        .button {
            display: inline-block;
            margin-top: 8px;
            padding: 12px 24px;
            background: #18181b;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
        }
        .footer {
            padding: 20px 28px;
            text-align: center;
            color: #999999;
            font-size: 12px;
            border-top: 1px solid #eaeaea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header {{ $anomaly['severity'] ?? 'info' }}">
            <h1>Alerte Monitoring</h1>
            <p>{{ strtoupper($anomaly['severity'] ?? 'info') }} : {{ $anomaly['label'] ?? 'Anomalie détectée' }}</p>
        </div>

        <div class="body">
            <h2>{{ $anomaly['label'] ?? 'Anomalie détectée' }}</h2>
            <p>{{ $anomaly['message'] ?? 'Une anomalie a été détectée sur la plateforme.' }}</p>

            <div class="meta">
                <p style="margin:0 0 6px"><strong>Type :</strong> {{ $anomaly['type'] ?? '-' }}</p>
                <p style="margin:0 0 6px"><strong>Sévérité :</strong> {{ strtoupper($anomaly['severity'] ?? '-') }}</p>
                <p style="margin:0"><strong>Première détection :</strong> {{ \Carbon\Carbon::parse($anomaly['first_seen'] ?? now())->format('d/m/Y H:i:s') }}</p>
            </div>

            <a class="button" href="{{ route('admin.monitoring.index') }}">Voir le dashboard monitoring</a>
        </div>

        <div class="footer">
            Cet email a été envoyé automatiquement par le système de monitoring de {{ config('app.name') }}.
        </div>
    </div>
</body>
</html>
