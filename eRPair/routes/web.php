<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/select-store', [StoreController::class, 'select'])->name('store.select');
    Route::post('/select-store', [StoreController::class, 'storeSelection'])->name('store.select.store');
});


Route::redirect('/','/dashboard');
Route::get('/url', function () {
    //acctions
});