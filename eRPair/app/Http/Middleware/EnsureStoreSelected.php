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
            $isOnStoreSelectionPage = $request->fullUrlIs(url('/dashboard/store-selection'));

            if (!$isStoreSet && !$isOnStoreSelectionPage) {
                return redirect('/dashboard/store-selection');
            }
        }

        return $next($request);
    }
}