<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    protected $fillable = ['empresa_id', 'name', 'email', 'password', 'activo'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['activo' => 'boolean', 'email_verified_at' => 'datetime'];

    public function empresa() { return $this->belongsTo(Empresa::class); }

    /** Iniciales para el avatar */
    public function getInicialesAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return strtoupper(substr($parts[0], 0, 1) . substr($parts[1] ?? '', 0, 1));
    }

    /** Nombre del primer rol */
    public function getRolLabelAttribute(): string
    {
        return $this->roles->first()?->name ?? 'Sin rol';
    }
    
}
