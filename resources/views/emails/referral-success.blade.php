<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parrainage - {{ config('app.name') }}</title>
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
        .card {
            background: #f5f5f5;
            padding: 16px 20px;
            border-radius: 8px;
            margin: 16px 0;
        }
        .card h3 {
            color: #4a4a4a;
            margin: 0 0 8px;
            font-size: 15px;
        }
        .card p {
            margin: 4px 0;
            font-size: 14px;
        }
        .reward {
            text-align: center;
            padding: 24px;
            margin: 20px 0;
            border: 2px solid #1a1a1a;
            border-radius: 10px;
        }
        .reward h3 {
            margin: 0;
            font-size: 15px;
        }
        .reward-points {
            font-size: 40px;
            font-weight: 800;
            color: #1a1a1a;
            margin: 8px 0;
        }
        .reward-label {
            margin: 0;
            font-size: 14px;
        }
        .reward-meta {
            margin: 8px 0 0;
            font-size: 13px;
            color: #9a9a9a;
        }
        .stats {
            display: flex;
            gap: 12px;
            margin: 16px 0;
        }
        .stat {
            flex: 1;
            padding: 16px;
            text-align: center;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
        }
        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
        }
        .stat-label {
            font-size: 12px;
            color: #9a9a9a;
            margin-top: 4px;
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
        .divider {
            border: none;
            border-top: 1px solid #e5e5e5;
            margin: 24px 0;
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
            .stats { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouveau parrainage</h1>
            <p>Félicitations {{ $referrer->name }} !</p>
        </div>
        <div class="content">
            <p style="margin: 0 0 16px;">Votre code <strong>{{ $referralCode->code }}</strong> a été utilisé par un nouveau membre :</p>

            <div class="card">
                <h3>Nouveau membre</h3>
                <p><strong>Nom :</strong> {{ $newUser->name }}</p>
                <p><strong>Inscription :</strong> {{ $newUser->created_at->format('d/m/Y à H:i') }}</p>
            </div>

            <div class="reward">
                <h3>Votre récompense</h3>
                <div class="reward-points">+{{ $pointsEarned }}</div>
                <p class="reward-label">points gagnés</p>
                <p class="reward-meta">Niveau {{ $referralCode->level }} &mdash; Multiplicateur x{{ $referralCode->multiplier }}</p>
            </div>

            <h3 style="margin: 0 0 12px; font-size: 15px;">Vos statistiques</h3>
            <div class="stats">
                <div class="stat">
                    <div class="stat-value">{{ $stats['total_referrals'] }}</div>
                    <div class="stat-label">Parrainages</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $stats['total_points'] }}</div>
                    <div class="stat-label">Points totaux</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $stats['conversion_rate'] }}%</div>
                    <div class="stat-label">Conversion</div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('affiliate.dashboard') }}" class="btn">Voir mon tableau de bord</a>
            </div>
        </div>
        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>Email envoyé suite à un parrainage réussi.</p>
            <p style="margin-top: 12px;">
                <a href="{{ route('profile.settings') }}">Gérer mes préférences</a>
            </p>
        </div>
    </div>
</body>
</html>
