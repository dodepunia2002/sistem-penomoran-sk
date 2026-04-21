<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Riwayat extends Model
{
    protected $table    = 'riwayat';
    protected $fillable = ['pengajuan_id', 'nama', 'alamat', 'tanggal', 'nomor_sk', 'processed_by'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date:Y-m-d',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
