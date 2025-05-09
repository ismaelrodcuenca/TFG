<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    // Mostrar la vista para seleccionar la tienda
    public function select()
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        $stores = $user->stores()->pluck('name', 'id'); // Obtener las tiendas asociadas al usuario

        return view('select-store', compact('stores', 'user'));
    }

    // Guardar la tienda seleccionada en la sesión
    public function storeSelection(Request $request)
    {
        // Validar que la tienda seleccionada existe en la base de datos
        $request->validate([
            'store_id' => 'required|exists:stores,id',
        ]);

        // Guardar el ID de la tienda seleccionada en la sesión
        session(['store_id' => $request->store_id]);

        // Redirigir al dashboard o la página principal de tu app
        return redirect()->intended('/dashboard'); // O la ruta que prefieras
    }
}
