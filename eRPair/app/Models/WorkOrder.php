<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;


class WorkOrder extends Model
{
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "work_orders";

    /**
     * Propiedad protegida que define los atributos que no pueden ser asignados masivamente.
     * 
     * @var array $guarded Atributos protegidos contra asignación masiva.
     */
    protected $guarded = ['id'];

    /**
     * Propiedad protegida que define los atributos que pueden ser asignados masivamente.
     * 
     * @var array $fillable Atributos permitidos para asignación masiva.
     */
    protected $fillable = [
        'work_order_number',
        'failure',
        'private_comment',
        'comment',
        'pyshical_condition',
        'humidity',
        'test',
        'is_warranty',
        'user_id',
        'deliverer_id',
        'closure_id',
        'repair_time_id',
    ];
    /**
     * PENDIENTE DE VER SI FUNCIONA REALMENTE BIEN O NO
     * 
     * Antes de realiza la inserccion en la BBDD, bloquea el recurso de Store a la que pertenece este WorkOrder para settearle el work_order_number de la tienda y posteriormente incrementarlo en la misma. 
     */
    protected static function booted()
    {
        static::creating(function ($workOrder) {
            DB::transaction(function () use ($workOrder) {
                $store = Store::lockForUpdate()->find($workOrder->store_id);

                if (!$store) {
                    throw new \Exception("Tienda no encontrada");
                }
                // Asignar el número actual a la orden
                $workOrder->work_order_number = $store->work_order_number;

                // Incrementar el contador de la tienda
                $store->increment('work_order_number');
            });
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class)->withPivot('modified_amount');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function closure(): BelongsTo
    {
        return $this->belongsTo(Closure::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function repairTime(): BelongsTo
    {
        return $this->belongsTo(RepairTime::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
