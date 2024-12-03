<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $doctor = $request->user();

        if (!$doctor || !$doctor->activeSubscription) {
            return response()->json([
                'message' => 'Active subscription required',
                'subscription_required' => true
            ], 403);
        }

        return $next($request);
    }
}
