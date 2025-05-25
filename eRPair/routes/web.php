<?php
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('dashboard');
Route::get('/workorders/{id}/pdf',[WorkOrderController::class, 'generateWorkOrderPDF'])->name("generateWorkOrder");
