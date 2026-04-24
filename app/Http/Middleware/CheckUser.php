<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token) {
            $user = \App\Models\User::first();
            
            if ($user) {
                auth()->login($user);
                return $next($request);
            }
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}