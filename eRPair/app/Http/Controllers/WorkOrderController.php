<?php

namespace App\Http\Controllers;

use App\Models\GlobalOption;
use App\Models\Item;
use App\Models\WorkOrder;
use Dompdf\Options;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
class WorkOrderController
{
    public function generateWorkOrderPDF($id)
    {

        $workOrder = WorkOrder::findOrFail($id);

        $items = $workOrder->items;
        $device = $workOrder->device;
        $store = $workOrder->store;
        $client = $device?->client;
        $repairTime = $workOrder->repairTime;
        $globalOption = GlobalOption::find(1);
        $options = new Options();
        $options->set('defaultFont', "Roboto");
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $html = view('work_orders.work_order', [
            'tipo_documento' => 'Nº Pedido: ' . $workOrder->id,
            'workOrder' => $workOrder,
            'items' => $items,
            'device' => $device,
            'store' => $store,
            'client' => $client,
            'repairTime' => $repairTime,
            'globalOption' => $globalOption,
        ])->render();

        // Cargar el HTML en Dompdf
        $dompdf->loadHtml($html);
        $dompdf->render();
        /*
        return view('work_orders.work_order', [
            'tipo_documento' => 'WorkOrder-' . $workOrder->id,
            'workOrder' => $workOrder,
            'items' => $items,
            'device' => $device,
            'store' => $store,
            'client' => $client,
            'repairTime' => $repairTime,
            'globalOption' => $globalOption,
        ]);
        */
        
        return $dompdf->stream("pedido-{$workOrder->id}.pdf", ['Attachment' => false]);
    }

}
