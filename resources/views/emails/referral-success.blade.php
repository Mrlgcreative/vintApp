<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau parrainage - VintApp</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px 20px;
            border: 1px solid #dee2e6;
        }
        .footer {
            background: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 10px 10px;
            font-size: 0.9em;
        }
        .bonus-card {
            background: white;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .points-display {
            font-size: 2em;
            color: #28a745;
            font-weight: bold;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Nouveau parrainage réussi !</h1>
        <p>Félicitations {{ $referrer->name }} !</p>
    </div>

    <div class="content">
        <h2>Excellente nouvelle !</h2>
        
        <p>Votre code de parrainage <strong>{{ $referralCode->code }}</strong> vient d'être utilisé par un nouveau membre :</p>
        
        <div style="background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #007bff;">
            <h3 style="margin-top: 0;">👤 Nouveau membre parrainé</h3>
            <p><strong>Nom :</strong> {{ $newUser->name }}</p>
            <p><strong>Date d'inscription :</strong> {{ $newUser->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="bonus-card">
            <h3 style="color: #28a745; margin-top: 0;">🎁 Votre récompense de parrainage</h3>
            <div class="points-display">+{{ $pointsEarned }} points</div>
            <p style="margin-bottom: 0;">Niveau {{ $referralCode->level }} - Multiplicateur x{{ $referralCode->multiplier }}</p>
        </div>

        <h3>📊 Vos statistiques de parrainage</h3>
        <div style="display: flex; justify-content: space-between; background: white; padding: 15px; border-radius: 8px;">
            <div style="text-align: center;">
                <div style="font-size: 1.5em; font-weight: bold; color: #007bff;">{{ $stats['total_referrals'] }}</div>
                <div>Parrainages totaux</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 1.5em; font-weight: bold; color: #28a745;">{{ $stats['total_points'] }}</div>
                <div>Points totaux</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 1.5em; font-weight: bold; color: #ffc107;">{{ $stats['conversion_rate'] }}%</div>
                <div>Taux de conversion</div>
            </div>
        </div>

        <p style="margin-top: 20px;">
            <a href="{{ route('affiliate.dashboard') }}" class="btn">
                📈 Voir mon tableau de bord d'affiliation
            </a>
        </p>

        <hr style="margin: 30px 0;">

        <h3>💡 Maximisez vos gains</h3>
        <ul>
            <li><strong>Partagez vos codes :</strong> Plus vous parrainez, plus vous gagnez !</li>
            <li><strong>Codes personnalisés :</strong> Créez des codes mémorables pour vos amis</li>
            <li><strong>Conversion en argent :</strong> Échangez vos points contre de l'argent réel</li>
            <li><strong>Système de niveaux :</strong> Débloquez des multiplicateurs plus élevés</li>
        </ul>
    </div>

    <div class="footer">
        <p><strong>VintApp</strong> - Plateforme de vente en ligne</p>
        <p>Cet email a été envoyé automatiquement suite à un parrainage réussi.</p>
        <p style="font-size: 0.8em; margin-top: 15px;">
            Si vous ne souhaitez plus recevoir ces notifications, 
            <a href="{{ route('profile.settings') }}" style="color: #17a2b8;">modifiez vos préférences</a>
        </p>
    </div>
</body>
</html>