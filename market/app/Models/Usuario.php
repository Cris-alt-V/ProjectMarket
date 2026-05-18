<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'Usuarios';          // nombre exacto de la tabla
    protected $primaryKey = 'id_usuario';   // clave primaria personalizada
    public $timestamps = false;             // no tienes created_at ni updated_at

    // Campos que se pueden insertar con create()
    protected $fillable = ['nombre', 'correo', 'contraseña', 'tipo_usuario'];

    // Indicar que la PK es autoincremental
    public $incrementing = true;

    // Tipo de la PK (SERIAL en PostgreSQL es entero)
    protected $keyType = 'int';
}
