<?php

namespace App\Services\GeneticAlgorithm;

use Carbon\Carbon;

/**
 * Class FitnessCalculator
 * Menghitung fitness kromosom berdasarkan berbagai constraint
 */
class FitnessCalculator
{
    private array $constraints;
    private array $weights;

    public function __construct()
    {
        // Bobot untuk setiap constraint (total harus 1.0)
        $this->weights = [
            'teacher_conflict' => 0.3,      // Guru tidak boleh mengajar di 2 tempat bersamaan
            'room_conflict' => 0.25,        // Ruangan tidak boleh dipakai 2 kelas bersamaan
            'class_conflict' => 0.25,       // Kelas tidak boleh ada 2 pelajaran bersamaan
            'teacher_workload' => 0.1,      // Distribusi beban kerja guru
            'time_preference' => 0.1        // Preferensi waktu mengajar
        ];
    }

    /**
     * Menghitung fitness kromosom
     */
    public function calculateFitness(Chromosome $chromosome): float
    {
        $penalties = [
            'teacher_conflict' => $this->calculateTeacherConflict($chromosome),
            'room_conflict' => $this->calculateRoomConflict($chromosome),
            'class_conflict' => $this->calculateClassConflict($chromosome),
            'teacher_workload' => $this->calculateTeacherWorkload($chromosome),
            'time_preference' => $this->calculateTimePreference($chromosome)
        ];

        // Hitung total penalty dengan bobot
        $totalPenalty = 0;
        foreach ($penalties as $type => $penalty) {
            $totalPenalty += $penalty * $this->weights[$type];
        }

        // Fitness = 1 - total_penalty (semakin rendah penalty, semakin tinggi fitness)
        $fitness = max(0, 1 - $totalPenalty);
        
        return $fitness;
    }

    /**
     * Menghitung konflik guru (guru mengajar di 2 tempat bersamaan)
     */
    private function calculateTeacherConflict(Chromosome $chromosome): float
    {
        $conflicts = 0;
        $genes = $chromosome->getGenes();
        
        for ($i = 0; $i < count($genes); $i++) {
            for ($j = $i + 1; $j < count($genes); $j++) {
                $gene1 = $genes[$i];
                $gene2 = $genes[$j];
                
                // Cek apakah guru sama dan waktu bertabrakan
                if ($gene1['guru_id'] === $gene2['guru_id'] && 
                    $gene1['hari'] === $gene2['hari'] &&
                    $this->isTimeOverlap($gene1, $gene2)) {
                    $conflicts++;
                }
            }
        }
        
        // Normalisasi berdasarkan jumlah maksimum konflik yang mungkin
        $maxPossibleConflicts = count($genes) * (count($genes) - 1) / 2;
        return $maxPossibleConflicts > 0 ? $conflicts / $maxPossibleConflicts : 0;
    }

    /**
     * Menghitung konflik ruangan
     */
    private function calculateRoomConflict(Chromosome $chromosome): float
    {
        $conflicts = 0;
        $genes = $chromosome->getGenes();
        
        for ($i = 0; $i < count($genes); $i++) {
            for ($j = $i + 1; $j < count($genes); $j++) {
                $gene1 = $genes[$i];
                $gene2 = $genes[$j];
                
                // Cek apakah ruangan sama dan waktu bertabrakan
                if ($gene1['ruangan_id'] === $gene2['ruangan_id'] && 
                    $gene1['hari'] === $gene2['hari'] &&
                    $this->isTimeOverlap($gene1, $gene2)) {
                    $conflicts++;
                }
            }
        }
        
        $maxPossibleConflicts = count($genes) * (count($genes) - 1) / 2;
        return $maxPossibleConflicts > 0 ? $conflicts / $maxPossibleConflicts : 0;
    }

