<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        // Add your subscription checking logic here
        // For example:
        if (!$request->user()->doctor || !$request->user()->doctor->hasActiveSubscription()) {
            return redirect()->route('billing')->with('error', 'Please subscribe to access this feature.');
        }

        // if ($request->user()->doctor->subscription('default')->onGracePeriod()) {
        //     return redirect()->route('billing')->with('error', 'Your subscription is on grace period. Please subscribe to access this feature.');
        // }

        return $next($request);
    }
}
