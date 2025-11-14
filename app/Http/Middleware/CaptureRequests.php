<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CaptureRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Capturer spécifiquement admin/wallets/pending
        if (str_contains($request->path(), 'admin/wallets/pending')) {
            Log::error('🎯 ADMIN WALLETS PENDING REQUEST DETECTED!', [
                'method' => $request->method(),
                'path' => $request->path(),
                'url' => $request->fullUrl(),
                'status_code' => $response->getStatusCode(),
                'user_agent' => $request->header('user-agent'),
                'referer' => $request->header('referer'),
                'x_requested_with' => $request->header('x-requested-with'),
                'accept' => $request->header('accept'),
                'is_authenticated' => Auth::check(),
                'user_id' => Auth::id(),
                'all_headers' => $request->headers->all(),
            ]);
        }
        
        // Log général pour voir le trafic
        Log::info('🔍 REQUEST: ' . $request->method() . ' ' . $request->path() . ' → ' . $response->getStatusCode());
        
        return $response;
    }
}
