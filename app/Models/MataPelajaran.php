<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'nama_mata_pelajaran',
        'kode_mata_pelajaran',
        'deskripsi',
        'jam_per_minggu',
        'tingkat',
        'is_active',
    ];

    protected $casts = [
        'jam_per_minggu' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship dengan Guru (many-to-many)
     */
    public function guru(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'guru_mata_pelajaran', 'mata_pelajaran_id', 'guru_id');
    }

    /**
     * Relationship dengan Jadwal
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    /**
     * Relationship dengan Materi
     */
    public function materi(): HasMany
    {
        return $this->hasMany(Materi::class);
    }
}
