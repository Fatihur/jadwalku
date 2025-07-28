<?php

namespace App\Console\Commands;

use App\Services\ScheduleService;
use Illuminate\Console\Command;

class TestGeneticAlgorithm extends Command
{
    protected $signature = 'edugenis:test-genetic';
    protected $description = 'Test genetic algorithm for schedule generation';

    public function handle()
    {
        $this->info('Testing Genetic Algorithm...');

        // Test database connection first
        try {
            $this->info('Testing database connection...');
            $userCount = \App\Models\User::count();
            $this->info("✅ Database OK - Users: {$userCount}");

            $guruCount = \App\Models\Guru::count();
            $this->info("✅ Guru count: {$guruCount}");

            $guruWithMapel = \App\Models\Guru::whereHas('mataPelajaran')->count();
            $this->info("✅ Guru with mata pelajaran: {$guruWithMapel}");

            $mapelCount = \App\Models\MataPelajaran::count();
            $this->info("✅ Mata pelajaran count: {$mapelCount}");

            $kelasCount = \App\Models\Kelas::count();
            $this->info("✅ Kelas count: {$kelasCount}");

            $ruanganCount = \App\Models\Ruangan::count();
            $this->info("✅ Ruangan count: {$ruanganCount}");

        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            return 1;
        }

        $scheduleService = new ScheduleService();

        try {
            $result = $scheduleService->generateSchedule('2024/2025', 'ganjil');

            if ($result['success']) {
                $this->info('✅ Genetic Algorithm berhasil!');
                $this->line('Fitness: ' . number_format($result['fitness'] * 100, 2) . '%');
                $this->line('Jumlah jadwal: ' . count($result['schedule']));
                $this->line('Statistik: ' . json_encode($result['statistics'], JSON_PRETTY_PRINT));
            } else {
                $this->error('❌ Genetic Algorithm gagal!');
                $this->line('Error: ' . $result['message']);
            }
        } catch (\Exception $e) {
            $this->error('❌ Exception: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
        }

        return 0;
    }
}
