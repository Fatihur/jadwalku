<?php

namespace App\Services\GeneticAlgorithm;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Log;

/**
 * Class ScheduleGeneticAlgorithm
 * Engine utama untuk algoritma genetika penjadwalan kelas
 */
class ScheduleGeneticAlgorithm
{
    private int $populationSize;
    private int $maxGenerations;
    private float $targetFitness;
    private FitnessCalculator $fitnessCalculator;
    private GeneticOperators $geneticOperators;
    
    private array $kelas;
    private array $mataPelajaran;
    private array $guru;
    private array $ruangan;
    private array $timeSlots;
    
    private array $constraints;
    private array $statistics;

    public function __construct(
        int $populationSize = 100,
        int $maxGenerations = 1000,
        float $targetFitness = 0.95,
        float $crossoverRate = 0.8,
        float $mutationRate = 0.1
    ) {
        $this->populationSize = $populationSize;
        $this->maxGenerations = $maxGenerations;
        $this->targetFitness = $targetFitness;
        
        $this->fitnessCalculator = new FitnessCalculator();
        $this->geneticOperators = new GeneticOperators($crossoverRate, $mutationRate);
        
        $this->statistics = [
            'generation' => 0,
            'best_fitness' => 0,
            'average_fitness' => 0,
            'worst_fitness' => 0,
            'fitness_history' => []
        ];
    }

    /**
     * Load data dari database
     */
    public function loadData(string $tahunAjaran, string $semester): void
    {
        $this->kelas = Kelas::where('is_active', true)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get()
            ->toArray();
            
        $this->mataPelajaran = MataPelajaran::where('is_active', true)
            ->get()
            ->toArray();
            
        $this->guru = Guru::with(['user', 'mataPelajaran'])
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->map(function ($guru) {
                return [
                    'id' => $guru->id,
                    'user_id' => $guru->user_id,
                    'nip' => $guru->nip,
                    'bidang_keahlian' => $guru->bidang_keahlian,
                    'mata_pelajaran' => $guru->mataPelajaran->map(function ($mapel) {
                        return [
                            'id' => $mapel->id,
                            'nama_mata_pelajaran' => $mapel->nama_mata_pelajaran,
                            'kode_mata_pelajaran' => $mapel->kode_mata_pelajaran,
                        ];
                    })->toArray()
                ];
            })
            ->toArray();
            
        $this->ruangan = Ruangan::where('is_active', true)
            ->get()
            ->toArray();
            
        $this->generateTimeSlots();
        
        // Set data untuk genetic operators
        $this->geneticOperators->setAvailableTimeSlots($this->timeSlots);
        $this->geneticOperators->setAvailableRooms($this->ruangan);
    }

    /**
     * Generate time slots yang tersedia
     */
    private function generateTimeSlots(): void
    {
        $this->timeSlots = [];
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        
        foreach ($days as $day) {
            // Jam 07:00 - 15:00 dengan interval 45 menit + 15 menit istirahat
            for ($hour = 7; $hour < 15; $hour++) {
                for ($minute = 0; $minute < 60; $minute += 60) { // 1 jam per slot
                    $jamMulai = sprintf('%02d:%02d', $hour, $minute);
                    $jamSelesai = sprintf('%02d:%02d', $hour + 1, $minute);
                    
                    // Skip jam istirahat (12:00-13:00)
                    if ($hour == 12) continue;
                    
                    $this->timeSlots[] = [
                        'hari' => $day,
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => $jamSelesai
                    ];
                }
            }
        }
    }

    /**
     * Generate populasi awal secara acak
     */
    public function generateInitialPopulation(): array
    {
        $population = [];
        
        for ($i = 0; $i < $this->populationSize; $i++) {
            $chromosome = $this->generateRandomChromosome();
            $population[] = $chromosome;
        }
        
        return $population;
    }

    /**
     * Generate kromosom acak
     */
    private function generateRandomChromosome(): Chromosome
    {
        $genes = [];

        // Debug: Check if we have data
        if (empty($this->kelas)) {
            throw new \Exception('Tidak ada data kelas yang tersedia');
        }

        if (empty($this->mataPelajaran)) {
            throw new \Exception('Tidak ada data mata pelajaran yang tersedia');
        }

        if (empty($this->ruangan)) {
            throw new \Exception('Tidak ada data ruangan yang tersedia');
        }

        if (empty($this->timeSlots)) {
            throw new \Exception('Tidak ada time slots yang tersedia');
        }

        foreach ($this->kelas as $kelas) {
            foreach ($this->mataPelajaran as $mapel) {
                // Cari guru yang bisa mengajar mata pelajaran ini
                $availableTeachers = $this->getAvailableTeachers($mapel['id']);

                if (empty($availableTeachers)) {
                    // Log warning but continue
                    Log::warning("Tidak ada guru yang bisa mengajar mata pelajaran: " . $mapel['nama_mata_pelajaran']);
                    continue;
                }

                // Generate jadwal sesuai jam per minggu
                $jamPerMinggu = $mapel['jam_per_minggu'];

                for ($j = 0; $j < $jamPerMinggu; $j++) {
                    $randomTeacher = $availableTeachers[array_rand($availableTeachers)];
                    $randomRoom = $this->ruangan[array_rand($this->ruangan)];
                    $randomTimeSlot = $this->timeSlots[array_rand($this->timeSlots)];

                    $gene = [
                        'kelas_id' => $kelas['id'],
                        'mata_pelajaran_id' => $mapel['id'],
                        'guru_id' => $randomTeacher['id'],
                        'ruangan_id' => $randomRoom['id'],
                        'hari' => $randomTimeSlot['hari'],
                        'jam_mulai' => $randomTimeSlot['jam_mulai'],
                        'jam_selesai' => $randomTimeSlot['jam_selesai']
                    ];

                    $genes[] = $gene;
                }
            }
        }

        if (empty($genes)) {
            throw new \Exception('Tidak dapat membuat gen jadwal. Pastikan ada guru yang mengajar mata pelajaran yang tersedia.');
        }

        return new Chromosome($genes);
    }

