<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DatabaseDumper extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Only allow admin users to dump database
        $user = Auth::user();
        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }

        // Additional security check for production
        if (app()->environment('production')) {
            abort(403, 'Database dump not allowed in production');
        }

        MySql::create()
        ->setDbName(env('DB_DATABASE'))
        ->setUserName(env('DB_USERNAME'))
        ->setPassword(env('DB_PASSWORD'))
        ->dumpToFile('dump.sql');

        return response()->json(['message' => 'Database dumped successfully']);
    }
}
