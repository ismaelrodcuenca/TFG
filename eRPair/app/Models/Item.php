<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Item extends Model
{

    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "items";

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
        'name',
        'cost',
        'price',
        'distributor',
        'type_id',
        'category_id'
    ];

    protected static function boot()
    {
        //deja boot tal cual 
        parent::boot();

        static::created(function ($item) {
            if ($item->link_to_stores) {
               $stores = Store::all();
                foreach ($stores as $store) {
                    $item->stores()->attach($store->id, ['quantity' => 0]);
                } 
            }
            
            if ($item->link_item_device_model) {
                $deviceModelId = request()->input('device_model_id');
                if ($deviceModelId) {
                    $item->deviceModels()->syncWithoutDetaching([$deviceModelId]);
                }
            }
        });
    }
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tax(): HasOneThrough
    {
        return $this->hasOneThrough(Tax::class, Category::class);
    }

    public function deviceModels(): BelongsToMany
    {
        return $this->belongsToMany(DeviceModel::class);
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'stocks')->withPivot('quantity');
    }

    //NO SE VA A USAR. 
    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(ItemWorkOrder::class);
    }
}
