<?php

namespace App\Http\Middleware\Roles;

use Closure;

class AllowRolesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if ($user->hasAnyRole($roles))
            return $next($request);

        return abort(403, 'Доступ запрещен');
    }
}
