<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvel article - {{ $item->name }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #4a4a5a;
            background-color: #f4f0f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
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
            margin: 8px 0 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 15px;
        }
        .content {
            padding: 36px 32px 28px;
        }
        .item-card {
            border: 1px solid #eeeaf5;
            border-radius: 12px;
            overflow: hidden;
            margin: 20px 0;
        }
        .item-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: block;
        }
        .item-details {
            padding: 24px;
        }
        .item-title {
            color: #1f1f2e;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }
        .item-description {
            color: #6b6b80;
            line-height: 1.6;
            margin: 12px 0;
            font-size: 14px;
        }
        .item-price {
            font-size: 26px;
            color: #7c3aed;
            font-weight: 800;
            margin: 16px 0;
        }
        .item-meta {
            color: #9a9aae;
            font-size: 13px;
            margin: 12px 0;
        }
        .badge {
            display: inline-block;
            background: #f5f3ff;
            color: #7c3aed;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin: 2px 4px 2px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: #ffffff !important;
            padding: 14px 36px;
            text-decoration: none !important;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            margin: 16px 0;
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
            .email-container { margin: 12px; border-radius: 12px; }
            .header, .content, .footer { padding: 20px; }
            .item-image { height: 220px; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Nouvel article disponible !</h1>
            <p>Découvrez cette nouvelle trouvaille</p>
        </div>
        <div class="content">
            <p>Bonjour {{ $subscriber->name ?? 'cher(e) abonné(e)' }},</p>
            <p>Un nouvel article vient d'être ajouté et pourrait vous intéresser :</p>

            <div class="item-card">
                @if($item->images && count($item->images) > 0)
                    <img src="{{ Storage::url($item->images[0]) }}" alt="{{ $item->name }}" class="item-image">
                @endif
                <div class="item-details">
                    <h2 class="item-title">{{ $item->name }}</h2>

                    <div>
                        <span class="badge">{{ $item->category->name }}</span>
                        @if($item->brand)
                            <span class="badge">{{ $item->brand->name }}</span>
                        @endif
                        <span class="badge">{{ ucfirst(str_replace('_', ' ', $item->condition)) }}</span>
                    </div>

                    <p class="item-description">{{ Str::limit($item->description, 200) }}</p>

                    <div class="item-price">{{ number_format($item->price) }} {{ $item->currency }}</div>

                    <div class="item-meta">
                        Par {{ $item->user->name }} &mdash; {{ $item->views }} vues
                    </div>

                    <div style="text-align: center;">
                        <a href="{{ route('newsletter.track.click', ['token' => $subscriber->unsubscribe_token, 'url' => route('items.show', $item)]) }}" class="cta-button">
                            Voir l'article
                        </a>
                    </div>
                </div>
            </div>

            <p>Ne manquez pas cette opportunité !</p>
            <p><strong>L'équipe {{ config('app.name') }}</strong></p>
        </div>
        <div class="footer">
            <p>Vous recevez cet email car vous êtes abonné(e) aux notifications de nouveaux articles.</p>
            <p>
                <a href="{{ route('newsletter.preferences', $subscriber->unsubscribe_token) }}">Gérer mes préférences</a>
                &mdash;
                <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}">Se désabonner</a>
            </p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <img src="{{ route('newsletter.track.open', $subscriber->unsubscribe_token) }}" alt="" width="1" height="1" style="display:none;">
        </div>
    </div>
</body>
</html>
