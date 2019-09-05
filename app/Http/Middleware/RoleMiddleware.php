<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        $user = auth()->user();

        if(in_array("guest", (array)$roles) || $user !== null && $user->hasRole('admin'))
            return $next($request);

        foreach($roles as $role) {
            if($user->hasRole($role))
                return $next($request);
        };

        if (auth()->check())
            abort(403, 'Доступ запрещен.');
        else
            return route('login');
    }
}
