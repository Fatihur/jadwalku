<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'gurus';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'bidang_keahlian',
        'status_kepegawaian',
        'tanggal_mulai_kerja',
    ];

    protected $casts = [
        'tanggal_mulai_kerja' => 'date',
    ];

    /**
     * Relationship dengan User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship dengan MataPelajaran (many-to-many)
     */
    public function mataPelajaran(): BelongsToMany
    {
        return $this->belongsToMany(MataPelajaran::class, 'guru_mata_pelajaran', 'guru_id', 'mata_pelajaran_id');
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

    /**
     * Relationship dengan Kelas sebagai wali kelas
     */
    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }
}
