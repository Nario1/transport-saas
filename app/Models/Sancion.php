<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable; // la interfaz
use OwenIt\Auditing\Auditable as AuditableTrait; // el trait
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sancion extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;
    protected $table = 'sanciones';
    protected $fillable = [
        'empresa_id','vehiculo_id','conductor_id',
        'registrado_por','cobrado_por',
        'fecha','motivo','descripcion','monto','estado','cobrado_at',
    ];
    protected $casts = [
        'fecha'      => 'date',
        'monto'      => 'decimal:2',
        'cobrado_at' => 'datetime',
    ];
    protected $auditInclude = ['motivo','monto','estado','cobrado_por'];

    public function empresa()       { return $this->belongsTo(Empresa::class); }
    public function vehiculo()      { return $this->belongsTo(Vehiculo::class); }
    public function conductor()     { return $this->belongsTo(Conductor::class); }
    public function registrador()   { return $this->belongsTo(User::class, 'registrado_por'); }
    public function cobrador()      { return $this->belongsTo(User::class, 'cobrado_por'); }

    public function scopeDeEmpresa($q)
    {
        return $q->where('empresa_id', Auth::user()?->empresa_id ?? 0);
    }    
    
    public function scopePendientes($q) { return $q->where('estado','pendiente'); }
}
