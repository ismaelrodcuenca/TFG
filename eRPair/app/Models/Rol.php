<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{

    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "roles";

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
    protected $fillable = ['name'];

    /**
     * Propiedad protegida que define los atributos que deben ser convertidos a tipos específicos.
     * 
     * @var array $casts Atributos con conversiones de tipo.
     */
    protected $casts = [];

    /**
     * Relación muchos a muchos entre el modelo Rol y el modelo User.
     * 
     * @return BelongsToMany Relación de usuarios asociados al rol.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
