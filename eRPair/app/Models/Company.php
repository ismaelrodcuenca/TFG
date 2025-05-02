<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "companies";

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
    protected $fillable = ['document','name','surname','surname2','phone_number','phone_number2','postal_code','address','document_type_id'];

    /**
     * Propiedad protegida que define los atributos que deben ser convertidos a tipos específicos.
     * 
     * @var array $casts Atributos con conversiones de tipo.
     */
    protected $casts = [];
    
    
    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class);
    }
}
