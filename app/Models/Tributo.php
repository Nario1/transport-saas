<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use OwenIt\Auditing\Contracts\Auditable; // la interfaz
use OwenIt\Auditing\Auditable as AuditableTrait; // el trait
use Illuminate\Support\Facades\Auth;
use App\Traits\Multitenantable;

class Tributo extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [
        'empresa_id','vehiculo_id','conductor_id','cobrado_por',
        'fecha','monto','metodo_pago','estado','cobrado_at','observaciones',
    ];
    protected $casts = [
        'fecha'      => 'date',
        'monto'      => 'decimal:2',
        'cobrado_at' => 'datetime',
    ];
    protected $auditInclude = ['estado','monto','metodo_pago','cobrado_por','cobrado_at'];

    public function empresa()    { return $this->belongsTo(Empresa::class); }
    public function vehiculo()   { return $this->belongsTo(Vehiculo::class); }
    public function conductor()  { return $this->belongsTo(Conductor::class); }
    public function cobrador()   { return $this->belongsTo(User::class, 'cobrado_por'); }

    public function scopeDeEmpresa($q)
    {
        return $q->where('empresa_id', Auth::user()?->empresa_id ?? 0);
    }
    public function scopePagados($q)    { return $q->where('estado','pagado'); }
    public function scopePendientes($q) { return $q->where('estado','pendiente'); }
    public function scopeHoy($q)        { return $q->whereDate('fecha', today()); }
}