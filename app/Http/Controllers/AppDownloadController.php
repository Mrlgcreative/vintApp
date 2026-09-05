<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\AppDownload;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class AppDownloadController extends Controller
{
    public function index()
    {
        $qrSvg = $this->generateQrCode(route('download'));

        return view('download', compact('qrSvg'));
    }

    private function generateQrCode(string $data): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle(400, 0),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);

            return $writer->writeString($data);
        } catch (\Exception $e) {
            return '';
        }
    }

    public function apk(Request $request)
    {
        $path = public_path('downloads/vintapp-v1.0.0.apk');

        if (! File::exists($path)) {
            abort(404, 'APK introuvable.');
        }

        $this->trackDownload($request);

        return response()->download($path, 'vintapp-v1.0.0.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="vintapp-v1.0.0.apk"',
        ]);
    }

    private function trackDownload(Request $request): void
    {
        try {
            $agent = new \Jenssegers\Agent\Agent();
            $agent->setUserAgent((string) $request->userAgent());

            AppDownload::create([
                'platform' => 'android',
                'version' => '1.0.0',
                'url' => $request->fullUrl(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
                'device_type' => $agent->device() ?: null,
                'browser' => $agent->browser(),
                'os' => $agent->platform(),
                'ip_address' => $request->ip(),
                'referrer' => mb_substr((string) $request->headers->get('referer'), 0, 500) ?: null,
            ]);
        } catch (\Throwable $e) {
            // Le tracking ne doit jamais bloquer le téléchargement.
        }
    }
}