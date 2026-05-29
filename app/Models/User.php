<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'id_pengguna', 'password', 'role', 'guru_id'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canManageUsers(): bool
    {
        return $this->role === 'super_admin';
    }

    public function canManageData(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Kepala Sekolah',
            'admin'       => 'TU / Admin',
            'guru'        => 'Guru',
            default       => ucfirst($this->role),
        };
    }

    public function getRoleColorAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => '#dc2626',
            'admin'       => '#4f46e5',
            'guru'        => '#16a34a',
            default       => '#64748b',
        };
    }

    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'danger',
            'admin'       => 'primary',
            'guru'        => 'success',
            default       => 'secondary',
        };
    }
}
