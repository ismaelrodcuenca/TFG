<?php

namespace App\Http\Middleware;

use App\Helpers\PermissionHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourcesAccess
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorisedURLs =
            [
                "categories",
                "document-types" ,
                "payment-methods",
                "repair-times",
                "rols",
                "statuses",
                "taxes",
                "types" ,
            ];
        $requestURL = $request->path();
        $segments = explode('/', $requestURL);
        $currentResource = $segments[1] ?? null; //Nombre del recurso sin el dashboard/
        $exist = in_array($currentResource, $authorisedURLs);
        if (!$exist) {
            return $next($request);
        }
        if($exist && PermissionHelper::isAdmin()) {
            return $next($request);
        }
        abort(403, "No tiene permiso de administrador.");
    }
}
