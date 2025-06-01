<?php

namespace app\Helpers;


use App\Http\Controllers\InvoiceController;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\ItemWorkOrder;
use Carbon\Carbon;
use DB;
use Filament\Notifications\Notification;

class PermissionHelper
{

    public static function developMode(): bool
    {
        return false;
    }
    public static function actualRol(): int
    {
        return session('rol_id', 0);
    }

    public static function hasRole(): bool
    {
        return session('rol_id') != 0;
    }
    /**
     * Check if the current user has administrative privileges.
     *
     * This method determines whether the user has the necessary permissions
     * to be considered an administrator.
     *
     * @return bool Returns true if the user is an administrator, false otherwise.
     */
    public static function isAdmin(): bool
    {
        return in_array(self::actualRol(), [ADMIN_ROL]);
    }
    /**
     * Checks if the current user has the role of a technician or upper.
     *
     * @return bool Returns true if the user is a technician, otherwise false.
     */
    public static function isTechnician(): bool
    {
        return in_array(self::actualRol(), [TECHNICIAN_ROL, ADMIN_ROL]);
    }

    /**
     * Checks if the current user has manager or upperlevel of permissions.
     *
     * @return bool Returns true if the user is a manager, false otherwise.
     */
    public static function isManager(): bool
    {
        Notification::make('ROL ACTUAL: ' . self::actualRol());
        return in_array(self::actualRol(), [ADMIN_ROL, MANAGER_ROL]);
    }

    /**
     * Checks if the current user has the role of a salesperson or upper.
     *
     * @return bool Returns true if the user is a salesperson, false otherwise.
     */
    public static function isSalesperson(): bool
    {
        return in_array(self::actualRol(), [SALESPERSON_ROL, MANAGER_ROL, ADMIN_ROL]);
    }

    /**
     * Determines if the provided value satisfies the "something else" condition.
     *
     * @param int|string $else The value to be checked. It can be an integer or a string.
     * @return bool Returns true if the condition is met, otherwise false.
     */
    public static function isSomethingElse(int|string $else): bool
    {
        if (is_int($else)) {
            $rol = DB::table("roles")->where("id", $else)->first();
        }
        if (is_string($else)) {
            $rol = DB::table("roles")->where("name", $else)->first();
        }
        if (!$rol) {
            return in_array(self::actualRol(), [$rol->id]);
        }
        return false;
    }


    /**
     * Check if the current user does not have administrative privileges.
     *
     * @return bool Returns true if the user is not an administrator, false otherwise.
     */
    public static function isNotAdmin(): bool
    {
        return !self::isAdmin();
    }

    /**
     * Checks if the current user does not have the role of a technician or upper.
     *
     * @return bool Returns true if the user is not a technician, otherwise false.
     */
    public static function isNotTechnician(): bool
    {
        return !self::isTechnician();
    }

    /**
     * Checks if the current user does not have manager or upper-level permissions.
     *
     * @return bool Returns true if the user is not a manager, false otherwise.
     */
    public static function isNotManager(): bool
    {
        return !self::isManager();
    }

    /**
     * Checks if the current user does not have the role of a salesperson or upper.
     *
     * @return bool Returns true if the user is not a salesperson, false otherwise.
     */
    public static function isNotSalesperson(): bool
    {
        return !self::isSalesperson();
    }


    /**
     * Checks if the current user is not allowed to access a record outside of their store.
     * This method is used to restrict access to records based on the user's store association.
     * @param mixed $record The record to check against the user's store.
     * @return bool Returns true if the user is not allowed to access the record outside of their store, false otherwise.
     */


    // Estados de las órdenes
    const CAN_DELIVER_STATES = [
        'COMPLETADO',
        'FACTURADO',
        'CANCELADO',
    ];

    const CAN_ADD_WARRANTY_STATES = [
        'ENTREGADO',
        'FACTURADO',
    ];

    const CANT_CANCEL_STATES = [
        'FACTURADO',
        'ENTREGADO',
        'CANCELADO',
        'DEVOLUCIÓN COMPLETADA',
    ];
    const CANT_BE_BILLED_STATES = [
        'ENTREGADO',
        'FACTURADO',
        'CANCELADO',
    ];

    public static function isOutsideStore($record): bool
    {
        if (self::isAdmin()) {
            return false;
        }

        $storeID = $record->store->id ?? null;
        if (!$storeID && method_exists($record, 'getParentRecord')) {
            $storeID = $record::getParentRecord()->store->id ?? null;
        }
        return session('store_id') != $storeID;
    }

    public static function isWorkOrderTooOld($workOrderRecord): bool
    {
        $date = Carbon::parse(time: $workOrderRecord->created_at);
        return $date < Carbon::now()->subMinutes(30);
    }

    public static function isWorkOrderProcessed($workOrderRecord): bool
    {
        $status = $workOrderRecord->statusWorkOrders->last()->status->name ?? 'SIN ESTADO';
        if (self::isTechnician()) {
            if ($status === "FACTURADO" || $status === "ENTREGADO" || $status === "CANCELADO" || $status === "DEVOLUCION REALIZADA") {
                return true;
            }
            return false;
        }
        return $status !== "PENDIENTE";
    }

    public static function isWorkOrderInvoiced($workOrderRecord): bool
    {
        $status = $workOrderRecord->statusWorkOrders->last()->status->name ?? 'SIN ESTADO';
        if ($status === "FACTURADO" || $status === "ENTREGADO") {
            return true;
        }
        return false;
    }

