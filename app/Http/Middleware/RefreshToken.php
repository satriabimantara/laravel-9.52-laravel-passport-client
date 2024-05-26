<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // buat logic untuk menangani access_token atau user
        // jika user belum login atau belum ada token
        if (!$request->user() || !$request->user()->token) {
            return $next($request);
        }

        // jika sudah expired maka redirect ke route refresh token
        if ($request->user()->token->hasExpired()) {
            return redirect('/auth/passport/refresh');
        }
        return $next($request);
    }
}
