<?php

namespace App\Http\Middleware\Roles;

use Closure;

class DenyRolesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param $roles array of names
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();
        if (count($user->roles) === 0)
            return abort(403, 'У вас нет ни одной роли');

        if ($user->hasAnyRole($roles))
            return abort(403, 'Доступ запрещен.');


        return $next($request);
    }
}
