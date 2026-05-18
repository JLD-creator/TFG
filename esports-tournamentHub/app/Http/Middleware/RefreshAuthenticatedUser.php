<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RefreshAuthenticatedUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $freshUser = Auth::user()->fresh();

            if ($freshUser === null) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login');
            }

            Auth::setUser($freshUser);
            $request->setUserResolver(static fn () => $freshUser);
        }

        return $next($request);
    }
}
