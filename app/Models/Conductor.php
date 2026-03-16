<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable; // la interfaz
use OwenIt\Auditing\Auditable as AuditableTrait; // el trait
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Conductor extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;
    protected $table = 'conductores';

    protected $fillable = [
        'empresa_id', 'propietario_id', 'nombre', 'apellidos', 'dni',
        'telefono', 'email', 'direccion', 'tipo_licencia',
        'licencia_vence', 'estado', 'notas',
    ];
    protected $casts = [
        'licencia_vence' => 'date',
    ];
    protected $auditInclude = ['nombre','apellidos','estado','tipo_licencia','licencia_vence'];

    public function empresa()     { return $this->belongsTo(Empresa::class); }
    public function propietario() { return $this->belongsTo(Propietario::class); }
    public function vehiculos()   { return $this->hasMany(Vehiculo::class); }
    public function vueltas()     { return $this->hasMany(Vuelta::class); }
    public function tributos()    { return $this->hasMany(Tributo::class); }
    public function sanciones()   { return $this->hasMany(Sancion::class); }

    public function scopeDeEmpresa($q)
    {
        return $q->where('empresa_id', Auth::user()?->empresa_id ?? 0);
    }
    public function scopeActivos($q) { return $q->where('estado', 'activo'); }

    public function getNombreCompletoAttribute(): string {
        return trim("{$this->nombre} {$this->apellidos}");
    }
    public function getInicialesAttribute(): string {
        $parts = explode(' ', $this->nombre);
        return strtoupper(substr($parts[0],0,1) . substr($parts[1] ?? '',0,1));
    }
    public function getLicenciaVenceAlertaAttribute(): bool {
        return $this->licencia_vence &&
               $this->licencia_vence->isFuture() &&
               $this->licencia_vence->diffInDays(today()) <= 30;
    }
}