<?php
namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStoreSelected
{
   
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check() && !auth()->user()->active){
            abort(403, 'Tu cuenta ha sido desactivada. Por favor contacta al administrador del sistema.');
        }
        if (auth()->check()) {
            $isStoreSet = session('store_id') != 0;
            $isRolSet = session('rol_id') != 0;
            $isOnStoreSelectionPage = $request->fullUrlIs(url('/dashboard/store-selection'));

            if (!$isStoreSet && !$isOnStoreSelectionPage && !$isRolSet) {
                Notification::make()
                    ->title('Selecciona tienda y perfil')
                    ->warning()
                    ->send();
                return redirect('/dashboard/store-selection');
            }
        }
        
        return $next($request);
    }
}