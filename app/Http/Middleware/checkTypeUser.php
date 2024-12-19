<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkTypeUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('sanctum')->check() && auth('sanctum')->user()->type == 'owner') {
            return responseApi(200, 'logged in owner');
        }else if (auth('sanctum')->check() && auth('sanctum')->user()->type == 'user') {
            return  responseApi(200, 'logged in user');
        }
        return $next($request);
    }
}