    public static function infoNotification($workOrderRecord): void
    {
        $isWarranty = $workOrderRecord->is_warranty ? "Sí" : "No";
        $infoNotification = Notification::make()
            ->title('Información del pedido')
            ->info()
            ->duration(10000)
            ->icon('heroicon-o-information-circle')
            ->body("
             <br><strong>Codigo dispositivo:</strong> {$workOrderRecord->device->unlock_code}</br>
            <br><strong>Nº Pedido:</strong> {$workOrderRecord->work_order_number}</br>
            <br><strong>¿Garantía?:</strong> {$isWarranty}</br>
            <br><strong>Tiempo reparación:</strong> {$workOrderRecord->repairTime->name}</br>
            <br><strong>Creado por:</strong> {$workOrderRecord->user->name}</br>
            <br><strong>Fecha creacion:</strong> {$workOrderRecord->created_at}</br>
            ")
            ->send();

        $editNotification = Notification::make()
            ->title('Edición del pedido no permitida')
            ->warning()
            ->icon('heroicon-o-exclamation-triangle');

        $infoNotification->send();
        if (self::isOutsideStore($workOrderRecord)) {

            $editNotification->body('No se puede editar un pedido fuera de la tienda asignada.')
                ->send();
        }

        if (self::isWorkOrderProcessed($workOrderRecord)) {
            $editNotification->body('No se puede editar un pedido que ya ha sido procesado o que está fuera del tiempo permitido.')
                ->send();
        }

        if (self::isWorkOrderTooOld($workOrderRecord)) {
            $editNotification->body('No se puede editar un pedido que ha sido creado hace más de 30 minutos.')
                ->send();
        }
    }

    public static function optionsAvailableOnWorkOrder($workOrderRecord): bool
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return true;
        }

        if (self::isWorkOrderProcessed($workOrderRecord)) {
            return true;
        }

        return false;
    }

    public static function isWorkOrderEditable($workOrderRecord): bool
    {

        if (self::isOutsideStore($workOrderRecord)) {
            return true;
        }

        if (self::isWorkOrderProcessed($workOrderRecord)) {
            return true;
        }

        if (self::isWorkOrderTooOld($workOrderRecord)) {
            return true;
        }

        return false;
    }
    public function isWorkOrderFinished($workOrderRecord): bool
    {
        $status = $workOrderRecord->statusWorkOrders->last()->status->name;
        return in_array($status, ['ENTREGADO', 'FACTURADO', 'CANCELADO', 'DEVOLUCION REALIZADA']);
    }

    public static function isRefundAvailable($workOrderRecord): bool
    {
        $invoices = Invoice::where('work_order_id', $workOrderRecord->id)->count();
        if ($invoices != 0) {
            if (InvoiceController::isFullyRefunded($workOrderRecord->id)) {
                return false;
            }
            return true;
        }

        return true;
    }

    public static function isChargeAvailable($workOrderRecord): bool
    {
        $items = ItemWorkOrder::where('work_order_id', $workOrderRecord->id)
            ->count();
        return !$items > 0;
    }

    public static function isItemsOptionsAvailable($workOrderRecord): bool
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return true;
        }

        if (self::isWorkOrderProcessed($workOrderRecord)) {
            return true;
        }

        return false;
    }

    public static function isWorkOrderDelivered($workOrderRecord): bool
    {
        $status = $workOrderRecord->statusWorkOrders->last()->status->name ?? 'SIN ESTADO';
        if ($status === "ENTREGADO" || $status === "FACTURADO") {
            return true;
        }
        return false;
    }

    public static function lastStatus($workOrderRecord): string
    {
        $status = $workOrderRecord->statusWorkOrders->last()->status->name ?? 'SIN ESTADO';
        return $status;
    }
    public static function canBeCanceled($workOrderRecord)
    {
        $last = self::lastStatus($workOrderRecord);
        if (in_array($last, self::CANT_CANCEL_STATES)) {
            return false;
        }
        return true;

    }

    public static function canAddWarranty($workOrderRecord): bool
    {
        if(self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        $last = self::lastStatus($workOrderRecord);
        if (in_array($last, self::CAN_ADD_WARRANTY_STATES)) {
            return true;
        }
        return false;
    }

    public static function canAddItems($workOrderRecord): bool
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }
       return self::canBeCanceled($workOrderRecord);
    }

    public static function canBeBilled($workOrderRecord): bool
    {
        if(self::isOutsideStore($workOrderRecord)) {
            return false;
        }
       $last = self::lastStatus($workOrderRecord);
        
        return !in_array($last, self::CANT_BE_BILLED_STATES);
    }

    public static function canBeRefunded($workOrderRecord): bool
    {
        if(self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        $hasNoInvoices = Invoice::where('work_order_id', $workOrderRecord->id)->doesntExist();
       
        $last = self::lastStatus($workOrderRecord);
        //Si tiene total devuelto != total facturado.
        $isNotFullyRefunded = !InvoiceController::isFullyRefunded($workOrderRecord->id);
        
        return  $isNotFullyRefunded && $hasNoInvoices ;
    }

    public static function canBeEdited($workOrderRecord): bool
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }

        if (self::isWorkOrderTooOld($workOrderRecord)) {
            return false;
        }

        return true;
    }

    public static function canBeDelivered($workOrderRecord): bool
    {
        if(self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        $isFullyPayed = InvoiceController::isFullyPayed($workOrderRecord->id);
        $canDeliver = in_array(self::lastStatus($workOrderRecord), self::CAN_DELIVER_STATES);
        return $isFullyPayed && $canDeliver;
    }
}