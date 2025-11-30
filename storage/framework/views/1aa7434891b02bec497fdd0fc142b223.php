<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Géolocalisation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">🌍 Test Détection Géographique</h1>

            <?php
                $ip = request()->ip();
                $position = null;
                $error = null;
                
                try {
                    $position = \Stevebauman\Location\Facades\Location::get($ip);
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            ?>

            <!-- Informations IP -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-blue-900 mb-4">📍 Votre Adresse IP</h2>
                <p class="text-2xl font-mono text-blue-700"><?php echo e($ip); ?></p>
            </div>

            <?php if($error): ?>
                <!-- Erreur -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-red-900 mb-4">❌ Erreur</h2>
                    <p class="text-red-700"><?php echo e($error); ?></p>
                </div>
            <?php elseif($position): ?>
                <!-- Localisation détectée -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-green-900 mb-4">✅ Localisation Détectée</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Ville</p>
                            <p class="text-lg font-semibold"><?php echo e($position->cityName ?? 'Non détecté'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Région</p>
                            <p class="text-lg font-semibold"><?php echo e($position->regionName ?? 'Non détecté'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Pays</p>
                            <p class="text-lg font-semibold"><?php echo e($position->countryName ?? 'Non détecté'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Code Pays</p>
                            <p class="text-lg font-semibold"><?php echo e($position->countryCode ?? 'Non détecté'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Latitude</p>
                            <p class="text-lg font-semibold"><?php echo e($position->latitude ?? 'Non détecté'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Longitude</p>
                            <p class="text-lg font-semibold"><?php echo e($position->longitude ?? 'Non détecté'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Vérification autorisation -->
                <?php
                    $cityName = $position->cityName;
                    $cityAllowed = $cityName ? \App\Models\AllowedCity::isCityAllowed($cityName, $position->countryName) : false;
                ?>

                <div class="bg-<?php echo e($cityAllowed ? 'green' : 'red'); ?>-50 border border-<?php echo e($cityAllowed ? 'green' : 'red'); ?>-200 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-<?php echo e($cityAllowed ? 'green' : 'red'); ?>-900 mb-4">
                        <?php echo e($cityAllowed ? '✅ Ville Autorisée' : '❌ Ville Non Autorisée'); ?>

                    </h2>
                    <p class="text-<?php echo e($cityAllowed ? 'green' : 'red'); ?>-700">
                        <?php echo e($cityAllowed ? "La ville {$cityName} est autorisée à accéder à l'application." : "La ville {$cityName} n'est pas encore disponible."); ?>

                    </p>
                </div>
            <?php else: ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-yellow-900 mb-4">⚠️ Localisation Non Disponible</h2>
                    <p class="text-yellow-700">Impossible de déterminer votre localisation.</p>
                </div>
            <?php endif; ?>

            <!-- Liste des villes autorisées -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <h2 class="text-xl font-bold text-purple-900 mb-4">📋 Villes Autorisées (<?php echo e(\App\Models\AllowedCity::active()->count()); ?>)</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <?php $__currentLoopData = \App\Models\AllowedCity::active()->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white rounded-lg p-3 shadow">
                            <p class="font-semibold text-gray-800"><?php echo e($city->name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($city->country); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Bouton retour -->
            <div class="mt-6">
                <a href="<?php echo e(route('home')); ?>" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/test-geo.blade.php ENDPATH**/ ?>