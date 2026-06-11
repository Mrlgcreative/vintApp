<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau parrainage - VintApp</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #4a4a5a;
            background-color: #f4f0f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(124, 58, 237, 0.08);
        }
        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #6d28d9 100%);
            padding: 36px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
        }
        .header p {
            margin: 6px 0 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 15px;
        }
        .content {
            padding: 36px 32px 28px;
        }
        .info-card {
            background: #f5f3ff;
            padding: 18px 20px;
            border-radius: 10px;
            border-left: 4px solid #7c3aed;
            margin: 16px 0;
        }
        .info-card h3 {
            color: #5b21b6;
            margin: 0 0 8px;
            font-size: 15px;
        }
        .info-card p {
            margin: 4px 0;
            font-size: 14px;
        }
        .bonus-card {
            background: #ecfdf5;
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 24px;
            margin: 20px 0;
            text-align: center;
        }
        .points-display {
            font-size: 42px;
            color: #10b981;
            font-weight: 800;
            margin: 8px 0;
        }
        .stats-grid {
            display: flex;
            gap: 12px;
            margin: 20px 0;
        }
        .stat-box {
            flex: 1;
            background: #ffffff;
            padding: 16px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #eeeaf5;
        }
        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #7c3aed;
        }
        .stat-label {
            font-size: 12px;
            color: #9a9aae;
            margin-top: 4px;
        }
        .btn {
            display: inline-block;
            background: #7c3aed;
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
            border-top: 1px solid #eeeaf5;
            margin: 28px 0;
        }
        .footer {
            background: #f8f6fc;
            padding: 28px 30px;
            text-align: center;
            border-top: 1px solid #eeeaf5;
        }
        .footer p {
            color: #9a9aae;
            margin: 4px 0;
            font-size: 13px;
        }
        .footer a {
            color: #7c3aed;
        }
        @media (max-width: 600px) {
            .container { margin: 12px; border-radius: 12px; }
            .header, .content, .footer { padding: 20px; }
            .stats-grid { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouveau parrainage réussi !</h1>
            <p>Félicitations {{ $referrer->name }} !</p>
        </div>

        <div class="content">
            <h2 style="color: #1f1f2e; margin: 0 0 16px;">Excellente nouvelle !</h2>

            <p>Votre code de parrainage <strong>{{ $referralCode->code }}</strong> vient d'être utilisé par un nouveau membre :</p>

            <div class="info-card">
                <h3>Nouveau membre parrainé</h3>
                <p><strong>Nom :</strong> {{ $newUser->name }}</p>
                <p><strong>Date d'inscription :</strong> {{ $newUser->created_at->format('d/m/Y à H:i') }}</p>
            </div>

            <div class="bonus-card">
                <h3 style="color: #10b981; margin: 0; font-size: 16px;">Votre récompense</h3>
                <div class="points-display">+{{ $pointsEarned }}</div>
                <p style="margin: 0; color: #4a4a5a;">points gagnés</p>
                <p style="margin: 8px 0 0; font-size: 13px; color: #9a9aae;">
                    Niveau {{ $referralCode->level }} &mdash; Multiplicateur x{{ $referralCode->multiplier }}
                </p>
            </div>

            <h3 style="color: #1f1f2e; font-size: 15px;">Vos statistiques</h3>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['total_referrals'] }}</div>
                    <div class="stat-label">Parrainages</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['total_points'] }}</div>
                    <div class="stat-label">Points totaux</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['conversion_rate'] }}%</div>
                    <div class="stat-label">Conversion</div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('affiliate.dashboard') }}" class="btn">
                    Voir mon tableau de bord
                </a>
            </div>

            <hr class="divider">

            <h3 style="color: #1f1f2e; font-size: 15px;">Maximisez vos gains</h3>
            <ul style="font-size: 14px; color: #4a4a5a; padding-left: 20px;">
                <li><strong>Partagez vos codes :</strong> Plus vous parrainez, plus vous gagnez !</li>
                <li><strong>Codes personnalisés :</strong> Créez des codes mémorables</li>
                <li><strong>Conversion en argent :</strong> Échangez vos points contre de l'argent réel</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong> &mdash; Marketplace de confiance</p>
            <p>Cet email a été envoyé automatiquement suite à un parrainage réussi.</p>
            <p style="margin-top: 12px; font-size: 12px;">
                <a href="{{ route('profile.settings') }}">Modifiez vos préférences</a>
            </p>
        </div>
    </div>
</body>
</html>
