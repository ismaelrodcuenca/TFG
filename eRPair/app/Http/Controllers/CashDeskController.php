<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class CashDeskController
{
    public function getTotales(){
        $Invoices = Invoice::where('created_at','like', date('Y-m-d') . '%')
            ->where('store_id', session('store_id'))
            ->get();
    }
}
