<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenEmailIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->header('access_token') || Hash::check(env('EMAIL_SECRET'), $request->header))
        {
            return response()->json([
                'code' => 401,
                'msg' => 'Invalid token.',
            ]);
        }
        
        return $next($request);
    }
}
