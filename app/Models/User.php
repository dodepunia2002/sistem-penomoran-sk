<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isPetugas(): bool { return $this->role === 'petugas'; }
    public function pengajuan() { return $this->hasMany(Pengajuan::class, 'submitted_by'); }
    public function riwayat() { return $this->hasMany(Riwayat::class, 'processed_by'); }
}
