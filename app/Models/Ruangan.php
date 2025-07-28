<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangans';

    protected $fillable = [
        'nama_ruangan',
        'kode_ruangan',
        'kapasitas',
        'tipe_ruangan',
        'fasilitas',
        'lokasi',
        'is_active',
    ];

    protected $casts = [
        'kapasitas' => 'integer',
        'fasilitas' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship dengan Jadwal
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    /**
     * Scope untuk filter ruangan aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter berdasarkan tipe ruangan
     */
    public function scopeTipe($query, $tipe)
    {
        return $query->where('tipe_ruangan', $tipe);
    }
}
