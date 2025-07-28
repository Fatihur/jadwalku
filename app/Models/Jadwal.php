<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwals';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'ruangan_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'semester',
        'tahun_ajaran',
        'is_active',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship dengan Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relationship dengan MataPelajaran
     */
    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    /**
     * Relationship dengan Guru
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    /**
     * Relationship dengan Ruangan
     */
    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    /**
     * Scope untuk filter berdasarkan hari
     */
    public function scopeHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Scope untuk filter berdasarkan semester
     */
    public function scopeSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Cek konflik ruangan pada waktu yang sama
     */
    public static function hasRoomConflict($ruanganId, $hari, $jamMulai, $semester, $tahunAjaran, $excludeId = null)
    {
        $query = self::where('ruangan_id', $ruanganId)
            ->where('hari', $hari)
            ->where('jam_mulai', $jamMulai)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('is_active', true);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Cek konflik guru pada waktu yang sama
     */
    public static function hasTeacherConflict($guruId, $hari, $jamMulai, $semester, $tahunAjaran, $excludeId = null)
    {
        $query = self::where('guru_id', $guruId)
            ->where('hari', $hari)
            ->where('jam_mulai', $jamMulai)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('is_active', true);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Cek konflik kelas pada waktu yang sama
     */
    public static function hasClassConflict($kelasId, $hari, $jamMulai, $semester, $tahunAjaran, $excludeId = null)
    {
        $query = self::where('kelas_id', $kelasId)
            ->where('hari', $hari)
            ->where('jam_mulai', $jamMulai)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('is_active', true);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Validasi semua konflik sekaligus
     */
    public static function hasAnyConflict($kelasId, $guruId, $ruanganId, $hari, $jamMulai, $semester, $tahunAjaran, $excludeId = null)
    {
        return self::hasClassConflict($kelasId, $hari, $jamMulai, $semester, $tahunAjaran, $excludeId) ||
               self::hasTeacherConflict($guruId, $hari, $jamMulai, $semester, $tahunAjaran, $excludeId) ||
               self::hasRoomConflict($ruanganId, $hari, $jamMulai, $semester, $tahunAjaran, $excludeId);
    }
}
