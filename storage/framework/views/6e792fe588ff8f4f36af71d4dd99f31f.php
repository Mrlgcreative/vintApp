<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques de pré-inscription - VintApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .stats-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .stats-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 15px;
            padding: 2rem;
            color: white;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .stat-value {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .progress-custom {
            height: 30px;
            border-radius: 15px;
            background: #f3f4f6;
        }

        .progress-bar-custom {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 15px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="stats-container">
        <div class="stats-card">
            <h1 class="text-center mb-4">
                <i class="fas fa-chart-line me-2"></i>Statistiques de pré-inscription
            </h1>

            <div class="row">
                <div class="col-md-6">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo e($stats['total']); ?></div>
                        <div class="stat-label"><i class="fas fa-users me-2"></i>Total inscrits</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo e($stats['confirmed']); ?></div>
                        <div class="stat-label"><i class="fas fa-check-circle me-2"></i>Emails confirmés</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-box" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <div class="stat-value"><?php echo e($stats['approved']); ?></div>
                        <div class="stat-label"><i class="fas fa-thumbs-up me-2"></i>Approuvés</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-box" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <div class="stat-value"><?php echo e($stats['converted']); ?></div>
                        <div class="stat-label"><i class="fas fa-user-check me-2"></i>Comptes créés</div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h5>Taux de conversion</h5>
                <div class="progress progress-custom">
                    <div class="progress-bar progress-bar-custom" 
                         style="width: <?php echo e($stats['total'] > 0 ? round(($stats['converted'] / $stats['total']) * 100) : 0); ?>%">
                        <?php echo e($stats['total'] > 0 ? round(($stats['converted'] / $stats['total']) * 100) : 0); ?>%
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="<?php echo e(route('preregistration.index')); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket me-2"></i>Rejoignez-nous !
                </a>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/preregistration/stats.blade.php ENDPATH**/ ?>