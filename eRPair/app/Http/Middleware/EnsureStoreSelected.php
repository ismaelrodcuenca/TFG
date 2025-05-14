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
        if (auth()->check()) {
            $isStoreSet = session()->has('store_id');
            $isOnStoreSelectionPage = $request->fullUrlIs(url('/dashboard/store-selection'));

            if (!$isStoreSet && !$isOnStoreSelectionPage) {
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