<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;

class AuthVendor
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken(); // get token from Authorization: Bearer <token>

        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        $vendor = ServiceProvider::where('api_token', $token)->first();

        if (!$vendor) {
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }

        // Attach vendor to request
        $request->vendor = $vendor;

        return $next($request);
    }
}
