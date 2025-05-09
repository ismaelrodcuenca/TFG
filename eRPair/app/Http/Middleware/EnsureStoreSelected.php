<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStoreSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $isStoreSet = session()->has('store_id');
            $isOnStoreSelectionPage = $request->routeIs('select-store');

            if (!$isStoreSet && !$isOnStoreSelectionPage) {
                return redirect("select-store");
            }
        }

        return $next($request);
    }
}
