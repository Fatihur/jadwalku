<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\Ruangan;
use App\Services\GeneticAlgorithm\ScheduleGeneticAlgorithm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ScheduleService
 * Service untuk mengelola penjadwalan kelas menggunakan algoritma genetika
 */
class ScheduleService
{
    private ScheduleGeneticAlgorithm $geneticAlgorithm;

    public function __construct()
    {
        $this->geneticAlgorithm = new ScheduleGeneticAlgorithm(
            populationSize: 100,
            maxGenerations: 500,
            targetFitness: 0.95,
            crossoverRate: 0.8,
            mutationRate: 0.15
        );
    }

    /**
     * Generate jadwal otomatis menggunakan algoritma genetika
     */
    public function generateSchedule(string $tahunAjaran, string $semester): array
    {
        try {
            // Load data dari database
            $this->geneticAlgorithm->loadData($tahunAjaran, $semester);

            Log::info("Memulai generate jadwal untuk tahun ajaran: {$tahunAjaran}, semester: {$semester}");

            // Jalankan algoritma genetika
            $result = $this->geneticAlgorithm->run();

            Log::info("Hasil algoritma genetika:", [
                'best_chromosome_exists' => isset($result['best_chromosome']) && $result['best_chromosome'] !== null,
                'best_fitness' => $result['best_fitness'] ?? 'null',
                'statistics' => $result['statistics'] ?? 'null'
            ]);

            if (!$result['best_chromosome']) {
                throw new \Exception('Gagal menghasilkan jadwal yang optimal. Best chromosome: ' . ($result['best_chromosome'] ? 'exists' : 'null'));
            }
            
            // Konversi kromosom terbaik ke format jadwal
            $scheduleData = $this->geneticAlgorithm->chromosomeToSchedule($result['best_chromosome']);
            
            Log::info("Berhasil generate jadwal dengan fitness: " . $result['best_fitness']);
            
            return [
                'success' => true,
                'schedule' => $scheduleData,
                'fitness' => $result['best_fitness'],
                'statistics' => $result['statistics'],
                'message' => 'Jadwal berhasil dibuat dengan fitness: ' . number_format($result['best_fitness'] * 100, 2) . '%'
            ];
            
        } catch (\Exception $e) {
            Log::error("Error generating schedule: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal membuat jadwal: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Simpan jadwal ke database
     */
    public function saveSchedule(array $scheduleData, string $tahunAjaran, string $semester): array
    {
        try {
            DB::beginTransaction();
            
            // Hapus jadwal lama untuk tahun ajaran dan semester yang sama
            Jadwal::where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->delete();
            
            $savedCount = 0;
            
            foreach ($scheduleData as $schedule) {
                // Validasi data sebelum menyimpan
                if ($this->validateScheduleData($schedule)) {
                    Jadwal::create([
                        'kelas_id' => $schedule['kelas_id'],
                        'mata_pelajaran_id' => $schedule['mata_pelajaran_id'],
                        'guru_id' => $schedule['guru_id'],
                        'ruangan_id' => $schedule['ruangan_id'],
                        'hari' => $schedule['hari'],
                        'jam_mulai' => $schedule['jam_mulai'],
                        'jam_selesai' => $schedule['jam_selesai'],
                        'semester' => $semester,
                        'tahun_ajaran' => $tahunAjaran,
                        'is_active' => true
                    ]);
                    
                    $savedCount++;
                }
            }
            
            DB::commit();
            
            Log::info("Berhasil menyimpan {$savedCount} jadwal ke database");
            
            return [
                'success' => true,
                'saved_count' => $savedCount,
                'message' => "Berhasil menyimpan {$savedCount} jadwal"
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saving schedule: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal menyimpan jadwal: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validasi data jadwal sebelum disimpan
     */
    private function validateScheduleData(array $schedule): bool
    {
        // Cek apakah semua field required ada
        $requiredFields = ['kelas_id', 'mata_pelajaran_id', 'guru_id', 'ruangan_id', 'hari', 'jam_mulai', 'jam_selesai'];
        
        foreach ($requiredFields as $field) {
            if (!isset($schedule[$field]) || empty($schedule[$field])) {
                Log::warning("Missing required field: {$field}");
                return false;
            }
        }
        
        // Validasi apakah kelas, mata pelajaran, guru, dan ruangan masih ada
        if (!Kelas::find($schedule['kelas_id'])) {
            Log::warning("Kelas not found: " . $schedule['kelas_id']);
            return false;
        }
        
        if (!MataPelajaran::find($schedule['mata_pelajaran_id'])) {
            Log::warning("Mata pelajaran not found: " . $schedule['mata_pelajaran_id']);
            return false;
        }
        
        if (!Guru::find($schedule['guru_id'])) {
            Log::warning("Guru not found: " . $schedule['guru_id']);
            return false;
        }
        
        if (!Ruangan::find($schedule['ruangan_id'])) {
            Log::warning("Ruangan not found: " . $schedule['ruangan_id']);
            return false;
        }
        
        return true;
    }

    /**
     * Mendapatkan jadwal berdasarkan filter
     */
    public function getSchedule(array $filters = []): array
    {
        $query = Jadwal::with(['kelas', 'mataPelajaran', 'guru.user', 'ruangan'])
            ->where('is_active', true);
        
        // Apply filters
        if (isset($filters['tahun_ajaran'])) {
            $query->where('tahun_ajaran', $filters['tahun_ajaran']);
        }
        
        if (isset($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }
        
        if (isset($filters['kelas_id'])) {
            $query->where('kelas_id', $filters['kelas_id']);
        }
        
        if (isset($filters['guru_id'])) {
            $query->where('guru_id', $filters['guru_id']);
        }
        
        if (isset($filters['hari'])) {
            $query->where('hari', $filters['hari']);
        }
        
        $schedules = $query->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();
        
        return $schedules->toArray();
    }

    /**
     * Mendapatkan jadwal dalam format mingguan
     */
    public function getWeeklySchedule(array $filters = []): array
    {
        $schedules = $this->getSchedule($filters);
        $weeklySchedule = [];
        
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        
        foreach ($days as $day) {
            $weeklySchedule[$day] = array_filter($schedules, function ($schedule) use ($day) {
                return $schedule['hari'] === $day;
            });
            
            // Sort by time
            usort($weeklySchedule[$day], function ($a, $b) {
                return strcmp($a['jam_mulai'], $b['jam_mulai']);
            });
        }
        
        return $weeklySchedule;
    }

    /**
     * Cek konflik jadwal
     */
    public function checkConflicts(string $tahunAjaran, string $semester): array
    {
        $conflicts = [];
        
        // Cek konflik guru (guru mengajar di 2 tempat bersamaan)
        $teacherConflicts = DB::select("
            SELECT j1.guru_id, j1.hari, j1.jam_mulai, j1.jam_selesai,
                   j1.kelas_id as kelas1, j2.kelas_id as kelas2,
                   g.user_id, u.nama as nama_guru
            FROM jadwals j1
            JOIN jadwals j2 ON j1.guru_id = j2.guru_id 
                AND j1.hari = j2.hari 
                AND j1.id != j2.id
                AND j1.tahun_ajaran = ? 
                AND j1.semester = ?
                AND j2.tahun_ajaran = ? 
                AND j2.semester = ?
                AND ((j1.jam_mulai < j2.jam_selesai AND j1.jam_selesai > j2.jam_mulai))
            JOIN gurus g ON j1.guru_id = g.id
            JOIN users u ON g.user_id = u.id
        ", [$tahunAjaran, $semester, $tahunAjaran, $semester]);
        
        foreach ($teacherConflicts as $conflict) {
            $conflicts[] = [
                'type' => 'teacher_conflict',
                'message' => "Guru {$conflict->nama_guru} mengajar di 2 kelas bersamaan pada {$conflict->hari} jam {$conflict->jam_mulai}-{$conflict->jam_selesai}",
                'details' => $conflict
            ];
        }
        
        // Cek konflik ruangan
        $roomConflicts = DB::select("
            SELECT j1.ruangan_id, j1.hari, j1.jam_mulai, j1.jam_selesai,
                   j1.kelas_id as kelas1, j2.kelas_id as kelas2,
                   r.nama_ruangan
            FROM jadwals j1
            JOIN jadwals j2 ON j1.ruangan_id = j2.ruangan_id 
                AND j1.hari = j2.hari 
                AND j1.id != j2.id
                AND j1.tahun_ajaran = ? 
                AND j1.semester = ?
                AND j2.tahun_ajaran = ? 
                AND j2.semester = ?
                AND ((j1.jam_mulai < j2.jam_selesai AND j1.jam_selesai > j2.jam_mulai))
            JOIN ruangans r ON j1.ruangan_id = r.id
        ", [$tahunAjaran, $semester, $tahunAjaran, $semester]);
        
        foreach ($roomConflicts as $conflict) {
            $conflicts[] = [
                'type' => 'room_conflict',
                'message' => "Ruangan {$conflict->nama_ruangan} digunakan 2 kelas bersamaan pada {$conflict->hari} jam {$conflict->jam_mulai}-{$conflict->jam_selesai}",
                'details' => $conflict
            ];
        }
        
        return $conflicts;
    }

    /**
     * Generate dan simpan jadwal sekaligus
     */
    public function generateAndSaveSchedule(string $tahunAjaran, string $semester): array
    {
        // Generate jadwal
        $generateResult = $this->generateSchedule($tahunAjaran, $semester);
        
        if (!$generateResult['success']) {
            return $generateResult;
        }
        
        // Simpan jadwal
        $saveResult = $this->saveSchedule($generateResult['schedule'], $tahunAjaran, $semester);
        
        if (!$saveResult['success']) {
            return $saveResult;
        }
        
        // Cek konflik
        $conflicts = $this->checkConflicts($tahunAjaran, $semester);
        
        return [
            'success' => true,
            'message' => 'Jadwal berhasil dibuat dan disimpan',
            'fitness' => $generateResult['fitness'],
            'statistics' => $generateResult['statistics'],
            'saved_count' => $saveResult['saved_count'],
            'conflicts' => $conflicts,
            'conflict_count' => count($conflicts)
        ];
    }
}
