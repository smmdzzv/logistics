<?php

namespace App\Http\Middleware\Branches;

use Closure;


class HasBranchMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param $roles
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->user()->branch)
            abort(403, 'Для доступа к ресурсу необходимо быть работником филиала');
        return $next($request);
    }
}
