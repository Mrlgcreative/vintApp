<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvel article - <?php echo e($item->name); ?></title>
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
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .item-card {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin: 20px 0;
        }
        .item-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .item-details {
            padding: 20px;
        }
        .item-title {
            color: #4f00ce;
            font-size: 22px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }
        .item-description {
            color: #666;
            line-height: 1.6;
            margin: 15px 0;
        }
        .item-price {
            font-size: 28px;
            color: #4f00ce;
            font-weight: bold;
            margin: 15px 0;
        }
        .item-meta {
            display: flex;
            justify-content: space-between;
            color: #999;
            font-size: 14px;
            margin: 10px 0;
        }
        .badge {
            display: inline-block;
            background-color: #4f00ce;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 12px;
            margin: 5px 5px 5px 0;
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
            <h1>🆕 Nouvel article disponible !</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Bonjour <?php echo e($subscriber->name ?? 'cher(e) abonné(e)'); ?>,</p>
            
            <p>Un nouvel article vient d'être ajouté sur VintApp et pourrait vous intéresser :</p>

            <div class="item-card">
                <?php if($item->images && count($item->images) > 0): ?>
                    <img src="<?php echo e(Storage::url($item->images[0])); ?>" alt="<?php echo e($item->name); ?>" class="item-image">
                <?php endif; ?>
                
                <div class="item-details">
                    <h2 class="item-title"><?php echo e($item->name); ?></h2>
                    
                    <div>
                        <span class="badge"><?php echo e($item->category->name); ?></span>
                        <?php if($item->brand): ?>
                            <span class="badge"><?php echo e($item->brand->name); ?></span>
                        <?php endif; ?>
                        <span class="badge"><?php echo e(ucfirst(str_replace('_', ' ', $item->condition))); ?></span>
                    </div>
                    
                    <p class="item-description">
                        <?php echo e(Str::limit($item->description, 200)); ?>

                    </p>
                    
                    <div class="item-price">
                        <?php echo e(number_format($item->price)); ?> <?php echo e($item->currency); ?>

                    </div>
                    
                    <div class="item-meta">
                        <span>👤 Par <?php echo e($item->user->name); ?></span>
                        <span>👁️ <?php echo e($item->views); ?> vues</span>
                    </div>

                    <center>
                        <a href="<?php echo e(route('newsletter.track.click', ['token' => $subscriber->unsubscribe_token, 'url' => route('items.show', $item)])); ?>" class="cta-button">
                            Voir l'article
                        </a>
                    </center>
                </div>
            </div>

            <p style="margin-top: 20px;">Ne manquez pas cette opportunité !</p>
            <p><strong>L'équipe VintApp</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Vous recevez cet email car vous êtes abonné(e) aux notifications de nouveaux articles.</p>
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
<?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/emails/newsletter/new-item.blade.php ENDPATH**/ ?>