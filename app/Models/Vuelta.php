<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable; // la interfaz
use OwenIt\Auditing\Auditable as AuditableTrait; // el trait
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Vuelta extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [
        'empresa_id','vehiculo_id','conductor_id','ruta_id','created_by',
        'fecha','numero_vuelta','hora_salida','hora_llegada','observaciones',
    ];
    protected $casts = ['fecha' => 'date'];
    protected $auditInclude = ['vehiculo_id','conductor_id','ruta_id','fecha','numero_vuelta'];

    public function empresa()    { return $this->belongsTo(Empresa::class); }
    public function vehiculo()   { return $this->belongsTo(Vehiculo::class); }
    public function conductor()  { return $this->belongsTo(Conductor::class); }
    public function ruta()       { return $this->belongsTo(Ruta::class); }
    public function creadoPor()  { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeDeEmpresa($q)
    {
        return $q->where('empresa_id', Auth::user()?->empresa_id ?? 0);
    }
    public function scopeHoy($q)    { return $q->whereDate('fecha', today()); }
    public function scopeDelDia($q, $fecha) { return $q->whereDate('fecha', $fecha); }
}
