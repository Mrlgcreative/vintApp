<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $order->order_number }} - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #6A0DAD;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            max-width: 150px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #6A0DAD;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 12px;
            color: #666;
            line-height: 1.8;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .invoice-number {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }

        .invoice-date {
            font-size: 13px;
            color: #999;
        }

        .qr-code-section {
            text-align: center;
            padding: 20px;
            background: #f9f9f9;
            border: 2px dashed #6A0DAD;
            border-radius: 10px;
            margin-top: 10px;
        }

        .qr-code-section img {
            display: block;
            margin: 0 auto 10px;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .qr-code-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6A0DAD;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .qr-code-instructions {
            font-size: 11px;
            color: #666;
            line-height: 1.4;
        }

        .parties-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .party-box {
            flex: 1;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            margin-right: 20px;
        }

        .party-box:last-child {
            margin-right: 0;
        }

        .party-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #999;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .party-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .party-details {
            font-size: 13px;
            color: #666;
            line-height: 1.8;
        }

        .items-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .items-table thead {
            background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
            color: white;
        }

        .items-table th {
            padding: 15px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .items-table tbody tr:hover {
            background: #f9f9f9;
        }

        .item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .item-description {
            font-size: 12px;
            color: #999;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            width: 350px;
            margin-left: auto;
            margin-bottom: 30px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }

        .total-row.grand-total {
            background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
            color: white;
            padding: 15px 20px;
            margin-top: 10px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
        }

        .tracking-section {
            background: #f0f7ff;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }

        .tracking-title {
            font-size: 16px;
            font-weight: bold;
            color: #2196F3;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .tracking-title i {
            margin-right: 10px;
        }

        .tracking-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .tracking-item {
            font-size: 13px;
        }

        .tracking-label {
            color: #666;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .tracking-value {
            color: #333;
        }

        .tracking-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .tracking-status.delivered {
            background: #10B981;
            color: white;
        }

        .tracking-status.in-transit {
            background: #3B82F6;
            color: white;
        }

        .tracking-status.pending {
            background: #FCD34D;
            color: #92400E;
        }

        .notes-section {
            background: #fff9e6;
            border-left: 4px solid #FCD34D;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }

        .notes-title {
            font-size: 14px;
            font-weight: bold;
            color: #92400E;
            margin-bottom: 10px;
        }

        .notes-content {
            font-size: 13px;
            color: #666;
            line-height: 1.8;
        }

        .footer {
            text-align: center;
            padding-top: 30px;
            border-top: 2px solid #eee;
            color: #999;
            font-size: 12px;
        }

        .footer-highlight {
            color: #6A0DAD;
            font-weight: bold;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(106, 13, 173, 0.3);
            transition: all 0.3s;
        }

        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(106, 13, 173, 0.4);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                padding: 20px;
            }

            .print-button {
                display: none;
            }
        }

        @page {
            margin: 2cm;
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        🖨️ Imprimer / PDF
    </button>

    <div class="invoice-container">
        <!-- En-tête de la facture -->
        <div class="invoice-header">
            <div class="company-info">
                @if(isset($company['logo']))
                    <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="company-logo">
                @endif
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-details">
                    📍 {{ $company['address'] }}<br>
                    📞 {{ $company['phone'] }}<br>
                    ✉️ {{ $company['email'] }}<br>
                    🌐 {{ $company['website'] }}
                </div>
            </div>

            <div class="invoice-meta">
                <div class="invoice-title">FACTURE</div>
                <div class="invoice-number">
                    <strong>N°:</strong> {{ $order->order_number }}
                </div>
                <div class="invoice-date">
                    <strong>Date:</strong> {{ $order->created_at->format('d/m/Y') }}
                </div>
                @if($order->paid_at)
                <div class="invoice-date" style="color: #10B981; font-weight: bold; margin-top: 5px;">
                    ✓ Payée le {{ $order->paid_at->format('d/m/Y') }}
                </div>
                @endif
                
                <!-- QR Code pour confirmation -->
                @if($order->scan_token)
                <div class="qr-code-section">
                    <div class="qr-code-label">Confirmation de réception</div>
                    {!! QrCode::size(120)->generate($order->scan_url) !!}
                    <div class="qr-code-instructions">
                        Scannez ce code pour<br>confirmer la réception
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Informations des parties -->
        <div class="parties-section">
            <div class="party-box">
                <div class="party-label">Facturé à</div>
                <div class="party-name">{{ $order->buyer->name }}</div>
                <div class="party-details">
                    ✉️ {{ $order->buyer->email }}<br>
                    📞 {{ $order->shipping_phone }}<br>
                    📍 {{ $order->shipping_address }}<br>
                    🏙️ {{ $order->shipping_city }}
                </div>
            </div>

            <div class="party-box">
                <div class="party-label">Vendu par</div>
                <div class="party-name">{{ $order->seller->name }}</div>
                <div class="party-details">
                    ✉️ {{ $order->seller->email }}<br>
                    @if($order->seller->phone)
                    📞 {{ $order->seller->phone }}<br>
                    @endif
                    🏪 Vendeur vérifié
                </div>
            </div>
        </div>

        <!-- Tableau des articles -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Article</th>
                    <th class="text-center">Quantité</th>
                    <th class="text-right">Prix Unitaire</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-name">{{ $order->item->name }}</div>
                        <div class="item-description">
                            @if($order->item->brand)
                                Marque: {{ $order->item->brand->name }} •
                            @endif
                            @if($order->item->category)
                                {{ $order->item->category->name }}
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        <strong>{{ $order->quantity }}</strong>
                    </td>
                    <td class="text-right">
                        {{ number_format($order->unit_price, 2) }} {{ $order->currency }}
                    </td>
                    <td class="text-right">
                        <strong>{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Totaux -->
        <div class="totals-section">
            <div class="total-row">
                <span>Sous-total</span>
                <span>{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</span>
            </div>
            <div class="total-row">
                <span>Frais de livraison</span>
                <span>Inclus</span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL À PAYER</span>
                <span>{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</span>
            </div>
        </div>

        <!-- Section de traçage -->
        @if($currentTracking)
        <div class="tracking-section">
            <div class="tracking-title">
                🚚 Informations de Suivi de Livraison
            </div>
            <div class="tracking-info">
                <div class="tracking-item">
                    <div class="tracking-label">Statut actuel</div>
                    <div class="tracking-value">
                        @php
                            $statusClass = match($currentTracking->status) {
                                'delivered' => 'delivered',
                                'in_transit', 'out_for_delivery' => 'in-transit',
                                default => 'pending'
                            };
                        @endphp
                        <span class="tracking-status {{ $statusClass }}">
                            {{ $currentTracking->status_text }}
                        </span>
                    </div>
                </div>

                @if($currentTracking->tracking_code)
                <div class="tracking-item">
                    <div class="tracking-label">Code de suivi</div>
                    <div class="tracking-value">
                        <strong>{{ $currentTracking->tracking_code }}</strong>
                    </div>
                </div>
                @endif

                @if($currentTracking->carrier)
                <div class="tracking-item">
                    <div class="tracking-label">Transporteur</div>
                    <div class="tracking-value">{{ $currentTracking->carrier }}</div>
                </div>
                @endif

                @if($currentTracking->address)
                <div class="tracking-item">
                    <div class="tracking-label">Position actuelle</div>
                    <div class="tracking-value">
                        {{ $currentTracking->address }}
                        @if($currentTracking->city)
                            , {{ $currentTracking->city }}
                        @endif
                    </div>
                </div>
                @endif

                @if($currentTracking->estimated_delivery)
                <div class="tracking-item">
                    <div class="tracking-label">Livraison estimée</div>
                    <div class="tracking-value">
                        {{ $currentTracking->formatted_estimated_delivery }}
                    </div>
                </div>
                @endif

                @if($currentTracking->distance_to_customer)
                <div class="tracking-item">
                    <div class="tracking-label">Distance restante</div>
                    <div class="tracking-value">
                        <strong>{{ $currentTracking->distance_to_customer }} km</strong>
                    </div>
                </div>
                @endif

                <div class="tracking-item">
                    <div class="tracking-label">Dernière mise à jour</div>
                    <div class="tracking-value">
                        {{ $currentTracking->formatted_tracked_at }}
                    </div>
                </div>

                @if($currentTracking->description)
                <div class="tracking-item" style="grid-column: 1 / -1;">
                    <div class="tracking-label">Note de suivi</div>
                    <div class="tracking-value">{{ $currentTracking->description }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($order->notes)
        <div class="notes-section">
            <div class="notes-title">📝 Notes de commande</div>
            <div class="notes-content">
                {{ $order->notes }}
            </div>
        </div>
        @endif

        <!-- Pied de page -->
        <div class="footer">
            <p>
                <strong class="footer-highlight">{{ $company['name'] }}</strong> - Votre marketplace de confiance
            </p>
            <p style="margin-top: 10px;">
                Cette facture a été générée électroniquement et est traçable via notre système de suivi GPS.<br>
                Pour toute question, contactez-nous à {{ $company['email'] }} ou {{ $company['phone'] }}
            </p>
            <p style="margin-top: 15px; font-size: 11px;">
                Document généré le {{ now()->format('d/m/Y à H:i') }}
            </p>
        </div>
    </div>

    <script>
        // Impression automatique au chargement si paramètre ?print=1
        if (window.location.search.includes('print=1')) {
            window.onload = function() {
                window.print();
            };
        }
    </script>
</body>
</html>
