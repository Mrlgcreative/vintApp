<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AppDownloadController extends Controller
{
    public function index()
    {
        return view('download');
    }

    public function apk(Request $request)
    {
        $path = public_path('downloads/vintapp-v1.0.0.apk');

        if (! File::exists($path)) {
            abort(404, 'APK introuvable.');
        }

        return response()->download($path, 'vintapp-v1.0.0.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="vintapp-v1.0.0.apk"',
        ]);
    }
}