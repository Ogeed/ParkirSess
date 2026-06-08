<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EspTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - No token provided',
            ], 401);
        }

        $device = Device::where('api_token', hash('sha256', $token))->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Invalid token',
            ], 401);
        }

        $request->merge(['authenticated_device' => $device]);

        return $next($request);
    }
}
