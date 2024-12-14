<?php

namespace App\Http\Middleware;

use Closure;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath as BaseMiddleware;

class InitializeTenancyByPath extends BaseMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!$request->route()) {
            return $next($request);
        }

        $path = explode('/', $request->path());
        $tenantId = $path[0] ?? null;

        if ($tenantId && is_numeric($tenantId)) {
            $this->initializeTenancy(
                $request,
                $tenantId
            );
        }

        return $next($request);
    }
}
