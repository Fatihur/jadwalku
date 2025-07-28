<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'wali_kelas_id',
        'kapasitas_maksimal',
        'tahun_ajaran',
        'is_active',
    ];

    protected $casts = [
        'kapasitas_maksimal' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship dengan Guru sebagai wali kelas
     */
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    /**
     * Relationship dengan Siswa
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    /**
     * Relationship dengan Jadwal
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}
