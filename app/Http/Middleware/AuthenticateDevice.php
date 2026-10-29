<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-Device-Key') ?: $request->bearerToken();

        $device = $key
            ? Device::where('api_key', $key)->where('is_active', true)->first()
            : null;

        if (! $device) {
            return response()->json(['ok' => false, 'message' => 'Perangkat tidak dikenal / nonaktif'], 401);
        }

        $device->forceFill(['last_seen_at' => now()])->save();
        $request->attributes->set('device', $device);

        return $next($request);
    }
}
