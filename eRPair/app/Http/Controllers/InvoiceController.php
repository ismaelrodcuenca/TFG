<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\WorkOrder;
use DB;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class InvoiceController
{

   
    public static function calculoCompleto(int $workOrderId, int $clienteEntrega)
    {
        $total = self::calcularTotal($workOrderId);
        $impuestos = self::calcularImpuestos($workOrderId);
        $base = self::calcularBase($workOrderId);
        $pendiente = self::calcularPendiente($workOrderId);
        $totalFacturado = self::calcularTotalFacturado($workOrderId);
        $devolucion = self::calcularDevolucion($workOrderId);

        return collect([
            'total' => round($total, 2),
            'impuestos' => round($impuestos, 2),
            'base' => round($base, 2),
            'pendiente' => round($pendiente, 2),
            'total_facturado' => round($totalFacturado, 2),
            'devolucion' => round($devolucion, 2),
        ])->options();
    }
    public static function calcularTotal(int $workOrderId)
    {
        $workOrder = WorkOrder::with('itemWorkOrders')->findOrFail($workOrderId);
        $total = 0;
        foreach ($workOrder->itemWorkOrders as $itemWorkOrder) {
            $total += $itemWorkOrder->modified_amount ?? $itemWorkOrder->item->price;
        }
        return $total;
    }

    public static function calcularImpuestos(int $workOrderId)
    {
        $workOrder = WorkOrder::with('itemWorkOrders')->findOrFail($workOrderId);
        $impuestos = 0;
        foreach ($workOrder->itemWorkOrders as $itemWorkOrder) {
            $item = $itemWorkOrder->item;
            $tax = $item->category->tax->porcentage / 100;
            $price = $itemWorkOrder->modified_amount ?? $item->price;
            $impuestos += $price - ($price / (1 + $tax));
        }
        return round($impuestos, 2);
    }

    public static function calcularBase(int $workOrderId)
    {
        $workOrder = WorkOrder::with('itemWorkOrders')->findOrFail($workOrderId);
        $base = 0;
        foreach ($workOrder->itemWorkOrders as $itemWorkOrder) {
            $item = $itemWorkOrder->item;
            $tax = $item->category->tax->porcentage / 100;
            $price = $itemWorkOrder->modified_amount ?? $item->price;
            $base += $price / (1 + $tax);
        }
        return round($base, 2);
    }
    public static function calcularPendiente(int $workOrderId){
        $invoices = Invoice::where('work_order_id', $workOrderId)->count();
        $total = self::calcularTotal($workOrderId);
        if($invoices == 0){
            return round($total, 2);
        } else {
            $totalFacturado = self::calcularTotalFacturado($workOrderId);
            return round($total - $totalFacturado, 2);
        }
    }
    public static function isFullyPayed(int $workOrderId): bool
    {
        $total = self::calcularTotal($workOrderId);
        $totalFacturado = self::calcularTotalFacturado($workOrderId);
        return round($total, 2) === round($totalFacturado, 2);
    }
    private static function calcularTotalFacturado(int $workOrderId)
    {
        $invoices = Invoice::where('work_order_id', $workOrderId)
            ->where('is_refund', false)
            ->get();
        $totalFacturado = 0;
        foreach ($invoices as $invoice) {
            $totalFacturado += $invoice->total;
        }
        return round($totalFacturado, 2);
    }

    private static function calcularDevolucion(int $workOrderId)
    {
        $workOrder = WorkOrder::with('invoices')->findOrFail($workOrderId);
        $totalDevolucion = 0;

        foreach ($workOrder->invoices as $invoice) {
            if (!$invoice->is_refund) {
                continue;
            }
            foreach ($invoice->items as $invoiceItem) {
                $pivot = $invoiceItem->pivot;
                $totalDevolucion += $pivot->modified_amount ?? $invoiceItem->price;
            }
        }

        return round($totalDevolucion, 2);
    }


    /**
     * Genera el número de factura según la lógica del negocio.
     *
     * @param int|null $workOrderId
     * @param int $storeId
     * @return string
     */
    function generateInvoiceNumber(?int $workOrderId, int $storeId): string
    {
        $today = now()->format('Ymd');
        if ($workOrderId) {
            $count = DB::table('invoices')
                ->where('work_order_id', $workOrderId)
                ->where('store_id', $storeId)
                ->count() + 1;
            $workOrderNumber = DB::table('work_orders')
                ->where('id', $workOrderId)
                ->value('work_order_number');
            return "W{$workOrderNumber}-{$count}-{$storeId}-{$today}";
        } else {
            $count = DB::table('invoices')
                ->whereNull('work_order_id')
                ->where('store_id', $storeId)
                ->count() + 1;
            return "S{$count}-{$storeId}-{$today}";
        }
    }

    /**
     * Genera el número de factura para devolución.
     *
     * @param string $originalInvoiceNumber
     * @return string
     */
    function generateRefundInvoiceNumber(string $originalInvoiceNumber): string
    {
        return "{$originalInvoiceNumber}-R";
    }

    function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $workOrderId = $data['work_order_id'] ?? null;
            $storeId = $data['store_id'];

            $invoiceNumber = $this->generateInvoiceNumber($workOrderId, $storeId);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'base' => $data['base'],
                'taxes' => $data['taxes'],
                'total' => $data['total'],
                'is_refund' => false,
                'is_down_payment' => $data['is_down_payment'] ?? false,
                'work_order_id' => $workOrderId,
                'client_id' => $data['client_id'] ?? null,
                'store_id' => $storeId,
                'company_id' => $data['company_id'] ?? null,
                'payment_method_id' => $data['payment_method_id'],
                'user_id' => $data['user_id'],
                'comment' => $data['comment'] ?? null,
            ]);

            return $invoice;
        });
    }

    public static function createRefundInvoice(int $originalInvoiceId, array $dataOverrides = []): Invoice
    {
        return DB::transaction(function () use ($originalInvoiceId, $dataOverrides) {
            $original = Invoice::findOrFail($originalInvoiceId);
            $refundNumber = self::generateRefundInvoiceNumber($original->invoice_number);

            $refundData = array_merge($original->toArray(), [
                'invoice_number' => $refundNumber,
                'is_refund' => true,
                'comment' => $dataOverrides['comment'],
                'base' => $dataOverrides['base'] ?? $original->base * -1,
                'taxes' => $dataOverrides['taxes'] ?? $original->taxes * -1,
                'total' => $dataOverrides['total'] ?? $original->total * -1,
            ]);

            unset($refundData['id'], $refundData['created_at'], $refundData['updated_at']);

            return Invoice::create($refundData);
        });
    }

    public static function isFullyRefunded(int $workOrderId): bool
    {
        $amount = self::calcularTotalFacturado($workOrderId);
        $amountRefund = self::calcularDevolucion($workOrderId);

        return $amount === $amountRefund;
    }

    public static function getInvoicesForRefund(int $workOrderId): array
    {
        $invoices [] = [];
        $allInvoices = Invoice::where('work_order_id', $workOrderId)
            ->where('is_refund', false)
            ->get();
            foreach ($allInvoices as $invoice) {
                $invoiceRefunded = Invoice::where('invoice_number', $invoice->invoice_number."-R")
                    ->count();
                if ($invoiceRefunded === 0) {
                    $invoices[] = $invoice;
                }
            }

        return $invoices;
    }
}
