<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotion VintApp</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4f00ce 0%, #8f5cff 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
            color: #333;
            line-height: 1.6;
        }
        .content p {
            font-size: 16px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4f00ce 0%, #8f5cff 100%);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            background-color: #f8f8f8;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer a {
            color: #4f00ce;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎁 Offre spéciale VintApp</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Bonjour <?php echo e($subscriber->name ?? 'cher(e) abonné(e)'); ?>,</p>
            
            <?php echo $emailContent; ?>


            <center>
                <a href="<?php echo e(route('newsletter.track.click', ['token' => $subscriber->unsubscribe_token, 'url' => route('items.index')])); ?>" class="cta-button">
                    Profiter de l'offre
                </a>
            </center>

            <p style="margin-top: 30px;">À bientôt sur VintApp !</p>
            <p><strong>L'équipe VintApp</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Vous recevez cet email car vous êtes abonné(e) à notre newsletter.</p>
            <p>
                <a href="<?php echo e(route('newsletter.preferences', $subscriber->unsubscribe_token)); ?>">Gérer mes préférences</a> | 
                <a href="<?php echo e(route('newsletter.unsubscribe', $subscriber->unsubscribe_token)); ?>">Se désabonner</a>
            </p>
            <p>&copy; <?php echo e(date('Y')); ?> VintApp. Tous droits réservés.</p>
            
            <!-- Pixel de tracking -->
            <img src="<?php echo e(route('newsletter.track.open', $subscriber->unsubscribe_token)); ?>" alt="" width="1" height="1" style="display:none;">
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/emails/newsletter/promotion.blade.php ENDPATH**/ ?>