    /**
     * Mendapatkan guru yang bisa mengajar mata pelajaran tertentu
     */
    private function getAvailableTeachers(int $mataPelajaranId): array
    {
        $availableTeachers = [];
        
        foreach ($this->guru as $guru) {
            if (isset($guru['mata_pelajaran'])) {
                foreach ($guru['mata_pelajaran'] as $mapel) {
                    if ($mapel['id'] == $mataPelajaranId) {
                        $availableTeachers[] = $guru;
                        break;
                    }
                }
            }
        }
        
        return $availableTeachers;
    }

    /**
     * Evaluasi fitness untuk seluruh populasi
     */
    public function evaluatePopulation(array $population): void
    {
        foreach ($population as $chromosome) {
            $fitness = $this->fitnessCalculator->calculateFitness($chromosome);
            $chromosome->setFitness($fitness);
        }
    }

    /**
     * Jalankan algoritma genetika
     */
    public function run(): array
    {
        Log::info("Starting genetic algorithm run");

        // Generate populasi awal
        try {
            $population = $this->generateInitialPopulation();
            Log::info("Generated initial population with " . count($population) . " chromosomes");
        } catch (\Exception $e) {
            Log::error("Failed to generate initial population: " . $e->getMessage());
            throw $e;
        }

        $this->evaluatePopulation($population);

        $bestChromosome = null;
        $bestFitness = 0;

        // Initialize best chromosome from initial population
        foreach ($population as $chromosome) {
            if ($chromosome->getFitness() > $bestFitness) {
                $bestFitness = $chromosome->getFitness();
                $bestChromosome = $chromosome->clone();
            }
        }
        
        for ($generation = 0; $generation < $this->maxGenerations; $generation++) {
            $this->statistics['generation'] = $generation;
            
            // Hitung statistik generasi saat ini
            $this->calculateGenerationStatistics($population);
            
            // Cek apakah sudah mencapai target fitness
            if ($this->statistics['best_fitness'] >= $this->targetFitness) {
                break;
            }
            
            // Generate populasi baru
            $newPopulation = [];
            
            // Elitism - pertahankan 10% individu terbaik
            $eliteCount = (int)($this->populationSize * 0.1);
            $elites = $this->geneticOperators->elitismSelection($population, $eliteCount);
            $newPopulation = array_merge($newPopulation, $elites);
            
            // Generate offspring untuk mengisi sisa populasi
            while (count($newPopulation) < $this->populationSize) {
                // Seleksi parent
                $parent1 = $this->geneticOperators->tournamentSelection($population);
                $parent2 = $this->geneticOperators->tournamentSelection($population);
                
                // Crossover
                $offspring = $this->geneticOperators->twoPointCrossover($parent1, $parent2);
                
                // Mutasi
                $offspring[0] = $this->geneticOperators->combinedMutation($offspring[0]);
                $offspring[1] = $this->geneticOperators->combinedMutation($offspring[1]);
                
                $newPopulation[] = $offspring[0];
                if (count($newPopulation) < $this->populationSize) {
                    $newPopulation[] = $offspring[1];
                }
            }
            
            // Evaluasi populasi baru
            $this->evaluatePopulation($newPopulation);
            $population = $newPopulation;
            
            // Update best chromosome
            foreach ($population as $chromosome) {
                if ($chromosome->getFitness() > $bestFitness) {
                    $bestFitness = $chromosome->getFitness();
                    $bestChromosome = $chromosome->clone();
                }
            }
        }
        
        return [
            'best_chromosome' => $bestChromosome,
            'best_fitness' => $bestFitness,
            'statistics' => $this->statistics,
            'final_population' => $population
        ];
    }

    /**
     * Hitung statistik generasi
     */
    private function calculateGenerationStatistics(array $population): void
    {
        $fitnesses = array_map(function ($chromosome) {
            return $chromosome->getFitness();
        }, $population);
        
        $this->statistics['best_fitness'] = max($fitnesses);
        $this->statistics['average_fitness'] = array_sum($fitnesses) / count($fitnesses);
        $this->statistics['worst_fitness'] = min($fitnesses);
        
        $this->statistics['fitness_history'][] = [
            'generation' => $this->statistics['generation'],
            'best' => $this->statistics['best_fitness'],
            'average' => $this->statistics['average_fitness'],
            'worst' => $this->statistics['worst_fitness']
        ];
    }

    /**
     * Konversi kromosom terbaik ke format jadwal
     */
    public function chromosomeToSchedule(Chromosome $chromosome): array
    {
        $schedule = [];
        
        foreach ($chromosome->getGenes() as $gene) {
            $schedule[] = [
                'kelas_id' => $gene['kelas_id'],
                'mata_pelajaran_id' => $gene['mata_pelajaran_id'],
                'guru_id' => $gene['guru_id'],
                'ruangan_id' => $gene['ruangan_id'],
                'hari' => $gene['hari'],
                'jam_mulai' => $gene['jam_mulai'],
                'jam_selesai' => $gene['jam_selesai']
            ];
        }
        
        return $schedule;
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return $this->statistics;
    }
}
