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
        $authorisedAdminURLs =
            [
                "categories",
                "document-types",
                "payment-methods",
                "repair-times",
                "rols",
                "statuses",
                "taxes",
                "types",
                "stores",
                "global-options",
                "users",
                "owners",
            ];

        $authorisedManagerURLs =
            [
                ""
            ];
        $requestURL = $request->path();
        $segments = explode('/', $requestURL);
        $currentResource = $segments[1] ?? null; //Nombre del recurso sin el dashboard/
        $exist = in_array($currentResource, $authorisedAdminURLs);
        if (!$exist) {
            return $next($request);
        }
        if (PermissionHelper::isNotAdmin() && $currentResource == "users") {
            $hasID = isset($segments[2]) && is_numeric($segments[2]);
            $isEdit = isset($segments[3]) && $segments[3] == "edit";
            if ($hasID) {
                $userId = $segments[2];
                if (auth()->user()->id == $userId) {
                    return $next($request);
                }
            }else{
                return $next($request);
            }
        }
        if ($exist && PermissionHelper::isAdmin()) {
            return $next($request);
        }
        abort(403, "No tiene permiso de administrador.");
    }
}
