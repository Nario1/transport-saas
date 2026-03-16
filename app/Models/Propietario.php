<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable; // la interfaz
use OwenIt\Auditing\Auditable as AuditableTrait; // el trait
use Illuminate\Database\Eloquent\SoftDeletes;

class Propietario extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [
        'empresa_id', 'nombre', 'apellidos', 'dni', 'telefono',
        'telefono_alt', 'email', 'direccion', 'activo', 'notas',
    ];
    protected $casts = ['activo' => 'boolean'];

    // Auditoría: solo campos relevantes
    protected $auditInclude = ['nombre','apellidos','dni','telefono','activo'];

    public function empresa()   { return $this->belongsTo(Empresa::class); }
    public function vehiculos() { return $this->hasMany(Vehiculo::class); }
    public function conductor() { return $this->hasOne(Conductor::class); }

    public function scopeDeEmpresa($q)
    {
        return $q->where('empresa_id', Auth::user()?->empresa_id ?? 0);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellidos}");
    }
}
