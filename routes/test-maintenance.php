<?php

use App\Http\Middleware\MaintenanceMode;
use Illuminate\Support\Facades\Route;

// Route temporaire pour tester le mode maintenance
Route::get('/test-maintenance-enable', function() {
    MaintenanceMode::enable('Site en maintenance pour tests', '15 minutes');
    return response()->json(['success' => true, 'message' => 'Mode maintenance activé']);
});

Route::get('/test-maintenance-disable', function() {
    MaintenanceMode::disable();
    return response()->json(['success' => true, 'message' => 'Mode maintenance désactivé']);
});

Route::get('/test-maintenance-status', function() {
    return response()->json(['enabled' => MaintenanceMode::isEnabled()]);
});