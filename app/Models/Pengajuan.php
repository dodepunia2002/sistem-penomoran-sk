<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    protected $table    = 'pengajuan';
    protected $fillable = ['nama', 'alamat', 'tanggal', 'status', 'submitted_by'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date:Y-m-d',
        ];
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function riwayat(): HasOne
    {
        return $this->hasOne(Riwayat::class, 'pengajuan_id');
    }

    /** Scopes for common filters */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDiterima($query)
    {
        return $query->where('status', 'diterima');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('submitted_by', $userId);
    }
}
