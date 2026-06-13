# Script de build local pour Windows
Write-Host "🔨 Compilation des assets VintApp..." -ForegroundColor Cyan

# Activer la palette Tons Naturels
Write-Host "🎨 Activation palette Tons Naturels..." -ForegroundColor Yellow
php artisan tinker --execute="App\Models\Setting::updateOrCreate(['key'=>'active_color_palette'],['value'=>'earth','label'=>'Active Color Palette','type'=>'string']);"

# Vider les caches
Write-Host "🧹 Nettoyage des caches..." -ForegroundColor Yellow
php artisan cache:clear | Out-Null
php artisan config:clear | Out-Null

# Injecter les couleurs
Write-Host "🎨 Injection des couleurs..." -ForegroundColor Yellow
php artisan colors:inject

# Build
Write-Host "🔨 Build npm..." -ForegroundColor Yellow
npm run build

Write-Host ""
Write-Host "✅ Build terminé !" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Fichiers générés:" -ForegroundColor Cyan
Get-ChildItem -Path "public\build\assets\*.css" | Select-Object Name, @{Name = "Size"; Expression = { "{0:N2} KB" -f ($_.Length / 1KB) } } | Format-Table -AutoSize
