<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermitMiddleware
{

    public function __construct()
    {
        require_once base_path('permit-php/vendor/autoload.php');

        $this->permit = new Permit([
            'pdp' => config('permit.pdp'),
            'token' => config('permit.token'),
        ]);
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */   
    public function handle(Request $request, Closure $next, $permission, $resource)
    {
        $user = auth()->user()->grade;

        // Perform the permission check
        $permitted = $this->permit->check($user->grade, $permission, $resource);

        if (!$permitted) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