    /**
     * Menghitung konflik kelas
     */
    private function calculateClassConflict(Chromosome $chromosome): float
    {
        $conflicts = 0;
        $genes = $chromosome->getGenes();
        
        for ($i = 0; $i < count($genes); $i++) {
            for ($j = $i + 1; $j < count($genes); $j++) {
                $gene1 = $genes[$i];
                $gene2 = $genes[$j];
                
                // Cek apakah kelas sama dan waktu bertabrakan
                if ($gene1['kelas_id'] === $gene2['kelas_id'] && 
                    $gene1['hari'] === $gene2['hari'] &&
                    $this->isTimeOverlap($gene1, $gene2)) {
                    $conflicts++;
                }
            }
        }
        
        $maxPossibleConflicts = count($genes) * (count($genes) - 1) / 2;
        return $maxPossibleConflicts > 0 ? $conflicts / $maxPossibleConflicts : 0;
    }

    /**
     * Menghitung distribusi beban kerja guru
     */
    private function calculateTeacherWorkload(Chromosome $chromosome): float
    {
        $teacherHours = [];
        $genes = $chromosome->getGenes();
        
        foreach ($genes as $gene) {
            $guruId = $gene['guru_id'];
            $duration = $this->calculateDuration($gene['jam_mulai'], $gene['jam_selesai']);
            
            if (!isset($teacherHours[$guruId])) {
                $teacherHours[$guruId] = 0;
            }
            $teacherHours[$guruId] += $duration;
        }
        
        if (empty($teacherHours)) {
            return 0;
        }
        
        // Hitung standar deviasi dari jam mengajar guru
        $mean = array_sum($teacherHours) / count($teacherHours);
        $variance = 0;
        
        foreach ($teacherHours as $hours) {
            $variance += pow($hours - $mean, 2);
        }
        
        $stdDev = sqrt($variance / count($teacherHours));
        
        // Normalisasi (semakin tinggi standar deviasi, semakin buruk distribusi)
        return min(1, $stdDev / 10); // Asumsi maksimal standar deviasi 10 jam
    }

    /**
     * Menghitung preferensi waktu mengajar
     */
    private function calculateTimePreference(Chromosome $chromosome): float
    {
        $penalty = 0;
        $genes = $chromosome->getGenes();
        
        foreach ($genes as $gene) {
            $jamMulai = Carbon::createFromFormat('H:i', $gene['jam_mulai']);
            
            // Penalty untuk jam terlalu pagi (sebelum 07:00) atau terlalu sore (setelah 15:00)
            if ($jamMulai->hour < 7) {
                $penalty += 0.5;
            } elseif ($jamMulai->hour >= 15) {
                $penalty += 0.3;
            }
            
            // Penalty untuk hari Jumat sore
            if ($gene['hari'] === 'jumat' && $jamMulai->hour >= 14) {
                $penalty += 0.2;
            }
        }
        
        return count($genes) > 0 ? min(1, $penalty / count($genes)) : 0;
    }

    /**
     * Cek apakah dua waktu bertabrakan
     */
    private function isTimeOverlap(array $gene1, array $gene2): bool
    {
        $start1 = Carbon::createFromFormat('H:i', $gene1['jam_mulai']);
        $end1 = Carbon::createFromFormat('H:i', $gene1['jam_selesai']);
        $start2 = Carbon::createFromFormat('H:i', $gene2['jam_mulai']);
        $end2 = Carbon::createFromFormat('H:i', $gene2['jam_selesai']);
        
        return $start1->lt($end2) && $start2->lt($end1);
    }

    /**
     * Menghitung durasi dalam jam
     */
    private function calculateDuration(string $jamMulai, string $jamSelesai): float
    {
        $start = Carbon::createFromFormat('H:i', $jamMulai);
        $end = Carbon::createFromFormat('H:i', $jamSelesai);
        
        return $start->diffInMinutes($end) / 60;
    }

    /**
     * Mendapatkan detail penalty untuk debugging
     */
    public function getDetailedPenalties(Chromosome $chromosome): array
    {
        return [
            'teacher_conflict' => $this->calculateTeacherConflict($chromosome),
            'room_conflict' => $this->calculateRoomConflict($chromosome),
            'class_conflict' => $this->calculateClassConflict($chromosome),
            'teacher_workload' => $this->calculateTeacherWorkload($chromosome),
            'time_preference' => $this->calculateTimePreference($chromosome)
        ];
    }
}
