<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class CheckInitialUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if there are any users in the system
        $userCount = User::count();
        
        // If no users exist, redirect to setup page
        if ($userCount === 0) {
            // Skip redirect if already on setup page to avoid infinite redirect
            if (!$request->is('setup') && !$request->is('setup/*')) {
                // For API requests, return JSON response
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => 'System setup required. Please create an admin user first.',
                        'setup_required' => true,
                        'setup_url' => route('setup')
                    ], 503);
                }
                
                return redirect()->route('setup');
            }
        }
        
        return $next($request);
    }
}
