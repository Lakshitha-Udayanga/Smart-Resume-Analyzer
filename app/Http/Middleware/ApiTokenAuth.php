<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = env('API_TOKEN');
        if ($request->header('Authorization') !== 'Bearer ' . $token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $next($request);

    }
}
