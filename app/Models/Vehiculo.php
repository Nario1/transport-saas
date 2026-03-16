<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable; // la interfaz
use OwenIt\Auditing\Auditable as AuditableTrait; // el trait
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class Vehiculo extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [
        'empresa_id', 'propietario_id', 'conductor_id',
        'placa', 'numero_flota',                          // ← número de flota
        'marca', 'modelo', 'color', 'anio',
        'numero_motor', 'numero_chasis',
        'soat_vence', 'rev_tecnica_vence', 'tarjeta_prop_vence',
        'estado', 'notas',
    ];
    protected $casts = [
        'soat_vence'           => 'date',
        'rev_tecnica_vence'    => 'date',
        'tarjeta_prop_vence'   => 'date',
    ];
    protected $auditInclude = [
        'placa','numero_flota','conductor_id','propietario_id',
        'estado','soat_vence','rev_tecnica_vence',
    ];

    public function empresa()     { return $this->belongsTo(Empresa::class); }
    public function propietario() { return $this->belongsTo(Propietario::class); }
    public function conductor()   { return $this->belongsTo(Conductor::class); }
    public function rutas()       { return $this->belongsToMany(Ruta::class, 'vehiculo_rutas')->withPivot('activo','fecha_asignacion'); }
    public function vueltas()     { return $this->hasMany(Vuelta::class); }
    public function tributos()    { return $this->hasMany(Tributo::class); }
    public function sanciones()   { return $this->hasMany(Sancion::class); }

    public function scopeDeEmpresa($q)
    {
        return $q->where('empresa_id', Auth::user()?->empresa_id ?? 0);
    }
    public function scopeActivos($q)      { return $q->where('estado', 'activo'); }
    public function scopeConDeuda($q)     {
        return $q->whereHas('tributos', fn($t) => $t->where('estado','pendiente'));
    }

    // ── Accessors ──

    /** Placa en mayúsculas formateada */
    public function getPlacaFormAttribute(): string {
        return strtoupper($this->placa);
    }

    /** Label de flota: "Nro. 12" o null */
    public function getNumeroFlotaLabelAttribute(): ?string {
        return $this->numero_flota ? "Nro. {$this->numero_flota}" : null;
    }

    /** ¿Tiene tributo pagado hoy? */
    public function getTributoPagadoHoyAttribute(): bool {
        return $this->tributos()
            ->whereDate('fecha', today())
            ->where('estado', 'pagado')
            ->exists();
    }

    /** ¿SOAT próximo a vencer (<= 15 días)? */
    public function getSoatAlertaAttribute(): bool {
        return $this->soat_vence &&
               $this->soat_vence->isFuture() &&
               $this->soat_vence->diffInDays(today()) <= 15;
    }

    /** ¿Rev. técnica próxima a vencer (<= 15 días)? */
    public function getRevAlertaAttribute(): bool {
        return $this->rev_tecnica_vence &&
               $this->rev_tecnica_vence->isFuture() &&
               $this->rev_tecnica_vence->diffInDays(today()) <= 15;
    }

    /** ¿Algún documento vencido? */
    public function getDocVencidoAttribute(): bool {
        return ($this->soat_vence && $this->soat_vence->isPast()) ||
               ($this->rev_tecnica_vence && $this->rev_tecnica_vence->isPast());
    }
}