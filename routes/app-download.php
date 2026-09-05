<?php

use Illuminate\Support\Facades\Route;

Route::get('/download', [App\Http\Controllers\AppDownloadController::class, 'index'])->name('download');
Route::get('/download/app.apk', [App\Http\Controllers\AppDownloadController::class, 'apk'])->name('download.apk');